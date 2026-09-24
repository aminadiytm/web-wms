<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    private array $allowed = ['stock_view', 'inbound_view', 'outbound_view', 'warehouse_view'];

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array', 'session.driver' => 'array']);
        config(['chatbot.api_key' => null, 'chatbot.provider' => 'gemini']);
        Http::preventStrayRequests();
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('email'); $t->string('password'); $t->timestamps();
        });
        (require database_path('migrations/2026_06_17_085000_create_permission_tables.php'))->up();
        Schema::create('menu_lists', function (Blueprint $t) {
            $t->id(); $t->string('menu_route'); $t->string('menu_code'); $t->boolean('is_active');
        });
        foreach (['inventory.stockIndex' => 'stock', 'transaction.inbIndex' => 'inbound', 'transaction.outbIndex' => 'outbound', 'masterdata.whIndex' => 'warehouse'] as $route => $code) {
            DB::table('menu_lists')->insert(['menu_route' => $route, 'menu_code' => $code, 'is_active' => true]);
        }
        Schema::create('products', function (Blueprint $t) {
            $t->id('prd_id'); $t->string('prd_code'); $t->string('prd_name'); $t->string('prd_unit'); $t->decimal('prd_min_stock', 20, 4)->default(0);
        });
        Schema::create('stocks', function (Blueprint $t) {
            $t->id('st_id'); $t->integer('ref_prd_id'); $t->integer('ref_wh_id'); $t->integer('ref_loc_id'); $t->decimal('qty_on_hand', 20, 4)->nullable(); $t->decimal('qty_reserved', 20, 4)->nullable();
        });
        Schema::create('warehouses', function (Blueprint $t) {
            $t->id('wh_id'); $t->string('wh_code'); $t->string('wh_name');
        });
        Schema::create('locations', function (Blueprint $t) {
            $t->id('loc_id'); $t->string('loc_code');
        });
        foreach (['inb', 'outb'] as $prefix) {
            Schema::create($prefix.'_mstr', function (Blueprint $t) use ($prefix) {
                $t->id($prefix.'_id'); $t->integer('ref_wh_id'); $t->string($prefix.'_code'); $t->string($prefix.'_stat'); $t->string($prefix.($prefix === 'inb' ? '_supplier' : '_customer')); $t->date($prefix.($prefix === 'inb' ? '_rcv' : '_shipped'))->nullable(); $t->timestamps();
            });
        }
        DB::table('products')->insert([
            ['prd_id' => 1, 'prd_code' => 'ABC', 'prd_name' => 'Baut Baja', 'prd_unit' => 'PCS', 'prd_min_stock' => 5],
            ['prd_id' => 2, 'prd_code' => 'EMPTY', 'prd_name' => 'Barang Kosong', 'prd_unit' => 'KG', 'prd_min_stock' => 2],
            ['prd_id' => 3, 'prd_code' => 'LOW', 'prd_name' => 'Mur', 'prd_unit' => 'PCS', 'prd_min_stock' => 5],
        ]);
        DB::table('warehouses')->insert([
            ['wh_id' => 1, 'wh_code' => 'UTAMA', 'wh_name' => 'Gudang Utama'],
            ['wh_id' => 2, 'wh_code' => 'CABANG', 'wh_name' => 'Gudang Cabang'],
        ]);
        DB::table('locations')->insert(['loc_id' => 1, 'loc_code' => 'RAK-01']);
        DB::table('stocks')->insert([
            ['ref_prd_id' => 1, 'ref_wh_id' => 1, 'ref_loc_id' => 1, 'qty_on_hand' => 10.1234, 'qty_reserved' => 3],
            ['ref_prd_id' => 1, 'ref_wh_id' => 2, 'ref_loc_id' => 1, 'qty_on_hand' => 5, 'qty_reserved' => null],
            ['ref_prd_id' => 3, 'ref_wh_id' => 1, 'ref_loc_id' => 1, 'qty_on_hand' => 7, 'qty_reserved' => 3],
        ]);
        foreach ($this->allowed as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }
        $user = User::create(['name' => 'Tester', 'email' => 'test@example.test', 'password' => 'unused']);
        $user->givePermissionTo($this->allowed);
        $this->actingAs($user);
    }

    public function test_stock_totals_and_location_details_are_read_from_database(): void
    {
        $this->postJson('/chatbot/message', ['message' => 'stok barang ABC berapa?'])->assertOk()
            ->assertJsonPath('rows.0.3', '15,1234')->assertJsonPath('rows.0.4', '3')->assertJsonPath('rows.0.5', '12,1234')
            ->assertJsonCount(2, 'details.rows')->assertJsonPath('context.product', 'ABC');
    }

    public function test_warehouse_scope_and_follow_up(): void
    {
        $this->postJson('/chatbot/message', ['message' => 'barang itu di gudang CABANG', 'product' => 'ABC'])->assertOk()
            ->assertJsonPath('rows.0.5', '5')->assertJsonCount(1, 'details.rows')->assertJsonPath('context.warehouse', 'CABANG');
        $this->postJson('/chatbot/message', ['message' => 'stok ABC di gudang TIDAKADA'])->assertOk()->assertJsonCount(0, 'rows');
        DB::table('warehouses')->where('wh_id', 2)->update(['wh_name' => 'Lokasi kedua']);
        $this->postJson('/chatbot/message', ['message' => 'stok barang itu', 'product' => 'ABC', 'warehouse' => 'CABANG'])->assertOk()->assertJsonPath('rows.0.5', '5');
    }

    public function test_low_stock_uses_available_and_includes_products_without_stock(): void
    {
        $this->postJson('/chatbot/message', ['message' => 'stok menipis'])->assertOk()->assertJsonCount(2, 'rows')
            ->assertJsonPath('rows.0.0', 'EMPTY')->assertJsonPath('rows.1.0', 'LOW');
        $this->postJson('/chatbot/message', ['message' => 'stok habis'])->assertOk()->assertJsonCount(1, 'rows')->assertJsonPath('rows.0.5', '0');
    }

    public function test_search_is_literal_and_unknown_questions_do_not_return_stock(): void
    {
        foreach (['stok %', "stok ' OR 1=1 --", 'stok TIDAKADA', 'apa cuaca hari ini?'] as $message) {
            $this->postJson('/chatbot/message', compact('message'))->assertOk()->assertJsonCount(0, 'rows');
        }
        $this->postJson('/chatbot/message', ['message' => 'stok baut baja'])->assertOk()->assertJsonPath('rows.0.0', 'ABC');
    }

    public function test_permissions_are_enforced_for_each_data_type(): void
    {
        auth()->user()->syncPermissions([]);
        foreach (['stok ABC', 'inbound terbaru', 'outbound terbaru', 'daftar gudang'] as $message) {
            $this->postJson('/chatbot/message', compact('message'))->assertForbidden();
        }
        $this->postJson('/chatbot/message', ['message' => 'bantuan'])->assertOk();
    }

    public function test_authentication_and_validation(): void
    {
        $this->postJson('/chatbot/message', ['message' => ''])->assertUnprocessable();
        $this->postJson('/chatbot/message', ['message' => str_repeat('a', 501)])->assertUnprocessable();
        $this->app['auth']->forgetGuards();
        $this->postJson('/chatbot/message', ['message' => 'stok ABC'])->assertUnauthorized();
    }

    public function test_transactions_filter_by_creation_date_and_document(): void
    {
        foreach (['inb', 'outb'] as $prefix) {
            DB::table($prefix.'_mstr')->insert([
                ['ref_wh_id' => 1, $prefix.'_code' => 'DOC-NEW', $prefix.'_stat' => 'draft', $prefix.($prefix === 'inb' ? '_supplier' : '_customer') => 'Mitra', 'created_at' => now()],
                ['ref_wh_id' => 1, $prefix.'_code' => 'DOC-OLD', $prefix.'_stat' => 'draft', $prefix.($prefix === 'inb' ? '_supplier' : '_customer') => 'Mitra', 'created_at' => now()->subDay()],
            ]);
        }
        $this->postJson('/chatbot/message', ['message' => 'inbound hari ini'])->assertOk()->assertJsonCount(1, 'rows')->assertJsonPath('rows.0.0', 'DOC-NEW');
        $this->postJson('/chatbot/message', ['message' => 'outbound DOC-OLD'])->assertOk()->assertJsonCount(1, 'rows')->assertJsonPath('rows.0.0', 'DOC-OLD');
    }

    public function test_each_question_reads_current_stock_and_quotes_preserve_names(): void
    {
        DB::table('stocks')->where('ref_prd_id', 1)->update(['qty_on_hand' => 20, 'qty_reserved' => 1]);
        $this->postJson('/chatbot/message', ['message' => 'stok ABC'])->assertOk()->assertJsonPath('rows.0.5', '38');
        $this->postJson('/chatbot/message', ['message' => 'stok "Barang Kosong"'])->assertOk()->assertJsonCount(1, 'rows')->assertJsonPath('rows.0.0', 'EMPTY');
    }

    public function test_widget_renders_and_endpoint_has_rate_limit(): void
    {
        $this->view('components.chatbot')->assertSee('Chat Asisten WMS')->assertSee('wms-chatbot.js');
        for ($i = 0; $i < 30; $i++) {
            $this->postJson('/chatbot/message', ['message' => 'bantuan'])->assertOk();
        }
        $this->postJson('/chatbot/message', ['message' => 'bantuan'])->assertStatus(429);
    }

    public function test_missing_menu_configuration_denies_access(): void
    {
        DB::table('menu_lists')->where('menu_route', 'inventory.stockIndex')->delete();
        $this->postJson('/chatbot/message', ['message' => 'stok ABC'])->assertForbidden();
    }
    public function test_jakarta_question_counts_actual_stock_without_ai(): void
    {
        DB::table('warehouses')->where('wh_id', 1)->update(['wh_name' => 'Warehouse Jakarta']);
        $this->postJson('/chatbot/message', ['message' => 'ada berapa barang di warehouse jakarta?'])->assertOk()
            ->assertJsonPath('engine', 'patterns')->assertJsonPath('rows.1.0', 'PCS')->assertJsonPath('rows.1.1', 2)
            ->assertJsonPath('rows.1.2', '17,1234')->assertJsonPath('rows.1.4', '11,1234');
        Http::assertNothingSent();
    }

    private function fakeAi(array $overrides = []): void
    {
        Http::swap(new \Illuminate\Http\Client\Factory());
        Http::preventStrayRequests();
        config(['chatbot.api_key' => 'test-key', 'chatbot.model' => 'gemini-3.6-flash']);
        $plan = array_merge(['intent' => 'stock_summary', 'product' => '', 'warehouse' => 'jakarta', 'stock_filter' => 'all', 'document' => '', 'today' => false], $overrides);
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [['finishReason' => 'STOP', 'content' => ['parts' => [['text' => json_encode($plan)]]]]],
        ])]);
    }

    public function test_ai_interprets_question_and_database_computes_answer(): void
    {
        DB::table('warehouses')->where('wh_id', 1)->update(['wh_name' => 'Warehouse Jakarta']);
        $this->fakeAi();
        $this->postJson('/chatbot/message', ['message' => 'Bisa kasih tahu ada berapa item di tempat penyimpanan Jakarta?'])->assertOk()
            ->assertJsonPath('engine', 'gemini')->assertJsonPath('rows.1.1', 2)->assertJsonPath('rows.1.2', '17,1234');
        Http::assertSent(function ($request) {
            $payload = json_decode($request['contents'][0]['parts'][0]['text'], true);
            return $request->hasHeader('x-goog-api-key', 'test-key')
                && $payload['message'] === 'Bisa kasih tahu ada berapa item di tempat penyimpanan Jakarta?'
                && array_keys($payload) === ['message', 'previous_context']
                && !str_contains($request->body(), '17.1234');
        });
    }

    public function test_ai_filters_remain_subject_to_permissions(): void
    {
        $this->fakeAi();
        auth()->user()->syncPermissions([]);
        $this->postJson('/chatbot/message', ['message' => 'berapa barang di Jakarta'])->assertForbidden();
    }

    public function test_ai_followup_receives_context_and_can_change_warehouse(): void
    {
        $this->fakeAi(['intent' => 'stock_list', 'product' => 'ABC', 'warehouse' => 'CABANG']);
        $this->postJson('/chatbot/message', ['message' => 'kalau yang di cabang?', 'product' => 'ABC', 'warehouse' => 'UTAMA'])->assertOk()
            ->assertJsonPath('rows.0.5', '5')->assertJsonPath('context.warehouse', 'CABANG');
        Http::assertSent(fn ($request) => json_decode($request['contents'][0]['parts'][0]['text'], true)['previous_context'] === ['product' => 'ABC', 'warehouse' => 'UTAMA']);
    }

    public function test_ai_does_not_execute_unknown_actions_or_return_invented_data(): void
    {
        $this->fakeAi(['intent' => 'sql', 'sql' => 'DROP TABLE stocks']);
        $this->postJson('/chatbot/message', ['message' => 'hapus stok'])->assertOk()->assertJsonPath('engine', 'unavailable')->assertJsonCount(0, 'rows');
        $this->assertSame(3, DB::table('stocks')->count());
        $this->fakeAi(['intent' => 'inbound', 'warehouse' => 'jakarta']);
        $this->postJson('/chatbot/message', ['message' => 'inbound Jakarta'])->assertOk()->assertJsonCount(0, 'rows');
    }

    public function test_ai_api_failure_is_explicit_and_does_not_expose_provider_errors(): void
    {
        config(['chatbot.api_key' => 'test-key']);
        Http::fake(['*' => Http::response(['error' => ['message' => 'private-provider-error']], 429)]);
        $this->postJson('/chatbot/message', ['message' => 'berapa barang?'])->assertOk()
            ->assertJsonPath('engine', 'unavailable')->assertDontSee('private-provider-error')->assertJsonCount(0, 'rows');
    }

    public function test_summary_is_not_limited_to_first_twenty_products_and_separates_units(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $id = DB::table('products')->insertGetId(['prd_code' => 'SKU'.$i, 'prd_name' => 'Produk '.$i, 'prd_unit' => 'KG', 'prd_min_stock' => 0], 'prd_id');
            DB::table('stocks')->insert(['ref_prd_id' => $id, 'ref_wh_id' => 1, 'ref_loc_id' => 1, 'qty_on_hand' => 2, 'qty_reserved' => 0]);
        }
        $this->postJson('/chatbot/message', ['message' => 'berapa barang di warehouse UTAMA?'])->assertOk()
            ->assertJsonPath('rows.0.0', 'KG')->assertJsonPath('rows.0.1', 25)->assertJsonPath('rows.0.2', '50')
            ->assertJsonPath('rows.1.0', 'PCS')->assertJsonPath('rows.1.1', 2)->assertJsonPath('has_more', false);
    }
    public function test_ai_errors_explain_status_without_leaking_provider_details(): void
    {
        config(['chatbot.api_key' => 'test-key']);
        foreach ([404 => 'model_unavailable', 401 => 'authentication', 403 => 'authentication', 429 => 'quota', 400 => 'invalid_request', 503 => 'upstream'] as $status => $reason) {
            Http::swap(new \Illuminate\Http\Client\Factory());
            Http::preventStrayRequests();
            Http::fake(['*' => Http::response(['error' => ['message' => 'private-provider-error test-key']], $status)]);
            $this->postJson('/chatbot/message', ['message' => 'berapa stok?'])->assertOk()
                ->assertJsonPath('engine', 'unavailable')->assertJsonPath('error_code', $reason)
                ->assertDontSee('private-provider-error')->assertDontSee('test-key');
        }
    }

    public function test_ai_incomplete_and_invalid_responses_are_distinguished(): void
    {
        config(['chatbot.api_key' => 'test-key']);
        foreach (['MAX_TOKENS' => 'incomplete', 'SAFETY' => 'blocked', 'STOP' => 'invalid_response'] as $finish => $reason) {
            $candidate = [
                'finishReason' => $finish,
                'content' => ['parts' => [['text' => '{invalid']]],
            ];
            Http::swap(new \Illuminate\Http\Client\Factory());
            Http::preventStrayRequests();
            Http::fake(['*' => Http::response(['candidates' => [$candidate]])]);
            $this->postJson('/chatbot/message', ['message' => 'berapa stok?'])->assertOk()
                ->assertJsonPath('engine', 'unavailable')->assertJsonPath('error_code', $reason);
        }
    }
    public function test_results_are_bounded_and_explicitly_truncated(): void
    {
        for ($i = 0; $i < 25; $i++) {
            DB::table('products')->insert(['prd_code' => 'X'.$i, 'prd_name' => 'Produk '.$i, 'prd_unit' => 'PCS', 'prd_min_stock' => 0]);
        }
        $this->postJson('/chatbot/message', ['message' => 'ringkasan stok'])->assertOk()->assertJsonCount(20, 'rows')->assertJsonPath('has_more', true);
    }
}

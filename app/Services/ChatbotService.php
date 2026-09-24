<?php

namespace App\Services;

use App\Helpers\MenuPermissionHelper;
use App\Exceptions\ChatbotAiException;
use Illuminate\Support\Facades\Log;
use App\Models\InboundMaster;
use App\Models\OutboundMaster;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class ChatbotService
{
    public const EXAMPLES = ['Ringkasan stok', 'Stok menipis', 'Stok habis', 'Stok barang ABC', 'Stok di gudang UTAMA', 'Inbound terbaru', 'Outbound hari ini'];

    public function reply(array $input): array
    {
        if (config('chatbot.provider') !== 'gemini' || !config('chatbot.api_key')) {
            $answer = $this->replyWithPatterns($input);
            $answer['engine'] = 'patterns';
            $answer['notice'] = 'AI belum aktif. Jawaban menggunakan pencarian dasar; administrator perlu mengatur API key Gemini.';
            return $answer;
        }
        try {
            $plan = app(ChatbotIntentService::class)->interpret($input);
        } catch (ChatbotAiException $exception) {
            return $this->aiFailure($exception);
        } catch (\Illuminate\Http\Client\ConnectionException $exception) {
            return $this->aiFailure(new ChatbotAiException('connection'));
        } catch (\RuntimeException | \JsonException $exception) {
            return $this->aiFailure(new ChatbotAiException('invalid_response'));
        }
        $answer = $this->executePlan($plan, $input);
        $answer['engine'] = 'gemini';
        return $answer;
    }

    private function aiFailure(ChatbotAiException $exception): array
    {
        Log::warning('Chatbot AI request failed', ['reason' => $exception->reason, 'http_status' => $exception->httpStatus]);
        return array_merge($this->answer($exception->userMessage()), ['engine' => 'unavailable', 'error_code' => $exception->reason]);
    }

    private function executePlan(array $plan, array $input): array
    {
        if (in_array($plan['intent'], ['stock_summary', 'stock_list'], true)) {
            if ($plan['today'] || $plan['document'] !== '') {
                return $this->answer('Pencarian stok mendukung kondisi saat ini, nama barang, dan gudang. Filter tersebut belum didukung.');
            }
            $this->authorize('inventory.stockIndex');
            return $this->stocks('', $input, $plan);
        }
        if (in_array($plan['intent'], ['inbound', 'outbound'], true)) {
            if ($plan['warehouse'] !== '' || $plan['product'] !== '' || $plan['stock_filter'] !== 'all') {
                return $this->answer('Pencarian transaksi saat ini mendukung kode dokumen dan tanggal pembuatan hari ini.');
            }
            return $this->transactions('', $plan);
        }
        if ($plan['intent'] === 'warehouses' && $plan['warehouse'] === '' && $plan['product'] === '') {
            return $this->replyWithPatterns(['message' => 'daftar gudang']);
        }
        if ($plan['intent'] === 'help') {
            return $this->replyWithPatterns(['message' => 'bantuan']);
        }
        return $this->answer('Saya bisa membantu menghitung stok per gudang, mencari barang/lokasi, stok menipis/habis, dan transaksi terbaru. Permintaan tersebut belum didukung.');
    }

    private function replyWithPatterns(array $input): array
    {
        $message = trim($input['message']);
        $text = preg_replace('/\bwarehouse\b/u', 'gudang', mb_strtolower($message));
        if (preg_match('/^(hai|halo|hi|hello|bantuan|help|menu|terima kasih)[!?.\s]*$/u', $text)) {
            return $this->answer('Halo! Saya asisten WMS. Saya bisa membaca stok fisik, cadangan, stok tersedia, lokasi barang, stok menipis/habis, serta inbound dan outbound terbaru. Sebutkan nama/kode barang dan tambahkan "di gudang KODE" untuk membatasi pencarian.');
        }
        if (preg_match('/\b(hapus|delete|drop|update|ubah|tambah|insert|password|token|rahasia)\b/u', $text)) {
            return $this->answer('Saya hanya membantu membaca informasi persediaan dan transaksi. Perubahan data dilakukan melalui menu aplikasi.');
        }
        if (preg_match('/\b(inbound|barang masuk|outbound|barang keluar)\b/u', $text)) {
            return $this->transactions($text);
        }
        if (preg_match('/^(daftar|list)\s+gudang[?.\s]*$/u', $text)) {
            $this->authorize('masterdata.whIndex');
            $warehouses = Warehouse::orderBy('wh_code')->limit(21)->get(['wh_code', 'wh_name']);
            return $this->answer($warehouses->isEmpty() ? 'Belum ada gudang.' : 'Daftar gudang:', ['Kode', 'Nama'], $warehouses->take(20)->map(fn ($w) => [$w->wh_code, $w->wh_name])->all(), $warehouses->count() > 20);
        }
        if (!preg_match('/\b(stok|stock|stocknya|stoknya|persediaan|barang|produk|lokasi|tersedia|sisanya|gudang|ringkasan)\b/u', $text)) {
            return $this->answer('Saya belum memahami pertanyaan tersebut. Coba "stok barang ABC berapa?", "stok menipis", "daftar gudang", atau "inbound terbaru".');
        }
        $this->authorize('inventory.stockIndex');
        return $this->stocks($text, $input);
    }

    private function stocks(string $text, array $input, ?array $filters = null): array
    {
        $warehouse = null;
        if (preg_match('/\b(?:di\s+)?gudang\s+(.+)$/u', $text, $match)) {
            $warehouse = trim(preg_replace('/\b(berapa|dong|ya|saja)\b/u', '', $match[1]), " \t\n\r\0\x0B?!.,\"");
            $text = trim(substr($text, 0, strpos($text, $match[0])));
        }
        // Quotes preserve product names that contain command words, e.g. "Barang Kosong".
        $quotedTerm = null;
        if (preg_match('/"([^"]+)"/u', $text, $quoted)) {
            $quotedTerm = trim($quoted[1]);
            $text = str_replace($quoted[0], '', $text);
        }
        $followup = (bool) preg_match('/\b(itu|tersebut|sisanya)\b/u', $text);
        if ($followup && !$warehouse) {
            $warehouse = $input['warehouse'] ?? null;
        }
        $low = (bool) preg_match('/\b(menipis|minimum|minim|rendah|kurang|low)\b/u', $text);
        $empty = (bool) preg_match('/\b(habis|kosong|nol)\b/u', $text);
        $term = preg_replace('/\b(stoknya|stocknya|stok|stock|persediaan|barang|produk|lokasi|tersedia|sisanya|ringkasan|total|semua|seluruh|daftar|list|cek|lihat|tampilkan|cari|berapa|berapakah|jumlah|jumlahnya|ada|yang|untuk|dari|di|mana|dimana|dan|saat|ini|sekarang|tolong|dong|ya|itu|tersebut|menipis|minimum|minim|rendah|kurang|low|habis|kosong|nol|dicadangkan|reserved|fisik|available)\b/u', ' ', $text);
        $term = trim(preg_replace('/\s+/u', ' ', $term), " \t\n\r\0\x0B?!.,\"");
        if ($quotedTerm !== null) {
            $term = $quotedTerm;
        }
        if ($followup && $term === '') {
            $term = $input['product'] ?? '';
        }
        $summary = $term === '' && (bool) preg_match('/\b(berapa|berapakah|jumlah|total)\b/u', $text);
        if ($filters !== null) {
            $term = $filters['product'] ?? '';
            $warehouse = $filters['warehouse'] ?: null;
            $low = $filters['stock_filter'] === 'low';
            $empty = $filters['stock_filter'] === 'empty';
            $summary = $filters['intent'] === 'stock_summary';
        }
        $warehouseId = null;
        if ($warehouse) {
            $matches = Warehouse::whereRaw('LOWER(wh_code) = ?', [mb_strtolower($warehouse)])->get();
            if ($matches->isEmpty()) {
                $matches = Warehouse::whereRaw('LOWER(wh_name) LIKE ? ESCAPE \'!\'', [$this->pattern($warehouse)])->limit(6)->get();
            }
            if ($matches->count() !== 1) {
                return $this->answer($matches->isEmpty() ? 'Gudang tidak ditemukan. Periksa nama atau kode gudangnya.' : 'Nama gudang cocok dengan beberapa data. Sebutkan kode gudang: '. $matches->pluck('wh_code')->implode(', '));
            }
            $warehouseId = $matches->first()->wh_id;
            $warehouse = $matches->first()->wh_code;
        }
        $totals = DB::table('stocks')->select('ref_prd_id')
            ->selectRaw('SUM(COALESCE(qty_on_hand, 0)) as on_hand, SUM(COALESCE(qty_reserved, 0)) as reserved')
            ->when($warehouseId, fn ($q) => $q->where('ref_wh_id', $warehouseId))->groupBy('ref_prd_id');
        $query = DB::table('products as p')->leftJoinSub($totals, 's', 'p.prd_id', '=', 's.ref_prd_id')
            ->select('p.prd_id', 'p.prd_code', 'p.prd_name', 'p.prd_unit', 'p.prd_min_stock')
            ->selectRaw('COALESCE(s.on_hand, 0) as on_hand, COALESCE(s.reserved, 0) as reserved, COALESCE(s.on_hand, 0) - COALESCE(s.reserved, 0) as available');
        if ($term !== '') {
            $exact = DB::table('products')->whereRaw('LOWER(prd_code) = ?', [mb_strtolower($term)])->value('prd_id');
            $query->where(function ($q) use ($term, $exact) {
                if ($exact !== null) {
                    $q->where('p.prd_id', $exact);
                } else {
                    $q->whereRaw('LOWER(p.prd_code) LIKE ? ESCAPE \'!\'', [$this->pattern($term)])
                        ->orWhereRaw('LOWER(p.prd_name) LIKE ? ESCAPE \'!\'', [$this->pattern($term)]);
                }
            });
        }
        if ($empty) {
            $query->whereRaw('(COALESCE(s.on_hand, 0) - COALESCE(s.reserved, 0)) <= 0');
        } elseif ($low) {
            $query->whereRaw('(COALESCE(s.on_hand, 0) - COALESCE(s.reserved, 0)) <= COALESCE(p.prd_min_stock, 0)');
        }
        $scope = $warehouse ? 'gudang '.$warehouse : 'semua gudang';
        if ($summary) {
            // Aggregate the full result before applying the table's display limit.
            $groups = DB::query()->fromSub($query, 'inventory')
                ->select('prd_unit')->selectRaw('SUM(CASE WHEN on_hand > 0 THEN 1 ELSE 0 END) as product_count, SUM(on_hand) as on_hand, SUM(reserved) as reserved, SUM(available) as available')
                ->groupBy('prd_unit')->orderBy('prd_unit')->get();
            $count = (int) $groups->sum('product_count');
            $answer = $this->answer('Di '.$scope.' ada '.$count.' jenis barang dengan stok fisik positif. Total stok per satuan ditampilkan di bawah; tersedia = fisik - dicadangkan.',
                ['Satuan', 'Jenis barang', 'Fisik', 'Dicadangkan', 'Tersedia'],
                $groups->map(fn ($g) => [$g->prd_unit, (int) $g->product_count, $this->number($g->on_hand), $this->number($g->reserved), $this->number($g->available)])->all());
            $answer['context'] = ['product' => $term ?: null, 'warehouse' => $warehouse];
            return $answer;
        }
        $products = $query->orderBy('p.prd_code')->limit(21)->get();
        if ($products->isEmpty()) {
            return $this->answer('Tidak ada barang yang cocok dengan pencarian pada '.$scope.'. Coba nama atau kode barang lainnya.');
        }
        $rows = $products->take(20)->map(fn ($p) => [$p->prd_code, $p->prd_name, $p->prd_unit, $this->number($p->on_hand), $this->number($p->reserved), $this->number($p->available), $this->number($p->prd_min_stock ?? 0)])->all();
        $answer = $this->answer('Stok pada '.$scope.'. Tersedia = fisik − dicadangkan. '.($low ? 'Stok menipis: tersedia ≤ minimum produk. ' : '').($empty ? 'Stok habis: tersedia ≤ 0. ' : '').'Barang tanpa catatan stok dihitung 0.', ['Kode', 'Barang', 'Satuan', 'Fisik', 'Dicadangkan', 'Tersedia', 'Minimum'], $rows, $products->count() > 20);
        $answer['context'] = ['product' => $term ?: null, 'warehouse' => $warehouse];
        if ($products->count() === 1) {
            $product = $products->first();
            $details = Stock::with(['warehouse', 'location'])->where('ref_prd_id', $product->prd_id)
                ->when($warehouseId, fn ($q) => $q->where('ref_wh_id', $warehouseId))->orderBy('ref_wh_id')->orderBy('ref_loc_id')->limit(21)->get();
            $answer['context'] = ['product' => $product->prd_code, 'warehouse' => $warehouse];
            $answer['details'] = ['columns' => ['Gudang', 'Lokasi', 'Fisik', 'Dicadangkan', 'Tersedia'], 'rows' => $details->take(20)->map(fn ($s) => [$s->warehouse?->wh_code ?? '-', $s->location?->loc_code ?? '-', $this->number($s->qty_on_hand ?? 0), $this->number($s->qty_reserved ?? 0), $this->number(($s->qty_on_hand ?? 0) - ($s->qty_reserved ?? 0))])->all(), 'has_more' => $details->count() > 20];
        }
        return $answer;
    }

    private function transactions(string $text, ?array $filters = null): array
    {
        $inbound = (bool) preg_match('/\b(inbound|barang masuk)\b/u', $text);
        if ($filters !== null) {
            $inbound = $filters['intent'] === 'inbound';
        }
        $this->authorize($inbound ? 'transaction.inbIndex' : 'transaction.outbIndex');
        $prefix = $inbound ? 'inb' : 'outb';
        $query = $inbound ? InboundMaster::query() : OutboundMaster::query();
        $today = str_contains($text, 'hari ini');
        $term = trim(preg_replace('/\b(inbound|outbound|barang masuk|barang keluar|hari ini|terbaru|terakhir|transaksi|daftar|list|cek|lihat|tampilkan|tolong|status)\b/u', '', $text), " \t\n\r\0\x0B?!.,\"");
        if ($filters !== null) {
            $term = $filters['document'];
            $today = $filters['today'];
        }
        if ($term !== '') {
            $query->whereRaw('LOWER('.$prefix.'_code) LIKE ? ESCAPE \'!\'', [$this->pattern($term)]);
        }
        if ($today) {
            $query->whereDate('created_at', today());
        }
        $records = $query->with('warehouse')->orderByDesc('created_at')->orderByDesc($prefix.'_id')->limit(21)->get();
        $rows = $records->take(20)->map(fn ($r) => [$r->{$prefix.'_code'}, $r->warehouse?->wh_code ?? '-', $r->{$prefix.($inbound ? '_supplier' : '_customer')}, $r->{$prefix.'_stat'}, $r->created_at?->format('d/m/Y H:i') ?? '-', $r->{$prefix.($inbound ? '_rcv' : '_shipped')} ?? '-'])->all();
        return $this->answer($records->isEmpty() ? 'Tidak ada transaksi yang cocok.' : ($inbound ? 'Inbound' : 'Outbound').' terbaru'.($today ? ' yang dibuat hari ini' : '').'. Status sesuai catatan database.', ['Dokumen', 'Gudang', $inbound ? 'Supplier' : 'Customer', 'Status', 'Dibuat', $inbound ? 'Tanggal terima' : 'Tanggal kirim'], $rows, $records->count() > 20);
    }

    private function authorize(string $route): void
    {
        abort_unless(MenuPermissionHelper::canView($route), 403, 'Anda tidak memiliki hak akses untuk melihat data ini.');
    }

    private function pattern(string $term): string
    {
        return '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], mb_strtolower($term)).'%';
    }

    private function number($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 4, ',', '.'), '0'), ',');
    }

    private function answer(string $message, array $columns = [], array $rows = [], bool $more = false): array
    {
        return ['message' => $message, 'columns' => $columns, 'rows' => $rows, 'has_more' => $more, 'context' => null, 'suggestions' => self::EXAMPLES, 'checked_at' => now()->toIso8601String()];
    }
}

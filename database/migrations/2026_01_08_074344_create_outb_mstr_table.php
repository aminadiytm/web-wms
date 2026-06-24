<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('outb_mstr', function (Blueprint $table) {
            $table->id('outb_id');
            $table->string('outb_code', 25)->unique();
            $table->foreignId('ref_wh_id')
                    ->constrained('warehouses', 'wh_id')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->string('outb_customer', 40);
            $table->string('outb_stat', 10);
            $table->date('outb_shipped')->nullable();
            $table->string('outb_add_by', 30);
            $table->string('outb_upd_by', 30)->nullable();
            $table->timestamps();
            $table->index(['ref_wh_id', 'outb_stat', 'outb_shipped']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outb_mstr');
    }
};

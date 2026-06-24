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
        Schema::create('inb_mstr', function (Blueprint $table) {
            $table->id('inb_id');
            $table->string('inb_code', 25)->unique();
            $table->foreignId('ref_wh_id')
                    ->constrained('warehouses', 'wh_id')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->string('inb_supplier', 40);
            $table->string('inb_stat', 10);
            $table->date('inb_rcv')->nullable();
            $table->string('inb_add_by', 30);
            $table->string('inb_upd_by', 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inb_mstr');
    }
};

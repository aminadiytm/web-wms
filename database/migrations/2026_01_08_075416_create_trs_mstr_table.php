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
        Schema::create('trs_mstr', function (Blueprint $table) {
            $table->id('trs_id');
            $table->string('trs_code', 25)->unique();
            $table->foreignId('from_wh_id')
                    ->constrained('warehouses', 'wh_id')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->foreignId('to_wh_id')
                    ->constrained('warehouses', 'wh_id')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->string('trs_stat', 10);
            $table->date('trs_shipped')->nullable();
            $table->date('trs_rcv')->nullable();
            $table->string('trs_add_by', 30);
            $table->string('trs_upd_by', 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_mstr');
    }
};

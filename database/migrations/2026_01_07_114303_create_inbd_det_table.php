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
        Schema::create('inbd_det', function (Blueprint $table) {
            $table->id('inbd_id');
            $table->foreignId('ref_inb_id')
                    ->constrained('inb_mstr', 'inb_id')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->foreignId('ref_prd_id')
                    ->constrained('products', 'prd_id')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->foreignId('ref_loc_id')
                    ->constrained('locations', 'loc_id')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->decimal('qty_order', 20, 4)->default(0);
            $table->decimal('qty_rcv', 20, 4)->default(0);
            $table->string('inbd_add_by', 30);
            $table->string('inbd_upd_by', 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbd_det');
    }
};

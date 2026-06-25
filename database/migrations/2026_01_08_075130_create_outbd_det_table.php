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
        Schema::create('outbd_det', function (Blueprint $table) {
            $table->id('outbd_id');
            $table->foreignId('ref_outb_id')
                    ->constrained('outb_mstr', 'outb_id')
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
            $table->string('outbd_confrm_by', 30)->nullable();
            $table->decimal('qty_req', 20, 4)->default(0);
            $table->decimal('qty_picked', 20, 4)->default(0);
            $table->string('outbd_add_by', 30);
            $table->string('outbd_upd_by', 30)->nullable();
            $table->timestamps();
            $table->index(['ref_outb_id', 'ref_prd_id', 'ref_loc_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbd_det');
    }
};

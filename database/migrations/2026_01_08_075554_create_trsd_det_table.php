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
        Schema::create('trsd_det', function (Blueprint $table) {
            $table->id('trsd_id');
            $table->foreignId('ref_trs_id')
                    ->constrained('trs_mstr', 'trs_id')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->foreignId('ref_prd_id')
                    ->constrained('products', 'prd_id')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->foreignId('from_loc_id')
                    ->constrained('locations', 'loc_id')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->foreignId('to_loc_id')
                    ->constrained('locations', 'loc_id')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->string('trsd_confrm_by', 30);
            $table->decimal('trsd_qty', 20, 4)->default(0);
            $table->string('trsd_add_by', 30);
            $table->string('trsd_upd_by', 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trsd_det');
    }
};

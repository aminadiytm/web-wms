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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id('st_id');
            $table->foreignId('ref_prd_id')
                    ->constrained('products', 'prd_id')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->foreignId('ref_wh_id')
                    ->constrained('warehouses', 'wh_id')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->foreignId('ref_loc_id')
                    ->constrained('locations', 'loc_id')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->decimal('qty_on_hand', 20, 4)->nullable()->default(0);
            $table->decimal('qty_reserved', 20, 4)->nullable()->default(0);
            $table->timestamps();
            
            $table->unique(
                ['ref_prd_id', 'ref_wh_id', 'ref_loc_id'],
                'stocks_product_warehouse_location_unique'
            );


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};

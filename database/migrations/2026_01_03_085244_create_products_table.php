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
        Schema::create('products', function (Blueprint $table) {
            $table->id('prd_id')->primary();
            $table->string('prd_code', 25)->unique();
            $table->string('prd_name', 40);
            $table->string('prd_unit', 10);
            // $table->unsignedBigInteger('ref_cat_id');
            // $table->foreign('ref_cat_id')->references('cat_id')->on('categories')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('ref_cat_id')
                    ->constrained('categories', 'cat_id')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->decimal('prd_min_stock', 20, 4)->nullable()->default(0);
            $table->string('prd_desc', 75)->nullable();
            $table->string('prd_add_by', 30);
            $table->string('prd_upd_by', 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

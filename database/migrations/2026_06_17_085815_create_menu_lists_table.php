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
        Schema::create('menu_lists', function (Blueprint $table) {
            $table->id('menu_id');

            $table->string('menu_code', 30)->unique(); 
            // contoh: MNU201

            $table->string('menu_group', 100)->nullable();
            // contoh: Master Data, Transactions, Inventory

            $table->string('menu_name', 100);
            // contoh: Inbound

            $table->string('menu_route')->nullable();
            // contoh: transaction.inbIndex

            $table->string('menu_icon')->nullable();
            // contoh: fas fa-dolly-flatbed

            $table->integer('sort_order')->default(0);
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_lists');
    }
};

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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id('wh_id');
            $table->string('wh_code', 25)->unique();
            $table->string('wh_name', 40);
            $table->string('wh_addr', 75);
            $table->string('wh_desc', 75)->nullable();
            $table->string('wh_add_by', 30);
            $table->string('wh_upd_by', 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};

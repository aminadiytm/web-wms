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
        Schema::create('locations', function (Blueprint $table) {
            $table->id('loc_id');
            $table->foreignId('ref_wh_id')
                    ->constrained('warehouses', 'wh_id')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            $table->string('loc_code', 25)->unique();
            $table->string('loc_desc', 75)->nullable();
            $table->boolean('loc_act')->default(true);
            $table->string('loc_add_by', 30);
            $table->string('loc_upd_by', 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};

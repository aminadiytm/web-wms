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
        Schema::create('code_mstr', function (Blueprint $table) {
            $table->id();
            $table->string('doc_type', 20);
            $table->string('period', 10);
            $table->unsignedBigInteger('last_number')->default(0);
            $table->timestamps();
            $table->unique(['doc_type', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('code_mstr');
    }
};

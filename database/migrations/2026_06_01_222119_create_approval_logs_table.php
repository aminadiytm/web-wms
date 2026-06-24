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
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id('approval_log_id');
            $table->unsignedBigInteger('approval_id');
            $table->string('action', 50); 
            // CREATED / SENT_WA / VIEWED / APPROVED / REJECTED / FAILED_WA
        
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        
            $table->foreign('approval_id')
                ->references('approval_id')
                ->on('approval_transactions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_logs');
    }
};

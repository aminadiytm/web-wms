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
        Schema::create('approval_transactions', function (Blueprint $table) {
            $table->id('approval_id');
        
            $table->string('transaction_type', 30); // INBOUND / OUTBOUND
            $table->unsignedBigInteger('transaction_id');
            $table->string('transaction_code', 50);
        
            $table->unsignedBigInteger('approval_route_id');
            $table->unsignedBigInteger('approver_user_id');
        
            $table->string('status', 30)->default('PENDING'); 
            // PENDING / APPROVED / REJECTED
        
            $table->string('token', 100)->unique();
            $table->timestamp('expired_at')->nullable();
        
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('remarks')->nullable();
        
            $table->string('wa_target', 30)->nullable();
            $table->timestamp('wa_sent_at')->nullable();
        
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        
            $table->foreign('approval_route_id')
                ->references('approval_route_id')
                ->on('approval_routes');
        
            $table->foreign('approver_user_id')
                ->references('id')
                ->on('users');
        
            $table->index(['transaction_type', 'transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_transactions');
    }
};

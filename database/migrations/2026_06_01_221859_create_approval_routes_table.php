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
        Schema::create('approval_routes', function (Blueprint $table) {
            $table->id('approval_route_id');
            $table->string('route_code', 50)->unique();
            $table->string('route_name', 150);
            $table->string('transaction_type', 30); // INBOUND / OUTBOUND
            $table->unsignedBigInteger('approver_user_id');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('add_by')->nullable();
            $table->string('upd_by')->nullable();
            $table->timestamps();
        
            $table->foreign('approver_user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_routes');
    }
};

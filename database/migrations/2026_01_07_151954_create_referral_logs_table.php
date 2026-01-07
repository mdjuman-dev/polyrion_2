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
        Schema::create('referral_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('User who received the commission');
            $table->foreignId('source_user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('User who made the deposit');
            $table->decimal('amount', 20, 8)->comment('Commission amount credited');
            $table->integer('level')->comment('Referral level: 1, 2, or 3');
            $table->decimal('percentage_applied', 5, 2)->comment('Percentage that was applied for this commission');
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['source_user_id', 'created_at']);
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_logs');
    }
};

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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('market_id')->constrained('markets')->cascadeOnDelete();
            
            // Trade details
            $table->enum('option', ['yes', 'no']); // User's prediction
            $table->decimal('amount', 15, 2); // Amount bet
            $table->decimal('price', 8, 4)->nullable(); // Price at which trade was placed (0.0001 to 0.9999)
            
            // Trade status
            $table->enum('status', ['pending', 'win', 'loss'])->default('pending');
            
            // Settlement info
            $table->decimal('payout', 15, 2)->nullable(); // Amount returned + profit (if win)
            $table->timestamp('settled_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index(['user_id', 'status']);
            $table->index(['market_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
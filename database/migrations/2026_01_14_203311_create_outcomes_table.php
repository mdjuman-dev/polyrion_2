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
        Schema::create('outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_id')->constrained('markets')->cascadeOnDelete();
            
            // Outcome details
            $table->string('name'); // e.g., "Yes", "No", "Up", "Down", "Over 2.5", "Under 2.5"
            $table->integer('order_index')->default(0); // Order for display (0, 1, 2, ...)
            
            // Trading statistics (calculated from trades)
            $table->decimal('total_traded_amount', 30, 8)->default(0); // Total amount traded on this outcome
            $table->decimal('total_shares', 30, 8)->default(0); // Total shares/tokens for this outcome
            $table->decimal('current_price', 10, 6)->default(0.5); // Current price (0-1 range)
            
            // Status
            $table->boolean('is_winning')->default(false); // Set to true when market is resolved and this outcome wins
            $table->boolean('active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['market_id', 'active']);
            $table->index(['market_id', 'order_index']);
            $table->unique(['market_id', 'name']); // Each outcome name must be unique per market
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outcomes');
    }
};

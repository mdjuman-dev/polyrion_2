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
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();

            // Basic info
            $table->string('slug')->unique();
            $table->string('question');
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();

            // Trading info
            $table->decimal('liquidity', 30, 8)->default(0);
            $table->decimal('liquidity_clob', 30, 8)->default(0);
            $table->decimal('volume', 30, 8)->default(0);
            $table->decimal('volume_24hr', 30, 8)->default(0);
            $table->decimal('volume_1wk', 30, 8)->default(0);
            $table->decimal('volume_1mo', 30, 8)->default(0);
            $table->decimal('volume_1yr', 30, 8)->default(0);

            $table->json('outcome_prices')->nullable();

            $table->boolean('active')->default(true);
            $table->boolean('closed')->default(false);
            $table->boolean('archived')->default(false);
            $table->boolean('featured')->default(false);
            $table->boolean('new')->default(false);
            $table->boolean('restricted')->default(false);
            $table->boolean('approved')->default(true);

            // Dates
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('markets');
    }
};
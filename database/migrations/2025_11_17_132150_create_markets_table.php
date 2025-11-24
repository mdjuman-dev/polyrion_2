<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();

            // Basic info
            $table->string('question');
            $table->string('groupItem_title')->nullable();
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->string('resolution_source')->nullable();

            // Trading info
            $table->decimal('liquidity_clob', 30, 8)->nullable()->default(0);
            $table->decimal('volume', 30, 8)->nullable()->default(0);
            $table->decimal('volume24hr', 30, 8)->nullable()->default(0);
            $table->decimal('volume1wk', 30, 8)->nullable()->default(0);
            $table->decimal('volume1mo', 30, 8)->nullable()->default(0);
            $table->decimal('volume1yr', 30, 8)->nullable()->default(0);

            $table->json('outcome_prices')->nullable();
            $table->json('outcomes')->nullable();

            $table->boolean('active')->default(true);
            $table->boolean('closed')->default(false);
            $table->boolean('archived')->default(false);
            $table->boolean('featured')->default(false);
            $table->boolean('new')->default(false);
            $table->boolean('approved')->default(true);
            $table->boolean('restricted')->default(false);

            // Dates (microseconds support)
            $table->datetime('start_date', 6)->nullable();
            $table->datetime('end_date', 6)->nullable();

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
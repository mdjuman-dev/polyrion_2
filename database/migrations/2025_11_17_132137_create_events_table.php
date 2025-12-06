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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category', 50)->nullable(); // Add category field directly
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();

            // Liquidity / Volume
            $table->decimal('liquidity', 30, 8)->nullable()->default(0);
            $table->decimal('volume', 30, 8)->nullable()->default(0);
            $table->decimal('volume_24hr', 30, 8)->nullable()->default(0);
            $table->decimal('volume_1wk', 30, 8)->nullable()->default(0);
            $table->decimal('volume_1mo', 30, 8)->nullable()->default(0);
            $table->decimal('volume_1yr', 30, 8)->nullable()->default(0);
            $table->decimal('liquidity_clob', 30, 8)->nullable()->default(0);

            // Status / Flags
            $table->boolean('active')->default(true);
            $table->boolean('closed')->default(false);
            $table->boolean('archived')->default(false);
            $table->boolean('new')->default(false);
            $table->boolean('featured')->default(false);

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
        Schema::dropIfExists('events');
    }
};

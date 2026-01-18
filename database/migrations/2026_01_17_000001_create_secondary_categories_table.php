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
        Schema::create('secondary_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Chattogram", "Dhaka", "International"
            $table->string('slug')->unique();
            $table->string('main_category'); // e.g., "Politics", "Sports", "Finance", "Crypto"
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Optional icon for the category
            $table->boolean('active')->default(true);
            $table->integer('display_order')->default(0); // For ordering categories
            $table->timestamps();

            // Index for faster queries
            $table->index('main_category');
            $table->index('active');
            $table->index(['main_category', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secondary_categories');
    }
};






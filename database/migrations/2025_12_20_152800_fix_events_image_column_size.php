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
        Schema::table('events', function (Blueprint $table) {
            // Change image and icon columns to text to handle long URLs
            $table->text('image')->nullable()->change();
            $table->text('icon')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Revert back to string (255 chars)
            $table->string('image')->nullable()->change();
            $table->string('icon')->nullable()->change();
        });
    }
};


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
        // Check if events table exists before trying to alter it
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                // Check if category column doesn't exist before adding
                if (!Schema::hasColumn('events', 'category')) {
                    $table->string('category', 50)->nullable()->after('title');
                    $table->index('category'); // Add index for faster category-based queries
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('events') && Schema::hasColumn('events', 'category')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropIndex(['category']);
                $table->dropColumn('category');
            });
        }
    }
};

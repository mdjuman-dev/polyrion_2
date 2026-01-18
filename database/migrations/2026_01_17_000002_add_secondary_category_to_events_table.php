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
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                // Add secondary_category_id if it doesn't exist
                if (!Schema::hasColumn('events', 'secondary_category_id')) {
                    $table->foreignId('secondary_category_id')
                        ->nullable()
                        ->after('category')
                        ->constrained('secondary_categories')
                        ->nullOnDelete();
                    
                    // Add index for faster queries
                    $table->index('secondary_category_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('events') && Schema::hasColumn('events', 'secondary_category_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropForeign(['secondary_category_id']);
                $table->dropIndex(['secondary_category_id']);
                $table->dropColumn('secondary_category_id');
            });
        }
    }
};






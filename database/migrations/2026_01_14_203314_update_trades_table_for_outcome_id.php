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
        Schema::table('trades', function (Blueprint $table) {
            // Add outcome_id foreign key
            if (!Schema::hasColumn('trades', 'outcome_id')) {
                $table->foreignId('outcome_id')->nullable()->after('market_id')->constrained('outcomes')->cascadeOnDelete();
            }
            
            // Make outcome enum nullable (for backward compatibility during migration)
            if (Schema::hasColumn('trades', 'outcome')) {
                $table->enum('outcome', ['YES', 'NO'])->nullable()->change();
            }
            
            // Add index for faster queries
            $table->index(['outcome_id', 'status']);
            $table->index(['market_id', 'outcome_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            if (Schema::hasColumn('trades', 'outcome_id')) {
                $table->dropForeign(['outcome_id']);
                $table->dropColumn('outcome_id');
            }
            
            // Restore outcome enum to not nullable
            if (Schema::hasColumn('trades', 'outcome')) {
                $table->enum('outcome', ['YES', 'NO'])->nullable(false)->change();
            }
        });
    }
};

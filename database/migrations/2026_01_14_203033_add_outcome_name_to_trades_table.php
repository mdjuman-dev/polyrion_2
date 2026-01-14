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
            // Add outcome_name to store actual outcome name (e.g., "Over 2.5", "Under 2.5", "Up", "Down")
            // This allows frontend to display actual outcome names while database stores YES/NO
            if (!Schema::hasColumn('trades', 'outcome_name')) {
                $table->string('outcome_name', 100)->nullable()->after('outcome');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            if (Schema::hasColumn('trades', 'outcome_name')) {
                $table->dropColumn('outcome_name');
            }
        });
    }
};

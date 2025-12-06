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
        Schema::table('markets', function (Blueprint $table) {
            // Add outcome_result column (yes, no, invalid)
            if (!Schema::hasColumn('markets', 'outcome_result')) {
                $table->string('outcome_result')->nullable()->after('final_result');
            }

            // Add is_closed column (boolean)
            if (!Schema::hasColumn('markets', 'is_closed')) {
                $table->boolean('is_closed')->default(false)->after('closed');
            }

            // Add settled column (boolean)
            if (!Schema::hasColumn('markets', 'settled')) {
                $table->boolean('settled')->default(false)->after('is_closed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('markets', function (Blueprint $table) {
            $table->dropColumn(['outcome_result', 'is_closed', 'settled']);
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column exists first
        if (!Schema::hasColumn('markets', 'final_outcome')) {
            Schema::table('markets', function (Blueprint $table) {
                if (Schema::hasColumn('markets', 'final_result')) {
                    // Add column first
                    $table->enum('final_outcome', ['YES', 'NO'])->nullable()->after('final_result');
                } else {
                    $table->enum('final_outcome', ['YES', 'NO'])->nullable()->after('close_time');
                }
            });

            // Copy data from final_result to final_outcome AFTER column is added
            if (Schema::hasColumn('markets', 'final_result')) {
                DB::statement("UPDATE markets SET final_outcome = UPPER(final_result) WHERE final_result IS NOT NULL");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('markets', function (Blueprint $table) {
            if (Schema::hasColumn('markets', 'final_outcome')) {
                $table->dropColumn('final_outcome');
            }
        });
    }
};

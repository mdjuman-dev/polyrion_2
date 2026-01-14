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
            // Add liquidity field (separate from liquidity_clob)
            // This stores the main liquidity value from Polymarket API
            $table->decimal('liquidity', 30, 8)->nullable()->default(0)->after('liquidity_clob');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('markets', function (Blueprint $table) {
            $table->dropColumn('liquidity');
        });
    }
};

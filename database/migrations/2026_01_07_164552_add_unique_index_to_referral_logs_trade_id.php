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
        Schema::table('referral_logs', function (Blueprint $table) {
            // Add unique composite index to prevent duplicate commission for same trade+user+level
            // This ensures a referrer can only get commission once per trade at each level
            $table->unique(['trade_id', 'user_id', 'level'], 'referral_logs_trade_user_level_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referral_logs', function (Blueprint $table) {
            $table->dropUnique('referral_logs_trade_user_level_unique');
        });
    }
};

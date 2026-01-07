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
        Schema::table('referral_settings', function (Blueprint $table) {
            $table->enum('commission_type', ['trade_volume', 'profit'])
                ->default('trade_volume')
                ->after('commission_percent')
                ->comment('Type of commission: trade_volume or profit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referral_settings', function (Blueprint $table) {
            $table->dropColumn('commission_type');
        });
    }
};

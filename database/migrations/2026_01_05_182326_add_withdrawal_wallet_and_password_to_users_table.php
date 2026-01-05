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
        Schema::table('users', function (Blueprint $table) {
            $table->string('binance_wallet_address')->nullable()->after('password');
            $table->string('metamask_wallet_address')->nullable()->after('binance_wallet_address');
            $table->string('withdrawal_password')->nullable()->after('metamask_wallet_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['binance_wallet_address', 'metamask_wallet_address', 'withdrawal_password']);
        });
    }
};

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
        // MySQL doesn't support easy modification of ENUM columns
        // We need to use raw SQL to add 'position_closed' and 'trade_payout' to the enum
        // Current enum: ['deposit', 'withdraw', 'purchase', 'refund', 'bonus', 'commission', 'trade']
        DB::statement("ALTER TABLE `wallet_transactions` MODIFY COLUMN `type` ENUM('deposit', 'withdraw', 'purchase', 'refund', 'bonus', 'commission', 'trade', 'position_closed', 'trade_payout') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'position_closed' and 'trade_payout' from the enum
        // Note: This will fail if there are any transactions with these types
        DB::statement("ALTER TABLE `wallet_transactions` MODIFY COLUMN `type` ENUM('deposit', 'withdraw', 'purchase', 'refund', 'bonus', 'commission', 'trade') NOT NULL");
    }
};

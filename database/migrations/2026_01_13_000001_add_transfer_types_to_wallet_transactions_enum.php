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
        // Add 'transfer_in' and 'transfer_out' to the enum
        // Current enum: ['deposit', 'withdraw', 'purchase', 'refund', 'bonus', 'commission', 'trade', 'position_closed', 'trade_payout']
        DB::statement("ALTER TABLE `wallet_transactions` MODIFY COLUMN `type` ENUM('deposit', 'withdraw', 'purchase', 'refund', 'bonus', 'commission', 'trade', 'position_closed', 'trade_payout', 'transfer_in', 'transfer_out') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'transfer_in' and 'transfer_out' from the enum
        // Note: This will fail if there are any transactions with these types
        DB::statement("ALTER TABLE `wallet_transactions` MODIFY COLUMN `type` ENUM('deposit', 'withdraw', 'purchase', 'refund', 'bonus', 'commission', 'trade', 'position_closed', 'trade_payout') NOT NULL");
    }
};


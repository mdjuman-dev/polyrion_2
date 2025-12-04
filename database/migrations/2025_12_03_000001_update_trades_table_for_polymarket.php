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
            // Add new columns if they don't exist
            if (!Schema::hasColumn('trades', 'outcome')) {
                $table->enum('outcome', ['YES', 'NO'])->after('market_id');
            }
            
            if (!Schema::hasColumn('trades', 'amount_invested')) {
                $table->decimal('amount_invested', 15, 2)->after('outcome');
            }
            
            if (!Schema::hasColumn('trades', 'token_amount')) {
                $table->decimal('token_amount', 20, 8)->after('amount_invested');
            }
            
            if (!Schema::hasColumn('trades', 'price_at_buy')) {
                $table->decimal('price_at_buy', 8, 6)->after('token_amount');
            }
            
            // Keep existing columns for backward compatibility but mark as nullable
            if (Schema::hasColumn('trades', 'option')) {
                $table->enum('option', ['yes', 'no'])->nullable()->change();
            }
            
            if (Schema::hasColumn('trades', 'amount')) {
                $table->decimal('amount', 15, 2)->nullable()->change();
            }
            
            if (Schema::hasColumn('trades', 'price')) {
                $table->decimal('price', 8, 4)->nullable()->change();
            }
            
            // Update status enum to match requirements
            if (Schema::hasColumn('trades', 'status')) {
                $table->enum('status', ['PENDING', 'WON', 'LOST'])->default('PENDING')->change();
            }
            
            // Add payout column if not exists
            if (!Schema::hasColumn('trades', 'payout')) {
                $table->decimal('payout', 15, 2)->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropColumn(['outcome', 'amount_invested', 'token_amount', 'price_at_buy']);
        });
    }
};



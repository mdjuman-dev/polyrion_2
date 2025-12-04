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
            // Polymarket specific fields for trading and charts
            $table->string('condition_id')->nullable()->after('slug');
            $table->string('group_item_threshold')->nullable()->after('groupItem_title');

            // Trading prices
            $table->decimal('best_bid', 10, 6)->nullable()->after('outcome_prices');
            $table->decimal('best_ask', 10, 6)->nullable()->after('best_bid');
            $table->decimal('last_trade_price', 10, 6)->nullable()->after('best_ask');
            $table->decimal('spread', 10, 6)->nullable()->after('last_trade_price');

            // Price changes
            $table->decimal('one_day_price_change', 10, 6)->nullable()->after('spread');
            $table->decimal('one_week_price_change', 10, 6)->nullable()->after('one_day_price_change');
            $table->decimal('one_month_price_change', 10, 6)->nullable()->after('one_week_price_change');

            // Chart and display
            $table->string('series_color')->nullable()->after('one_month_price_change');
            $table->decimal('competitive', 10, 8)->nullable()->after('series_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('markets', function (Blueprint $table) {
            $table->dropColumn([
                'condition_id',
                'group_item_threshold',
                'best_bid',
                'best_ask',
                'last_trade_price',
                'spread',
                'one_day_price_change',
                'one_week_price_change',
                'one_month_price_change',
                'series_color',
                'competitive'
            ]);
        });
    }
};

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
            // Store API-provided base values (separate from internal trades)
            $table->decimal('api_volume', 30, 8)->nullable()->default(0)->after('volume');
            $table->decimal('api_liquidity', 30, 8)->nullable()->default(0)->after('liquidity');
            $table->decimal('api_volume24hr', 30, 8)->nullable()->default(0)->after('volume24hr');
            
            // Track internal user trade contributions (always additive)
            $table->decimal('internal_volume', 30, 8)->nullable()->default(0)->after('api_volume');
            $table->decimal('internal_liquidity', 30, 8)->nullable()->default(0)->after('api_liquidity');
            $table->decimal('internal_volume24hr', 30, 8)->nullable()->default(0)->after('api_volume24hr');
            
            // Index for performance
            $table->index('internal_volume');
            $table->index('internal_liquidity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('markets', function (Blueprint $table) {
            $table->dropIndex(['internal_volume']);
            $table->dropIndex(['internal_liquidity']);
            
            $table->dropColumn([
                'api_volume',
                'api_liquidity',
                'api_volume24hr',
                'internal_volume',
                'internal_liquidity',
                'internal_volume24hr',
            ]);
        });
    }
};

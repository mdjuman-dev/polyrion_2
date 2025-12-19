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
        // Indexes for events table
        Schema::table('events', function (Blueprint $table) {
            // Composite index for active/closed filtering (most common query)
            if (!$this->hasIndex('events', 'events_active_closed_index')) {
                $table->index(['active', 'closed'], 'events_active_closed_index');
            }
            // Index for volume sorting
            if (!$this->hasIndex('events', 'events_volume_24hr_index')) {
                $table->index('volume_24hr', 'events_volume_24hr_index');
            }
            // Index for end_date filtering
            if (!$this->hasIndex('events', 'events_end_date_index')) {
                $table->index('end_date', 'events_end_date_index');
            }
        });

        // Indexes for markets table
        Schema::table('markets', function (Blueprint $table) {
            // Composite index for closed/close_time filtering
            if (!$this->hasIndex('markets', 'markets_closed_close_time_index')) {
                $table->index(['closed', 'close_time'], 'markets_closed_close_time_index');
            }
            // Index for event_id (foreign key - should already exist but ensure it)
            if (!$this->hasIndex('markets', 'markets_event_id_index')) {
                $table->index('event_id', 'markets_event_id_index');
            }
            // Index for active status
            if (!$this->hasIndex('markets', 'markets_active_index')) {
                $table->index('active', 'markets_active_index');
            }
        });

        // Indexes for trades table
        Schema::table('trades', function (Blueprint $table) {
            // Composite index for user_id and status (most common query pattern)
            if (!$this->hasIndex('trades', 'trades_user_id_status_index')) {
                $table->index(['user_id', 'status'], 'trades_user_id_status_index');
            }
            // Index for market_id
            if (!$this->hasIndex('trades', 'trades_market_id_index')) {
                $table->index('market_id', 'trades_market_id_index');
            }
            // Index for created_at (for sorting)
            if (!$this->hasIndex('trades', 'trades_created_at_index')) {
                $table->index('created_at', 'trades_created_at_index');
            }
        });

        // Indexes for wallets table
        Schema::table('wallets', function (Blueprint $table) {
            // Index for user_id (should exist but ensure it)
            if (!$this->hasIndex('wallets', 'wallets_user_id_index')) {
                $table->index('user_id', 'wallets_user_id_index');
            }
        });

        // Indexes for deposits table
        Schema::table('deposits', function (Blueprint $table) {
            // Composite index for user_id and status
            if (!$this->hasIndex('deposits', 'deposits_user_id_status_index')) {
                $table->index(['user_id', 'status'], 'deposits_user_id_status_index');
            }
            // Index for merchant_trade_no (for lookups)
            if (!$this->hasIndex('deposits', 'deposits_merchant_trade_no_index')) {
                $table->index('merchant_trade_no', 'deposits_merchant_trade_no_index');
            }
        });

        // Indexes for withdrawals table
        Schema::table('withdrawals', function (Blueprint $table) {
            // Composite index for user_id and status
            if (!$this->hasIndex('withdrawals', 'withdrawals_user_id_status_index')) {
                $table->index(['user_id', 'status'], 'withdrawals_user_id_status_index');
            }
        });

        // Indexes for event_comments table
        if (Schema::hasTable('event_comments')) {
            Schema::table('event_comments', function (Blueprint $table) {
                // Composite index for event_id and is_active
                if (!$this->hasIndex('event_comments', 'event_comments_event_id_active_index')) {
                    $table->index(['event_id', 'is_active'], 'event_comments_event_id_active_index');
                }
                // Index for parent_comment_id
                if (!$this->hasIndex('event_comments', 'event_comments_parent_id_index')) {
                    $table->index('parent_comment_id', 'event_comments_parent_id_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('events_active_closed_index');
            $table->dropIndex('events_volume_24hr_index');
            $table->dropIndex('events_end_date_index');
        });

        Schema::table('markets', function (Blueprint $table) {
            $table->dropIndex('markets_closed_close_time_index');
            $table->dropIndex('markets_event_id_index');
            $table->dropIndex('markets_active_index');
        });

        Schema::table('trades', function (Blueprint $table) {
            $table->dropIndex('trades_user_id_status_index');
            $table->dropIndex('trades_market_id_index');
            $table->dropIndex('trades_created_at_index');
        });

        Schema::table('wallets', function (Blueprint $table) {
            $table->dropIndex('wallets_user_id_index');
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->dropIndex('deposits_user_id_status_index');
            $table->dropIndex('deposits_merchant_trade_no_index');
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropIndex('withdrawals_user_id_status_index');
        });

        if (Schema::hasTable('event_comments')) {
            Schema::table('event_comments', function (Blueprint $table) {
                $table->dropIndex('event_comments_event_id_active_index');
                $table->dropIndex('event_comments_parent_id_index');
            });
        }
    }

    /**
     * Check if index exists
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        
        $result = $connection->select(
            "SELECT COUNT(*) as count FROM information_schema.statistics 
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$databaseName, $table, $indexName]
        );
        
        return $result[0]->count > 0;
    }
};

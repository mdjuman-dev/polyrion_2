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
        // Step 1: Drop the foreign key constraint on source_user_id
        Schema::table('referral_logs', function (Blueprint $table) {
            $table->dropForeign(['source_user_id']);
        });

        // Step 2: Drop the composite index (must be done after dropping FK)
        Schema::table('referral_logs', function (Blueprint $table) {
            $table->dropIndex(['source_user_id', 'created_at']);
        });

        // Step 3: Rename source_user_id to from_user_id using raw SQL
        DB::statement('ALTER TABLE referral_logs CHANGE source_user_id from_user_id BIGINT UNSIGNED NOT NULL');

        // Step 4: Recreate the foreign key constraint with new column name
        Schema::table('referral_logs', function (Blueprint $table) {
            $table->foreign('from_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        // Step 5: Add trade_id column
        Schema::table('referral_logs', function (Blueprint $table) {
            $table->foreignId('trade_id')
                ->nullable()
                ->after('from_user_id')
                ->constrained('trades')
                ->onDelete('cascade')
                ->comment('Trade that generated this commission (null for deposit-based)');
        });

        // Step 6: Add new indexes
        Schema::table('referral_logs', function (Blueprint $table) {
            $table->index(['from_user_id', 'created_at']);
            $table->index(['trade_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Drop trade_id foreign key and column
        Schema::table('referral_logs', function (Blueprint $table) {
            $table->dropForeign(['trade_id']);
            $table->dropColumn('trade_id');
        });

        // Step 2: Drop indexes
        Schema::table('referral_logs', function (Blueprint $table) {
            $table->dropIndex(['from_user_id', 'created_at']);
        });

        // Step 3: Drop foreign key on from_user_id
        Schema::table('referral_logs', function (Blueprint $table) {
            $table->dropForeign(['from_user_id']);
        });

        // Step 4: Rename back using raw SQL
        DB::statement('ALTER TABLE referral_logs CHANGE from_user_id source_user_id BIGINT UNSIGNED NOT NULL');
        
        // Step 5: Recreate foreign key and index
        Schema::table('referral_logs', function (Blueprint $table) {
            $table->foreign('source_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->index(['source_user_id', 'created_at']);
        });
    }
};

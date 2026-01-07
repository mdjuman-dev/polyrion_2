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
            $table->foreignId('referrer_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->onDelete('set null');
            
            // Check if balance column already exists before adding
            if (!Schema::hasColumn('users', 'balance')) {
                $table->decimal('balance', 20, 8)
                    ->default(0)
                    ->after('referrer_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referrer_id']);
            $table->dropColumn('referrer_id');
            
            // Only drop balance if it was added by this migration
            if (Schema::hasColumn('users', 'balance')) {
                $table->dropColumn('balance');
            }
        });
    }
};

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
        Schema::table('referral_logs', function (Blueprint $table) {
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed')->after('percentage_applied');
            $table->decimal('trade_amount', 12, 2)->nullable()->after('amount')->comment('Original trade amount that generated this commission');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referral_logs', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'trade_amount']);
        });
    }
};


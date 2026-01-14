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
        // Update all existing wallets to have wallet_type = 'main'
        // This ensures backward compatibility
        DB::table('wallets')
            ->whereNull('wallet_type')
            ->orWhere('wallet_type', '')
            ->update(['wallet_type' => 'main']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - wallet_type column will be dropped separately
    }
};


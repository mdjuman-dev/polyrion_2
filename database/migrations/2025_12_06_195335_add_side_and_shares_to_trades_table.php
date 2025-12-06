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
            // Add side column if it doesn't exist (yes or no)
            if (!Schema::hasColumn('trades', 'side')) {
                $table->enum('side', ['yes', 'no'])->nullable()->after('market_id');
            }
            
            // Add shares column if it doesn't exist (amount / price)
            if (!Schema::hasColumn('trades', 'shares')) {
                $table->decimal('shares', 20, 8)->nullable()->after('price');
            }
            
            // Ensure status column supports lowercase values
            if (Schema::hasColumn('trades', 'status')) {
                // Note: Laravel doesn't support changing enum values easily
                // We'll handle this in the model/application layer
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            if (Schema::hasColumn('trades', 'side')) {
                $table->dropColumn('side');
            }
            if (Schema::hasColumn('trades', 'shares')) {
                $table->dropColumn('shares');
            }
        });
    }
};

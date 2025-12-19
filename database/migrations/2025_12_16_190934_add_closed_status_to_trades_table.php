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
        // MySQL doesn't support easy modification of ENUM columns
        // We need to use raw SQL to add 'CLOSED' to the enum
        // The current enum is ['PENDING', 'WON', 'LOST'] based on the previous migration
        DB::statement("ALTER TABLE `trades` MODIFY COLUMN `status` ENUM('PENDING', 'WON', 'LOST', 'CLOSED') DEFAULT 'PENDING'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'CLOSED' from the enum
        // Note: This will fail if there are any trades with 'CLOSED' status
        DB::statement("ALTER TABLE `trades` MODIFY COLUMN `status` ENUM('PENDING', 'WON', 'LOST') DEFAULT 'PENDING'");
    }
};

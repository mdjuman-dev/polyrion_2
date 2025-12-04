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
        Schema::table('events', function (Blueprint $table) {
            // Polymarket specific fields
            $table->string('ticker')->nullable()->after('slug');
            $table->decimal('competitive', 10, 8)->nullable()->after('liquidity_clob');
            $table->integer('comment_count')->default(0)->after('competitive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['ticker', 'competitive', 'comment_count']);
        });
    }
};

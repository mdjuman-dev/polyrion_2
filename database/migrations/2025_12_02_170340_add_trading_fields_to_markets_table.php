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
            // Trading fields
            $table->datetime('close_time')->nullable()->after('end_date');
            $table->enum('final_result', ['yes', 'no'])->nullable()->after('close_time');
            $table->timestamp('result_set_at')->nullable()->after('final_result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('markets', function (Blueprint $table) {
            $table->dropColumn(['close_time', 'final_result', 'result_set_at']);
        });
    }
};
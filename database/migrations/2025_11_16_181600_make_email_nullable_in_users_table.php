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
            // Drop unique index first
            $table->dropUnique(['email']);
        });
        
        Schema::table('users', function (Blueprint $table) {
            // Make email nullable
            $table->string('email')->nullable()->change();
        });
        
        Schema::table('users', function (Blueprint $table) {
            // Add unique index back (allows multiple NULLs)
            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop unique index
            $table->dropUnique(['email']);
        });
        
        Schema::table('users', function (Blueprint $table) {
            // Make email not nullable
            $table->string('email')->nullable(false)->change();
        });
        
        Schema::table('users', function (Blueprint $table) {
            // Add unique index back
            $table->unique('email');
        });
    }
};
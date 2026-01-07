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
        Schema::create('referral_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('level')->unique()->comment('Referral level: 1, 2, or 3');
            $table->decimal('commission_percent', 5, 2)->comment('Commission percentage for this level');
            $table->boolean('is_active')->default(true)->comment('Whether this level is currently active');
            $table->timestamps();
            
            $table->index(['level', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_settings');
    }
};

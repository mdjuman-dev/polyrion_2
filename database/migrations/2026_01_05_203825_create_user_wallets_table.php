<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('wallet_type'); // metamask, binance, other
            $table->string('wallet_address');
            $table->string('network')->nullable(); // ERC20, BEP20, TRC20, BTC, LTC, Ethereum, BSC, Polygon, etc.
            $table->string('wallet_name')->nullable();
            $table->string('memo_tag')->nullable(); // For BNB, XRP, XLM, etc.
            $table->text('signature_verification')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->index(['user_id', 'wallet_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_wallets');
    }
};

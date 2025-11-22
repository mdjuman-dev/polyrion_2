<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class BinancePayController extends Controller
{
    public function createOrder()
    {
        $payload = [
            "merchantId" => env('BINANCE_PAY_MERCHANT_ID'),
            "merchantTradeNo" => time(),
            "totalFee" => "5",
            "currency" => "USDT",
            "productDetail" => "Fund Deposit",
            "productId" => "10001"
        ];

        $json   = json_encode($payload);
        $nonce  = uniqid();
        $timestamp = round(microtime(true) * 1000);

        $signatureData = $timestamp . "\n" . $nonce . "\n" . $json . "\n";
        $signature = strtoupper(hash_hmac('SHA512', $signatureData, env('BINANCE_PAY_SECRET_KEY')));

        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            "BinancePay-Timestamp" => $timestamp,
            "BinancePay-Nonce" => $nonce,
            "BinancePay-Certificate-SN" => env('BINANCE_PAY_API_KEY'),
            "BinancePay-Signature" => $signature
        ])->post(env('BINANCE_PAY_BASE_URL') . "/binancepay/openapi/order", $payload);

        return $response->json();
    }
}
<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Deposit;
use App\Models\Wallet;
use App\Models\WalletTransaction;

class BinancePayController extends Controller
{

    private $apiKey;
    private $secretKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = 'jCaL0Y7arCkS75CdRW5WONIaw7wifNf5QP9zA2JM0oqdSwPPIARboHjcq2AOLUva';
        $this->secretKey = 'nte8RCgMKDoffXI4H5uM9ErrsbQASMDfgmPd6W27oJVW7kTLa0ItiAbMZaShXL34';
        $this->baseUrl = 'https://bpay.binanceapi.com';
    }
    // signature for Binance Pay API
    private function generateSignature($timestamp, $nonce, $body)
    {
        $payload = $timestamp . "\n" . $nonce . "\n" . $body . "\n";
        return strtoupper(hash_hmac('sha512', $payload, $this->secretKey));
    }

    // Create payment - Frontend method
    public function createPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10|max:10000',
            'currency' => 'nullable|string|max:10',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $amount = $request->amount;
            $currency = $request->currency ?? 'USDT';
            $merchantTradeNo = 'DEP' . time() . '_' . $user->id . '_' . uniqid();

            // Create deposit record
            $deposit = Deposit::create([
                'user_id' => $user->id,
                'merchant_trade_no' => $merchantTradeNo,
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'pending',
                'payment_method' => 'binance_pay',
            ]);

            // Prepare order data for Binance Pay
            $orderData = [
                'env' => [
                    'terminalType' => 'WEB'
                ],
                'merchantTradeNo' => $merchantTradeNo,
                'orderAmount' => $amount,
                'currency' => $currency,
                'goods' => [
                    'goodsType' => '01',
                    'goodsCategory' => 'D000',
                    'referenceGoodsId' => 'DEPOSIT_' . $deposit->id,
                    'goodsName' => 'Wallet Deposit',
                    'goodsDetail' => 'Deposit to wallet account'
                ],
                'buyer' => [
                    'referenceBuyerId' => (string) $user->id,
                    'buyerName' => [
                        'firstName' => $user->name ?? 'User',
                        'lastName' => ''
                    ]
                ],
                'returnUrl' => route('binance.return'),
                'cancelUrl' => route('binance.cancel'),
            ];

            // Create order with Binance Pay
            $response = $this->createOrder($orderData);

            if (isset($response['status']) && $response['status'] === 'SUCCESS') {
                // Update deposit with prepay ID
                $deposit->prepay_id = $response['data']['prepayId'] ?? null;
                $deposit->response_data = $response;
                $deposit->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'prepayId' => $response['data']['prepayId'] ?? null,
                    'checkoutUrl' => $response['data']['checkoutUrl'] ?? null,
                    'deposit_id' => $deposit->id
                ]);
            }

            DB::rollBack();
            throw new \Exception('Failed to create payment order');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Binance Pay create payment error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment: ' . $e->getMessage()
            ], 500);
        }
    }

    // payment order - Internal method
    public function createOrder($orderData)
    {
        $timestamp = floor(microtime(true) * 1000);
        $nonce = $this->generateNonce();
        $body = json_encode($orderData);

        $signature = $this->generateSignature($timestamp, $nonce, $body);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'BinancePay-Timestamp' => $timestamp,
            'BinancePay-Nonce' => $nonce,
            'BinancePay-Certificate-SN' => $this->apiKey,
            'BinancePay-Signature' => $signature,
        ])->post($this->baseUrl . '/binancepay/openapi/v2/order', $orderData);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Binance Pay Error', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        throw new \Exception('Failed to create Binance Pay order: ' . $response->body());
    }

    // order status
    public function queryOrder($merchantTradeNo = null, $prepayId = null)
    {
        $timestamp = floor(microtime(true) * 1000);
        $nonce = $this->generateNonce();

        $data = [];
        if ($merchantTradeNo) {
            $data['merchantTradeNo'] = $merchantTradeNo;
        }
        if ($prepayId) {
            $data['prepayId'] = $prepayId;
        }

        $body = json_encode($data);
        $signature = $this->generateSignature($timestamp, $nonce, $body);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'BinancePay-Timestamp' => $timestamp,
            'BinancePay-Nonce' => $nonce,
            'BinancePay-Certificate-SN' => $this->apiKey,
            'BinancePay-Signature' => $signature,
        ])->post($this->baseUrl . '/binancepay/openapi/v2/order/query', $data);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to query Binance Pay order: ' . $response->body());
    }

    //Close an order
    public function closeOrder($merchantTradeNo)
    {
        $timestamp = floor(microtime(true) * 1000);
        $nonce = $this->generateNonce();

        $data = ['merchantTradeNo' => $merchantTradeNo];
        $body = json_encode($data);
        $signature = $this->generateSignature($timestamp, $nonce, $body);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'BinancePay-Timestamp' => $timestamp,
            'BinancePay-Nonce' => $nonce,
            'BinancePay-Certificate-SN' => $this->apiKey,
            'BinancePay-Signature' => $signature,
        ])->post($this->baseUrl . '/binancepay/openapi/order/close', $data);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to close Binance Pay order: ' . $response->body());
    }

    //Verify webhook signature
    public function verifyWebhook($payload, $signature, $timestamp, $nonce)
    {
        $computedSignature = $this->generateSignature($timestamp, $nonce, $payload);
        return hash_equals($computedSignature, $signature);
    }

    // Generate random nonce
    private function generateNonce($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }

    // Return page after payment
    public function return(Request $request)
    {
        $merchantTradeNo = $request->get('merchantTradeNo');
        $status = $request->get('status');

        if ($merchantTradeNo) {
            // Query order status
            try {
                $orderStatus = $this->queryOrder($merchantTradeNo);
                if (isset($orderStatus['data']['status'])) {
                    $status = $orderStatus['data']['status'];
                }
            } catch (\Exception $e) {
                Log::error('Failed to query order on return', [
                    'merchant_trade_no' => $merchantTradeNo,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return view('frontend.binance.return', [
            'status' => $status,
            'merchantTradeNo' => $merchantTradeNo
        ]);
    }

    // Cancel page
    public function cancel(Request $request)
    {
        return view('frontend.binance.cancel');
    }

    public function manualProcess(Request $request, $depositId)
    {
        try {
            DB::beginTransaction();

            $deposit = Deposit::findOrFail($depositId);

            if ($deposit->status !== 'pending') {
                return back()->with([
                    'message' => 'Deposit is not in pending status',
                    'alert-type' => 'error'
                ]);
            }

            $this->processDeposit($deposit);

            DB::commit();

            Log::info('Manual deposit processed', [
                'deposit_id' => $deposit->id,
                'merchant_trade_no' => $deposit->merchant_trade_no,
                'amount' => $deposit->amount,
                'user_id' => $deposit->user_id
            ]);

            return back()->with([
                'message' => 'Deposit processed successfully',
                'alert-type' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual deposit processing failed', [
                'deposit_id' => $depositId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with([
                'message' => 'Failed to process deposit: ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }

    public function webhook(Request $request)
    {
        try {
            $payload = $request->getContent();
            $signature = $request->header('BinancePay-Signature');
            $timestamp = $request->header('BinancePay-Timestamp');
            $nonce = $request->header('BinancePay-Nonce');

            // Verify webhook signature
            if (!$this->verifyWebhook($payload, $signature, $timestamp, $nonce)) {
                Log::warning('Binance Pay webhook signature verification failed', [
                    'timestamp' => $timestamp,
                    'nonce' => $nonce
                ]);
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            $data = json_decode($payload, true);

            Log::info('Binance Pay webhook received', [
                'event_type' => $data['bizType'] ?? 'unknown',
                'data' => $data
            ]);

            // Handle payment status update
            if (isset($data['bizType']) && $data['bizType'] === 'PAY') {
                $this->autoProcessPayment($data);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Binance Pay webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->getContent()
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    private function autoProcessPayment($webhookData)
    {
        try {
            DB::beginTransaction();

            $merchantTradeNo = $webhookData['data']['merchantTradeNo'] ?? null;
            $status = $webhookData['data']['status'] ?? null;
            $transactionId = $webhookData['data']['transactionId'] ?? null;

            if (!$merchantTradeNo) {
                throw new \Exception('Merchant trade number not found in webhook data');
            }

            $deposit = Deposit::where('merchant_trade_no', $merchantTradeNo)->first();

            if (!$deposit) {
                Log::warning('Deposit not found for webhook', [
                    'merchant_trade_no' => $merchantTradeNo
                ]);
                DB::rollBack();
                return;
            }

            // Update deposit with webhook data
            $deposit->transaction_id = $transactionId;
            $deposit->response_data = $webhookData;

            // Process based on status
            if ($status === 'SUCCESS' && $deposit->status === 'pending') {
                $this->processDeposit($deposit);
            } elseif ($status === 'EXPIRED' || $status === 'CANCELED') {
                $deposit->status = 'expired';
                $deposit->save();
            } elseif ($status === 'ERROR') {
                $deposit->status = 'failed';
                $deposit->save();
            }

            DB::commit();

            Log::info('Auto payment processed from webhook', [
                'deposit_id' => $deposit->id,
                'merchant_trade_no' => $merchantTradeNo,
                'status' => $status,
                'deposit_status' => $deposit->status
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Auto payment processing failed', [
                'error' => $e->getMessage(),
                'webhook_data' => $webhookData
            ]);
            throw $e;
        }
    }

    private function processDeposit(Deposit $deposit)
    {
        $user = $deposit->user;

        // Get or create wallet
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'currency' => $deposit->currency ?? 'USDT', 'status' => 'active']
        );

        $balanceBefore = $wallet->balance;
        $wallet->balance += $deposit->amount;
        $wallet->save();

        // Update deposit status
        $deposit->status = 'completed';
        $deposit->completed_at = now();
        $deposit->save();

        // Create wallet transaction
        WalletTransaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'deposit',
            'amount' => $deposit->amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $wallet->balance,
            'reference_type' => Deposit::class,
            'reference_id' => $deposit->id,
            'description' => 'Deposit via Binance Pay',
            'metadata' => [
                'merchant_trade_no' => $deposit->merchant_trade_no,
                'prepay_id' => $deposit->prepay_id,
                'transaction_id' => $deposit->transaction_id,
                'currency' => $deposit->currency
            ]
        ]);

        Log::info('Deposit processed successfully', [
            'deposit_id' => $deposit->id,
            'user_id' => $user->id,
            'amount' => $deposit->amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $wallet->balance
        ]);
    }

    // Manual payment verification using query code
    public function manualVerify(Request $request)
    {
        $request->validate([
            'query_code' => 'required|string',
            'amount' => 'required|numeric|min:10|max:10000',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $queryCode = $request->query_code;
            $amount = $request->amount;

            // Try to find deposit by merchant trade number or prepay ID
            $deposit = Deposit::where(function ($query) use ($queryCode) {
                $query->where('merchant_trade_no', $queryCode)
                    ->orWhere('prepay_id', $queryCode)
                    ->orWhere('transaction_id', $queryCode);
            })->where('user_id', $user->id)->first();

            if (!$deposit) {
                // If deposit not found, query Binance Pay API
                try {
                    $orderStatus = $this->queryOrder($queryCode);

                    if (isset($orderStatus['status']) && $orderStatus['status'] === 'SUCCESS') {
                        $orderData = $orderStatus['data'] ?? [];
                        $merchantTradeNo = $orderData['merchantTradeNo'] ?? $queryCode;
                        $status = $orderData['status'] ?? null;
                        $transactionId = $orderData['transactionId'] ?? null;
                        $orderAmount = $orderData['orderAmount'] ?? $amount;

                        // Check if amount matches
                        if (abs($orderAmount - $amount) > 0.01) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Amount mismatch. Please enter the correct amount.'
                            ], 400);
                        }

                        // Check if payment was successful
                        if ($status !== 'SUCCESS') {
                            return response()->json([
                                'success' => false,
                                'message' => 'Payment status is not successful. Current status: ' . ($status ?? 'Unknown')
                            ], 400);
                        }

                        // Create or update deposit record
                        $deposit = Deposit::updateOrCreate(
                            ['merchant_trade_no' => $merchantTradeNo],
                            [
                                'user_id' => $user->id,
                                'prepay_id' => $orderData['prepayId'] ?? null,
                                'transaction_id' => $transactionId,
                                'amount' => $orderAmount,
                                'currency' => $orderData['currency'] ?? 'USDT',
                                'status' => 'pending',
                                'payment_method' => 'binance_pay',
                                'response_data' => $orderStatus
                            ]
                        );
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Payment not found or invalid query code.'
                        ], 404);
                    }
                } catch (\Exception $e) {
                    Log::error('Binance Pay query order failed', [
                        'query_code' => $queryCode,
                        'error' => $e->getMessage()
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to verify payment. Please check your query code and try again.'
                    ], 400);
                }
            }

            // Verify deposit belongs to user
            if ($deposit->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This payment does not belong to your account.'
                ], 403);
            }

            // Verify amount matches
            if (abs($deposit->amount - $amount) > 0.01) {
                return response()->json([
                    'success' => false,
                    'message' => 'Amount mismatch. Please enter the correct amount: $' . number_format($deposit->amount, 2)
                ], 400);
            }

            // Check if already processed
            if ($deposit->status === 'completed') {
                $wallet = Wallet::where('user_id', $user->id)->first();
                return response()->json([
                    'success' => false,
                    'message' => 'This payment has already been processed.',
                    'balance' => $wallet ? number_format($wallet->balance, 2) : '0.00'
                ], 400);
            }

            // Process the deposit
            if ($deposit->status === 'pending') {
                $this->processDeposit($deposit);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment status is: ' . $deposit->status . '. Cannot process.'
                ], 400);
            }

            DB::commit();

            $wallet = Wallet::where('user_id', $user->id)->first();

            return response()->json([
                'success' => true,
                'message' => 'Payment verified and processed successfully!',
                'amount' => number_format($deposit->amount, 2),
                'balance' => $wallet ? number_format($wallet->balance, 2) : '0.00'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual payment verification failed', [
                'user_id' => Auth::id(),
                'query_code' => $request->query_code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment: ' . $e->getMessage()
            ], 500);
        }
    }
}
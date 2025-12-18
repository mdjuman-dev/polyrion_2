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
use App\Models\GlobalSetting;

class BinancePayController extends Controller
{

    private $apiKey;
    private $secretKey;
    private $baseUrl;

    public function __construct()
    {
        // Use database settings first, then config, then env, fallback to hardcoded for backward compatibility
        $this->apiKey = GlobalSetting::getValue('binance_api_key') 
            ?: config('services.binance.api_key') 
            ?: env('BINANCE_API_KEY', 'jCaL0Y7arCkS75CdRW5WONIaw7wifNf5QP9zA2JM0oqdSwPPIARboHjcq2AOLUva');
        
        $this->secretKey = GlobalSetting::getValue('binance_secret_key') 
            ?: config('services.binance.secret_key') 
            ?: env('BINANCE_SECRET_KEY', 'nte8RCgMKDoffXI4H5uM9ErrsbQASMDfgmPd6W27oJVW7kTLa0ItiAbMZaShXL34');
        
        // Normalize base URL - remove trailing slashes to prevent double slashes in URLs
        $baseUrl = GlobalSetting::getValue('binance_base_url') 
            ?: config('services.binance.base_url') 
            ?: env('BINANCE_BASE_URL', 'https://bpay.binanceapi.com');
        $this->baseUrl = rtrim($baseUrl, '/');
        
        // Log configuration for debugging (without exposing secrets)
        $outgoingIp = $this->getOutgoingServerIp();
        Log::info('Binance Pay Configuration', [
            'base_url' => $this->baseUrl,
            'api_key_set' => !empty($this->apiKey),
            'secret_key_set' => !empty($this->secretKey),
            'client_ip' => request()->ip(),
            'outgoing_server_ip' => $outgoingIp
        ]);
    }

    /**
     * Get the actual outgoing server IP address that external services will see
     * This is different from request()->ip() which is the client's IP
     */
    private function getOutgoingServerIp()
    {
        // First, try to get from environment variable if set
        $envIp = env('SERVER_OUTGOING_IP');
        if ($envIp) {
            return $envIp;
        }

        // Try to get from a cached value (to avoid making external requests every time)
        $cacheKey = 'binance_outgoing_ip';
        $cachedIp = cache()->get($cacheKey);
        if ($cachedIp) {
            return $cachedIp;
        }

        // Try to detect from server configuration
        try {
            // Method 1: Try to get from $_SERVER variables
            if (isset($_SERVER['SERVER_ADDR']) && filter_var($_SERVER['SERVER_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $ip = $_SERVER['SERVER_ADDR'];
                cache()->put($cacheKey, $ip, now()->addHours(24));
                return $ip;
            }

            // Method 2: Make a quick request to a service that returns our IP
            // Using a lightweight service with short timeout
            try {
                $response = Http::timeout(3)->get('https://api.ipify.org?format=json');
                if ($response->successful()) {
                    $data = $response->json();
                    $ip = $data['ip'] ?? null;
                    if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
                        cache()->put($cacheKey, $ip, now()->addHours(24));
                        return $ip;
                    }
                }
            } catch (\Exception $e) {
                // Fallback if external service fails
                Log::warning('Failed to get outgoing IP from external service', [
                    'error' => $e->getMessage()
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to detect outgoing server IP', [
                'error' => $e->getMessage()
            ]);
        }

        // Fallback: return null if we can't determine it
        return null;
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
            $outgoingIp = $this->getOutgoingServerIp();
            Log::error('Binance Pay create payment error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'client_ip' => request()->ip(),
                'outgoing_server_ip' => $outgoingIp
            ]);

            // Extract user-friendly error message
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, '400004') !== false || strpos($errorMessage, 'API authentication failed') !== false) {
                $ipToShow = $outgoingIp ?: 'unknown';
                $errorMessage = 'API authentication failed. Your server IP (' . $ipToShow . ') needs to be whitelisted in your Binance Pay merchant dashboard. Go to: Settings → API Management → IP Whitelist and add: ' . $ipToShow;
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error_code' => $this->extractErrorCode($e->getMessage())
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

        // Ensure URL doesn't have double slashes
        $url = rtrim($this->baseUrl, '/') . '/binancepay/openapi/v2/order';
        $outgoingIp = $this->getOutgoingServerIp();
        
        Log::info('Binance Pay API Request', [
            'url' => $url,
            'timestamp' => $timestamp,
            'nonce' => $nonce,
            'merchant_trade_no' => $orderData['merchantTradeNo'] ?? null,
            'client_ip' => request()->ip(),
            'outgoing_server_ip' => $outgoingIp
        ]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'User-Agent' => 'Laravel-BinancePay-Integration/1.0',
            'BinancePay-Timestamp' => $timestamp,
            'BinancePay-Nonce' => $nonce,
            'BinancePay-Certificate-SN' => $this->apiKey,
            'BinancePay-Signature' => $signature,
        ])->timeout(30)->post($url, $orderData);

        if ($response->successful()) {
            return $response->json();
        }

        $errorBody = $response->body();
        $errorData = json_decode($errorBody, true);
        
        // Check if it's a CloudFront/HTML error response
        $isCloudFrontError = strpos($errorBody, 'CloudFront') !== false || strpos($errorBody, '<HTML>') !== false;
        
        $outgoingIp = $this->getOutgoingServerIp();
        
        // Try to extract IP from error message (Binance Pay reports the IP they see)
        $binanceReportedIp = null;
        if (isset($errorData['errorMessage'])) {
            // Try to extract IP from error message like "Your server IP (27.147.166.40) needs to be whitelisted"
            if (preg_match('/\((\d+\.\d+\.\d+\.\d+)\)/', $errorData['errorMessage'], $matches)) {
                $binanceReportedIp = $matches[1];
            }
        }
        // Also check the raw error body
        if (!$binanceReportedIp && preg_match('/\((\d+\.\d+\.\d+\.\d+)\)/', $errorBody, $matches)) {
            $binanceReportedIp = $matches[1];
        }
        
        // Use Binance's reported IP if available, otherwise use detected IP
        $ipToShow = $binanceReportedIp ?: ($outgoingIp ?: 'unknown');
        
        Log::error('Binance Pay API Error', [
            'status' => $response->status(),
            'error_code' => $errorData['code'] ?? null,
            'error_message' => $errorData['errorMessage'] ?? null,
            'is_cloudfront_error' => $isCloudFrontError,
            'url' => $url,
            'full_response' => $isCloudFrontError ? 'HTML response (CloudFront error)' : $errorBody,
            'client_ip' => request()->ip(),
            'detected_outgoing_ip' => $outgoingIp,
            'binance_reported_ip' => $binanceReportedIp,
            'ip_to_whitelist' => $ipToShow,
            'api_key_preview' => substr($this->apiKey, 0, 10) . '...'
        ]);

        // Provide user-friendly error messages
        $errorMessage = 'Failed to create Binance Pay order';
        
        if ($isCloudFrontError) {
            // CloudFront blocking - usually means URL issue, IP blocking, or API endpoint problem
            $errorMessage = 'Request blocked by CloudFront. This usually means: 1) Invalid API endpoint URL, 2) Your server IP is blocked, or 3) API credentials are invalid. Please check your Binance Pay configuration and IP whitelist. Server IP: ' . $ipToShow;
        } elseif (isset($errorData['code'])) {
            switch ($errorData['code']) {
                case '400004':
                    $errorMessage = 'API authentication failed. Your server IP (' . $ipToShow . ') needs to be whitelisted in Binance Pay merchant dashboard. Go to: Settings → API Management → IP Whitelist and add: ' . $ipToShow . '. Note: IP whitelist changes may take a few minutes to take effect.';
                    break;
                case '400001':
                    $errorMessage = 'Invalid request parameters. Please check your order data.';
                    break;
                case '400002':
                    $errorMessage = 'Invalid signature. Please check your API secret key.';
                    break;
                default:
                    $errorMessage = $errorData['errorMessage'] ?? 'Failed to create Binance Pay order';
            }
        } elseif ($response->status() === 403) {
            $errorMessage = 'Access forbidden (403). Please check your API credentials, IP whitelist, and ensure your server IP (' . $ipToShow . ') is whitelisted in Binance Pay merchant dashboard. Go to: Settings → API Management → IP Whitelist and add: ' . $ipToShow . '. Note: IP whitelist changes may take a few minutes to take effect.';
        }

        throw new \Exception($errorMessage);
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

        $url = rtrim($this->baseUrl, '/') . '/binancepay/openapi/v2/order/query';
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'User-Agent' => 'Laravel-BinancePay-Integration/1.0',
            'BinancePay-Timestamp' => $timestamp,
            'BinancePay-Nonce' => $nonce,
            'BinancePay-Certificate-SN' => $this->apiKey,
            'BinancePay-Signature' => $signature,
        ])->timeout(30)->post($url, $data);

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

        $url = rtrim($this->baseUrl, '/') . '/binancepay/openapi/order/close';
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'User-Agent' => 'Laravel-BinancePay-Integration/1.0',
            'BinancePay-Timestamp' => $timestamp,
            'BinancePay-Nonce' => $nonce,
            'BinancePay-Certificate-SN' => $this->apiKey,
            'BinancePay-Signature' => $signature,
        ])->timeout(30)->post($url, $data);

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

    // Extract error code from error message
    private function extractErrorCode($errorMessage)
    {
        if (preg_match('/"code":"(\d+)"/', $errorMessage, $matches)) {
            return $matches[1];
        }
        return null;
    }

    // Return page after payment
    public function return(Request $request)
    {
        $merchantTradeNo = $request->get('merchantTradeNo');
        $prepayId = $request->get('prepayId');
        $status = $request->get('status');
        $orderData = null;
        $processed = false;
        $message = '';

        Log::info('Binance Pay return page accessed', [
            'merchant_trade_no' => $merchantTradeNo,
            'prepay_id' => $prepayId,
            'status' => $status,
            'all_params' => $request->all()
        ]);

        // Try to query order by merchantTradeNo or prepayId
        if ($merchantTradeNo || $prepayId) {
            try {
                $orderStatus = $this->queryOrder($merchantTradeNo, $prepayId);
                
                if (isset($orderStatus['status']) && $orderStatus['status'] === 'SUCCESS') {
                    if (isset($orderStatus['data'])) {
                        $orderData = $orderStatus['data'];
                        $status = $orderData['status'] ?? $status;
                        $actualMerchantTradeNo = $orderData['merchantTradeNo'] ?? $merchantTradeNo;
                        
                        // Auto-process deposit if payment is successful
                        if ($status === 'SUCCESS' && $actualMerchantTradeNo) {
                            try {
                                DB::beginTransaction();
                                
                                // Find deposit by merchant trade no
                                $deposit = Deposit::where('merchant_trade_no', $actualMerchantTradeNo)->first();
                                
                                // If not found and we have prepayId, try finding by prepayId
                                if (!$deposit && $prepayId) {
                                    $deposit = Deposit::where('prepay_id', $prepayId)->first();
                                }
                                
                                if ($deposit) {
                                    // Update deposit with transaction info
                                    if (isset($orderData['transactionId'])) {
                                        $deposit->transaction_id = $orderData['transactionId'];
                                    }
                                    if ($prepayId && !$deposit->prepay_id) {
                                        $deposit->prepay_id = $prepayId;
                                    }
                                    $deposit->response_data = $orderStatus;
                                    $deposit->save();
                                    
                                    if ($deposit->status === 'pending') {
                                        // Process the deposit
                                        $this->processDeposit($deposit);
                                        $processed = true;
                                        $message = 'Payment successful! Funds have been added to your account.';
                                        
                                        Log::info('Deposit auto-processed on return page', [
                                            'deposit_id' => $deposit->id,
                                            'merchant_trade_no' => $actualMerchantTradeNo,
                                            'prepay_id' => $prepayId,
                                            'status' => $status,
                                            'amount' => $deposit->amount
                                        ]);
                                    } elseif ($deposit->status === 'completed') {
                                        $message = 'Payment already processed. Funds are in your account.';
                                        Log::info('Deposit already processed on return page', [
                                            'deposit_id' => $deposit->id,
                                            'merchant_trade_no' => $actualMerchantTradeNo
                                        ]);
                                    } else {
                                        $message = 'Payment status: ' . $deposit->status;
                                        Log::warning('Deposit not in pending status on return page', [
                                            'deposit_id' => $deposit->id,
                                            'status' => $deposit->status,
                                            'merchant_trade_no' => $actualMerchantTradeNo
                                        ]);
                                    }
                                } else {
                                    Log::warning('Deposit not found on return page', [
                                        'merchant_trade_no' => $actualMerchantTradeNo,
                                        'prepay_id' => $prepayId,
                                        'order_data' => $orderData
                                    ]);
                                    $message = 'Deposit record not found. Please contact support.';
                                }
                                
                                DB::commit();
                            } catch (\Exception $e) {
                                DB::rollBack();
                                Log::error('Failed to auto-process deposit on return', [
                                    'merchant_trade_no' => $actualMerchantTradeNo,
                                    'prepay_id' => $prepayId,
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString()
                                ]);
                                $message = 'Error processing payment: ' . $e->getMessage();
                            }
                        } else {
                            $message = 'Payment status: ' . ($status ?? 'Unknown');
                            Log::info('Payment not successful on return page', [
                                'status' => $status,
                                'merchant_trade_no' => $actualMerchantTradeNo
                            ]);
                        }
                    }
                } else {
                    Log::warning('Order query failed on return page', [
                        'merchant_trade_no' => $merchantTradeNo,
                        'prepay_id' => $prepayId,
                        'response' => $orderStatus
                    ]);
                    $message = 'Failed to verify payment status.';
                }
            } catch (\Exception $e) {
                Log::error('Failed to query order on return', [
                    'merchant_trade_no' => $merchantTradeNo,
                    'prepay_id' => $prepayId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $message = 'Error verifying payment: ' . $e->getMessage();
            }
        } else {
            // Fallback: If user is logged in, check their most recent pending deposit
            if (Auth::check()) {
                try {
                    $recentDeposit = Deposit::where('user_id', Auth::id())
                        ->where('payment_method', 'binance_pay')
                        ->where('status', 'pending')
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    if ($recentDeposit && $recentDeposit->merchant_trade_no) {
                        // Try to query this deposit
                        try {
                            $orderStatus = $this->queryOrder($recentDeposit->merchant_trade_no, $recentDeposit->prepay_id);
                            
                            if (isset($orderStatus['status']) && $orderStatus['status'] === 'SUCCESS' && isset($orderStatus['data'])) {
                                $orderData = $orderStatus['data'];
                                $status = $orderData['status'] ?? 'UNKNOWN';
                                
                                if ($status === 'SUCCESS') {
                                    try {
                                        DB::beginTransaction();
                                        
                                        $deposit = Deposit::where('id', $recentDeposit->id)->lockForUpdate()->first();
                                        
                                        if ($deposit && $deposit->status === 'pending') {
                                            if (isset($orderData['transactionId'])) {
                                                $deposit->transaction_id = $orderData['transactionId'];
                                            }
                                            $deposit->response_data = $orderStatus;
                                            $deposit->save();
                                            
                                            $this->processDeposit($deposit);
                                            $processed = true;
                                            $message = 'Payment successful! Funds have been added to your account.';
                                            
                                            Log::info('Deposit auto-processed via fallback on return page', [
                                                'deposit_id' => $deposit->id,
                                                'merchant_trade_no' => $deposit->merchant_trade_no
                                            ]);
                                        }
                                        
                                        DB::commit();
                                    } catch (\Exception $e) {
                                        DB::rollBack();
                                        Log::error('Failed to auto-process deposit via fallback', [
                                            'deposit_id' => $recentDeposit->id,
                                            'error' => $e->getMessage()
                                        ]);
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            Log::error('Failed to query order via fallback', [
                                'deposit_id' => $recentDeposit->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Fallback deposit check failed', [
                        'user_id' => Auth::id(),
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            if (!$processed) {
                Log::warning('No merchantTradeNo or prepayId in return URL', [
                    'all_params' => $request->all(),
                    'user_authenticated' => Auth::check()
                ]);
                if (!$message) {
                    $message = 'Missing payment information. Please contact support or use manual verification.';
                }
            }
        }

        return view('frontend.binance.return', [
            'status' => $status,
            'merchantTradeNo' => $merchantTradeNo ?? ($orderData['merchantTradeNo'] ?? null),
            'prepayId' => $prepayId,
            'processed' => $processed,
            'message' => $message
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

            // Log webhook attempt for debugging
            Log::info('Binance Pay webhook received', [
                'has_signature' => !empty($signature),
                'has_timestamp' => !empty($timestamp),
                'has_nonce' => !empty($nonce),
                'payload_length' => strlen($payload)
            ]);

            // Verify webhook signature
            if (!$signature || !$timestamp || !$nonce) {
                Log::warning('Binance Pay webhook missing required headers', [
                    'headers' => $request->headers->all()
                ]);
                return response()->json(['error' => 'Missing required headers'], 400);
            }

            if (!$this->verifyWebhook($payload, $signature, $timestamp, $nonce)) {
                Log::warning('Binance Pay webhook signature verification failed', [
                    'timestamp' => $timestamp,
                    'nonce' => $nonce,
                    'payload_preview' => substr($payload, 0, 200)
                ]);
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            $data = json_decode($payload, true);

            if (!$data) {
                Log::error('Binance Pay webhook invalid JSON payload', [
                    'payload' => $payload
                ]);
                return response()->json(['error' => 'Invalid JSON payload'], 400);
            }

            Log::info('Binance Pay webhook processed', [
                'event_type' => $data['bizType'] ?? 'unknown',
                'merchant_trade_no' => $data['data']['merchantTradeNo'] ?? null,
                'status' => $data['data']['status'] ?? null
            ]);

            // Handle payment status update - check for PAY event type
            if (isset($data['bizType']) && $data['bizType'] === 'PAY') {
                $this->autoProcessPayment($data);
            } else {
                Log::info('Binance Pay webhook event type not handled', [
                    'bizType' => $data['bizType'] ?? 'unknown',
                    'data' => $data
                ]);
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
                    'merchant_trade_no' => $merchantTradeNo,
                    'webhook_data' => $webhookData
                ]);
                DB::rollBack();
                return;
            }

            // Update deposit with webhook data (always update transaction info)
            if ($transactionId) {
                $deposit->transaction_id = $transactionId;
            }
            $deposit->response_data = $webhookData;
            $deposit->save();

            // Process based on status
            if ($status === 'SUCCESS') {
                if ($deposit->status === 'pending') {
                    $this->processDeposit($deposit);
                } elseif ($deposit->status === 'completed') {
                    Log::info('Deposit already completed, skipping processing', [
                        'deposit_id' => $deposit->id,
                        'merchant_trade_no' => $merchantTradeNo
                    ]);
                } else {
                    Log::warning('Deposit status is not pending, cannot process', [
                        'deposit_id' => $deposit->id,
                        'current_status' => $deposit->status,
                        'merchant_trade_no' => $merchantTradeNo
                    ]);
                }
            } elseif ($status === 'EXPIRED' || $status === 'CANCELED') {
                if ($deposit->status === 'pending') {
                    $deposit->status = 'expired';
                    $deposit->save();
                }
            } elseif ($status === 'ERROR') {
                if ($deposit->status === 'pending') {
                    $deposit->status = 'failed';
                    $deposit->save();
                }
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
                'trace' => $e->getTraceAsString(),
                'webhook_data' => $webhookData
            ]);
            throw $e;
        }
    }

    private function processDeposit(Deposit $deposit)
    {
        // Reload deposit to ensure we have latest status (prevent race conditions)
        $deposit->refresh();
        
        // Double-check deposit is still pending
        if ($deposit->status !== 'pending') {
            Log::warning('Attempted to process non-pending deposit', [
                'deposit_id' => $deposit->id,
                'current_status' => $deposit->status
            ]);
            throw new \Exception('Deposit is not in pending status. Current status: ' . $deposit->status);
        }

        $user = $deposit->user;
        
        if (!$user) {
            Log::error('User not found for deposit', [
                'deposit_id' => $deposit->id,
                'user_id' => $deposit->user_id
            ]);
            throw new \Exception('User not found for deposit');
        }

        // Validate amount
        if ($deposit->amount <= 0) {
            Log::error('Invalid deposit amount', [
                'deposit_id' => $deposit->id,
                'amount' => $deposit->amount
            ]);
            throw new \Exception('Invalid deposit amount: ' . $deposit->amount);
        }

        // Get or create wallet with lock to prevent race conditions
        $wallet = Wallet::lockForUpdate()
            ->firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0, 'currency' => $deposit->currency ?? 'USDT', 'status' => 'active']
            );

        $balanceBefore = (float) $wallet->balance;
        $newBalance = $balanceBefore + (float) $deposit->amount;
        
        // Update wallet balance
        $wallet->balance = $newBalance;
        $wallet->save();

        // Update deposit status
        $deposit->status = 'completed';
        $deposit->completed_at = now();
        $deposit->save();

        // Create wallet transaction
        try {
            WalletTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'amount' => $deposit->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $newBalance,
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
        } catch (\Exception $e) {
            // Log error but don't fail the deposit processing
            Log::error('Failed to create wallet transaction', [
                'deposit_id' => $deposit->id,
                'error' => $e->getMessage()
            ]);
        }

        Log::info('Deposit processed successfully', [
            'deposit_id' => $deposit->id,
            'user_id' => $user->id,
            'amount' => $deposit->amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $newBalance,
            'currency' => $deposit->currency
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

    /**
     * Diagnostic method to check server IP and Binance Pay configuration
     * This can be called to verify the outgoing IP address
     */
    public function diagnostic(Request $request)
    {
        try {
            $outgoingIp = $this->getOutgoingServerIp();
            $clientIp = request()->ip();
            
            // Try to make a test request to Binance Pay to see what IP they see
            $testResult = null;
            try {
                // Make a minimal test request (query with invalid merchantTradeNo to trigger error)
                // This will show us what IP Binance Pay sees
                $testResponse = $this->queryOrder('TEST_DIAGNOSTIC_' . time());
            } catch (\Exception $e) {
                // Extract IP from error message if available
                $errorMsg = $e->getMessage();
                if (preg_match('/\((\d+\.\d+\.\d+\.\d+)\)/', $errorMsg, $matches)) {
                    $testResult = [
                        'binance_reported_ip' => $matches[1],
                        'error' => $errorMsg
                    ];
                } else {
                    $testResult = [
                        'error' => $errorMsg
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'diagnostics' => [
                    'client_ip' => $clientIp,
                    'detected_outgoing_ip' => $outgoingIp,
                    'binance_test_result' => $testResult,
                    'api_key_set' => !empty($this->apiKey),
                    'secret_key_set' => !empty($this->secretKey),
                    'base_url' => $this->baseUrl,
                    'server_addr' => $_SERVER['SERVER_ADDR'] ?? 'not_set',
                    'recommendation' => $outgoingIp ? 
                        "Ensure IP {$outgoingIp} is whitelisted in Binance Pay dashboard. If Binance reports a different IP, use that one instead." :
                        "Could not detect outgoing IP. Check your server configuration or set SERVER_OUTGOING_IP in .env file."
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
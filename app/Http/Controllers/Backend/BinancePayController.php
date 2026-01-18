<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Deposit;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\GlobalSetting;

class BinancePayController extends Controller
{
    private $apiKey;
    private $secretKey;
    private $baseUrl;
    private $isLocalEnv;

    public function __construct()
    {
        // Detect environment
        $this->isLocalEnv = app()->environment('local', 'development');
        
        // Use database settings first, then config, then env
        $this->apiKey = GlobalSetting::getValue('binance_api_key') 
            ?: config('services.binance.api_key') 
            ?: env('BINANCE_API_KEY');
        
        $this->secretKey = GlobalSetting::getValue('binance_secret_key') 
            ?: config('services.binance.secret_key') 
            ?: env('BINANCE_SECRET_KEY');
        
        // Normalize base URL - remove trailing slashes
        $baseUrl = GlobalSetting::getValue('binance_base_url') 
            ?: config('services.binance.base_url') 
            ?: env('BINANCE_BASE_URL', 'https://bpay.binanceapi.com');
        $this->baseUrl = rtrim($baseUrl, '/');
        
        // Validate configuration
        if (empty($this->apiKey) || empty($this->secretKey)) {
            Log::error('Binance Pay Configuration Missing', [
                'api_key_set' => !empty($this->apiKey),
                'secret_key_set' => !empty($this->secretKey),
                'environment' => app()->environment()
            ]);
        }
        
        // Log configuration for debugging (only in local)
        if ($this->isLocalEnv) {
            $outgoingIp = $this->getOutgoingServerIp();
            Log::info('Binance Pay Configuration Loaded', [
                'environment' => app()->environment(),
                'base_url' => $this->baseUrl,
                'api_key_set' => !empty($this->apiKey),
                'secret_key_set' => !empty($this->secretKey),
                'client_ip' => request()->ip(),
                'outgoing_server_ip' => $outgoingIp
            ]);
        }
    }

    /**
     * Get the actual outgoing server IP address
     */
    private function getOutgoingServerIp()
    {
        // Check environment variable first (highest priority)
        $envIp = env('SERVER_OUTGOING_IP');
        if ($envIp && filter_var($envIp, FILTER_VALIDATE_IP)) {
            Log::debug('Using IP from SERVER_OUTGOING_IP env variable', ['ip' => $envIp]);
            return $envIp;
        }

        // In local environment, return warning message instead of trying to detect
        if ($this->isLocalEnv) {
            Log::info('Local environment detected - set SERVER_OUTGOING_IP in .env for testing');
            return 'local-development';
        }

        // Check cache (only in production)
        $cacheKey = 'binance_outgoing_ip_v2';
        $cachedIp = Cache::get($cacheKey);
        if ($cachedIp && $cachedIp !== 'local-development') {
            return $cachedIp;
        }

        try {
            // Try multiple detection methods
            $detectedIp = null;

            // Method 1: Check $_SERVER['SERVER_ADDR']
            if (isset($_SERVER['SERVER_ADDR'])) {
                $serverAddr = $_SERVER['SERVER_ADDR'];
                if (filter_var($serverAddr, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    $detectedIp = $serverAddr;
                    Log::info('IP detected from SERVER_ADDR', ['ip' => $detectedIp]);
                }
            }

            // Method 2: Query external service (fallback)
            if (!$detectedIp) {
                $services = [
                    'https://api.ipify.org?format=json',
                    'https://ipinfo.io/json',
                    'https://api.my-ip.io/ip.json'
                ];

                foreach ($services as $service) {
                    try {
                        $response = Http::timeout(5)->retry(2, 100)->get($service);
                        if ($response->successful()) {
                            $data = $response->json();
                            $ip = $data['ip'] ?? $data['address'] ?? null;
                            
                            if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
                                $detectedIp = $ip;
                                Log::info('IP detected from external service', [
                                    'service' => $service,
                                    'ip' => $detectedIp
                                ]);
                                break;
                            }
                        }
                    } catch (\Exception $e) {
                        Log::debug('External IP service failed', [
                            'service' => $service,
                            'error' => $e->getMessage()
                        ]);
                        continue;
                    }
                }
            }

            // Cache the result if found
            if ($detectedIp) {
                Cache::put($cacheKey, $detectedIp, now()->addHours(24));
                return $detectedIp;
            }

        } catch (\Exception $e) {
            Log::error('Failed to detect outgoing server IP', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        Log::warning('Could not detect outgoing server IP - please set SERVER_OUTGOING_IP in .env');
        return null;
    }

    /**
     * Generate signature for Binance Pay API
     * Format: UPPERCASE(HMAC_SHA512(payload, secret_key))
     * Payload format: timestamp + "\n" + nonce + "\n" + body + "\n"
     */
    private function generateSignature($timestamp, $nonce, $body)
    {
        // Ensure body is a string (JSON encoded if necessary)
        if (is_array($body)) {
            $body = json_encode($body);
        }
        
        $payload = $timestamp . "\n" . $nonce . "\n" . $body . "\n";
        $signature = strtoupper(hash_hmac('sha512', $payload, $this->secretKey));
        
        if ($this->isLocalEnv) {
            Log::debug('Binance Pay Signature Generated', [
                'timestamp' => $timestamp,
                'nonce' => $nonce,
                'body_length' => strlen($body),
                'payload_length' => strlen($payload),
                'signature' => substr($signature, 0, 20) . '...' // Only log first 20 chars
            ]);
        }
        
        return $signature;
    }

    /**
     * Generate random nonce
     */
    private function generateNonce($length = 32)
    {
        $nonce = bin2hex(random_bytes($length / 2));
        
        if ($this->isLocalEnv) {
            Log::debug('Nonce generated', [
                'length' => strlen($nonce),
                'nonce' => substr($nonce, 0, 10) . '...'
            ]);
        }
        
        return $nonce;
    }

    /**
     * Create payment order - Frontend method
     */
    public function createPayment(Request $request)
    {
        // Validate API credentials first
        if (empty($this->apiKey) || empty($this->secretKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Binance Pay is not configured. Please contact administrator.'
            ], 503);
        }

        $request->validate([
            'amount' => 'required|numeric|min:10|max:10000',
            'currency' => 'nullable|string|in:USDT,USD,EUR,GBP',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            
            if (!$user) {
                throw new \Exception('User not authenticated');
            }

            $amount = (float) $request->amount;
            $currency = $request->currency ?? 'USDT';
            
            // Generate unique merchant trade number
            $merchantTradeNo = 'DEP_' . date('YmdHis') . '_' . $user->id . '_' . strtoupper(substr(md5(uniqid()), 0, 6));

            Log::info('Creating Binance Pay deposit', [
                'user_id' => $user->id,
                'amount' => $amount,
                'currency' => $currency,
                'merchant_trade_no' => $merchantTradeNo
            ]);

            // Create deposit record
            $deposit = Deposit::create([
                'user_id' => $user->id,
                'merchant_trade_no' => $merchantTradeNo,
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'pending',
                'payment_method' => 'binance_pay',
            ]);

            // Prepare order data according to Binance Pay API v2 specifications
            $orderData = [
                'env' => [
                    'terminalType' => 'WEB'
                ],
                'merchantTradeNo' => $merchantTradeNo,
                'orderAmount' => $amount,
                'currency' => $currency,
                'goods' => [
                    'goodsType' => '01', // Virtual goods
                    'goodsCategory' => 'D000', // Digital goods
                    'referenceGoodsId' => 'DEPOSIT_' . $deposit->id,
                    'goodsName' => 'Wallet Deposit',
                    'goodsDetail' => 'Add funds to wallet balance'
                ],
                'buyer' => [
                    'referenceBuyerId' => (string) $user->id,
                    'buyerName' => [
                        'firstName' => $user->name ?? 'User',
                        'lastName' => $user->last_name ?? ''
                    ]
                ],
                'returnUrl' => route('binance.return'),
                'cancelUrl' => route('binance.cancel'),
            ];

            // Add buyer email if available
            if ($user->email) {
                $orderData['buyer']['buyerEmail'] = $user->email;
            }

            // Create order with Binance Pay
            $response = $this->createOrder($orderData);

            if (isset($response['status']) && $response['status'] === 'SUCCESS') {
                // Update deposit with response data
                $deposit->prepay_id = $response['data']['prepayId'] ?? null;
                $deposit->response_data = $response;
                $deposit->save();

                DB::commit();

                Log::info('Binance Pay deposit created successfully', [
                    'deposit_id' => $deposit->id,
                    'prepay_id' => $deposit->prepay_id,
                    'merchant_trade_no' => $merchantTradeNo
                ]);

                return response()->json([
                    'success' => true,
                    'prepayId' => $response['data']['prepayId'] ?? null,
                    'checkoutUrl' => $response['data']['checkoutUrl'] ?? null,
                    'universalUrl' => $response['data']['universalUrl'] ?? null,
                    'qrcodeLink' => $response['data']['qrcodeLink'] ?? null,
                    'qrContent' => $response['data']['qrContent'] ?? null,
                    'deposit_id' => $deposit->id,
                    'merchant_trade_no' => $merchantTradeNo
                ]);
            }

            DB::rollBack();
            
            $errorMsg = $response['errorMessage'] ?? 'Failed to create payment order';
            Log::error('Binance Pay returned non-success status', [
                'response' => $response
            ]);
            
            throw new \Exception($errorMsg);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $outgoingIp = $this->getOutgoingServerIp();
            
            Log::error('Binance Pay create payment error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'client_ip' => request()->ip(),
                'outgoing_server_ip' => $outgoingIp
            ]);

            // Extract user-friendly error message
            $errorMessage = $this->formatErrorMessage($e->getMessage(), $outgoingIp);

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error_code' => $this->extractErrorCode($e->getMessage()),
                'debug_info' => $this->isLocalEnv ? [
                    'outgoing_ip' => $outgoingIp,
                    'base_url' => $this->baseUrl
                ] : null
            ], 500);
        }
    }

    /**
     * Create order with Binance Pay API
     */
    private function createOrder($orderData)
    {
        // Validate required credentials
        if (empty($this->apiKey) || empty($this->secretKey)) {
            throw new \Exception('Binance Pay API credentials not configured. Please set BINANCE_API_KEY and BINANCE_SECRET_KEY in your environment or admin settings.');
        }

        $timestamp = floor(microtime(true) * 1000);
        $nonce = $this->generateNonce();
        $body = json_encode($orderData, JSON_UNESCAPED_SLASHES);

        $signature = $this->generateSignature($timestamp, $nonce, $body);
        $url = $this->baseUrl . '/binancepay/openapi/v2/order';
        
        $logData = [
            'url' => $url,
            'timestamp' => $timestamp,
            'merchant_trade_no' => $orderData['merchantTradeNo'] ?? null,
            'amount' => $orderData['orderAmount'] ?? null,
            'currency' => $orderData['currency'] ?? null,
        ];
        
        if ($this->isLocalEnv) {
            $logData['nonce'] = substr($nonce, 0, 10) . '...';
            $logData['body_preview'] = substr($body, 0, 200) . '...';
        }
        
        Log::info('Binance Pay Create Order Request', $logData);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'User-Agent' => 'Laravel-BinancePay-Integration/1.0',
                'BinancePay-Timestamp' => (string) $timestamp,
                'BinancePay-Nonce' => $nonce,
                'BinancePay-Certificate-SN' => $this->apiKey,
                'BinancePay-Signature' => $signature,
            ])
            ->timeout(30)
            ->retry(2, 100) // Retry twice with 100ms delay
            ->post($url, $orderData);

            if ($response->successful()) {
                $responseData = $response->json();
                
                Log::info('Binance Pay Order Created Successfully', [
                    'merchant_trade_no' => $orderData['merchantTradeNo'] ?? null,
                    'status' => $responseData['status'] ?? null,
                    'prepay_id' => $responseData['data']['prepayId'] ?? null,
                ]);
                
                return $responseData;
            }

            $this->handleApiError($response);
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Binance Pay Connection Error', [
                'url' => $url,
                'error' => $e->getMessage(),
                'outgoing_ip' => $this->getOutgoingServerIp()
            ]);
            throw new \Exception('Unable to connect to Binance Pay API. Please check your internet connection and firewall settings.');
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Binance Pay Request Error', [
                'url' => $url,
                'error' => $e->getMessage(),
                'status_code' => $e->response ? $e->response->status() : null
            ]);
            throw $e;
        }
    }

    /**
     * Query order status
     */
    public function queryOrder($merchantTradeNo = null, $prepayId = null)
    {
        if (!$merchantTradeNo && !$prepayId) {
            throw new \Exception('Either merchantTradeNo or prepayId is required');
        }

        // Validate credentials
        if (empty($this->apiKey) || empty($this->secretKey)) {
            throw new \Exception('Binance Pay API credentials not configured.');
        }

        $timestamp = floor(microtime(true) * 1000);
        $nonce = $this->generateNonce();

        $data = array_filter([
            'merchantTradeNo' => $merchantTradeNo,
            'prepayId' => $prepayId
        ]);

        $body = json_encode($data, JSON_UNESCAPED_SLASHES);
        $signature = $this->generateSignature($timestamp, $nonce, $body);
        $url = $this->baseUrl . '/binancepay/openapi/v2/order/query';
        
        Log::info('Binance Pay Query Order', [
            'merchant_trade_no' => $merchantTradeNo,
            'prepay_id' => $prepayId,
        ]);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'User-Agent' => 'Laravel-BinancePay-Integration/1.0',
                'BinancePay-Timestamp' => (string) $timestamp,
                'BinancePay-Nonce' => $nonce,
                'BinancePay-Certificate-SN' => $this->apiKey,
                'BinancePay-Signature' => $signature,
            ])
            ->timeout(30)
            ->retry(2, 100)
            ->post($url, $data);

            if ($response->successful()) {
                $responseData = $response->json();
                
                Log::info('Binance Pay Query Successful', [
                    'status' => $responseData['status'] ?? null,
                    'order_status' => $responseData['data']['status'] ?? null,
                ]);
                
                return $responseData;
            }

            $this->handleApiError($response);
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Binance Pay Query Connection Error', [
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Unable to connect to Binance Pay API for order query.');
        }
    }

    /**
     * Close an order
     */
    public function closeOrder($merchantTradeNo)
    {
        // Validate credentials
        if (empty($this->apiKey) || empty($this->secretKey)) {
            throw new \Exception('Binance Pay API credentials not configured.');
        }

        $timestamp = floor(microtime(true) * 1000);
        $nonce = $this->generateNonce();

        $data = ['merchantTradeNo' => $merchantTradeNo];
        $body = json_encode($data, JSON_UNESCAPED_SLASHES);
        $signature = $this->generateSignature($timestamp, $nonce, $body);
        $url = $this->baseUrl . '/binancepay/openapi/order/close';
        
        Log::info('Binance Pay Close Order', [
            'merchant_trade_no' => $merchantTradeNo
        ]);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'User-Agent' => 'Laravel-BinancePay-Integration/1.0',
                'BinancePay-Timestamp' => (string) $timestamp,
                'BinancePay-Nonce' => $nonce,
                'BinancePay-Certificate-SN' => $this->apiKey,
                'BinancePay-Signature' => $signature,
            ])
            ->timeout(30)
            ->retry(2, 100)
            ->post($url, $data);

            if ($response->successful()) {
                $responseData = $response->json();
                
                Log::info('Binance Pay Order Closed', [
                    'merchant_trade_no' => $merchantTradeNo,
                    'status' => $responseData['status'] ?? null
                ]);
                
                return $responseData;
            }

            $this->handleApiError($response);
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Binance Pay Close Order Connection Error', [
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Unable to connect to Binance Pay API to close order.');
        }
    }

    /**
     * Handle API errors
     */
    private function handleApiError($response)
    {
        $errorBody = $response->body();
        $errorData = json_decode($errorBody, true);
        $isCloudFrontError = strpos($errorBody, 'CloudFront') !== false || strpos($errorBody, '<HTML>') !== false;
        
        // Extract IP from error message
        $binanceReportedIp = null;
        if (preg_match('/\((\d+\.\d+\.\d+\.\d+)\)/', $errorBody, $matches)) {
            $binanceReportedIp = $matches[1];
        }
        
        $outgoingIp = $this->getOutgoingServerIp();
        $ipToShow = $binanceReportedIp ?: ($outgoingIp ?: 'unknown');
        
        Log::error('Binance Pay API Error', [
            'status' => $response->status(),
            'error_code' => $errorData['code'] ?? null,
            'error_message' => $errorData['errorMessage'] ?? null,
            'is_cloudfront_error' => $isCloudFrontError,
            'full_response' => $isCloudFrontError ? 'HTML response (CloudFront)' : $errorBody,
            'detected_outgoing_ip' => $outgoingIp,
            'binance_reported_ip' => $binanceReportedIp,
            'ip_to_whitelist' => $ipToShow,
        ]);

        $errorMessage = $this->formatErrorMessage($errorBody, $ipToShow, $errorData);
        throw new \Exception($errorMessage);
    }

    /**
     * Format error message for user
     */
    private function formatErrorMessage($errorBody, $ipToShow = null, $errorData = null)
    {
        if (!$errorData) {
            $errorData = json_decode($errorBody, true);
        }

        $isCloudFrontError = strpos($errorBody, 'CloudFront') !== false || strpos($errorBody, '<HTML>') !== false;
        $ipToShow = $ipToShow ?: 'unknown';

        if ($isCloudFrontError) {
            return "Request blocked by CloudFront. Please check: 1) API endpoint URL is correct, 2) Server IP ({$ipToShow}) is whitelisted in Binance Pay dashboard, 3) API credentials are valid.";
        }

        if (isset($errorData['code'])) {
            switch ($errorData['code']) {
                case '400004':
                    return "API authentication failed. Whitelist server IP ({$ipToShow}) in Binance Pay: Settings → API Management → IP Whitelist. Changes may take a few minutes.";
                case '400001':
                    return 'Invalid request parameters. Please check your order data.';
                case '400002':
                    return 'Invalid signature. Please verify your API secret key.';
                case '429002':
                    return 'Rate limit exceeded. Please try again in a few moments.';
                default:
                    return $errorData['errorMessage'] ?? 'Failed to process Binance Pay request';
            }
        }

        if (strpos($errorBody, '403') !== false || strpos($errorBody, 'Forbidden') !== false) {
            return "Access forbidden. Whitelist server IP ({$ipToShow}) in Binance Pay: Settings → API Management → IP Whitelist.";
        }

        return 'Failed to process Binance Pay request. Please contact support.';
    }

    /**
     * Extract error code from message
     */
    private function extractErrorCode($errorMessage)
    {
        if (preg_match('/"code":"?(\d+)"?/', $errorMessage, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Verify webhook signature
     */
    private function verifyWebhook($payload, $signature, $timestamp, $nonce)
    {
        $computedSignature = $this->generateSignature($timestamp, $nonce, $payload);
        return hash_equals($computedSignature, $signature);
    }

    /**
     * Handle webhook from Binance Pay
     */
    public function webhook(Request $request)
    {
        try {
            $payload = $request->getContent();
            $signature = $request->header('BinancePay-Signature');
            $timestamp = $request->header('BinancePay-Timestamp');
            $nonce = $request->header('BinancePay-Nonce');
            $certificateSN = $request->header('BinancePay-Certificate-SN');

            Log::info('Binance Pay webhook received', [
                'has_signature' => !empty($signature),
                'has_timestamp' => !empty($timestamp),
                'has_nonce' => !empty($nonce),
                'has_certificate' => !empty($certificateSN),
                'payload_length' => strlen($payload),
                'ip' => $request->ip()
            ]);

            // Verify required headers
            if (!$signature || !$timestamp || !$nonce) {
                Log::warning('Binance Pay webhook missing required headers', [
                    'signature_present' => !empty($signature),
                    'timestamp_present' => !empty($timestamp),
                    'nonce_present' => !empty($nonce),
                    'ip' => $request->ip()
                ]);
                return response()->json(['error' => 'Missing required headers'], 400);
            }

            // Timestamp validation (prevent replay attacks)
            $currentTimestamp = floor(microtime(true) * 1000);
            $requestTimestamp = (int) $timestamp;
            $timeDiff = abs($currentTimestamp - $requestTimestamp);
            
            // Allow 5 minutes time difference
            if ($timeDiff > 300000) {
                Log::warning('Binance Pay webhook timestamp expired', [
                    'current_timestamp' => $currentTimestamp,
                    'request_timestamp' => $requestTimestamp,
                    'diff_ms' => $timeDiff,
                    'ip' => $request->ip()
                ]);
                return response()->json(['error' => 'Request timestamp expired'], 400);
            }

            // Verify signature
            if (!$this->verifyWebhook($payload, $signature, $timestamp, $nonce)) {
                Log::warning('Binance Pay webhook signature verification failed', [
                    'ip' => $request->ip(),
                    'timestamp' => $timestamp,
                    'nonce' => substr($nonce, 0, 10) . '...'
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Parse JSON payload
            $data = json_decode($payload, true);

            if (!$data || json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Binance Pay webhook invalid JSON', [
                    'json_error' => json_last_error_msg(),
                    'payload_preview' => substr($payload, 0, 100)
                ]);
                return response()->json(['error' => 'Invalid JSON payload'], 400);
            }

            Log::info('Binance Pay webhook data', [
                'event_type' => $data['bizType'] ?? 'unknown',
                'merchant_trade_no' => $data['data']['merchantTradeNo'] ?? null,
                'status' => $data['data']['status'] ?? null,
                'biz_id' => $data['bizId'] ?? null
            ]);

            // Process different webhook types
            $bizType = $data['bizType'] ?? null;
            
            switch ($bizType) {
                case 'PAY':
                    $this->processWebhookPayment($data);
                    break;
                    
                case 'REFUND':
                    Log::info('Binance Pay refund webhook received', [
                        'merchant_trade_no' => $data['data']['merchantTradeNo'] ?? null
                    ]);
                    // Add refund processing if needed
                    break;
                    
                default:
                    Log::warning('Unknown Binance Pay webhook type', [
                        'biz_type' => $bizType
                    ]);
            }

            return response()->json([
                'returnCode' => 'SUCCESS',
                'returnMessage' => null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Binance Pay webhook error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'returnCode' => 'FAIL',
                'returnMessage' => 'Webhook processing failed'
            ], 500);
        }
    }

    /**
     * Process payment from webhook
     */
    private function processWebhookPayment($webhookData)
    {
        try {
            DB::beginTransaction();

            $merchantTradeNo = $webhookData['data']['merchantTradeNo'] ?? null;
            $status = $webhookData['data']['status'] ?? null;
            $transactionId = $webhookData['data']['transactionId'] ?? null;

            if (!$merchantTradeNo) {
                throw new \Exception('Merchant trade number missing in webhook');
            }

            $deposit = Deposit::where('merchant_trade_no', $merchantTradeNo)
                ->lockForUpdate()
                ->first();

            if (!$deposit) {
                Log::warning('Deposit not found for webhook', [
                    'merchant_trade_no' => $merchantTradeNo
                ]);
                DB::rollBack();
                return;
            }

            // Update transaction info
            if ($transactionId) {
                $deposit->transaction_id = $transactionId;
            }
            $deposit->response_data = $webhookData;
            $deposit->save();

            // Process based on status
            switch ($status) {
                case 'SUCCESS':
                    if ($deposit->status === 'pending') {
                        $this->processDeposit($deposit);
                        Log::info('Deposit processed via webhook', [
                            'deposit_id' => $deposit->id,
                            'merchant_trade_no' => $merchantTradeNo
                        ]);
                    }
                    break;
                
                case 'EXPIRED':
                case 'CANCELED':
                    if ($deposit->status === 'pending') {
                        $deposit->status = 'expired';
                        $deposit->save();
                    }
                    break;
                
                case 'ERROR':
                    if ($deposit->status === 'pending') {
                        $deposit->status = 'failed';
                        $deposit->save();
                    }
                    break;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Webhook payment processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Return page after payment
     */
    public function return(Request $request)
    {
        $merchantTradeNo = $request->get('merchantTradeNo');
        $prepayId = $request->get('prepayId');
        $status = $request->get('status');
        $processed = false;
        $message = '';

        Log::info('Binance Pay return page accessed', [
            'merchant_trade_no' => $merchantTradeNo,
            'prepay_id' => $prepayId,
            'status' => $status
        ]);

        // Query and process payment
        if ($merchantTradeNo || $prepayId) {
            try {
                $orderStatus = $this->queryOrder($merchantTradeNo, $prepayId);
                
                if (isset($orderStatus['status']) && $orderStatus['status'] === 'SUCCESS') {
                    $orderData = $orderStatus['data'] ?? [];
                    $status = $orderData['status'] ?? $status;
                    $actualMerchantTradeNo = $orderData['merchantTradeNo'] ?? $merchantTradeNo;
                    
                    if ($status === 'SUCCESS' && $actualMerchantTradeNo) {
                        $processed = $this->processReturnPayment($actualMerchantTradeNo, $prepayId, $orderStatus);
                        $message = $processed ? 'Payment successful! Funds added to your account.' : 'Payment already processed.';
                    } else {
                        $message = 'Payment status: ' . ($status ?? 'Unknown');
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to process return payment', [
                    'error' => $e->getMessage()
                ]);
                $message = 'Error verifying payment. Please use manual verification.';
            }
        } else {
            // Fallback: check user's recent pending deposit
            $message = $this->processFallbackReturn();
        }

        return view('frontend.binance.return', [
            'status' => $status,
            'merchantTradeNo' => $merchantTradeNo,
            'prepayId' => $prepayId,
            'processed' => $processed,
            'message' => $message
        ]);
    }

    /**
     * Process payment on return page
     */
    private function processReturnPayment($merchantTradeNo, $prepayId, $orderStatus)
    {
        try {
            DB::beginTransaction();
            
            $deposit = Deposit::where('merchant_trade_no', $merchantTradeNo)
                ->lockForUpdate()
                ->first();
            
            if (!$deposit && $prepayId) {
                $deposit = Deposit::where('prepay_id', $prepayId)
                    ->lockForUpdate()
                    ->first();
            }
            
            if (!$deposit) {
                Log::warning('Deposit not found on return', [
                    'merchant_trade_no' => $merchantTradeNo
                ]);
                DB::rollBack();
                return false;
            }

            // Update transaction info
            $orderData = $orderStatus['data'] ?? [];
            if (isset($orderData['transactionId'])) {
                $deposit->transaction_id = $orderData['transactionId'];
            }
            if ($prepayId && !$deposit->prepay_id) {
                $deposit->prepay_id = $prepayId;
            }
            $deposit->response_data = $orderStatus;
            $deposit->save();
            
            if ($deposit->status === 'pending') {
                $this->processDeposit($deposit);
                DB::commit();
                return true;
            }
            
            DB::commit();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process return payment', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Fallback processing for return page
     */
    private function processFallbackReturn()
    {
        if (!Auth::check()) {
            return 'Missing payment information. Please contact support.';
        }

        try {
            $recentDeposit = Deposit::where('user_id', Auth::id())
                ->where('payment_method', 'binance_pay')
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($recentDeposit) {
                $this->processReturnPayment(
                    $recentDeposit->merchant_trade_no,
                    $recentDeposit->prepay_id,
                    []
                );
            }
        } catch (\Exception $e) {
            Log::error('Fallback return processing failed', [
                'error' => $e->getMessage()
            ]);
        }

        return 'Please use manual verification if payment was successful.';
    }

    /**
     * Cancel page
     */
    public function cancel(Request $request)
    {
        return view('frontend.binance.cancel');
    }

    /**
     * Manual verification
     */
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
            $amount = (float) $request->amount;

            // Find existing deposit
            $deposit = Deposit::where(function ($query) use ($queryCode) {
                $query->where('merchant_trade_no', $queryCode)
                    ->orWhere('prepay_id', $queryCode)
                    ->orWhere('transaction_id', $queryCode);
            })->where('user_id', $user->id)->first();

            // If not found, query Binance Pay
            if (!$deposit) {
                $orderStatus = $this->queryOrder($queryCode);

                if (!isset($orderStatus['status']) || $orderStatus['status'] !== 'SUCCESS') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment not found or invalid query code.'
                    ], 404);
                }

                $orderData = $orderStatus['data'] ?? [];
                $deposit = $this->createOrUpdateDeposit($user, $orderData, $orderStatus);
            }

            // Verify deposit
            if ($deposit->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This payment does not belong to your account.'
                ], 403);
            }

            if (abs($deposit->amount - $amount) > 0.01) {
                return response()->json([
                    'success' => false,
                    'message' => 'Amount mismatch. Expected: $' . number_format($deposit->amount, 2)
                ], 400);
            }

            if ($deposit->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment already processed.',
                    'balance' => $this->getUserBalance($user->id)
                ], 400);
            }

            // Process deposit
            if ($deposit->status === 'pending') {
                $this->processDeposit($deposit);
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment verified and processed successfully!',
                    'amount' => number_format($deposit->amount, 2),
                    'balance' => $this->getUserBalance($user->id)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment status: ' . $deposit->status
            ], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual verification failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create or update deposit from order data
     */
    private function createOrUpdateDeposit($user, $orderData, $orderStatus)
    {
        $merchantTradeNo = $orderData['merchantTradeNo'] ?? null;
        $status = $orderData['status'] ?? null;

        if ($status !== 'SUCCESS') {
            throw new \Exception('Payment not successful. Status: ' . ($status ?? 'Unknown'));
        }

        return Deposit::updateOrCreate(
            ['merchant_trade_no' => $merchantTradeNo],
            [
                'user_id' => $user->id,
                'prepay_id' => $orderData['prepayId'] ?? null,
                'transaction_id' => $orderData['transactionId'] ?? null,
                'amount' => $orderData['orderAmount'] ?? 0,
                'currency' => $orderData['currency'] ?? 'USDT',
                'status' => 'pending',
                'payment_method' => 'binance_pay',
                'response_data' => $orderStatus
            ]
        );
    }

    /**
     * Get user's wallet balance
     */
    private function getUserBalance($userId)
    {
        $wallet = Wallet::where('user_id', $userId)
            ->where('wallet_type', Wallet::TYPE_MAIN)
            ->first();
        
        return $wallet ? number_format($wallet->balance, 2) : '0.00';
    }

    /**
     * Process deposit - core logic
     */
    private function processDeposit(Deposit $deposit)
    {
        // Reload and lock
        $deposit = Deposit::where('id', $deposit->id)
            ->lockForUpdate()
            ->first();
        
        if (!$deposit || $deposit->status !== 'pending') {
            throw new \Exception('Deposit not in pending status');
        }

        $user = $deposit->user;
        if (!$user) {
            throw new \Exception('User not found');
        }

        if ($deposit->amount <= 0) {
            throw new \Exception('Invalid deposit amount');
        }

        // Get or create wallet
        $wallet = Wallet::lockForUpdate()
            ->firstOrCreate(
                ['user_id' => $user->id, 'wallet_type' => Wallet::TYPE_MAIN],
                [
                    'balance' => 0,
                    'currency' => $deposit->currency ?? 'USDT',
                    'status' => 'active'
                ]
            );

        $balanceBefore = (float) $wallet->balance;
        $newBalance = $balanceBefore + (float) $deposit->amount;
        
        // Update wallet
        $wallet->balance = $newBalance;
        $wallet->save();

        // Update deposit
        $deposit->status = 'completed';
        $deposit->completed_at = now();
        $deposit->save();

        // Create transaction record
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
            Log::error('Failed to create wallet transaction', [
                'deposit_id' => $deposit->id,
                'error' => $e->getMessage()
            ]);
        }

        // Distribute referral commissions
        try {
            if (class_exists('\App\Services\ReferralService')) {
                $referralService = new \App\Services\ReferralService();
                $referralService->distributeCommission($user, (float) $deposit->amount);
            }
        } catch (\Exception $e) {
            Log::error('Failed to distribute referral commission', [
                'deposit_id' => $deposit->id,
                'error' => $e->getMessage()
            ]);
        }

        Log::info('Deposit processed successfully', [
            'deposit_id' => $deposit->id,
            'user_id' => $user->id,
            'amount' => $deposit->amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $newBalance
        ]);
    }

    /**
     * Manual process deposit (admin)
     */
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

            return back()->with([
                'message' => 'Deposit processed successfully',
                'alert-type' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual deposit processing failed', [
                'deposit_id' => $depositId,
                'error' => $e->getMessage()
            ]);

            return back()->with([
                'message' => 'Failed to process: ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }

    /**
     * Diagnostic endpoint for IP checking and configuration validation
     */
    public function diagnostic(Request $request)
    {
        try {
            $outgoingIp = $this->getOutgoingServerIp();
            $clientIp = request()->ip();
            $environment = app()->environment();
            
            // Configuration check
            $configStatus = [
                'api_key_set' => !empty($this->apiKey),
                'secret_key_set' => !empty($this->secretKey),
                'base_url' => $this->baseUrl,
                'api_key_source' => $this->getConfigSource('binance_api_key'),
                'secret_key_source' => $this->getConfigSource('binance_secret_key'),
                'base_url_source' => $this->getConfigSource('binance_base_url'),
            ];
            
            // Network information
            $networkInfo = [
                'client_ip' => $clientIp,
                'detected_outgoing_ip' => $outgoingIp,
                'server_addr' => $_SERVER['SERVER_ADDR'] ?? 'not_set',
                'server_name' => $_SERVER['SERVER_NAME'] ?? 'not_set',
                'http_host' => $_SERVER['HTTP_HOST'] ?? 'not_set',
            ];
            
            // Test Binance Pay connection (only if credentials are set)
            $testResult = null;
            $connectionStatus = 'not_tested';
            
            if ($configStatus['api_key_set'] && $configStatus['secret_key_set']) {
                try {
                    // Try to query a non-existent order to test API connectivity
                    $testMerchantNo = 'TEST_DIAG_' . time();
                    $this->queryOrder($testMerchantNo);
                    
                    $connectionStatus = 'success';
                    $testResult = ['message' => 'API connection successful'];
                    
                } catch (\Exception $e) {
                    $errorMsg = $e->getMessage();
                    $connectionStatus = 'failed';
                    
                    // Extract IP from error message if present
                    if (preg_match('/\((\d+\.\d+\.\d+\.\d+)\)/', $errorMsg, $matches)) {
                        $testResult = [
                            'binance_reported_ip' => $matches[1],
                            'error' => $errorMsg,
                            'ip_match' => ($matches[1] === $outgoingIp)
                        ];
                    } else {
                        $testResult = [
                            'error' => $errorMsg,
                            'error_short' => substr($errorMsg, 0, 200)
                        ];
                    }
                }
            } else {
                $testResult = ['error' => 'API credentials not configured'];
            }
            
            // Generate recommendations
            $recommendations = [];
            
            if (!$configStatus['api_key_set'] || !$configStatus['secret_key_set']) {
                $recommendations[] = 'Set BINANCE_API_KEY and BINANCE_SECRET_KEY in your .env file or admin settings';
            }
            
            if ($outgoingIp && $outgoingIp !== 'local-development') {
                $recommendations[] = "Whitelist IP {$outgoingIp} in Binance Pay dashboard: Settings → API Management → IP Whitelist";
            } elseif ($environment === 'local') {
                $recommendations[] = 'In local environment: Set SERVER_OUTGOING_IP in .env to your public IP for testing';
                $recommendations[] = 'Or test in production environment where IP can be detected automatically';
            } else {
                $recommendations[] = 'Could not detect outgoing IP. Set SERVER_OUTGOING_IP in .env file';
            }
            
            if (isset($testResult['binance_reported_ip']) && $outgoingIp && $testResult['binance_reported_ip'] !== $outgoingIp) {
                $recommendations[] = "Binance sees your IP as {$testResult['binance_reported_ip']} - whitelist this IP instead";
            }
            
            // System information
            $systemInfo = [
                'environment' => $environment,
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'timezone' => config('app.timezone'),
                'current_time' => now()->toIso8601String(),
            ];

            return response()->json([
                'success' => true,
                'diagnostics' => [
                    'system' => $systemInfo,
                    'configuration' => $configStatus,
                    'network' => $networkInfo,
                    'connection_test' => [
                        'status' => $connectionStatus,
                        'result' => $testResult
                    ],
                    'recommendations' => $recommendations
                ],
                'debug_info' => $this->isLocalEnv ? [
                    'headers_sample' => [
                        'user_agent' => $request->header('User-Agent'),
                        'accept' => $request->header('Accept'),
                    ]
                ] : null
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $this->isLocalEnv ? $e->getTraceAsString() : null
            ], 500);
        }
    }
    
    /**
     * Get the source of configuration value
     */
    private function getConfigSource($key)
    {
        if (GlobalSetting::getValue($key)) {
            return 'database';
        }
        
        // Map key to config path
        $configKey = str_replace('binance_', '', $key);
        $configValue = config('services.binance.' . $configKey);
        
        if ($configValue) {
            return 'config';
        }
        
        if (env(strtoupper($key))) {
            return 'env';
        }
        
        return 'not_set';
    }
}

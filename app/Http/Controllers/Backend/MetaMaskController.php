<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Wallet;
use App\Models\WalletTransaction;

class MetaMaskController extends Controller
{
    /**
     * Create a deposit request for MetaMask payment
     */
    public function createDeposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10|max:10000',
            'currency' => 'nullable|string|max:10',
            'network' => 'nullable|string|in:ethereum,bsc,polygon',
            'token_address' => 'nullable|string', // For ERC20 tokens, null for native token
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $amount = $request->amount;
            $currency = $request->currency ?? 'USDT';
            $network = $request->network ?? env('CHAIN_NETWORK', 'ethereum');
            $tokenAddress = $request->token_address;

            // Auto-detect token address for common tokens if not provided
            if (!$tokenAddress && $currency === 'USDT') {
                // USDT token addresses on different networks
                switch (strtolower($network)) {
                    case 'ethereum':
                        $tokenAddress = '0xdAC17F958D2ee523a2206206994597C13D831ec7'; // USDT on Ethereum
                        break;
                    case 'bsc':
                        $tokenAddress = '0x55d398326f99059fF775485246999027B3197955'; // USDT on BSC
                        break;
                    case 'polygon':
                        $tokenAddress = '0xc2132D05D31c914a87C6611C10748AEb04B58e8F'; // USDT on Polygon
                        break;
                }
            } elseif (!$tokenAddress && $currency === 'USDC') {
                // USDC token addresses
                switch (strtolower($network)) {
                    case 'ethereum':
                        $tokenAddress = '0xA0b86991c6218b36c1d19D4a2e9Eb0cE3606eB48'; // USDC on Ethereum
                        break;
                    case 'bsc':
                        $tokenAddress = '0x8AC76a51cc950d9822D68b83fE1Ad97B32Cd580d'; // USDC on BSC
                        break;
                    case 'polygon':
                        $tokenAddress = '0x2791Bca1f2de4661ED88A30C99A7a9449Aa84174'; // USDC on Polygon
                        break;
                }
            }

            // Generate unique merchant trade number
            $merchantTradeNo = 'MM_' . time() . '_' . $user->id . '_' . uniqid();

            // Get merchant wallet address for this network
            $merchantAddress = $this->getMerchantAddress($network);

            if (!$merchantAddress) {
                throw new \Exception('Merchant wallet address not configured for ' . $network);
            }

            // Create deposit record
            $deposit = Deposit::create([
                'user_id' => $user->id,
                'merchant_trade_no' => $merchantTradeNo,
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'pending',
                'payment_method' => 'metamask',
                'response_data' => [
                    'network' => $network,
                    'token_address' => $tokenAddress,
                    'merchant_address' => $merchantAddress,
                ],
            ]);

            DB::commit();

            Log::info('MetaMask deposit created', [
                'user_id' => $user->id,
                'deposit_id' => $deposit->id,
                'amount' => $amount,
                'currency' => $currency,
                'network' => $network,
                'merchant_address' => $merchantAddress,
            ]);

            return response()->json([
                'success' => true,
                'deposit_id' => $deposit->id,
                'merchant_trade_no' => $merchantTradeNo,
                'merchant_address' => $merchantAddress,
                'amount' => $amount,
                'currency' => $currency,
                'network' => $network,
                'token_address' => $tokenAddress,
                'token_decimals' => $this->getTokenDecimals($currency, $network),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MetaMask deposit creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create deposit: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify and process MetaMask transaction
     */
    public function verifyTransaction(Request $request)
    {
        $request->validate([
            'tx_hash' => 'required|string',
            'deposit_id' => 'required|integer|exists:deposits,id',
            'network' => 'nullable|string|in:ethereum,bsc,polygon',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $txHash = trim($request->tx_hash);
            $depositId = $request->deposit_id;
            $network = $request->network ?? env('CHAIN_NETWORK', 'ethereum');

            // Validate transaction hash format
            if (!preg_match('/^0x[a-fA-F0-9]{64}$/', $txHash)) {
                // Check if user entered merchant address instead
                $merchantAddress = $this->getMerchantAddress($network);
                if (strtolower($txHash) === strtolower($merchantAddress)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You entered the merchant wallet address instead of transaction hash. Please enter the transaction hash (tx hash) from your MetaMask transaction. It should be 66 characters long starting with 0x.',
                    ], 400);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid transaction hash format. Transaction hash must be 66 characters (0x followed by 64 hexadecimal characters). Example: 0x1234567890abcdef...',
                ], 400);
            }

            // Check if this transaction hash has already been used (prevent duplicate deposits)
            // Check for completed deposits first (most important)
            $existingCompletedDeposit = Deposit::where('transaction_id', $txHash)
                ->where('payment_method', 'metamask')
                ->where('status', 'completed')
                ->first();

            if ($existingCompletedDeposit) {
                $wallet = Wallet::where('user_id', $user->id)->first();
                Log::warning('Duplicate transaction hash attempt - already completed', [
                    'user_id' => $user->id,
                    'tx_hash' => $txHash,
                    'existing_deposit_id' => $existingCompletedDeposit->id,
                    'existing_user_id' => $existingCompletedDeposit->user_id,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'This transaction has already been used to add balance. Each transaction can only be used once. Transaction was already processed on deposit #' . $existingCompletedDeposit->id . '.',
                    'balance' => $wallet ? number_format($wallet->balance, 2) : '0.00',
                ], 400);
            }

            // Get deposit first (needed for pending check)
            $deposit = Deposit::where('id', $depositId)
                ->where('user_id', $user->id)
                ->where('payment_method', 'metamask')
                ->where('status', 'pending')
                ->firstOrFail();

            // Also check if transaction is already assigned to any pending deposit (prevent race conditions)
            $existingPendingDeposit = Deposit::where('transaction_id', $txHash)
                ->where('payment_method', 'metamask')
                ->where('status', 'pending')
                ->where('id', '!=', $depositId) // Exclude current deposit
                ->first();

            if ($existingPendingDeposit) {
                Log::warning('Duplicate transaction hash attempt - already in pending deposit', [
                    'user_id' => $user->id,
                    'tx_hash' => $txHash,
                    'existing_deposit_id' => $existingPendingDeposit->id,
                    'current_deposit_id' => $depositId,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'This transaction is already being processed in another deposit (#' . $existingPendingDeposit->id . '). Each transaction can only be used once. Please wait for that deposit to complete or use a different transaction.',
                ], 400);
            }

            // Check if this deposit already has this transaction ID and is completed
            if ($deposit->transaction_id === $txHash && $deposit->status === 'completed') {
                $wallet = Wallet::where('user_id', $user->id)->first();
                return response()->json([
                    'success' => false,
                    'message' => 'This transaction has already been processed for this deposit.',
                    'balance' => $wallet ? number_format($wallet->balance, 2) : '0.00',
                ], 400);
            }

            // Verify transaction on blockchain
            $verificationResult = $this->verifyTransactionOnChain($txHash, $deposit, $network);

            if (!$verificationResult['verified']) {
                return response()->json([
                    'success' => false,
                    'message' => $verificationResult['message'],
                ], 400);
            }

            // Update deposit with transaction info
            $deposit->transaction_id = $txHash;
            $deposit->response_data = array_merge(
                $deposit->response_data ?? [],
                [
                    'verification_result' => $verificationResult,
                    'verified_at' => now()->toIso8601String(),
                ]
            );
            $deposit->save();

            // Process the deposit
            $this->processDeposit($deposit);

            DB::commit();

            $wallet = Wallet::where('user_id', $user->id)->first();

            Log::info('MetaMask transaction verified and processed', [
                'user_id' => $user->id,
                'deposit_id' => $deposit->id,
                'tx_hash' => $txHash,
                'amount' => $deposit->amount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaction verified and processed successfully!',
                'amount' => number_format($deposit->amount, 2),
                'balance' => $wallet ? number_format($wallet->balance, 2) : '0.00',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MetaMask transaction verification failed', [
                'user_id' => Auth::id(),
                'tx_hash' => $request->tx_hash,
                'deposit_id' => $request->deposit_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to verify transaction: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify transaction on blockchain
     */
    private function verifyTransactionOnChain($txHash, $deposit, $network)
    {
        try {
            $apiBase = $this->getApiBase($network);
            $apiKey = $this->getApiKey($network);

            if (!$apiKey) {
                return [
                    'verified' => false,
                    'message' => 'Blockchain API key not configured for ' . $network,
                ];
            }

            // Get transaction receipt using Etherscan API
            // Try the standard proxy endpoint first
            $response = Http::timeout(30)->get($apiBase, [
            'module' => 'proxy',
            'action' => 'eth_getTransactionReceipt',
            'txhash' => $txHash,
            'apikey' => $apiKey,
        ]);

            if ($response->failed()) {
                Log::error('Etherscan API request failed', [
                    'tx_hash' => $txHash,
                    'network' => $network,
                    'status_code' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [
                    'verified' => false,
                    'message' => 'Failed to fetch transaction from blockchain. Please try again later.',
                ];
            }

            $data = $response->json();
            
            Log::info('Etherscan API response', [
                'tx_hash' => $txHash,
                'network' => $network,
                'response_status' => $data['status'] ?? 'not_set',
                'response_message' => $data['message'] ?? 'not_set',
                'has_result' => isset($data['result']),
                'result_type' => gettype($data['result'] ?? null),
                'result_preview' => is_string($data['result'] ?? null) ? substr($data['result'], 0, 100) : (is_array($data['result'] ?? null) ? 'array with ' . count($data['result']) . ' keys' : 'null'),
            ]);
            
            // Check if API returned success (status = "1")
            if (isset($data['status']) && $data['status'] === '1' && isset($data['result']) && is_array($data['result'])) {
                // Transaction found successfully
                $receipt = $data['result'];
                Log::info('Transaction found successfully', [
                    'tx_hash' => $txHash,
                    'block_number' => $receipt['blockNumber'] ?? null,
                    'status' => $receipt['status'] ?? null,
                ]);
                // Continue to verification below - skip error handling
            }
            // Check if API returned an error (status = "0")
            elseif (isset($data['status']) && $data['status'] === '0') {
                $apiMessage = $data['message'] ?? 'Unknown error';
                $resultMessage = isset($data['result']) && is_string($data['result']) ? $data['result'] : '';
                
                Log::warning('Blockchain API error', [
                    'tx_hash' => $txHash,
                    'network' => $network,
                    'api_message' => $apiMessage,
                    'result_message' => $resultMessage,
                    'full_response' => $data,
                ]);
                
                // Check if it's just a deprecation warning
                if (stripos($resultMessage, 'deprecated') !== false) {
                    // Deprecation warning - but transaction might still exist
                    // Even with deprecation, if transaction exists, we should try to get it
                    // The issue is that Etherscan returns status "0" with deprecation message
                    // But the transaction might actually exist - we need to check the actual API response
                    // For now, let's try using a public RPC endpoint as fallback
                    Log::info('Etherscan API deprecation warning, trying alternative method', [
                        'tx_hash' => $txHash,
                        'network' => $network,
                    ]);
                    
                    // Try using public RPC endpoint as fallback
                    try {
                        $rpcUrl = $network === 'ethereum' ? 'https://eth.llamarpc.com' : ($network === 'bsc' ? 'https://bsc-dataseed.binance.org' : 'https://polygon-rpc.com');
                        $rpcResponse = Http::timeout(30)->post($rpcUrl, [
                            'jsonrpc' => '2.0',
                            'method' => 'eth_getTransactionReceipt',
                            'params' => [$txHash],
                            'id' => 1,
                        ]);
                        
                        if ($rpcResponse->successful()) {
                            $rpcData = $rpcResponse->json();
                            if (isset($rpcData['result']) && is_array($rpcData['result']) && !empty($rpcData['result'])) {
                                Log::info('Got transaction from RPC fallback', [
                                    'tx_hash' => $txHash,
                                ]);
                                $receipt = $rpcData['result'];
                                // Continue to verification below
                            } else {
                                $explorerUrl = $network === 'ethereum' ? 'etherscan.io' : ($network === 'bsc' ? 'bscscan.com' : 'polygonscan.com');
                                return [
                                    'verified' => false,
                                    'message' => 'Transaction not found. Please verify the transaction exists on https://' . $explorerUrl . '/tx/' . $txHash,
                                ];
                            }
                        } else {
                            $explorerUrl = $network === 'ethereum' ? 'etherscan.io' : ($network === 'bsc' ? 'bscscan.com' : 'polygonscan.com');
                            return [
                                'verified' => false,
                                'message' => 'Transaction not found. Please verify the transaction exists on https://' . $explorerUrl . '/tx/' . $txHash,
                            ];
                        }
                    } catch (\Exception $e) {
                        Log::error('RPC fallback failed', [
                            'tx_hash' => $txHash,
                            'error' => $e->getMessage(),
                        ]);
                        $explorerUrl = $network === 'ethereum' ? 'etherscan.io' : ($network === 'bsc' ? 'bscscan.com' : 'polygonscan.com');
                        return [
                            'verified' => false,
                            'message' => 'Transaction not found. Please verify the transaction exists on https://' . $explorerUrl . '/tx/' . $txHash,
                        ];
                    }
                } else {
                    // Provide user-friendly error messages
                    $errorMessage = 'Failed to fetch transaction from blockchain. ';
                    
                    if (stripos($apiMessage, 'NOTOK') !== false || stripos($apiMessage, 'invalid') !== false) {
                        // Check if tx hash looks like an address
                        if (preg_match('/^0x[a-fA-F0-9]{40}$/', $txHash)) {
                            $errorMessage .= 'The entered value looks like a wallet address (42 characters), not a transaction hash (66 characters). Please enter the transaction hash from your MetaMask transaction history.';
                        } else {
                            $explorerUrl = $network === 'ethereum' ? 'etherscan.io' : ($network === 'bsc' ? 'bscscan.com' : 'polygonscan.com');
                            $errorMessage .= 'Transaction not found. Please verify: 1) Transaction hash is correct (66 characters: 0x + 64 hex characters), 2) Transaction was sent on ' . $network . ' network, 3) Transaction has been confirmed. Check on ' . $explorerUrl . '/tx/' . $txHash;
                        }
                    } else {
                        $errorMessage .= $apiMessage . ($resultMessage ? ' - ' . $resultMessage : '');
                    }
                    
                    return [
                        'verified' => false,
                        'message' => $errorMessage,
                    ];
                }
            }

            // At this point, if we haven't set $receipt yet, check the response
            if (!isset($receipt)) {
                // Get receipt from response
                $receipt = null;
                
                // If status is "1", transaction was found
                if (isset($data['status']) && $data['status'] === '1' && isset($data['result']) && is_array($data['result'])) {
                    $receipt = $data['result'];
                } 
                // If status is "0" but result is an array (transaction data), use it
                elseif (isset($data['result']) && is_array($data['result']) && !empty($data['result'])) {
                    $receipt = $data['result'];
                }
                // Otherwise, result might be null or error message
                else {
        $receipt = $data['result'] ?? null;
                }

                if (!$receipt || !is_array($receipt)) {
                    $explorerUrl = $network === 'ethereum' ? 'etherscan.io' : ($network === 'bsc' ? 'bscscan.com' : 'polygonscan.com');
                    return [
                        'verified' => false,
                        'message' => 'Transaction not found on blockchain. Please verify: 1) Transaction hash is correct (66 characters: 0x + 64 hex), 2) Transaction was sent on ' . $network . ' network (not testnet), 3) Transaction has been confirmed. Check on https://' . $explorerUrl . '/tx/' . $txHash . ' to verify the transaction exists.',
                    ];
                }
            }

            // Check transaction status (0x1 = success, 0x0 = failed)
        if (!isset($receipt['status']) || $receipt['status'] === '0x0') {
                Log::warning('Transaction failed on blockchain', [
                    'tx_hash' => $txHash,
                    'network' => $network,
                    'status' => $receipt['status'] ?? 'not_set',
                ]);
                return [
                    'verified' => false,
                    'message' => 'Transaction failed on blockchain. Status: ' . ($receipt['status'] ?? 'unknown'),
                ];
            }

            $merchantAddress = strtolower($this->getMerchantAddress($network));
            
            // Safely get token address from response_data
            $responseData = is_array($deposit->response_data) ? $deposit->response_data : [];
            $tokenAddress = isset($responseData['token_address']) ? strtolower($responseData['token_address']) : null;
            
            $expectedAmount = $deposit->amount;
            $tokenDecimals = $this->getTokenDecimals($deposit->currency, $network);
            
            // Auto-detect ERC20 token if not specified but currency is USDT/USDC/etc
        $logs = $receipt['logs'] ?? [];
            $transferEventSignature = '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef';
            
            // If token address not set but we have logs, try to detect ERC20 transfer
            if (!$tokenAddress && !empty($logs)) {
                foreach ($logs as $log) {
                    if (isset($log['topics'][0]) && strtolower($log['topics'][0]) === $transferEventSignature) {
                        // Found Transfer event - this is an ERC20 token transfer
                        $tokenAddress = strtolower($log['address']);
                        
                        // Check if this transfer is to our merchant address
                        $toTopic = $log['topics'][2] ?? null;
                        if ($toTopic) {
                            $toAddress = '0x' . substr($toTopic, 26);
                            if (strtolower($toAddress) === $merchantAddress) {
                                // This is the token we're looking for
                                Log::info('Auto-detected ERC20 token', [
                                    'token_address' => $tokenAddress,
                                    'currency' => $deposit->currency,
                                ]);
                                break;
                            }
                        }
                    }
                }
            }
            
            Log::info('Verifying transaction', [
                'tx_hash' => $txHash,
                'network' => $network,
                'merchant_address' => $merchantAddress,
                'token_address' => $tokenAddress,
                'expected_amount' => $expectedAmount,
                'currency' => $deposit->currency,
                'token_decimals' => $tokenDecimals,
                'has_logs' => !empty($logs),
            ]);

            // Convert amount to smallest unit (wei/smallest token unit)
            $expectedAmountWei = bcmul((string)$expectedAmount, bcpow('10', (string)$tokenDecimals, 0), 0);

            // Check if it's native token (ETH/BNB/MATIC) or ERC20 token
            // If we have token address (either from deposit or auto-detected), it's ERC20
            if (!$tokenAddress) {
                // Native token transfer
                $toAddress = strtolower($receipt['to'] ?? '');
                $value = $receipt['value'] ?? '0x0';
                $fromAddress = strtolower($receipt['from'] ?? '');

                Log::info('Verifying native token transfer', [
                    'tx_hash' => $txHash,
                    'from' => $fromAddress,
                    'to' => $toAddress,
                    'merchant' => $merchantAddress,
                    'value_hex' => $value,
                    'expected_wei' => $expectedAmountWei,
                ]);

                if ($toAddress !== $merchantAddress) {
                    Log::warning('Address mismatch', [
                        'tx_hash' => $txHash,
                        'received_to' => $toAddress,
                        'expected_to' => $merchantAddress,
                    ]);
                    return [
                        'verified' => false,
                        'message' => 'Transaction recipient address does not match merchant address. Expected: ' . $merchantAddress . ', Got: ' . $toAddress,
                    ];
                }

                // Compare amounts - convert hex to decimal
                $receivedAmount = $this->hexToDecimal($value);
                Log::info('Amount comparison', [
                    'received_wei' => $receivedAmount,
                    'expected_wei' => $expectedAmountWei,
                    'comparison' => bccomp($receivedAmount, $expectedAmountWei, 0),
                ]);
                
                if (bccomp($receivedAmount, $expectedAmountWei, 0) < 0) {
                    $receivedFormatted = bcdiv($receivedAmount, bcpow('10', (string)$tokenDecimals, 0), 6);
                    $expectedFormatted = bcdiv($expectedAmountWei, bcpow('10', (string)$tokenDecimals, 0), 6);
                    Log::warning('Amount mismatch', [
                        'tx_hash' => $txHash,
                        'received' => $receivedFormatted,
                        'expected' => $expectedFormatted,
                    ]);
                    return [
                        'verified' => false,
                        'message' => 'Transaction amount mismatch. Expected: ' . $expectedFormatted . ' ' . $deposit->currency . ', Received: ' . $receivedFormatted . ' ' . $deposit->currency,
                    ];
                }
            } else {
                // ERC20 token transfer
                $logs = $receipt['logs'] ?? [];
                $transferEventSignature = '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef';
        $found = false;
                $receivedAmount = '0';

                Log::info('Verifying ERC20 token transfer', [
                    'tx_hash' => $txHash,
                    'token_address' => $tokenAddress,
                    'merchant_address' => $merchantAddress,
                    'logs_count' => count($logs),
                ]);

                foreach ($logs as $logIndex => $log) {
                    if (
                        isset($log['topics'][0]) &&
                        strtolower($log['topics'][0]) === $transferEventSignature &&
                        strtolower($log['address']) === $tokenAddress
                    ) {
                        // topics[1] is 'from' address, topics[2] is 'to' address (indexed)
                        $fromTopic = $log['topics'][1] ?? null;
                        $toTopic = $log['topics'][2] ?? null;
                        
                        if ($toTopic) {
                            $toAddress = '0x' . substr($toTopic, 26);
                            $fromAddress = $fromTopic ? '0x' . substr($fromTopic, 26) : 'unknown';
                            
                            Log::info('Found Transfer event', [
                                'log_index' => $logIndex,
                                'from' => $fromAddress,
                                'to' => $toAddress,
                                'merchant' => $merchantAddress,
                            ]);
                            
                            if (strtolower($toAddress) === $merchantAddress) {
                                // data contains the amount (uint256)
                                $valueHex = $log['data'] ?? '0x0';
                                $receivedAmount = $this->hexToDecimal($valueHex);
                                $found = true;
                                
                                Log::info('Matching transfer found', [
                                    'received_wei' => $receivedAmount,
                                    'expected_wei' => $expectedAmountWei,
                                ]);
                                break;
                            }
                        }
                    }
                }

                if (!$found) {
                    Log::warning('No matching token transfer found', [
                        'tx_hash' => $txHash,
                        'token_address' => $tokenAddress,
                        'merchant_address' => $merchantAddress,
                        'total_logs' => count($logs),
                    ]);
                    return [
                        'verified' => false,
                        'message' => 'No matching token transfer to merchant address found in transaction. Please verify you sent to the correct address: ' . $merchantAddress,
                    ];
                }

                // Compare amounts
                if (bccomp($receivedAmount, $expectedAmountWei, 0) < 0) {
                    $receivedFormatted = bcdiv($receivedAmount, bcpow('10', (string)$tokenDecimals, 0), 6);
                    $expectedFormatted = bcdiv($expectedAmountWei, bcpow('10', (string)$tokenDecimals, 0), 6);
                    Log::warning('ERC20 amount mismatch', [
                        'tx_hash' => $txHash,
                        'received' => $receivedFormatted,
                        'expected' => $expectedFormatted,
                    ]);
                    return [
                        'verified' => false,
                        'message' => 'Transaction amount mismatch. Expected: ' . $expectedFormatted . ' ' . $deposit->currency . ', Received: ' . $receivedFormatted . ' ' . $deposit->currency,
                    ];
                }
            }

            return [
                'verified' => true,
                'message' => 'Transaction verified successfully',
                'received_amount' => $receivedAmount ?? $expectedAmountWei,
                'expected_amount' => $expectedAmountWei,
            ];
        } catch (\Exception $e) {
            Log::error('Transaction verification error', [
                'tx_hash' => $txHash,
                'network' => $network,
                'error' => $e->getMessage(),
            ]);

            return [
                'verified' => false,
                'message' => 'Error verifying transaction: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Process deposit to wallet
     */
    private function processDeposit(Deposit $deposit)
    {
        $deposit->refresh();

        if ($deposit->status !== 'pending') {
            throw new \Exception('Deposit is not in pending status. Current status: ' . $deposit->status);
        }

        $user = $deposit->user;

        if (!$user) {
            throw new \Exception('User not found for deposit');
        }

        if ($deposit->amount <= 0) {
            throw new \Exception('Invalid deposit amount: ' . $deposit->amount);
        }

        // Get or create wallet
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
                'description' => 'Deposit via MetaMask',
                'metadata' => [
                    'merchant_trade_no' => $deposit->merchant_trade_no,
                    'transaction_id' => $deposit->transaction_id,
                    'network' => $deposit->response_data['network'] ?? null,
                    'currency' => $deposit->currency,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create wallet transaction', [
                'deposit_id' => $deposit->id,
                'error' => $e->getMessage(),
            ]);
        }

        Log::info('MetaMask deposit processed', [
            'deposit_id' => $deposit->id,
            'user_id' => $user->id,
            'amount' => $deposit->amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $newBalance,
        ]);
    }

    /**
     * Get API base URL for network
     * Note: Etherscan V1 is deprecated but proxy endpoints still work
     * Keep using /api endpoint as proxy module works there
     */
    private function getApiBase($network)
    {
        switch (strtolower($network)) {
            case 'bsc':
                return 'https://api.bscscan.com/api';
            case 'polygon':
                return 'https://api.polygonscan.com/api';
            case 'ethereum':
            default:
                // Keep using /api endpoint - proxy module still works here
                // The deprecation warning is for other endpoints, not proxy
                return 'https://api.etherscan.io/api';
        }
    }

    /**
     * Get API key for network
     */
    private function getApiKey($network)
    {
        switch (strtolower($network)) {
            case 'bsc':
                return env('BSCSCAN_API_KEY') ?: env('ETHERSCAN_API_KEY');
            case 'polygon':
                return env('POLYGONSCAN_API_KEY') ?: env('ETHERSCAN_API_KEY');
            case 'ethereum':
            default:
                return env('ETHERSCAN_API_KEY');
        }
    }

    /**
     * Get merchant wallet address for network
     */
    private function getMerchantAddress($network)
    {
        switch (strtolower($network)) {
            case 'bsc':
                return env('MERCHANT_ADDRESS_BSC') ?: env('MERCHANT_ADDRESS');
            case 'polygon':
                return env('MERCHANT_ADDRESS_POLYGON') ?: env('MERCHANT_ADDRESS');
            case 'ethereum':
            default:
                return env('MERCHANT_ADDRESS');
        }
    }

    /**
     * Get token decimals
     */
    private function getTokenDecimals($currency, $network)
    {
        // Common token decimals
        $decimals = [
            'USDT' => 6, // USDT on most chains uses 6 decimals
            'USDC' => 6,
            'DAI' => 18,
            'ETH' => 18,
            'BNB' => 18,
            'MATIC' => 18,
        ];

        // USDT on Ethereum mainnet uses 6, but on BSC/Polygon might use 18
        if ($currency === 'USDT' && $network !== 'ethereum') {
            return 18;
        }

        return $decimals[strtoupper($currency)] ?? 18;
    }

    /**
     * Convert hexadecimal string to decimal string
     * Uses BCMath for big number support (works without GMP extension)
     */
    private function hexToDecimal($hex)
    {
        // Remove 0x prefix if present
        $hex = ltrim($hex, '0x');
        
        // Handle empty or zero
        if (empty($hex) || $hex === '0') {
            return '0';
        }
        
        // Convert hex to decimal using BCMath
        // BCMath doesn't have direct hex conversion, so we convert digit by digit
        $decimal = '0';
        $hexLength = strlen($hex);
        
        for ($i = 0; $i < $hexLength; $i++) {
            $digit = hexdec($hex[$i]);
            $decimal = bcmul($decimal, '16', 0);
            $decimal = bcadd($decimal, (string)$digit, 0);
        }
        
        return $decimal;
    }

    /**
     * Check transaction status (for polling)
     */
    public function checkTransactionStatus(Request $request)
    {
        $request->validate([
            'tx_hash' => 'required|string',
            'network' => 'nullable|string|in:ethereum,bsc,polygon',
        ]);

        try {
            $txHash = trim($request->tx_hash);
            $network = $request->network ?? env('CHAIN_NETWORK', 'ethereum');

            // Validate transaction hash format
            if (!preg_match('/^0x[a-fA-F0-9]{64}$/', $txHash)) {
                // Check if user entered merchant address instead
                $merchantAddress = $this->getMerchantAddress($network);
                if (strtolower($txHash) === strtolower($merchantAddress)) {
                    return response()->json([
                        'success' => false,
                        'status' => 'error',
                        'message' => 'You entered the merchant wallet address instead of transaction hash. Please enter the transaction hash from your MetaMask transaction.',
                    ], 400);
                }
                
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Invalid transaction hash format. Must be 66 characters (0x + 64 hex characters).',
                ], 400);
            }

            $apiBase = $this->getApiBase($network);
            $apiKey = $this->getApiKey($network);

            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key not configured',
                ], 400);
            }

            $response = Http::timeout(30)->get($apiBase, [
                'module' => 'proxy',
                'action' => 'eth_getTransactionReceipt',
                'txhash' => $txHash,
                'apikey' => $apiKey,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch transaction',
                ], 500);
            }

            $data = $response->json();
            
            // Check if API returned an error
            if (isset($data['status']) && $data['status'] === '0') {
                $apiMessage = $data['message'] ?? 'Unknown error';
                
                // Check if tx hash looks like an address
                $errorMessage = 'Failed to check transaction status. ';
                if (preg_match('/^0x[a-fA-F0-9]{40}$/', $txHash)) {
                    $errorMessage = 'The entered value looks like a wallet address, not a transaction hash. Please enter the transaction hash (66 characters) from your MetaMask transaction history.';
                } else if (stripos($apiMessage, 'NOTOK') !== false || stripos($apiMessage, 'invalid') !== false) {
                    $errorMessage = 'Invalid transaction hash. Please verify you entered the correct transaction hash from your MetaMask transaction.';
                } else {
                    $errorMessage .= $apiMessage;
                }
                
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => $errorMessage,
                ], 400);
            }

            $receipt = $data['result'] ?? null;

            if (!$receipt) {
                return response()->json([
                    'success' => true,
                    'status' => 'pending',
                    'message' => 'Transaction is still pending',
                ]);
            }

            // Check if receipt is actually an array (transaction data) or a string (error/null)
            if (!is_array($receipt)) {
                return response()->json([
                    'success' => true,
                    'status' => 'pending',
                    'message' => 'Transaction is still pending or not found',
                ]);
            }

            $status = (isset($receipt['status']) && $receipt['status'] === '0x1') ? 'success' : 'failed';

            return response()->json([
                'success' => true,
                'status' => $status,
                'block_number' => $receipt['blockNumber'] ?? null,
                'confirmations' => isset($receipt['blockNumber']) ? 'confirmed' : 'pending',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking transaction: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test transaction hash on Etherscan (for debugging)
     */
    public function testTransaction(Request $request)
    {
        $request->validate([
            'tx_hash' => 'required|string',
            'network' => 'nullable|string|in:ethereum,bsc,polygon',
        ]);

        $txHash = trim($request->tx_hash);
        $network = $request->network ?? env('CHAIN_NETWORK', 'ethereum');
        
        $apiBase = $this->getApiBase($network);
        $apiKey = $this->getApiKey($network);
        
        $response = Http::timeout(30)->get($apiBase, [
            'module' => 'proxy',
            'action' => 'eth_getTransactionReceipt',
            'txhash' => $txHash,
            'apikey' => $apiKey,
        ]);
        
        $data = $response->json();
        $explorerUrl = $network === 'ethereum' ? 'etherscan.io' : ($network === 'bsc' ? 'bscscan.com' : 'polygonscan.com');
        
        return response()->json([
            'success' => true,
            'tx_hash' => $txHash,
            'network' => $network,
            'api_response' => $data,
            'explorer_url' => 'https://' . $explorerUrl . '/tx/' . $txHash,
            'status' => $data['status'] ?? 'unknown',
            'message' => $data['message'] ?? 'unknown',
            'has_result' => isset($data['result']),
            'result_type' => gettype($data['result'] ?? null),
        ]);
    }
}

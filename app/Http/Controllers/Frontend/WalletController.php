<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class WalletController extends Controller
{
    /**
     * Transfer balance from earning wallet to main wallet
     */
    public function transferEarningToMain(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'wallet_password' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Verify wallet password
            if (empty($user->withdrawal_password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet password not set. Please set your wallet password first.',
                ], 400);
            }

            if (!Hash::check($request->wallet_password, $user->withdrawal_password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid wallet password. Please try again.',
                ], 400);
            }

            // Get earning wallet
            $earningWallet = Wallet::lockForUpdate()
                ->firstOrCreate(
                    ['user_id' => $user->id, 'wallet_type' => Wallet::TYPE_EARNING],
                    ['balance' => 0, 'currency' => 'USDT', 'status' => 'active']
                );

            // Get main wallet
            $mainWallet = Wallet::lockForUpdate()
                ->firstOrCreate(
                    ['user_id' => $user->id, 'wallet_type' => Wallet::TYPE_MAIN],
                    ['balance' => 0, 'currency' => 'USDT', 'status' => 'active']
                );

            $amount = (float) $request->amount;

            // Validate amount
            if ($amount > $earningWallet->balance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance in earning wallet. Available: $' . number_format($earningWallet->balance, 2),
                ], 400);
            }

            if ($amount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid transfer amount.',
                ], 400);
            }

            // Transfer from earning to main
            $earningBalanceBefore = $earningWallet->balance;
            $mainBalanceBefore = $mainWallet->balance;

            $earningWallet->balance -= $amount;
            $mainWallet->balance += $amount;

            $earningWallet->save();
            $mainWallet->save();

            // Create transaction records for both wallets
            WalletTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $earningWallet->id,
                'type' => 'transfer_out',
                'amount' => -$amount,
                'balance_before' => $earningBalanceBefore,
                'balance_after' => $earningWallet->balance,
                'description' => 'Transfer to Main Wallet',
                'metadata' => [
                    'transfer_type' => 'earning_to_main',
                    'target_wallet_id' => $mainWallet->id,
                ],
            ]);

            WalletTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $mainWallet->id,
                'type' => 'transfer_in',
                'amount' => $amount,
                'balance_before' => $mainBalanceBefore,
                'balance_after' => $mainWallet->balance,
                'description' => 'Transfer from Earning Wallet',
                'metadata' => [
                    'transfer_type' => 'earning_to_main',
                    'source_wallet_id' => $earningWallet->id,
                ],
            ]);

            DB::commit();

            Log::info('Balance transferred from earning to main wallet', [
                'user_id' => $user->id,
                'amount' => $amount,
                'earning_balance_before' => $earningBalanceBefore,
                'earning_balance_after' => $earningWallet->balance,
                'main_balance_before' => $mainBalanceBefore,
                'main_balance_after' => $mainWallet->balance,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Balance transferred successfully!',
                'earning_balance' => number_format($earningWallet->balance, 2),
                'main_balance' => number_format($mainWallet->balance, 2),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to transfer balance', [
                'user_id' => Auth::id(),
                'amount' => $request->amount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Transfer failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get transfer history for the authenticated user
     */
    public function getTransferHistory(Request $request)
    {
        try {
            $user = Auth::user();

            // Get transfer transactions (both transfer_in and transfer_out)
            $transfers = WalletTransaction::where('user_id', $user->id)
                ->whereIn('type', ['transfer_in', 'transfer_out'])
                ->with('wallet')
                ->orderBy('created_at', 'desc')
                ->get();

            // Format the data
            $history = $transfers->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => abs($transaction->amount),
                    'balance_before' => $transaction->balance_before,
                    'balance_after' => $transaction->balance_after,
                    'description' => $transaction->description,
                    'created_at' => $transaction->created_at->format('M d, Y h:i A'),
                    'created_at_human' => $transaction->created_at->diffForHumans(),
                    'wallet_type' => $transaction->wallet->wallet_type ?? 'main',
                ];
            });

            return response()->json([
                'success' => true,
                'history' => $history,
                'total' => $history->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch transfer history', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch transfer history',
            ], 500);
        }
    }
}

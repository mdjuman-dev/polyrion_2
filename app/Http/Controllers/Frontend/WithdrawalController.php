<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    /**
     * Show withdrawal request form
     */
    public function index()
    {
        $user = Auth::user();
        // Get main wallet for withdrawals
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id, 'wallet_type' => Wallet::TYPE_MAIN],
            ['balance' => 0, 'status' => 'active', 'currency' => 'USDT']
        );

        $withdrawals = Withdrawal::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('frontend.withdrawal.index', compact('wallet', 'withdrawals'));
    }

    /**
     * Store withdrawal request
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:crypto',
            'payment_details' => 'required|array',
            'withdrawal_password' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            
            // Check withdrawal password
            if (!$user->withdrawal_password) {
                return response()->json([
                    'success' => false,
                    'message' => 'Withdrawal password not set. Please set your withdrawal password first.'
                ], 400);
            }

            if (!Hash::check($request->withdrawal_password, $user->withdrawal_password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect withdrawal password.'
                ], 400);
            }

            // Get main wallet for withdrawals
            $wallet = Wallet::lockForUpdate()
                ->firstOrCreate(
                    ['user_id' => $user->id, 'wallet_type' => Wallet::TYPE_MAIN],
                    ['balance' => 0, 'status' => 'active', 'currency' => 'USDT']
                );

            $amount = $request->amount;

            // Check if user has sufficient balance
            if ($wallet->balance < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance. Your current balance is ' . number_format($wallet->balance, 2) . ' ' . $wallet->currency
                ], 400);
            }

            // Check minimum withdrawal amount (you can set this in settings)
            $minWithdrawal = 10; // Minimum withdrawal amount
            if ($amount < $minWithdrawal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minimum withdrawal amount is ' . $minWithdrawal . ' ' . $wallet->currency
                ], 400);
            }

            // Lock the amount from wallet
            $balanceBefore = (float) $wallet->balance;
            $wallet->balance -= $amount;
            $wallet->save();

            // Create withdrawal request
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'currency' => $wallet->currency,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_details' => $request->payment_details,
            ]);

            // Create wallet transaction record
            WalletTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'type' => 'withdraw', // Use 'withdraw' not 'withdrawal'
                'amount' => -$amount, // Negative for withdrawal
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'reference_type' => Withdrawal::class,
                'reference_id' => $withdrawal->id,
                'description' => 'Withdrawal request - ' . ucfirst($request->payment_method),
                'metadata' => [
                    'payment_method' => $request->payment_method,
                    'payment_details' => $request->payment_details,
                ],
            ]);

            DB::commit();

            Log::info("Withdrawal request created", [
                'user_id' => $user->id,
                'withdrawal_id' => $withdrawal->id,
                'amount' => $amount,
                'payment_method' => $request->payment_method
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully. It will be reviewed by admin.',
                'withdrawal' => $withdrawal,
                'balance' => number_format($wallet->balance, 2)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Withdrawal request failed", [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Withdrawal request failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Show withdrawal history
     */
    public function history()
    {
        $user = Auth::user();
        $withdrawals = Withdrawal::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('frontend.withdrawal.history', compact('withdrawals'));
    }
}


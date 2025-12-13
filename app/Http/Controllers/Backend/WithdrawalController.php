<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    /**
     * Display all withdrawal requests
     */
    public function index(Request $request)
    {
        $query = Withdrawal::with(['user', 'approver']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search by user email or name
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $withdrawals = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'pending' => Withdrawal::where('status', 'pending')->count(),
            'processing' => Withdrawal::where('status', 'processing')->count(),
            'completed' => Withdrawal::where('status', 'completed')->count(),
            'rejected' => Withdrawal::where('status', 'rejected')->count(),
            'total' => Withdrawal::count(),
        ];

        return view('backend.withdrawal.index', compact('withdrawals', 'stats'));
    }

    /**
     * Show withdrawal details
     */
    public function show($id)
    {
        $withdrawal = Withdrawal::with(['user', 'approver'])->findOrFail($id);
        return view('backend.withdrawal.show', compact('withdrawal'));
    }

    /**
     * Approve withdrawal
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $withdrawal = Withdrawal::lockForUpdate()->findOrFail($id);

            if ($withdrawal->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Withdrawal is not in pending status.'
                ], 400);
            }

            $admin = Auth::guard('admin')->user();

            // Update withdrawal status
            $withdrawal->status = 'completed';
            $withdrawal->approved_by = $admin->id;
            $withdrawal->admin_note = $request->admin_note;
            $withdrawal->processed_at = now();
            $withdrawal->save();

            // Note: The amount was already deducted from wallet when withdrawal was created
            // So we don't need to deduct again here

            DB::commit();

            Log::info("Withdrawal approved", [
                'withdrawal_id' => $withdrawal->id,
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
                'amount' => $withdrawal->amount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal approved successfully.',
                'withdrawal' => $withdrawal->load('approver')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Withdrawal approval failed", [
                'withdrawal_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve withdrawal. Please try again.'
            ], 500);
        }
    }

    /**
     * Reject withdrawal
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $withdrawal = Withdrawal::lockForUpdate()->findOrFail($id);

            if ($withdrawal->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Withdrawal is not in pending status.'
                ], 400);
            }

            $admin = Auth::guard('admin')->user();

            // Refund the amount back to user's wallet
            $user = $withdrawal->user;
            $wallet = Wallet::lockForUpdate()
                ->firstOrCreate(
                    ['user_id' => $user->id],
                    ['balance' => 0, 'status' => 'active', 'currency' => $withdrawal->currency]
                );

            $balanceBefore = (float) $wallet->balance;
            $wallet->balance += $withdrawal->amount;
            $wallet->save();

            // Update withdrawal status
            $withdrawal->status = 'rejected';
            $withdrawal->approved_by = $admin->id;
            $withdrawal->admin_note = $request->admin_note;
            $withdrawal->processed_at = now();
            $withdrawal->save();

            // Create wallet transaction for refund
            WalletTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'type' => 'refund',
                'amount' => $withdrawal->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'reference_type' => Withdrawal::class,
                'reference_id' => $withdrawal->id,
                'description' => 'Withdrawal rejected - Amount refunded',
                'metadata' => [
                    'withdrawal_id' => $withdrawal->id,
                    'admin_note' => $request->admin_note,
                    'rejected_by' => $admin->name,
                ],
            ]);

            DB::commit();

            Log::info("Withdrawal rejected", [
                'withdrawal_id' => $withdrawal->id,
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
                'amount' => $withdrawal->amount,
                'refunded' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal rejected and amount refunded to user wallet.',
                'withdrawal' => $withdrawal->load('approver')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Withdrawal rejection failed", [
                'withdrawal_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject withdrawal. Please try again.'
            ], 500);
        }
    }

    /**
     * Mark withdrawal as processing
     */
    public function processing($id)
    {
        try {
            $withdrawal = Withdrawal::findOrFail($id);

            if ($withdrawal->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Withdrawal is not in pending status.'
                ], 400);
            }

            $withdrawal->status = 'processing';
            $withdrawal->save();

            Log::info("Withdrawal marked as processing", [
                'withdrawal_id' => $withdrawal->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal marked as processing.',
                'withdrawal' => $withdrawal
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to mark withdrawal as processing", [
                'withdrawal_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update withdrawal status.'
            ], 500);
        }
    }
}


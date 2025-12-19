<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepositController extends Controller
{
    /**
     * Display all deposits
     */
    public function index(Request $request)
    {
        $query = Deposit::with('user');

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method !== '') {
            $query->where('payment_method', $request->payment_method);
        }

        // Search by user email or name
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('merchant_trade_no', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('email', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $deposits = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'pending' => Deposit::where('status', 'pending')->count(),
            'completed' => Deposit::where('status', 'completed')->count(),
            'failed' => Deposit::where('status', 'failed')->count(),
            'expired' => Deposit::where('status', 'expired')->count(),
            'total' => Deposit::count(),
            'manual_pending' => Deposit::where('status', 'pending')->where('payment_method', 'manual')->count(),
        ];

        return view('backend.deposits.index', compact('deposits', 'stats'));
    }

    /**
     * Show deposit details
     */
    public function show($id)
    {
        $deposit = Deposit::with('user')->findOrFail($id);
        return view('backend.deposits.show', compact('deposit'));
    }

    /**
     * Approve/Process deposit (add balance to user wallet)
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $deposit = Deposit::lockForUpdate()->findOrFail($id);

            if ($deposit->status !== 'pending') {
                return redirect()->route('admin.deposits.index')
                    ->with('error', 'Deposit is not in pending status. Current status: ' . $deposit->status);
            }

            $user = $deposit->user;

            if (!$user) {
                return redirect()->route('admin.deposits.index')
                    ->with('error', 'User not found for deposit');
            }

            if ($deposit->amount <= 0) {
                return redirect()->route('admin.deposits.index')
                    ->with('error', 'Invalid deposit amount: ' . $deposit->amount);
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
            
            // Store admin note in response_data if provided
            $responseData = $deposit->response_data ?? [];
            if ($request->admin_note) {
                $responseData['admin_note'] = $request->admin_note;
                $responseData['approved_by'] = Auth::guard('admin')->id();
                $responseData['approved_at'] = now()->toDateTimeString();
            }
            $deposit->response_data = $responseData;
            $deposit->save();

            // Create wallet transaction
            WalletTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'amount' => $deposit->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $newBalance,
                'reference_type' => Deposit::class,
                'reference_id' => $deposit->id,
                'description' => 'Deposit via ' . ucfirst($deposit->payment_method ?? 'Manual'),
                'metadata' => [
                    'merchant_trade_no' => $deposit->merchant_trade_no,
                    'transaction_id' => $deposit->transaction_id,
                    'payment_method' => $deposit->payment_method,
                    'currency' => $deposit->currency,
                    'query_code' => $deposit->response_data['query_code'] ?? null,
                ],
            ]);

            DB::commit();

            $admin = Auth::guard('admin')->user();
            Log::info('Deposit approved and processed', [
                'deposit_id' => $deposit->id,
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
                'user_id' => $user->id,
                'amount' => $deposit->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $newBalance,
            ]);

            return redirect()->route('admin.deposits.index')
                ->with('success', 'Deposit approved and balance added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Deposit approval failed', [
                'deposit_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.deposits.index')
                ->with('error', 'Failed to approve deposit: ' . $e->getMessage());
        }
    }

    /**
     * Reject deposit
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $deposit = Deposit::lockForUpdate()->findOrFail($id);

            if ($deposit->status !== 'pending') {
                return redirect()->route('admin.deposits.index')
                    ->with('error', 'Deposit is not in pending status.');
            }

            $admin = Auth::guard('admin')->user();

            // Update deposit status
            $deposit->status = 'failed';
            
            // Store admin note in response_data
            $responseData = $deposit->response_data ?? [];
            if ($request->admin_note) {
                $responseData['admin_note'] = $request->admin_note;
                $responseData['rejected_by'] = $admin->id;
                $responseData['rejected_at'] = now()->toDateTimeString();
            }
            $deposit->response_data = $responseData;
            $deposit->save();

            DB::commit();

            Log::info('Deposit rejected', [
                'deposit_id' => $deposit->id,
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
                'amount' => $deposit->amount,
                'user_id' => $deposit->user_id
            ]);

            return redirect()->route('admin.deposits.index')
                ->with('success', 'Deposit rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Deposit rejection failed', [
                'deposit_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.deposits.index')
                ->with('error', 'Failed to reject deposit: ' . $e->getMessage());
        }
    }
}


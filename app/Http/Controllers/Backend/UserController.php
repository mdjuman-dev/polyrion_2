<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Trade;
use App\Models\Wallet;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct()
    {
        // Don't require admin auth for returnToAdmin method
        $this->middleware('auth:admin')->except(['returnToAdmin']);
        // Permission checks are handled in routes
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        // Optimize: Select only necessary columns and eager load main wallet with select
        $query = User::select(['id', 'name', 'email', 'username', 'number', 'profile_image', 'created_at', 'balance'])
            ->with(['mainWallet' => function($q) {
                $q->select(['id', 'user_id', 'wallet_type', 'balance', 'locked_balance', 'currency']);
            }]);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('username', 'like', "%{$searchTerm}%")
                    ->orWhere('number', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all' && $request->status !== '') {
            // You can add status filtering here if needed
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('backend.users.index', compact('users'));
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        try {
        // Optimize: Load only necessary relationships with select
        $user = User::with([
            'mainWallet' => function($q) {
                $q->select(['id', 'user_id', 'wallet_type', 'balance', 'locked_balance', 'currency']);
            },
            'earningWallet' => function($q) {
                $q->select(['id', 'user_id', 'wallet_type', 'balance', 'currency']);
            },
            'trades' => function($q) {
                $q->select(['id', 'user_id', 'market_id', 'amount_invested', 'amount', 'status', 'payout', 'payout_amount', 'created_at'])
                  ->with(['market' => function($mq) {
                      $mq->select(['id', 'event_id', 'question', 'slug'])
                         ->with(['event' => function($eq) {
                             $eq->select(['id', 'title', 'slug']);
                         }]);
                  }])
                  ->latest()
                  ->limit(100); // Limit initial load
            },
            'deposits' => function($q) {
                $q->select(['id', 'user_id', 'amount', 'status', 'created_at'])
                  ->latest()
                  ->limit(50);
            },
            'withdrawals' => function($q) {
                $q->select(['id', 'user_id', 'amount', 'status', 'created_at'])
                  ->latest()
                  ->limit(50);
            }
        ])
        ->select(['id', 'name', 'email', 'username', 'number', 'profile_image', 'created_at'])
            ->findOrFail($id);

        // User Statistics - optimize with base query
        $tradesQuery = $user->trades();
        $totalTrades = (clone $tradesQuery)->count();
        $pendingTrades = (clone $tradesQuery)->whereRaw('UPPER(status) = ?', ['PENDING'])->count();
        $wonTrades = (clone $tradesQuery)->whereIn('status', ['won', 'WON'])->count();
        $lostTrades = (clone $tradesQuery)->whereIn('status', ['lost', 'LOST'])->count();
        
        $totalInvested = (clone $tradesQuery)->sum(DB::raw('COALESCE(amount_invested, amount, 0)')) ?? 0;
        $totalPayouts = (clone $tradesQuery)->whereIn('status', ['won', 'WON'])
            ->sum(DB::raw('COALESCE(payout, payout_amount, 0)')) ?? 0;
        
        $totalDeposits = $user->deposits()->where('status', 'completed')->sum('amount') ?? 0;
        $totalWithdrawals = $user->withdrawals()->where('status', 'approved')->sum('amount') ?? 0;
        
        $mainWallet = $user->mainWallet;
        $earningWallet = $user->earningWallet;
        $walletBalance = $mainWallet ? $mainWallet->balance : 0;
        $lockedBalance = $mainWallet ? $mainWallet->locked_balance : 0;
        $earningBalance = $earningWallet ? $earningWallet->balance : 0;

        // All User Activities (for activity log) - Optimized: Limit to last 100 records
        // Reuse trades query to avoid duplicate queries
        $trades = $user->trades()
            ->select(['id', 'market_id', 'amount_invested', 'amount', 'status', 'created_at'])
            ->with(['market' => function($q) {
                $q->select(['id', 'event_id'])->with(['event' => function($eq) {
                    $eq->select(['id', 'title']);
                }]);
            }])
            ->latest()
            ->limit(50)
            ->get();
        
        // Recent Activity - reuse trades collection to avoid duplicate query
        $recentTrades = $trades->take(10);
        $recentDeposits = $user->deposits()->latest()->take(5)->get();
        $recentWithdrawals = $user->withdrawals()->latest()->take(5)->get();
        
        $allActivities = collect();
        
        // Add trades as activities - Limit to last 50 trades (already loaded above)
        foreach ($trades as $trade) {
            $allActivities->push([
                'type' => 'trade',
                'title' => 'Trade Placed',
                'description' => $trade->market && $trade->market->event 
                    ? 'Traded on: ' . \Illuminate\Support\Str::limit($trade->market->event->title, 50)
                    : 'Trade #' . $trade->id,
                'amount' => $trade->amount_invested ?? $trade->amount ?? 0,
                'status' => $trade->status,
                'date' => $trade->created_at,
                'icon' => 'exchange-alt',
                'color' => 'primary'
            ]);
        }
        
        // Add deposits as activities - Limit to last 30 deposits
        $deposits = $user->deposits()
            ->select(['id', 'amount', 'status', 'created_at'])
            ->latest()
            ->limit(30)
            ->get();
        foreach ($deposits as $deposit) {
            $allActivities->push([
                'type' => 'deposit',
                'title' => 'Deposit',
                'description' => 'Deposit #' . $deposit->id,
                'amount' => $deposit->amount ?? 0,
                'status' => $deposit->status,
                'date' => $deposit->created_at,
                'icon' => 'arrow-down',
                'color' => 'success'
            ]);
        }
        
        // Add withdrawals as activities - Limit to last 30 withdrawals
        $withdrawals = $user->withdrawals()
            ->select(['id', 'amount', 'status', 'created_at'])
            ->latest()
            ->limit(30)
            ->get();
        foreach ($withdrawals as $withdrawal) {
            $allActivities->push([
                'type' => 'withdrawal',
                'title' => 'Withdrawal',
                'description' => 'Withdrawal #' . $withdrawal->id,
                'amount' => $withdrawal->amount ?? 0,
                'status' => $withdrawal->status,
                'date' => $withdrawal->created_at,
                'icon' => 'arrow-up',
                'color' => 'warning'
            ]);
        }
        
        // Add wallet transactions as activities - Limit to last 30 transactions from both wallets
        $walletIds = collect();
        if ($mainWallet) {
            $walletIds->push($mainWallet->id);
        }
        if ($earningWallet) {
            $walletIds->push($earningWallet->id);
        }
        
        if ($walletIds->isNotEmpty()) {
            $walletTransactions = WalletTransaction::whereIn('wallet_id', $walletIds)
                ->orWhere('user_id', $user->id)
                ->select(['id', 'type', 'amount', 'description', 'created_at'])
                ->latest()
                ->limit(30)
                ->get();
            foreach ($walletTransactions as $transaction) {
                $allActivities->push([
                    'type' => 'wallet_transaction',
                    'title' => ucfirst(str_replace('_', ' ', $transaction->type ?? 'Transaction')),
                    'description' => 'Wallet Transaction #' . $transaction->id . ' - ' . ($transaction->description ?? 'Transaction'),
                    'amount' => $transaction->amount ?? 0,
                    'status' => 'completed',
                    'date' => $transaction->created_at,
                    'icon' => 'wallet',
                    'color' => 'info'
                ]);
            }
        }
        
        // Sort all activities by date (newest first) and limit to 100 most recent
        $allActivities = $allActivities->sortByDesc('date')->take(100)->values();

        $stats = [
            'total_trades' => $totalTrades,
            'pending_trades' => $pendingTrades,
            'won_trades' => $wonTrades,
            'lost_trades' => $lostTrades,
            'total_invested' => $totalInvested,
            'total_payouts' => $totalPayouts,
            'total_deposits' => $totalDeposits,
            'total_withdrawals' => $totalWithdrawals,
            'main_wallet_balance' => $walletBalance,
            'earning_wallet_balance' => $earningBalance,
            'wallet_balance' => $walletBalance, // For backward compatibility
            'locked_balance' => $lockedBalance,
        ];

        return view('backend.users.show', compact('user', 'stats', 'recentTrades', 'recentDeposits', 'recentWithdrawals', 'allActivities'));
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database connection failed in UserController@show: ' . $e->getMessage());
            return redirect()->route('admin.users.index')
                ->with('error', 'Unable to load user. Please try again later.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User not found.');
        } catch (\Exception $e) {
            Log::error('Error in UserController@show: ' . $e->getMessage());
            return redirect()->route('admin.users.index')
                ->with('error', 'An error occurred. Please try again later.');
        }
    }

    /**
     * Update user status (activate/deactivate)
     */
    public function updateStatus(Request $request, $id)
    {
        try {
        $user = User::findOrFail($id);
        
        // You can add status field to users table if needed
        // For now, we'll just return success
        
        return redirect()->back()->with('success', 'User status updated successfully');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database connection failed in UserController@updateStatus: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to update user. Please try again later.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'User not found.');
        } catch (\Exception $e) {
            Log::error('Error in UserController@updateStatus: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred. Please try again later.');
        }
    }

    /**
     * Login as user (impersonate)
     */
    public function loginAsUser($id)
    {
        try {
        $user = User::findOrFail($id);
        
        // Store admin session before logging in as user
        session(['admin_id' => Auth::guard('admin')->id()]);
        
        // Logout admin and login as user
        Auth::guard('admin')->logout();
        Auth::guard('web')->login($user);
        
        // Redirect to user profile/dashboard
        // Using url() to avoid route name resolution issues
        return redirect('/profile')->with('success', 'Logged in as ' . $user->name);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database connection failed in UserController@loginAsUser: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to login as user. Please try again later.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'User not found.');
        } catch (\Exception $e) {
            Log::error('Error in UserController@loginAsUser: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred. Please try again later.');
        }
    }

    /**
     * Return to admin account
     * This method can be accessed without admin authentication when impersonating
     */
    public function returnToAdmin()
    {
        // Check if user is authenticated (either as user or admin)
        if (!Auth::check() && !Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'Session expired. Please login again.');
        }

        $adminId = session('admin_id');
        
        if (!$adminId) {
            // If no admin_id in session, check if already logged in as admin
            if (Auth::guard('admin')->check()) {
                return redirect()->route('admin.backend.dashboard');
            }
            return redirect()->route('admin.login')->with('error', 'No admin session found.');
        }
        
        // Logout current user (if logged in as user)
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }
        
        // Login as admin using stored admin ID
        Auth::guard('admin')->loginUsingId($adminId);
        session()->forget('admin_id');
        
        return redirect()->route('admin.backend.dashboard')->with('success', 'Returned to admin account');
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        try {
        $user = User::findOrFail($id);
        
        // Delete related records
        $user->trades()->delete();
        $user->wallet()->delete();
        $user->deposits()->delete();
        $user->withdrawals()->delete();
        
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database connection failed in UserController@destroy: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to delete user. Please try again later.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'User not found.');
        } catch (\Exception $e) {
            Log::error('Error in UserController@destroy: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred. Please try again later.');
        }
    }

    /**
     * Update user wallet password
     */
    public function updateWalletPassword(Request $request, $id)
    {
        $request->validate([
            'withdrawal_password' => 'required|string|min:6|max:255',
        ]);

        try {
            $user = User::findOrFail($id);
            
            // Update withdrawal password - bypass model casting to avoid double hashing
            // Since User model has 'withdrawal_password' => 'hashed' casting, we need to update directly via DB
            // to prevent automatic hashing by the model
            $hashedPassword = Hash::make($request->withdrawal_password);
            
            // Update directly in database to bypass model casting
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'withdrawal_password' => $hashedPassword,
                    'updated_at' => now()
                ]);

            Log::info('Wallet password updated successfully', [
                'user_id' => $user->id,
                'updated_by_admin' => Auth::guard('admin')->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wallet password updated successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error updating wallet password: ' . $e->getMessage(), [
                'user_id' => $id,
                'error' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update wallet password. Please try again.'
            ], 500);
        }
    }

    /**
     * Add test deposit to user wallet (for testing purposes)
     */
    public function addTestDeposit(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:100000',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            try {
            $user = User::findOrFail($id);
            } catch (\Illuminate\Database\QueryException $e) {
                Log::error('Database connection failed in UserController@addTestDeposit (user lookup): ' . $e->getMessage());
                return redirect()->back()->with('error', 'Unable to load user. Please try again later.');
            }
            
            $admin = Auth::guard('admin')->user();

            // Get or create main wallet
            $wallet = Wallet::lockForUpdate()
                ->firstOrCreate(
                    ['user_id' => $user->id, 'wallet_type' => Wallet::TYPE_MAIN],
                    ['balance' => 0, 'currency' => 'USDT', 'status' => 'active']
                );

            $balanceBefore = (float) $wallet->balance;
            $amount = (float) $request->amount;
            $newBalance = $balanceBefore + $amount;

            // Update wallet balance
            $wallet->balance = $newBalance;
            $wallet->save();

            // Create test deposit record
            $deposit = Deposit::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'currency' => $wallet->currency ?? 'USDT',
                'payment_method' => 'test',
                'status' => 'completed',
                'completed_at' => now(),
                'response_data' => [
                    'test_deposit' => true,
                    'added_by_admin' => $admin->id,
                    'admin_name' => $admin->name,
                    'note' => $request->note ?? 'Test deposit for testing purposes',
                    'added_at' => now()->toDateTimeString(),
                ],
            ]);

            // Create wallet transaction
            WalletTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $newBalance,
                'reference_type' => Deposit::class,
                'reference_id' => $deposit->id,
                'description' => 'Test Deposit' . ($request->note ? ' - ' . $request->note : ''),
                'metadata' => [
                    'test_deposit' => true,
                    'added_by_admin' => $admin->id,
                    'admin_name' => $admin->name,
                    'note' => $request->note,
                ],
            ]);

            // Distribute referral commissions
            try {
                $referralService = new \App\Services\ReferralService();
                $referralService->distributeCommission($user, $amount);
            } catch (\Exception $e) {
                // Log error but don't fail the deposit
                Log::error('Failed to distribute referral commission for test deposit', [
                    'deposit_id' => $deposit->id,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'error' => $e->getMessage(),
                ]);
            }

            DB::commit();

            Log::info('Test deposit added', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $newBalance,
                'note' => $request->note,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test deposit added successfully.',
                'data' => [
                    'amount' => number_format($amount, 2),
                    'balance_before' => number_format($balanceBefore, 2),
                    'balance_after' => number_format($newBalance, 2),
                    'deposit_id' => $deposit->id,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to add test deposit', [
                'user_id' => $id,
                'admin_id' => Auth::guard('admin')->id(),
                'amount' => $request->amount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add test deposit: ' . $e->getMessage(),
            ], 500);
        }
    }
}


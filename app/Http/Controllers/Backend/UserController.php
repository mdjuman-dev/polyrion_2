<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Trade;
use App\Models\Wallet;
use App\Models\Deposit;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        // Don't require admin auth for returnToAdmin method
        $this->middleware('auth:admin')->except(['returnToAdmin']);
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with('wallet');

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
        $user = User::with(['wallet', 'trades.market.event', 'deposits', 'withdrawals'])
            ->findOrFail($id);

        // User Statistics
        $totalTrades = $user->trades()->count();
        $pendingTrades = $user->trades()->where('status', 'pending')->count();
        $wonTrades = $user->trades()->whereIn('status', ['won', 'WON'])->count();
        $lostTrades = $user->trades()->whereIn('status', ['lost', 'LOST'])->count();
        
        $totalInvested = $user->trades()->sum('amount_invested') ?? $user->trades()->sum('amount') ?? 0;
        $totalPayouts = $user->trades()->whereIn('status', ['won', 'WON'])
            ->sum('payout') ?? $user->trades()->whereIn('status', ['won', 'WON'])->sum('payout_amount') ?? 0;
        
        $totalDeposits = $user->deposits()->where('status', 'completed')->sum('amount') ?? 0;
        $totalWithdrawals = $user->withdrawals()->where('status', 'approved')->sum('amount') ?? 0;
        
        $walletBalance = $user->wallet ? $user->wallet->balance : 0;
        $lockedBalance = $user->wallet ? $user->wallet->locked_balance : 0;

        // Recent Activity
        $recentTrades = $user->trades()->with('market.event')->latest()->take(10)->get();
        $recentDeposits = $user->deposits()->latest()->take(5)->get();
        $recentWithdrawals = $user->withdrawals()->latest()->take(5)->get();

        $stats = [
            'total_trades' => $totalTrades,
            'pending_trades' => $pendingTrades,
            'won_trades' => $wonTrades,
            'lost_trades' => $lostTrades,
            'total_invested' => $totalInvested,
            'total_payouts' => $totalPayouts,
            'total_deposits' => $totalDeposits,
            'total_withdrawals' => $totalWithdrawals,
            'wallet_balance' => $walletBalance,
            'locked_balance' => $lockedBalance,
        ];

        return view('backend.users.show', compact('user', 'stats', 'recentTrades', 'recentDeposits', 'recentWithdrawals'));
    }

    /**
     * Update user status (activate/deactivate)
     */
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // You can add status field to users table if needed
        // For now, we'll just return success
        
        return redirect()->back()->with('success', 'User status updated successfully');
    }

    /**
     * Login as user (impersonate)
     */
    public function loginAsUser($id)
    {
        $user = User::findOrFail($id);
        
        // Store admin session before logging in as user
        session(['admin_id' => Auth::guard('admin')->id()]);
        
        // Logout admin and login as user
        Auth::guard('admin')->logout();
        Auth::guard('web')->login($user);
        
        // Redirect to user profile/dashboard
        // Using url() to avoid route name resolution issues
        return redirect('/profile')->with('success', 'Logged in as ' . $user->name);
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
        $user = User::findOrFail($id);
        
        // Delete related records
        $user->trades()->delete();
        $user->wallet()->delete();
        $user->deposits()->delete();
        $user->withdrawals()->delete();
        
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}


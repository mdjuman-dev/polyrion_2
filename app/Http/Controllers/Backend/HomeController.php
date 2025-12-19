<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\Market;
use App\Models\Trade;
use App\Models\Withdrawal;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view dashboard,admin');
    }

    function dashboard()
    {
        // Total Statistics
        $totalUsers = User::count();
        $totalEvents = Event::count();
        $totalMarkets = Market::count();
        $totalTrades = Trade::count();
        
        // Active Statistics - optimize with composite index usage
        $activeEvents = Event::where('active', true)->where('closed', false)->count();
        $activeMarkets = Market::where('active', true)->where('closed', false)->count();
        $pendingTrades = Trade::whereRaw('UPPER(status) = ?', ['PENDING'])->count();
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();
        
        // Financial Statistics
        $totalVolume = Trade::sum('amount_invested') ?? Trade::sum('amount') ?? 0;
        $totalPayouts = Trade::whereIn('status', ['won', 'WON'])->sum('payout') ?? Trade::whereIn('status', ['won', 'WON'])->sum('payout_amount') ?? 0;
        $totalWalletBalance = Wallet::sum('balance') ?? 0;
        // Calculate total locked balance (money in pending trades)
        $totalLockedBalance = Wallet::sum('locked_balance') ?? 0;
        
        // Recent Activity
        $recentEvents = Event::latest()->take(5)->get();
        $recentTrades = Trade::with(['user', 'market.event'])->latest()->take(10)->get();
        $recentWithdrawals = Withdrawal::with('user')->where('status', 'pending')->latest()->take(5)->get();
        
        // User Growth (Last 30 days)
        $userGrowth = User::where('created_at', '>=', now()->subDays(30))->count();
        $userGrowthPercentage = $totalUsers > 0 ? round(($userGrowth / $totalUsers) * 100, 2) : 0;
        
        // Trade Volume (Last 7 days)
        $volumeLast7Days = Trade::where('created_at', '>=', now()->subDays(7))
            ->sum('amount_invested') ?? Trade::where('created_at', '>=', now()->subDays(7))->sum('amount') ?? 0;
        
        // Top Markets by Volume
        $topMarkets = Market::with('event')
            ->orderBy('volume', 'desc')
            ->take(5)
            ->get();
        
        // Statistics for cards
        $stats = [
            'total_users' => $totalUsers,
            'total_events' => $totalEvents,
            'total_markets' => $totalMarkets,
            'total_trades' => $totalTrades,
            'active_events' => $activeEvents,
            'active_markets' => $activeMarkets,
            'pending_trades' => $pendingTrades,
            'pending_withdrawals' => $pendingWithdrawals,
            'total_volume' => $totalVolume,
            'total_payouts' => $totalPayouts,
            'total_wallet_balance' => $totalWalletBalance,
            'total_locked_balance' => $totalLockedBalance,
            'user_growth' => $userGrowth,
            'user_growth_percentage' => $userGrowthPercentage,
            'volume_last_7_days' => $volumeLast7Days,
        ];
        
        return view('backend.dashboard', compact(
            'stats',
            'recentEvents',
            'recentTrades',
            'recentWithdrawals',
            'topMarkets'
        ));
    }

    /**
     * Search for users and events
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (empty($query)) {
            return redirect()->back()
                ->with('error', 'Please enter a search term.');
        }

        // First, search for event by title (prioritize events)
        $event = Event::where('title', 'like', "%{$query}%")
            ->orWhere('slug', 'like', "%{$query}%")
            ->first();

        if ($event) {
            return redirect()->route('admin.events.show', $event->id)
                ->with('success', 'Event found!');
        }

        // If no event found, search for user by name, email, or username
        $user = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('username', 'like', "%{$query}%")
            ->first();

        if ($user) {
            // Redirect to dashboard with user info
            // You can create a user details page later and redirect there
            return redirect()->route('admin.backend.dashboard')
                ->with('search_result', [
                    'type' => 'user',
                    'user' => $user,
                    'message' => "User found: {$user->name} ({$user->email})"
                ]);
        }

        // No results found
        return redirect()->back()
            ->with('error', 'No user or event found with that search term.');
    }

    /**
     * Clear all caches
     */
    public function clearCache(Request $request)
    {
        try {
            // Clear application cache
            \Artisan::call('cache:clear');
            
            // Clear config cache
            \Artisan::call('config:clear');
            
            // Clear route cache
            \Artisan::call('route:clear');
            
            // Clear view cache
            \Artisan::call('view:clear');
            
            // Clear permission cache
            if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                \Artisan::call('permission:cache-reset');
            }
            
            // Clear compiled files
            \Artisan::call('clear-compiled');
            
            // If AJAX request, return JSON response
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'All caches cleared successfully!'
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'All caches cleared successfully!');
        } catch (\Exception $e) {
            // If AJAX request, return JSON response
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to clear cache: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }
}

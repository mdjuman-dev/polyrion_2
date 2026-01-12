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

    function dashboard(Request $request)
    {
        // Get date range from request (default: 30 days)
        $days = (int) $request->get('days', 30);
        $days = in_array($days, [7, 30, 60, 90]) ? $days : 30;
        
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
        
        // Recent Activity - Optimize with select
        $recentEvents = Event::select(['id', 'title', 'slug', 'volume', 'created_at'])
            ->latest()
            ->take(5)
            ->get();
        $recentTrades = Trade::select([
            'id', 'user_id', 'market_id', 'amount_invested', 'amount', 'status', 'created_at'
        ])
        ->with([
            'user' => function($q) {
                $q->select(['id', 'name', 'email']);
            },
            'market' => function($q) {
                $q->select(['id', 'event_id', 'question', 'slug'])
                  ->with(['event' => function($eq) {
                      $eq->select(['id', 'title', 'slug']);
                  }]);
            }
        ])
        ->latest()
        ->take(10)
        ->get();
        $recentWithdrawals = Withdrawal::select(['id', 'user_id', 'amount', 'status', 'created_at'])
            ->with(['user' => function($q) {
                $q->select(['id', 'name', 'email']);
            }])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();
        
        // User Growth (Last 30 days)
        $userGrowth = User::where('created_at', '>=', now()->subDays(30))->count();
        $userGrowthPercentage = $totalUsers > 0 ? round(($userGrowth / $totalUsers) * 100, 2) : 0;
        
        // Trade Volume (Last 7 days)
        $volumeLast7Days = Trade::where('created_at', '>=', now()->subDays(7))
            ->sum('amount_invested') ?? Trade::where('created_at', '>=', now()->subDays(7))->sum('amount') ?? 0;
        
        // Top Markets by Volume - Optimize with select
        $topMarkets = Market::select(['id', 'event_id', 'question', 'slug', 'volume', 'created_at'])
            ->with(['event' => function($q) {
                $q->select(['id', 'title', 'slug']);
            }])
            ->orderBy('volume', 'desc')
            ->take(5)
            ->get();
        
        // Chart Data for selected days
        $chartData = $this->getChartData($days);
        
        // Mini chart data for each card (last 7 days for sparklines)
        $miniChartData = $this->getMiniChartData();
        
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
            'topMarkets',
            'chartData',
            'miniChartData',
            'days'
        ));
    }

    /**
     * Get mini chart data for card sparklines (last 7 days)
     */
    private function getMiniChartData()
    {
        $startDate = now()->subDays(7)->startOfDay();
        $endDate = now()->endOfDay();
        
        $labels = [];
        $usersData = [];
        $eventsData = [];
        $marketsData = [];
        $tradesData = [];
        $volumeData = [];
        $payoutsData = [];
        $walletData = [];
        $withdrawalsData = [];
        
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $periodStart = clone $currentDate;
            $periodEnd = (clone $currentDate)->endOfDay();
            
            $labels[] = $periodStart->format('M d');
            
            // Users
            $usersData[] = User::whereBetween('created_at', [$periodStart, $periodEnd])->count();
            
            // Events
            $eventsData[] = Event::whereBetween('created_at', [$periodStart, $periodEnd])->count();
            
            // Markets
            $marketsData[] = Market::whereBetween('created_at', [$periodStart, $periodEnd])->count();
            
            // Trades
            $tradesData[] = Trade::whereBetween('created_at', [$periodStart, $periodEnd])->count();
            
            // Volume
            $volume = Trade::whereBetween('created_at', [$periodStart, $periodEnd])
                ->sum(DB::raw('COALESCE(amount_invested, amount, 0)'));
            $volumeData[] = round($volume, 2);
            
            // Payouts
            $payouts = Trade::whereBetween('created_at', [$periodStart, $periodEnd])
                ->whereIn('status', ['won', 'WON'])
                ->sum(DB::raw('COALESCE(payout, payout_amount, 0)'));
            $payoutsData[] = round($payouts, 2);
            
            // Wallet balance (cumulative)
            $walletData[] = round(Wallet::where('created_at', '<=', $periodEnd)->sum('balance'), 2);
            
            // Withdrawals
            $withdrawalsData[] = Withdrawal::whereBetween('created_at', [$periodStart, $periodEnd])
                ->where('status', 'pending')
                ->count();
            
            $currentDate->addDay();
        }
        
        return [
            'labels' => $labels,
            'users' => $usersData,
            'events' => $eventsData,
            'markets' => $marketsData,
            'trades' => $tradesData,
            'volume' => $volumeData,
            'payouts' => $payoutsData,
            'wallet' => $walletData,
            'withdrawals' => $withdrawalsData,
        ];
    }

    /**
     * Get chart data for specified number of days
     */
    private function getChartData($days)
    {
        $startDate = now()->subDays($days)->startOfDay();
        $endDate = now()->endOfDay();
        
        // Determine interval and format based on days
        if ($days <= 7) {
            $intervalDays = 1;
            $format = 'M d';
        } elseif ($days <= 30) {
            $intervalDays = 1;
            $format = 'M d';
        } elseif ($days <= 60) {
            $intervalDays = 2;
            $format = 'M d';
        } else {
            $intervalDays = 3;
            $format = 'M d';
        }
        
        // Generate date labels and data points
        $labels = [];
        $usersData = [];
        $tradesData = [];
        $volumeData = [];
        $revenueData = [];
        
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $periodStart = clone $currentDate;
            $periodEnd = (clone $currentDate)->addDays($intervalDays)->subSecond();
            
            // Ensure we don't go beyond endDate
            if ($periodEnd > $endDate) {
                $periodEnd = clone $endDate;
            }
            
            // Add label
            $labels[] = $periodStart->format($format);
            
            // Get data for this period
            $usersData[] = User::whereBetween('created_at', [$periodStart, $periodEnd])->count();
            
            $tradesData[] = Trade::whereBetween('created_at', [$periodStart, $periodEnd])->count();
            
            $volume = Trade::whereBetween('created_at', [$periodStart, $periodEnd])
                ->sum(DB::raw('COALESCE(amount_invested, amount, 0)'));
            $volumeData[] = round($volume, 2);
            
            $revenue = Trade::whereBetween('created_at', [$periodStart, $periodEnd])
                ->whereIn('status', ['won', 'WON'])
                ->sum(DB::raw('COALESCE(payout, payout_amount, 0)'));
            $revenueData[] = round($revenue, 2);
            
            // Move to next period
            $currentDate->addDays($intervalDays);
        }
        
        return [
            'labels' => $labels,
            'users' => $usersData,
            'trades' => $tradesData,
            'volume' => $volumeData,
            'revenue' => $revenueData,
        ];
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

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Market;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    /**
     * Display finance page with filters and events
     */
    public function index(Request $request)
    {
        // Get selected filters from query parameters
        $selectedTimeframe = $request->get('timeframe', 'all');
        $selectedCategory = $request->get('category', 'all');
        $selectedSecondaryCategory = $request->get('secondary_category', null);

        // Base query for finance events - Exclude ended events (reused for counts)
        $baseQuery = Event::whereIn('category', ['Finance', 'Economy', 'Business'])
            ->where('active', true)
            ->where('closed', false)
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
            });

        // Get secondary categories for Finance, Economy, and Business
        $secondaryCategories = \App\Models\SecondaryCategory::active()
            ->whereIn('main_category', ['Finance', 'Economy', 'Business'])
            ->ordered()
            ->withCount('activeEvents')
            ->get();

        // Get events filtered by finance category - Exclude ended events
        $eventsQuery = (clone $baseQuery)
            ->with(['markets' => function ($query) {
                $query->select([
                    'id', 'event_id', 'question', 'slug', 'groupItem_title',
                    'outcome_prices', 'outcomes', 'active', 'closed',
                    'best_ask', 'best_bid', 'last_trade_price',
                    'close_time', 'end_date', 'volume24hr', 'final_result',
                    'outcome_result', 'final_outcome', 'created_at'
                ])
                ->where('active', true)
                ->where('closed', false)
                ->orderBy('created_at', 'desc')
                ->limit(10);
            }])
            ->orderBy('created_at', 'desc');

        // Filter by timeframe if selected
        if ($selectedTimeframe !== 'all') {
            $eventsQuery = $this->filterByTimeframe($eventsQuery, $selectedTimeframe);
        }

        // Filter by secondary category if selected
        if ($selectedSecondaryCategory) {
            $eventsQuery->where('secondary_category_id', $selectedSecondaryCategory);
        }

        // Filter by category if selected
        if ($selectedCategory !== 'all') {
            $categoryKeywords = $this->getCategoryKeywords($selectedCategory);
            $eventsQuery->where(function ($query) use ($categoryKeywords) {
                foreach ($categoryKeywords as $keyword) {
                    $query->orWhere('title', 'LIKE', '%' . $keyword . '%');
                }
            });

            $eventsQuery->whereHas('markets', function ($query) use ($categoryKeywords) {
                foreach ($categoryKeywords as $keyword) {
                    $query->orWhere('question', 'LIKE', '%' . $keyword . '%');
                }
            });
        }

        $events = $eventsQuery->paginate(20);

        // Get filter counts using database queries (more efficient than collection filtering)
        $timeframeCounts = $this->getTimeframeCounts($baseQuery);
        $categoryCounts = $this->getCategoryCounts($baseQuery);

        return view('frontend.finance', compact(
            'selectedTimeframe',
            'selectedCategory',
            'events',
            'timeframeCounts',
            'categoryCounts',
            'secondaryCategories',
            'selectedSecondaryCategory'
        ));
    }

    /**
     * Filter events by timeframe
     */
    private function filterByTimeframe($query, $timeframe)
    {
        $now = now();

        switch ($timeframe) {
            case 'daily':
                $query->where('created_at', '>=', $now->copy()->subDay());
                break;
            case 'weekly':
                $query->where('created_at', '>=', $now->copy()->subWeek());
                break;
            case 'monthly':
                $query->where('created_at', '>=', $now->copy()->subMonth());
                break;
        }

        return $query;
    }

    /**
     * Get timeframe counts using database queries (more efficient)
     */
    private function getTimeframeCounts($baseQuery)
    {
        $now = now();
        
        // Cache counts for 1 minute to avoid duplicate queries
        $cacheKey = 'finance_timeframe_counts';
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function () use ($baseQuery, $now) {
            return [
                'all' => (clone $baseQuery)->count(),
                'daily' => (clone $baseQuery)->where('created_at', '>=', $now->copy()->subDay())->count(),
                'weekly' => (clone $baseQuery)->where('created_at', '>=', $now->copy()->subWeek())->count(),
                'monthly' => (clone $baseQuery)->where('created_at', '>=', $now->copy()->subMonth())->count(),
        ];
        });
    }

    /**
     * Get category counts using database queries (more efficient)
     */
    private function getCategoryCounts($baseQuery)
    {
        $categories = [
            'Stocks' => ['stock', 'nvidia', 'apple', 'alphabet', 'microsoft', 'amazon', 'netflix', 'nflx', 'aapl', 'msft', 'googl', 'amzn'],
            'Earnings' => ['earnings', 'earnings call', 'quarterly earnings', 'q1', 'q2', 'q3', 'q4'],
            'Indices' => ['s&p 500', 'sp500', 'sp 500', 'dow jones', 'nasdaq', 'index', 'indices'],
            'Commodities' => ['gold', 'silver', 'oil', 'crude', 'commodity', 'commodities'],
            'Forex' => ['forex', 'currency', 'usd', 'eur', 'gbp', 'jpy', 'exchange rate'],
            'Acquisitions' => ['acquisition', 'merger', 'takeover', 'buyout', 'acquire'],
            'Earnings Calls' => ['earnings call', 'earnings conference', 'quarterly call'],
            'IPOs' => ['ipo', 'initial public offering', 'goes public', 'public offering'],
            'Fed Rates' => ['fed rate', 'federal reserve', 'fed decision', 'interest rate', 'rate cut', 'rate hike', 'bps'],
            'Prediction Markets' => ['prediction market', 'forecast', 'prediction'],
            'Treasuries' => ['treasury', 'treasuries', 'bond', 't-bill', 't-bond'],
        ];

        // Cache counts for 1 minute to avoid duplicate queries
        $cacheKey = 'finance_category_counts';
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function () use ($baseQuery, $categories) {
        $counts = [];

        foreach ($categories as $categoryName => $keywords) {
                $query = (clone $baseQuery)->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                        $q->orWhere('title', 'LIKE', '%' . $keyword . '%');
                    }
                })->orWhereHas('markets', function ($mq) use ($keywords) {
                    $mq->where(function ($q) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            $q->orWhere('question', 'LIKE', '%' . $keyword . '%');
                            }
                    });
                });

            $counts[] = [
                'name' => $categoryName,
                'slug' => strtolower(str_replace(' ', '-', $categoryName)),
                    'count' => $query->count(),
                'icon' => $this->getCategoryIcon($categoryName),
            ];
        }

        return $counts;
        });
    }

    /**
     * Get category icon
     */
    private function getCategoryIcon($categoryName)
    {
        $icons = [
            'Stocks' => 'fas fa-chart-line',
            'Earnings' => 'fas fa-dollar-sign',
            'Indices' => 'fas fa-chart-bar',
            'Commodities' => 'fas fa-gem',
            'Forex' => 'fas fa-exchange-alt',
            'Acquisitions' => 'fas fa-handshake',
            'Earnings Calls' => 'fas fa-phone',
            'IPOs' => 'fas fa-rocket',
            'Fed Rates' => 'fas fa-landmark',
            'Prediction Markets' => 'fas fa-crystal-ball',
            'Treasuries' => 'fas fa-coins',
        ];

        return $icons[$categoryName] ?? 'fas fa-circle';
    }

    /**
     * Get keywords for a category
     */
    private function getCategoryKeywords($category)
    {
        $keywords = [
            'stocks' => ['stock', 'nvidia', 'apple', 'alphabet', 'microsoft', 'amazon', 'netflix', 'nflx', 'aapl', 'msft', 'googl', 'amzn'],
            'earnings' => ['earnings', 'earnings call', 'quarterly earnings', 'q1', 'q2', 'q3', 'q4'],
            'indices' => ['s&p 500', 'sp500', 'sp 500', 'dow jones', 'nasdaq', 'index', 'indices'],
            'commodities' => ['gold', 'silver', 'oil', 'crude', 'commodity', 'commodities'],
            'forex' => ['forex', 'currency', 'usd', 'eur', 'gbp', 'jpy', 'exchange rate'],
            'acquisitions' => ['acquisition', 'merger', 'takeover', 'buyout', 'acquire'],
            'earnings-calls' => ['earnings call', 'earnings conference', 'quarterly call'],
            'ipos' => ['ipo', 'initial public offering', 'goes public', 'public offering'],
            'fed-rates' => ['federal reserve', 'fed decision', 'interest rate', 'rate cut', 'rate hike', 'bps'],
            'prediction-markets' => ['prediction market', 'forecast', 'prediction'],
            'treasuries' => ['treasury', 'treasuries', 'bond', 't-bill', 't-bond'],
        ];

        return $keywords[strtolower($category)] ?? [strtolower($category)];
    }
}

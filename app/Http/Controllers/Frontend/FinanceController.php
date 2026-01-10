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
        // Get all finance events
        $allFinanceEvents = Event::whereIn('category', ['Finance', 'Economy', 'Business'])
            ->where('active', true)
            ->where('closed', false)
            ->with('markets')
            ->get();

        // Get selected filters from query parameters
        $selectedTimeframe = $request->get('timeframe', 'all');
        $selectedCategory = $request->get('category', 'all');

        // Get events filtered by finance category
        $eventsQuery = Event::whereIn('category', ['Finance', 'Economy', 'Business'])
            ->where('active', true)
            ->where('closed', false)
            ->with(['markets' => function ($query) {
                $query->where('active', true)
                    ->orderBy('created_at', 'desc');
            }])
            ->orderBy('created_at', 'desc');

        // Filter by timeframe if selected
        if ($selectedTimeframe !== 'all') {
            $eventsQuery = $this->filterByTimeframe($eventsQuery, $selectedTimeframe);
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

        // Get filter counts
        $timeframeCounts = $this->getTimeframeCounts($allFinanceEvents);
        $categoryCounts = $this->getCategoryCounts($allFinanceEvents);

        return view('frontend.finance', compact(
            'selectedTimeframe',
            'selectedCategory',
            'events',
            'timeframeCounts',
            'categoryCounts'
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
     * Get timeframe counts
     */
    private function getTimeframeCounts($events)
    {
        $now = now();
        $counts = [
            'all' => $events->count(),
            'daily' => $events->filter(fn($e) => $e->created_at >= $now->copy()->subDay())->count(),
            'weekly' => $events->filter(fn($e) => $e->created_at >= $now->copy()->subWeek())->count(),
            'monthly' => $events->filter(fn($e) => $e->created_at >= $now->copy()->subMonth())->count(),
        ];

        return $counts;
    }

    /**
     * Get category counts
     */
    private function getCategoryCounts($events)
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

        $counts = [];

        foreach ($categories as $categoryName => $keywords) {
            $count = $events->filter(function ($event) use ($keywords) {
                $title = strtolower($event->title);
                $hasKeyword = false;

                foreach ($keywords as $keyword) {
                    if (strpos($title, $keyword) !== false) {
                        $hasKeyword = true;
                        break;
                    }
                }

                if (!$hasKeyword) {
                    foreach ($event->markets as $market) {
                        $question = strtolower($market->question ?? '');
                        foreach ($keywords as $keyword) {
                            if (strpos($question, $keyword) !== false) {
                                $hasKeyword = true;
                                break 2;
                            }
                        }
                    }
                }

                return $hasKeyword;
            })->count();

            $counts[] = [
                'name' => $categoryName,
                'slug' => strtolower(str_replace(' ', '-', $categoryName)),
                'count' => $count,
                'icon' => $this->getCategoryIcon($categoryName),
            ];
        }

        return $counts;
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

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Market;
use Illuminate\Http\Request;

class CryptoController extends Controller
{
    /**
     * Display crypto page with filters and events
     */
    public function index(Request $request)
    {
        // Get selected filters from query parameters
        $selectedTimeframe = $request->get('timeframe', 'all');
        $selectedAsset = $request->get('asset', 'all');
        $selectedSecondaryCategory = $request->get('secondary_category', null);

        // Base query for crypto events - Exclude ended events (reused for counts)
        $baseQuery = Event::where('category', 'Crypto')
            ->where('active', true)
            ->where('closed', false)
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
            });

        // Get secondary categories for Crypto
        $secondaryCategories = \App\Models\SecondaryCategory::active()
            ->byMainCategory('Crypto')
            ->ordered()
            ->withCount('activeEvents')
            ->get();

        // Get events filtered by crypto category - Exclude ended events
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

        // Filter by asset if selected
        if ($selectedAsset !== 'all') {
            $assetKeywords = $this->getAssetKeywords($selectedAsset);
            $eventsQuery->where(function ($query) use ($assetKeywords) {
                foreach ($assetKeywords as $keyword) {
                    $query->orWhere('title', 'LIKE', '%' . $keyword . '%');
                }
            });

            $eventsQuery->whereHas('markets', function ($query) use ($assetKeywords) {
                foreach ($assetKeywords as $keyword) {
                    $query->orWhere('question', 'LIKE', '%' . $keyword . '%');
                }
            });
        }

        $events = $eventsQuery->paginate(20);

        // Get filter counts using database queries (more efficient than collection filtering)
        $timeframeCounts = $this->getTimeframeCounts($baseQuery);
        $assetCounts = $this->getAssetCounts($baseQuery);

        return view('frontend.crypto', compact(
            'selectedTimeframe',
            'selectedAsset',
            'events',
            'timeframeCounts',
            'assetCounts',
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
            case '15m':
                $query->where('created_at', '>=', $now->copy()->subMinutes(15));
                break;
            case '1h':
            case 'hourly':
                $query->where('created_at', '>=', $now->copy()->subHour());
                break;
            case '4h':
                $query->where('created_at', '>=', $now->copy()->subHours(4));
                break;
            case 'daily':
                $query->where('created_at', '>=', $now->copy()->subDay());
                break;
            case 'weekly':
                $query->where('created_at', '>=', $now->copy()->subWeek());
                break;
            case 'monthly':
                $query->where('created_at', '>=', $now->copy()->subMonth());
                break;
            case 'pre-market':
                // Events that haven't started yet
                $query->where(function ($q) use ($now) {
                    $q->whereNull('start_date')
                      ->orWhere('start_date', '>', $now);
                });
                break;
            case 'etf':
                // Filter for ETF-related events
                $query->where(function ($q) {
                    $q->where('title', 'LIKE', '%etf%')
                      ->orWhere('title', 'LIKE', '%exchange traded fund%');
                });
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
        $cacheKey = 'crypto_timeframe_counts';
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function () use ($baseQuery, $now) {
            return [
                'all' => (clone $baseQuery)->count(),
                '15m' => (clone $baseQuery)->where('created_at', '>=', $now->copy()->subMinutes(15))->count(),
                'hourly' => (clone $baseQuery)->where('created_at', '>=', $now->copy()->subHour())->count(),
                '4h' => (clone $baseQuery)->where('created_at', '>=', $now->copy()->subHours(4))->count(),
                'daily' => (clone $baseQuery)->where('created_at', '>=', $now->copy()->subDay())->count(),
                'weekly' => (clone $baseQuery)->where('created_at', '>=', $now->copy()->subWeek())->count(),
                'monthly' => (clone $baseQuery)->where('created_at', '>=', $now->copy()->subMonth())->count(),
                'pre-market' => (clone $baseQuery)->where(function ($q) use ($now) {
                    $q->whereNull('start_date')->orWhere('start_date', '>', $now);
            })->count(),
                'etf' => (clone $baseQuery)->where(function ($q) {
                    $q->where('title', 'LIKE', '%etf%')
                      ->orWhere('title', 'LIKE', '%exchange traded fund%');
            })->count(),
        ];
        });
    }

    /**
     * Get asset counts using database queries (more efficient)
     */
    private function getAssetCounts($baseQuery)
    {
        $assets = [
            'Bitcoin' => ['bitcoin', 'btc'],
            'Ethereum' => ['ethereum', 'eth'],
            'Solana' => ['solana', 'sol'],
            'XRP' => ['xrp', 'ripple'],
            'Dogecoin' => ['dogecoin', 'doge'],
            'Microstrategy' => ['microstrategy', 'mstr'],
        ];

        // Cache counts for 1 minute to avoid duplicate queries
        $cacheKey = 'crypto_asset_counts';
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function () use ($baseQuery, $assets) {
        $counts = [];

        foreach ($assets as $assetName => $keywords) {
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
                'name' => $assetName,
                'slug' => strtolower($assetName),
                    'count' => $query->count(),
                'icon' => $this->getAssetIcon($assetName),
            ];
        }

        return $counts;
        });
    }

    /**
     * Get asset icon
     */
    private function getAssetIcon($assetName)
    {
        $icons = [
            'Bitcoin' => 'fab fa-bitcoin',
            'Ethereum' => 'fab fa-ethereum',
            'Solana' => 'fas fa-coins',
            'XRP' => 'fas fa-coins',
            'Dogecoin' => 'fas fa-dog',
            'Microstrategy' => 'fas fa-chart-line',
        ];

        return $icons[$assetName] ?? 'fas fa-coins';
    }

    /**
     * Get keywords for an asset
     */
    private function getAssetKeywords($asset)
    {
        $keywords = [
            'bitcoin' => ['bitcoin', 'btc'],
            'ethereum' => ['ethereum', 'eth'],
            'solana' => ['solana', 'sol'],
            'xrp' => ['xrp', 'ripple'],
            'dogecoin' => ['dogecoin', 'doge'],
            'microstrategy' => ['microstrategy', 'mstr'],
        ];

        return $keywords[strtolower($asset)] ?? [strtolower($asset)];
    }
}

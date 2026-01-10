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
        // Get all crypto events
        $allCryptoEvents = Event::where('category', 'Crypto')
            ->where('active', true)
            ->where('closed', false)
            ->with('markets')
            ->get();

        // Get selected filters from query parameters
        $selectedTimeframe = $request->get('timeframe', 'all');
        $selectedAsset = $request->get('asset', 'all');

        // Get events filtered by crypto category
        $eventsQuery = Event::where('category', 'Crypto')
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

        // Get filter counts
        $timeframeCounts = $this->getTimeframeCounts($allCryptoEvents);
        $assetCounts = $this->getAssetCounts($allCryptoEvents);

        return view('frontend.crypto', compact(
            'selectedTimeframe',
            'selectedAsset',
            'events',
            'timeframeCounts',
            'assetCounts'
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
     * Get timeframe counts
     */
    private function getTimeframeCounts($events)
    {
        $now = now();
        $counts = [
            'all' => $events->count(),
            '15m' => $events->filter(fn($e) => $e->created_at >= $now->copy()->subMinutes(15))->count(),
            'hourly' => $events->filter(fn($e) => $e->created_at >= $now->copy()->subHour())->count(),
            '4h' => $events->filter(fn($e) => $e->created_at >= $now->copy()->subHours(4))->count(),
            'daily' => $events->filter(fn($e) => $e->created_at >= $now->copy()->subDay())->count(),
            'weekly' => $events->filter(fn($e) => $e->created_at >= $now->copy()->subWeek())->count(),
            'monthly' => $events->filter(fn($e) => $e->created_at >= $now->copy()->subMonth())->count(),
            'pre-market' => $events->filter(function ($e) use ($now) {
                return !$e->start_date || $e->start_date > $now;
            })->count(),
            'etf' => $events->filter(function ($e) {
                $title = strtolower($e->title);
                return strpos($title, 'etf') !== false || strpos($title, 'exchange traded fund') !== false;
            })->count(),
        ];

        return $counts;
    }

    /**
     * Get asset counts
     */
    private function getAssetCounts($events)
    {
        $assets = [
            'Bitcoin' => ['bitcoin', 'btc'],
            'Ethereum' => ['ethereum', 'eth'],
            'Solana' => ['solana', 'sol'],
            'XRP' => ['xrp', 'ripple'],
            'Dogecoin' => ['dogecoin', 'doge'],
            'Microstrategy' => ['microstrategy', 'mstr'],
        ];

        $counts = [];

        foreach ($assets as $assetName => $keywords) {
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
                'name' => $assetName,
                'slug' => strtolower($assetName),
                'count' => $count,
                'icon' => $this->getAssetIcon($assetName),
            ];
        }

        return $counts;
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

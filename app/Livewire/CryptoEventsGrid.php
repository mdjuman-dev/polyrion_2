<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class CryptoEventsGrid extends Component
{
    public $perPage = 20;
    public $timeframe = 'all';
    public $asset = 'all';

    protected $queryString = [
        'timeframe' => ['except' => 'all'],
        'asset' => ['except' => 'all'],
    ];

    public function mount($timeframe = 'all', $asset = 'all')
    {
        $this->timeframe = $timeframe ?: request()->get('timeframe', 'all');
        $this->asset = $asset ?: request()->get('asset', 'all');
    }

    public function loadMore()
    {
        if ($this->perPage < 1000) {
            $this->perPage += 20;
        }
    }

    public function updatedTimeframe()
    {
        $this->perPage = 20;
    }

    public function updatedAsset()
    {
        $this->perPage = 20;
    }

    public function refreshEvents()
    {
        // Auto-refresh events
    }

    public function render()
    {
        // Exclude ended events from frontend
        $query = Event::where('category', 'Crypto')
            ->where('active', true)
            ->where('closed', false)
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
            })
            ->with(['markets' => function ($q) {
                $q->select([
                    'id', 'event_id', 'question', 'slug', 'groupItem_title',
                    'outcome_prices', 'outcomes', 'active', 'closed',
                    'best_ask', 'best_bid', 'last_trade_price',
                    'close_time', 'end_date', 'volume_24hr', 'final_result',
                    'outcome_result', 'final_outcome', 'created_at'
                ])
                ->where('active', true)
                ->where('closed', false)
                ->orderBy('created_at', 'desc')
                ->limit(10);
            }])
            ->orderBy('created_at', 'desc');

        // Filter by timeframe
        if ($this->timeframe !== 'all') {
            $query = $this->filterByTimeframe($query, $this->timeframe);
        }

        // Filter by asset
        if ($this->asset !== 'all') {
            $assetKeywords = $this->getAssetKeywords($this->asset);
            $query->where(function ($q) use ($assetKeywords) {
                foreach ($assetKeywords as $keyword) {
                    $q->orWhere('title', 'LIKE', '%' . $keyword . '%');
                }
            });

            $query->whereHas('markets', function ($q) use ($assetKeywords) {
                foreach ($assetKeywords as $keyword) {
                    $q->orWhere('question', 'LIKE', '%' . $keyword . '%');
                }
            });
        }

        // Cache count query for 30 seconds to avoid duplicate queries
        $cacheKey = 'events_count:crypto:' . md5(serialize([
            $this->timeframe, $this->asset, $this->search
        ]));
        $totalCount = \Illuminate\Support\Facades\Cache::remember($cacheKey, 30, function () use ($query) {
            return (clone $query)->count();
        });
        
        $events = $query->take($this->perPage)->get();
        $hasMore = $totalCount > $this->perPage;

        return view('livewire.crypto-events-grid', [
            'events' => $events,
            'hasMore' => $hasMore
        ]);
    }

    private function filterByTimeframe($query, $timeframe)
    {
        $now = now();

        switch ($timeframe) {
            case '15m':
                $query->where('created_at', '>=', $now->copy()->subMinutes(15));
                break;
            case 'hourly':
            case '1h':
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
                $query->where(function ($q) use ($now) {
                    $q->whereNull('start_date')
                      ->orWhere('start_date', '>', $now);
                });
                break;
            case 'etf':
                $query->where(function ($q) {
                    $q->where('title', 'LIKE', '%etf%')
                      ->orWhere('title', 'LIKE', '%exchange traded fund%');
                });
                break;
        }

        return $query;
    }

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

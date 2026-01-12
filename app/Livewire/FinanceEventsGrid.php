<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class FinanceEventsGrid extends Component
{
    public $perPage = 20;
    public $timeframe = 'all';
    public $category = 'all';

    protected $queryString = [
        'timeframe' => ['except' => 'all'],
        'category' => ['except' => 'all'],
    ];

    public function mount($timeframe = 'all', $category = 'all')
    {
        $this->timeframe = $timeframe ?: request()->get('timeframe', 'all');
        $this->category = $category ?: request()->get('category', 'all');
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

    public function updatedCategory()
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
        $query = Event::whereIn('category', ['Finance', 'Economy', 'Business'])
            ->where('active', true)
            ->where('closed', false)
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
            })
            ->with(['markets' => function ($q) {
                $q->where('active', true)
                    ->orderBy('created_at', 'desc');
            }])
            ->orderBy('created_at', 'desc');

        // Filter by timeframe
        if ($this->timeframe !== 'all') {
            $query = $this->filterByTimeframe($query, $this->timeframe);
        }

        // Filter by category
        if ($this->category !== 'all') {
            $categoryKeywords = $this->getCategoryKeywords($this->category);
            $query->where(function ($q) use ($categoryKeywords) {
                foreach ($categoryKeywords as $keyword) {
                    $q->orWhere('title', 'LIKE', '%' . $keyword . '%');
                }
            });

            $query->whereHas('markets', function ($q) use ($categoryKeywords) {
                foreach ($categoryKeywords as $keyword) {
                    $q->orWhere('question', 'LIKE', '%' . $keyword . '%');
                }
            });
        }

        $totalCount = (clone $query)->count();
        $events = $query->take($this->perPage)->get();
        $hasMore = $totalCount > $this->perPage;

        return view('livewire.finance-events-grid', [
            'events' => $events,
            'hasMore' => $hasMore
        ]);
    }

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

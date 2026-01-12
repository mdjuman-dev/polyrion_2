<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class SportsEventsGrid extends Component
{
    public $perPage = 20;
    public $category = 'all';
    public $subcategory = null;

    protected $queryString = [
        'category' => ['except' => 'all'],
        'subcategory' => ['except' => ''],
    ];

    public function mount($category = 'all', $subcategory = null)
    {
        $this->category = $category ?: request()->get('category', 'all');
        $this->subcategory = $subcategory ?: request()->get('subcategory', null);
    }

    public function loadMore()
    {
        if ($this->perPage < 1000) {
            $this->perPage += 20;
        }
    }

    public function updatedCategory()
    {
        $this->perPage = 20;
        $this->subcategory = null;
    }

    public function updatedSubcategory()
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
        $query = Event::where('category', 'Sports')
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
                    'close_time', 'end_date', 'volume24hr', 'final_result',
                    'outcome_result', 'final_outcome', 'created_at'
                ])
                ->where('active', true)
                ->where('closed', false)
                ->orderBy('created_at', 'desc')
                ->limit(10);
            }])
            ->orderBy('created_at', 'desc');

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

            // Filter by subcategory if selected
            if ($this->subcategory) {
                $query->where(function ($q) {
                    $q->orWhere('title', 'LIKE', '%' . $this->subcategory . '%');
                });
            }
        }

        // Cache count query for 30 seconds to avoid duplicate queries
        $cacheKey = 'events_count:sports:' . md5(serialize([
            $this->category, $this->subcategory
        ]));
        $totalCount = \Illuminate\Support\Facades\Cache::remember($cacheKey, 30, function () use ($query) {
            return (clone $query)->count();
        });
        
        $events = $query->take($this->perPage)->get();
        $hasMore = $totalCount > $this->perPage;

        return view('livewire.sports-events-grid', [
            'events' => $events,
            'hasMore' => $hasMore
        ]);
    }

    private function getCategoryKeywords($category)
    {
        $keywords = [
            'football' => ['football', 'soccer', 'premier league', 'champions league'],
            'cricket' => ['cricket', 'ipl', 't20', 'test match'],
            'basketball' => ['basketball', 'nba', 'ncaa'],
            'tennis' => ['tennis', 'wimbledon', 'us open', 'french open'],
            'baseball' => ['baseball', 'mlb', 'world series'],
        ];

        return $keywords[strtolower($category)] ?? [strtolower($category)];
    }
}

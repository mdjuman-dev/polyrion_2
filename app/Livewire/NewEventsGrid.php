<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class NewEventsGrid extends Component
{
    public $search = '';
    public $perPage = 20;
    public $selectedTag = null;

    protected $listeners = [
        'tag-selected' => 'filterByTag'
    ];

    public function filterByTag($tagSlug)
    {
        $this->selectedTag = $tagSlug;
        $this->perPage = 20; // Reset pagination when filter changes
    }

    public function loadMore()
    {
        if ($this->perPage < 1000) {
            $this->perPage += 20;
        }
    }

    public function updatingSearch()
    {
        $this->perPage = 20;
    }

    public function refreshEvents()
    {
        // This method is called by wire:poll to refresh the events
    }

    public function render()
    {
        try {
        // Optimize: Select only necessary columns
        $query = Event::select([
            'id', 'title', 'slug', 'image', 'icon', 'category',
            'volume', 'volume_24hr', 'liquidity', 'active', 'closed',
            'end_date', 'created_at'
        ])
        ->with(['markets' => function($q) {
            $q->select([
                'id', 'event_id', 'question', 'slug', 'groupItem_title',
                'outcome_prices', 'outcomes', 'active', 'closed',
                'best_ask', 'best_bid', 'last_trade_price',
                'close_time', 'end_date', 'volume24hr', 'final_result',
                'outcome_result', 'final_outcome'
            ])
              ->where('active', true)
              ->where('closed', false)
              ->limit(10); // Limit markets per event
        }])
        ->where('active', true)
        ->where('closed', false);

        // Hide events where end_date has passed
        $query->where(function ($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>', now());
        });

        // Filter by tag if selected
        if (!empty($this->selectedTag)) {
            $query->whereHas('tags', function ($q) {
                $q->where('tags.slug', $this->selectedTag);
            });
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhereHas('markets', function ($marketQuery) {
                        $marketQuery->where('groupItem_title', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Cache count query for 30 seconds to avoid duplicate queries
        $cacheKey = 'events_count:new:' . md5(serialize([
            $this->selectedTag, $this->search
        ]));
        $totalCount = \Illuminate\Support\Facades\Cache::remember($cacheKey, 30, function () use ($query) {
            return $query->count();
        });

        // New = sorted by created_at desc
        $events = $query->orderBy('created_at', 'desc')
            ->take($this->perPage)
            ->get();

        $hasMore = $totalCount > $this->perPage;

        return view('livewire.new-events-grid', [
            'events' => $events,
            'hasMore' => $hasMore
        ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database connection failed in NewEventsGrid: ' . $e->getMessage());
            return view('livewire.new-events-grid', [
                'events' => collect([]),
                'hasMore' => false
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in NewEventsGrid: ' . $e->getMessage());
            return view('livewire.new-events-grid', [
                'events' => collect([]),
                'hasMore' => false
            ]);
        }
    }
}

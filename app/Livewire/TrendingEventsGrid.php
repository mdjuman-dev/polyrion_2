<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class TrendingEventsGrid extends Component
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
            $query = Event::with('markets')
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

        $totalCount = $query->count();

        // Trending = sorted by 24hr volume
        $events = $query->orderBy('volume_24hr', 'desc')
            ->take($this->perPage)
            ->get();

        $hasMore = $totalCount > $this->perPage;

        return view('livewire.trending-events-grid', [
            'events' => $events,
            'hasMore' => $hasMore
        ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database connection failed in TrendingEventsGrid: ' . $e->getMessage());
            return view('livewire.trending-events-grid', [
                'events' => collect([]),
                'hasMore' => false
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in TrendingEventsGrid: ' . $e->getMessage());
            return view('livewire.trending-events-grid', [
                'events' => collect([]),
                'hasMore' => false
            ]);
        }
    }
}

<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Tag;
use Livewire\Component;

class TaggedEventsGrid extends Component
{
    public $tagSlug;
    public $search = '';
    public $perPage = 20;

    public function mount($tagSlug)
    {
        $this->tagSlug = $tagSlug;
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
        // No action needed as render() will be called automatically
    }

    public function render()
    {
        try {
        $tag = Tag::where('slug', $this->tagSlug)->firstOrFail();
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database connection failed in TaggedEventsGrid: ' . $e->getMessage());
            return view('livewire.tagged-events-grid', [
                'events' => collect([]),
                'hasMore' => false,
                'tag' => null
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::warning('Tag not found: ' . $this->tagSlug);
            return view('livewire.tagged-events-grid', [
                'events' => collect([]),
                'hasMore' => false,
                'tag' => null
            ]);
        }

        // Frontend always shows only active events - Optimize with select
        $query = Event::select([
            'id', 'title', 'slug', 'image', 'icon', 'category',
            'volume', 'volume_24hr', 'liquidity', 'active', 'closed',
            'end_date', 'created_at'
        ])
        ->where('active', true)
        ->where('closed', false)
        ->where(function ($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>', now());
        })
        ->whereHas('tags', function ($q) use ($tag) {
            $q->where('tags.id', $tag->id);
        })
        ->with(['markets' => function ($q) {
            // Only active markets
            $q->select([
                'id', 'event_id', 'question', 'slug', 'groupItem_title',
                'volume', 'volume24hr', 'active', 'closed', 'close_time', 'created_at'
            ])
            ->where('active', true)
            ->where('closed', false)
            ->where(function ($query) {
                $query->whereNull('close_time')
                      ->orWhere('close_time', '>', now());
            })
            ->limit(10); // Limit markets per event
        }])
            ->whereHas('markets', function ($q) {
                // Only events with at least one active market
                $q->where('active', true)
                  ->where('closed', false)
                  ->where(function ($query) {
                      $query->whereNull('close_time')
                            ->orWhere('close_time', '>', now());
                  });
            });

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhereHas('markets', function ($marketQuery) {
                        $marketQuery->where('groupItem_title', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $totalCount = (clone $query)->count();

        $events = $query->orderBy('volume', 'desc')
            ->take($this->perPage)
            ->get();

        $hasMore = $totalCount > $this->perPage;

        return view('livewire.tagged-events-grid', [
            'events' => $events,
            'hasMore' => $hasMore,
            'tag' => $tag
        ]);
    }
}

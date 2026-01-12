<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\SavedEvent;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class SavedEventsGrid extends Component
{
    public $search = '';
    public $perPage = 20;

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
        if (!Auth::check()) {
            return view('livewire.saved-events-grid', [
                'events' => collect([]),
                'hasMore' => false
            ]);
        }

        $savedEventIds = SavedEvent::where('user_id', Auth::id())->pluck('event_id');

        // Exclude ended events from saved events list
        $query = Event::select([
            'id', 'title', 'slug', 'image', 'icon', 'category',
            'volume', 'volume_24hr', 'liquidity', 'active', 'closed',
            'end_date', 'created_at'
        ])
        ->whereIn('id', $savedEventIds)
        ->where('active', true)
        ->where('closed', false)
        ->where(function ($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>', now());
        })
        ->with(['markets' => function($q) {
            $q->select([
                'id', 'event_id', 'question', 'slug', 'groupItem_title',
                'outcome_prices', 'outcomes', 'active', 'closed',
                'best_ask', 'best_bid', 'last_trade_price',
                'close_time', 'end_date', 'volume_24hr', 'final_result',
                'outcome_result', 'final_outcome', 'created_at'
            ])
            ->where('active', true)
            ->where('closed', false)
            ->limit(10);
        }]);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhereHas('markets', function ($marketQuery) {
                        $marketQuery->where('question', 'like', '%' . $this->search . '%')
                            ->orWhere('groupItem_title', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $totalCount = $query->count();

        $events = $query->orderBy('created_at', 'desc')
            ->take($this->perPage)
            ->get();

        $hasMore = $totalCount > $this->perPage;

        return view('livewire.saved-events-grid', [
            'events' => $events,
            'hasMore' => $hasMore
        ]);
    }
}

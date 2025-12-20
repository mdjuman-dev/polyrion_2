<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class BreakingEventsGrid extends Component
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
    }

    public function render()
    {
        $query = Event::with('markets')
            ->where('active', true)
            ->where('closed', false)
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
            })
            ->where(function ($q) {
                $q->where('featured', true)
                    ->orWhere('new', true)
                    ->orWhere('created_at', '>=', now()->subDays(7)); // Recent events
            });

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhereHas('markets', function ($marketQuery) {
                        $marketQuery->where('groupItem_title', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $totalCount = $query->count();

        // Breaking = featured, new, or high volume recent events
        $events = $query->orderBy('volume_24hr', 'desc')
            ->orderBy('created_at', 'desc')
            ->take($this->perPage)
            ->get();

        $hasMore = $totalCount > $this->perPage;

        return view('livewire.breaking-events-grid', [
            'events' => $events,
            'hasMore' => $hasMore
        ]);
    }
}

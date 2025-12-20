<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class CategoryEventsGrid extends Component
{
    public $category;
    public $search = '';
    public $perPage = 20;

    public function mount($category)
    {
        $this->category = $category;
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
        $query = Event::with('markets')
            ->where('active', true)
            ->where('closed', false);

        // Hide events where end_date has passed
        $query->where(function ($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>', now());
        });

        // Filter by category
        if ($this->category && $this->category !== 'all') {
            // Convert to proper case (first letter uppercase)
            $categoryName = ucfirst(strtolower($this->category));
            $query->byCategory($categoryName);
        }

        // Search functionality
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhereHas('markets', function ($marketQuery) {
                        $marketQuery->where('groupItem_title', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $totalCount = $query->count();

        // Order by volume and date
        $events = $query->orderBy('volume_24hr', 'desc')
            ->orderBy('created_at', 'desc')
            ->take($this->perPage)
            ->get();

        $hasMore = $totalCount > $this->perPage;

        return view('livewire.category-events-grid', [
            'events' => $events,
            'hasMore' => $hasMore,
            'category' => $this->category
        ]);
    }
}


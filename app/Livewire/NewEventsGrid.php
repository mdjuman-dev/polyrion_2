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
        $query = Event::with('markets')
            ->where('active', true)
            ->where('closed', false);

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

        // New = sorted by created_at desc
        $events = $query->orderBy('created_at', 'desc')
            ->take($this->perPage)
            ->get();

        $hasMore = $totalCount > $this->perPage;

        return view('livewire.new-events-grid', [
            'events' => $events,
            'hasMore' => $hasMore
        ]);
    }
}

<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class MarketsGrid extends Component
{
    public $search = '';
    public $perPage = 20;
    public $selectedTag = null;
    public $sortBy = '24hr-volume';
    public $frequency = 'all';
    public $status = 'active';
    public $hideSports = false;
    public $hideCrypto = false;
    public $hideEarnings = false;

    protected $listeners = ['tag-selected' => 'filterByTag'];

    public function mount()
    {
        // Listen for tag selection from TagFilters component
    }

    public function filterByTag($tagSlug)
    {
        $this->selectedTag = $tagSlug;
        $this->perPage = 20; // Reset pagination when filter changes
    }

    public function setSortBy($sort)
    {
        $this->sortBy = $sort;
        $this->perPage = 20;
    }

    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
        $this->perPage = 20;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->perPage = 20;
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
        $query = Event::with('markets');

        // Filter by tag if selected
        if (!empty($this->selectedTag)) {
            $query->whereHas('tags', function ($q) {
                $q->where('tags.slug', $this->selectedTag);
            });
        }

        // Filter by status
        if ($this->status === 'active') {
            $query->where('active', true)->where('closed', false);
        } elseif ($this->status === 'closed') {
            $query->where('closed', true);
        } elseif ($this->status === 'pending') {
            $query->where('active', false)->where('closed', false);
        }

        // Filter by frequency (based on end_date)
        if ($this->frequency === 'daily') {
            $query->where('end_date', '>=', now())
                ->where('end_date', '<=', now()->addDay());
        } elseif ($this->frequency === 'weekly') {
            $query->where('end_date', '>=', now())
                ->where('end_date', '<=', now()->addWeek());
        } elseif ($this->frequency === 'monthly') {
            $query->where('end_date', '>=', now())
                ->where('end_date', '<=', now()->addMonth());
        }

        // Hide categories (based on tags)
        if ($this->hideSports) {
            $query->whereDoesntHave('tags', function ($q) {
                $q->where('slug', 'like', '%sport%');
            });
        }
        if ($this->hideCrypto) {
            $query->whereDoesntHave('tags', function ($q) {
                $q->whereIn('slug', ['crypto', 'bitcoin', 'crypto-prices']);
            });
        }
        if ($this->hideEarnings) {
            $query->whereDoesntHave('tags', function ($q) {
                $q->where('slug', 'like', '%earning%');
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

        // Sort by
        switch ($this->sortBy) {
            case '24hr-volume':
                $query->orderBy('volume_24hr', 'desc');
                break;
            case 'total-volume':
                $query->orderBy('volume', 'desc');
                break;
            case 'liquidity':
                $query->orderBy('liquidity', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'ending-soon':
                $query->orderBy('end_date', 'asc');
                break;
            case 'competitive':
                // Assuming competitive is based on volume or some other metric
                $query->orderBy('volume', 'desc');
                break;
            default:
                $query->orderBy('volume_24hr', 'desc');
        }

        $totalCount = $query->count();

        $events = $query->take($this->perPage)
            ->get();

        $hasMore = $totalCount > $this->perPage;

        return view('livewire.markets-grid', [
            'events' => $events,
            'hasMore' => $hasMore
        ]);
    }
}

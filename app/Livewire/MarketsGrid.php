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

    protected $listeners = [
        'tag-selected' => 'filterByTag',
        'filter-selected' => 'handleBrowseFilter',
        'search-query-updated' => 'updateSearch'
    ];

    public function mount()
    {
        // Listen for tag selection from TagFilters component
        // Check if filter is passed via query parameter
        if (request()->has('filter')) {
            $this->handleBrowseFilter(request()->get('filter'));
        }
        
        // Check if search query is passed via query parameter
        if (request()->has('search')) {
            $this->search = request()->get('search');
        }
    }
    
    public function updateSearch($query)
    {
        $this->search = $query;
        $this->perPage = 20; // Reset pagination when search changes
    }

    public function filterByTag($tagSlug)
    {
        $this->selectedTag = $tagSlug;
        $this->perPage = 20; // Reset pagination when filter changes
    }

    public function handleBrowseFilter($filter)
    {
        $this->perPage = 20; // Reset pagination when filter changes
        
        // Map filter to sortBy
        switch ($filter) {
            case 'new':
                $this->sortBy = 'newest';
                break;
            case 'trending':
                $this->sortBy = '24hr-volume';
                break;
            case 'popular':
                $this->sortBy = 'total-volume';
                break;
            case 'liquid':
                $this->sortBy = 'liquidity';
                break;
            case 'ending-soon':
                $this->sortBy = 'ending-soon';
                break;
            case 'competitive':
                $this->sortBy = 'competitive';
                break;
        }
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
    }

    public function render()
    {
        // Eager load markets based on status filter (same logic as admin panel)
        $query = Event::with(['markets' => function ($q) {
            if ($this->status === 'closed') {
                // Closed: closed=true (same as admin panel)
                $q->where('closed', true);
            } elseif ($this->status === 'active') {
                // Active: active=true AND closed=false
                $q->where('active', true)
                  ->where('closed', false)
                  ->where(function ($query) {
                      $query->whereNull('close_time')
                            ->orWhere('close_time', '>', now());
                  });
            } elseif ($this->status === 'pending') {
                // Inactive: active=false AND closed=false
                $q->where('active', false)
                  ->where('closed', false)
                  ->where(function ($query) {
                      $query->whereNull('close_time')
                            ->orWhere('close_time', '>', now());
                  });
            } else {
                // Default: load non-closed markets
                $q->where('closed', false)
                  ->where(function ($query) {
                      $query->whereNull('close_time')
                            ->orWhere('close_time', '>', now());
                  });
            }
        }]);

        // Filter by tag if selected
        if (!empty($this->selectedTag)) {
            $query->whereHas('tags', function ($q) {
                $q->where('tags.slug', $this->selectedTag);
            });
        }

        // Filter by status (same logic as admin panel)
        if ($this->status === 'active') {
            // Active: active=true AND closed=false
            $query->where('active', true)->where('closed', false);
        } elseif ($this->status === 'closed') {
            // Closed: closed=true (same as admin panel)
            $query->where('closed', true);
        } elseif ($this->status === 'pending') {
            // Inactive: active=false AND closed=false (not closed but not active)
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
                        // Apply status filter to market search (same as admin panel)
                        if ($this->status === 'closed') {
                            // Closed: closed=true
                            $marketQuery->where('closed', true);
                        } elseif ($this->status === 'active') {
                            // Active: active=true AND closed=false
                            $marketQuery->where('active', true)
                              ->where('closed', false)
                              ->where(function ($query) {
                                  $query->whereNull('close_time')
                                        ->orWhere('close_time', '>', now());
                              });
                        } elseif ($this->status === 'pending') {
                            // Inactive: active=false AND closed=false
                            $marketQuery->where('active', false)
                              ->where('closed', false)
                              ->where(function ($query) {
                                  $query->whereNull('close_time')
                                        ->orWhere('close_time', '>', now());
                              });
                        } else {
                            $marketQuery->where('closed', false)
                              ->where(function ($query) {
                                  $query->whereNull('close_time')
                                        ->orWhere('close_time', '>', now());
                              });
                        }
                        
                        $marketQuery->where(function ($mq) {
                            $mq->where('groupItem_title', 'like', '%' . $this->search . '%')
                              ->orWhere('question', 'like', '%' . $this->search . '%');
                        });
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

        // Only show events that have at least one market matching the status filter
        // Note: This is already filtered in the eager loading, but we need whereHas for the main query
        $query->whereHas('markets', function ($q) {
            if ($this->status === 'closed') {
                // Closed: closed=true (same as admin panel)
                $q->where('closed', true);
            } elseif ($this->status === 'active') {
                // Active: active=true AND closed=false
                $q->where('active', true)
                  ->where('closed', false)
                  ->where(function ($query) {
                      $query->whereNull('close_time')
                            ->orWhere('close_time', '>', now());
                  });
            } elseif ($this->status === 'pending') {
                // Inactive: active=false AND closed=false
                $q->where('active', false)
                  ->where('closed', false)
                  ->where(function ($query) {
                      $query->whereNull('close_time')
                            ->orWhere('close_time', '>', now());
                  });
            } else {
                // Default: show events with non-closed markets
                $q->where('closed', false)
                  ->where(function ($query) {
                      $query->whereNull('close_time')
                            ->orWhere('close_time', '>', now());
                  });
            }
        });

        // Use clone for count to avoid affecting the main query
        $totalCount = (clone $query)->count();

        $events = $query->take($this->perPage)
            ->get();

        $hasMore = $totalCount > $this->perPage;

        return view('livewire.markets-grid', [
            'events' => $events,
            'hasMore' => $hasMore
        ]);
    }
}
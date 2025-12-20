<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class MobileSearch extends Component
{
    public $query = '';
    public $showSuggestions = false;
    public $selectedFilter = null;
    public $activeTab = 'markets';

    protected $listeners = ['closeSearch' => 'closeSuggestions'];

    public function updatedQuery()
    {
        $this->showSuggestions = true;
        
        // Dispatch search query to MarketsGrid if on home page
        if (request()->routeIs('home') && !empty($this->query)) {
            $this->dispatch('search-query-updated', query: $this->query);
        }
    }
    
    public function performSearch()
    {
        if (!empty($this->query)) {
            // If on home page, dispatch search to MarketsGrid
            if (request()->routeIs('home')) {
                $this->dispatch('search-query-updated', query: $this->query);
            } else {
                // If not on home page, redirect to home with search query
                return redirect()->route('home', ['search' => $this->query]);
            }
        }
    }

    public function mount()
    {
        $this->showSuggestions = false;
    }

    public function selectEvent($eventId)
    {
        $event = Event::find($eventId);
        if ($event) {
            // Save to recent searches
            $this->addToRecentSearches($event);
            return redirect()->route('market.details', $event->slug);
        }
    }

    public function selectFilter($filter)
    {
        $this->selectedFilter = $filter;
        $this->query = '';
        $this->showSuggestions = false;

        // If not on home page, redirect to home with filter query parameter
        if (!request()->routeIs('home')) {
            return redirect()->route('home', ['filter' => $filter]);
        }

        // Emit event to MarketsGrid
        $this->dispatch('filter-selected', filter: $filter);
    }

    public function removeRecentSearch($index)
    {
        $recentSearches = session('recent_searches', []);
        if (isset($recentSearches[$index])) {
            unset($recentSearches[$index]);
            $recentSearches = array_values($recentSearches); // Re-index array
            session(['recent_searches' => $recentSearches]);
        }
    }

    public function addToRecentSearches($event)
    {
        $recentSearches = session('recent_searches', []);

        // Remove if already exists
        $recentSearches = array_filter($recentSearches, function ($item) use ($event) {
            return $item['slug'] !== $event->slug;
        });

        // Add to beginning
        array_unshift($recentSearches, [
            'title' => $event->title,
            'slug' => $event->slug,
            'image' => $event->image
        ]);

        // Keep only last 5
        $recentSearches = array_slice($recentSearches, 0, 5);

        session(['recent_searches' => $recentSearches]);
    }

    public function closeSuggestions()
    {
        $this->showSuggestions = false;
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->showSuggestions = false;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $suggestions = collect([]);

        if (!empty($this->query)) {
            // Get search suggestions - only active, non-closed events with active markets
            $suggestions = Event::with(['markets' => function ($q) {
                // Only active markets
                $q->where('active', true)
                  ->where('closed', false)
                  ->where(function ($query) {
                      $query->whereNull('close_time')
                            ->orWhere('close_time', '>', now());
                  });
            }])
                ->where('active', true)
                ->where('closed', false)
                ->where(function ($q) {
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>', now());
                })
                ->whereHas('markets', function ($q) {
                    // Only events with at least one active market
                    $q->where('active', true)
                      ->where('closed', false)
                      ->where(function ($query) {
                          $query->whereNull('close_time')
                                ->orWhere('close_time', '>', now());
                      });
                })
                ->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->query . '%')
                        ->orWhereHas('markets', function ($marketQuery) {
                            // Only search in active markets
                            $marketQuery->where('active', true)
                              ->where('closed', false)
                              ->where(function ($query) {
                                  $query->whereNull('close_time')
                                        ->orWhere('close_time', '>', now());
                              })
                              ->where(function ($mq) {
                                  $mq->where('groupItem_title', 'like', '%' . $this->query . '%')
                                    ->orWhere('question', 'like', '%' . $this->query . '%');
                              });
                        });
                })
                ->orderBy('volume_24hr', 'desc')
                ->take(5)
                ->get();
        }

        // Get recent searches from session
        $recentSearches = session('recent_searches', []);

        return view('livewire.mobile-search', [
            'suggestions' => $suggestions,
            'recentSearches' => $recentSearches
        ]);
    }
}


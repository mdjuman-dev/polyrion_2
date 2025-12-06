<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class HeaderSearch extends Component
{
    public $query = '';
    public $showSuggestions = false;
    public $selectedFilter = null;

    protected $listeners = ['closeSearch' => 'closeSuggestions'];

    public function updatedQuery()
    {
        $this->showSuggestions = true;
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

    public function render()
    {
        $suggestions = collect([]);

        if (!empty($this->query)) {
            // Get search suggestions
            $suggestions = Event::with('markets')
                ->where('active', true)
                ->where('closed', false)
                ->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->query . '%')
                        ->orWhereHas('markets', function ($marketQuery) {
                            $marketQuery->where('groupItem_title', 'like', '%' . $this->query . '%');
                        });
                })
                ->orderBy('volume_24hr', 'desc')
                ->take(5)
                ->get();
        }

        // Get recent searches from session
        $recentSearches = session('recent_searches', []);

        return view('livewire.header-search', [
            'suggestions' => $suggestions,
            'recentSearches' => $recentSearches
        ]);
    }
}

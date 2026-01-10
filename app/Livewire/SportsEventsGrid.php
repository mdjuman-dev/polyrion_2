<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class SportsEventsGrid extends Component
{
    public $perPage = 20;
    public $category = 'all';
    public $subcategory = null;

    protected $queryString = [
        'category' => ['except' => 'all'],
        'subcategory' => ['except' => ''],
    ];

    public function mount($category = 'all', $subcategory = null)
    {
        $this->category = $category ?: request()->get('category', 'all');
        $this->subcategory = $subcategory ?: request()->get('subcategory', null);
    }

    public function loadMore()
    {
        if ($this->perPage < 1000) {
            $this->perPage += 20;
        }
    }

    public function updatedCategory()
    {
        $this->perPage = 20;
        $this->subcategory = null;
    }

    public function updatedSubcategory()
    {
        $this->perPage = 20;
    }

    public function refreshEvents()
    {
        // Auto-refresh events
    }

    public function render()
    {
        $query = Event::where('category', 'Sports')
            ->where('active', true)
            ->where('closed', false)
            ->with(['markets' => function ($q) {
                $q->where('active', true)
                    ->orderBy('created_at', 'desc');
            }])
            ->orderBy('created_at', 'desc');

        // Filter by category
        if ($this->category !== 'all') {
            $categoryKeywords = $this->getCategoryKeywords($this->category);
            $query->where(function ($q) use ($categoryKeywords) {
                foreach ($categoryKeywords as $keyword) {
                    $q->orWhere('title', 'LIKE', '%' . $keyword . '%');
                }
            });

            $query->whereHas('markets', function ($q) use ($categoryKeywords) {
                foreach ($categoryKeywords as $keyword) {
                    $q->orWhere('question', 'LIKE', '%' . $keyword . '%');
                }
            });

            // Filter by subcategory if selected
            if ($this->subcategory) {
                $query->where(function ($q) {
                    $q->orWhere('title', 'LIKE', '%' . $this->subcategory . '%');
                });
            }
        }

        $totalCount = (clone $query)->count();
        $events = $query->take($this->perPage)->get();
        $hasMore = $totalCount > $this->perPage;

        return view('livewire.sports-events-grid', [
            'events' => $events,
            'hasMore' => $hasMore
        ]);
    }

    private function getCategoryKeywords($category)
    {
        $keywords = [
            'football' => ['football', 'soccer', 'premier league', 'champions league'],
            'cricket' => ['cricket', 'ipl', 't20', 'test match'],
            'basketball' => ['basketball', 'nba', 'ncaa'],
            'tennis' => ['tennis', 'wimbledon', 'us open', 'french open'],
            'baseball' => ['baseball', 'mlb', 'world series'],
        ];

        return $keywords[strtolower($category)] ?? [strtolower($category)];
    }
}

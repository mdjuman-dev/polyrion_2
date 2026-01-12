<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class PoliticsEventsGrid extends Component
{
    public $perPage = 20;
    public $category = 'all';
    public $country = null;

    protected $queryString = [
        'category' => ['except' => 'all'],
        'country' => ['except' => ''],
    ];

    public function mount($category = 'all', $country = null)
    {
        $this->category = $category ?: request()->get('category', 'all');
        $this->country = $country ?: request()->get('country', null);
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
    }

    public function updatedCountry()
    {
        $this->perPage = 20;
    }

    public function refreshEvents()
    {
        // Auto-refresh events
    }

    public function render()
    {
        // Exclude ended events from frontend
        $query = Event::whereIn('category', ['Politics', 'Elections', 'Geopolitics'])
            ->where('active', true)
            ->where('closed', false)
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
            })
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
        }

        // Filter by country
        if ($this->country) {
            $countryKeywords = $this->getCountryKeywords($this->country);
            $query->where(function ($q) use ($countryKeywords) {
                foreach ($countryKeywords as $keyword) {
                    $q->orWhere('title', 'LIKE', '%' . $keyword . '%');
                }
            });
        }

        $totalCount = (clone $query)->count();
        $events = $query->take($this->perPage)->get();
        $hasMore = $totalCount > $this->perPage;

        return view('livewire.politics-events-grid', [
            'events' => $events,
            'hasMore' => $hasMore
        ]);
    }

    private function getCategoryKeywords($category)
    {
        $keywords = [
            'trump' => ['trump', 'donald trump'],
            'epstein' => ['epstein'],
            'venezuela' => ['venezuela'],
            'midterms' => ['midterms', 'midterm'],
            'primaries' => ['primaries', 'primary'],
            'us-election' => ['us election', 'united states election', 'presidential election'],
        ];

        return $keywords[strtolower($category)] ?? [strtolower($category)];
    }

    private function getCountryKeywords($country)
    {
        $keywords = [
            'us' => ['united states', 'usa', 'us', 'america'],
            'uk' => ['united kingdom', 'uk', 'britain', 'england'],
            'india' => ['india', 'indian'],
            'brazil' => ['brazil', 'brazilian'],
        ];

        return $keywords[strtolower($country)] ?? [strtolower($country)];
    }
}

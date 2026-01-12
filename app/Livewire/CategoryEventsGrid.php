<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class CategoryEventsGrid extends Component
{
    public $category;
    public $subcategory = 'all';
    public $search = '';
    public $perPage = 20;

    protected $queryString = [
        'subcategory' => ['except' => 'all'],
    ];

    public function mount($category, $subcategory = 'all')
    {
        $this->category = $category;
        $this->subcategory = $subcategory ?: request()->get('subcategory', 'all');
    }

    public function loadMore()
    {
        if ($this->perPage < 1000) {
            $this->perPage += 20;
        }
    }

    public function updatedSubcategory()
    {
        $this->perPage = 20;
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
        // Optimize: Select only necessary columns
        $query = Event::select([
            'id', 'title', 'slug', 'image', 'icon', 'category', 
            'volume', 'volume_24hr', 'liquidity', 'active', 'closed', 
            'end_date', 'created_at'
        ])
        ->with(['markets' => function($q) {
            $q->select([
                'id', 'event_id', 'question', 'slug', 'groupItem_title',
                'outcome_prices', 'outcomes', 'active', 'closed', 
                'best_ask', 'best_bid', 'last_trade_price',
                'close_time', 'end_date', 'volume_24hr', 'final_result',
                'outcome_result', 'final_outcome'
            ])
              ->where('active', true)
              ->where('closed', false)
              ->limit(10); // Limit markets per event (increased for better display)
        }])
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

        // Filter by sub-category
        if ($this->subcategory && $this->subcategory !== 'all') {
            $subCategoryKeywords = $this->getSubCategoryKeywords($this->subcategory, $this->category);
            $query->where(function ($q) use ($subCategoryKeywords) {
                foreach ($subCategoryKeywords as $keyword) {
                    $q->orWhere('title', 'LIKE', '%' . $keyword . '%');
                }
            });

            $query->whereHas('markets', function ($q) use ($subCategoryKeywords) {
                foreach ($subCategoryKeywords as $keyword) {
                    $q->orWhere('question', 'LIKE', '%' . $keyword . '%');
                }
            });
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

    private function getSubCategoryKeywords($subcategory, $category)
    {
        $patterns = [
            'Geopolitics' => [
                'ukraine' => ['ukraine', 'russian', 'russia', 'putin', 'zelensky'],
                'venezuela' => ['venezuela', 'maduro', 'guaidó'],
                'iran' => ['iran', 'khamenei', 'iranian', 'tehran'],
                'gaza' => ['gaza', 'palestine', 'palestinian'],
                'israel' => ['israel', 'israeli', 'netanyahu'],
                'sudan' => ['sudan', 'sudanese'],
                'china' => ['china', 'chinese', 'beijing', 'xi jinping'],
                'thailand-cambodia' => ['thailand', 'cambodia', 'thai', 'cambodian'],
                'middle-east' => ['middle east', 'syria', 'iraq', 'yemen', 'lebanon'],
                'us-strikes' => ['us strikes', 'us strike', 'american strike'],
                'taiwan' => ['taiwan', 'taiwanese'],
                'north-korea' => ['north korea', 'north korean', 'kim jong'],
            ],
            'Tech' => [
                'ai' => ['artificial intelligence', 'ai', 'machine learning', 'ml'],
                'apple' => ['apple', 'iphone', 'ipad', 'macbook'],
                'google' => ['google', 'alphabet', 'android'],
                'microsoft' => ['microsoft', 'windows', 'azure'],
                'meta' => ['meta', 'facebook', 'instagram', 'whatsapp'],
                'tesla' => ['tesla', 'elon musk', 'model s', 'model 3'],
                'amazon' => ['amazon', 'aws', 'alexa'],
                'netflix' => ['netflix', 'streaming'],
            ],
        ];

        $categoryPatterns = $patterns[$category] ?? [];
        return $categoryPatterns[strtolower($subcategory)] ?? [strtolower($subcategory)];
    }
}


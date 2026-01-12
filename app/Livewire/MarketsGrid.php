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

   // Enable Livewire to track search property changes
   protected $queryString = [
      'search' => ['except' => ''],
      'sortBy' => ['except' => '24hr-volume'],
      'frequency' => ['except' => 'all'],
      'status' => ['except' => 'active'],
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

   public function updateSearch($query = null)
   {
      // Handle both event dispatch and direct call
      if ($query !== null) {
         $this->search = $query;
      }
      $this->perPage = 20; // Reset pagination when search changes
   }

   // This method is called when search property is updated via wire:model
   public function updatedSearch()
   {
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
      // Prevent loading if already at max or if there are no more events
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
      try {
         // Frontend always shows only active events
         // Eager load only active markets - Optimize with select
         $query = Event::select([
            'id', 'title', 'slug', 'image', 'icon', 'category',
            'volume', 'volume_24hr', 'liquidity', 'active', 'closed',
            'end_date', 'created_at'
         ])
         ->with([
            'markets' => function ($q) {
               // Only active markets: active=true AND closed=false with all required fields
               $q->select([
                  'id', 'event_id', 'question', 'slug', 'groupItem_title',
                  'outcome_prices', 'outcomes', 'active', 'closed',
                  'best_ask', 'best_bid', 'last_trade_price',
                  'close_time', 'end_date', 'volume', 'volume24hr', 
                  'liquidity_clob', 'final_result', 'outcome_result', 
                  'final_outcome', 'created_at'
               ])
               ->where('active', true)
               ->where('closed', false)
               ->where(function ($query) {
                  $query->whereNull('close_time')
                     ->orWhere('close_time', '>', now());
               })
               ->limit(10); // Limit markets per event
            }
         ]);

         // Filter by tag if selected
         if (!empty($this->selectedTag)) {
            $query->whereHas('tags', function ($q) {
               $q->where('tags.slug', $this->selectedTag);
            });
         }

         // Frontend shows active events by default
         // But also shows ended events if status filter is set to 'closed' or 'resolved'
         if ($this->status === 'closed' || $this->status === 'resolved') {
            // Show closed/resolved events
            $query->where(function ($q) {
               $q->where('closed', true)
                  ->orWhere(function ($subQ) {
                     $subQ->whereNotNull('end_date')
                        ->where('end_date', '<=', now());
                  });
            });
         } else {
            // Show active events
            $query->where('active', true)->where('closed', false);

            // Hide events where end_date has passed
            $query->where(function ($q) {
               $q->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
            });
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
                     // Only search in active markets
                     $marketQuery->where('active', true)
                        ->where('closed', false)
                        ->where(function ($query) {
                        $query->whereNull('close_time')
                           ->orWhere('close_time', '>', now());
                     })
                        ->where(function ($mq) {
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

         // Only show events that have at least one active market
         $query->whereHas('markets', function ($q) {
            // Only active markets: active=true AND closed=false
            $q->where('active', true)
               ->where('closed', false)
               ->where(function ($query) {
                  $query->whereNull('close_time')
                     ->orWhere('close_time', '>', now());
               });
         });

         // Cache count query for 30 seconds to avoid duplicate queries
         $cacheKey = 'events_count:markets:' . md5(serialize([
            $this->selectedTag, $this->status, $this->frequency, $this->sortBy, $this->search
         ]));
         $totalCount = \Illuminate\Support\Facades\Cache::remember($cacheKey, 30, function () use ($query) {
            return (clone $query)->count();
         });

         $events = $query->take($this->perPage)
            ->get();

         $hasMore = $totalCount > $this->perPage;

         return view('livewire.markets-grid', [
            'events' => $events,
            'hasMore' => $hasMore
         ]);
      } catch (\Illuminate\Database\QueryException $e) {
         \Log::error('Database connection failed in MarketsGrid: ' . $e->getMessage());
         return view('livewire.markets-grid', [
            'events' => collect([]),
            'hasMore' => false
         ]);
      } catch (\Exception $e) {
         \Log::error('Error in MarketsGrid: ' . $e->getMessage());
         return view('livewire.markets-grid', [
            'events' => collect([]),
            'hasMore' => false
         ]);
      }
   }
}
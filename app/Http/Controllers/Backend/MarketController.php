<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\Market;
use App\Models\Tag;
use App\Services\SettlementService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class MarketController extends Controller
{
   public function __construct()
   {
      // Permission checks are handled in routes
   }

   /**
    * Display a listing of markets
    */
   public function index(Request $request)
   {
      // Optimize: Select only necessary columns
      $query = Market::select(['id', 'event_id', 'question', 'slug', 'description', 'created_at'])
          ->with(['event' => function($q) {
              $q->select(['id', 'title', 'slug']);
          }]);

      // Search functionality
      if ($request->has('search') && !empty($request->search)) {
         $searchTerm = $request->search;
         $query->where(function ($q) use ($searchTerm) {
            $q->where('question', 'like', "%{$searchTerm}%")
               ->orWhere('description', 'like', "%{$searchTerm}%")
               ->orWhere('slug', 'like', "%{$searchTerm}%");
         });
      }

      // Filter by active status if provided
      // IMPORTANT: Filter is applied BEFORE pagination to filter all data
      if ($request->has('status') && !empty($request->status)) {
         if ($request->status === 'active') {
            // Active: active=true AND closed=false
            $query->where('active', true)->where('closed', false);
         } elseif ($request->status === 'closed') {
            // Closed: closed=true
            $query->where('closed', true);
         } elseif ($request->status === 'inactive') {
            // Inactive: active=false AND closed=false (not closed but not active)
            $query->where('active', false)->where('closed', false);
         }
      }

      // Apply pagination AFTER filtering - this ensures filter works on all data
      $markets = $query->orderBy('volume', 'desc')->paginate(20)->withQueryString();

      return view('backend.market.index', compact('markets'));
   }

   /**
    * Display the specified market
    */
   public function show($id)
   {
      // Optimize: Select only necessary columns
      $market = Market::select(['id', 'event_id', 'question', 'slug', 'description', 'outcome_prices', 'outcomes', 'active', 'closed', 'featured', 'created_at'])
          ->with(['event' => function($q) {
              $q->select(['id', 'title', 'slug']);
          }])
          ->findOrFail($id);

      // Decode JSON fields
      $outcomePrices = $market->outcome_prices ? json_decode($market->outcome_prices, true) : [];
      $outcomes = $market->outcomes ? json_decode($market->outcomes, true) : [];

      return view('backend.market.show', compact('market', 'outcomePrices', 'outcomes'));
   }

   /**
    * Show the form for editing the specified market
    */
   public function edit($id)
   {
      // Optimize: Select only necessary columns
      $market = Market::select(['id', 'event_id', 'question', 'slug', 'description', 'outcome_prices', 'outcomes', 'active', 'closed', 'featured', 'created_at'])
          ->with(['event' => function($q) {
              $q->select(['id', 'title', 'slug']);
          }])
          ->findOrFail($id);

      // Decode JSON fields
      $outcomePrices = $market->outcome_prices ? json_decode($market->outcome_prices, true) : [0.5, 0.5];
      $outcomes = $market->outcomes ? json_decode($market->outcomes, true) : [];

      // Optimize: Select only necessary columns
      $events = Event::select(['id', 'title', 'slug'])
          ->orderBy('title')
          ->get();

      return view('backend.market.edit', compact('market', 'outcomePrices', 'outcomes', 'events'));
   }

   /**
    * Update the specified market
    */
   public function update(Request $request, $id)
   {
      $request->validate([
         'question' => 'required|string|max:255',
         'description' => 'nullable|string',
         'event_id' => 'nullable|exists:events,id',
         'volume' => 'nullable|numeric|min:0',
         'yes_price' => 'nullable|numeric|min:0|max:1',
         'no_price' => 'nullable|numeric|min:0|max:1',
         'active' => 'boolean',
         'closed' => 'boolean',
         'featured' => 'boolean',
      ]);

      // Validate that yes_price + no_price = 1.0
      if ($request->has('yes_price') && $request->has('no_price')) {
         $yesPrice = (float) $request->yes_price;
         $noPrice = (float) $request->no_price;
         $sum = $yesPrice + $noPrice;

         if (abs($sum - 1.0) > 0.001) {
            return back()
               ->withInput()
               ->withErrors(['yes_price' => "Yes and No prices must sum to 1.0 (Current sum: {$sum})"]);
         }
      }

      try {
         $market = Market::findOrFail($id);

         $market->question = $request->question;
         $market->description = $request->description;

         if ($request->has('event_id')) {
            $market->event_id = $request->event_id;
         }

         // Update volume if provided
         if ($request->has('volume')) {
            $market->volume = $request->volume;
         }

         // Update outcome prices if provided
         if ($request->has('yes_price') && $request->has('no_price')) {
            $outcomePrices = [
               (string) $request->no_price,  // Index 0 = No price
               (string) $request->yes_price   // Index 1 = Yes price
            ];
            $market->outcome_prices = json_encode($outcomePrices);
         }

         if ($request->has('active')) {
            $market->active = $request->active;
         }

         if ($request->has('closed')) {
            $market->closed = $request->closed;
         }

         if ($request->has('featured')) {
            $market->featured = $request->featured;
         }

         $market->save();

         return redirect()->route('admin.market.show', $market->id)
            ->with('success', 'Market updated successfully');
      } catch (\Exception $e) {
         Log::error('Failed to update market', [
            'market_id' => $id,
            'error' => $e->getMessage(),
         ]);

         return back()->with('error', 'Failed to update market: ' . $e->getMessage());
      }
   }

   /**
    * Remove the specified market
    */
   public function delete($id)
   {
      try {
         $market = Market::findOrFail($id);
         $market->delete();

         return redirect()->route('admin.market.index')
            ->with('success', 'Market deleted successfully');
      } catch (\Exception $e) {
         Log::error('Failed to delete market', [
            'market_id' => $id,
            'error' => $e->getMessage(),
         ]);

         return back()->with('error', 'Failed to delete market: ' . $e->getMessage());
      }
   }

   /**
    * Store a new market
    */
   public function store(Request $request)
   {
      $request->validate([
         'question' => 'required|string|max:255',
         'description' => 'nullable|string',
         'event_id' => 'required|exists:events,id',
         'slug' => 'nullable|string|unique:markets,slug',
      ]);

      try {
         $market = Market::create([
            'question' => $request->question,
            'description' => $request->description,
            'event_id' => $request->event_id,
            'slug' => $request->slug ?? \Illuminate\Support\Str::slug($request->question),
            'active' => $request->active ?? true,
            'closed' => false,
         ]);

         return redirect()->route('admin.market.show', $market->id)
            ->with('success', 'Market created successfully');
      } catch (\Exception $e) {
         Log::error('Failed to create market', [
            'error' => $e->getMessage(),
         ]);

         return back()->with('error', 'Failed to create market: ' . $e->getMessage());
      }
   }

   /**
    * Save market from Polymarket API
    */
   public function marketSave($slug)
   {
      try {
         // Fetch market from Polymarket API
         $response = Http::timeout(30)->get("https://gamma-api.polymarket.com/markets/{$slug}");

         if (!$response->successful()) {
            return back()->with('error', 'Failed to fetch market from Polymarket API');
         }

         $marketData = $response->json();

         // Find or create event
         $event = Event::where('slug', $marketData['event']['slug'])->first();
         if (!$event) {
            $event = Event::create([
               'slug' => $marketData['event']['slug'],
               'title' => $marketData['event']['title'],
               'description' => $marketData['event']['description'] ?? null,
            ]);
         }

         // Create or update market
         $market = Market::updateOrCreate(
            ['slug' => $slug],
            [
               'event_id' => $event->id,
               'question' => $marketData['question'] ?? null,
               'description' => $marketData['description'] ?? null,
               'outcome_prices' => json_encode($marketData['outcomePrices'] ?? []),
               'outcomes' => json_encode($marketData['outcomes'] ?? []),
               'active' => $marketData['active'] ?? true,
               'closed' => $marketData['closed'] ?? false,
            ]
         );

         return redirect()->route('admin.market.show', $market->id)
            ->with('success', 'Market saved successfully');
      } catch (\Exception $e) {
         Log::error('Failed to save market from API', [
            'slug' => $slug,
            'error' => $e->getMessage(),
         ]);

         return back()->with('error', 'Failed to save market: ' . $e->getMessage());
      }
   }

   /**
    * Get market list (for AJAX/API)
    */
   public function marketList(Request $request)
   {
      // Optimize: Select only necessary columns
      $query = Market::select(['id', 'event_id', 'question', 'slug', 'description', 'created_at'])
          ->with(['event' => function($q) {
              $q->select(['id', 'title', 'slug']);
          }]);

      if ($request->has('search')) {
         $searchTerm = $request->search;
         $query->where(function ($q) use ($searchTerm) {
            $q->where('question', 'like', "%{$searchTerm}%")
               ->orWhere('description', 'like', "%{$searchTerm}%");
         });
      }

      $markets = $query->orderBy('created_at', 'desc')->paginate(20);

      return response()->json($markets);
   }

   /**
    * Search markets
    */
   public function search(Request $request)
   {
      $request->validate([
         'search' => 'required|string|min:1',
      ]);

      $searchTerm = $request->search;
      // Optimize: Select only necessary columns
      $markets = Market::select(['id', 'event_id', 'question', 'slug', 'description', 'volume', 'active', 'closed', 'created_at'])
         ->with(['event' => function($q) {
             $q->select(['id', 'title', 'slug']);
         }])
         ->where(function ($q) use ($searchTerm) {
            $q->where('question', 'like', "%{$searchTerm}%")
               ->orWhere('description', 'like', "%{$searchTerm}%")
               ->orWhere('slug', 'like', "%{$searchTerm}%");
         })
         ->orderBy('volume', 'desc')
         ->paginate(20);

      return view('backend.market.index', compact('markets'));
   }

   function toMysqlDate(?string $date): ?string
   {
      if (empty($date)) {
         return null;
      }

      try {
         return Carbon::parse($date)->format('Y-m-d H:i:s');
      } catch (\Exception $e) {
         return null;
      }
   }

   /**
    * Determine market outcome from Polymarket API data
    */
   private function determineMarketOutcome($mk)
   {
      $outcomeResult = null;
      $finalOutcome = null;
      $finalResult = null;

      // Method 1: Check umaResolutionStatus and lastTradePrice (Polymarket standard method)
      if (isset($mk['umaResolutionStatus']) && $mk['umaResolutionStatus'] === 'resolved') {
         if (isset($mk['lastTradePrice']) && isset($mk['outcomePrices']) && isset($mk['outcomes'])) {
            $lastTradePrice = floatval($mk['lastTradePrice']);
            $outcomePrices = is_string($mk['outcomePrices']) ? json_decode($mk['outcomePrices'], true) : $mk['outcomePrices'];
            $outcomes = is_string($mk['outcomes']) ? json_decode($mk['outcomes'], true) : $mk['outcomes'];

            if (is_array($outcomePrices) && is_array($outcomes) && count($outcomePrices) > 0 && count($outcomes) > 0) {
               $winningIndex = null;
               foreach ($outcomePrices as $index => $price) {
                  if (abs(floatval($price) - $lastTradePrice) < 0.0001) {
                     $winningIndex = $index;
                     break;
                  }
               }

               if ($winningIndex !== null && isset($outcomes[$winningIndex])) {
                  $winningOutcome = $outcomes[$winningIndex];
                  $winningOutcomeUpper = strtoupper(trim($winningOutcome));
                  if ($winningOutcomeUpper === 'YES' || $winningOutcomeUpper === 'NO') {
                     $finalOutcome = $winningOutcomeUpper;
                     $outcomeResult = strtolower($finalOutcome);
                     $finalResult = $outcomeResult;
                  } elseif ($winningIndex === 0) {
                     $finalOutcome = 'YES';
                     $outcomeResult = 'yes';
                     $finalResult = 'yes';
                  } elseif ($winningIndex === 1 && count($outcomes) === 2) {
                     $finalOutcome = 'NO';
                     $outcomeResult = 'no';
                     $finalResult = 'no';
                  }
               }
            }
         }
      }

      // Method 2: Check various possible fields from Polymarket API (fallback)
      if (!$outcomeResult && isset($mk['resolved']) && $mk['resolved'] === true) {
         if (isset($mk['outcome'])) {
            $outcomeResult = strtolower($mk['outcome']);
            $finalOutcome = strtoupper($mk['outcome']);
            $finalResult = $outcomeResult;
         } elseif (isset($mk['finalOutcome'])) {
            $outcomeResult = strtolower($mk['finalOutcome']);
            $finalOutcome = strtoupper($mk['finalOutcome']);
            $finalResult = $outcomeResult;
         } elseif (isset($mk['resolution'])) {
            $outcomeResult = strtolower($mk['resolution']);
            $finalOutcome = strtoupper($mk['resolution']);
            $finalResult = $outcomeResult;
         }
      }

      // Method 3: Also check if market is closed and has resolution info
      if (($mk['closed'] ?? false) && !$outcomeResult && isset($mk['winningOutcome'])) {
         $outcomeResult = strtolower($mk['winningOutcome']);
         $finalOutcome = strtoupper($mk['winningOutcome']);
         $finalResult = $outcomeResult;
      }

      return [$outcomeResult, $finalOutcome, $finalResult];
   }

   function storeEvents()
   {
      $startTime = time();
      $maxExecutionTime = 300;
      $limit = 100;
      $offset = 0;
      $maxBatches = 100;
      $batchCount = 0;
      $totalProcessed = 0;
      $totalSkipped = 0;
      $totalFailed = 0;
      $consecutiveEmptyBatches = 0;
      $maxConsecutiveEmpty = 3;
      $marketsToSettle = [];

      while ($batchCount < $maxBatches) {
         // Global time check
         if ((time() - $startTime) >= $maxExecutionTime) {
            Log::info("Time limit reached. Processed {$totalProcessed} events in this run.");
            break;
         }

         // Fetch events with increased timeout and better error handling
         try {
            $response = Http::timeout(120)
               ->retry(2, 3000, function ($exception, $request) {
                  return $exception instanceof \Illuminate\Http\Client\ConnectionException;
               })
               ->get('https://gamma-api.polymarket.com/events', [
                  'limit' => $limit,
                  'closed' => false,
                  'offset' => $offset,
                  'ascending' => false,
               ]);

            if (!$response->successful()) {
               Log::error("Error fetching events: status " . $response->status() . " at offset " . $offset);
               if ($consecutiveEmptyBatches < 2) {
                  sleep(3);
                  continue;
               }
               break;
            }
         } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::warning("Connection timeout at offset {$offset}. Retrying... Error: " . $e->getMessage());
            if ($consecutiveEmptyBatches < 2) {
               sleep(5);
               continue;
            }
            $offset += $limit;
            $batchCount++;
            continue;
         } catch (\Exception $e) {
            Log::error("Unexpected error at offset {$offset}: " . $e->getMessage());
            $offset += $limit;
            $batchCount++;
            continue;
         }

         $events = $response->json();

         // Check if response is empty or not an array
         if (empty($events) || !is_array($events)) {
            $consecutiveEmptyBatches++;
            Log::info("Empty response at offset {$offset}. Consecutive empty batches: {$consecutiveEmptyBatches}");

            if ($consecutiveEmptyBatches >= $maxConsecutiveEmpty) {
               Log::info("No more events found after {$consecutiveEmptyBatches} consecutive empty batches. Total processed: {$totalProcessed}");
               break;
            }

            $offset += $limit;
            $batchCount++;
            continue;
         }

         // Reset consecutive empty counter if we got events
         $consecutiveEmptyBatches = 0;

         // Pre-fetch existing event slugs to reduce queries
         $eventSlugs = array_filter(array_column($events, 'slug'));
         $existingEvents = Event::whereIn('slug', $eventSlugs)->pluck('id', 'slug')->toArray();

         // Pre-fetch existing market slugs
         $allMarketSlugs = [];
         foreach ($events as $ev) {
            if (!empty($ev['markets'])) {
               foreach ($ev['markets'] as $mk) {
                  if (!empty($mk['slug'])) {
                     $allMarketSlugs[] = $mk['slug'];
                  }
               }
            }
         }
         $existingMarkets = !empty($allMarketSlugs)
            ? Market::whereIn('slug', $allMarketSlugs)->pluck('id', 'slug')->toArray()
            : [];

         // Pre-fetch existing tag slugs
         $allTagSlugs = [];
         foreach ($events as $ev) {
            if (!empty($ev['tags'])) {
               foreach ($ev['tags'] as $tag) {
                  if (!empty($tag['slug'])) {
                     $allTagSlugs[] = $tag['slug'];
                  }
               }
            }
         }
         $existingTags = !empty($allTagSlugs)
            ? Tag::whereIn('slug', $allTagSlugs)->pluck('id', 'slug')->toArray()
            : [];

         // Process events in smaller chunks (10 events per transaction)
         $eventChunks = array_chunk($events, 10);

         foreach ($eventChunks as $chunkIndex => $chunk) {
            try {
               DB::beginTransaction();

               foreach ($chunk as $ev) {
                  // Time check inside loop
                  if ((time() - $startTime) >= $maxExecutionTime) {
                     DB::commit();
                     Log::info("Time limit reached during processing. Processed {$totalProcessed} events.");
                     break 3; // Break out of all loops
                  }

                  try {
                     // Skip closed/archived/inactive events
                     if (isset($ev['closed']) && $ev['closed'] === true) {
                        $totalSkipped++;
                        continue;
                     }
                     if (isset($ev['archived']) && $ev['archived'] === true) {
                        $totalSkipped++;
                        continue;
                     }
                     if (isset($ev['active']) && $ev['active'] === false) {
                        $totalSkipped++;
                        continue;
                     }
                     if (empty($ev['markets'])) {
                        $totalSkipped++;
                        continue;
                     }

                     // Create/update event
                     $event = Event::updateOrCreate(
                        ['slug' => $ev['slug']],
                        [
                           'slug' => $ev['slug'],
                           'ticker' => $ev['ticker'] ?? null,
                           'polymarket_event_id' => $ev['id'] ?? null,
                           'title' => $ev['title'] ?? null,
                           'description' => $ev['description'] ?? null,
                           'image' => $ev['image'] ?? null,
                           'icon' => $ev['icon'] ?? null,
                           'liquidity' => $ev['liquidity'] ?? null,
                           'volume' => $ev['volume'] ?? null,
                           'volume_24hr' => $ev['volume24hr'] ?? null,
                           'volume_1wk' => $ev['volume1wk'] ?? null,
                           'volume_1mo' => $ev['volume1mo'] ?? null,
                           'volume_1yr' => $ev['volume1yr'] ?? null,
                           'liquidity_clob' => $ev['liquidityClob'] ?? null,
                           'competitive' => isset($ev['competitive']) ? floatval($ev['competitive']) : null,
                           'comment_count' => $ev['commentCount'] ?? 0,
                           'active' => $ev['active'] ?? true,
                           'closed' => $ev['closed'] ?? false,
                           'archived' => $ev['archived'] ?? false,
                           'new' => $ev['new'] ?? false,
                           'featured' => $ev['featured'] ?? false,
                           'start_date' => toMysqlDate($ev['startDate'] ?? null),
                           'end_date' => toMysqlDate($ev['endDate'] ?? null),
                        ]
                     );

                     // Update cache
                     $existingEvents[$ev['slug']] = $event->id;

                     // Process tags - Optimize to avoid N+1 queries
                     $tagIds = [];
                     if (!empty($ev['tags'])) {
                        $newTagSlugs = [];
                        foreach ($ev['tags'] as $tag) {
                           $tagSlug = $tag['slug'] ?? null;
                           if (!$tagSlug)
                              continue;

                           // Check cache first
                           if (isset($existingTags[$tagSlug])) {
                              $tagIds[] = $existingTags[$tagSlug];
                           } else {
                              // Collect new tags to create in bulk
                              $newTagSlugs[$tagSlug] = $tag['label'] ?? $tagSlug;
                           }
                        }
                        
                        // Bulk create new tags to avoid N+1 queries - Use insertOrIgnore for better performance
                        if (!empty($newTagSlugs)) {
                           // Prepare bulk insert data
                           $tagsToInsert = [];
                           foreach ($newTagSlugs as $tagSlug => $tagLabel) {
                              $tagsToInsert[] = [
                                 'slug' => $tagSlug,
                                 'label' => $tagLabel,
                                 'created_at' => now(),
                                 'updated_at' => now(),
                              ];
                           }
                           
                           // Bulk insert new tags (ignore duplicates)
                           if (!empty($tagsToInsert)) {
                              try {
                                 DB::table('tags')->insertOrIgnore($tagsToInsert);
                                 
                                 // Refresh cache with newly inserted tags
                                 $newTagIds = Tag::whereIn('slug', array_keys($newTagSlugs))
                                    ->pluck('id', 'slug')
                                    ->toArray();
                                 
                                 foreach ($newTagIds as $slug => $id) {
                                    $existingTags[$slug] = $id;
                                    $tagIds[] = $id;
                                 }
                              } catch (\Exception $e) {
                                 // Fallback to individual creates if bulk insert fails
                                 foreach ($newTagSlugs as $tagSlug => $tagLabel) {
                                    $tagModel = Tag::firstOrCreate(
                                       ['slug' => $tagSlug],
                                       ['label' => $tagLabel]
                                    );
                                    $existingTags[$tagSlug] = $tagModel->id;
                                    $tagIds[] = $tagModel->id;
                                 }
                              }
                           }
                        }
                        
                        // Batch sync tags
                        if (!empty($tagIds)) {
                           $event->tags()->sync($tagIds);
                        }
                     }

                     // Process markets
                     foreach ($ev['markets'] as $mk) {
                        try {
                           // Log outcomes extraction for debugging
                           if (empty($mk['outcomes']) || !is_array($mk['outcomes'])) {
                              Log::debug('Market missing or invalid outcomes', [
                                 'market_slug' => $mk['slug'] ?? 'unknown',
                                 'event_slug' => $ev['slug'] ?? 'unknown',
                                 'outcomes_received' => $mk['outcomes'] ?? 'null',
                                 'outcomes_type' => gettype($mk['outcomes'] ?? null),
                              ]);
                           }

                           // Determine outcome
                           [$outcomeResult, $finalOutcome, $finalResult] = $this->determineMarketOutcome($mk);

                           // Determine if market should be closed
                           $isClosed = ($mk['closed'] ?? false) || (bool) $outcomeResult;

                           // Process outcomes - ensure it's properly formatted as JSON array
                           $outcomes = $mk['outcomes'] ?? null;
                           if ($outcomes !== null) {
                              // If it's already a JSON string, decode and re-encode to ensure proper format
                              if (is_string($outcomes)) {
                                 $decoded = json_decode($outcomes, true);
                                 $outcomes = $decoded !== null ? $decoded : null;
                              }
                              // If it's an array, keep it as is (will be auto-encoded to JSON by Laravel)
                              if (!is_array($outcomes) || empty($outcomes)) {
                                 // Fallback to default Yes/No if invalid or empty
                                 $outcomes = ['Yes', 'No'];
                              }
                           } else {
                              // If outcomes not provided, use default
                              $outcomes = ['Yes', 'No'];
                           }

                           // Process outcome_prices - ensure it's properly formatted
                           $outcomePrices = $mk['outcomePrices'] ?? null;
                           if ($outcomePrices !== null) {
                              // If it's already a JSON string, decode and re-encode to ensure proper format
                              if (is_string($outcomePrices)) {
                                 $decoded = json_decode($outcomePrices, true);
                                 $outcomePrices = $decoded !== null ? $decoded : null;
                              }
                              // If it's an array, keep it as is
                              if (!is_array($outcomePrices) || empty($outcomePrices)) {
                                 $outcomePrices = null;
                              }
                           }

                           // First, create or get the market (without volume/liquidity to preserve internal data)
                           $market = Market::updateOrCreate(
                              ['slug' => $mk['slug']],
                              [
                                 'event_id' => $event->id,
                                 'question' => $mk['question'] ?? null,
                                 'condition_id' => $mk['conditionId'] ?? null,
                                 'groupItem_title' => $mk['groupItemTitle'] ?? null,
                                 'group_item_threshold' => $mk['groupItemThreshold'] ?? null,
                                 'description' => $mk['description'] ?? null,
                                 'resolution_source' => $mk['resolutionSource'] ?? null,
                                 'image' => cleanImageUrl($mk['image'] ?? null),
                                 'icon' => cleanImageUrl($mk['icon'] ?? null),
                                 'liquidity_clob' => $mk['liquidityClob'] ?? $mk['liquidityNum'] ?? null,
                                 'volume1wk' => $mk['volume1wk'] ?? $mk['volume1wkClob'] ?? null,
                                 'volume1mo' => $mk['volume1mo'] ?? $mk['volume1moClob'] ?? null,
                                 'volume1yr' => $mk['volume1yr'] ?? $mk['volume1yrClob'] ?? null,
                                 'outcome_prices' => $outcomePrices,
                                 'outcomes' => $outcomes, // Now properly formatted array
                                 'best_bid' => isset($mk['bestBid']) ? floatval($mk['bestBid']) : null,
                                 'best_ask' => isset($mk['bestAsk']) ? floatval($mk['bestAsk']) : null,
                                 'last_trade_price' => isset($mk['lastTradePrice']) ? floatval($mk['lastTradePrice']) : null,
                                 'spread' => isset($mk['spread']) ? floatval($mk['spread']) : null,
                                 'one_day_price_change' => isset($mk['oneDayPriceChange']) ? floatval($mk['oneDayPriceChange']) : null,
                                 'one_week_price_change' => isset($mk['oneWeekPriceChange']) ? floatval($mk['oneWeekPriceChange']) : null,
                                 'one_month_price_change' => isset($mk['oneMonthPriceChange']) ? floatval($mk['oneMonthPriceChange']) : null,
                                 'series_color' => $mk['seriesColor'] ?? null,
                                 'competitive' => isset($mk['competitive']) ? floatval($mk['competitive']) : null,
                                 'active' => $mk['active'] ?? true,
                                 'closed' => $isClosed,
                                 'archived' => $mk['archived'] ?? false,
                                 'featured' => $mk['featured'] ?? false,
                                 'new' => $mk['new'] ?? false,
                                 'restricted' => $mk['restricted'] ?? false,
                                 'approved' => $mk['approved'] ?? true,
                                 'outcome_result' => $outcomeResult,
                                 'final_outcome' => $finalOutcome,
                                 'final_result' => $finalResult,
                                 'result_set_at' => ($outcomeResult && !isset($mk['result_set_at'])) ? now() : ($mk['result_set_at'] ?? null),
                                 'is_closed' => $isClosed,
                                 'settled' => false,
                                 'start_date' => toMysqlDate($mk['startDate'] ?? null),
                                 'end_date' => toMysqlDate($mk['endDate'] ?? null),
                              ]
                           );

                           // Sync outcomes to outcomes table (new system)
                           // This creates Outcome models from the outcomes array
                           $market->syncOutcomesFromApi($outcomes);

                           // Update API data while preserving internal trade data
                           // This ensures user-generated volume/liquidity is never lost
                           $apiData = [
                              'volume' => $mk['volume'] ?? $mk['volumeNum'] ?? null,
                              'liquidity' => $mk['liquidity'] ?? $mk['liquidityNum'] ?? null,
                              'volume24hr' => $mk['volume24hr'] ?? $mk['volume24hrClob'] ?? null,
                           ];
                           $market->updateApiDataPreservingInternal($apiData);

                           // Update cache
                           $existingMarkets[$mk['slug']] = $market->id;

                           // CRITICAL: If market has a result from API, mark winning outcome and queue for settlement
                           if ($outcomeResult && $isClosed && !$market->settled) {
                              // Find winning outcome by name
                              $winningOutcome = null;
                              
                              // Try to find by exact name match
                              $winningOutcome = $market->getOutcomeByName($finalOutcome);
                              
                              // If not found, try by outcome result (yes/no)
                              if (!$winningOutcome) {
                                 if (strtolower($outcomeResult) === 'yes') {
                                    $winningOutcome = $market->getOutcomeByName('Yes');
                                 } elseif (strtolower($outcomeResult) === 'no') {
                                    $winningOutcome = $market->getOutcomeByName('No');
                                 }
                              }
                              
                              // If still not found, try matching by index in outcomes array
                              if (!$winningOutcome && is_array($outcomes)) {
                                 foreach ($outcomes as $index => $outcomeName) {
                                    if (strcasecmp($outcomeName, $finalOutcome) === 0 || 
                                        strcasecmp($outcomeName, $outcomeResult) === 0) {
                                       $winningOutcome = $market->getOutcomeByName($outcomeName);
                                       break;
                                    }
                                 }
                              }
                              
                              if ($winningOutcome) {
                                 // Mark as winning outcome
                                 $market->outcomes()->update(['is_winning' => false]);
                                 $winningOutcome->is_winning = true;
                                 $winningOutcome->save();
                                 
                                 // Ensure market is closed and locked
                                 $market->closed = true;
                                 $market->is_closed = true;
                                 $market->save();
                                 
                                 // Queue for automatic settlement
                                 $marketsToSettle[] = $market->id;
                                 
                                 Log::info("Market resolved from API - queued for automatic settlement", [
                                    'market_id' => $market->id,
                                    'winning_outcome_id' => $winningOutcome->id,
                                    'winning_outcome_name' => $winningOutcome->name,
                                    'outcome_result' => $outcomeResult,
                                 ]);
                              } else {
                                 Log::warning("Market has result but winning outcome not found", [
                                    'market_id' => $market->id,
                                    'outcome_result' => $outcomeResult,
                                    'final_outcome' => $finalOutcome,
                                    'available_outcomes' => $market->outcomes()->pluck('name')->toArray(),
                                 ]);
                              }
                           }
                        } catch (\Exception $e) {
                           Log::error('Market processing failed', [
                              'event_slug' => $ev['slug'],
                              'market_slug' => $mk['slug'] ?? 'unknown',
                              'error' => $e->getMessage()
                           ]);
                        }
                     }

                     $totalProcessed++;
                  } catch (\Exception $e) {
                     $totalFailed++;
                     Log::error('Event processing failed', [
                        'slug' => $ev['slug'] ?? 'unknown',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                     ]);
                  }
               }

               DB::commit();
            } catch (\Exception $e) {
               DB::rollBack();
               Log::error("Error processing events chunk #{$chunkIndex}: " . $e->getMessage(), [
                  'offset' => $offset,
                  'batch' => $batchCount,
                  'chunk' => $chunkIndex,
                  'exception' => $e->getMessage()
               ]);
            }
         }

         // Batch settlement operations (settle every 50 markets)
         if (count($marketsToSettle) >= 50) {
            try {
               $settlementService = app(\App\Services\SettlementService::class);
               $settledCount = 0;
               $failedCount = 0;

               foreach ($marketsToSettle as $marketId) {
                  try {
                     $settlementService->settleMarket($marketId);
                     $settledCount++;
                  } catch (\Exception $e) {
                     $failedCount++;
                     Log::error('Failed to auto-settle market after API update', [
                        'market_id' => $marketId,
                        'error' => $e->getMessage(),
                     ]);
                  }
               }

               Log::info('Batch settlement completed', [
                  'total' => count($marketsToSettle),
                  'success' => $settledCount,
                  'failed' => $failedCount
               ]);

               $marketsToSettle = []; // Reset for next batch
            } catch (\Exception $e) {
               Log::error('Error in batch settlement: ' . $e->getMessage());
            }
         }

         $eventsCount = count($events);
         Log::info("[Batch #{$batchCount}] Fetched {$eventsCount} events from offset {$offset}", [
            'processed' => $totalProcessed,
            'skipped' => $totalSkipped,
            'failed' => $totalFailed,
            'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
         ]);

         // If we got less than the limit, we might be near the end
         if ($eventsCount < $limit) {
            Log::info("Received less than limit ({$eventsCount} < {$limit}). May be near end of available events.");
         }

         $offset += $limit;
         $batchCount++;

         // Small delay between batches to avoid rate limit
         if ($batchCount < $maxBatches) {
            usleep(200000); // 200 ms
         }
      }

      // Settle remaining markets
      if (!empty($marketsToSettle)) {
         try {
            $settlementService = app(\App\Services\SettlementService::class);
            $settledCount = 0;
            $failedCount = 0;

            foreach ($marketsToSettle as $marketId) {
               try {
                  $settlementService->settleMarket($marketId);
                  $settledCount++;
               } catch (\Exception $e) {
                  $failedCount++;
                  Log::error('Failed to settle remaining market', [
                     'market_id' => $marketId,
                     'error' => $e->getMessage(),
                  ]);
               }
            }

            Log::info('Final settlement completed', [
               'total' => count($marketsToSettle),
               'success' => $settledCount,
               'failed' => $failedCount
            ]);
         } catch (\Exception $e) {
            Log::error('Error in final settlement: ' . $e->getMessage());
         }
      }

      $executionTime = time() - $startTime;
      $message = "=== Event Fetch Summary ===\n";
      $message .= "Total Processed: {$totalProcessed} events\n";
      $message .= "Total Skipped: {$totalSkipped} events\n";
      $message .= "Total Failed: {$totalFailed} events\n";
      $message .= "Batches Completed: {$batchCount}\n";
      $message .= "Execution Time: {$executionTime} seconds\n";
      $message .= "Peak Memory: " . round(memory_get_peak_usage(true) / 1024 / 1024, 2) . " MB\n";
      if ($batchCount >= $maxBatches) {
         $message .= "Reached max batches limit ({$maxBatches})\n";
      }
      $message .= "Next batch will continue from offset {$offset}";

      Log::info($message);
      return $message;
   }

   /**
    * Set final result for a market and settle trades
    * Supports both outcome_id (new system) and outcome name (legacy)
    */
   public function setResult(Request $request, $id)
   {
      $request->validate([
         'outcome_id' => ['nullable', 'integer', 'exists:outcomes,id'],
         'outcome_name' => ['nullable', 'string'],
         'final_result' => ['nullable', Rule::in(['yes', 'no'])], // Legacy support
      ]);

      try {
         $market = Market::findOrFail($id);

         $winningOutcome = null;

         // Priority 1: Use outcome_id (new system)
         if ($request->outcome_id) {
            $winningOutcome = \App\Models\Outcome::where('id', $request->outcome_id)
               ->where('market_id', $market->id)
               ->first();
            
            if (!$winningOutcome) {
               return response()->json([
                  'success' => false,
                  'message' => 'Invalid outcome_id for this market',
               ], 400);
            }
         }
         // Priority 2: Use outcome_name
         elseif ($request->outcome_name) {
            $winningOutcome = $market->getOutcomeByName($request->outcome_name);
            
            if (!$winningOutcome) {
               return response()->json([
                  'success' => false,
                  'message' => 'Invalid outcome_name for this market',
               ], 400);
            }
         }
         // Priority 3: Legacy support - use final_result (yes/no)
         elseif ($request->final_result) {
            $result = strtolower($request->final_result);
            $winningOutcome = $market->getOutcomeByName($result === 'yes' ? 'Yes' : 'No');
            
            if (!$winningOutcome) {
               // Fallback: create Yes/No outcomes if they don't exist
               $market->syncOutcomesFromApi(['Yes', 'No']);
               $winningOutcome = $market->getOutcomeByName($result === 'yes' ? 'Yes' : 'No');
            }
         }

         if (!$winningOutcome) {
            return response()->json([
               'success' => false,
               'message' => 'No valid outcome specified. Provide outcome_id, outcome_name, or final_result',
            ], 400);
         }

         // Mark this outcome as winning
         // Unmark all other outcomes
         $market->outcomes()->update(['is_winning' => false]);
         $winningOutcome->is_winning = true;
         $winningOutcome->save();

         // Set legacy result fields for backward compatibility
         $result = strtolower($winningOutcome->name);
         $market->final_result = $result;
         $market->outcome_result = $result;
         $market->final_outcome = strtoupper($winningOutcome->name);
         $market->result_set_at = now();
         $market->closed = true;
         $market->is_closed = true;
         $market->settled = false; // Will be set to true after settlement
         $market->save();

         // CRITICAL: Automatically settle all pending trades immediately
         // This ensures payouts are processed instantly when market is resolved
         $settlementService = new SettlementService();
         $settlementResult = $settlementService->settleMarket($market->id);
         
         if (!$settlementResult) {
             Log::warning("Automatic settlement failed after market resolution", [
                 'market_id' => $market->id,
                 'winning_outcome_id' => $winningOutcome->id,
             ]);
         }

         return response()->json([
            'success' => true,
            'message' => 'Market result set and trades settled successfully',
            'winning_outcome' => [
               'id' => $winningOutcome->id,
               'name' => $winningOutcome->name,
            ],
            'settlement' => $settlementResult,
         ]);
      } catch (\Exception $e) {
         Log::error('Failed to set market result', [
            'market_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
         ]);

         return response()->json([
            'success' => false,
            'message' => 'Failed to set result: ' . $e->getMessage(),
         ], 500);
      }
   }

   /**
    * Manually settle trades for a market (if result already set)
    */
   public function settleTrades($id)
   {
      try {
         $market = Market::findOrFail($id);

         if (!$market->hasResult()) {
            return response()->json([
               'success' => false,
               'message' => 'Market does not have a final result yet',
            ], 400);
         }

         $settlementService = new SettlementService();
         $result = $settlementService->settleMarket($market);

         return response()->json([
            'success' => true,
            'message' => 'Trades settled successfully',
            'settlement' => $result,
         ]);
      } catch (\Exception $e) {
         Log::error('Failed to settle trades', [
            'market_id' => $id,
            'error' => $e->getMessage(),
         ]);

         return response()->json([
            'success' => false,
            'message' => 'Failed to settle trades: ' . $e->getMessage(),
         ], 500);
      }
   }
}

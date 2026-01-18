<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Tag;
use App\Models\Event;
use App\Models\EventComment;
use App\Models\Market;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
   function index()
   {
      return view('frontend.home');
   }

   /**
    * Fetch comments from Polymarket API for an event
    */
   public function fetchEventComments($eventId)
   {
      try {
         $event = Event::findOrFail($eventId);

         // Use Polymarket event ID from the event
         $polymarketEventId = $event->polymarket_event_id ?? $event->id;

         // Fetch comments from Polymarket API
         // API: https://gamma-api.polymarket.com/comments
         // Parameters: parent_entity_type (Event/Series/market), parent_entity_id (integer)
         // Reduced timeout from 30s to 5s and limit from 100 to 50 for better performance
         $response = Http::timeout(5)
            ->get('https://gamma-api.polymarket.com/comments', [
               'parent_entity_type' => 'Event',
               'parent_entity_id' => (int) $polymarketEventId, // Ensure it's an integer
               'limit' => 50, // Reduced from 100 to 50
               'offset' => 0,
               'order' => 'createdAt',
               'ascending' => false,
            ]);

         if ($response->successful()) {
            $comments = $response->json();

            // Handle both array and object responses
            if (is_array($comments) && count($comments) > 0) {
               $this->syncPolymarketComments($event, $comments);

               return response()->json([
                  'success' => true,
                  'comments' => $comments,
                  'count' => count($comments),
                  'synced' => true,
               ]);
            } elseif (is_object($comments) && isset($comments->data) && is_array($comments->data) && count($comments->data) > 0) {
               // Handle object response with data property
               $this->syncPolymarketComments($event, $comments->data);

               return response()->json([
                  'success' => true,
                  'comments' => $comments->data,
                  'count' => count($comments->data),
                  'synced' => true,
               ]);
            } else {
               // Empty response - log for debugging
               Log::info('Polymarket comments API returned empty', [
                  'event_id' => $event->id,
                  'polymarket_event_id' => $polymarketEventId,
                  'response_type' => gettype($comments),
               ]);
            }
         } else {
            // Log API error for debugging
            Log::warning('Polymarket comments API error', [
               'status' => $response->status(),
               'body' => $response->body(),
               'event_id' => $event->id,
               'polymarket_event_id' => $polymarketEventId,
            ]);
         }

         return response()->json([
            'success' => true,
            'comments' => [],
            'count' => 0,
         ]);
      } catch (\Exception $e) {
         Log::error('Failed to fetch Polymarket comments: ' . $e->getMessage());

         return response()->json([
            'success' => false,
            'message' => 'Failed to fetch comments',
         ], 500);
      }
   }

   /**
    * Sync Polymarket comments to database
    */
   private function syncPolymarketComments(Event $event, array $polymarketComments)
   {
      $syncedCount = 0;

      // Optimize: Load all parent comments at once to avoid N+1 queries
      $parentCommentIds = array_filter(array_column($polymarketComments, 'parentCommentID'));
      $parentComments = [];
      if (!empty($parentCommentIds)) {
         $parentComments = EventComment::whereIn('polymarket_id', $parentCommentIds)
            ->pluck('id', 'polymarket_id')
            ->toArray();
      }

      foreach ($polymarketComments as $pmComment) {
         try {
            $parentCommentId = null;

            // Find parent comment if this is a reply (from pre-loaded array)
            if (!empty($pmComment['parentCommentID']) && isset($parentComments[$pmComment['parentCommentID']])) {
               $parentCommentId = $parentComments[$pmComment['parentCommentID']];
            }

            // Find or create user by address (you may need to map addresses to users)
            $userId = null;
            if (!empty($pmComment['userAddress'])) {
               // Try to find user by wallet address or create a guest user
               // This depends on your user system
               // You can implement user mapping here if needed
            }

            // Extract profile data safely
            $profileData = null;
            if (isset($pmComment['profile']) && is_array($pmComment['profile'])) {
               $profileData = $pmComment['profile'];
            } elseif (isset($pmComment['profile']) && is_object($pmComment['profile'])) {
               $profileData = json_decode(json_encode($pmComment['profile']), true);
            }

            // Extract reactions safely
            $reactions = [];
            if (isset($pmComment['reactions']) && is_array($pmComment['reactions'])) {
               $reactions = $pmComment['reactions'];
            }

            EventComment::updateOrCreate(
               ['polymarket_id' => $pmComment['id']],
               [
                  'event_id' => $event->id,
                  'user_id' => $userId,
                  'comment_text' => $pmComment['body'] ?? '',
                  'parent_comment_id' => $parentCommentId,
                  'user_address' => $pmComment['userAddress'] ?? null,
                  'reply_address' => $pmComment['replyAddress'] ?? null,
                  'parent_entity_type' => $pmComment['parentEntityType'] ?? null,
                  'parent_entity_id' => isset($pmComment['parentEntityID']) ? (int) $pmComment['parentEntityID'] : null,
                  'parent_comment_polymarket_id' => $pmComment['parentCommentID'] ?? null,
                  'reactions' => $reactions,
                  'reaction_count' => isset($pmComment['reactionCount']) ? (int) $pmComment['reactionCount'] : 0,
                  'report_count' => isset($pmComment['reportCount']) ? (int) $pmComment['reportCount'] : 0,
                  'profile_data' => $profileData,
                  'replies_count' => count($pmComment['replies'] ?? []),
                  'likes_count' => isset($pmComment['reactionCount']) ? (int) $pmComment['reactionCount'] : 0,
                  'is_active' => true,
                  'created_at' => !empty($pmComment['createdAt']) ? Carbon::parse($pmComment['createdAt']) : now(),
                  'updated_at' => !empty($pmComment['updatedAt']) ? Carbon::parse($pmComment['updatedAt']) : now(),
               ]
            );

            $syncedCount++;
         } catch (\Exception $e) {
            Log::error('Failed to sync Polymarket comment', [
               'comment_id' => $pmComment['id'] ?? 'unknown',
               'error' => $e->getMessage(),
            ]);
         }
      }

      Log::info("Synced {$syncedCount} comments from Polymarket for event {$event->id}");
   }

   function marketDetails($slug)
   {
      try {
         // Cache key for this specific event
         $cacheKey = "event_details:{$slug}:" . auth()->id();
         $cacheTTL = 60; // Cache for 1 minute
         
         // Try to get from cache first (for non-comment data)
         $event = \Illuminate\Support\Facades\Cache::remember(
            $cacheKey,
            $cacheTTL,
            function () use ($slug) {
               // Optimized: Eager load all relationships to prevent N+1 queries (59 → ~8 queries)
               return Event::where('slug', $slug)
                  ->with([
                     'markets' => function ($query) {
                        $query->select([
                           'id', 'event_id', 'question', 'slug', 'description', 
                           'outcome_prices', 'outcomes', 'active', 'closed', 'featured',
                           'groupItem_title', 'series_color', 'icon', 'volume', 
                           'volume24hr', 'volume1wk', 'volume1mo', 'best_bid', 'best_ask',
                           'last_trade_price', 'one_day_price_change', 'one_week_price_change',
                           'one_month_price_change', 'created_at', 'updated_at'
                        ])
                        ->where('active', true)
                        ->where('closed', false);
                        // No orderBy here, we'll sort by price after loading
                     },
                     'secondaryCategory' => function($query) {
                        $query->select('id', 'name', 'slug', 'icon', 'main_category');
                     },
                     'tags' => function($query) {
                        $query->select('tags.id', 'tags.label', 'tags.slug');
                     }
                  ])
                  ->firstOrFail();
            }
         );
         
         // Load comments separately (don't cache - needs to be fresh)
         if (!$event->relationLoaded('comments')) {
            $event->load([
               'comments' => function ($query) {
                  $query->select(['id', 'event_id', 'user_id', 'comment_text', 'created_at', 'updated_at'])
                        ->with(['user' => function($q) {
                           $q->select('id', 'name', 'avatar');
                        }])
                        ->latest()
                        ->limit(50);
               }
            ]);
         }
         
         // Load saved status for authenticated user (don't cache - user-specific)
         if (auth()->check() && !$event->relationLoaded('savedByUsers')) {
            $event->load([
               'savedByUsers' => function($query) {
                  $query->where('user_id', auth()->id())
                        ->select('users.id');
               }
            ]);
         }

         // If event has only 1 active market, redirect to single market page
         $activeMarkets = $event->markets->filter(function($market) {
            return $market->active && !$market->closed;
         });
         
         if ($activeMarkets->count() === 1) {
            $singleMarket = $activeMarkets->first();
            if ($singleMarket->slug) {
               return redirect()->route('market.single', $singleMarket->slug);
            } else {
               // If no slug, use market ID
               return redirect()->route('market.single', $singleMarket->id);
            }
         }

         // Color palette for markets (Polymarket style) - fallback if series_color not set
         $marketColors = [
            '#ff7b2c', // Orange
            '#4c8df5', // Blue
            '#9cdbff', // Light Blue
            '#ffe04d', // Yellow
            '#ff6b9d', // Pink
            '#4ecdc4', // Teal
            '#a8e6cf', // Green
            '#ff8b94', // Coral
         ];

      // Prepare seriesData for chart
      $seriesData = [];
      $labels = [];
      
      // Use current time (not rounded to start of day)
      $now = now();
      
      // Use event creation time as base
      $baseTime = $event->created_at ? \Carbon\Carbon::parse($event->created_at)->startOfDay() : $now->copy()->subDays(30);
      $startDate = $event->start_date ? \Carbon\Carbon::parse($event->start_date)->startOfDay() : $baseTime;
      
      // If event is very new, ensure we have enough time range
      if ($baseTime->diffInDays($now) < 7) {
         $baseTime = $now->copy()->subDays(30);
      }

      // Generate time labels (x-axis) - calculate points based on time range
      $daysDiff = max(7, $baseTime->diffInDays($now));
      
      // For long periods (>60 days), show months. For shorter, show dates
      if ($daysDiff > 60) {
         $maxPoints = min(12, ceil($daysDiff / 30)); // Show months
         $interval = max(1, ceil($daysDiff / $maxPoints));
         $labelFormat = 'M'; // Just month name (Jan, Feb, etc)
      } else {
         $maxPoints = min(30, $daysDiff); // Show dates
         $interval = max(1, ceil($daysDiff / $maxPoints));
         $labelFormat = 'M d'; // Month and day
      }
      
      $timeLabels = [];
      $allTimes = [];
      $currentTime = $baseTime->copy();
      $pointCount = 0;

      // Generate labels from baseTime to now
      while ($currentTime <= $now && $pointCount < $maxPoints) {
         $allTimes[] = $currentTime->copy();
         $timeLabels[] = $currentTime->format($labelFormat);
         $currentTime->addDays($interval);
         $pointCount++;
      }
      
      // Ensure the last point is current date/time
      if (count($allTimes) > 0) {
         $lastTime = $allTimes[count($allTimes) - 1];
         // If last time is not today, add current time
         if ($lastTime->diffInDays($now) >= 1) {
            $allTimes[] = $now->copy();
            $timeLabels[] = $now->format('M d');
         }
      }

      // If no labels generated, create default ones
      if (empty($timeLabels)) {
         for ($i = 0; $i <= 6; $i++) {
            $time = $baseTime->copy()->addDays($i);
            if ($time > $now) {
               break;
            }
            $timeLabels[] = $time->format('M d');
            $allTimes[] = $time;
         }
      }

      $labels = $timeLabels;

         // Sort markets by price (highest first)
         $allMarkets = $event->markets->sortByDesc(function($market) {
            // Get YES price (outcome_prices[1])
            if ($market->outcome_prices) {
               $prices = is_string($market->outcome_prices) 
                   ? json_decode($market->outcome_prices, true) 
                   : ($market->outcome_prices ?? []);
               
               if (is_array($prices) && isset($prices[1])) {
                  return floatval($prices[1]) * 100; // YES price
               }
            }
            
            // Use best_ask if available
            if ($market->best_ask !== null && $market->best_ask > 0) {
               return floatval($market->best_ask) * 100;
            }
            
            return 0; // Default
         })->values(); // Reset keys after sorting
         
         // Generate series data for ALL markets (not just first 4)
         $allSeriesData = [];
         
         foreach ($allMarkets as $index => $market) {
            // Get current price - use YES price (prices[1]) for chart display
            $currentPrice = 50;
            if ($market->outcome_prices) {
               // Handle both string (JSON) and array formats
               $prices = is_string($market->outcome_prices) 
                   ? json_decode($market->outcome_prices, true) 
                   : ($market->outcome_prices ?? []);
               // Fix: prices[0] = NO, prices[1] = YES (Polymarket format)
               // Use YES price (prices[1]) for chart, or best_ask if available
               if (is_array($prices)) {
                  if (isset($prices[1])) {
                     $currentPrice = floatval($prices[1]) * 100; // YES price
                  } elseif (isset($prices[0])) {
                     // Fallback: convert NO price to YES (1 - NO)
                     $currentPrice = (1 - floatval($prices[0])) * 100;
                  }
               }
            }

            // Use best_ask if available (more accurate from order book)
            if ($market->best_ask !== null && $market->best_ask > 0) {
               $currentPrice = floatval($market->best_ask) * 100;
            }

         // Generate historical data points matching the labels
         $basePrice = $currentPrice;
         $priceVariation = min(20, abs($basePrice - 50));
         $dataPoints = [];
         $priceDirections = [];

         // Use market ID as seed for consistent pattern across reloads
         $seed = $market->id ?? ($index + 1);
         $totalPoints = count($allTimes);
         $previousPrice = null;

         foreach ($allTimes as $timeIndex => $time) {
            if ($time < $startDate) {
               $dataPoints[] = null;
               $priceDirections[] = 'neutral';
               continue;
            }

            // Progressive price trend from 50% to current price
            $progress = $totalPoints > 1 ? ($timeIndex / ($totalPoints - 1)) : 1;
            $targetPrice = 50 + ($basePrice - 50) * $progress;

            // Deterministic wave pattern based on market ID and time index
            $wave1 = sin(($seed * 3.14159 + $timeIndex * 0.8) / 2) * $priceVariation * 0.3;
            $wave2 = cos(($seed * 2.71828 + $timeIndex * 0.5) / 3) * $priceVariation * 0.2;
            
            $volatility = $wave1 + $wave2;
            $price = $targetPrice + $volatility;
            
            // Clamp between 1 and 99
            $price = max(1, min(99, $price));
            $price = round($price, 1);

            // Determine direction (up, down, or neutral)
            $direction = 'neutral';
            if ($previousPrice !== null) {
               if ($price > $previousPrice) {
                  $direction = 'up';
               } elseif ($price < $previousPrice) {
                  $direction = 'down';
               }
            }

            $dataPoints[] = $price;
            $priceDirections[] = $direction;
            $previousPrice = $price;
         }

         // Ensure last point is exactly current price
         if (count($dataPoints) > 0) {
            $dataPoints[count($dataPoints) - 1] = round($currentPrice, 1);
         }

            // Format name with current price percentage
            $priceText = $currentPrice < 1 ? '<1%' : ($currentPrice >= 99 ? '>99%' : round($currentPrice, 1) . '%');
            $marketName = $market->question; // Full market question/title
            $marketNameShort = strlen($marketName) > 40 ? substr($marketName, 0, 37) . '...' : $marketName;

            // Use series_color from database if available, otherwise use color palette
            $marketColor = $market->series_color ?? $marketColors[$index % count($marketColors)];

            // Ensure color is not empty and has # prefix
            if (empty($marketColor) || trim($marketColor) === '') {
               $marketColor = $marketColors[$index % count($marketColors)];
            }
            if (!str_starts_with($marketColor, '#')) {
               $marketColor = '#' . $marketColor;
            }

            // Use groupItem_title if available, otherwise use market question
            $displayTitle = $market->groupItem_title ?? $marketName;
            
            // Store all market data
            $allSeriesData[] = [
               'id' => $market->id, // Market ID
               'market_id' => $market->id, // Market ID (duplicate for compatibility)
               'name' => $displayTitle . ' ' . $priceText, // Display name with price
               'full_name' => $displayTitle, // Full display title
               'question' => $marketName, // Original market question
               'groupItem_title' => $market->groupItem_title ?? null, // Group item title
               'price_text' => $priceText, // Price percentage
               'color' => $marketColor,
               'data' => $dataPoints,
               'directions' => $priceDirections, // Price movement directions
               'icon' => $market->icon ?? null,
               'volume' => $market->volume ?? 0,
               'volume24hr' => $market->volume24hr ?? 0,
               'volume1wk' => $market->volume1wk ?? 0,
               'volume1mo' => $market->volume1mo ?? 0,
               'best_bid' => $market->best_bid,
               'best_ask' => $market->best_ask,
               'last_trade_price' => $market->last_trade_price,
               'one_day_price_change' => $market->one_day_price_change,
               'one_week_price_change' => $market->one_week_price_change,
               'one_month_price_change' => $market->one_month_price_change,
            ];
         }

         // Send ALL market data to frontend (user will select which ones to display)
         $seriesData = $allSeriesData;

         // Response with cache headers for browser caching
         return response()
            ->view('frontend.market_details', compact('event', 'seriesData', 'labels'))
            ->header('Cache-Control', 'public, max-age=60'); // Cache in browser for 1 minute
      } catch (\Illuminate\Database\QueryException $e) {
         \Log::error('Database connection failed in marketDetails: ' . $e->getMessage());
         return redirect()->route('home')
            ->with('error', 'Unable to load market details. Please try again later.');
      } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
         \Log::warning('Event not found: ' . $slug);
         return redirect()->route('home')
            ->with('error', 'Event not found.');
      } catch (\Exception $e) {
         \Log::error('Error in marketDetails: ' . $e->getMessage());
         return redirect()->route('home')
            ->with('error', 'An error occurred. Please try again later.');
      }
   }

   /**
    * Get chart data for a specific time period (AJAX endpoint)
    */
   function getChartDataByPeriod($slug, Request $request)
   {
      try {
         $period = $request->get('period', 'all');
         
         // Get selected market IDs from request (comma-separated string)
         $marketIdsString = $request->get('market_ids', '');
         $selectedMarketIds = [];
         
         \Log::info('Chart data request', [
            'period' => $period,
            'market_ids_string' => $marketIdsString,
            'slug' => $slug
         ]);
         
         if (!empty($marketIdsString)) {
            // Parse comma-separated IDs
            $selectedMarketIds = array_filter(
               array_map('intval', explode(',', $marketIdsString)),
               function($id) { return $id > 0; }
            );
            \Log::info('Parsed selected market IDs:', ['ids' => $selectedMarketIds, 'count' => count($selectedMarketIds)]);
         } else {
            \Log::info('No market IDs specified, will return all markets');
         }
         
         // Load event with markets
         $event = Event::where('slug', $slug)
            ->with([
               'markets' => function ($query) {
                  $query->where('active', true)
                        ->where('closed', false);
                  // No limit or orderBy here, we'll sort by price after loading
               }
            ])
            ->firstOrFail();

         // Generate chart data based on period and selected markets
         $chartData = $this->generateChartDataForPeriod($event, $period, $selectedMarketIds);

         \Log::info('Returning chart data', [
            'series_count' => count($chartData['series'] ?? []),
            'labels_count' => count($chartData['labels'] ?? []),
            'series_ids' => collect($chartData['series'] ?? [])->pluck('id')->toArray()
         ]);

         return response()->json([
            'success' => true,
            'data' => $chartData
         ]);
      } catch (\Exception $e) {
         \Log::error('Error in getChartDataByPeriod: ' . $e->getMessage());
         return response()->json([
            'success' => false,
            'error' => 'Failed to load chart data'
         ], 500);
      }
   }

   /**
    * Generate chart data for specific time period
    * @param array $selectedMarketIds Optional array of market IDs to filter
    */
   private function generateChartDataForPeriod($event, $period, $selectedMarketIds = [])
   {
      $now = now();
      $marketColors = [
         '#ff7b2c', '#4c8df5', '#9cdbff', '#ffe04d',
         '#ff6b9d', '#4ecdc4', '#a8e6cf', '#ff8b94'
      ];

      // Determine time range and interval based on period
      switch ($period) {
         case '1h':
            $startTime = $now->copy()->subHour();
            $endTime = $now->copy();
            $interval = 5; // 5-minute intervals
            $intervalType = 'minutes';
            $labelFormat = 'H:i';
            $maxPoints = 12;
            break;
         case '6h':
            $startTime = $now->copy()->subHours(6);
            $endTime = $now->copy();
            $interval = 30; // 30-minute intervals
            $intervalType = 'minutes';
            $labelFormat = 'H:i';
            $maxPoints = 12;
            break;
         case '1d':
            $startTime = $now->copy()->subDay();
            $endTime = $now->copy();
            $interval = 1; // Hourly
            $intervalType = 'hours';
            $labelFormat = 'H:i';
            $maxPoints = 24;
            break;
         case '1w':
            $startTime = $now->copy()->subWeek();
            $endTime = $now->copy();
            $interval = 1; // Daily
            $intervalType = 'days';
            $labelFormat = 'M d';
            $maxPoints = 7;
            break;
         case '1m':
            $startTime = $now->copy()->subMonth();
            $endTime = $now->copy();
            $interval = 1; // Daily
            $intervalType = 'days';
            $labelFormat = 'M d';
            $maxPoints = 30;
            break;
         case 'all':
         default:
            // From market creation to now
            $createdAt = $event->created_at ? \Carbon\Carbon::parse($event->created_at) : $now->copy()->subDays(30);
            $startTime = $createdAt->copy()->startOfDay();
            $endTime = $now->copy();
            $daysDiff = max(7, $startTime->diffInDays($endTime));
            
            // For long periods (>30 days), show months. For shorter, show dates
            if ($daysDiff > 30) {
               $maxPoints = min(12, ceil($daysDiff / 30)); // Show months
               $interval = max(1, ceil($daysDiff / $maxPoints));
               $intervalType = 'days';
               $labelFormat = 'M'; // Just month name (Jan, Feb, etc)
            } else {
               $maxPoints = min(30, $daysDiff); // Show dates
               $interval = max(1, ceil($daysDiff / $maxPoints));
               $intervalType = 'days';
               $labelFormat = 'M d'; // Month and day
            }
            break;
      }

      // Generate time labels from start to end
      $labels = [];
      $timestamps = [];
      $currentTime = $startTime->copy();
      $pointCount = 0;

      while ($currentTime <= $endTime && $pointCount < $maxPoints) {
         $timestamps[] = $currentTime->copy();
         $labels[] = $currentTime->format($labelFormat);
         
         if ($intervalType === 'minutes') {
            $currentTime->addMinutes($interval);
         } elseif ($intervalType === 'hours') {
            $currentTime->addHours($interval);
         } else {
            $currentTime->addDays($interval);
         }
         
         $pointCount++;
      }
      
      // Always ensure the last point is current time
      if (count($timestamps) > 0) {
         $lastTime = $timestamps[count($timestamps) - 1];
         $timeDiffMinutes = $lastTime->diffInMinutes($endTime);
         $thresholdMinutes = $intervalType === 'minutes' ? $interval : ($intervalType === 'hours' ? $interval * 60 : $interval * 1440);
         
         // If last timestamp is not close to now, add current time
         if ($timeDiffMinutes > $thresholdMinutes / 2) {
            $timestamps[] = $endTime->copy();
            $labels[] = $endTime->format($labelFormat);
         }
      }

      // Sort markets by price (highest first)
      $sortedMarkets = $event->markets->sortByDesc(function($market) {
         // Get YES price (outcome_prices[1])
         if ($market->outcome_prices) {
            $prices = is_string($market->outcome_prices) 
                ? json_decode($market->outcome_prices, true) 
                : ($market->outcome_prices ?? []);
            
            if (is_array($prices) && isset($prices[1])) {
               return floatval($prices[1]) * 100; // YES price
            }
         }
         
         // Use best_ask if available
         if ($market->best_ask !== null && $market->best_ask > 0) {
            return floatval($market->best_ask) * 100;
         }
         
         return 0; // Default
      })->values(); // Reset keys after sorting
      
      // Filter by selected market IDs if provided
      $marketsToShow = $sortedMarkets;
      if (!empty($selectedMarketIds)) {
         $marketsToShow = $sortedMarkets->filter(function($market) use ($selectedMarketIds) {
            return in_array($market->id, $selectedMarketIds);
         })->values();
         
         \Log::info('Filtered markets:', [
            'selected_ids' => $selectedMarketIds,
            'filtered_count' => $marketsToShow->count(),
            'market_ids' => $marketsToShow->pluck('id')->toArray()
         ]);
      }
      
      // Generate series data for each market
      $seriesData = [];

      foreach ($marketsToShow as $index => $market) {
         // Get current price
         $currentPrice = 50;
         if ($market->outcome_prices) {
            $prices = is_string($market->outcome_prices) 
                ? json_decode($market->outcome_prices, true) 
                : ($market->outcome_prices ?? []);
            
            if (is_array($prices)) {
               if (isset($prices[1])) {
                  $currentPrice = floatval($prices[1]) * 100;
               } elseif (isset($prices[0])) {
                  $currentPrice = (1 - floatval($prices[0])) * 100;
               }
            }
         }

         if ($market->best_ask !== null && $market->best_ask > 0) {
            $currentPrice = floatval($market->best_ask) * 100;
         }

         // Generate price data points with direction indicators
         $dataPoints = [];
         $priceDirections = [];
         $basePrice = $currentPrice;
         $priceVariation = min(20, abs($basePrice - 50));
         $seed = $market->id ?? ($index + 1);
         $totalPoints = count($timestamps);

         $previousPrice = null;
         foreach ($timestamps as $timeIndex => $time) {
            // Progressive price trend from 50% to current price
            $progress = $totalPoints > 1 ? ($timeIndex / ($totalPoints - 1)) : 1;
            $targetPrice = 50 + ($basePrice - 50) * $progress;

            // Deterministic wave pattern
            $wave1 = sin(($seed * 3.14159 + $timeIndex * 0.8) / 2) * $priceVariation * 0.3;
            $wave2 = cos(($seed * 2.71828 + $timeIndex * 0.5) / 3) * $priceVariation * 0.2;
            
            $volatility = $wave1 + $wave2;
            $price = max(1, min(99, $targetPrice + $volatility));
            $price = round($price, 1);

            // Determine direction (up, down, or neutral)
            $direction = 'neutral';
            if ($previousPrice !== null) {
               if ($price > $previousPrice) {
                  $direction = 'up';
               } elseif ($price < $previousPrice) {
                  $direction = 'down';
               }
            }

            $dataPoints[] = $price;
            $priceDirections[] = $direction;
            $previousPrice = $price;
         }

         // Ensure last point is exactly current price
         if (count($dataPoints) > 0) {
            $dataPoints[count($dataPoints) - 1] = round($currentPrice, 1);
         }

         // Format market name
         $priceText = $currentPrice < 1 ? '<1%' : ($currentPrice >= 99 ? '>99%' : round($currentPrice, 1) . '%');
         $marketName = $market->question; // Full market question/title
         $marketNameShort = strlen($marketName) > 40 ? substr($marketName, 0, 37) . '...' : $marketName;

         // Get color
         $marketColor = $market->series_color ?? $marketColors[$index % count($marketColors)];
         if (empty($marketColor) || trim($marketColor) === '') {
            $marketColor = $marketColors[$index % count($marketColors)];
         }
         if (!str_starts_with($marketColor, '#')) {
            $marketColor = '#' . $marketColor;
         }

         // Use groupItem_title if available, otherwise use market question
         $displayTitle = $market->groupItem_title ?? $marketName;
         
         $seriesData[] = [
            'id' => $market->id, // Market ID
            'market_id' => $market->id, // Market ID (duplicate for compatibility)
            'name' => $displayTitle . ' ' . $priceText, // Display name with price
            'full_name' => $displayTitle, // Full display title
            'question' => $marketName, // Original market question
            'groupItem_title' => $market->groupItem_title ?? null, // Group item title
            'price_text' => $priceText, // Price percentage
            'color' => $marketColor,
            'data' => $dataPoints,
            'directions' => $priceDirections,
         ];
      }

      return [
         'labels' => $labels,
         'series' => $seriesData,
         'period' => $period,
         'startTime' => $startTime->toIso8601String(),
         'endTime' => $endTime->toIso8601String(),
      ];
   }

   /**
    * Display single market page
    */
   function singleMarket($slug)
   {
      try {
         // Find market by slug or ID
         $market = Market::where(function($query) use ($slug) {
               $query->where('slug', $slug)
                     ->orWhere('id', $slug);
            })
            ->with('event')
            ->firstOrFail();

         $event = $market->event;
         if (!$event) {
            return redirect()->route('home')
               ->with('error', 'Event not found for this market.');
         }

         // Use same chart data generation logic as marketDetails but for single market
         $marketColors = ['#ff7b2c', '#4c8df5', '#9cdbff', '#ffe04d', '#ff6b9d', '#4ecdc4', '#a8e6cf', '#ff8b94'];
         
         // Generate time labels (last 30 days, 2 days apart)
         $labels = [];
         $allTimes = [];
         $endDate = now();
         $startDate = $endDate->copy()->subDays(30);
         
         for ($i = 30; $i >= 0; $i -= 2) {
            $date = $endDate->copy()->subDays($i);
            $labels[] = $date->format('M d');
            $allTimes[] = $date;
         }

         // Prepare data for single market
         $seriesData = [];
         
         // Get current price
         $currentPrice = 50;
         if ($market->outcome_prices) {
            $prices = is_string($market->outcome_prices) 
               ? json_decode($market->outcome_prices, true) 
               : ($market->outcome_prices ?? []);
            if (is_array($prices)) {
               if (isset($prices[1])) {
                  $currentPrice = floatval($prices[1]) * 100;
               } elseif (isset($prices[0])) {
                  $currentPrice = (1 - floatval($prices[0])) * 100;
               }
            }
         }

         if ($market->best_ask !== null && $market->best_ask > 0) {
            $currentPrice = floatval($market->best_ask) * 100;
         }

         // Generate historical data points
         $basePrice = $currentPrice;
         $priceVariation = min(20, abs($basePrice - 50));
         $dataPoints = [];

         foreach ($allTimes as $timeIndex => $time) {
            if ($time < $startDate) {
               $dataPoints[] = null;
               continue;
            }

            $progress = ($timeIndex + 1) / count($allTimes);
            $targetPrice = 50 + ($basePrice - 50) * $progress;
            $volatility = (($timeIndex % 3 - 1) / 3) * $priceVariation * 0.2;
            $price = max(1, min(99, $targetPrice + $volatility));
            $dataPoints[] = round($price, 1);
         }

         if (count($dataPoints) > 0) {
            $dataPoints[count($dataPoints) - 1] = round($currentPrice, 1);
         }

         $priceText = $currentPrice < 1 ? '<1%' : ($currentPrice >= 99 ? '>99%' : round($currentPrice, 1) . '%');
         $marketName = $market->question;
         if (strlen($marketName) > 40) {
            $marketName = substr($marketName, 0, 37) . '...';
         }

         $marketColor = $market->series_color ?? $marketColors[0];
         if (empty($marketColor) || trim($marketColor) === '') {
            $marketColor = $marketColors[0];
         }
         if (!str_starts_with($marketColor, '#')) {
            $marketColor = '#' . $marketColor;
         }

         $seriesData[] = [
            'name' => $marketName . ' ' . $priceText,
            'color' => $marketColor,
            'data' => $dataPoints,
            'icon' => $market->icon ?? null,
            'market_id' => $market->id,
            'volume' => $market->volume ?? 0,
            'volume24hr' => $market->volume24hr ?? 0,
            'volume1wk' => $market->volume1wk ?? 0,
            'volume1mo' => $market->volume1mo ?? 0,
            'best_bid' => $market->best_bid,
            'best_ask' => $market->best_ask,
            'last_trade_price' => $market->last_trade_price,
            'one_day_price_change' => $market->one_day_price_change,
            'one_week_price_change' => $market->one_week_price_change,
            'one_month_price_change' => $market->one_month_price_change,
         ];

         return view('frontend.single_market', compact('market', 'event', 'seriesData', 'labels'));
      } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
         \Log::warning('Market not found: ' . $slug);
         return redirect()->route('home')
            ->with('error', 'Market not found.');
      } catch (\Exception $e) {
         \Log::error('Error in singleMarket: ' . $e->getMessage());
         return redirect()->route('home')
            ->with('error', 'An error occurred. Please try again later.');
      }
   }

   function savedEvents()
   {
      return view('frontend.saved_events');
   }

   function eventsByTag($slug)
   {
      try {
         $tag = Tag::where('slug', $slug)->firstOrFail();
         return view('frontend.events_by_tag', compact('tag'));
      } catch (\Illuminate\Database\QueryException $e) {
         \Log::error('Database connection failed in eventsByTag: ' . $e->getMessage());
         return redirect()->route('home')
            ->with('error', 'Unable to load tag. Please try again later.');
      } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
         \Log::warning('Tag not found: ' . $slug);
         return redirect()->route('home')
            ->with('error', 'Tag not found.');
      }
   }

   function trending()
   {
      return view('frontend.trending');
   }

   function breaking()
   {
      return view('frontend.breaking');
   }

   function newEvents()
   {
      return view('frontend.new');
   }

   function eventsByCategory($category, Request $request)
   {
      // Special handling for Elections category
      if (strtolower($category) === 'elections') {
         return $this->electionsPage($request);
      }

      // Get all events for this category to extract dynamic sub-categories - Exclude ended events
      $categoryName = ucfirst(strtolower($category));
      
      // Get secondary categories for this main category
      $secondaryCategories = \App\Models\SecondaryCategory::active()
         ->byMainCategory($categoryName)
         ->ordered()
         ->withCount('activeEvents')
         ->get();

      // Get selected secondary category from query
      $selectedSecondaryCategory = $request->get('secondary_category', null);
      
      // Cache subcategories for 5 minutes to avoid duplicate queries
      $cacheKey = 'category_subcategories_' . strtolower($categoryName);
      $dynamicSubCategories = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($categoryName) {
      $allCategoryEvents = Event::where('category', $categoryName)
         ->where('active', true)
         ->where('closed', false)
            ->where(function ($q) {
               $q->whereNull('end_date')
                 ->orWhere('end_date', '>', now());
            })
            ->select(['id', 'title'])
            ->with(['markets' => function($q) {
               $q->select(['id', 'event_id', 'question']);
            }])
         ->get();

         return $this->extractSubCategoriesFromEvents($allCategoryEvents, $categoryName);
      });

      // Get popular sub-categories (top 10 by event count)
      $popularSubCategories = collect($dynamicSubCategories)
         ->sortByDesc('count')
         ->take(10)
         ->values()
         ->all();

      // Get selected sub-category from query parameters
      $selectedSubCategory = $request->get('subcategory', 'all');

      return view('frontend.events_by_category', compact(
         'category',
         'popularSubCategories',
         'selectedSubCategory',
         'secondaryCategories',
         'selectedSecondaryCategory'
      ));
   }

   /**
    * Dedicated Elections page with special layout
    */
   private function electionsPage(Request $request)
   {
      // Get election events sorted by date (earliest first)
      $events = Event::where('category', 'Elections')
         ->where('active', true)
         ->where('closed', false)
         ->where(function ($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>', now());
         })
         ->with(['markets' => function($q) {
            $q->where('active', true)
              ->where('closed', false)
              ->select([
                 'id', 'event_id', 'question', 'slug', 'groupItem_title',
                 'outcome_prices', 'outcomes', 'active', 'closed',
                 'best_ask', 'icon', 'image', 'created_at'
              ]);
         }])
         ->orderBy('end_date', 'asc') // Sort by date, earliest first
         ->orderBy('created_at', 'asc')
         ->paginate(20);

      return view('frontend.elections', compact('events'));
   }

   /**
    * Extract sub-categories dynamically from event titles and market questions
    */
   private function extractSubCategoriesFromEvents($events, $categoryName)
   {
      $subCategories = [];
      
      // Define sub-category patterns based on category
      $patterns = $this->getSubCategoryPatterns($categoryName);

      foreach ($events as $event) {
         $title = strtolower($event->title);
         $markets = $event->markets;

         foreach ($patterns as $subCategoryName => $keywords) {
            foreach ($keywords as $keyword) {
               if (strpos($title, $keyword) !== false) {
                  if (!isset($subCategories[$subCategoryName])) {
                     $subCategories[$subCategoryName] = [
                        'name' => $subCategoryName,
                        'count' => 0,
                        'slug' => strtolower(str_replace(' ', '-', $subCategoryName)),
                     ];
                  }
                  if (!isset($subCategories[$subCategoryName]['counted_events'][$event->id])) {
                     $subCategories[$subCategoryName]['count']++;
                     $subCategories[$subCategoryName]['counted_events'][$event->id] = true;
                  }
                  break;
               }
            }

            // Also check market questions
            foreach ($markets as $market) {
               $question = strtolower($market->question ?? '');
               foreach ($keywords as $keyword) {
                  if (strpos($question, $keyword) !== false) {
                     if (!isset($subCategories[$subCategoryName])) {
                        $subCategories[$subCategoryName] = [
                           'name' => $subCategoryName,
                           'count' => 0,
                           'slug' => strtolower(str_replace(' ', '-', $subCategoryName)),
                        ];
                     }
                     if (!isset($subCategories[$subCategoryName]['counted_events'][$event->id])) {
                        $subCategories[$subCategoryName]['count']++;
                        $subCategories[$subCategoryName]['counted_events'][$event->id] = true;
                     }
                     break;
                  }
               }
            }
         }
      }

      // Clean up counted_events from result
      foreach ($subCategories as &$subCategory) {
         unset($subCategory['counted_events']);
      }

      return $subCategories;
   }

   /**
    * Get sub-category patterns based on category
    */
   private function getSubCategoryPatterns($categoryName)
   {
      $patterns = [
         'Geopolitics' => [
            'Ukraine' => ['ukraine', 'russian', 'russia', 'putin', 'zelensky'],
            'Venezuela' => ['venezuela', 'maduro', 'guaidó'],
            'Iran' => ['iran', 'khamenei', 'iranian', 'tehran'],
            'Gaza' => ['gaza', 'palestine', 'palestinian'],
            'Israel' => ['israel', 'israeli', 'netanyahu'],
            'Sudan' => ['sudan', 'sudanese'],
            'China' => ['china', 'chinese', 'beijing', 'xi jinping'],
            'Thailand-Cambodia' => ['thailand', 'cambodia', 'thai', 'cambodian'],
            'Middle East' => ['middle east', 'syria', 'iraq', 'yemen', 'lebanon'],
            'US Strikes' => ['us strikes', 'us strike', 'american strike'],
            'Taiwan' => ['taiwan', 'taiwanese'],
            'North Korea' => ['north korea', 'north korean', 'kim jong'],
         ],
         'Tech' => [
            'AI' => ['artificial intelligence', 'ai', 'machine learning', 'ml'],
            'Apple' => ['apple', 'iphone', 'ipad', 'macbook'],
            'Google' => ['google', 'alphabet', 'android'],
            'Microsoft' => ['microsoft', 'windows', 'azure'],
            'Meta' => ['meta', 'facebook', 'instagram', 'whatsapp'],
            'Tesla' => ['tesla', 'elon musk', 'model s', 'model 3'],
            'Amazon' => ['amazon', 'aws', 'alexa'],
            'Netflix' => ['netflix', 'streaming'],
         ],
         'Earnings' => [
            'Q1' => ['q1', 'first quarter'],
            'Q2' => ['q2', 'second quarter'],
            'Q3' => ['q3', 'third quarter'],
            'Q4' => ['q4', 'fourth quarter'],
            'Annual' => ['annual', 'yearly'],
         ],
         'World' => [
            'Europe' => ['europe', 'european', 'eu', 'european union'],
            'Asia' => ['asia', 'asian'],
            'Africa' => ['africa', 'african'],
            'Americas' => ['america', 'american', 'latin america'],
            'Oceania' => ['oceania', 'australia', 'new zealand'],
         ],
         'Culture' => [
            'Movies' => ['movie', 'film', 'oscar', 'cinema'],
            'Music' => ['music', 'album', 'song', 'grammy'],
            'Sports' => ['sport', 'athlete', 'championship'],
            'Entertainment' => ['entertainment', 'celebrity', 'tv show'],
         ],
         'Economy' => [
            'Inflation' => ['inflation', 'cpi', 'consumer price'],
            'GDP' => ['gdp', 'gross domestic product'],
            'Unemployment' => ['unemployment', 'jobless', 'employment'],
            'Interest Rates' => ['interest rate', 'fed rate', 'central bank'],
         ],
         'Climate & Science' => [
            'Climate Change' => ['climate change', 'global warming', 'carbon'],
            'Space' => ['space', 'nasa', 'rocket', 'satellite'],
            'Health' => ['health', 'medical', 'disease', 'vaccine'],
            'Environment' => ['environment', 'pollution', 'renewable'],
         ],
      ];

      return $patterns[$categoryName] ?? [];
   }

   function getMarketPriceData($slug)
   {
      try {
         // Only load first market to improve performance
         $event = Event::where('slug', $slug)
            ->with([
               'markets' => function ($query) {
                  $query->limit(1)->orderBy('id');
               }
            ])
            ->firstOrFail();

         $marketData = [];
         if ($event->markets && $event->markets->count() > 0) {
            $firstMarket = $event->markets->first();
            if ($firstMarket->outcome_prices) {
               // Handle both string (JSON) and array formats
               $prices = is_string($firstMarket->outcome_prices) 
                   ? json_decode($firstMarket->outcome_prices, true) 
                   : ($firstMarket->outcome_prices ?? []);
               // Fix: prices[0] = NO, prices[1] = YES (Polymarket format)
               // Use YES price (prices[1]) for current price
               if (is_array($prices)) {
                  $currentPrice = 50;
                  if (isset($prices[1])) {
                     $currentPrice = floatval($prices[1]) * 100; // YES price
                  } elseif (isset($prices[0])) {
                     // Fallback: convert NO price to YES (1 - NO)
                     $currentPrice = (1 - floatval($prices[0])) * 100;
                  }

                  // Use best_ask if available (more accurate)
                  if ($firstMarket->best_ask !== null && $firstMarket->best_ask > 0) {
                     $currentPrice = floatval($firstMarket->best_ask) * 100;
                  }

                  $marketData = [
                     'current_price' => $currentPrice,
                     'market_id' => $firstMarket->id,
                     'question' => $firstMarket->question,
                     'volume' => $firstMarket->volume,
                     'created_at' => $firstMarket->created_at ? $firstMarket->created_at->toIso8601String() : null,
                     'updated_at' => $firstMarket->updated_at ? $firstMarket->updated_at->toIso8601String() : null,
                  ];
               }
            }
         }

         return response()->json($marketData);
      } catch (\Illuminate\Database\QueryException $e) {
         \Log::error('Database connection failed in getMarketPriceData: ' . $e->getMessage());
         return response()->json(['error' => 'Unable to load market data. Please try again later.'], 500);
      } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
         \Log::warning('Event not found in getMarketPriceData: ' . $slug);
         return response()->json(['error' => 'Event not found.'], 404);
      } catch (\Exception $e) {
         \Log::error('Error in getMarketPriceData: ' . $e->getMessage());
         return response()->json(['error' => 'An error occurred. Please try again later.'], 500);
      }
   }

   function getMarketLivePrice($marketId)
   {
      try {
         // First get the market from database to find the event slug
         $market = Market::with('event')->find($marketId);
         if (!$market || !$market->event) {
            return response()->json(['error' => 'Market not found'], 404);
         }

         $eventSlug = $market->event->slug;

         // Fetch live data from Polymarket API
         $response = Http::timeout(5)
            ->get('https://gamma-api.polymarket.com/events', [
               'slug' => $eventSlug,
               'closed' => false,
            ]);

         if ($response->successful()) {
            $events = $response->json();

            if (!empty($events) && is_array($events) && count($events) > 0) {
               $polymarketEvent = $events[0];

               if (!empty($polymarketEvent['markets']) && is_array($polymarketEvent['markets'])) {
                  // Find the matching market by condition_id or question
                  foreach ($polymarketEvent['markets'] as $pmMarket) {
                     $matches = false;

                     // Match by condition_id if available
                     if (
                        $market->condition_id && isset($pmMarket['conditionId']) &&
                        $market->condition_id === $pmMarket['conditionId']
                     ) {
                        $matches = true;
                     }
                     // Or match by question/slug
                     elseif (
                        isset($pmMarket['question']) && $market->question &&
                        strtolower(trim($market->question)) === strtolower(trim($pmMarket['question']))
                     ) {
                        $matches = true;
                     }
                     // Or match by slug
                     elseif (
                        isset($pmMarket['slug']) && $market->slug &&
                        $market->slug === $pmMarket['slug']
                     ) {
                        $matches = true;
                     }

                     if ($matches) {
                        // Extract prices from Polymarket API
                        $yesPrice = 0.5;
                        $noPrice = 0.5;

                        // Use bestAsk/bestBid first (most accurate from order book)
                        if (!empty($pmMarket['bestAsk'])) {
                           $yesPrice = floatval($pmMarket['bestAsk']);
                        } elseif (!empty($pmMarket['outcomePrices']) && is_array($pmMarket['outcomePrices']) && isset($pmMarket['outcomePrices'][1])) {
                           $yesPrice = floatval($pmMarket['outcomePrices'][1]);
                        }

                        if (!empty($pmMarket['bestBid'])) {
                           // bestBid is for YES, so NO = 1 - bestBid
                           $noPrice = 1 - floatval($pmMarket['bestBid']);
                        } elseif (!empty($pmMarket['outcomePrices']) && is_array($pmMarket['outcomePrices']) && isset($pmMarket['outcomePrices'][0])) {
                           $noPrice = floatval($pmMarket['outcomePrices'][0]);
                        }

                        return response()->json([
                           'success' => true,
                           'market_id' => $marketId,
                           'yes_price' => $yesPrice,
                           'no_price' => $noPrice,
                           'yes_price_cents' => round($yesPrice * 100, 1),
                           'no_price_cents' => round($noPrice * 100, 1),
                           'best_ask' => isset($pmMarket['bestAsk']) ? floatval($pmMarket['bestAsk']) : null,
                           'best_bid' => isset($pmMarket['bestBid']) ? floatval($pmMarket['bestBid']) : null,
                        ]);
                     }
                  }
               }
            }
         }
      } catch (\Exception $e) {
         Log::warning('Failed to fetch live price from Polymarket: ' . $e->getMessage());
      }

      // Fallback to database prices
      $market = Market::find($marketId);
      if ($market) {
         // Handle both string (JSON) and array formats
         $outcomePricesRaw = $market->outcome_prices ?? null;
         $prices = is_string($outcomePricesRaw) 
             ? json_decode($outcomePricesRaw, true) 
             : ($outcomePricesRaw ?? []);
         $yesPrice = isset($prices[1]) ? floatval($prices[1]) : 0.5;
         $noPrice = isset($prices[0]) ? floatval($prices[0]) : 0.5;

         // Use best_ask/best_bid if available
         if ($market->best_ask !== null && $market->best_ask > 0) {
            $yesPrice = floatval($market->best_ask);
         }
         if ($market->best_bid !== null && $market->best_bid > 0) {
            $noPrice = 1 - floatval($market->best_bid);
         }

         return response()->json([
            'success' => true,
            'market_id' => $marketId,
            'yes_price' => $yesPrice,
            'no_price' => $noPrice,
            'yes_price_cents' => round($yesPrice * 100, 1),
            'no_price_cents' => round($noPrice * 100, 1),
         ]);
      }

      return response()->json(['error' => 'Market not found'], 404);
   }

   function getMarketHistoryData($slug)
   {
      try {
         // Only load first 8 markets to improve performance (we only show 4 in chart)
         $event = Event::where('slug', $slug)
            ->with([
               'markets' => function ($query) {
                  $query->limit(8)->orderBy('id');
               }
            ])
            ->firstOrFail();

         $marketsData = [];
         $now = now();
         $startDate = $event->start_date ? \Carbon\Carbon::parse($event->start_date) : $now->copy()->subDays(30);

         // Limit to max 4 markets for performance (reduced from 6)
         $maxMarkets = 4;
         $marketsProcessed = 0;

         try {
            // Fetch live data from Polymarket API with shorter timeout
            $response = Http::timeout(5)
               ->get('https://gamma-api.polymarket.com/events', [
                  'slug' => $slug,
                  'closed' => false,
               ]);

            if ($response->successful()) {
               $events = $response->json();

               if (!empty($events) && is_array($events) && count($events) > 0) {
                  $polymarketEvent = $events[0];

                  if (!empty($polymarketEvent['markets']) && is_array($polymarketEvent['markets'])) {
                     // Limit to first 6 markets for performance
                     $marketsToProcess = array_slice($polymarketEvent['markets'], 0, $maxMarkets);

                     foreach ($marketsToProcess as $index => $polymarketMarket) {
                        if ($marketsProcessed >= $maxMarkets)
                           break;

                        // Get current price from Polymarket API
                        // Priority: bestAsk (for YES) > outcomePrices[1] (YES) > outcomePrices[0] (NO) > default
                        $currentPrice = 50;

                        // Try bestAsk first (most accurate from order book)
                        if (!empty($polymarketMarket['bestAsk'])) {
                           $currentPrice = floatval($polymarketMarket['bestAsk']) * 100;
                        }
                        // Fallback to outcomePrices[1] (YES price)
                        elseif (!empty($polymarketMarket['outcomePrices']) && is_array($polymarketMarket['outcomePrices']) && isset($polymarketMarket['outcomePrices'][1])) {
                           $currentPrice = floatval($polymarketMarket['outcomePrices'][1]) * 100;
                        }
                        // Fallback to outcomePrices[0] (NO price) - convert to YES equivalent
                        elseif (!empty($polymarketMarket['outcomePrices']) && is_array($polymarketMarket['outcomePrices']) && isset($polymarketMarket['outcomePrices'][0])) {
                           $noPrice = floatval($polymarketMarket['outcomePrices'][0]);
                           $currentPrice = (1 - $noPrice) * 100; // Convert NO to YES
                        }

                        // Reduce data points from 50 to 30 for better performance
                        $points = 30;
                        $basePrice = $currentPrice;
                        $priceVariation = min(20, abs($basePrice - 50)); // Max 20% variation
                        $history = [];

                        for ($i = $points; $i >= 0; $i--) {
                           $time = $now->copy()->subHours($i); // 1 hour intervals (was 30 minutes)
                           if ($time < $startDate)
                              continue;

                           // Generate price that trends toward current price
                           $progress = ($points - $i) / $points;
                           $targetPrice = 50 + ($basePrice - 50) * $progress;

                           // Add some realistic volatility (different for each market)
                           $volatility = (rand(-100, 100) / 100) * $priceVariation * 0.3;
                           $price = max(1, min(99, $targetPrice + $volatility));

                           $history[] = [
                              'time' => $time->toIso8601String(),
                              'price' => round($price, 2)
                           ];
                        }

                        // Ensure last point is exactly current price
                        if (count($history) > 0) {
                           $history[count($history) - 1]['price'] = $currentPrice;
                           $history[count($history) - 1]['time'] = $now->toIso8601String();
                        }

                        // Extract both YES and NO prices properly from Polymarket API
                        $yesPrice = 50;
                        $noPrice = 50;

                        if (!empty($polymarketMarket['outcomePrices']) && is_array($polymarketMarket['outcomePrices'])) {
                           // outcomePrices[0] = NO, outcomePrices[1] = YES (Polymarket format)
                           $noPrice = isset($polymarketMarket['outcomePrices'][0]) ? floatval($polymarketMarket['outcomePrices'][0]) * 100 : 50;
                           $yesPrice = isset($polymarketMarket['outcomePrices'][1]) ? floatval($polymarketMarket['outcomePrices'][1]) * 100 : 50;
                        }

                        // Use bestAsk/bestBid if available (more accurate from order book)
                        if (!empty($polymarketMarket['bestAsk'])) {
                           $yesPrice = floatval($polymarketMarket['bestAsk']) * 100;
                        }
                        if (!empty($polymarketMarket['bestBid'])) {
                           // bestBid is for YES, so NO = (1 - bestBid) * 100
                           $noPrice = (1 - floatval($polymarketMarket['bestBid'])) * 100;
                        }

                        $marketsData[] = [
                           'market_id' => $polymarketMarket['id'] ?? null,
                           'question' => $polymarketMarket['question'] ?? '',
                           'current_price' => $currentPrice,
                           'yes_price' => $yesPrice,
                           'no_price' => $noPrice,
                           'history' => $history,
                        ];

                        $marketsProcessed++;
                     }

                     if (count($marketsData) > 0) {
                        return response()->json([
                           'success' => true,
                           'markets' => $marketsData,
                        ]);
                     }
                  }
               }
            }
         } catch (\Exception $e) {
            Log::warning('Failed to fetch Polymarket data: ' . $e->getMessage());
         }

         // Fallback to database data - limit to first 6 markets
         $marketsToProcess = $event->markets->take($maxMarkets);
         foreach ($marketsToProcess as $index => $market) {
            if ($marketsProcessed >= $maxMarkets)
               break;

            // Get current price - use YES price (prices[1]) for chart display
            $currentPrice = 50;
            if ($market->outcome_prices) {
               // Handle both string (JSON) and array formats
               $prices = is_string($market->outcome_prices) 
                   ? json_decode($market->outcome_prices, true) 
                   : ($market->outcome_prices ?? []);
               // Fix: prices[0] = NO, prices[1] = YES (Polymarket format)
               if (is_array($prices)) {
                  if (isset($prices[1])) {
                     $currentPrice = floatval($prices[1]) * 100; // YES price
                  } elseif (isset($prices[0])) {
                     // Fallback: convert NO price to YES (1 - NO)
                     $currentPrice = (1 - floatval($prices[0])) * 100;
                  }
               }
            }

            // Use best_ask if available (more accurate from order book)
            if ($market->best_ask !== null && $market->best_ask > 0) {
               $currentPrice = floatval($market->best_ask) * 100;
            }

            // Reduce data points from 50 to 30 for better performance
            $points = 30;
            $basePrice = $currentPrice;
            $priceVariation = min(20, abs($basePrice - 50));
            $history = [];

            for ($i = $points; $i >= 0; $i--) {
               $time = $now->copy()->subHours($i); // 1 hour intervals (was 30 minutes)
               if ($time < $startDate)
                  continue;

               $progress = ($points - $i) / $points;
               $targetPrice = 50 + ($basePrice - 50) * $progress;
               $volatility = (rand(-100, 100) / 100) * $priceVariation * 0.3;
               $price = max(1, min(99, $targetPrice + $volatility));

               $history[] = [
                  'time' => $time->toIso8601String(),
                  'price' => round($price, 2)
               ];
            }

            if (count($history) > 0) {
               $history[count($history) - 1]['price'] = $currentPrice;
               $history[count($history) - 1]['time'] = $now->toIso8601String();
            }

            $marketsData[] = [
               'market_id' => $market->id,
               'question' => $market->question,
               'current_price' => $currentPrice,
               'history' => $history,
            ];

            $marketsProcessed++;
         }

         return response()->json([
            'success' => true,
            'markets' => $marketsData,
         ]);
      } catch (\Illuminate\Database\QueryException $e) {
         \Log::error('Database connection failed in getMarketHistoryData: ' . $e->getMessage());
         return response()->json(['error' => 'Unable to load market history. Please try again later.'], 500);
      } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
         \Log::warning('Event not found in getMarketHistoryData: ' . $slug);
         return response()->json(['error' => 'Event not found.'], 404);
      } catch (\Exception $e) {
         \Log::error('Error in getMarketHistoryData: ' . $e->getMessage());
         return response()->json(['error' => 'An error occurred. Please try again later.'], 500);
      }
   }
}

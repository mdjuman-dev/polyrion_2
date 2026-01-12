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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
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

      foreach ($polymarketComments as $pmComment) {
         try {
            $parentCommentId = null;

            // Find parent comment if this is a reply
            if (!empty($pmComment['parentCommentID'])) {
               $parentComment = EventComment::where('polymarket_id', $pmComment['parentCommentID'])->first();
               if ($parentComment) {
                  $parentCommentId = $parentComment->id;
               }
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
         // Only load first 8 markets to improve performance (we only show 4 in chart)
         $event = Event::where('slug', $slug)
            ->with([
               'markets' => function ($query) {
                  $query->limit(8)->orderBy('id');
               }
            ])
            ->firstOrFail();

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

         // Prepare data for Highcharts Stock from outcome_prices table or trades
         // Format: Each series contains [[timestamp_ms, value], ...]
         $marketsToShow = $event->markets->take(4);
         $seriesData = [];

         foreach ($marketsToShow as $index => $market) {
            // Get outcomes for this market
            $outcomes = [];
            if ($market->outcomes) {
               $outcomes = is_string($market->outcomes) ? json_decode($market->outcomes, true) : $market->outcomes;
            }
            
            // If no outcomes, create default YES/NO
            if (empty($outcomes) || !is_array($outcomes)) {
               $outcomes = ['NO', 'YES'];
            }

            // Get price history for each outcome
            foreach ($outcomes as $outcomeIndex => $outcomeName) {
               $highchartsData = [];
               
               // Try to get price history from outcome_prices table if it exists
               if (\Schema::hasTable('outcome_prices')) {
                  // Check if outcomes table exists to get outcome_id
                  if (\Schema::hasTable('outcomes')) {
                     $outcome = \DB::table('outcomes')
                        ->where('market_id', $market->id)
                        ->where('name', $outcomeName)
                        ->first();
                     
                     if ($outcome) {
                        $priceHistory = \DB::table('outcome_prices')
                           ->where('outcome_id', $outcome->id)
                           ->orderBy('created_at', 'asc')
                           ->get();
                        
                        foreach ($priceHistory as $priceRecord) {
                           $timestamp = \Carbon\Carbon::parse($priceRecord->created_at)->timestamp * 1000;
                           $price = floatval($priceRecord->price);
                           
                           // Ensure price is between 0 and 1, convert to percentage
                           if ($price > 1) {
                              $price = $price / 100; // If stored as percentage (0-100), convert to decimal
                           }
                           $price = max(0, min(1, $price)); // Clamp between 0 and 1
                           $pricePercent = $price * 100; // Convert to percentage (0-100%)
                           
                           $highchartsData[] = [
                              (int)$timestamp, // Ensure integer timestamp in milliseconds
                              round((float)$pricePercent, 2) // Ensure numeric value, rounded to 2 decimals
                           ];
                        }
                     }
                  }
               }
               
               // Fallback: Use trades table for price history
               if (empty($highchartsData) && \Schema::hasTable('trades')) {
                  $trades = \DB::table('trades')
                     ->where('market_id', $market->id)
                     ->where('outcome', strtoupper($outcomeName))
                     ->orderBy('created_at', 'asc')
                     ->get();
                  
                  foreach ($trades as $trade) {
                     $timestamp = \Carbon\Carbon::parse($trade->created_at)->timestamp * 1000;
                     $price = floatval($trade->price_at_buy ?? $trade->price ?? 0.5);
                     
                     // Ensure price is between 0 and 1, convert to percentage
                     if ($price > 1) {
                        $price = $price / 100;
                     }
                     $price = max(0, min(1, $price));
                     $pricePercent = $price * 100;
                     
                     $highchartsData[] = [
                        (int)$timestamp,
                        round((float)$pricePercent, 2)
                     ];
                  }
               }
               
               // If still no data, use current price from outcome_prices JSON field
               if (empty($highchartsData)) {
                  $currentPrice = 50; // Default
                  if ($market->outcome_prices) {
                     $prices = is_string($market->outcome_prices) ? json_decode($market->outcome_prices, true) : $market->outcome_prices;
                     if (is_array($prices) && isset($prices[$outcomeIndex])) {
                        $currentPrice = floatval($prices[$outcomeIndex]) * 100;
                     }
                  }
                  
                  // Generate sample data points for last 30 days
                  $now = now();
                  $startDate = $event->start_date ? \Carbon\Carbon::parse($event->start_date) : $now->copy()->subDays(30);
                  $points = 30;
                  
                  for ($i = $points; $i >= 0; $i--) {
                     $time = $startDate->copy()->addDays($i * (($now->diffInDays($startDate)) / $points));
                     if ($time > $now) {
                        $time = $now->copy();
                     }
                     
                     $progress = ($i + 1) / ($points + 1);
                     $price = 50 + ($currentPrice - 50) * $progress;
                     $price = max(1, min(99, $price));
                     
                     $highchartsData[] = [
                        (int)($time->timestamp * 1000),
                        round((float)$price, 2)
                     ];
                  }
               }
               
               // Ensure we have at least 2 data points
               if (count($highchartsData) < 2 && count($highchartsData) > 0) {
                  // Duplicate the last point
                  $lastPoint = end($highchartsData);
                  $highchartsData[] = [
                     (int)(now()->timestamp * 1000),
                     $lastPoint[1]
                  ];
               }
               
               // Skip if no data
               if (empty($highchartsData)) {
                  continue;
               }
               
               // Format name
               $currentPrice = end($highchartsData)[1];
               $priceText = $currentPrice < 1 ? '<1%' : ($currentPrice >= 99 ? '>99%' : round($currentPrice, 1) . '%');
               $outcomeDisplayName = $outcomeName;
               if (strlen($outcomeDisplayName) > 35) {
                  $outcomeDisplayName = substr($outcomeDisplayName, 0, 32) . '...';
               }
               
               // Get color
               $outcomeColor = $marketColors[($index * count($outcomes) + $outcomeIndex) % count($marketColors)];
               if ($market->series_color) {
                  // If market has series_color, use it with variations for outcomes
                  $baseColor = $market->series_color;
                  if (!str_starts_with($baseColor, '#')) {
                     $baseColor = '#' . $baseColor;
                  }
                  $outcomeColor = $baseColor;
               }
               
               // Format for Highcharts Stock
               $seriesData[] = [
                  'name' => $outcomeDisplayName . ' ' . $priceText,
                  'color' => $outcomeColor,
                  'data' => $highchartsData, // [[timestamp_ms, value], ...]
                  'market_id' => $market->id,
                  'outcome' => $outcomeName,
               ];
            }
         }

         return view('frontend.market_details', compact('event', 'seriesData'));
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
      // Get all events for this category to extract dynamic sub-categories
      $categoryName = ucfirst(strtolower($category));
      $allCategoryEvents = Event::where('category', $categoryName)
         ->where('active', true)
         ->where('closed', false)
         ->with('markets')
         ->get();

      // Extract dynamic sub-categories from event titles and market questions
      $dynamicSubCategories = $this->extractSubCategoriesFromEvents($allCategoryEvents, $categoryName);

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
         'selectedSubCategory'
      ));
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
               $prices = json_decode($firstMarket->outcome_prices, true);
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
         $prices = json_decode($market->outcome_prices ?? '[]', true);
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
               $prices = json_decode($market->outcome_prices, true);
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

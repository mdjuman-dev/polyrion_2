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
        // Only load first 8 markets to improve performance (we only show 4 in chart)
        $event = Event::where('slug', $slug)
            ->with(['markets' => function($query) {
                $query->limit(8)->orderBy('id');
            }])
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

        // Prepare seriesData for chart
        $seriesData = [];
        $labels = [];
        $now = now();
        $startDate = $event->start_date ? \Carbon\Carbon::parse($event->start_date) : $now->copy()->subDays(30);

        // Generate time labels (x-axis) - optimized to 12 points
        $points = 12; // 12 data points for cleaner chart
        $timeLabels = [];
        $allTimes = [];

        for ($i = $points; $i >= 0; $i--) {
            $time = $now->copy()->subDays($i * 2); // 2 day intervals for 12 points
            if ($time < $startDate) continue;
            $allTimes[] = $time;
            $timeLabels[] = $time->format('M d');
        }

        // If no labels generated, create default ones
        if (empty($timeLabels)) {
            for ($i = 6; $i >= 0; $i--) {
                $time = $now->copy()->subDays($i);
                $timeLabels[] = $time->format('M d');
                $allTimes[] = $time;
            }
        }

        $labels = $timeLabels;

        // Prepare data for each market (show up to 4 markets or all if less than 4)
        $marketsToShow = $event->markets->take(4); // Limit to first 4 markets

        foreach ($marketsToShow as $index => $market) {
            // Get current price - use YES price (prices[1]) for chart display
            $currentPrice = 50;
            if ($market->outcome_prices) {
                $prices = json_decode($market->outcome_prices, true);
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

            // Generate historical data points matching the labels (optimized - less random calculations)
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

                // Simplified volatility calculation (removed rand() for performance)
                $volatility = (($timeIndex % 3 - 1) / 3) * $priceVariation * 0.2; // Deterministic instead of random
                $price = max(1, min(99, $targetPrice + $volatility));

                $dataPoints[] = round($price, 1);
            }

            // Ensure last point is exactly current price
            if (count($dataPoints) > 0) {
                $dataPoints[count($dataPoints) - 1] = round($currentPrice, 1);
            }

            // Format name with current price percentage
            $priceText = $currentPrice < 1 ? '<1%' : ($currentPrice >= 99 ? '>99%' : round($currentPrice, 1) . '%');
            $marketName = $market->question;
            if (strlen($marketName) > 40) {
                $marketName = substr($marketName, 0, 37) . '...';
            }

            // Use series_color from database if available, otherwise use color palette
            $marketColor = $market->series_color ?? $marketColors[$index % count($marketColors)];
            
            // Ensure color is not empty and has # prefix
            if (empty($marketColor) || trim($marketColor) === '') {
                $marketColor = $marketColors[$index % count($marketColors)];
            }
            if (!str_starts_with($marketColor, '#')) {
                $marketColor = '#' . $marketColor;
            }

            // Restore full data structure for chart compatibility
            $seriesData[] = [
                'name' => $marketName . ' ' . $priceText,
                'color' => $marketColor,
                'data' => $dataPoints,
                'icon' => $market->icon ?? null,
                'market_id' => $market->id,
                'best_bid' => $market->best_bid,
                'best_ask' => $market->best_ask,
                'last_trade_price' => $market->last_trade_price,
                'one_day_price_change' => $market->one_day_price_change,
                'one_week_price_change' => $market->one_week_price_change,
                'one_month_price_change' => $market->one_month_price_change,
            ];
        }

        return view('frontend.market_details', compact('event', 'seriesData', 'labels'));
    }

    function savedEvents()
    {
        return view('frontend.saved_events');
    }

    function eventsByTag($slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        return view('frontend.events_by_tag', compact('tag'));
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

    function eventsByCategory($category)
    {
        return view('frontend.events_by_category', compact('category'));
    }

    function getMarketPriceData($slug)
    {
        // Only load first market to improve performance
        $event = Event::where('slug', $slug)
            ->with(['markets' => function($query) {
                $query->limit(1)->orderBy('id');
            }])
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
                            if ($market->condition_id && isset($pmMarket['conditionId']) && 
                                $market->condition_id === $pmMarket['conditionId']) {
                                $matches = true;
                            }
                            // Or match by question/slug
                            elseif (isset($pmMarket['question']) && $market->question && 
                                strtolower(trim($market->question)) === strtolower(trim($pmMarket['question']))) {
                                $matches = true;
                            }
                            // Or match by slug
                            elseif (isset($pmMarket['slug']) && $market->slug && 
                                $market->slug === $pmMarket['slug']) {
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
        // Only load first 8 markets to improve performance (we only show 4 in chart)
        $event = Event::where('slug', $slug)
            ->with(['markets' => function($query) {
                $query->limit(8)->orderBy('id');
            }])
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
                            if ($marketsProcessed >= $maxMarkets) break;
                            
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
                                if ($time < $startDate) continue;

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
            if ($marketsProcessed >= $maxMarkets) break;
            
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
                if ($time < $startDate) continue;

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
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Tag;
use App\Models\Event;
use App\Models\EventComment;
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
            $response = Http::timeout(30)
                ->get('https://gamma-api.polymarket.com/comments', [
                    'parent_entity_type' => 'Event',
                    'parent_entity_id' => (int) $polymarketEventId, // Ensure it's an integer
                    'limit' => 100,
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
        $event = Event::where('slug', $slug)->with('markets')->firstOrFail();

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

        // Generate time labels (x-axis)
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
            // Get current price
            $currentPrice = 50;
            if ($market->outcome_prices) {
                $prices = json_decode($market->outcome_prices, true);
                if (is_array($prices) && isset($prices[0])) {
                    $currentPrice = floatval($prices[0]) * 100;
                }
            }

            // Generate historical data points matching the labels
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

                // Add realistic volatility
                $volatility = (rand(-100, 100) / 100) * $priceVariation * 0.3;
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
        $event = Event::where('slug', $slug)->with('markets')->firstOrFail();
        
        $marketData = [];
        if ($event->markets && $event->markets->count() > 0) {
            $firstMarket = $event->markets->first();
            if ($firstMarket->outcome_prices) {
                $prices = json_decode($firstMarket->outcome_prices, true);
                if (is_array($prices) && isset($prices[0])) {
                    $marketData = [
                        'current_price' => $prices[0] * 100, // Convert to percentage
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

    function getMarketHistoryData($slug)
    {
        $event = Event::where('slug', $slug)->with('markets')->firstOrFail();

        $marketsData = [];
        $now = now();
        $startDate = $event->start_date ? \Carbon\Carbon::parse($event->start_date) : $now->copy()->subDays(30);

        try {
            // Fetch live data from Polymarket API
            $response = Http::timeout(10)
                ->get('https://gamma-api.polymarket.com/events', [
                    'slug' => $slug,
                    'closed' => false,
                ]);

            if ($response->successful()) {
                $events = $response->json();

                if (!empty($events) && is_array($events) && count($events) > 0) {
                    $polymarketEvent = $events[0];

                    if (!empty($polymarketEvent['markets']) && is_array($polymarketEvent['markets'])) {
                        // Process all markets from Polymarket API
                        foreach ($polymarketEvent['markets'] as $index => $polymarketMarket) {
                            // Get current price
                            $currentPrice = 50;
                            if (!empty($polymarketMarket['outcomePrices']) && is_array($polymarketMarket['outcomePrices']) && isset($polymarketMarket['outcomePrices'][0])) {
                                $currentPrice = floatval($polymarketMarket['outcomePrices'][0]) * 100;
                            }

                            // Generate historical data points based on current price
                            $points = 100;
                            $basePrice = $currentPrice;
                            $priceVariation = min(20, abs($basePrice - 50)); // Max 20% variation
                            $history = [];

                            for ($i = $points; $i >= 0; $i--) {
                                $time = $now->copy()->subMinutes($i * 15); // 15 minute intervals
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

                            $marketsData[] = [
                                'market_id' => $polymarketMarket['id'] ?? null,
                                'question' => $polymarketMarket['question'] ?? '',
                                'current_price' => $currentPrice,
                                'history' => $history,
                            ];
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

        // Fallback to database data - process all markets
        foreach ($event->markets as $index => $market) {
            $currentPrice = 50;
            if ($market->outcome_prices) {
                $prices = json_decode($market->outcome_prices, true);
                if (is_array($prices) && isset($prices[0])) {
                    $currentPrice = $prices[0] * 100;
                }
            }

            // Generate basic history for database markets
            $points = 100;
            $basePrice = $currentPrice;
            $priceVariation = min(20, abs($basePrice - 50));
            $history = [];

            for ($i = $points; $i >= 0; $i--) {
                $time = $now->copy()->subMinutes($i * 15);
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
        }

        return response()->json([
            'success' => true,
            'markets' => $marketsData,
        ]);
    }
}

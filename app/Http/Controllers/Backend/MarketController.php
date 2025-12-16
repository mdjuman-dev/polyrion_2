<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Event;
use App\Models\Market;
use App\Models\Tag;
use App\Services\SettlementService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class MarketController extends Controller
{
    /**
     * Display a listing of markets
     */
    public function index(Request $request)
    {
        $query = Market::with('event');

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
        $market = Market::with('event')->findOrFail($id);

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
        $market = Market::with('event')->findOrFail($id);
        
        // Decode JSON fields
        $outcomePrices = $market->outcome_prices ? json_decode($market->outcome_prices, true) : [0.5, 0.5];
        $outcomes = $market->outcomes ? json_decode($market->outcomes, true) : [];
        
        $events = Event::orderBy('title')->get();
        
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
            'active' => 'boolean',
            'closed' => 'boolean',
            'featured' => 'boolean',
        ]);

        try {
            $market = Market::findOrFail($id);
            
            $market->question = $request->question;
            $market->description = $request->description;
            
            if ($request->has('event_id')) {
                $market->event_id = $request->event_id;
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
        $query = Market::with('event');

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
        $markets = Market::with('event')
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

    function storeEvents()
    {
        $startTime = time();
        $maxExecutionTime = 300; // Increased to 5 minutes
        $limit = 100;
        $offset = 0;
        $maxBatches = 100; // Increased from 10 to 100 (can fetch up to 10,000 events)
        $batchCount = 0;
        $totalProcessed = 0;
        $consecutiveEmptyBatches = 0;
        $maxConsecutiveEmpty = 3; // Stop after 3 consecutive empty batches

        Log::info("=== Starting event fetch process at " . date('Y-m-d H:i:s') . " ===");

        while ($batchCount < $maxBatches) {

            // Global time check
            if ((time() - $startTime) >= $maxExecutionTime) {
                Log::info("Time limit reached. Processed {$totalProcessed} events in this run.");
                break;
            }

            // Fetch events with increased timeout and better error handling
            try {
                $response = Http::timeout(120) // 2 minutes timeout
                    ->retry(2, 3000, function ($exception, $request) {
                        // Only retry on timeout or connection errors
                        return $exception instanceof \Illuminate\Http\Client\ConnectionException;
                    })
                    ->get('https://gamma-api.polymarket.com/events', [
                        'closed' => false,
                        'limit' => $limit,
                        'offset' => $offset,
                        'ascending' => false,
                    ]);

                if (!$response->successful()) {
                    Log::error("Error fetching events: status " . $response->status() . " at offset " . $offset);
                    // Retry logic: wait and try again
                    if ($consecutiveEmptyBatches < 2) {
                        sleep(3);
                        continue;
                    }
                    break;
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::warning("Connection timeout at offset {$offset}. Retrying... Error: " . $e->getMessage());
                // Retry on timeout
                if ($consecutiveEmptyBatches < 2) {
                    sleep(5); // Wait longer before retry
                    continue;
                }
                // Skip this offset and continue
                $offset += $limit;
                $batchCount++;
                continue;
            } catch (\Exception $e) {
                Log::error("Unexpected error at offset {$offset}: " . $e->getMessage());
                // Skip this offset and continue
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

                // Still increment offset to try next batch
                $offset += $limit;
                $batchCount++;
                continue;
            }

            // Reset consecutive empty counter if we got events
            $consecutiveEmptyBatches = 0;

            // Process each event
            foreach ($events as $ev) {

                // Time check inside loop
                if ((time() - $startTime) >= $maxExecutionTime) {
                    Log::info("Time limit reached during processing. Processed {$totalProcessed} events.");
                    break 2;
                }

                if (!empty($ev['markets'])) {

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

                            'active' => $ev['active'] ?? null,
                            'closed' => $ev['closed'] ?? null,
                            'archived' => $ev['archived'] ?? null,
                            'new' => $ev['new'] ?? null,
                            'featured' => $ev['featured'] ?? null,

                            'start_date' => toMysqlDate($ev['startDate'] ?? null),
                            'end_date' => toMysqlDate($ev['endDate'] ?? null),
                        ]
                    );

                    // Save markets
                    foreach ($ev['markets'] as $mk) {
                        Market::updateOrCreate(
                            ['slug' => $mk['slug']],
                            [
                                'event_id' => $event->id,
                                'question' => $mk['question'] ?? null,
                                'condition_id' => $mk['conditionId'] ?? null,
                                'groupItem_title' => $mk['groupItemTitle'] ?? null,
                                'group_item_threshold' => $mk['groupItemThreshold'] ?? null,
                                'description' => $mk['description'] ?? null,
                                'resolution_source' => $mk['resolutionSource'] ?? null,
                                'image' => $mk['image'] ?? null,
                                'icon' => $mk['icon'] ?? null,

                                'liquidity_clob' => $mk['liquidityClob'] ?? $mk['liquidityNum'] ?? null,
                                'volume' => $mk['volume'] ?? $mk['volumeNum'] ?? null,
                                'volume24hr' => $mk['volume24hr'] ?? $mk['volume24hrClob'] ?? null,
                                'volume1wk' => $mk['volume1wk'] ?? $mk['volume1wkClob'] ?? null,
                                'volume1mo' => $mk['volume1mo'] ?? $mk['volume1moClob'] ?? null,
                                'volume1yr' => $mk['volume1yr'] ?? $mk['volume1yrClob'] ?? null,

                                'outcome_prices' => $mk['outcomePrices'] ?? null,
                                'outcomes' => $mk['outcomes'] ?? null,

                                // Trading prices
                                'best_bid' => isset($mk['bestBid']) ? floatval($mk['bestBid']) : null,
                                'best_ask' => isset($mk['bestAsk']) ? floatval($mk['bestAsk']) : null,
                                'last_trade_price' => isset($mk['lastTradePrice']) ? floatval($mk['lastTradePrice']) : null,
                                'spread' => isset($mk['spread']) ? floatval($mk['spread']) : null,

                                // Price changes
                                'one_day_price_change' => isset($mk['oneDayPriceChange']) ? floatval($mk['oneDayPriceChange']) : null,
                                'one_week_price_change' => isset($mk['oneWeekPriceChange']) ? floatval($mk['oneWeekPriceChange']) : null,
                                'one_month_price_change' => isset($mk['oneMonthPriceChange']) ? floatval($mk['oneMonthPriceChange']) : null,

                                // Chart and display
                                'series_color' => $mk['seriesColor'] ?? null,
                                'competitive' => isset($mk['competitive']) ? floatval($mk['competitive']) : null,

                                'active' => $mk['active'] ?? null,
                                'closed' => $mk['closed'] ?? null,
                                'archived' => $mk['archived'] ?? null,
                                'featured' => $mk['featured'] ?? null,
                                'new' => $mk['new'] ?? null,
                                'restricted' => $mk['restricted'] ?? null,
                                'approved' => $mk['approved'] ?? null,

                                'start_date' => toMysqlDate($mk['startDate'] ?? null),
                                'end_date' => toMysqlDate($mk['endDate'] ?? null),
                            ]
                        );
                    }

                    // Save Tags
                    $tagIds = [];
                    if (!empty($ev['tags'])) {
                        foreach ($ev['tags'] as $tag) {
                            $tagModel = Tag::updateOrCreate(
                                ['slug' => $tag['slug']],
                                [
                                    'label' => $tag['label'],
                                ]
                            );
                            $tagIds[] = $tagModel->id;
                        }
                        // Attach tags to event
                        $event->tags()->sync($tagIds);
                    }
                }

                $totalProcessed++;
            }

            $eventsCount = count($events);
            Log::info("[Batch #{$batchCount}] Fetched {$eventsCount} events from offset {$offset}. Total processed: {$totalProcessed}");

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

        $executionTime = time() - $startTime;
        $message = "=== Event fetch completed ===\n";
        $message .= "Total processed: {$totalProcessed} events\n";
        $message .= "Batches completed: {$batchCount}\n";
        $message .= "Execution time: {$executionTime} seconds\n";
        if ($batchCount >= $maxBatches) {
            $message .= "Reached max batches limit ({$maxBatches})\n";
        }
        $message .= "Next batch will continue from offset {$offset}";

        Log::info($message);
        return $message;
    }

    /**
     * Set final result for a market and settle trades
     */
    public function setResult(Request $request, $id)
    {
        $request->validate([
            'final_result' => ['required', Rule::in(['yes', 'no'])],
        ]);

        try {
            $market = Market::findOrFail($id);

            // Set final result
            $market->final_result = $request->final_result;
            $market->result_set_at = now();
            $market->closed = true;
            $market->save();

            // Settle all pending trades
            $settlementService = new SettlementService();
            $settlementResult = $settlementService->settleMarket($market);

            return response()->json([
                'success' => true,
                'message' => 'Market result set and trades settled successfully',
                'settlement' => $settlementResult,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to set market result', [
                'market_id' => $id,
                'error' => $e->getMessage(),
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

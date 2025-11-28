<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Event;
use App\Models\Market;
use App\Models\Tag;
use Illuminate\Support\Carbon;

class MarketController extends Controller
{
    function index()
    {
        $events = Event::with('markets')->latest()->paginate(20);
        return view('backend.market.index', compact('events'));
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
        $maxExecutionTime = 25;
        $limit = 100;
        $offset = 0;
        $maxBatches = 5;
        $batchCount = 0;
        $totalProcessed = 0;

        while ($batchCount < $maxBatches) {

            // Global time check
            if ((time() - $startTime) >= $maxExecutionTime) {
                Log::info("Time limit reached. Processed {$totalProcessed} events in this run.");
                break;
            }

            // Fetch events
            $response = Http::get('https://gamma-api.polymarket.com/events', [
                'closed' => false,
                'limit' => $limit,
                'offset' => $offset,
                'ascending' => false,
            ]);

            if (!$response->successful()) {
                Log::error("Error fetching events: status " . $response->status() . " at offset " . $offset);
                break;
            }

            $events = $response->json();

            if (empty($events)) {
                Log::info("No more events at offset " . $offset);
                break;
            }

            // Process each event
            foreach ($events as $ev) {

                // Time check inside loop
                if ((time() - $startTime) >= $maxExecutionTime) {
                    Log::info("Time limit reached during processing. Processed {$totalProcessed} events.");
                    break 2; // Break outer while + foreach
                }

                // Only save events that have at least one market
                if (!empty($ev['markets'])) {

                    $event = Event::updateOrCreate(
                        ['slug' => $ev['slug']],
                        [
                            'slug' => $ev['slug'],
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
                                'groupItem_title' => $mk['groupItemTitle'] ?? null,
                                'description' => $mk['description'] ?? null,
                                'resolution_source' => $mk['resolutionSource'] ?? null,
                                'image' => $mk['image'] ?? null,
                                'icon' => $mk['icon'] ?? null,

                                'liquidity_clob' => $mk['liquidityClob'] ?? null,
                                'volume' => $mk['volume'] ?? null,
                                'volume24hr' => $mk['volume24hr'] ?? null,
                                'volume1wk' => $mk['volume1wk'] ?? null,
                                'volume1mo' => $mk['volume1mo'] ?? null,
                                'volume1yr' => $mk['volume1yr'] ?? null,

                                'outcome_prices' => $mk['outcomePrices'] ?? null,
                                'outcomes' => $mk['outcomes'] ?? null,

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

            Log::info("Fetched " . count($events) . " events from offset " . $offset);

            $offset += $limit;
            $batchCount++;

            // Small delay between batches to avoid rate limit
            if ($batchCount < $maxBatches) {
                usleep(200000); // 200 ms
            }
        }

        return "Processed {$totalProcessed} events. Next batch will continue from offset {$offset}.";
    }
}

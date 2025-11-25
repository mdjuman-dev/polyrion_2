<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Event;
use App\Models\Market;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class MarketController extends Controller
{
    function index()
    {


        return view('backend.market.index');
    }

    function marketList()
    {
        $events = Event::with('markets')->latest()->paginate(20);
        return view('backend.market.list', compact('events'));
    }

    function search(Request $request)
    {
        $slug = Str::slug($request->search);
        $api = 'https://gamma-api.polymarket.com/events/slug/';

        $response = Http::get($api . $slug);
        if (!$response->successful()) {
            return back()->with(['message' => 'Failed to get data', 'alert-type' => 'error']);
        }
        $data = $response->object();

        return view('backend.market.index', compact('data'));
    }


    function marketSave($slug)
    {
        $api = 'https://gamma-api.polymarket.com/events/slug/';
        $response = Http::get($api . $slug);
        if (!$response->successful()) {
            return back()->with(['message' => 'Failed to save market', 'alert-type' => 'error']);
        }
        $data = $response->object();
        return view('backend.market.edit', compact('data'));
    }

    function edit($id)
    {
        $event = Event::with('markets')->findOrFail($id);

        // Convert event to object format for compatibility with edit view
        $data = (object) [
            'id' => $event->id,
            'title' => $event->title,
            'slug' => $event->slug,
            'description' => $event->description,
            'image' => $event->image,
            'icon' => $event->icon,
            'liquidity' => $event->liquidity,
            'volume' => $event->volume,
            'volume_24hr' => $event->volume_24hr,
            'volume_1wk' => $event->volume_1wk,
            'volume_1mo' => $event->volume_1mo,
            'volume_1yr' => $event->volume_1yr,
            'liquidity_clob' => $event->liquidity_clob,
            'active' => $event->active,
            'closed' => $event->closed,
            'archived' => $event->archived,
            'new' => $event->new,
            'featured' => $event->featured,
            'restricted' => $event->restricted,
            'show_all_outcomes' => $event->show_all_outcomes,
            'enable_order_book' => $event->enable_order_book,
            'start_date' => $event->start_date,
            'end_date' => $event->end_date,
            'markets' => $event->markets->map(function ($market) {
                return (object) [
                    'id' => $market->id,
                    'slug' => $market->slug,
                    'question' => $market->question,
                    'description' => $market->description,
                    'icon' => $market->icon,
                    'liquidity' => $market->liquidity,
                    'liquidity_clob' => $market->liquidity_clob,
                    'volume' => $market->volume,
                    'volume_24hr' => $market->volume_24hr,
                    'volume_1wk' => $market->volume_1wk,
                    'volume_1mo' => $market->volume_1mo,
                    'volume_1yr' => $market->volume_1yr,
                    'outcome_prices' => $market->outcome_prices,
                    'active' => $market->active,
                    'closed' => $market->closed,
                    'archived' => $market->archived,
                    'new' => $market->new,
                    'restricted' => $market->restricted,
                    'start_date' => $market->start_date,
                    'end_date' => $market->end_date,
                ];
            })->toArray()
        ];

        return view('backend.market.edit', compact('data'));
    }


    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Handle file uploads - preserve existing if no new file
            $imagePath = $request->existing_image ?? null;
            $iconPath = $request->existing_icon ?? null;

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('events', 'public');
            }

            if ($request->hasFile('icon')) {
                $iconPath = $request->file('icon')->store('events', 'public');
            }

            // Prepare event data
            $eventData = [
                'title' => $request->title,
                'slug' => $request->slug,
                'description' => $request->description,
                'image' => $imagePath,
                'icon' => $iconPath,
                'liquidity' => $request->liquidity ?? 0,
                'volume' => $request->volume ?? 0,
                'volume_24hr' => $request->volume_24hr ?? 0,
                'volume_1wk' => $request->volume_1wk ?? 0,
                'volume_1mo' => $request->volume_1mo ?? 0,
                'volume_1yr' => $request->volume_1yr ?? 0,
                'liquidity_clob' => $request->liquidity_clob ?? 0,
                'active' => $request->active ? 1 : 0,
                'closed' => $request->closed ? 1 : 0,
                'archived' => $request->archived ? 1 : 0,
                'new' => $request->new ? 1 : 0,
                'featured' => $request->featured ? 1 : 0,
                'restricted' => $request->restricted ? 1 : 0,
                'show_all_outcomes' => $request->show_all_outcomes ? 1 : 0,
                'enable_order_book' => $request->enable_order_book ? 1 : 0,
                'start_date' => $request->start_date ? Carbon::parse($request->start_date) : null,
                'end_date' => $request->end_date ? Carbon::parse($request->end_date) : null,
            ];

            // Check if event exists (for update)
            if (!empty($request->event_id)) {
                $event = Event::find($request->event_id);
                if ($event) {
                    $event->update($eventData);
                } else {
                    $event = Event::create($eventData);
                }
            } else {
                // Create new event
                $event = Event::create($eventData);
            }

            // Handle markets - update, create, or delete
            if (!empty($request->markets) && is_array($request->markets)) {
                foreach ($request->markets as $index => $marketData) {

                    // Delete market if marked
                    if (!empty($marketData['_delete']) && !empty($marketData['id'])) {
                        Market::where('id', $marketData['id'])->delete();
                        continue;
                    }

                    // Validate required fields
                    if (empty($marketData['slug']) || empty($marketData['question'])) {
                        continue;
                    }

                    // Handle market icon upload
                    $marketIcon = $marketData['existing_icon'] ?? null;
                    if ($request->hasFile("markets.$index.icon")) {
                        $marketIcon = $request->file("markets.$index.icon")
                            ->store('markets', 'public');
                    }

                    // Outcome price convert
                    $outcomePrices = null;
                    if (isset($marketData['yesPercent'], $marketData['noPercent'])) {
                        $outcomePrices = json_encode([
                            floatval($marketData['yesPercent']) / 100,
                            floatval($marketData['noPercent']) / 100,
                        ]);
                    }

                    // Prepare market fields
                    $marketFields = [
                        'event_id' => $event->id,
                        'slug' => $marketData['slug'],
                        'question' => $marketData['question'],
                        'description' => $marketData['description'] ?? null,
                        'icon' => $marketIcon,
                        'liquidity' => floatval($marketData['liquidity'] ?? 0),
                        'liquidity_clob' => floatval($marketData['liquidity_clob'] ?? 0),
                        'volume' => floatval($marketData['volume'] ?? 0),
                        'volume_24hr' => floatval($marketData['volume_24hr'] ?? 0),
                        'volume_1wk' => floatval($marketData['volume_1wk'] ?? 0),
                        'volume_1mo' => floatval($marketData['volume_1mo'] ?? 0),
                        'volume_1yr' => floatval($marketData['volume_1yr'] ?? 0),
                        'outcome_prices' => $outcomePrices,
                        'active' => !empty($marketData['active']),
                        'closed' => !empty($marketData['closed']),
                        'archived' => !empty($marketData['archived']),
                        'new' => !empty($marketData['new']),
                        'restricted' => !empty($marketData['restricted']),
                        'start_date' => !empty($marketData['start_date'])
                            ? Carbon::parse($marketData['start_date'])
                            : null,
                        'end_date' => !empty($marketData['end_date'])
                            ? Carbon::parse($marketData['end_date'])
                            : null,
                    ];

                    // Update or create market
                    if (!empty($marketData['id'])) {
                        $market = Market::find($marketData['id']);
                        if ($market) {
                            $market->update($marketFields);
                        } else {
                            Market::create($marketFields);
                        }
                    } else {
                        Market::create($marketFields);
                    }
                }
            }

            DB::commit();

            $message = !empty($request->event_id)
                ? 'Event updated successfully!'
                : 'Event created successfully!';

            return redirect()->route('admin.market.list')
                ->with(['message' => $message, 'alert-type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Event store error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with([
                'message' => 'Error: ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        // Redirect to store method with event_id
        $request->merge(['event_id' => $id]);
        return $this->store($request);
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
                'order' => 'id',
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

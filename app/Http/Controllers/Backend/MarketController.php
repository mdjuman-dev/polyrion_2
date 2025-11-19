<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Event;
use App\Models\Market;
use Illuminate\Support\Facades\DB;

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
}
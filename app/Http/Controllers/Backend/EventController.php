<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Market;
use App\Services\CategoryDetector;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    protected CategoryDetector $categoryDetector;

    public function __construct(CategoryDetector $categoryDetector)
    {
        $this->categoryDetector = $categoryDetector;
        // Ensure only authenticated admins can access
        $this->middleware('auth:admin')->except([]);
        // Permission checks are handled in routes
    }

    /**
     * Display a listing of events
     */
    public function index(Request $request)
    {
        $query = Event::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhere('slug', 'like', "%{$searchTerm}%")
                    ->orWhere('category', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by category if provided
        if ($request->has('category') && $request->category !== 'all' && $request->category !== '') {
            $query->byCategory($request->category);
        }

        // Filter by status if provided
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
        // Optimize: Select only necessary columns and eager load markets with select to avoid N+1
        $events = $query->select([
            'id', 'title', 'slug', 'description', 'category', 'image', 'icon',
            'volume', 'volume_24hr', 'liquidity', 'active', 'closed', 'featured',
            'start_date', 'end_date', 'created_at', 'updated_at'
        ])
        ->with(['markets' => function($q) {
            $q->select(['id', 'event_id', 'question', 'slug', 'active', 'closed', 'volume', 'created_at']);
        }])
        ->orderBy('volume', 'desc')
        ->paginate(20)
        ->withQueryString();
        $categories = $this->categoryDetector->getAvailableCategories();

        return view('backend.events.index', compact('events', 'categories'));
    }

    /**
     * Show the form for creating a new event
     * Only admins can access
     */
    public function create()
    {
        // Additional check - ensure user is admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Unauthorized. Only admins can create events.');
        }

        $categories = $this->categoryDetector->getAvailableCategories();
        return view('backend.events.create', compact('categories'));
    }

    /**
     * Show the form for creating a new event with markets
     * Only admins can access
     */
    public function createWithMarkets()
    {
        // Additional check - ensure user is admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Unauthorized. Only admins can create events.');
        }

        $categories = $this->categoryDetector->getAvailableCategories();
        return view('backend.events.create-with-markets', compact('categories'));
    }

    /**
     * Show form to add markets to an event
     * Only admins can access
     */
    public function addMarkets(Event $event)
    {
        // Additional check - ensure user is admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Unauthorized. Only admins can add markets.');
        }

        return view('backend.events.add-markets', compact('event'));
    }

    /**
     * Store markets for an event
     * Only admins can access
     */
    public function storeMarkets(Request $request, Event $event)
    {
        // Additional check - ensure user is admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Unauthorized. Only admins can add markets.');
        }
        $validated = $request->validate([
            'markets' => 'required|array|min:1',
            'markets.*.question' => 'required|string|max:255',
            'markets.*.description' => 'nullable|string',
            'markets.*.slug' => 'nullable|string|max:255',
            'markets.*.image' => 'nullable|url|max:500',
            'markets.*.icon' => 'nullable|url|max:500',
            'markets.*.start_date' => 'nullable|date',
            'markets.*.end_date' => 'nullable|date',
            'markets.*.yes_price' => 'nullable|numeric|min:0|max:1',
            'markets.*.no_price' => 'nullable|numeric|min:0|max:1',
        ]);

        $createdCount = 0;
        foreach ($validated['markets'] as $marketData) {
            // Generate slug if not provided
            if (empty($marketData['slug'])) {
                $marketData['slug'] = Str::slug($marketData['question']);
                // Ensure unique slug
                $baseSlug = $marketData['slug'];
                $counter = 1;
                while (Market::where('slug', $marketData['slug'])->exists()) {
                    $marketData['slug'] = $baseSlug . '-' . $counter;
                    $counter++;
                }
            }

            // Set outcome prices
            $outcomePrices = [];
            if (isset($marketData['yes_price']) && isset($marketData['no_price'])) {
                $outcomePrices = [
                    (string) $marketData['no_price'],
                    (string) $marketData['yes_price']
                ];
            } else {
                // Default prices
                $outcomePrices = ['0.5', '0.5'];
            }

            Market::create([
                'event_id' => $event->id,
                'question' => $marketData['question'],
                'slug' => $marketData['slug'],
                'description' => $marketData['description'] ?? null,
                'image' => $marketData['image'] ?? null,
                'icon' => $marketData['icon'] ?? null,
                'start_date' => $marketData['start_date'] ?? null,
                'end_date' => $marketData['end_date'] ?? null,
                'outcome_prices' => json_encode($outcomePrices),
                'outcomes' => json_encode(['No', 'Yes']),
                'active' => true,
            ]);

            $createdCount++;
        }

        return redirect()
            ->route('admin.events.show', $event)
            ->with('success', "Successfully created {$createdCount} market(s) for this event.");
    }

    /**
     * Store a newly created event
     * Category will be automatically detected from title
     * Only admins can access
     */
    public function store(Request $request)
    {
        // Additional check - ensure user is admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Unauthorized. Only admins can create events.');
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'slug' => 'nullable|string|max:255|unique:events,slug',
            'image' => 'nullable|url|max:500',
            'icon' => 'nullable|url|max:500',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'active' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
        ]);

        // Auto-detect category if not provided
        if (empty($validated['category'])) {
            $validated['category'] = $this->categoryDetector->detect($validated['title']);
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            // Ensure unique slug
            $baseSlug = $validated['slug'];
            $counter = 1;
            while (Event::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        // Set defaults
        $validated['active'] = $validated['active'] ?? true;
        $validated['featured'] = $validated['featured'] ?? false;

        $event = Event::create($validated);

        // Check if user wants to add markets immediately
        if ($request->has('add_markets') && $request->add_markets == '1') {
            return redirect()
                ->route('admin.events.add-markets', $event)
                ->with('success', "Event created successfully. Now add markets to this event.");
        }

        return redirect()
            ->route('admin.events.show', $event)
            ->with('success', "Event created successfully. You can now add markets to this event.")
            ->with('show_add_markets_prompt', true);
    }

    /**
     * Store a newly created event with markets
     * Only admins can access
     */
    public function storeWithMarkets(Request $request)
    {
        // Additional check - ensure user is admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Unauthorized. Only admins can create events.');
        }

        // Validate event data with custom messages
        $eventValidated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'slug' => 'nullable|string|max:255|unique:events,slug',
            'image' => 'nullable|url|max:500',
            'icon' => 'nullable|url|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'active' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
        ], [
            'title.required' => 'Event title is required.',
            'title.max' => 'Event title cannot exceed 255 characters.',
            'slug.unique' => 'This slug is already taken. Please use a different one.',
            'image.url' => 'Please provide a valid image URL.',
            'icon.url' => 'Please provide a valid icon URL.',
            'image_file.image' => 'Event image must be an image file.',
            'image_file.mimes' => 'Event image must be a jpeg, png, jpg, gif, or webp file.',
            'image_file.max' => 'Event image must not exceed 2MB.',
            'icon_file.image' => 'Event icon must be an image file.',
            'icon_file.mimes' => 'Event icon must be a jpeg, png, jpg, gif, or webp file.',
            'icon_file.max' => 'Event icon must not exceed 2MB.',
            'end_date.after' => 'End date must be after start date.',
        ]);

        // Validate markets data with custom messages
        $marketsValidated = $request->validate([
            'markets' => 'required|array|min:1',
            'markets.*.question' => 'required|string|max:255',
            'markets.*.description' => 'nullable|string',
            'markets.*.slug' => 'nullable|string|max:255',
            'markets.*.image' => 'nullable|url|max:500',
            'markets.*.icon' => 'nullable|url|max:500',
            'markets.*.image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'markets.*.icon_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'markets.*.start_date' => 'nullable|date',
            'markets.*.end_date' => 'nullable|date',
            'markets.*.yes_price' => 'nullable|numeric|min:0|max:1',
            'markets.*.no_price' => 'nullable|numeric|min:0|max:1',
        ], [
            'markets.required' => 'At least one market is required.',
            'markets.min' => 'At least one market is required.',
            'markets.*.question.required' => 'Market question is required.',
            'markets.*.question.max' => 'Market question cannot exceed 255 characters.',
            'markets.*.image.url' => 'Please provide a valid image URL.',
            'markets.*.icon.url' => 'Please provide a valid icon URL.',
            'markets.*.image_file.image' => 'Market image must be an image file.',
            'markets.*.image_file.mimes' => 'Market image must be a jpeg, png, jpg, gif, or webp file.',
            'markets.*.image_file.max' => 'Market image must not exceed 2MB.',
            'markets.*.icon_file.image' => 'Market icon must be an image file.',
            'markets.*.icon_file.mimes' => 'Market icon must be a jpeg, png, jpg, gif, or webp file.',
            'markets.*.icon_file.max' => 'Market icon must not exceed 2MB.',
            'markets.*.yes_price.numeric' => 'Yes price must be a number.',
            'markets.*.yes_price.min' => 'Yes price must be between 0 and 1.',
            'markets.*.yes_price.max' => 'Yes price must be between 0 and 1.',
            'markets.*.no_price.numeric' => 'No price must be a number.',
            'markets.*.no_price.min' => 'No price must be between 0 and 1.',
            'markets.*.no_price.max' => 'No price must be between 0 and 1.',
        ]);

        // Additional validation: Check if yes_price + no_price = 1.0 for each market
        foreach ($marketsValidated['markets'] as $index => $marketData) {
            $yesPrice = isset($marketData['yes_price']) ? (float) $marketData['yes_price'] : 0.5;
            $noPrice = isset($marketData['no_price']) ? (float) $marketData['no_price'] : 0.5;
            $sum = $yesPrice + $noPrice;

            if (abs($sum - 1.0) > 0.001) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors([
                        "markets.{$index}.yes_price" => "Yes and No prices must sum to 1.0 (Current sum: {$sum})"
                    ]);
            }
        }

        // Use database transaction to ensure data consistency
        try {
            DB::beginTransaction();

            // Auto-detect category if not provided
            if (empty($eventValidated['category'])) {
                $eventValidated['category'] = $this->categoryDetector->detect($eventValidated['title']);
            }

            // Generate slug if not provided
            if (empty($eventValidated['slug'])) {
                $eventValidated['slug'] = Str::slug($eventValidated['title']);
                // Ensure unique slug
                $baseSlug = $eventValidated['slug'];
                $counter = 1;
                while (Event::where('slug', $eventValidated['slug'])->exists()) {
                    $eventValidated['slug'] = $baseSlug . '-' . $counter;
                    $counter++;
                }
            }

            // Set defaults
            $eventValidated['active'] = $eventValidated['active'] ?? true;
            $eventValidated['featured'] = $eventValidated['featured'] ?? false;

            // Handle event image file upload (prioritize file over URL)
            if ($request->hasFile('image_file')) {
                $imageFile = $request->file('image_file');
                $imageName = 'event-' . time() . '-' . Str::random(10) . '.' . $imageFile->getClientOriginalExtension();
                $imagePath = $imageFile->storeAs('events', $imageName, 'public');
                $eventValidated['image'] = $imagePath;
            } else {
                // Use URL if provided, otherwise null
                $eventValidated['image'] = $eventValidated['image'] ?? null;
            }

            // Handle event icon file upload (prioritize file over URL)
            if ($request->hasFile('icon_file')) {
                $iconFile = $request->file('icon_file');
                $iconName = 'event-icon-' . time() . '-' . Str::random(10) . '.' . $iconFile->getClientOriginalExtension();
                $iconPath = $iconFile->storeAs('events', $iconName, 'public');
                $eventValidated['icon'] = $iconPath;
            } else {
                // Use URL if provided, otherwise null
                $eventValidated['icon'] = $eventValidated['icon'] ?? null;
            }

            // Remove file fields from validated data before creating event
            unset($eventValidated['image_file'], $eventValidated['icon_file']);

            // Create event
            $event = Event::create($eventValidated);

            // Create markets
            $createdCount = 0;
            foreach ($marketsValidated['markets'] as $index => $marketData) {
                // Generate slug if not provided
                if (empty($marketData['slug'])) {
                    $marketData['slug'] = Str::slug($marketData['question']);
                    // Ensure unique slug
                    $baseSlug = $marketData['slug'];
                    $counter = 1;
                    while (Market::where('slug', $marketData['slug'])->exists()) {
                        $marketData['slug'] = $baseSlug . '-' . $counter;
                        $counter++;
                    }
                }

                // Handle market image file upload (prioritize file over URL)
                $marketImage = null;
                if ($request->hasFile("markets.{$index}.image_file")) {
                    $imageFile = $request->file("markets.{$index}.image_file");
                    $imageName = 'market-' . time() . '-' . Str::random(10) . '.' . $imageFile->getClientOriginalExtension();
                    $imagePath = $imageFile->storeAs('markets', $imageName, 'public');
                    $marketImage = $imagePath;
                } else {
                    // Use URL if provided, otherwise null
                    $marketImage = $marketData['image'] ?? null;
                }

                // Handle market icon file upload (prioritize file over URL)
                $marketIcon = null;
                if ($request->hasFile("markets.{$index}.icon_file")) {
                    $iconFile = $request->file("markets.{$index}.icon_file");
                    $iconName = 'market-icon-' . time() . '-' . Str::random(10) . '.' . $iconFile->getClientOriginalExtension();
                    $iconPath = $iconFile->storeAs('markets', $iconName, 'public');
                    $marketIcon = $iconPath;
                } else {
                    // Use URL if provided, otherwise null
                    $marketIcon = $marketData['icon'] ?? null;
                }

                // Set outcome prices
                $outcomePrices = [];
                if (isset($marketData['yes_price']) && isset($marketData['no_price'])) {
                    $outcomePrices = [
                        (string) $marketData['no_price'],
                        (string) $marketData['yes_price']
                    ];
                } else {
                    // Default prices
                    $outcomePrices = ['0.5', '0.5'];
                }

                Market::create([
                    'event_id' => $event->id,
                    'question' => $marketData['question'],
                    'slug' => $marketData['slug'],
                    'description' => $marketData['description'] ?? null,
                    'image' => $marketImage,
                    'icon' => $marketIcon,
                    'start_date' => $marketData['start_date'] ?? null,
                    'end_date' => $marketData['end_date'] ?? null,
                    'outcome_prices' => json_encode($outcomePrices),
                    'outcomes' => json_encode(['No', 'Yes']),
                    'active' => true,
                ]);

                $createdCount++;
            }

            // Commit transaction if everything is successful
            DB::commit();

            return redirect()
                ->route('admin.events.show', $event)
                ->with('success', "Event created successfully with {$createdCount} market(s).");
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            Log::error('Error creating event with markets: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while creating the event. Please try again.']);
        }
    }

    /**
     * Display the specified event
     */
    public function show(Event $event)
    {
        // In admin panel, show ALL comments (both active and inactive) - Optimized with limits
        $event->load([
            'markets' => function($q) {
                $q->select(['id', 'event_id', 'question', 'slug', 'active', 'closed', 'volume', 'created_at'])
                  ->latest()
                  ->limit(50); // Limit markets shown
            },
            'tags' => function($q) {
                $q->select(['id', 'label', 'slug']);
            },
            'comments' => function ($query) {
                $query->select(['id', 'event_id', 'user_id', 'parent_comment_id', 'comment', 'is_active', 'created_at'])
                    ->whereNull('parent_comment_id')
                    ->with(['user' => function($uq) {
                        $uq->select(['id', 'name', 'email']);
                    }, 'replies' => function ($replyQuery) {
                        $replyQuery->select(['id', 'event_id', 'user_id', 'parent_comment_id', 'comment', 'is_active', 'created_at'])
                            ->with(['user' => function($uq) {
                                $uq->select(['id', 'name', 'email']);
                            }])
                            ->limit(10); // Limit replies per comment
                    }])
                    ->latest()
                    ->limit(50); // Limit comments shown
            }
        ]);

        // Optimize: Get both counts in a single query to avoid duplicate queries
        $commentsBaseQuery = \App\Models\MarketComment::where('market_id', $event->id);
        $totalCommentsCount = (clone $commentsBaseQuery)->count();
        $activeCommentsCount = (clone $commentsBaseQuery)
            ->where(function ($q) {
                $q->where('is_active', true)
                    ->orWhereNull('is_active'); // For backward compatibility
            })
            ->count();

        return view('backend.events.show', compact('event', 'totalCommentsCount', 'activeCommentsCount'));
    }

    /**
     * Show the form for editing the specified event
     */
    public function edit(Event $event)
    {
        $categories = $this->categoryDetector->getAvailableCategories();
        return view('backend.events.edit', compact('event', 'categories'));
    }

    /**
     * Update the specified event
     * Category will be re-detected if title changes
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
        ]);

        // Re-detect category if title changed and category not manually set
        if ($event->title !== $validated['title'] && empty($validated['category'])) {
            $validated['category'] = $this->categoryDetector->detect($validated['title']);
        }

        $event->update($validated);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }

    /**
     * Re-detect category for an existing event
     */
    public function redetectCategory(Event $event)
    {
        $oldCategory = $event->category;
        $event->detectCategory();
        $event->save();

        return response()->json([
            'success' => true,
            'old_category' => $oldCategory,
            'new_category' => $event->category,
            'message' => "Category updated from '{$oldCategory}' to '{$event->category}'"
        ]);
    }

    /**
     * Bulk re-detect categories for all events
     */
    public function bulkRedetectCategories()
    {
        // Optimize: Process in chunks to avoid memory issues
        $updated = 0;
        $totalProcessed = 0;
        
        Event::whereNotNull('title')
            ->select(['id', 'title', 'category'])
            ->chunk(100, function ($events) use (&$updated, &$totalProcessed) {
                foreach ($events as $event) {
                    $oldCategory = $event->category;
                    $event->detectCategory();

                    if ($oldCategory !== $event->category) {
                        $event->save();
                        $updated++;
                    }
                    $totalProcessed++;
                }
            });

        return response()->json([
            'success' => true,
            'total_events' => $totalProcessed,
            'updated' => $updated,
            'message' => "Updated categories for {$updated} out of {$totalProcessed} events."
        ]);
    }
}

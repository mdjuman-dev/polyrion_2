<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\CategoryDetector;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    protected CategoryDetector $categoryDetector;

    public function __construct(CategoryDetector $categoryDetector)
    {
        $this->categoryDetector = $categoryDetector;
    }

    /**
     * Display a listing of events
     */
    public function index(Request $request)
    {
        $query = Event::query();

        // Filter by category if provided
        if ($request->has('category') && $request->category !== 'all') {
            $query->byCategory($request->category);
        }

        $events = $query->with('markets')->latest()->paginate(20);
        $categories = $this->categoryDetector->getAvailableCategories();

        return view('backend.events.index', compact('events', 'categories'));
    }

    /**
     * Show the form for creating a new event
     */
    public function create()
    {
        $categories = $this->categoryDetector->getAvailableCategories();
        return view('backend.events.create', compact('categories'));
    }

    /**
     * Store a newly created event
     * Category will be automatically detected from title
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50', // Optional: can override auto-detection
        ]);

        // Auto-detect category if not provided
        if (empty($validated['category'])) {
            $validated['category'] = $this->categoryDetector->detect($validated['title']);
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $event = Event::create($validated);

        return redirect()
            ->route('backend.events.index')
            ->with('success', "Event created successfully. Category: {$event->category}");
    }

    /**
     * Display the specified event
     */
    public function show(Event $event)
    {
        $event->load('markets', 'tags');
        return view('backend.events.show', compact('event'));
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
            ->route('backend.events.index')
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()
            ->route('backend.events.index')
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
        $events = Event::whereNotNull('title')->get();
        $updated = 0;

        foreach ($events as $event) {
            $oldCategory = $event->category;
            $event->detectCategory();
            
            if ($oldCategory !== $event->category) {
                $event->save();
                $updated++;
            }
        }

        return response()->json([
            'success' => true,
            'total_events' => $events->count(),
            'updated' => $updated,
            'message' => "Updated categories for {$updated} out of {$events->count()} events."
        ]);
    }
}


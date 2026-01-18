<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SecondaryCategory;
use App\Services\CategoryDetector;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SecondaryCategoryController extends Controller
{
    protected $categoryDetector;

    public function __construct(CategoryDetector $categoryDetector)
    {
        $this->categoryDetector = $categoryDetector;
    }

    /**
     * Display a listing of secondary categories
     */
    public function index(Request $request)
    {
        $query = SecondaryCategory::query();

        // Filter by main category if provided
        if ($request->filled('main_category')) {
            $query->where('main_category', $request->main_category);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->ordered()->paginate(20);
        $mainCategories = $this->categoryDetector->getAvailableCategories();

        return view('backend.secondary-categories.index', compact('categories', 'mainCategories'));
    }

    /**
     * Show the form for creating a new secondary category
     */
    public function create()
    {
        $mainCategories = $this->categoryDetector->getAvailableCategories();
        return view('backend.secondary-categories.create', compact('mainCategories'));
    }

    /**
     * Store a newly created secondary category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:secondary_categories,slug',
            'main_category' => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
            'active' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Category name is required',
            'name.max' => 'Category name cannot exceed 255 characters',
            'main_category.required' => 'Main category is required',
            'slug.unique' => 'This slug is already in use. Please choose a different one',
            'icon.image' => 'Icon must be an image file',
            'icon.mimes' => 'Icon must be a JPEG, JPG, PNG, GIF, or SVG file',
            'icon.max' => 'Icon file size cannot exceed 2MB',
            'description.max' => 'Description cannot exceed 1000 characters',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            
            // Ensure unique slug
            $baseSlug = $validated['slug'];
            $counter = 1;
            while (SecondaryCategory::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        // Handle icon upload
        if ($request->hasFile('icon')) {
            try {
                $iconFile = $request->file('icon');
                $iconName = 'secondary-category-' . time() . '-' . Str::random(10) . '.' . $iconFile->getClientOriginalExtension();
                $iconPath = $iconFile->storeAs('secondary-categories', $iconName, 'public');
                $validated['icon'] = $iconPath;
            } catch (\Exception $e) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['icon' => 'Failed to upload icon: ' . $e->getMessage()]);
            }
        }

        // Set defaults
        $validated['active'] = $validated['active'] ?? true;
        $validated['display_order'] = $validated['display_order'] ?? 0;

        try {
            SecondaryCategory::create($validated);

            return redirect()
                ->route('admin.secondary-categories.index')
                ->with('success', 'Secondary category created successfully!');
        } catch (\Exception $e) {
            // Delete uploaded icon if category creation fails
            if (isset($validated['icon']) && Storage::disk('public')->exists($validated['icon'])) {
                Storage::disk('public')->delete($validated['icon']);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create category: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified secondary category
     */
    public function show(SecondaryCategory $secondaryCategory)
    {
        $secondaryCategory->load(['events' => function($query) {
            $query->latest()->take(10);
        }]);

        return view('backend.secondary-categories.show', compact('secondaryCategory'));
    }

    /**
     * Show the form for editing the specified secondary category
     */
    public function edit(SecondaryCategory $secondaryCategory)
    {
        $mainCategories = $this->categoryDetector->getAvailableCategories();
        return view('backend.secondary-categories.edit', compact('secondaryCategory', 'mainCategories'));
    }

    /**
     * Update the specified secondary category
     */
    public function update(Request $request, SecondaryCategory $secondaryCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:secondary_categories,slug,' . $secondaryCategory->id,
            'main_category' => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
            'remove_icon' => 'nullable|boolean',
            'active' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Category name is required',
            'name.max' => 'Category name cannot exceed 255 characters',
            'main_category.required' => 'Main category is required',
            'slug.unique' => 'This slug is already in use. Please choose a different one',
            'icon.image' => 'Icon must be an image file',
            'icon.mimes' => 'Icon must be a JPEG, JPG, PNG, GIF, or SVG file',
            'icon.max' => 'Icon file size cannot exceed 2MB',
            'description.max' => 'Description cannot exceed 1000 characters',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            
            // Ensure unique slug
            $baseSlug = $validated['slug'];
            $counter = 1;
            while (SecondaryCategory::where('slug', $validated['slug'])->where('id', '!=', $secondaryCategory->id)->exists()) {
                $validated['slug'] = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        $oldIcon = $secondaryCategory->icon;

        // Handle icon removal
        if ($request->has('remove_icon') && $request->remove_icon) {
            if ($oldIcon && Storage::disk('public')->exists($oldIcon)) {
                Storage::disk('public')->delete($oldIcon);
            }
            $validated['icon'] = null;
        }
        // Handle icon upload
        elseif ($request->hasFile('icon')) {
            try {
                $iconFile = $request->file('icon');
                $iconName = 'secondary-category-' . time() . '-' . Str::random(10) . '.' . $iconFile->getClientOriginalExtension();
                $iconPath = $iconFile->storeAs('secondary-categories', $iconName, 'public');
                
                // Delete old icon
                if ($oldIcon && Storage::disk('public')->exists($oldIcon)) {
                    Storage::disk('public')->delete($oldIcon);
                }
                
                $validated['icon'] = $iconPath;
            } catch (\Exception $e) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['icon' => 'Failed to upload icon: ' . $e->getMessage()]);
            }
        }

        // Set defaults
        $validated['active'] = $validated['active'] ?? false;
        $validated['display_order'] = $validated['display_order'] ?? 0;

        // Remove remove_icon from validated data
        unset($validated['remove_icon']);

        try {
            $secondaryCategory->update($validated);

            return redirect()
                ->route('admin.secondary-categories.index')
                ->with('success', 'Secondary category updated successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update category: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified secondary category
     */
    public function destroy(SecondaryCategory $secondaryCategory)
    {
        try {
            // Check if category has events
            $eventsCount = $secondaryCategory->events()->count();
            
            if ($eventsCount > 0) {
                return redirect()
                    ->back()
                    ->withErrors(['error' => "Cannot delete this category. It has {$eventsCount} event(s) associated with it. Please reassign or delete those events first."]);
            }

            // Delete icon if exists
            if ($secondaryCategory->icon && Storage::disk('public')->exists($secondaryCategory->icon)) {
                Storage::disk('public')->delete($secondaryCategory->icon);
            }

            $secondaryCategory->delete();

            return redirect()
                ->route('admin.secondary-categories.index')
                ->with('success', 'Secondary category deleted successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to delete category: ' . $e->getMessage()]);
        }
    }

    /**
     * Get secondary categories by main category (AJAX)
     */
    public function getByMainCategory(Request $request)
    {
        $mainCategory = $request->get('main_category');
        
        if (empty($mainCategory)) {
            return response()->json([
                'success' => false,
                'message' => 'Main category is required',
            ], 400);
        }

        $categories = SecondaryCategory::active()
            ->byMainCategory($mainCategory)
            ->ordered()
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'success' => true,
            'categories' => $categories,
        ]);
    }
}






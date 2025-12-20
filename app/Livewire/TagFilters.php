<?php

namespace App\Livewire;

use App\Models\Tag;
use Livewire\Component;

class TagFilters extends Component
{
    public $selectedTag = null;

    public function selectTag($tagSlug)
    {
        $this->selectedTag = $tagSlug;
        $this->dispatch('tag-selected', tagSlug: $tagSlug);
    }

    public function clearFilter()
    {
        $this->selectedTag = null;
        $this->dispatch('tag-selected', tagSlug: null);
    }

    public function render()
    {
        try {
        $tags = Tag::orderBy('label', 'asc')->get();
        } catch (\Illuminate\Database\QueryException $e) {
            // If database connection fails, return empty collection
            \Log::error('Failed to load tags: ' . $e->getMessage());
            $tags = collect([]);
        } catch (\Exception $e) {
            // Catch any other exceptions
            \Log::error('Error loading tags: ' . $e->getMessage());
            $tags = collect([]);
        }

        return view('livewire.tag-filters', [
            'tags' => $tags
        ]);
    }
}

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
        $tags = Tag::orderBy('label', 'asc')->get();

        return view('livewire.tag-filters', [
            'tags' => $tags
        ]);
    }
}

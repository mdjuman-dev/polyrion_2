<?php

namespace App\Livewire;

use App\Services\CategoryDetector;
use Livewire\Component;

class CategoryNavigation extends Component
{
    public $currentCategory = null;

    public function mount($category = null)
    {
        $this->currentCategory = $category;
    }

    public function render(CategoryDetector $categoryDetector)
    {
        $categories = $categoryDetector->getAvailableCategories();
        
        // Add "Other" category
        $categories[] = 'Other';
        
        // Add "Mentions" category
        $categories[] = 'Mentions';

        return view('livewire.category-navigation', [
            'categories' => $categories
        ]);
    }
}


<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class MarketsGrid extends Component
{
    use WithPagination;

    public function render()
    {
        $events = Event::with('markets')->paginate(50);

        return view('livewire.markets-grid', [
            'events' => $events
        ]);
    }
}

<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class EventCard extends Component
{
    public $eventId;
    public $titleLength = 60;
    public $keyPrefix = 'event';
    public $showNewBadge = true;
    public $newBadgeThreshold = 10;

    public function mount($event, $titleLength = 60, $keyPrefix = 'event', $showNewBadge = true, $newBadgeThreshold = 10)
    {
        // Handle both Event model and event ID
        if ($event instanceof Event) {
            $this->eventId = $event->id;
        } else {
            $this->eventId = $event;
        }
        
        $this->titleLength = $titleLength;
        $this->keyPrefix = $keyPrefix;
        $this->showNewBadge = $showNewBadge;
        $this->newBadgeThreshold = $newBadgeThreshold;
    }

    public function getEventProperty()
    {
        return Event::with('markets')->find($this->eventId);
    }

    public function render()
    {
        return view('livewire.event-card');
    }
}


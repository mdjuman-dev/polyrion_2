<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Event;
use App\Models\SavedEvent;
use Illuminate\Support\Facades\Auth;

class SaveEvent extends Component
{
    public $event;
    public $eventId;
    public $isSaved = false;

    public function mount($event)
    {
        $this->event = $event;
        $this->eventId = $event->id ?? null;
        $this->checkIfSaved();
    }

    public function checkIfSaved()
    {
        if (!Auth::check() || !$this->eventId) {
            $this->isSaved = false;
            return;
        }

        // Use eager-loaded relationship if available (from controller)
        if ($this->event && $this->event->relationLoaded('savedByUsers')) {
            $this->isSaved = $this->event->savedByUsers
                ->contains('id', Auth::id());
        } else {
            // Fallback: query database (with cache)
            $cacheKey = "event_saved:{$this->eventId}:" . Auth::id();
            $this->isSaved = \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function() {
                return SavedEvent::where('user_id', Auth::id())
                    ->where('event_id', $this->eventId)
                    ->exists();
            });
        }
    }

    public function saveEvent()
    {
        if (!Auth::check()) {
            $this->dispatch('event-saved', [
                'message' => 'Please login to save events',
                'type' => 'error'
            ]);
            return;
        }

        if (!$this->eventId) {
            $this->dispatch('event-saved', [
                'message' => 'Invalid event',
                'type' => 'error'
            ]);
            return;
        }

        $savedEvent = SavedEvent::where('user_id', Auth::id())
            ->where('event_id', $this->eventId)
            ->first();

        if ($savedEvent) {
            // Unsave
            $savedEvent->delete();
            $this->isSaved = false;
            
            // Clear cache
            \Illuminate\Support\Facades\Cache::forget("event_saved:{$this->eventId}:" . Auth::id());
            
            $this->dispatch('event-saved', [
                'message' => 'Event removed from saved',
                'type' => 'success'
            ]);
        } else {
            // Save
            SavedEvent::create([
                'user_id' => Auth::id(),
                'event_id' => $this->eventId
            ]);
            $this->isSaved = true;
            
            // Clear cache
            \Illuminate\Support\Facades\Cache::forget("event_saved:{$this->eventId}:" . Auth::id());
            
            $this->dispatch('event-saved', [
                'message' => 'Event saved successfully!',
                'type' => 'success'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.save-event');
    }
}

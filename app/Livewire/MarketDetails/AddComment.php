<?php

namespace App\Livewire\MarketDetails;

use App\Models\Event;
use App\Models\EventComment;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AddComment extends Component
{
    public $event;
    public $commentText = '';

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function addComment()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to comment');
            return;
        }

        $this->validate([
            'commentText' => 'required|min:1|max:1000',
        ]);

        EventComment::create([
            'event_id' => $this->event->id,
            'user_id' => Auth::id(),
            'comment_text' => $this->commentText,
        ]);

        $this->commentText = '';
        $this->dispatch('commentAdded');
    }

    public function render()
    {
        return view('livewire.market-details.add-comment');
    }
}

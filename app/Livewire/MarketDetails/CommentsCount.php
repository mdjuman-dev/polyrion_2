<?php

namespace App\Livewire\MarketDetails;

use App\Models\Event;
use App\Models\MarketComment;
use Livewire\Component;

class CommentsCount extends Component
{
    public $event;

    protected $listeners = ['commentAdded' => '$refresh', 'replyAdded' => '$refresh'];

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function render()
    {
        // Use EventComment instead of MarketComment and cache the count
        $count = \App\Models\EventComment::where('event_id', $this->event->id)
            ->whereNull('parent_comment_id')
            ->where(function ($q) {
                $q->where('is_active', true)
                    ->orWhereNull('is_active');
            })
            ->count();

        return view('livewire.market-details.comments-count', [
            'count' => $count,
        ]);
    }
}

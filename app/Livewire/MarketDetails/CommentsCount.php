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
        $count = MarketComment::where('market_id', $this->event->id)
            ->whereNull('parent_comment_id')
            ->count();

        return view('livewire.market-details.comments-count', [
            'count' => $count,
        ]);
    }
}

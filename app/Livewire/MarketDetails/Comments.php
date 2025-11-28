<?php

namespace App\Livewire\MarketDetails;

use App\Models\Event;
use App\Models\MarketComment;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Comments extends Component
{
    public $event;
    public $replyingTo = null;
    public $replyText = '';
    public $showReplies = [];

    protected $listeners = ['commentAdded' => '$refresh', 'replyAdded' => '$refresh', 'commentLiked' => '$refresh'];

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function toggleReply($commentId)
    {
        if ($this->replyingTo === $commentId) {
            $this->replyingTo = null;
            $this->replyText = '';
        } else {
            $this->replyingTo = $commentId;
            $this->replyText = '';
        }
    }

    public function addReply($commentId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to reply');
            return;
        }

        $this->validate([
            'replyText' => 'required|min:1|max:1000',
        ]);

        $parentComment = MarketComment::findOrFail($commentId);

        MarketComment::create([
            'market_id' => $this->event->id,
            'user_id' => Auth::id(),
            'comment_text' => $this->replyText,
            'parent_comment_id' => $commentId,
        ]);

        // Update replies count
        $parentComment->increment('replies_count');

        $this->replyingTo = null;
        $this->replyText = '';
        $this->dispatch('replyAdded');
    }

    public function toggleShowReplies($commentId)
    {
        if (isset($this->showReplies[$commentId])) {
            unset($this->showReplies[$commentId]);
        } else {
            $this->showReplies[$commentId] = true;
        }
    }

    public function likeComment($commentId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to like comments');
            return;
        }

        $comment = MarketComment::findOrFail($commentId);
        $userId = Auth::id();

        // Check if user already liked this comment
        $alreadyLiked = $comment->likes()->where('user_id', $userId)->exists();

        if ($alreadyLiked) {
            // Unlike: remove from pivot table and decrement count
            $comment->likes()->detach($userId);
            $comment->decrement('likes_count');
        } else {
            // Like: add to pivot table and increment count
            $comment->likes()->attach($userId);
            $comment->increment('likes_count');
        }

        $this->dispatch('commentLiked');
    }

    public function isLiked($commentId)
    {
        if (!Auth::check()) {
            return false;
        }

        $comment = MarketComment::findOrFail($commentId);
        return $comment->likes()->where('user_id', Auth::id())->exists();
    }

    public function render()
    {
        $comments = MarketComment::where('market_id', $this->event->id)
            ->whereNull('parent_comment_id')
            ->with(['user', 'replies.user', 'replies.likes', 'likes'])
            ->latest()
            ->get();

        $commentsCount = MarketComment::where('market_id', $this->event->id)
            ->whereNull('parent_comment_id')
            ->count();

        return view('livewire.market-details.comments', [
            'comments' => $comments,
            'commentsCount' => $commentsCount,
        ]);
    }
}

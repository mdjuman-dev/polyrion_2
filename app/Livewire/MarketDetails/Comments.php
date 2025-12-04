<?php

namespace App\Livewire\MarketDetails;

use App\Models\Event;
use App\Models\EventComment;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Comments extends Component
{
    public $event;
    public $replyingTo = null;
    public $replyText = '';
    public $showReplies = [];
    public $commentsFetched = false;

    protected $listeners = ['commentAdded' => '$refresh', 'replyAdded' => '$refresh', 'commentLiked' => '$refresh', 'commentsFetched' => 'handleCommentsFetched'];

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function handleCommentsFetched()
    {
        $this->commentsFetched = true;
        $this->dispatch('$refresh');
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

        $parentComment = EventComment::findOrFail($commentId);

        EventComment::create([
            'event_id' => $this->event->id,
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

        $comment = EventComment::findOrFail($commentId);
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

        $comment = EventComment::findOrFail($commentId);
        return $comment->likes()->where('user_id', Auth::id())->exists();
    }

    public function fetchPolymarketComments()
    {
        // Prevent multiple simultaneous calls
        if ($this->commentsFetched) {
            return;
        }

        try {
            $url = url('/api/event/' . $this->event->id . '/comments');
            $response = \Illuminate\Support\Facades\Http::timeout(30)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['success']) && $data['success']) {
                    // Comments are synced to database by the API endpoint
                    $this->commentsFetched = true;
                    // Refresh the component to show new comments
                    $this->dispatch('commentsFetched');
                }
            } else {
                \Illuminate\Support\Facades\Log::warning('Polymarket comments API call failed', [
                    'status' => $response->status(),
                    'event_id' => $this->event->id,
                    'url' => $url,
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to fetch Polymarket comments', [
                'error' => $e->getMessage(),
                'event_id' => $this->event->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function render()
    {
        // Fetch comments from API on first render if not already fetched
        if (!$this->commentsFetched) {
            $this->fetchPolymarketComments();
        }

        // Frontend: Only show active comments
        $comments = EventComment::where('event_id', $this->event->id)
            ->whereNull('parent_comment_id')
            ->where(function ($q) {
                $q->where('is_active', true)
                    ->orWhereNull('is_active'); // For backward compatibility
            })
            ->with(['user', 'replies' => function ($replyQuery) {
                $replyQuery->where(function ($q) {
                    $q->where('is_active', true)
                        ->orWhereNull('is_active');
                })->with('user', 'likes');
            }, 'replies.likes', 'likes'])
            ->orderBy('created_at', 'desc')
            ->get();

        $commentsCount = EventComment::where('event_id', $this->event->id)
            ->whereNull('parent_comment_id')
            ->where(function ($q) {
                $q->where('is_active', true)
                    ->orWhereNull('is_active');
            })
            ->count();

        return view('livewire.market-details.comments', [
            'comments' => $comments,
            'commentsCount' => $commentsCount,
        ]);
    }
}

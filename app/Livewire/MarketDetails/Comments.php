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

        // Clear cache when reply is added
        $this->clearCommentsCache();

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

        // Clear cache when likes change
        $this->clearCommentsCache();
        
        $this->dispatch('commentLiked');
    }

    public function isLiked($commentId)
    {
        if (!Auth::check()) {
            return false;
        }

        // Use cached data from view bag (set in render method)
        $userLikedComments = view()->shared('userLikedComments', []);
        return isset($userLikedComments[$commentId]);
    }
    
    /**
     * Clear comments cache when data changes
     */
    private function clearCommentsCache()
    {
        \Illuminate\Support\Facades\Cache::forget("event_comments:{$this->event->id}");
        \Illuminate\Support\Facades\Cache::forget("event_comments_count:{$this->event->id}");
    }

    public function fetchPolymarketComments()
    {
        // Prevent multiple simultaneous calls
        if ($this->commentsFetched) {
            return;
        }

        try {
            $url = url('/api/event/' . $this->event->id . '/comments');
            // Reduced timeout from 30s to 5s to prevent blocking
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get($url);

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
            ]);
        }
    }

    public function render()
    {
        // Cache key for comments (event-specific, updates every 30 seconds)
        $cacheKey = "event_comments:{$this->event->id}";
        $cacheTTL = 30; // 30 seconds cache
        
        // Get comments with optimized eager loading (prevent N+1)
        $comments = \Illuminate\Support\Facades\Cache::remember($cacheKey, $cacheTTL, function() {
            return EventComment::where('event_id', $this->event->id)
                ->whereNull('parent_comment_id')
                ->where(function ($q) {
                    $q->where('is_active', true)
                        ->orWhereNull('is_active');
                })
                ->with([
                    'user:id,name,avatar', // Select only needed columns
                    'replies' => function ($replyQuery) {
                        $replyQuery->select([
                            'id', 'event_id', 'user_id', 'comment_text', 'parent_comment_id',
                            'likes_count', 'replies_count', 'is_active', 'created_at', 'updated_at'
                        ])
                        ->where(function ($q) {
                            $q->where('is_active', true)
                                ->orWhereNull('is_active');
                        })
                        ->limit(3)
                        ->with('user:id,name,avatar')
                        ->orderBy('created_at', 'asc');
                    }
                ])
                ->select([
                    'id', 'event_id', 'user_id', 'comment_text', 'parent_comment_id',
                    'likes_count', 'replies_count', 'is_active', 'created_at', 'updated_at'
                ])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        });
        
        // Load likes for current user only (if authenticated) - single query
        if (Auth::check()) {
            $commentIds = $comments->pluck('id')->toArray();
            $replyIds = $comments->flatMap(function($comment) {
                return $comment->replies->pluck('id');
            })->toArray();
            
            $allIds = array_merge($commentIds, $replyIds);
            
            if (!empty($allIds)) {
                // Single query to get all likes for current user
                $userLikes = \Illuminate\Support\Facades\DB::table('comment_likes')
                    ->where('user_id', Auth::id())
                    ->whereIn('comment_id', $allIds)
                    ->pluck('comment_id')
                    ->toArray();
                
                // Store in view bag for efficient lookup
                view()->share('userLikedComments', array_flip($userLikes));
            } else {
                view()->share('userLikedComments', []);
            }
        } else {
            view()->share('userLikedComments', []);
        }

        // Cache count query separately
        $countCacheKey = "event_comments_count:{$this->event->id}";
        $commentsCount = \Illuminate\Support\Facades\Cache::remember($countCacheKey, $cacheTTL, function() {
            return EventComment::where('event_id', $this->event->id)
                ->whereNull('parent_comment_id')
                ->where(function ($q) {
                    $q->where('is_active', true)
                        ->orWhereNull('is_active');
                })
                ->count();
        });

        return view('livewire.market-details.comments', [
            'comments' => $comments,
            'commentsCount' => $commentsCount,
        ]);
    }
}

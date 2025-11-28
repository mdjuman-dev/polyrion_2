<div>
    <div class="comments-section">
        <livewire:market-details.add-comment :event="$event" />
    </div>

    <div class="comment-list">
        @forelse($comments as $comment)
            <div class="comment-section">
                <div class="comment-header">
                    <div class="comment-avatar">
                        @if ($comment->user->avatar)
                            <img class="comment-avatar-img" src="{{ $comment->user->avatar }}"
                                alt="{{ $comment->user->name }}">
                        @else
                            <div class="comment-avatar-gradient">{{ strtoupper(substr($comment->user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="comment-meta">
                        <div class="comment-author-row">
                            <span class="comment-author">{{ $comment->user->name }}</span>
                            <span class="comment-date">{{ format_time_ago($comment->created_at) }}</span>
                        </div>
                    </div>
                </div>
                <div class="comment-body">
                    {{ $comment->comment_text }}
                </div>
                <div class="comment-actions">
                    <button wire:click="likeComment({{ $comment->id }})"
                        class="comment-action {{ $this->isLiked($comment->id) ? 'liked' : '' }}"
                        wire:loading.attr="disabled">
                        <i class="{{ $this->isLiked($comment->id) ? 'fas' : 'far' }} fa-heart"></i>
                        {{ $comment->likes_count }}
                    </button>
                    @auth
                        <button wire:click="toggleReply({{ $comment->id }})" class="comment-action reply-btn">
                            Reply
                        </button>
                    @endauth
                </div>

                @if ($replyingTo === $comment->id)
                    <div class="comment-reply-input-inline">
                        <input wire:model="replyText" wire:keydown.enter="addReply({{ $comment->id }})"
                            class="comment-reply-input-inline-field" type="text" placeholder="Add a comment"
                            autofocus>
                        <button wire:click="addReply({{ $comment->id }})" class="comment-reply-submit-inline"
                            wire:loading.attr="disabled">Post</button>
                    </div>
                @endif

                @if ($comment->replies->count() > 0)
                    <div class="comment-replies-toggle">
                        <button wire:click="toggleShowReplies({{ $comment->id }})" class="show-replies-btn">
                            @if (isset($showReplies[$comment->id]))
                                <i class="fas fa-chevron-up"></i> Hide {{ $comment->replies->count() }}
                                {{ $comment->replies->count() == 1 ? 'Reply' : 'Replies' }}
                            @else
                                <i class="fas fa-chevron-down"></i> Show {{ $comment->replies->count() }}
                                {{ $comment->replies->count() == 1 ? 'Reply' : 'Replies' }}
                            @endif
                        </button>
                    </div>

                    @if (isset($showReplies[$comment->id]))
                        <div class="comment-replies">
                            @foreach ($comment->replies as $reply)
                                <div class="comment-reply">
                                    <div class="comment-reply-header">
                                        <div class="comment-reply-avatar">
                                            @if ($reply->user->avatar)
                                                <img class="comment-reply-avatar-img" src="{{ $reply->user->avatar }}"
                                                    alt="{{ $reply->user->name }}">
                                            @else
                                                <div class="comment-reply-avatar-gradient">
                                                    {{ strtoupper(substr($reply->user->name, 0, 1)) }}</div>
                                            @endif
                                        </div>
                                        <div class="comment-reply-meta">
                                            <div class="comment-reply-author-row">
                                                <span class="comment-reply-author">{{ $reply->user->name }}</span>
                                                <span
                                                    class="comment-reply-date">{{ format_time_ago($reply->created_at) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="comment-reply-body">
                                        {{ $reply->comment_text }}
                                    </div>
                                    <div class="comment-reply-actions">
                                        <button wire:click="likeComment({{ $reply->id }})"
                                            class="comment-action {{ $this->isLiked($reply->id) ? 'liked' : '' }}"
                                            wire:loading.attr="disabled">
                                            <i class="{{ $this->isLiked($reply->id) ? 'fas' : 'far' }} fa-heart"></i>
                                            {{ $reply->likes_count }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        @empty
            <div class="no-comments">
                <p>No comments yet. Be the first to comment!</p>
            </div>
        @endforelse
    </div>
</div>

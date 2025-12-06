<div>
    <div class="comments-section">
        <livewire:market-details.add-comment :event="$event" />
    </div>

    <div class="comment-list">
        @forelse($comments as $comment)
            <div class="comment-section">
                <div class="comment-header">
                    <div class="comment-avatar">
                        @php
                            $profileData = is_array($comment->profile_data)
                                ? $comment->profile_data
                                : (is_string($comment->profile_data)
                                    ? json_decode($comment->profile_data, true)
                                    : []);
                            $userName = $comment->user
                                ? $comment->user->name
                                : $profileData['name'] ?? ($profileData['pseudonym'] ?? 'Anonymous');
                            $userAvatar = $comment->user
                                ? $comment->user->avatar
                                : $profileData['profileImage'] ??
                                    ($profileData['profileImageOptimized']['imageUrlOptimized'] ?? null);
                        @endphp
                        @if ($userAvatar)
                            <img class="comment-avatar-img" src="{{ $userAvatar }}" alt="{{ $userName }}">
                        @else
                            <div class="comment-avatar-gradient">{{ strtoupper(substr($userName, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="comment-meta">
                        <div class="comment-author-row">
                            <span class="comment-author">{{ $userName }}</span>
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
                                @php
                                    $replyProfileData = is_array($reply->profile_data)
                                        ? $reply->profile_data
                                        : (is_string($reply->profile_data)
                                            ? json_decode($reply->profile_data, true)
                                            : []);
                                    $replyUserName = $reply->user
                                        ? $reply->user->name
                                        : $replyProfileData['name'] ?? ($replyProfileData['pseudonym'] ?? 'Anonymous');
                                    $replyUserAvatar = $reply->user
                                        ? $reply->user->avatar
                                        : $replyProfileData['profileImage'] ??
                                            ($replyProfileData['profileImageOptimized']['imageUrlOptimized'] ?? null);
                                @endphp
                                <div class="comment-reply">
                                    <div class="comment-reply-header">
                                        <div class="comment-reply-avatar">
                                            @if ($replyUserAvatar)
                                                <img class="comment-reply-avatar-img" src="{{ $replyUserAvatar }}"
                                                    alt="{{ $replyUserName }}">
                                            @else
                                                <div class="comment-reply-avatar-gradient">
                                                    {{ strtoupper(substr($replyUserName, 0, 1)) }}</div>
                                            @endif
                                        </div>
                                        <div class="comment-reply-meta">
                                            <div class="comment-reply-author-row">
                                                <span class="comment-reply-author">{{ $replyUserName }}</span>
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
            <div class="no-comments" style="text-align: center; padding: 2rem; color: #9ab1c6;">
                @if (!$commentsFetched)
                    <p>Loading comments...</p>
                @else
                    <p>No comments yet. Be the first to comment!</p>
                @endif
            </div>
        @endforelse
    </div>
</div>

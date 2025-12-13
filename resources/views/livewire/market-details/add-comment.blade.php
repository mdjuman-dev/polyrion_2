<div>
    @auth
        <div class="comment-input-container">
            <div class="comment-input-wrapper">
                <input wire:model="commentText" wire:keydown.enter="addComment" class="comment-input" type="text"
                    placeholder="Add a comment">
                <button wire:click="addComment" class="comment-submit-btn"  wire:loading.attr="disabled">
                    <span style='color: var(--accent);'>Post</span> 
                </button>
            </div>
        </div>
    @else
        <div class="comment-input-container">
            <div class="comment-input-wrapper">
                <input class="comment-input" type="text" placeholder="Add a comment" disabled>
                <a href="{{ route('login') }}" class="comment-submit-btn">
                    <span>Login</span>
                </a>
            </div>
        </div>
    @endauth



    @if (session()->has('error'))
        <div class="alert alert-danger mt-2">
            {{ session('error') }}
        </div>
    @endif
</div>

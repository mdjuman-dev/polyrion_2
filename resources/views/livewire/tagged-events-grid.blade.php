<div wire:poll.5s="refreshEvents">
    <!-- Search Bar -->
    <div class="secondary-filters mb-3">
        <div class="filter-top-bar d-lg-flex d-block">
            <div class="row align-items-center justify-content-between w-100">
                <div class="secondary-search-bar" style="flex: 1; max-width: 100%; margin-right: 12px;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="Search events in {{ $tag->label }}" class="secondary-search-input"
                        wire:model.live.debounce.300ms="search" style="width: 100%;">
                    @if ($search)
                        <button type="button"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 5px; z-index: 10;"
                            wire:click="$set('search', '')">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>
                <a href="{{ route('home') }}" class="bookmark-icon-btn" title="Back to Home" style="flex-shrink: 0;">
                    <i class="fas fa-home"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Markets Grid -->
    <div class="markets-grid mt-3 mt-lg-0">
        @foreach ($events as $event)
            <x-event-card 
                :event="$event" 
                :titleLength="90" 
                :keyPrefix="'tagged'" 
                :showNewBadge="true" 
                :newBadgeThreshold="10" />
        @endforeach
    </div>

    @if ($hasMore)
        <div x-intersect.threshold.10="$wire.loadMore()" class="text-center">
            <div wire:loading wire:target="loadMore" class="d-flex align-items-center justify-content-center gap-2">
                <span class="loader"></span>
            </div>
        </div>
    @endif

    @if ($events->count() == 0)
        <div class="market-card empty-state-card">
            <div class="empty-state-icon-wrapper">
                <div class="empty-state-icon-circle">
                    <i class="fas fa-tag"></i>
                </div>
            </div>
            <h3 class="empty-state-title">No Events Found</h3>
            <p class="empty-state-description">
                No events found for this tag. Try browsing other tags or check back later!
            </p>
            <a href="{{ route('home') }}" class="empty-state-btn">
                <i class="fas fa-home"></i>
                <span>Browse All Events</span>
            </a>
        </div>
    @endif
</div>

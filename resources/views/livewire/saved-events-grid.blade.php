<div>
    <!-- Search Bar with Home Button -->
    <div class="secondary-filters mb-3">
        <div class="filter-top-bar d-lg-flex d-block">
            <div class="row align-items-center justify-content-between w-100">
                <div class="secondary-search-bar" style="flex: 1; max-width: 100%; margin-right: 12px;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="Search saved events" class="secondary-search-input"
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

    @if ($events->count() > 0)
        <!-- Markets Grid -->
        <div class="markets-grid mt-3 mt-lg-0">
            @foreach ($events as $event)
                <x-event-card :event="$event" :titleLength="100" :keyPrefix="'saved'" :showNewBadge="true"
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
    @else
        <div class="market-card empty-state-card">
            <div class="empty-state-icon-wrapper">
                <div class="empty-state-icon-circle">
                    <i class="fas fa-bookmark"></i>
                </div>
            </div>
            <h3 class="empty-state-title">No Saved Events</h3>
            <p class="empty-state-description">
                You haven't saved any events yet. Start saving events to access them quickly from your saved list!
            </p>
            <a href="{{ route('home') }}" class="empty-state-btn">
                <i class="fas fa-home"></i>
                <span>Browse Events</span>
            </a>
        </div>
    @endif

</div>

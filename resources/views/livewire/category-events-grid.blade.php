<div wire:poll.5s="refreshEvents" data-component="category-events-grid">
    <!-- Search Bar -->
    <div class="secondary-filters mb-4">
        <div class="filter-top-bar d-lg-flex d-block">
            <div class="row align-items-center">
                <div class="secondary-search-bar">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search events..."
                        class="secondary-search-input" id="categorySearchInput">
                </div>
            </div>
            <a href="{{ route('saved.events') }}" class="bookmark-icon-btn" title="Saved Events">
                <i class="fas fa-bookmark"></i>
            </a>
        </div>
    </div>

    <!-- Markets Grid -->
    @if ($events->count() > 0)
        <div class="markets-grid mt-3 mt-lg-0">
            @foreach ($events as $event)
                <x-event-card 
                    :event="$event" 
                    :titleLength="90" 
                    :keyPrefix="'category'" 
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
    @else
        <div class="text-center py-5">
            <p class="text-muted">No events found in this category.</p>
            @if (!empty($search))
                <p class="text-muted">Try adjusting your search terms.</p>
            @endif
        </div>
    @endif
</div>

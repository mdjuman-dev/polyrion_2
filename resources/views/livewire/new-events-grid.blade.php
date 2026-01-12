<div wire:poll.5s="refreshEvents" data-component="new-events-grid">
    <!-- Search Bar -->
    <div class="secondary-filters mb-4">
        <div class="filter-top-bar d-lg-flex d-block">
            <div class="row align-items-center justify-content-between">
                <div class="secondary-search-bar">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search events..."
                        class="secondary-search-input" id="newSearchInput">
                </div>
                <a href="{{ route('saved.events') }}" class="bookmark-icon-btn" title="Saved Events">
                    <i class="fas fa-bookmark"></i>
                </a>
            </div>
            <div class="filters-section-wrapper ms-lg-4">
                <button class="filter-scroll-btn filter-scroll-left" id="filterScrollLeft">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <livewire:tag-filters />
                <button class="filter-scroll-btn filter-scroll-right" id="filterScrollRight">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Markets Grid -->
    <div class="markets-grid mt-3 mt-lg-0">
        @foreach ($events as $event)
            <x-event-card 
                :event="$event" 
                :titleLength="50" 
                :keyPrefix="'new'" 
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
</div>

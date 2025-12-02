<div wire:poll.5s="refreshEvents" data-component="markets-grid">
    <!-- Markets Grid -->
    <div class="markets-grid mt-3 mt-lg-0">
        @foreach ($events as $event)
            <x-event-card 
                :event="$event" 
                :titleLength="90" 
                :keyPrefix="'grid'" 
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

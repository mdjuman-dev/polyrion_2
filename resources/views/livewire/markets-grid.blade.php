<div wire:poll.5s="refreshEvents" data-component="markets-grid"
    wire:key="markets-grid-{{ $search }}-{{ $sortBy }}-{{ $frequency }}-{{ $status }}">
    <!-- Markets Grid -->
    <div class="markets-grid mt-3 mt-lg-0" class='row'>
        @foreach ($events as $event)
            <x-event-card :event="$event" :titleLength="50" :keyPrefix="'grid'" :showNewBadge="true" :newBadgeThreshold="10" />
        @endforeach
    </div>

    @if ($hasMore)
        <div x-intersect.threshold.10="$wire.loadMore()" 
             x-intersect:enter="$wire.loadMore()"
             class="text-center" 
             style="padding: 20px; min-height: 60px;">
            <div wire:loading wire:target="loadMore" class="d-flex align-items-center justify-content-center gap-2">
                <span class="loader"></span>
                <span style="color: var(--text-secondary); font-size: 14px;">Loading more events...</span>
            </div>
        </div>
   
    @endif

</div>

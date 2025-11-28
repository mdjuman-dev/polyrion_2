<button wire:click="saveEvent" wire:loading.attr="disabled" class="market-card-action-btn {{ $isSaved ? 'saved' : '' }}"
    aria-label="Save Event" title="{{ $isSaved ? 'Event Saved - Click to unsave' : 'Save Event' }}">
    <span wire:loading.remove wire:target="saveEvent">
        @if ($isSaved)
            <i class="fas fa-bookmark" style="color: #28a745;"></i>
        @else
            <i class="far fa-bookmark"></i>
        @endif
    </span>
    <span wire:loading wire:target="saveEvent">
        <i class="fas fa-spinner fa-spin"></i>
    </span>
</button>

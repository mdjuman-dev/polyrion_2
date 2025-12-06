<x-backend.action-buttons>
    @if (!$isActive)
        <x-backend.status-badge status="inactive" size="sm" />
    @endif
    <x-backend.loading-button wire:click="toggleStatus" target="toggleStatus" size="sm" loadingText="Processing..."
        class="btn-{{ $isActive ? 'warning' : 'success' }}" title="{{ $isActive ? 'Deactivate' : 'Activate' }} Comment">
        <i class="fa fa-{{ $isActive ? 'ban' : 'check' }}"></i>
        {{ $isActive ? 'Deactivate' : 'Activate' }}
    </x-backend.loading-button>
    <button type="button" wire:target="delete" wire:loading.attr="disabled"
        class="btn btn-sm btn-danger delete-comment-btn" title="Delete Comment"
        onclick="return confirmDeleteWithToastr(event, '{{ $commentId }}', 'Are you sure you want to delete this comment? This will also delete all replies.')">
        <span wire:loading.remove wire:target="delete">
            <i class="fa fa-trash"></i> Delete
        </span>
        <span wire:loading wire:target="delete">
            <i class="fa fa-spinner fa-spin"></i> Deleting...
        </span>
    </button>
</x-backend.action-buttons>

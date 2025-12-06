@props([
    'id' => 'confirmModal',
    'title' => 'Are you sure?',
    'message' => 'This action cannot be undone!',
    'confirmText' => 'Yes, proceed!',
    'cancelText' => 'Cancel',
    'confirmColor' => '#d33',
    'cancelColor' => '#3085d6',
])

<div id="{{ $id }}" style="display: none;">
    {{ $slot }}
</div>

<script>
    function showConfirmModal({{ $id }}Config) {
        const config = {{ $id }}Config || {
            title: '{{ $title }}',
            text: '{{ $message }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '{{ $confirmColor }}',
            cancelButtonColor: '{{ $cancelColor }}',
            confirmButtonText: '{{ $confirmText }}',
            cancelButtonText: '{{ $cancelText }}',
            reverseButtons: true
        };

        if (typeof Swal !== 'undefined') {
            return Swal.fire(config);
        } else {
            return Promise.resolve({
                isConfirmed: confirm(config.text || config.title)
            });
        }
    }
</script>

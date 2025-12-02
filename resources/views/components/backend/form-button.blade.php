@props([
    'type' => 'submit',
    'size' => 'sm', // sm, xs, md, lg
    'variant' => 'primary', // primary, danger, success, warning, info
    'icon' => null,
    'confirm' => null, // Confirmation message
])

@php
    $sizeClass = 'btn-' . $size;
    $variantClass = 'btn-' . $variant;
    $confirmAttr = $confirm ? "onclick=\"return confirmAction('{$confirm}')\"" : '';
@endphp

<button type="{{ $type }}" class="btn {{ $sizeClass }} {{ $variantClass }}" {{ $confirmAttr }}
    {{ $attributes }}>
    @if ($icon)
        <i class="fa fa-{{ $icon }}"></i>
    @endif
    {{ $slot }}
</button>

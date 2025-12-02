@props([
    'type' => 'button',
    'loadingText' => 'Processing...',
    'target' => null,
    'size' => 'sm', // sm, xs, md, lg
])

@php
    $sizeClass = 'btn-' . $size;
@endphp

<button type="{{ $type }}" @if ($target) wire:target="{{ $target }}" @endif
    wire:loading.attr="disabled" {{ $attributes->merge(['class' => "btn {$sizeClass}"]) }}>
    <span @if ($target) wire:target="{{ $target }}" @endif wire:loading.remove>
        {{ $slot }}
    </span>
    <span @if ($target) wire:target="{{ $target }}" @endif wire:loading>
        <i class="fa fa-spinner fa-spin"></i> {{ $loadingText }}
    </span>
</button>

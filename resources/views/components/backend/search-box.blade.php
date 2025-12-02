@props([
    'placeholder' => 'Search...',
    'name' => 'search',
    'value' => null,
])

@php
    $currentValue = $value ?? request($name);
@endphp

<div class="search-input-wrapper">
    <input type="text" name="{{ $name }}" class="form-control search-input" placeholder="{{ $placeholder }}"
        value="{{ $currentValue }}" autocomplete="off">
    @if ($currentValue)
        <a href="{{ route('admin.events.index', array_filter(request()->except($name))) }}" class="search-clear-btn"
            title="Clear search">
            <i class="fa fa-times"></i>
        </a>
    @endif
</div>

@props([
    'name' => 'filter',
    'label' => 'Filter',
    'options' => [],
    'allOption' => true,
    'allText' => 'All',
    'currentValue' => null,
])

@php
    $currentValue = $currentValue ?? request($name);
@endphp

<select name="{{ $name }}" id="{{ $name }}" class="form-control category-select"
    onchange="this.form.submit()">
    @if ($allOption)
        <option value="all" {{ $currentValue === 'all' || $currentValue === null ? 'selected' : '' }}>
            {{ $allText }}
        </option>
    @endif
    @foreach ($options as $key => $value)
        <option value="{{ $key }}" {{ $currentValue == $key ? 'selected' : '' }}>
            {{ $value }}
        </option>
    @endforeach
</select>

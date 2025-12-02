@props([
    'status' => 'active', // active, inactive, pending, etc.
    'size' => 'normal', // normal, sm, lg
])

@php
    $badgeClasses = [
        'active' => 'badge-success',
        'inactive' => 'badge-warning',
        'pending' => 'badge-info',
        'closed' => 'badge-danger',
        'featured' => 'badge-primary',
        'new' => 'badge-info',
    ];

    $sizeClasses = [
        'sm' => 'badge-sm',
        'normal' => '',
        'lg' => 'badge-lg',
    ];

    $class = ($badgeClasses[$status] ?? 'badge-secondary') . ' ' . ($sizeClasses[$size] ?? '');
@endphp

<span class="badge {{ $class }}" {{ $attributes }}>
    {{ $slot->isEmpty() ? ucfirst($status) : $slot }}
</span>

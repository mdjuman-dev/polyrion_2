@props([
    'size' => 'sm', // sm, xs
    'gap' => '10px',
])

<div class="d-inline-flex align-items-center gap-2 flex-wrap" style="gap: {{ $gap }};">
    {{ $slot }}
</div>

<div class="filters-section mt-lg-0 mt-3" id="filtersSection">
    <button class="filter-btn {{ $selectedTag === null ? 'active' : '' }}" wire:click="clearFilter" type="button">
        All
    </button>
    @foreach ($tags as $tag)
        <button class="filter-btn {{ $selectedTag === $tag->slug ? 'active' : '' }}"
            wire:click="selectTag('{{ $tag->slug }}')" type="button">
            {{ $tag->label }}
        </button>
    @endforeach
</div>

<div class="header-search-wrapper" x-data="{ show: @entangle('showSuggestions') }" x-on:click.away="show = false; $wire.closeSuggestions()"
    style="position: relative; width: 100%;">
    <i class="fas fa-search search-icon"></i>
    <input type="text" placeholder="Search polyrion" class="header-search-input" wire:model.live.debounce.300ms="query"
        x-on:focus="show = true; $wire.showSuggestions = true"
        x-on:keydown.escape="show = false; $wire.closeSuggestions()"
        style="width: 100%; padding: 10px 40px 10px 40px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); font-size: 0.95rem; transition: all 0.3s ease;">

    @if ($query)
        <button type="button" class="header-search-clear" wire:click="$set('query', '')" x-on:click.stop
            style="position: absolute; right: 35px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 5px; z-index: 10;">
            <i class="fas fa-times"></i>
        </button>
    @endif

    <span class="search-shortcut"
        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); font-size: 0.85rem; font-weight: 500; pointer-events: none;">/</span>

    <div class="search-suggestions-dropdown" x-show="show" x-transition
        style="display: none; position: absolute; top: calc(100% + 10px); left: 0; right: 0; background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3); z-index: 1000; max-height: 600px; overflow-y: auto; padding: 16px;">
        @if (empty($query))
            {{-- Browse Filters --}}
            <div class="search-section">
                <div class="search-section-title">BROWSE</div>
                <div class="search-filters">
                    <button type="button" class="search-filter-btn" wire:click="selectFilter('new')">
                        <i class="fas fa-star"></i>
                        <span>New</span>
                    </button>
                    <button type="button" class="search-filter-btn" wire:click="selectFilter('trending')">
                        <i class="fas fa-arrow-trend-up"></i>
                        <span>Trending</span>
                    </button>
                    <button type="button" class="search-filter-btn" wire:click="selectFilter('popular')">
                        <i class="fas fa-fire"></i>
                        <span>Popular</span>
                    </button>
                    <button type="button" class="search-filter-btn" wire:click="selectFilter('liquid')">
                        <i class="fas fa-tint"></i>
                        <span>Liquid</span>
                    </button>
                    <button type="button" class="search-filter-btn" wire:click="selectFilter('ending-soon')">
                        <i class="fas fa-clock"></i>
                        <span>Ending Soon</span>
                    </button>
                    <button type="button" class="search-filter-btn" wire:click="selectFilter('competitive')">
                        <i class="fas fa-trophy"></i>
                        <span>Competitive</span>
                    </button>
                </div>
            </div>

            {{-- Recent Searches --}}
            @if (count($recentSearches) > 0)
                <div class="search-section">
                    <div class="search-section-title">RECENT</div>
                    <div class="recent-searches">
                        @foreach ($recentSearches as $index => $recent)
                            <div class="recent-search-item">
                                <a href="{{ route('market.details', $recent['slug']) }}" class="recent-search-link">
                                    @if (isset($recent['image']))
                                        <img src="{{ $recent['image'] }}" alt="{{ $recent['title'] }}"
                                            class="recent-search-image">
                                    @endif
                                    <span class="recent-search-title">{{ $recent['title'] }}</span>
                                </a>
                                <button type="button" class="recent-search-remove"
                                    wire:click="removeRecentSearch({{ $index }})" wire:click.stop
                                    aria-label="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            {{-- Search Suggestions --}}
            @if ($suggestions->count() > 0)
                <div class="search-section">
                    <div class="search-suggestions">
                        @foreach ($suggestions as $event)
                            <a href="{{ route('market.details', $event->slug) }}" class="search-suggestion-item">
                                <img src="{{ $event->image }}" alt="{{ $event->title }}" class="suggestion-image">
                                <div class="suggestion-content">
                                    <div class="suggestion-title">{{ $event->title }}</div>
                                    @if ($event->volume > 0)
                                        <div class="suggestion-meta">${{ number_format($event->volume / 1000000, 1) }}m
                                            Vol.</div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="search-section">
                    <div class="search-no-results">No results found for "{{ $query }}"</div>
                </div>
            @endif
        @endif
    </div>
</div>

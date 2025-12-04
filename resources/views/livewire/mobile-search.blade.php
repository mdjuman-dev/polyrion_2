<div>
    <div class="mobile-search-header">
        <button class="mobile-search-close" id="mobileSearchClose" wire:click="clearSearch">
            <i class="fas fa-times"></i>
        </button>

        <div class="mobile-search-bar">
            <i class="fas fa-search search-icon"></i>
            <input type="text" placeholder="Search polymarket" id="mobileSearchInput"
                wire:model.live.debounce.300ms="query" x-on:focus="$wire.showSuggestions = true"
                x-on:keydown.escape="$wire.closeSuggestions()" autocomplete="off">

            @if ($query)
                <button type="button" class="mobile-search-clear" wire:click="$set('query', '')"
                    style="position: absolute; right: 50px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 5px; z-index: 10;">
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>

        <div class="mobile-search-tabs">
            <button class="mobile-search-tab {{ $activeTab === 'markets' ? 'active' : '' }}"
                wire:click="setActiveTab('markets')" data-tab="markets">Markets</button>
            <button class="mobile-search-tab {{ $activeTab === 'profiles' ? 'active' : '' }}"
                wire:click="setActiveTab('profiles')" data-tab="profiles">Profiles</button>
        </div>
    </div>
    <div class="mobile-search-content">
        <div class="mobile-search-tab-content {{ $activeTab === 'markets' ? 'active' : '' }}" id="marketsTab">
            <div class="mobile-search-results" id="mobileSearchResults">
                @if (empty($query))
                    {{-- Browse Filters --}}
                    <div class="mobile-search-section">
                        <div class="mobile-search-section-title">BROWSE</div>
                        <div class="mobile-search-filters">
                            <button type="button" class="mobile-search-filter-btn" wire:click="selectFilter('new')">
                                <i class="fas fa-star"></i>
                                <span>New</span>
                            </button>
                            <button type="button" class="mobile-search-filter-btn"
                                wire:click="selectFilter('trending')">
                                <i class="fas fa-arrow-trend-up"></i>
                                <span>Trending</span>
                            </button>
                            <button type="button" class="mobile-search-filter-btn"
                                wire:click="selectFilter('popular')">
                                <i class="fas fa-fire"></i>
                                <span>Popular</span>
                            </button>
                            <button type="button" class="mobile-search-filter-btn" wire:click="selectFilter('liquid')">
                                <i class="fas fa-tint"></i>
                                <span>Liquid</span>
                            </button>
                            <button type="button" class="mobile-search-filter-btn"
                                wire:click="selectFilter('ending-soon')">
                                <i class="fas fa-clock"></i>
                                <span>Ending Soon</span>
                            </button>
                            <button type="button" class="mobile-search-filter-btn"
                                wire:click="selectFilter('competitive')">
                                <i class="fas fa-trophy"></i>
                                <span>Competitive</span>
                            </button>
                        </div>
                    </div>

                    {{-- Recent Searches --}}
                    @if (count($recentSearches) > 0)
                        <div class="mobile-search-section">
                            <div class="mobile-search-section-title">RECENT</div>
                            <div class="mobile-recent-searches">
                                @foreach ($recentSearches as $index => $recent)
                                    <div class="mobile-recent-search-item">
                                        <a href="{{ route('market.details', $recent['slug']) }}"
                                            class="mobile-recent-search-link">
                                            @if (isset($recent['image']))
                                                <img src="{{ $recent['image'] }}" alt="{{ $recent['title'] }}"
                                                    class="mobile-recent-search-image">
                                            @endif
                                            <span class="mobile-recent-search-title">{{ $recent['title'] }}</span>
                                        </a>
                                        <button type="button" class="mobile-recent-search-remove"
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
                        <div class="mobile-search-section">
                            <div class="mobile-search-suggestions">
                                @foreach ($suggestions as $event)
                                    <a href="{{ route('market.details', $event->slug) }}"
                                        class="mobile-search-suggestion-item">
                                        <img src="{{ $event->image }}" alt="{{ $event->title }}"
                                            class="mobile-suggestion-image">
                                        <div class="mobile-suggestion-content">
                                            <div class="mobile-suggestion-title">{{ $event->title }}</div>
                                            @if ($event->volume > 0)
                                                <div class="mobile-suggestion-meta">
                                                    ${{ number_format($event->volume / 1000000, 1) }}m Vol.</div>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mobile-search-section">
                            <div class="mobile-search-no-results">No results found for "{{ $query }}"</div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
        <div class="mobile-search-tab-content {{ $activeTab === 'profiles' ? 'active' : '' }}" id="profilesTab">
            <div class="mobile-search-profiles" id="mobileSearchProfiles">
                <!-- Profile results will appear here -->
                <div class="mobile-search-section">
                    <div class="mobile-search-no-results">Profile search coming soon</div>
                </div>
            </div>
        </div>
    </div>
</div>

@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Politics & Elections - {{ $appName }}</title>
    <meta name="description" content="Explore politics and election prediction markets on {{ $appName }}. Bet on elections, political events, and more.">
    <meta property="og:title" content="Politics & Elections - {{ $appName }}">
    <link rel="canonical" href="{{ $appUrl }}/politics">
@endsection

@section('content')
    <!-- Main Content -->
    <main style="padding: 20px 0;">
        <div class="container">
            <!-- Secondary Search and Filter Bar (Same as Home Page) -->
            <div class="secondary-filters">
                <div class="filter-top-bar d-lg-flex d-block">
                    <div class="row align-items-center">
                        <div class="secondary-search-bar">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" placeholder="Search" class="secondary-search-input" id="politicsSearchInput"
                                style="width: 100%;">
                            <button type="button" id="clearPoliticsSearchBtn"
                                style="display: none; position: absolute; right: 20px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 5px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button class="filter-icon-btn mx-2" id="politicsFilterToggleBtn"><i class="fas fa-sliders-h"></i></button>
                        <a href="{{ route('saved.events') }}" class="bookmark-icon-btn" title="Saved Events">
                            <i class="fas fa-bookmark"></i>
                        </a>
                    </div>
                    <!-- Category Filters - Same Line -->
                    <div class="filters-section-wrapper ms-lg-4">
                        <button class="filter-scroll-btn filter-scroll-left" id="politicsFilterScrollLeft" aria-label="Scroll left">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <div class="filters-section" id="politicsFiltersSection" style="display: flex; gap: 8px; align-items: center; overflow-x: auto; overflow-y: hidden; scroll-behavior: smooth; -webkit-overflow-scrolling: touch; padding: 4px 0;">
                            <!-- All Button -->
                            <a href="{{ route('politics.index') }}?category=all{{ $selectedCountry ? '&country=' . $selectedCountry : '' }}" 
                               class="filter-btn {{ $selectedCategory === 'all' ? 'active' : '' }}">
                                All
                            </a>

                            <!-- Time Period Filters -->
                            <a href="{{ route('politics.index') }}?timeframe=15m{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}{{ $selectedCountry ? '&country=' . $selectedCountry : '' }}" 
                               class="filter-btn {{ request()->get('timeframe') === '15m' ? 'active' : '' }}">
                                15M
                            </a>
                            <a href="{{ route('politics.index') }}?timeframe=1h{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}{{ $selectedCountry ? '&country=' . $selectedCountry : '' }}" 
                               class="filter-btn {{ request()->get('timeframe') === '1h' ? 'active' : '' }}">
                                1H
                            </a>
                            <a href="{{ route('politics.index') }}?timeframe=4h{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}{{ $selectedCountry ? '&country=' . $selectedCountry : '' }}" 
                               class="filter-btn {{ request()->get('timeframe') === '4h' ? 'active' : '' }}">
                                4H
                            </a>
                            <a href="{{ route('politics.index') }}?timeframe=5m{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}{{ $selectedCountry ? '&country=' . $selectedCountry : '' }}" 
                               class="filter-btn {{ request()->get('timeframe') === '5m' ? 'active' : '' }}">
                                5M
                            </a>

                            <!-- Prediction Tags -->
                            <a href="{{ route('politics.index') }}?tag=2025-predictions{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}{{ $selectedCountry ? '&country=' . $selectedCountry : '' }}" 
                               class="filter-btn {{ request()->get('tag') === '2025-predictions' ? 'active' : '' }}">
                                2025 Predictions
                            </a>
                            <a href="{{ route('politics.index') }}?tag=2026-predictions{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}{{ $selectedCountry ? '&country=' . $selectedCountry : '' }}" 
                               class="filter-btn {{ request()->get('tag') === '2026-predictions' ? 'active' : '' }}">
                                2026 Predictions
                            </a>

                            <!-- Secondary Categories -->
                            @if(isset($secondaryCategories) && $secondaryCategories->count() > 0)
                                @foreach($secondaryCategories as $secCategory)
                                    <a href="{{ route('politics.index') }}?secondary_category={{ $secCategory->id }}{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}{{ $selectedCountry ? '&country=' . $selectedCountry : '' }}" 
                                       class="filter-btn {{ ($selectedSecondaryCategory ?? '') == $secCategory->id ? 'active' : '' }}"
                                       title="{{ $secCategory->description }}">
                                        @if($secCategory->icon)
                                            <img src="{{ asset('storage/' . $secCategory->icon) }}" 
                                                 alt="{{ $secCategory->name }}" 
                                                 style="width: 16px; height: 16px; margin-right: 4px; border-radius: 3px; object-fit: cover;">
                                        @endif
                                        {{ $secCategory->name }}
                                        @if($secCategory->active_events_count > 0)
                                            <span class="badge badge-sm" style="background: rgba(255,255,255,0.2); margin-left: 4px; font-size: 10px;">{{ $secCategory->active_events_count }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            @endif

                            <!-- Popular Categories -->
                            @foreach($popularCategories as $category)
                                <a href="{{ route('politics.index') }}?category={{ strtolower(str_replace(' ', '-', $category['name'])) }}{{ $selectedCountry ? '&country=' . $selectedCountry : '' }}" 
                                   class="filter-btn {{ strtolower($selectedCategory) === strtolower(str_replace(' ', '-', $category['name'])) ? 'active' : '' }}">
                                    {{ $category['name'] }}
                                </a>
                            @endforeach

                            <!-- Countries/Regions -->
                            @foreach($countries as $country)
                                @if($country['count'] > 0 || $country['name'] === 'All')
                                    <a href="{{ route('politics.index') }}?country={{ $country['slug'] }}{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}" 
                                       class="filter-btn {{ ($selectedCountry ?? '') === $country['slug'] ? 'active' : '' }}">
                                        {{ $country['name'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                        <button class="filter-scroll-btn filter-scroll-right" id="politicsFilterScrollRight" aria-label="Scroll right">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Events Grid -->
            <div class="row">
                <div class="col-12">
                    <livewire:politics-events-grid :category="$selectedCategory" :country="$selectedCountry" />
                </div>
            </div>
        </div>
    </main>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter Scroll Functionality
            const filtersSection = document.getElementById('politicsFiltersSection');
            const scrollLeftBtn = document.getElementById('politicsFilterScrollLeft');
            const scrollRightBtn = document.getElementById('politicsFilterScrollRight');

            if (filtersSection && scrollLeftBtn && scrollRightBtn) {
                // Check scroll position and update button states
                function updateScrollButtons() {
                    const isScrollable = filtersSection.scrollWidth > filtersSection.clientWidth;
                    const isAtStart = filtersSection.scrollLeft <= 0;
                    const isAtEnd = filtersSection.scrollLeft >= filtersSection.scrollWidth - filtersSection.clientWidth - 1;

                    scrollLeftBtn.style.display = isScrollable && !isAtStart ? 'flex' : 'none';
                    scrollRightBtn.style.display = isScrollable && !isAtEnd ? 'flex' : 'none';
                }

                // Scroll left
                scrollLeftBtn.addEventListener('click', function() {
                    filtersSection.scrollBy({ left: -200, behavior: 'smooth' });
                    setTimeout(updateScrollButtons, 300);
                });

                // Scroll right
                scrollRightBtn.addEventListener('click', function() {
                    filtersSection.scrollBy({ left: 200, behavior: 'smooth' });
                    setTimeout(updateScrollButtons, 300);
                });

                // Update on scroll
                filtersSection.addEventListener('scroll', updateScrollButtons);
                
                // Initial check
                updateScrollButtons();
                window.addEventListener('resize', updateScrollButtons);
            }

            // Search functionality
            const searchInput = document.getElementById('politicsSearchInput');
            const clearBtn = document.getElementById('clearPoliticsSearchBtn');

            if (searchInput && clearBtn) {
                searchInput.addEventListener('input', function() {
                    if (this.value.length > 0) {
                        clearBtn.style.display = 'block';
                    } else {
                        clearBtn.style.display = 'none';
                    }
                });

                clearBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    this.style.display = 'none';
                    // Trigger search update if needed
                    if (typeof Livewire !== 'undefined') {
                        Livewire.dispatch('search-query-updated', { query: '' });
                    }
                });
            }
        });
    </script>
@endsection


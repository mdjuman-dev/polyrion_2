@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>{{ ucfirst($category) }} Events - {{ $appName }}</title>
    <meta name="description" content="Explore {{ strtolower($category) }} prediction markets and events on {{ $appName }}.">
    <meta property="og:title" content="{{ ucfirst($category) }} Events - {{ $appName }}">
    <link rel="canonical" href="{{ $appUrl }}/category/{{ $category }}">
@endsection
@section('content')
    <!-- Main Content -->
    <main style="padding: 20px 0;">
        <div class="container">
            <!-- Sub-Category Filters Bar (Same as Politics Page) -->
            <div class="secondary-filters">
                <div class="filter-top-bar d-lg-flex d-block">
                    <div class="row align-items-center">
                        <div class="secondary-search-bar">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" placeholder="Search" class="secondary-search-input" id="categorySearchInput"
                                style="width: 100%;">
                            <button type="button" id="clearCategorySearchBtn"
                                style="display: none; position: absolute; right: 20px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 5px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button class="filter-icon-btn mx-2" id="categoryFilterToggleBtn"><i class="fas fa-sliders-h"></i></button>
                        <a href="{{ route('saved.events') }}" class="bookmark-icon-btn" title="Saved Events">
                            <i class="fas fa-bookmark"></i>
                        </a>
                    </div>
                    <!-- Category Filters - Same Line -->
                    <div class="filters-section-wrapper ms-lg-4">
                        <button class="filter-scroll-btn filter-scroll-left" id="categoryFilterScrollLeft" aria-label="Scroll left">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <div class="filters-section" id="categoryFiltersSection" style="display: flex; gap: 8px; align-items: center; overflow-x: auto; overflow-y: hidden; scroll-behavior: smooth; -webkit-overflow-scrolling: touch; padding: 4px 0;">
                            <!-- All Button -->
                            <a href="{{ route('events.by.category', $category) }}?subcategory=all" 
                               class="filter-btn {{ $selectedSubCategory === 'all' ? 'active' : '' }}">
                                All
                            </a>

                            <!-- Popular Sub-Categories -->
                            @foreach($popularSubCategories as $subCategory)
                                <a href="{{ route('events.by.category', $category) }}?subcategory={{ $subCategory['slug'] }}" 
                                   class="filter-btn {{ $selectedSubCategory === $subCategory['slug'] ? 'active' : '' }}">
                                    {{ $subCategory['name'] }}
                                </a>
                            @endforeach
                        </div>
                        <button class="filter-scroll-btn filter-scroll-right" id="categoryFilterScrollRight" aria-label="Scroll right">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Events Grid -->
            <div class="row">
                <div class="col-12">
                    <livewire:category-events-grid :category="$category" :subcategory="$selectedSubCategory" wire:key="category-grid-{{ $category }}-{{ $selectedSubCategory }}" />
                </div>
            </div>
        </div>
    </main>

    <style>
        /* Events Grid */
        .category-events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        @media (max-width: 768px) {
            .category-events-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter Scroll Functionality
            const filtersSection = document.getElementById('categoryFiltersSection');
            const scrollLeftBtn = document.getElementById('categoryFilterScrollLeft');
            const scrollRightBtn = document.getElementById('categoryFilterScrollRight');

            if (filtersSection && scrollLeftBtn && scrollRightBtn) {
                function updateScrollButtons() {
                    const isScrollable = filtersSection.scrollWidth > filtersSection.clientWidth;
                    const isAtStart = filtersSection.scrollLeft <= 0;
                    const isAtEnd = filtersSection.scrollLeft >= filtersSection.scrollWidth - filtersSection.clientWidth - 1;

                    scrollLeftBtn.style.display = isScrollable && !isAtStart ? 'flex' : 'none';
                    scrollRightBtn.style.display = isScrollable && !isAtEnd ? 'flex' : 'none';
                }

                scrollLeftBtn.addEventListener('click', function() {
                    filtersSection.scrollBy({ left: -200, behavior: 'smooth' });
                    setTimeout(updateScrollButtons, 300);
                });

                scrollRightBtn.addEventListener('click', function() {
                    filtersSection.scrollBy({ left: 200, behavior: 'smooth' });
                    setTimeout(updateScrollButtons, 300);
                });

                filtersSection.addEventListener('scroll', updateScrollButtons);
                updateScrollButtons();
                window.addEventListener('resize', updateScrollButtons);
            }

            // Search functionality - Connect to Livewire component
            const searchInput = document.getElementById('categorySearchInput');
            const clearBtn = document.getElementById('clearCategorySearchBtn');
            
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    const value = this.value;
                    if (value.length > 0) {
                        clearBtn.style.display = 'block';
                    } else {
                        clearBtn.style.display = 'none';
                    }
                    
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        // Find Livewire component and update search
                        const component = document.querySelector('[data-component="category-events-grid"]');
                        if (component && component.__livewire) {
                            component.__livewire.$wire.set('search', value);
                        } else if (window.Livewire) {
                            // Fallback for Livewire v3
                            window.Livewire.find('category-events-grid')?.set('search', value);
                        }
                    }, 300);
                });
            }
            
            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    this.style.display = 'none';
                    // Clear search in Livewire component
                    const component = document.querySelector('[data-component="category-events-grid"]');
                    if (component && component.__livewire) {
                        component.__livewire.$wire.set('search', '');
                    } else if (window.Livewire) {
                        window.Livewire.find('category-events-grid')?.set('search', '');
                    }
                });
            }
        });
    </script>
@endsection


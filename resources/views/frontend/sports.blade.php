@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Sports Events - {{ $appName }}</title>
    <meta name="description" content="Explore sports prediction markets and events on {{ $appName }}. Bet on Football, Cricket, Basketball, and more.">
    <meta property="og:title" content="Sports Events - {{ $appName }}">
    <link rel="canonical" href="{{ $appUrl }}/sports">
@endsection

@section('content')
    <!-- Main Content -->
    <main style="padding: 20px 0;">
        <div class="container">
            <!-- Mobile: Categories Scrollable (Top) -->
            <div class="d-lg-none mb-4">
                <div class="sports-categories-scroll" style="overflow-x: auto; overflow-y: hidden; -webkit-overflow-scrolling: touch; padding: 10px 0;">
                    <div class="d-flex gap-2" style="min-width: max-content;">
                        <a href="{{ route('sports.index') }}?category=all" 
                           class="sports-category-btn {{ $selectedCategory === 'all' ? 'active' : '' }}"
                           style="white-space: nowrap; padding: 10px 20px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); text-decoration: none; font-weight: 500; transition: all 0.3s ease; display: inline-block;">
                            <i class="fas fa-th"></i> All
                        </a>
                        @foreach($popularCategories as $category)
                            <a href="{{ route('sports.index') }}?category={{ strtolower($category['name']) }}" 
                               class="sports-category-btn {{ strtolower($selectedCategory) === strtolower($category['name']) ? 'active' : '' }}"
                               style="white-space: nowrap; padding: 10px 20px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); text-decoration: none; font-weight: 500; transition: all 0.3s ease; display: inline-block;">
                                {{ $category['name'] }} ({{ $category['count'] }})
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Desktop & Mobile: Main Content -->
            <div class="row">
                <!-- Desktop: Categories Sidebar (3 columns) - Image Design -->
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="sports-sidebar" style="background: #1a1d29; border-radius: 8px; padding: 0; position: sticky; top: 100px; max-height: calc(100vh - 120px); overflow-y: auto;">
                        <!-- POPULAR Section -->
                        @if(count($popularCategories) > 0)
                            <div style="padding: 20px 16px 12px 16px;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                                    <i class="fas fa-fire" style="color: #ff6b6b; font-size: 14px;"></i>
                                    <h3 style="color: #fff; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;">POPULAR</h3>
                                </div>
                                <div class="sports-categories-list">
                                    @foreach($popularCategories as $category)
                                        <a href="{{ route('sports.index') }}?category={{ strtolower($category['name']) }}" 
                                           class="sports-category-link {{ strtolower($selectedCategory) === strtolower($category['name']) ? 'active' : '' }}"
                                           data-category="{{ strtolower($category['name']) }}">
                                            <i class="fas {{ $category['icon'] }}" style="color: {{ getCategoryColor($category['name']) }}; font-size: 18px; width: 24px; text-align: center;"></i>
                                            <span style="flex: 1; color: #fff; font-size: 14px;">{{ $category['name'] }}</span>
                                            <span style="color: #9ca3af; font-size: 13px; margin-right: 8px;">{{ $category['count'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            <div style="height: 1px; background: #2d3142; margin: 0 16px;"></div>
                        @endif

                        <!-- ALL SPORTS Section -->
                        <div style="padding: 12px 16px 20px 16px;">
                            <h3 style="color: #fff; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px; padding-left: 0;">ALL SPORTS</h3>
                            <div class="sports-categories-list">
                                @foreach($allCategories as $category)
                                    <div class="category-item-wrapper">
                                        <a href="{{ route('sports.index') }}?category={{ strtolower($category['name']) }}" 
                                           class="sports-category-link {{ strtolower($selectedCategory) === strtolower($category['name']) ? 'active' : '' }}"
                                           data-category="{{ strtolower($category['name']) }}">
                                            <i class="fas {{ $category['icon'] }}" style="color: #9ca3af; font-size: 18px; width: 24px; text-align: center;"></i>
                                            <span style="flex: 1; color: #fff; font-size: 14px;">{{ $category['name'] }}</span>
                                           
                                        </a>
                                        
                                        <!-- Subcategories (e.g., Countries for Cricket) -->
                                        @if(strtolower($selectedCategory) === strtolower($category['name']) && count($subcategories) > 0)
                                            <div class="subcategories-list" style="margin-top: 4px; padding-left: 32px;">
                                                @foreach($subcategories as $subcategory)
                                                    <a href="{{ route('sports.index') }}?category={{ strtolower($category['name']) }}&subcategory={{ strtolower($subcategory['name']) }}" 
                                                       class="subcategory-link {{ strtolower($selectedSubcategory ?? '') === strtolower($subcategory['name']) ? 'active' : '' }}"
                                                       style="display: block; padding: 8px 12px; color: {{ strtolower($selectedSubcategory ?? '') === strtolower($subcategory['name']) ? '#fff' : '#9ca3af' }}; text-decoration: none; font-size: 13px; border-radius: 4px; background: {{ strtolower($selectedSubcategory ?? '') === strtolower($subcategory['name']) ? '#2d3142' : 'transparent' }}; transition: all 0.2s;">
                                                        {{ $subcategory['name'] }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desktop: Events Grid (9 columns) | Mobile: Full Width -->
                <div class="col-lg-9">
                    <livewire:sports-events-grid :category="$selectedCategory" :subcategory="$selectedSubcategory" />
                </div>
            </div>
        </div>
    </main>

    <style>
        /* Sports Sidebar Styles */
        .sports-sidebar {
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }

        .sports-sidebar::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        .sports-categories-list {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .sports-category-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            border-radius: 6px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .sports-category-link:hover {
            background: #2d3142;
        }

        .sports-category-link.active {
            background: #2d3142;
        }

        .category-item-wrapper {
            position: relative;
        }

        .subcategory-link:hover {
            background: #2d3142 !important;
            color: #fff !important;
        }

        /* Mobile Categories Scrollbar */
        .sports-categories-scroll::-webkit-scrollbar {
            height: 6px;
        }

        .sports-categories-scroll::-webkit-scrollbar-track {
            background: var(--secondary);
            border-radius: 10px;
        }

        .sports-categories-scroll::-webkit-scrollbar-thumb {
            background: var(--accent);
            border-radius: 10px;
        }

        .sports-category-btn:hover {
            background: var(--accent) !important;
            color: #fff !important;
            border-color: var(--accent) !important;
            transform: translateY(-2px);
        }

        .sports-category-btn.active {
            background: var(--accent) !important;
            color: #fff !important;
            border-color: var(--accent) !important;
        }

    </style>

    <script>
        // Toggle subcategories on click
        document.addEventListener('DOMContentLoaded', function() {
            const categoryLinks = document.querySelectorAll('.sports-category-link[data-category]');
            categoryLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const category = this.getAttribute('data-category');
                    const wrapper = this.closest('.category-item-wrapper');
                    const subcategories = wrapper?.querySelector('.subcategories-list');
                    
                    if (subcategories) {
                        // If clicking the same category, toggle subcategories
                        if (this.classList.contains('active')) {
                            e.preventDefault();
                            subcategories.style.display = subcategories.style.display === 'none' ? 'block' : 'none';
                        }
                    }
                });
            });
        });
    </script>
@endsection

@php
    function getCategoryColor($categoryName) {
        $colors = [
            'NFL' => '#4a9eff',
            'NBA' => '#ff8c42',
            'NCAA CBB' => '#9ca3af',
            'NHL' => '#4ade80',
            'UFC' => '#ef4444',
            'Football' => '#fff',
            'Esports' => '#fbbf24',
            'Cricket' => '#ef4444',
            'Tennis' => '#fbbf24',
            'Hockey' => '#fff',
            'Rugby' => '#fff',
            'Basketball' => '#fff',
        ];
        return $colors[$categoryName] ?? '#9ca3af';
    }
@endphp

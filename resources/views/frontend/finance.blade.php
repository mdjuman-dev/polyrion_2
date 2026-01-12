@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Finance Markets - {{ $appName }}</title>
    <meta name="description" content="Explore finance and economy prediction markets on {{ $appName }}. Bet on stocks, earnings, indices, and more.">
    <meta property="og:title" content="Finance Markets - {{ $appName }}">
    <link rel="canonical" href="{{ $appUrl }}/finance">
@endsection

@section('content')
    <!-- Main Content -->
    <main style="padding: 20px 0;">
        <div class="container">
            <!-- Mobile: Filters Scrollable (Top) -->
            <div class="d-lg-none mb-4">
                <div class="finance-filters-mobile" style="overflow-x: auto; overflow-y: hidden; -webkit-overflow-scrolling: touch;">
                    <div class="d-flex gap-2" style="min-width: max-content;">
                        <!-- Time Filters -->
                        <a href="{{ route('finance.index') }}?timeframe=all{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}" 
                           class="filter-btn-mobile {{ $selectedTimeframe === 'all' ? 'active' : '' }}"
                           style="white-space: nowrap; padding: 8px 16px; background: {{ $selectedTimeframe === 'all' ? 'var(--accent)' : 'var(--card-bg)' }}; border: 1px solid {{ $selectedTimeframe === 'all' ? 'var(--accent)' : 'var(--border)' }}; border-radius: 8px; color: {{ $selectedTimeframe === 'all' ? '#fff' : 'var(--text-primary)' }}; text-decoration: none; font-weight: 500; font-size: 13px; display: inline-block;">
                            All ({{ $timeframeCounts['all'] }})
                        </a>
                        <a href="{{ route('finance.index') }}?timeframe=daily{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}" 
                           class="filter-btn-mobile {{ $selectedTimeframe === 'daily' ? 'active' : '' }}"
                           style="white-space: nowrap; padding: 8px 16px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); text-decoration: none; font-weight: 500; font-size: 13px; display: inline-block;">
                            Daily ({{ $timeframeCounts['daily'] }})
                        </a>
                        <a href="{{ route('finance.index') }}?timeframe=weekly{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}" 
                           class="filter-btn-mobile {{ $selectedTimeframe === 'weekly' ? 'active' : '' }}"
                           style="white-space: nowrap; padding: 8px 16px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); text-decoration: none; font-weight: 500; font-size: 13px; display: inline-block;">
                            Weekly ({{ $timeframeCounts['weekly'] }})
                        </a>
                        <a href="{{ route('finance.index') }}?timeframe=monthly{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}" 
                           class="filter-btn-mobile {{ $selectedTimeframe === 'monthly' ? 'active' : '' }}"
                           style="white-space: nowrap; padding: 8px 16px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); text-decoration: none; font-weight: 500; font-size: 13px; display: inline-block;">
                            Monthly ({{ $timeframeCounts['monthly'] }})
                        </a>
                        
                        <!-- Divider -->
                        <div style="width: 1px; height: 30px; background: var(--border); margin: 0 8px; flex-shrink: 0;"></div>
                        
                        <!-- Category Filters -->
                        @foreach($categoryCounts as $category)
                            <a href="{{ route('finance.index') }}?category={{ $category['slug'] }}{{ $selectedTimeframe !== 'all' ? '&timeframe=' . $selectedTimeframe : '' }}" 
                               class="filter-btn-mobile {{ $selectedCategory === $category['slug'] ? 'active' : '' }}"
                               style="white-space: nowrap; padding: 8px 16px; background: {{ $selectedCategory === $category['slug'] ? 'var(--accent)' : 'var(--card-bg)' }}; border: 1px solid {{ $selectedCategory === $category['slug'] ? 'var(--accent)' : 'var(--border)' }}; border-radius: 8px; color: {{ $selectedCategory === $category['slug'] ? '#fff' : 'var(--text-primary)' }}; text-decoration: none; font-weight: 500; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="{{ $category['icon'] }}" style="font-size: 14px;"></i>
                                <span>{{ $category['name'] }} ({{ $category['count'] }})</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Main Content Row -->
            <div class="row">
                <!-- Left Sidebar - Filters (3 columns) -->
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="finance-sidebar" style="background: #1a1d29; border-radius: 8px; padding: 0; position: sticky; top: 100px; max-height: calc(100vh - 120px); overflow-y: auto;">
                        <!-- Time-based Filters -->
                        <div style="padding: 20px 16px 12px 16px;">
                            <h3 style="color: #fff; font-size: 12px; font-weight: 600; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                                Time Filters
                            </h3>
                            <div class="filter-list" style="display: flex; flex-direction: column; gap: 2px;">
                                <a href="{{ route('finance.index') }}?timeframe=all{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}" 
                                   class="filter-item {{ $selectedTimeframe === 'all' ? 'active' : '' }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; color: #fff; text-decoration: none; font-size: 14px; border-radius: 6px; transition: all 0.2s ease;">
                                    <span>All</span>
                                    <span style="color: #9ca3af; font-size: 13px; margin-right: 8px;">{{ $timeframeCounts['all'] }}</span>
                                </a>
                                <a href="{{ route('finance.index') }}?timeframe=daily{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}" 
                                   class="filter-item {{ $selectedTimeframe === 'daily' ? 'active' : '' }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; color: #fff; text-decoration: none; font-size: 14px; border-radius: 6px; transition: all 0.2s ease;">
                                    <span>Daily</span>
                                    <span style="color: #9ca3af; font-size: 13px; margin-right: 8px;">{{ $timeframeCounts['daily'] }}</span>
                                </a>
                                <a href="{{ route('finance.index') }}?timeframe=weekly{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}" 
                                   class="filter-item {{ $selectedTimeframe === 'weekly' ? 'active' : '' }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; color: #fff; text-decoration: none; font-size: 14px; border-radius: 6px; transition: all 0.2s ease;">
                                    <span>Weekly</span>
                                    <span style="color: #9ca3af; font-size: 13px; margin-right: 8px;">{{ $timeframeCounts['weekly'] }}</span>
                                </a>
                                <a href="{{ route('finance.index') }}?timeframe=monthly{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}" 
                                   class="filter-item {{ $selectedTimeframe === 'monthly' ? 'active' : '' }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; color: #fff; text-decoration: none; font-size: 14px; border-radius: 6px; transition: all 0.2s ease;">
                                    <span>Monthly</span>
                                    <span style="color: #9ca3af; font-size: 13px; margin-right: 8px;">{{ $timeframeCounts['monthly'] }}</span>
                                </a>
                            </div>
                        </div>
                        <div style="height: 1px; background: #2d3142; margin: 0 16px;"></div>
                        <!-- Category Filters -->
                        <div style="padding: 12px 16px 20px 16px;">
                            <h3 style="color: #fff; font-size: 12px; font-weight: 600; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                                Categories
                            </h3>
                            <div class="filter-list" style="display: flex; flex-direction: column; gap: 2px;">
                                @foreach($categoryCounts as $category)
                                    <a href="{{ route('finance.index') }}?category={{ $category['slug'] }}{{ $selectedTimeframe !== 'all' ? '&timeframe=' . $selectedTimeframe : '' }}" 
                                       class="filter-item {{ $selectedCategory === $category['slug'] ? 'active' : '' }}"
                                       style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; color: #fff; text-decoration: none; font-size: 14px; border-radius: 6px; transition: all 0.2s ease;">
                                        <i class="{{ $category['icon'] }}" style="color: #9ca3af; font-size: 18px; width: 24px; text-align: center;"></i>
                                        <span style="flex: 1;">{{ $category['name'] }}</span>
                                        <span style="color: #9ca3af; font-size: 13px; margin-right: 8px;">{{ $category['count'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content - Events Grid (9 columns) -->
                <div class="col-lg-9">
                    <livewire:finance-events-grid :timeframe="$selectedTimeframe" :category="$selectedCategory" />
                </div>
            </div>
        </div>
    </main>

    <style>
        /* Finance Sidebar Styles */
        .finance-sidebar {
            scrollbar-width: none !important; 
            -ms-overflow-style: none !important; 
        }

        .finance-sidebar::-webkit-scrollbar {
            display: none !important ;
        }

        .filter-item:hover {
            background: #2d3142 !important;
        }

        .filter-item.active {
            background: #2d3142 !important;
        }

        @media (max-width: 991px) {
            .finance-sidebar {
                margin-bottom: 24px;
                position: relative;
                top: 0;
                max-height: none;
            }
        }

        /* Mobile Filters Scrollbar */
        .finance-filters-mobile {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .finance-filters-mobile::-webkit-scrollbar {
            height: 6px;
        }

        .finance-filters-mobile::-webkit-scrollbar-track {
            background: var(--secondary);
            border-radius: 10px;
        }

        .finance-filters-mobile::-webkit-scrollbar-thumb {
            background: var(--accent);
            border-radius: 10px;
        }

        .filter-btn-mobile:hover {
            background: var(--hover) !important;
            border-color: var(--accent) !important;
        }

        .filter-btn-mobile.active {
            background: var(--accent) !important;
            color: #fff !important;
            border-color: var(--accent) !important;
        }
    </style>
@endsection


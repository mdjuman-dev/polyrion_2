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
                <div class="finance-filters-mobile" style="overflow-x: auto; overflow-y: hidden; -webkit-overflow-scrolling: touch; padding: 10px 0;">
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
                    <div class="finance-sidebar" style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 20px; position: sticky; top: 100px; max-height: calc(100vh - 120px); overflow-y: auto;">
                        <!-- Time-based Filters -->
                        <div style="margin-bottom: 32px;">
                            <h3 style="color: var(--text-primary); font-size: 14px; font-weight: 600; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                                Time Filters
                            </h3>
                            <div class="filter-list" style="display: flex; flex-direction: column; gap: 8px;">
                                <a href="{{ route('finance.index') }}?timeframe=all{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}" 
                                   class="filter-item {{ $selectedTimeframe === 'all' ? 'active' : '' }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: {{ $selectedTimeframe === 'all' ? 'var(--accent)' : 'transparent' }}; border: 1px solid {{ $selectedTimeframe === 'all' ? 'var(--accent)' : 'var(--border)' }}; border-radius: 8px; color: {{ $selectedTimeframe === 'all' ? '#fff' : 'var(--text-primary)' }}; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s;">
                                    <span>All</span>
                                    <span style="color: {{ $selectedTimeframe === 'all' ? 'rgba(255,255,255,0.8)' : 'var(--text-secondary)' }}; font-size: 13px;">{{ $timeframeCounts['all'] }}</span>
                                </a>
                                <a href="{{ route('finance.index') }}?timeframe=daily{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}" 
                                   class="filter-item {{ $selectedTimeframe === 'daily' ? 'active' : '' }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: transparent; border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s;">
                                    <span>Daily</span>
                                    <span style="color: var(--text-secondary); font-size: 13px;">{{ $timeframeCounts['daily'] }}</span>
                                </a>
                                <a href="{{ route('finance.index') }}?timeframe=weekly{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}" 
                                   class="filter-item {{ $selectedTimeframe === 'weekly' ? 'active' : '' }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: transparent; border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s;">
                                    <span>Weekly</span>
                                    <span style="color: var(--text-secondary); font-size: 13px;">{{ $timeframeCounts['weekly'] }}</span>
                                </a>
                                <a href="{{ route('finance.index') }}?timeframe=monthly{{ $selectedCategory !== 'all' ? '&category=' . $selectedCategory : '' }}" 
                                   class="filter-item {{ $selectedTimeframe === 'monthly' ? 'active' : '' }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: transparent; border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s;">
                                    <span>Monthly</span>
                                    <span style="color: var(--text-secondary); font-size: 13px;">{{ $timeframeCounts['monthly'] }}</span>
                                </a>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div style="height: 1px; background: var(--border); margin: 24px 0;"></div>

                        <!-- Category Filters -->
                        <div>
                            <h3 style="color: var(--text-primary); font-size: 14px; font-weight: 600; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                                Categories
                            </h3>
                            <div class="filter-list" style="display: flex; flex-direction: column; gap: 8px;">
                                @foreach($categoryCounts as $category)
                                    <a href="{{ route('finance.index') }}?category={{ $category['slug'] }}{{ $selectedTimeframe !== 'all' ? '&timeframe=' . $selectedTimeframe : '' }}" 
                                       class="filter-item {{ $selectedCategory === $category['slug'] ? 'active' : '' }}"
                                       style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: {{ $selectedCategory === $category['slug'] ? 'var(--accent)' : 'transparent' }}; border: 1px solid {{ $selectedCategory === $category['slug'] ? 'var(--accent)' : 'var(--border)' }}; border-radius: 8px; color: {{ $selectedCategory === $category['slug'] ? '#fff' : 'var(--text-primary)' }}; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <i class="{{ $category['icon'] }}" style="font-size: 16px; width: 20px; text-align: center;"></i>
                                            <span>{{ $category['name'] }}</span>
                                        </div>
                                        <span style="color: {{ $selectedCategory === $category['slug'] ? 'rgba(255,255,255,0.8)' : 'var(--text-secondary)' }}; font-size: 13px;">{{ $category['count'] }}</span>
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
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }

        .finance-sidebar::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        .filter-item:hover {
            background: var(--hover) !important;
            border-color: var(--accent) !important;
        }

        .filter-item.active {
            background: var(--accent) !important;
            color: #fff !important;
            border-color: var(--accent) !important;
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
            scrollbar-width: thin;
            scrollbar-color: var(--accent) var(--secondary);
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


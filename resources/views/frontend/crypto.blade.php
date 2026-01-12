@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Crypto Markets - {{ $appName }}</title>
    <meta name="description" content="Explore cryptocurrency prediction markets on {{ $appName }}. Bet on Bitcoin, Ethereum, Solana, and more.">
    <meta property="og:title" content="Crypto Markets - {{ $appName }}">
    <link rel="canonical" href="{{ $appUrl }}/crypto">
@endsection

@section('content')
    <!-- Main Content -->
    <main style="padding: 20px 0;">
        <div class="container">

            <!-- Mobile: Filters Scrollable (Top) -->
            <div class="d-lg-none mb-4">
                <div class="crypto-filters-mobile" style="overflow-x: auto; overflow-y: hidden; -webkit-overflow-scrolling: touch; padding: 10px 0;">
                    <div class="d-flex gap-2" style="min-width: max-content;">
                        <!-- Time Filters -->
                        <a href="{{ route('crypto.index') }}?timeframe=all{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                           class="filter-btn-mobile {{ $selectedTimeframe === 'all' ? 'active' : '' }}"
                           style="white-space: nowrap; padding: 8px 16px; background: {{ $selectedTimeframe === 'all' ? 'var(--accent)' : 'var(--card-bg)' }}; border: 1px solid {{ $selectedTimeframe === 'all' ? 'var(--accent)' : 'var(--border)' }}; border-radius: 8px; color: {{ $selectedTimeframe === 'all' ? '#fff' : 'var(--text-primary)' }}; text-decoration: none; font-weight: 500; font-size: 13px; display: inline-block;">
                            All ({{ $timeframeCounts['all'] }})
                        </a>
                        <a href="{{ route('crypto.index') }}?timeframe=15m{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                           class="filter-btn-mobile {{ $selectedTimeframe === '15m' ? 'active' : '' }}"
                           style="white-space: nowrap; padding: 8px 16px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); text-decoration: none; font-weight: 500; font-size: 13px; display: inline-block;">
                            15M ({{ $timeframeCounts['15m'] }})
                        </a>
                        <a href="{{ route('crypto.index') }}?timeframe=hourly{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                           class="filter-btn-mobile {{ $selectedTimeframe === 'hourly' ? 'active' : '' }}"
                           style="white-space: nowrap; padding: 8px 16px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); text-decoration: none; font-weight: 500; font-size: 13px; display: inline-block;">
                            Hourly ({{ $timeframeCounts['hourly'] }})
                        </a>
                        <a href="{{ route('crypto.index') }}?timeframe=4h{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                           class="filter-btn-mobile {{ $selectedTimeframe === '4h' ? 'active' : '' }}"
                           style="white-space: nowrap; padding: 8px 16px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); text-decoration: none; font-weight: 500; font-size: 13px; display: inline-block;">
                            4H ({{ $timeframeCounts['4h'] }})
                        </a>
                        <a href="{{ route('crypto.index') }}?timeframe=daily{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                           class="filter-btn-mobile {{ $selectedTimeframe === 'daily' ? 'active' : '' }}"
                           style="white-space: nowrap; padding: 8px 16px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); text-decoration: none; font-weight: 500; font-size: 13px; display: inline-block;">
                            Daily ({{ $timeframeCounts['daily'] }})
                        </a>
                        <a href="{{ route('crypto.index') }}?timeframe=weekly{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                           class="filter-btn-mobile {{ $selectedTimeframe === 'weekly' ? 'active' : '' }}"
                           style="white-space: nowrap; padding: 8px 16px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); text-decoration: none; font-weight: 500; font-size: 13px; display: inline-block;">
                            Weekly ({{ $timeframeCounts['weekly'] }})
                        </a>
                        <a href="{{ route('crypto.index') }}?timeframe=monthly{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                           class="filter-btn-mobile {{ $selectedTimeframe === 'monthly' ? 'active' : '' }}"
                           style="white-space: nowrap; padding: 8px 16px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); text-decoration: none; font-weight: 500; font-size: 13px; display: inline-block;">
                            Monthly ({{ $timeframeCounts['monthly'] }})
                        </a>
                        <a href="{{ route('crypto.index') }}?timeframe=pre-market{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                           class="filter-btn-mobile {{ $selectedTimeframe === 'pre-market' ? 'active' : '' }}"
                           style="white-space: nowrap; padding: 8px 16px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); text-decoration: none; font-weight: 500; font-size: 13px; display: inline-block;">
                            Pre-Market ({{ $timeframeCounts['pre-market'] }})
                        </a>
                        <a href="{{ route('crypto.index') }}?timeframe=etf{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                           class="filter-btn-mobile {{ $selectedTimeframe === 'etf' ? 'active' : '' }}"
                           style="white-space: nowrap; padding: 8px 16px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); text-decoration: none; font-weight: 500; font-size: 13px; display: inline-block;">
                            ETF ({{ $timeframeCounts['etf'] }})
                        </a>
                        
                        <!-- Divider -->
                        <div style="width: 1px; height: 30px; background: var(--border); margin: 0 8px; flex-shrink: 0;"></div>
                        
                        <!-- Asset Filters -->
                        @foreach($assetCounts as $asset)
                            <a href="{{ route('crypto.index') }}?asset={{ $asset['slug'] }}{{ $selectedTimeframe !== 'all' ? '&timeframe=' . $selectedTimeframe : '' }}" 
                               class="filter-btn-mobile {{ $selectedAsset === $asset['slug'] ? 'active' : '' }}"
                               style="white-space: nowrap; padding: 8px 16px; background: {{ $selectedAsset === $asset['slug'] ? 'var(--accent)' : 'var(--card-bg)' }}; border: 1px solid {{ $selectedAsset === $asset['slug'] ? 'var(--accent)' : 'var(--border)' }}; border-radius: 8px; color: {{ $selectedAsset === $asset['slug'] ? '#fff' : 'var(--text-primary)' }}; text-decoration: none; font-weight: 500; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="{{ $asset['icon'] }}" style="font-size: 14px;"></i>
                                <span>{{ $asset['name'] }} ({{ $asset['count'] }})</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Main Content Row -->
            <div class="row">
                <!-- Left Sidebar - Filters (3 columns) -->
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="crypto-sidebar" style="background: #1a1d29; border-radius: 8px; padding: 0; position: sticky; top: 100px; max-height: calc(100vh - 120px); overflow-y: auto;">
                        <!-- Time-based Filters -->
                        <div style="padding: 20px 16px 12px 16px;">
                            <h3 style="color: #fff; font-size: 12px; font-weight: 600; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                                Time Filters
                            </h3>
                            <div class="filter-list" style="display: flex; flex-direction: column; gap: 2px;">
                                <a href="{{ route('crypto.index') }}?timeframe=all{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                                   class="filter-item {{ $selectedTimeframe === 'all' ? 'active' : '' }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; color: #fff; text-decoration: none; font-size: 14px; border-radius: 6px; transition: all 0.2s ease;">
                                    <span>All</span>
                                    <span style="color: #9ca3af; font-size: 13px; margin-right: 8px;">{{ $timeframeCounts['all'] }}</span>
                                </a>
                                <a href="{{ route('crypto.index') }}?timeframe=15m{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                                   class="filter-item {{ $selectedTimeframe === '15m' ? 'active' : '' }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; color: #fff; text-decoration: none; font-size: 14px; border-radius: 6px; transition: all 0.2s ease;">
                                    <span>15 Min</span>
                                    <span style="color: #9ca3af; font-size: 13px; margin-right: 8px;">{{ $timeframeCounts['15m'] }}</span>
                                </a>
                                <a href="{{ route('crypto.index') }}?timeframe=hourly{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                                   class="filter-item {{ $selectedTimeframe === 'hourly' ? 'active' : '' }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; color: #fff; text-decoration: none; font-size: 14px; border-radius: 6px; transition: all 0.2s ease;">
                                    <span>Hourly</span>
                                    <span style="color: #9ca3af; font-size: 13px; margin-right: 8px;">{{ $timeframeCounts['hourly'] }}</span>
                                </a>
                                <a href="{{ route('crypto.index') }}?timeframe=4h{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                                   class="filter-item {{ $selectedTimeframe === '4h' ? 'active' : '' }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; color: #fff; text-decoration: none; font-size: 14px; border-radius: 6px; transition: all 0.2s ease;">
                                    <span>4 Hour</span>
                                    <span style="color: #9ca3af; font-size: 13px; margin-right: 8px;">{{ $timeframeCounts['4h'] }}</span>
                                </a>
                                <a href="{{ route('crypto.index') }}?timeframe=daily{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                                   class="filter-item {{ $selectedTimeframe === 'daily' ? 'active' : '' }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; color: #fff; text-decoration: none; font-size: 14px; border-radius: 6px; transition: all 0.2s ease;">
                                    <span>Daily</span>
                                    <span style="color: #9ca3af; font-size: 13px; margin-right: 8px;">{{ $timeframeCounts['daily'] }}</span>
                                </a>
                                <a href="{{ route('crypto.index') }}?timeframe=weekly{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                                   class="filter-item {{ $selectedTimeframe === 'weekly' ? 'active' : '' }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; color: #fff; text-decoration: none; font-size: 14px; border-radius: 6px; transition: all 0.2s ease;">
                                    <span>Weekly</span>
                                    <span style="color: #9ca3af; font-size: 13px; margin-right: 8px;">{{ $timeframeCounts['weekly'] }}</span>
                                </a>
                                <a href="{{ route('crypto.index') }}?timeframe=monthly{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                                   class="filter-item {{ $selectedTimeframe === 'monthly' ? 'active' : '' }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; color: #fff; text-decoration: none; font-size: 14px; border-radius: 6px; transition: all 0.2s ease;">
                                    <span>Monthly</span>
                                    <span style="color: #9ca3af; font-size: 13px; margin-right: 8px;">{{ $timeframeCounts['monthly'] }}</span>
                                </a>
                                <a href="{{ route('crypto.index') }}?timeframe=pre-market{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                                   class="filter-item {{ $selectedTimeframe === 'pre-market' ? 'active' : '' }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; color: #fff; text-decoration: none; font-size: 14px; border-radius: 6px; transition: all 0.2s ease;">
                                    <span>Pre-Market</span>
                                    <span style="color: #9ca3af; font-size: 13px; margin-right: 8px;">{{ $timeframeCounts['pre-market'] }}</span>
                                </a>
                                <a href="{{ route('crypto.index') }}?timeframe=etf{{ $selectedAsset !== 'all' ? '&asset=' . $selectedAsset : '' }}" 
                                   class="filter-item {{ $selectedTimeframe === 'etf' ? 'active' : '' }}"
                                   style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; color: #fff; text-decoration: none; font-size: 14px; border-radius: 6px; transition: all 0.2s ease;">
                                    <span>ETF</span>
                                    <span style="color: #9ca3af; font-size: 13px; margin-right: 8px;">{{ $timeframeCounts['etf'] }}</span>
                                </a>
                            </div>
                        </div>
                        <div style="height: 1px; background: #2d3142; margin: 0 16px;"></div>
                        <!-- Asset-based Filters -->
                        <div style="padding: 12px 16px 20px 16px;">
                            <h3 style="color: #fff; font-size: 12px; font-weight: 600; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                                Assets
                            </h3>
                            <div class="filter-list" style="display: flex; flex-direction: column; gap: 2px;">
                                @foreach($assetCounts as $asset)
                                    <a href="{{ route('crypto.index') }}?asset={{ $asset['slug'] }}{{ $selectedTimeframe !== 'all' ? '&timeframe=' . $selectedTimeframe : '' }}" 
                                       class="filter-item {{ $selectedAsset === $asset['slug'] ? 'active' : '' }}"
                                       style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; color: #fff; text-decoration: none; font-size: 14px; border-radius: 6px; transition: all 0.2s ease;">
                                        <i class="{{ $asset['icon'] }}" style="color: #9ca3af; font-size: 18px; width: 24px; text-align: center;"></i>
                                        <span style="flex: 1;">{{ $asset['name'] }}</span>
                                        <span style="color: #9ca3af; font-size: 13px; margin-right: 8px;">{{ $asset['count'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content - Events Grid (9 columns) -->
                <div class="col-lg-9">
                    <livewire:crypto-events-grid :timeframe="$selectedTimeframe" :asset="$selectedAsset" />
                </div>
            </div>
        </div>
    </main>

    <style>
        /* Crypto Sidebar Styles */
        .crypto-sidebar {
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }

        .crypto-sidebar::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        .filter-item:hover {
            background: #2d3142 !important;
        }

        .filter-item.active {
            background: #2d3142 !important;
        }

        @media (max-width: 991px) {
            .crypto-sidebar {
                margin-bottom: 24px;
                position: relative;
                top: 0;
                max-height: none;
            }
        }

        /* Mobile Filters Scrollbar */
        .crypto-filters-mobile {
            scrollbar-width: thin;
            scrollbar-color: var(--accent) var(--secondary);
        }

        .crypto-filters-mobile::-webkit-scrollbar {
            height: 6px;
        }

        .crypto-filters-mobile::-webkit-scrollbar-track {
            background: var(--secondary);
            border-radius: 10px;
        }

        .crypto-filters-mobile::-webkit-scrollbar-thumb {
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


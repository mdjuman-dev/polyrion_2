@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Polymarket</title>
@endsection
@section('content')
    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Secondary Search and Filter Bar -->
            <div class="secondary-filters">
                <div class="filter-top-bar d-lg-flex d-block">
                    <div class="row align-items-center justify-content-between">
                        <div class="secondary-search-bar">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" placeholder="Search" class="secondary-search-input">
                        </div>
                        <button class="filter-icon-btn mx-2" id="filterToggleBtn"><i class="fas fa-sliders-h"></i></button>
                        <button class="bookmark-icon-btn"><i class="fas fa-bookmark"></i></button>
                    </div>
                    <!-- Category Filters - Same Line -->
                    <div class="filters-section-wrapper ms-lg-4">
                        <button class="filter-scroll-btn filter-scroll-left" id="filterScrollLeft" aria-label="Scroll left">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <div class="filters-section mt-lg-0 mt-3" id="filtersSection">
                            <button class="filter-btn active">All</button>
                            <button class="filter-btn">Trump</button>
                            <button class="filter-btn">Chile Election</button>
                            <button class="filter-btn">Epstein</button>
                            <button class="filter-btn">Venezuela</button>
                            <button class="filter-btn">Ukraine</button>
                            <button class="filter-btn">Best of 2025</button>
                            <button class="filter-btn">Mamdani</button>
                            <button class="filter-btn">Gemini 3</button>
                            <button class="filter-btn">China</button>
                            <button class="filter-btn">Google Search</button>
                            <button class="filter-btn">Gaza</button>
                            <button class="filter-btn">Earnings</button>
                            <button class="filter-btn">Global Elections</button>
                            <button class="filter-btn">Israel</button>
                            <button class="filter-btn">Fed</button>
                            <button class="filter-btn">Trade War</button>
                            <button class="filter-btn">India-Pakistan</button>
                            <button class="filter-btn">AI</button>
                            <button class="filter-btn">Parlays</button>
                            <button class="filter-btn">Earn 4%</button>
                            <button class="filter-btn">US Election</button>
                            <button class="filter-btn">Crypto Prices</button>
                            <button class="filter-btn">Bitcoin</button>
                            <button class="filter-btn">Weather</button>
                            <button class="filter-btn">Movies</button>
                        </div>
                        <button class="filter-scroll-btn filter-scroll-right" id="filterScrollRight"
                            aria-label="Scroll right">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <!-- Filter Options Row -->
                <div class="filter-options-row">
                    <div class="filter-buttons-group">
                        <div class="filter-dropdown-wrapper sort-by-wrapper">
                            <button class="filter-option-btn sort-by-btn">
                                Sort by: <span class="filter-option-text">24hr Volume</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="sort-dropdown-menu">
                                <ul>
                                    <li class="sort-option active" data-sort="24hr-volume">
                                        <i class="fas fa-chart-line"></i>
                                        <span>24hr Volume</span>
                                        <span class="sort-dot"></span>
                                    </li>
                                    <li class="sort-option" data-sort="total-volume">
                                        <i class="fas fa-fire"></i>
                                        <span>Total Volume</span>
                                    </li>
                                    <li class="sort-option" data-sort="liquidity">
                                        <i class="fas fa-tint"></i>
                                        <span>Liquidity</span>
                                    </li>
                                    <li class="sort-option" data-sort="newest">
                                        <i class="fas fa-sparkles"></i>
                                        <span>Newest</span>
                                    </li>
                                    <li class="sort-option" data-sort="ending-soon">
                                        <i class="fas fa-clock"></i>
                                        <span>Ending Soon</span>
                                    </li>
                                    <li class="sort-option" data-sort="competitive">
                                        <i class="fas fa-trophy"></i>
                                        <span>Competitive</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="filter-dropdown-wrapper frequency-wrapper">
                            <button class="filter-option-btn frequency-btn">
                                Frequency: <span class="filter-option-text">All</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="frequency-dropdown-menu">
                                <ul>
                                    <li class="frequency-option active" data-frequency="all">All</li>
                                    <li class="frequency-option" data-frequency="daily">Daily</li>
                                    <li class="frequency-option" data-frequency="weekly">Weekly</li>
                                    <li class="frequency-option" data-frequency="monthly">Monthly</li>
                                </ul>
                            </div>
                        </div>
                        <div class="filter-dropdown-wrapper status-wrapper">
                            <button class="filter-option-btn status-btn">
                                Status: <span class="filter-option-text">Active</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="status-dropdown-menu">
                                <ul>
                                    <li class="status-option active" data-status="active">Active</li>
                                    <li class="status-option" data-status="closed">Closed</li>
                                    <li class="status-option" data-status="pending">Pending</li>
                                </ul>
                            </div>
                        </div>
                        <div class="filter-checkboxes">
                            <label class="filter-checkbox">
                                <span>Hide sports?</span>
                                <input class="filter-checkbox-input" type="checkbox" id="hideSports">
                            </label>
                            <label class="filter-checkbox">
                                <span>Hide crypto?</span>
                                <input class="filter-checkbox-input" type="checkbox" id="hideCrypto">
                            </label>
                            <label class="filter-checkbox">
                                <span>Hide earnings?</span>
                                <input class="filter-checkbox-input" type="checkbox" id="hideEarnings">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Markets Grid -->
            <div class="markets-grid mt-3 mt-lg-0">
                <!-- Market Card 2 -->
                @foreach ($events as $event)

                    <div class="market-card">
                        <div class="market-card-header">
                            <div class="market-profile-img">
                                <img src="{{ $event->image }}" alt="{{ $event->title }}">
                            </div>
                            <a href="" class="market-card-title">{{ $event->title }}</a>
                        </div>
                        <div class="market-card-body">

                            @foreach ($event->markets as $market)

                                @php
                                    $prices = json_decode($market->outcome_prices, true);
                                    $yesProb = isset($prices[0]) ? round($prices[0] * 100) : 0;
                                    $noProb = isset($prices[1]) ? round($prices[1] * 100) : 0;
                                @endphp
                                @if(!$market->outcomes == null)
                                    <div class="market-card-outcome-row">
                                        <span class="market-card-outcome-label">{{ $market->groupItem_title }}</span>
                                        <span class="market-card-outcome-probability">{{$yesProb}}%</span>
                                        <button class="market-card-yes-btn">Yes</button>
                                        <button class="market-card-no-btn">No</button>
                                    </div>
                                @else
                                    <div class="market-card-outcome-row">
                                        <span class="market-card-outcome-label">{{ $market->groupItem_title }}</span>
                                        <span class="market-card-outcome-probability">{{$yesProb}}%</span>
                                        <button class="market-card-yes-btn">Yes</button>
                                        <button class="market-card-no-btn">No</button>
                                    </div>
                                @endif
                            @endforeach

                        </div>
                        <div class="market-footer">
                            <span class="market-card-volume"><i class="fas fa-money-bill-wave"></i> ${{ $event->volume }}
                                Vol.</span>
                            <div class="market-actions d-flex gap-2">
                                <!-- <button class="market-card-action-btn"><i class="fas fa-redo"></i></button> -->
                                <!-- <button class="market-card-action-btn"><i class="fas fa-gift"></i></button> -->
                                <button class="market-card-action-btn"><i class="fas fa-bookmark"></i></button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
@endsection
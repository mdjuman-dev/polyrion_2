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
            <!-- Markets Grid - Livewire Component with Auto Refresh -->
            <livewire:markets-grid />
        </div>
    </main>
    @push('style')
        <style>
            /* Common Styles */
            .market-card {
                border-radius: 12px;
                padding: 16px;
                margin-bottom: 16px;
            }

            .market-card-header {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 16px;
            }

            .market-profile-img {
                width: 48px;
                height: 48px;
                border-radius: 8px;
                overflow: hidden;
            }

            .market-profile-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .market-card-title {
                color: var(--text-primary);
                text-decoration: none;
                font-size: 16px;
                font-weight: 600;
            }

            /* Type 1: Single Market (Ukraine style) */
            .single-market .market-card-header {
                justify-content: space-between;
            }

            .market-title-section {
                flex: 1;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .market-chance {
                display: flex;
                align-items: center;
                gap: 4px;
                background: var(--hover);
                padding: 4px 12px;
                border-radius: 6px;
            }

            .chance-arrow {
                color: var(--danger);
                font-size: 18px;
            }

            .chance-value {
                color: var(--danger);
                font-weight: 700;
                font-size: 18px;
            }

            .chance-label {
                color: var(--text-secondary);
                font-size: 12px;
            }

            .market-card-body-single {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                margin-bottom: 16px;
            }

            .market-card-yes-btn-large,
            .market-card-no-btn-large {
                padding: 10px 16px;
                border-radius: 8px;
                border: none;
                font-size: 18px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
            }

            .market-card-yes-btn-large {
                background: rgba(0, 200, 83, 0.1);
                color: var(--success);
                border: 2px solid rgba(0, 200, 83, 0.3);
            }

            .market-card-yes-btn-large:hover {
                background: rgba(0, 200, 83, 0.2);
                border-color: var(--success);
            }

            .market-card-no-btn-large {
                background: rgba(255, 71, 87, 0.1);
                color: var(--danger);
                border: 2px solid rgba(255, 71, 87, 0.3);
            }

            .market-card-no-btn-large:hover {
                background: rgba(255, 71, 87, 0.2);
                border-color: var(--danger);
            }

            /* Type 2: Multi Market (Fed rates style) */
            .multi-market .market-card-body {
                display: flex;
                flex-direction: column;
                gap: 8px;
                margin-bottom: 16px;
            }

            .market-card-outcome-row {
                display: grid;
                grid-template-columns: 2fr auto auto auto;
                align-items: center;
                gap: 12px;
                padding: 8px;
                background: var(--secondary);
                border-radius: 6px;
            }

            .market-card-outcome-label {
                color: var(--text-secondary);
                font-size: 14px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .market-card-outcome-probability {
                color: var(--text-primary);
                font-weight: 700;
                font-size: 16px;
            }

            .market-card-yes-btn,
            .market-card-no-btn {
                padding: 6px 12px;
                border-radius: 6px;
                border: none;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
                min-width: 45px;
                white-space: nowrap;
            }

            .market-card-yes-btn {
                background: rgba(0, 200, 83, 0.1);
                color: var(--success);
            }

            .market-card-yes-btn:hover {
                background: rgba(0, 200, 83, 0.2);
            }

            .market-card-no-btn {
                background: rgba(255, 71, 87, 0.1);
                color: var(--danger);
            }

            .market-card-no-btn:hover {
                background: rgba(255, 71, 87, 0.2);
            }

            /* Footer (Common) */
            .market-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding-top: 12px;
                border-top: 1px solid var(--border);
            }

            .market-card-volume {
                color: var(--text-secondary);
                font-size: 14px;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .market-actions {
                display: flex;
                gap: 8px;
            }

            .market-card-action-btn {
                background: var(--secondary);
                border: 1px solid var(--border);
                color: var(--text-secondary);
                padding: 8px 12px;
                border-radius: 6px;
                cursor: pointer;
                transition: all 0.3s;
            }

            .market-card-action-btn:hover {
                background: var(--hover);
                color: var(--accent);
                border-color: var(--accent);
            }

            /* Responsive */
            @media (max-width: 768px) {
                .market-card-outcome-row {
                    grid-template-columns: 1fr auto;
                    gap: 8px;
                }

                .market-card-outcome-probability {
                    grid-column: 2;
                    grid-row: 1;
                }

                .market-card-yes-btn,
                .market-card-no-btn {
                    grid-column: span 1;
                }
            }
        </style>
    @endpush
@endsection

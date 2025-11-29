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
                    <div class="row align-items-center ">
                        <div class="secondary-search-bar">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" placeholder="Search" class="secondary-search-input" id="marketSearchInput"
                                style="width: 100%;">
                            <button type="button" id="clearSearchBtn"
                                style="display: none; position: absolute; right: 20px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 5px;">
                                <i class="fas fa-times"></i>

                            </button>
                        </div>
                        <button class="filter-icon-btn mx-2" id="filterToggleBtn"><i class="fas fa-sliders-h"></i></button>
                        <a href="{{ route('saved.events') }}" class="bookmark-icon-btn" title="Saved Events">
                            <i class="fas fa-bookmark"></i>
                        </a>
                    </div>
                    <!-- Category Filters - Same Line -->
                    <div class="filters-section-wrapper ms-lg-4">
                        <button class="filter-scroll-btn filter-scroll-left" id="filterScrollLeft" aria-label="Scroll left">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <livewire:tag-filters />
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
                                Sort by: <span class="filter-option-text" id="sortByText">24hr Volume</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="sort-dropdown-menu">
                                <ul>
                                    <li class="sort-option" data-sort="24hr-volume"
                                        onclick="setSortBy('24hr-volume', '24hr Volume')">
                                        <i class="fas fa-chart-line"></i>
                                        <span>24hr Volume</span>
                                    </li>
                                    <li class="sort-option" data-sort="total-volume"
                                        onclick="setSortBy('total-volume', 'Total Volume')">
                                        <i class="fas fa-fire"></i>
                                        <span>Total Volume</span>
                                    </li>
                                    <li class="sort-option" data-sort="liquidity"
                                        onclick="setSortBy('liquidity', 'Liquidity')">
                                        <i class="fas fa-tint"></i>
                                        <span>Liquidity</span>
                                    </li>
                                    <li class="sort-option" data-sort="newest" onclick="setSortBy('newest', 'Newest')">
                                        <i class="fas fa-sparkles"></i>
                                        <span>Newest</span>
                                    </li>
                                    <li class="sort-option" data-sort="ending-soon"
                                        onclick="setSortBy('ending-soon', 'Ending Soon')">
                                        <i class="fas fa-clock"></i>
                                        <span>Ending Soon</span>
                                    </li>
                                    <li class="sort-option" data-sort="competitive"
                                        onclick="setSortBy('competitive', 'Competitive')">
                                        <i class="fas fa-trophy"></i>
                                        <span>Competitive</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="filter-dropdown-wrapper frequency-wrapper">
                            <button class="filter-option-btn frequency-btn">
                                Frequency: <span class="filter-option-text" id="frequencyText">All</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="frequency-dropdown-menu">
                                <ul>
                                    <li class="frequency-option" data-frequency="all" onclick="setFrequency('all', 'All')">
                                        All</li>
                                    <li class="frequency-option" data-frequency="daily"
                                        onclick="setFrequency('daily', 'Daily')">Daily</li>
                                    <li class="frequency-option" data-frequency="weekly"
                                        onclick="setFrequency('weekly', 'Weekly')">Weekly</li>
                                    <li class="frequency-option" data-frequency="monthly"
                                        onclick="setFrequency('monthly', 'Monthly')">Monthly</li>
                                </ul>
                            </div>
                        </div>
                        <div class="filter-dropdown-wrapper status-wrapper">
                            <button class="filter-option-btn status-btn">
                                Status: <span class="filter-option-text" id="statusText">Active</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="status-dropdown-menu">
                                <ul>
                                    <li class="status-option" data-status="active" onclick="setStatus('active', 'Active')">
                                        Active</li>
                                    <li class="status-option" data-status="closed" onclick="setStatus('closed', 'Closed')">
                                        Closed</li>
                                    <li class="status-option" data-status="pending"
                                        onclick="setStatus('pending', 'Pending')">Pending</li>
                                </ul>
                            </div>
                        </div>
                        <div class="filter-checkboxes">
                            <label class="filter-checkbox">
                                <span>Hide sports?</span>
                                <input class="filter-checkbox-input" type="checkbox" wire:model.live="hideSports"
                                    id="hideSports">
                            </label>
                            <label class="filter-checkbox">
                                <span>Hide crypto?</span>
                                <input class="filter-checkbox-input" type="checkbox" wire:model.live="hideCrypto"
                                    id="hideCrypto">
                            </label>
                            <label class="filter-checkbox">
                                <span>Hide earnings?</span>
                                <input class="filter-checkbox-input" type="checkbox" wire:model.live="hideEarnings"
                                    id="hideEarnings">
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
                text-align: center;
            }

            .chance-value {
                font-size: 22px;
                font-weight: 700;
                display: block;
                margin-bottom: 2px;
                transition: 0.3s ease;
            }

            .chance-label {
                font-size: 12px;
                color: #83899f;
                text-transform: uppercase;
                letter-spacing: .5px;
            }

            /* Colors similar to Polymarket */
            .chance-value.danger {
                color: #ff4d4f;
                /* red */
            }

            .chance-value.warning {
                color: #f4c430;
                /* yellow */
            }

            .chance-value.success {
                color: #32d296;
                /* green */
            }

            .market-chance {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 4px;
                border-radius: 6px;
                text-align: center;
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
                    /* grid-template-columns: 1fr auto; */
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

            .loader,
            .loader:before,
            .loader:after {
                border-radius: 50%;
                width: 2.5em;
                height: 2.5em;
                animation-fill-mode: both;
                animation: bblFadInOut 1.8s infinite ease-in-out;
            }

            .loader {
                color: #FFF;
                font-size: 7px;
                position: relative;
                text-indent: -9999em;
                transform: translateZ(0);
                animation-delay: -0.16s;
            }

            .loader:before,
            .loader:after {
                content: '';
                position: absolute;
                top: 0;
            }

            .loader:before {
                left: -3.5em;
                animation-delay: -0.32s;
            }

            .loader:after {
                left: 3.5em;
            }

            @keyframes bblFadInOut {

                0%,
                80%,
                100% {
                    box-shadow: 0 2.5em 0 -1.3em
                }

                40% {
                    box-shadow: 0 2.5em 0 0
                }
            }
        </style>
    @endpush
    @push('script')
        <script>
            document.addEventListener('livewire:init', function() {
                const searchInput = document.getElementById('marketSearchInput');
                const clearBtn = document.getElementById('clearSearchBtn');
                let searchTimeout;

                function findMarketsGridComponent() {
                    // Find MarketsGrid component by data attribute
                    const marketsGridElement = document.querySelector('[data-component="markets-grid"]');
                    if (marketsGridElement) {
                        const wireId = marketsGridElement.getAttribute('wire:id');
                        if (wireId) {
                            return Livewire.find(wireId);
                        }
                    }
                    return null;
                }

                if (searchInput && clearBtn) {
                    // Sync input with Livewire MarketsGrid component
                    searchInput.addEventListener('input', function() {
                        const value = this.value;

                        // Show/hide clear button
                        if (value.length > 0) {
                            clearBtn.style.display = 'block';
                        } else {
                            clearBtn.style.display = 'none';
                        }

                        // Debounce search
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(function() {
                            const component = findMarketsGridComponent();
                            if (component && component.set) {
                                component.set('search', value);
                            }
                        }, 300);
                    });

                    // Clear search
                    clearBtn.addEventListener('click', function() {
                        searchInput.value = '';
                        this.style.display = 'none';

                        // Clear Livewire search
                        const component = findMarketsGridComponent();
                        if (component && component.set) {
                            component.set('search', '');
                        }
                    });
                }

                // Filter functions
                function setSortBy(sort, label) {
                    const component = findMarketsGridComponent();
                    if (component && component.call) {
                        component.call('setSortBy', sort);
                        document.getElementById('sortByText').textContent = label;
                    }
                }

                function setFrequency(frequency, label) {
                    const component = findMarketsGridComponent();
                    if (component && component.call) {
                        component.call('setFrequency', frequency);
                        document.getElementById('frequencyText').textContent = label;
                    }
                }

                function setStatus(status, label) {
                    const component = findMarketsGridComponent();
                    if (component && component.call) {
                        component.call('setStatus', status);
                        document.getElementById('statusText').textContent = label;
                    }
                }

                // Make functions globally available
                window.setSortBy = setSortBy;
                window.setFrequency = setFrequency;
                window.setStatus = setStatus;
            });
        </script>
    @endpush
@endsection

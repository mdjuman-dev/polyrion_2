@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>{{ $appName }} - Prediction Markets & Trading Platform</title>
    <meta name="description"
        content="Trade on prediction markets, bet on real-world events, and explore thousands of markets on {{ $appName }}. Join the future of decentralized prediction markets.">
    <meta name="keywords" content="prediction markets, trading, betting, events, markets, polymarket, decentralized">
    <meta name="author" content="{{ $appName }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $appUrl }}">
    <meta property="og:title" content="{{ $appName }} - Prediction Markets & Trading Platform">
    <meta property="og:description"
        content="Trade on prediction markets, bet on real-world events, and explore thousands of markets on {{ $appName }}.">
    @if ($logo)
        <meta property="og:image" content="{{ str_starts_with($logo, 'http') ? $logo : asset('storage/' . $logo) }}">
    @endif

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ $appUrl }}">
    <meta property="twitter:title" content="{{ $appName }} - Prediction Markets & Trading Platform">
    <meta property="twitter:description"
        content="Trade on prediction markets, bet on real-world events, and explore thousands of markets on {{ $appName }}.">
    @if ($logo)
        <meta property="twitter:image" content="{{ str_starts_with($logo, 'http') ? $logo : asset('storage/' . $logo) }}">
    @endif

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ $appUrl }}">
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
                                    <li class="status-option" data-status="active"
                                        onclick="setStatus('active', 'Active')">
                                        Active</li>
                                    <li class="status-option" data-status="closed"
                                        onclick="setStatus('closed', 'Resolved')">
                                        Resolved</li>
                                </ul>
                            </div>
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

            /* Event Countdown Timer */
            .event-countdown {
                display: flex;
                gap: 15px;
                padding: 12px 20px;
                border: 2px solid #ef4444;
                border-radius: 8px;
                background: rgba(239, 68, 68, 0.05);
                margin-left: auto;
                align-items: center;
                flex-shrink: 0;
            }

            .countdown-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                min-width: 50px;
            }

            .countdown-number {
                font-size: 28px;
                font-weight: 700;
                color: #6c757d;
                line-height: 1;
                margin-bottom: 4px;
            }

            .countdown-label {
                font-size: 11px;
                color: #9ca3af;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            @media (max-width: 768px) {
                .event-countdown {
                    gap: 10px;
                    padding: 10px 15px;
                    margin-left: 0;
                    margin-top: 10px;
                    width: 100%;
                    justify-content: center;
                }

                .countdown-number {
                    font-size: 24px;
                }

                .countdown-item {
                    min-width: 45px;
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
            // Event Countdown Timer
            function updateEventCountdowns() {
                const countdowns = document.querySelectorAll('.event-countdown');

                countdowns.forEach(countdown => {
                    const endDateStr = countdown.getAttribute('data-end-date');
                    if (!endDateStr) return;

                    const endDate = new Date(endDateStr);
                    const now = new Date();
                    const diff = endDate - now;

                    if (diff <= 0) {
                        // Event has ended
                        const daysEl = countdown.querySelector('[data-days]');
                        const hoursEl = countdown.querySelector('[data-hours]');
                        const minutesEl = countdown.querySelector('[data-minutes]');
                        if (daysEl) daysEl.textContent = '0';
                        if (hoursEl) hoursEl.textContent = '0';
                        if (minutesEl) minutesEl.textContent = '0';
                        return;
                    }

                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

                    const daysEl = countdown.querySelector('[data-days]');
                    const hoursEl = countdown.querySelector('[data-hours]');
                    const minutesEl = countdown.querySelector('[data-minutes]');

                    if (daysEl) daysEl.textContent = days;
                    if (hoursEl) hoursEl.textContent = hours;
                    if (minutesEl) minutesEl.textContent = minutes;
                });
            }

            // Initialize countdown on page load
            document.addEventListener('DOMContentLoaded', function() {
                updateEventCountdowns();
                // Update every minute
                setInterval(updateEventCountdowns, 60000);
            });

            // Update countdown after Livewire updates
            document.addEventListener('livewire:init', function() {
                updateEventCountdowns();
                Livewire.hook('morph.updated', () => {
                    setTimeout(updateEventCountdowns, 100);
                });
            });

            // Original Livewire search functionality
            document.addEventListener('livewire:initialized', function() {
                const searchInput = document.getElementById('marketSearchInput');
                const clearBtn = document.getElementById('clearSearchBtn');
                let searchTimeout;
                let marketsGridComponent = null;

                function findMarketsGridComponent() {
                    // Try to get cached component first
                    if (marketsGridComponent && marketsGridComponent.$wire) {
                        return marketsGridComponent;
                    }

                    // Ensure Livewire is available
                    if (typeof Livewire === 'undefined') {
                        console.warn('Livewire is not loaded');
                        return null;
                    }

                    // Find MarketsGrid component by data attribute
                    const marketsGridElement = document.querySelector('[data-component="markets-grid"]');
                    if (marketsGridElement) {
                        const wireId = marketsGridElement.getAttribute('wire:id');
                        if (wireId) {
                            try {
                                // Livewire v3 method
                                if (Livewire.find) {
                                    marketsGridComponent = Livewire.find(wireId);
                                    if (marketsGridComponent) {
                                        return marketsGridComponent;
                                    }
                                }
                                // Livewire v2 method
                                else if (Livewire.all) {
                                    const components = Livewire.all();
                                    for (let i = 0; i < components.length; i++) {
                                        const comp = components[i];
                                        if (comp && comp.__instance && comp.__instance.id === wireId) {
                                            marketsGridComponent = comp;
                                            return comp;
                                        }
                                    }
                                }
                            } catch (e) {
                                console.warn('Could not find Livewire component by ID:', e);
                            }
                        }

                        // Try to get component from element's properties (v3)
                        if (marketsGridElement.__livewire) {
                            marketsGridComponent = marketsGridElement.__livewire;
                            return marketsGridComponent;
                        }

                        // Try Alpine.js $wire (v3)
                        if (marketsGridElement._x_dataStack && marketsGridElement._x_dataStack[0] && marketsGridElement
                            ._x_dataStack[0].$wire) {
                            marketsGridComponent = marketsGridElement._x_dataStack[0];
                            return marketsGridComponent;
                        }
                    }

                    // Fallback: try to find by component name
                    try {
                        if (Livewire.all) {
                            const components = Livewire.all();
                            for (let i = 0; i < components.length; i++) {
                                const component = components[i];
                                if (component) {
                                    const element = component.el || component.$el || (component.__instance && component
                                        .__instance.el);
                                    if (element && element.getAttribute && element.getAttribute('data-component') ===
                                        'markets-grid') {
                                        marketsGridComponent = component;
                                        return component;
                                    }
                                }
                            }
                        }
                    } catch (e) {
                        console.warn('Error finding component:', e);
                    }

                    return null;
                }

                // Initialize component on load
                function initializeComponent() {
                    marketsGridComponent = findMarketsGridComponent();
                    if (!marketsGridComponent) {
                        // Retry after a short delay if component not found
                        setTimeout(initializeComponent, 100);
                    }
                }

                // Wait for DOM and Livewire to be ready
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initializeComponent);
                } else {
                    setTimeout(initializeComponent, 100);
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
                            // Use Livewire event dispatch (most reliable method)
                            if (typeof Livewire !== 'undefined') {
                                // Livewire v3 method
                                if (Livewire.dispatch) {
                                    Livewire.dispatch('search-query-updated', {
                                        query: value
                                    });
                                }
                                // Livewire v2 fallback
                                else if (Livewire.emit) {
                                    Livewire.emit('search-query-updated', value);
                                }
                                // Try to find component and update directly
                                else {
                                    const component = findMarketsGridComponent();
                                    if (component && component.$wire) {
                                        component.$wire.set('search', value);
                                    }
                                }
                            } else {
                                console.warn('Livewire is not loaded');
                            }
                        }, 300);
                    });

                    // Clear search
                    clearBtn.addEventListener('click', function() {
                        searchInput.value = '';
                        this.style.display = 'none';

                        // Clear Livewire search
                        if (typeof Livewire !== 'undefined') {
                            if (Livewire.dispatch) {
                                Livewire.dispatch('search-query-updated', {
                                    query: ''
                                });
                            } else if (Livewire.emit) {
                                Livewire.emit('search-query-updated', '');
                            }
                        }
                    });
                }

                // Filter functions - Improved with better component access
                function setSortBy(sort, label) {
                    const component = findMarketsGridComponent();
                    if (component) {
                        // Try Livewire v3 method first
                        if (component.$wire && typeof component.$wire.call === 'function') {
                            component.$wire.call('setSortBy', sort);
                        }
                        // Try Livewire v2 method
                        else if (typeof component.call === 'function') {
                            component.call('setSortBy', sort);
                        }
                        // Try direct method call
                        else if (component.__instance && component.__instance.call) {
                            component.__instance.call('setSortBy', sort);
                        } else {
                            console.warn('Could not call setSortBy on component');
                        }
                        const sortByText = document.getElementById('sortByText');
                        if (sortByText) {
                            sortByText.textContent = label;
                        }
                    } else {
                        console.warn('MarketsGrid component not found for setSortBy');
                    }
                }

                function setFrequency(frequency, label) {
                    const component = findMarketsGridComponent();
                    if (component) {
                        if (component.$wire && typeof component.$wire.call === 'function') {
                            component.$wire.call('setFrequency', frequency);
                        } else if (typeof component.call === 'function') {
                            component.call('setFrequency', frequency);
                        } else if (component.__instance && component.__instance.call) {
                            component.__instance.call('setFrequency', frequency);
                        }
                        const frequencyText = document.getElementById('frequencyText');
                        if (frequencyText) {
                            frequencyText.textContent = label;
                        }
                    } else {
                        console.warn('MarketsGrid component not found for setFrequency');
                    }
                }

                function setStatus(status, label) {
                    const component = findMarketsGridComponent();
                    if (component) {
                        if (component.$wire && typeof component.$wire.call === 'function') {
                            component.$wire.call('setStatus', status);
                        } else if (typeof component.call === 'function') {
                            component.call('setStatus', status);
                        } else if (component.__instance && component.__instance.call) {
                            component.__instance.call('setStatus', status);
                        }
                        const statusText = document.getElementById('statusText');
                        if (statusText) {
                            statusText.textContent = label;
                        }
                    } else {
                        console.warn('MarketsGrid component not found for setStatus');
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

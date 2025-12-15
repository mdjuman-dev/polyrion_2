@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Profile - {{ $appName }}</title>
    <meta name="description" content="View and manage your profile on {{ $appName }}.">
@endsection
@section('content')
    <main>
        <div class="container mt-5">
            <div class="row d-flex justify-content-between m-auto">
                <!-- Left Column - User Profile -->
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="profile-card">
                        <div class="profile-header">
                            <div class="profile-avatar-wrapper">
                                <div class="profile-avatar-gradient">
                                    <img src="{{ $profileImage }}" alt="{{ $user->name }}" loading="lazy"
                                        class="img-responsive"
                                        onerror="this.src='{{ asset('frontend/assets/images/default-avatar.png') }}'">
                                </div>
                            </div>
                            <div class="profile-info">
                                <div class="profile-id-wrapper d-flex justify-content-between">
                                    <span class="profile-id">{{ $user->name }}</span>
                                    <div class="profile-actions">
                                        <a href="{{ route('profile.settings') }}" class="btn-icon" title="Edit Profile">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-icon" title="Logout">
                                                <i class="fas fa-arrow-right-from-bracket"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="profile-meta">
                                    <span>Member since {{ $user->created_at->format('M Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="profile-stats">
                            <div class="stat-item">
                                <div class="stat-label">Positions Value</div>
                                <div class="stat-value">${{ number_format($stats['positions_value'], 2) }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Biggest Win</div>
                                <div class="stat-value">
                                    {{ $stats['biggest_win'] > 0 ? '$' . number_format($stats['biggest_win'], 2) : '—' }}
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Predictions</div>
                                <div class="stat-value">{{ $stats['predictions'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Profit/Loss  -->
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="profit-loss-card">
                        <div class="profit-loss-header">
                            <div class="profit-loss-title">
                                <i class="fas fa-arrow-up profit-icon"></i>
                                <span>Profit/Loss</span>
                            </div>
                            <div class="time-filters">
                                <button type="button" class="time-filter-btn active" data-time="1D">1D</button>
                                <button type="button" class="time-filter-btn" data-time="1W">1W</button>
                                <button type="button" class="time-filter-btn" data-time="1M">1M</button>
                                <button type="button" class="time-filter-btn" data-time="ALL">ALL</button>
                            </div>
                        </div>
                        <div class="profit-loss-content">
                            <div class="profit-loss-value">
                                <span class="pl-amount" id="profitLossAmount">$0.00</span>
                                <i class="fas fa-info-circle pl-info-icon" title="Total profit/loss"></i>
                            </div>
                            <div class="profit-loss-timeframe" id="profitLossTimeframe">Past Day</div>
                            <div class="profit-loss-chart" id="profitLossChart" style="height: 100px; margin-top: 20px;">
                                <!-- Chart will be rendered here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="row m-auto">
                <div class="col-12">
                    <div class="positions-activity-card">
                        <div class="content-tabs">
                            <button type="button" class="content-tab active" data-tab="positions">Positions</button>
                            <button type="button" class="content-tab" data-tab="activity">Activity</button>
                        </div>

                        <!-- Positions Tab Content -->
                        <div class="tab-content-wrapper" id="positions-tab">
                            <div class="positions-controls">
                                <div class="positions-subtabs">
                                    <button type="button" class="subtab-btn active" data-subtab="active">Active</button>
                                    <button type="button" class="subtab-btn" data-subtab="closed">Closed</button>
                                </div>
                                <div class="positions-search-filter">
                                    <div class="search-wrapper">
                                        <i class="fas fa-search search-icon"></i>
                                        <input type="text" class="search-input" placeholder="Q Search positions">
                                    </div>
                                    <div class="filter-dropdown-wrapper">
                                        <button type="button" class="filter-dropdown-btn" id="sortFilterBtn">
                                            <span>Value</span>
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                        <div class="filter-dropdown-menu" id="sortFilterMenu">
                                            <a href="#" class="filter-dropdown-item active"
                                                data-sort="value">Value</a>
                                            <a href="#" class="filter-dropdown-item" data-sort="profit">Profit/Loss
                                                $</a>
                                            <a href="#" class="filter-dropdown-item"
                                                data-sort="profit_pct">Profit/Loss %</a>
                                            <a href="#" class="filter-dropdown-item" data-sort="bet">Bet</a>
                                            <a href="#" class="filter-dropdown-item"
                                                data-sort="alphabetical">Alphabetically</a>
                                            <a href="#" class="filter-dropdown-item" data-sort="avg_price">Average
                                                Price</a>
                                            <a href="#" class="filter-dropdown-item"
                                                data-sort="current_price">Current Price</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($trades->count() > 0)
                                <div class="positions-table-container" style="padding: 0;">
                                    <table class="positions-table" style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr
                                                style="border-bottom: 1px solid var(--border); background: var(--bg-secondary);">
                                                <th
                                                    style="padding: 1rem; text-align: left; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                    MARKET</th>
                                                <th
                                                    style="padding: 1rem; text-align: center; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                    OPTION</th>
                                                <th
                                                    style="padding: 1rem; text-align: right; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                    AVG</th>
                                                <th
                                                    style="padding: 1rem; text-align: right; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                    CURRENT</th>
                                                <th
                                                    style="padding: 1rem; text-align: right; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                    VALUE</th>
                                                <th
                                                    style="padding: 1rem; text-align: right; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                    P/L</th>
                                                <th
                                                    style="padding: 1rem; text-align: center; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                    STATUS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($trades as $trade)
                                                @php
                                                    $position = $activePositions->firstWhere('trade.id', $trade->id);
                                                    $isActive = $position && $position['is_open'];
                                                    $isClosed =
                                                        $position && $position['is_closed'] && !$position['has_result'];
                                                    $hasResult = $position && $position['has_result'];

                                                    // Calculate current value and P/L (Polymarket style - matching first image)
                                                    $avgPrice = $trade->price ?? 0.0001; // Default to 0.0001 (0.01¢) if null

                                                    // Get current price from market
                                                    if ($position && $position['market']) {
                                                        $outcomePrices = json_decode(
                                                            $position['market']->outcome_prices,
                                                            true,
                                                        );
                                                        if (is_array($outcomePrices)) {
                                                            if ($trade->option === 'yes') {
                                                                $currentPrice = $outcomePrices[0] ?? $avgPrice;
                                                            } else {
                                                                $currentPrice = $outcomePrices[1] ?? $avgPrice;
                                                            }
                                                        } else {
                                                            $currentPrice = $avgPrice;
                                                        }
                                                    } else {
                                                        $currentPrice = $avgPrice;
                                                    }

                                                    // Calculate shares: amount / price per share (Polymarket formula)
                                                    // Example: $10 at 0.01¢ (0.0001) = 100,000 shares
                                                    $shares =
                                                        $avgPrice > 0 ? $trade->amount / $avgPrice : $trade->amount;

                                                    // Current value = shares * current price
                                                    // Example: 100,000 shares * 0.0095 = $950
                                                    $currentValue = $shares * $currentPrice;

                                                    // Calculate P/L (matching first image format)
                                                    if ($trade->status === 'win' && $trade->payout_amount) {
                                                        // Already settled - use actual payout
                                                        $profitLoss = $trade->payout_amount - $trade->amount;
                                                        // P/L % based on price change: ((current_price - avg_price) / avg_price) * 100
                                                        $profitLossPct =
                                                            $avgPrice > 0
                                                                ? (($currentPrice - $avgPrice) / $avgPrice) * 100
                                                                : 0;
                                                    } elseif ($trade->status === 'loss') {
                                                        // Lost - lost the full amount
                                                        $profitLoss = -$trade->amount;
                                                        $profitLossPct = -100;
                                                    } else {
                                                        // Pending - calculate based on current price (like first image)
                                                        $profitLoss = $currentValue - $trade->amount;
                                                        // P/L % = ((current_price - avg_price) / avg_price) * 100
                                                        // Example: ((0.0095 - 0.0001) / 0.0001) * 100 = 9,400%
                                                        $profitLossPct =
                                                            $avgPrice > 0
                                                                ? (($currentPrice - $avgPrice) / $avgPrice) * 100
                                                                : 0;
                                                    }
                                                @endphp
                                                <tr style="border-bottom: 1px solid var(--border); transition: background 0.2s; cursor: pointer;"
                                                    class="position-row"
                                                    data-subtab="{{ $trade->status === 'pending' && $isActive ? 'active' : 'closed' }}"
                                                    data-market="{{ strtolower($position['market']->question ?? '') }}">
                                                    <td style="padding: 1rem; color: var(--text-primary);">
                                                        <div
                                                            style="font-weight: 500; font-size: 0.95rem; max-width: 450px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; line-height: 1.4;">
                                                            {{ $position['market']->question ?? 'N/A' }}
                                                        </div>
                                                        @if ($position && $position['close_time'])
                                                            <div
                                                                style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.3rem; line-height: 1.4;">
                                                                <i class="fas fa-clock"></i> Closes
                                                                {{ $position['close_time']->diffForHumans() }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td style="padding: 1rem; text-align: center;">
                                                        <span
                                                            style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 4px; font-weight: 600; font-size: 0.85rem; 
                                                    background: {{ $trade->option === 'yes' ? '#10b98120' : '#ef444420' }};
                                                    color: {{ $trade->option === 'yes' ? '#10b981' : '#ef4444' }};
                                                    border: 1px solid {{ $trade->option === 'yes' ? '#10b98140' : '#ef444440' }};
                                                ">
                                                            {{ strtoupper($trade->option) }}
                                                        </span>
                                                    </td>
                                                    <td
                                                        style="padding: 1rem; text-align: right; color: var(--text-primary); font-weight: 500; font-size: 0.95rem; line-height: 1.4;">
                                                        {{ number_format($avgPrice * 100, 2) }}¢
                                                    </td>
                                                    <td
                                                        style="padding: 1rem; text-align: right; color: var(--text-primary); font-weight: 500; font-size: 0.95rem; line-height: 1.4;">
                                                        {{ number_format($currentPrice * 100, 2) }}¢
                                                    </td>
                                                    <td
                                                        style="padding: 1rem; text-align: right; color: var(--text-primary); font-weight: 600; font-size: 0.95rem; line-height: 1.4;">
                                                        ${{ number_format($currentValue, 2) }}
                                                    </td>
                                                    <td style="padding: 1rem; text-align: right;">
                                                        <div
                                                            style="font-weight: 600; font-size: 0.95rem; color: {{ $profitLoss >= 0 ? '#10b981' : '#ef4444' }}; line-height: 1.4;">
                                                            {{ $profitLoss >= 0 ? '+' : '' }}${{ number_format($profitLoss, 2) }}
                                                        </div>
                                                        <div
                                                            style="font-size: 0.85rem; color: {{ $profitLoss >= 0 ? '#10b981' : '#ef4444' }}; margin-top: 0.2rem; line-height: 1.4;">
                                                            {{ $profitLoss >= 0 ? '+' : '' }}{{ number_format($profitLossPct, 2) }}%
                                                        </div>
                                                    </td>
                                                    <td style="padding: 1rem; text-align: center;">
                                                        @if ($isActive)
                                                            <span
                                                                style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.9rem; color: #10b981;">
                                                                <span
                                                                    style="margin-right: 0.4rem; font-size: 1rem; line-height: 1;">•</span>
                                                                Open
                                                            </span>
                                                        @elseif($isClosed)
                                                            <span
                                                                style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.9rem; color: #f59e0b;">
                                                                <span
                                                                    style="margin-right: 0.4rem; font-size: 1rem; line-height: 1;">•</span>
                                                                Closed
                                                            </span>
                                                        @elseif($trade->status === 'win')
                                                            <span
                                                                style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.9rem; color: #10b981;">
                                                                <span
                                                                    style="margin-right: 0.4rem; font-size: 1rem; line-height: 1;">•</span>
                                                                Win
                                                            </span>
                                                        @elseif($trade->status === 'loss')
                                                            <span
                                                                style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.9rem; color: #ef4444;">
                                                                <span
                                                                    style="margin-right: 0.4rem; font-size: 1rem; line-height: 1;">•</span>
                                                                Loss
                                                            </span>
                                                        @else
                                                            <span
                                                                style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.9rem; color: #6366f1;">
                                                                <span
                                                                    style="margin-right: 0.4rem; font-size: 1rem; line-height: 1;">•</span>
                                                                Pending
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="no-positions-message" style="padding: 3rem; text-align: center;">
                                    <p style="color: var(--text-secondary); font-size: 1rem;">No positions found</p>
                                </div>
                            @endif
                        </div>

                        <!-- Activity Tab Content -->
                        <div class="tab-content-wrapper d-none" id="activity-tab">
                            <div class="positions-controls">
                                <div class="filter-dropdown-wrapper">
                                    <button type="button" class="filter-dropdown-btn" id="amountFilterBtn">
                                        <span>Min amount</span>
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                    <div class="filter-dropdown-menu" id="amountFilterMenu">
                                        <a href="#" class="filter-dropdown-item active" data-amount="all">All</a>
                                        <a href="#" class="filter-dropdown-item" data-amount="10">$10+</a>
                                        <a href="#" class="filter-dropdown-item" data-amount="100">$100+</a>
                                        <a href="#" class="filter-dropdown-item" data-amount="1000">$1,000+</a>
                                    </div>
                                </div>
                            </div>

                            <!-- No Activity Message -->
                            <div class="no-positions-message">
                                <p>No activity found</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('script')
        <script>
            $(document).ready(function() {
                // Time filter buttons
                $('.time-filter-btn').on('click', function() {
                    $('.time-filter-btn').removeClass('active');
                    $(this).addClass('active');

                    const timeframe = $(this).data('time');
                    updateProfitLoss(timeframe);
                });

                // Tab switching
                $('.content-tab').on('click', function() {
                    const tab = $(this).data('tab');
                    $('.content-tab').removeClass('active');
                    $(this).addClass('active');
                    $('.tab-content-wrapper').addClass('d-none');
                    $('#' + tab + '-tab').removeClass('d-none');
                });

                // Subtab switching
                $('.subtab-btn').on('click', function() {
                    const subtab = $(this).data('subtab');
                    $('.subtab-btn').removeClass('active');
                    $(this).addClass('active');

                    // Filter positions by subtab
                    $('.position-row').each(function() {
                        const rowSubtab = $(this).data('subtab');
                        if (subtab === 'active') {
                            $(this).toggle(rowSubtab === 'active');
                        } else {
                            $(this).toggle(rowSubtab === 'closed');
                        }
                    });
                });

                // Filter dropdown
                $('#sortFilterBtn').on('click', function(e) {
                    e.stopPropagation();
                    $('#sortFilterMenu').toggle();
                });

                $(document).on('click', function() {
                    $('#sortFilterMenu').hide();
                });

                $('.filter-dropdown-item').on('click', function(e) {
                    e.preventDefault();
                    $('.filter-dropdown-item').removeClass('active');
                    $(this).addClass('active');
                    const sort = $(this).data('sort');
                    $('#sortFilterBtn span').text($(this).text());
                    $('#sortFilterMenu').hide();
                    // TODO: Sort positions
                });

                // Amount filter dropdown
                $('#amountFilterBtn').on('click', function(e) {
                    e.stopPropagation();
                    $('#amountFilterMenu').toggle();
                });

                $('.filter-dropdown-item[data-amount]').on('click', function(e) {
                    e.preventDefault();
                    $('.filter-dropdown-item[data-amount]').removeClass('active');
                    $(this).addClass('active');
                    const amount = $(this).data('amount');
                    $('#amountFilterBtn span').text($(this).text());
                    $('#amountFilterMenu').hide();
                    // TODO: Filter activity by amount
                });

                // Search functionality
                $('.search-input').on('input', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    $('.position-row').each(function() {
                        const marketText = $(this).data('market') || '';
                        $(this).toggle(marketText.includes(searchTerm));
                    });
                });

                // Initialize profit/loss
                updateProfitLoss('1D');
            });

            function updateProfitLoss(timeframe) {
                const timeframes = {
                    '1D': 'Past Day',
                    '1W': 'Past Week',
                    '1M': 'Past Month',
                    'ALL': 'All Time'
                };

                $('#profitLossTimeframe').text(timeframes[timeframe] || 'Past Day');

                // TODO: Fetch actual profit/loss data via AJAX
                // For now, show placeholder
                $('#profitLossAmount').text('$0.00');
            }
        </script>
    @endpush
@endsection

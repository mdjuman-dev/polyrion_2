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
                            <div class="time-filters" style="display: flex; gap: 0.5rem;">
                                <button type="button" class="time-filter-btn active" data-time="1D"
                                    style="padding: 0.5rem 1rem; background: #ffb11a; color: #000; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">1D</button>
                                <button type="button" class="time-filter-btn" data-time="1W"
                                    style="padding: 0.5rem 1rem; background: var(--bg-secondary); color: var(--text-secondary); border: 1px solid var(--border); border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.3s ease;"
                                    onmouseover="this.style.background='rgba(255, 177, 26, 0.1)'; this.style.borderColor='#ffb11a'; this.style.color='var(--text-primary)'"
                                    onmouseout="this.style.background='var(--bg-secondary)'; this.style.borderColor='var(--border)'; this.style.color='var(--text-secondary)'">1W</button>
                                <button type="button" class="time-filter-btn" data-time="1M"
                                    style="padding: 0.5rem 1rem; background: var(--bg-secondary); color: var(--text-secondary); border: 1px solid var(--border); border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.3s ease;"
                                    onmouseover="this.style.background='rgba(255, 177, 26, 0.1)'; this.style.borderColor='#ffb11a'; this.style.color='var(--text-primary)'"
                                    onmouseout="this.style.background='var(--bg-secondary)'; this.style.borderColor='var(--border)'; this.style.color='var(--text-secondary)'">1M</button>
                                <button type="button" class="time-filter-btn" data-time="ALL"
                                    style="padding: 0.5rem 1rem; background: var(--bg-secondary); color: var(--text-secondary); border: 1px solid var(--border); border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.3s ease;"
                                    onmouseover="this.style.background='rgba(255, 177, 26, 0.1)'; this.style.borderColor='#ffb11a'; this.style.color='var(--text-primary)'"
                                    onmouseout="this.style.background='var(--bg-secondary)'; this.style.borderColor='var(--border)'; this.style.color='var(--text-secondary)'">ALL</button>
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
                            <button type="button" class="content-tab" data-tab="withdrawals">Withdrawal</button>
                            <button type="button" class="content-tab" data-tab="settings">Settings</button>
                        </div>

                        <!-- Positions Tab Content -->
                        <div class="tab-content-wrapper" id="positions-tab">
                            <div class="positions-controls">
                                <div class="positions-subtabs" style="display: flex; gap: 0.5rem;">
                                    <button type="button" class="subtab-btn active" data-subtab="active"
                                        style="padding: 0.5rem 1rem; background: #ffb11a; color: #000; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">Active</button>
                                    <button type="button" class="subtab-btn" data-subtab="closed"
                                        style="padding: 0.5rem 1rem; background: var(--bg-secondary); color: var(--text-secondary); border: 1px solid var(--border); border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.3s ease;"
                                        onmouseover="this.style.background='rgba(255, 177, 26, 0.1)'; this.style.borderColor='#ffb11a'; this.style.color='var(--text-primary)'"
                                        onmouseout="this.style.background='var(--bg-secondary)'; this.style.borderColor='var(--border)'; this.style.color='var(--text-secondary)'">Closed</button>
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
                                <div class="search-wrapper" style="flex: 1; max-width: 500px;">
                                    <i class="fas fa-search search-icon"></i>
                                    <input type="text" class="search-input" id="activitySearchInput"
                                        placeholder="Search for Order ID or Product">
                                </div>
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

                            <!-- Activity List -->
                            @if (isset($allActivity) && $allActivity->count() > 0)
                                <div class="activity-list" style="margin-top: 1.5rem;">
                                    @foreach ($allActivity as $activity)
                                        @if ($activity['type'] === 'trade')
                                            @php $trade = $activity['data']; @endphp
                                            <div class="activity-item"
                                                style="padding: 1rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; transition: background 0.2s; cursor: pointer;"
                                                data-activity-type="trade" data-activity-id="{{ $trade->id }}"
                                                data-amount="{{ $trade->amount_invested ?? ($trade->amount ?? 0) }}">
                                                <div style="flex: 1;">
                                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                        <div
                                                            style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                                            <i class="fas fa-chart-line"></i>
                                                        </div>
                                                        <div style="flex: 1;">
                                                            <div
                                                                style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;">
                                                                Trade #{{ $trade->id }}
                                                                @if ($trade->market && $trade->market->event)
                                                                    -
                                                                    {{ \Illuminate\Support\Str::limit($trade->market->event->title, 40) }}
                                                                @endif
                                                            </div>
                                                            <div style="font-size: 0.85rem; color: var(--text-secondary);">
                                                                {{ $trade->created_at->format('M d, Y h:i A') }}
                                                                @if ($trade->outcome || $trade->side)
                                                                    •
                                                                    {{ strtoupper($trade->outcome ?? ($trade->side ?? 'N/A')) }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div style="text-align: right;">
                                                    <div
                                                        style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;">
                                                        ${{ number_format($trade->amount_invested ?? ($trade->amount ?? 0), 2) }}
                                                    </div>
                                                    @php $status = strtoupper($trade->status ?? 'PENDING'); @endphp
                                                    @if ($status === 'PENDING')
                                                        <span style="font-size: 0.85rem; color: #f59e0b;">Pending</span>
                                                    @elseif($status === 'WON' || $status === 'WIN')
                                                        <span style="font-size: 0.85rem; color: #10b981;">Won</span>
                                                    @elseif($status === 'LOST' || $status === 'LOSS')
                                                        <span style="font-size: 0.85rem; color: #ef4444;">Lost</span>
                                                    @else
                                                        <span
                                                            style="font-size: 0.85rem; color: var(--text-secondary);">{{ $status }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            @php $withdrawal = $activity['data']; @endphp
                                            <div class="activity-item"
                                                style="padding: 1rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; transition: background 0.2s; cursor: pointer;"
                                                data-activity-type="withdrawal" data-activity-id="{{ $withdrawal->id }}"
                                                data-amount="{{ $withdrawal->amount ?? 0 }}">
                                                <div style="flex: 1;">
                                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                        <div
                                                            style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                                            <i class="fas fa-money-bill-wave"></i>
                                                        </div>
                                                        <div style="flex: 1;">
                                                            <div
                                                                style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;">
                                                                Withdrawal #{{ $withdrawal->id }}
                                                            </div>
                                                            <div style="font-size: 0.85rem; color: var(--text-secondary);">
                                                                {{ $withdrawal->created_at->format('M d, Y h:i A') }}
                                                                • {{ ucfirst($withdrawal->payment_method ?? 'N/A') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div style="text-align: right;">
                                                    <div
                                                        style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;">
                                                        ${{ number_format($withdrawal->amount ?? 0, 2) }}
                                                    </div>
                                                    @php $status = strtolower($withdrawal->status ?? 'pending'); @endphp
                                                    @if ($status === 'approved' || $status === 'completed')
                                                        <span style="font-size: 0.85rem; color: #10b981;">Approved</span>
                                                    @elseif($status === 'pending')
                                                        <span style="font-size: 0.85rem; color: #f59e0b;">Pending</span>
                                                    @elseif($status === 'rejected')
                                                        <span style="font-size: 0.85rem; color: #ef4444;">Rejected</span>
                                                    @else
                                                        <span
                                                            style="font-size: 0.85rem; color: var(--text-secondary);">{{ ucfirst($status) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="no-positions-message" style="padding: 3rem; text-align: center;">
                                    <p style="color: var(--text-secondary); font-size: 1rem;">No activity found</p>
                                </div>
                            @endif
                        </div>

                        <!-- Withdrawal Requests Tab -->
                        <div class="tab-content-wrapper d-none" id="withdrawals-tab">
                            <div class="positions-controls">
                                <div class="search-wrapper" style="flex: 1; max-width: 500px;">
                                    <i class="fas fa-search search-icon"></i>
                                    <input type="text" class="search-input" id="withdrawalSearchInput"
                                        placeholder="Search by amount or transaction ID">
                                </div>
                                <div class="filter-dropdown-wrapper">
                                    <button type="button" class="filter-dropdown-btn" id="withdrawalStatusFilterBtn">
                                        <span>All Status</span>
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                    <div class="filter-dropdown-menu" id="withdrawalStatusFilterMenu">
                                        <a href="#" class="filter-dropdown-item active" data-status="all">All
                                            Status</a>
                                        <a href="#" class="filter-dropdown-item" data-status="pending">Pending</a>
                                        <a href="#" class="filter-dropdown-item"
                                            data-status="approved">Approved</a>
                                        <a href="#" class="filter-dropdown-item"
                                            data-status="completed">Completed</a>
                                        <a href="#" class="filter-dropdown-item"
                                            data-status="rejected">Rejected</a>
                                    </div>
                                </div>
                                <button type="button" onclick="openWithdrawalModal()" class="btn btn-primary"
                                    style="margin-left: 1rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, #ffb11a 0%, #ff9500 100%); color: #000; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(255, 177, 26, 0.3);"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(255, 177, 26, 0.4)'; this.style.background='linear-gradient(135deg, #ff9500 0%, #ffb11a 100%)'"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(255, 177, 26, 0.3)'; this.style.background='linear-gradient(135deg, #ffb11a 0%, #ff9500 100%)'">
                                    <i class="fas fa-plus"></i> New Withdrawal
                                </button>
                            </div>

                            @if (isset($withdrawals) && $withdrawals->count() > 0)
                                <div class="withdrawal-list" style="margin-top: 1.5rem;">
                                    @foreach ($withdrawals as $withdrawal)
                                        <div class="withdrawal-item"
                                            style="padding: 1rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; transition: background 0.2s;"
                                            data-withdrawal-id="{{ $withdrawal->id }}"
                                            data-status="{{ strtolower($withdrawal->status ?? 'pending') }}"
                                            data-amount="{{ $withdrawal->amount ?? 0 }}">
                                            <div style="flex: 1;">
                                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                    <div
                                                        style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                                        <i class="fas fa-arrow-up"></i>
                                                    </div>
                                                    <div style="flex: 1;">
                                                        <div
                                                            style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;">
                                                            Transaction #{{ $withdrawal->id }}
                                                        </div>
                                                        <div style="font-size: 0.85rem; color: var(--text-secondary);">
                                                            {{ $withdrawal->created_at->format('M d, Y h:i A') }}
                                                            • {{ ucfirst($withdrawal->payment_method ?? 'N/A') }}
                                                            @if ($withdrawal->account_number || $withdrawal->account_name)
                                                                •
                                                                {{ $withdrawal->account_name ?? $withdrawal->account_number }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="text-align: right; display: flex; align-items: center; gap: 1rem;">
                                                <div>
                                                    <div
                                                        style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; font-size: 1.1rem;">
                                                        ${{ number_format($withdrawal->amount ?? 0, 2) }}
                                                    </div>
                                                    @php $status = strtolower($withdrawal->status ?? 'pending'); @endphp
                                                    @if ($status === 'approved' || $status === 'completed')
                                                        <span
                                                            style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.75rem; border-radius: 4px; background: #10b98120; color: #10b981; font-size: 0.85rem; font-weight: 500;">
                                                            <i class="fas fa-check-circle"></i> Approved
                                                        </span>
                                                    @elseif($status === 'pending')
                                                        <span
                                                            style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.75rem; border-radius: 4px; background: #f59e0b20; color: #f59e0b; font-size: 0.85rem; font-weight: 500;">
                                                            <i class="fas fa-clock"></i> Pending
                                                        </span>
                                                    @elseif($status === 'rejected')
                                                        <span
                                                            style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.75rem; border-radius: 4px; background: #ef444420; color: #ef4444; font-size: 0.85rem; font-weight: 500;">
                                                            <i class="fas fa-times-circle"></i> Rejected
                                                        </span>
                                                    @else
                                                        <span
                                                            style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.75rem; border-radius: 4px; background: var(--bg-secondary); color: var(--text-secondary); font-size: 0.85rem; font-weight: 500;">
                                                            {{ ucfirst($status) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="no-positions-message" style="padding: 3rem; text-align: center;">
                                    <i class="fas fa-inbox"
                                        style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                                    <p style="color: var(--text-secondary); font-size: 1rem; margin-bottom: 1rem;">No
                                        withdrawal requests yet</p>
                                    <button type="button" onclick="openWithdrawalModal()" class="btn btn-primary"
                                        style="padding: 0.875rem 1.75rem; background: linear-gradient(135deg, #ffb11a 0%, #ff9500 100%); color: #000; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(255, 177, 26, 0.3);"
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(255, 177, 26, 0.4)'; this.style.background='linear-gradient(135deg, #ff9500 0%, #ffb11a 100%)'"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(255, 177, 26, 0.3)'; this.style.background='linear-gradient(135deg, #ffb11a 0%, #ff9500 100%)'">
                                        <i class="fas fa-plus"></i> Create Withdrawal Request
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Settings Tab Content -->
                        <div class="tab-content-wrapper d-none" id="settings-tab">
                            <div class="settings-card"
                                style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 2rem;">
                                <h3 class="card-title"
                                    style="font-size: 1.5rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.5rem;">
                                    Profile Settings
                                </h3>

                                <form id="profileSettingsForm" method="POST" action="{{ route('profile.update') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <!-- Profile Image Upload -->
                                    <div class="profile-avatar-section mb-4"
                                        style="display: flex; align-items: center; gap: 1.5rem;">
                                        <div class="avatar-upload">
                                            <div class="profile-avatar-gradient"
                                                style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 3px; position: relative;">
                                                <img src="{{ $profileImage }}" alt="{{ $user->name }}"
                                                    id="profileImagePreview"
                                                    style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; background: white;">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="profile_image" class="btn btn-secondary"
                                                style="cursor: pointer; padding: 0.5rem 1rem; border-radius: 6px; background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text-primary);">
                                                <i class="fas fa-camera"></i> Upload Photo
                                            </label>
                                            <input type="file" id="profile_image" name="profile_image"
                                                accept="image/*" style="display: none;"
                                                onchange="previewProfileImage(this)">
                                            <p
                                                style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.5rem;">
                                                PNG, JPG up to 2MB</p>
                                        </div>
                                    </div>

                                    <!-- Form Fields -->
                                    <div class="profile-form-fields"
                                        style="display: flex; flex-direction: column; gap: 1.5rem;">
                                        <div class="form-field">
                                            <label for="name" class="form-label"
                                                style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">Full
                                                Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                placeholder="Enter your full name" value="{{ $user->name }}" required
                                                style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--card-bg); color: var(--text-primary);">
                                        </div>

                                        <div class="form-field">
                                            <label for="email" class="form-label"
                                                style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">Email</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                placeholder="Enter your email" value="{{ $user->email }}" required
                                                style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--card-bg); color: var(--text-primary);">
                                        </div>

                                        <div class="form-field">
                                            <label for="username" class="form-label"
                                                style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">Username</label>
                                            <input type="text" class="form-control" id="username" name="username"
                                                placeholder="Enter your username" value="{{ $user->username ?? '' }}"
                                                style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--card-bg); color: var(--text-primary);">
                                        </div>

                                        <div class="form-field">
                                            <label for="number" class="form-label"
                                                style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">Phone
                                                Number</label>
                                            <input type="text" class="form-control" id="number" name="number"
                                                placeholder="Enter your phone number" value="{{ $user->number ?? '' }}"
                                                style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--card-bg); color: var(--text-primary);">
                                        </div>
                                    </div>

                                    <!-- Save Button -->
                                    <div class="profile-save-section mt-4"
                                        style="display: flex; justify-content: flex-end; gap: 1rem;">
                                        <button type="submit" id="saveProfileBtn" class="btn-save-changes"
                                            style="padding: 0.875rem 2rem; background: linear-gradient(135deg, #ffb11a 0%, #ff9500 100%); color: #000; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(255, 177, 26, 0.3);"
                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(255, 177, 26, 0.4)'; this.style.background='linear-gradient(135deg, #ff9500 0%, #ffb11a 100%)'"
                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(255, 177, 26, 0.3)'; this.style.background='linear-gradient(135deg, #ffb11a 0%, #ff9500 100%)'">
                                            <i class="fas fa-save"></i> Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Withdrawal Modal Overlay -->
    <div class="withdrawal-modal-overlay" id="withdrawalModalOverlay"></div>

    <!-- Withdrawal Modal -->
    <div id="withdrawalModal" class="withdrawal-modal-popup">
        @livewire('withdrawal-request')
    </div>

    <style>
        /* Withdrawal Modal Overlay - Matching Deposit Design */
        .withdrawal-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 7000;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .withdrawal-modal-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Withdrawal Modal Popup */
        .withdrawal-modal-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.95);
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            z-index: 7001;
            display: none;
            opacity: 0;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .withdrawal-modal-popup.active {
            display: block;
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .withdrawal-modal-popup {
                width: 95%;
                max-width: none;
                border-radius: 20px 20px 0 0;
                top: auto;
                bottom: 0;
                transform: translate(-50%, 100%);
                max-height: 85vh;
            }

            .withdrawal-modal-popup.active {
                transform: translate(-50%, 0);
            }
        }

        @media (max-width: 480px) {
            .withdrawal-modal-popup {
                width: 100%;
                border-radius: 20px 20px 0 0;
            }
        }
    </style>

    @push('script')
        <script>
            $(document).ready(function() {
                // Time filter buttons
                $('.time-filter-btn').on('click', function() {
                    $('.time-filter-btn').each(function() {
                        $(this).removeClass('active');
                        if (!$(this).hasClass('active')) {
                            $(this).css({
                                'background': 'var(--bg-secondary)',
                                'color': 'var(--text-secondary)',
                                'border': '1px solid var(--border)'
                            });
                        }
                    });

                    $(this).addClass('active');
                    $(this).css({
                        'background': '#ffb11a',
                        'color': '#000',
                        'border': 'none'
                    });

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
                    $('.subtab-btn').each(function() {
                        $(this).removeClass('active');
                        if (!$(this).hasClass('active')) {
                            $(this).css({
                                'background': 'var(--bg-secondary)',
                                'color': 'var(--text-secondary)',
                                'border': '1px solid var(--border)',
                                'font-weight': '500'
                            });
                        }
                    });

                    $(this).addClass('active');
                    $(this).css({
                        'background': '#ffb11a',
                        'color': '#000',
                        'border': 'none',
                        'font-weight': '600'
                    });

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

                // Search functionality for positions
                $('.search-input').on('input', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    $('.position-row').each(function() {
                        const marketText = $(this).data('market') || '';
                        $(this).toggle(marketText.includes(searchTerm));
                    });
                });

                // Activity search
                $('#activitySearchInput').on('input', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    $('.activity-item').each(function() {
                        const activityId = $(this).data('activity-id') || '';
                        const activityText = $(this).text().toLowerCase();
                        $(this).toggle(activityId.toString().includes(searchTerm) || activityText
                            .includes(searchTerm));
                    });
                });

                // Withdrawal search
                $('#withdrawalSearchInput').on('input', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    $('.withdrawal-item').each(function() {
                        const withdrawalId = $(this).data('withdrawal-id') || '';
                        const withdrawalText = $(this).text().toLowerCase();
                        $(this).toggle(withdrawalId.toString().includes(searchTerm) || withdrawalText
                            .includes(searchTerm));
                    });
                });

                // Withdrawal status filter
                $('#withdrawalStatusFilterBtn').on('click', function(e) {
                    e.stopPropagation();
                    $('#withdrawalStatusFilterMenu').toggle();
                });

                $('.filter-dropdown-item[data-status]').on('click', function(e) {
                    e.preventDefault();
                    $('.filter-dropdown-item[data-status]').removeClass('active');
                    $(this).addClass('active');
                    const status = $(this).data('status');
                    $('#withdrawalStatusFilterBtn span').text($(this).text());
                    $('#withdrawalStatusFilterMenu').hide();

                    if (status === 'all') {
                        $('.withdrawal-item').show();
                    } else {
                        $('.withdrawal-item').each(function() {
                            const itemStatus = $(this).data('status');
                            $(this).toggle(itemStatus === status);
                        });
                    }
                });

                // Initialize profit/loss
                updateProfitLoss('1D');

                // Withdrawal Modal Functions (Matching Deposit Modal)
                window.openWithdrawalModal = function() {
                    const $modal = $('#withdrawalModal');
                    const $overlay = $('#withdrawalModalOverlay');
                    $modal.addClass('active');
                    $overlay.addClass('active');
                    $('body').css('overflow', 'hidden');
                };

                window.closeWithdrawalModal = function() {
                    const $modal = $('#withdrawalModal');
                    const $overlay = $('#withdrawalModalOverlay');
                    $modal.removeClass('active');
                    $overlay.removeClass('active');
                    $('body').css('overflow', '');
                };

                // Close modal on overlay click
                $('#withdrawalModalOverlay').on('click', function() {
                    closeWithdrawalModal();
                });

                // Open modal if redirected from withdrawal page
                @if (session('open_withdrawal_modal'))
                    setTimeout(function() {
                        openWithdrawalModal();
                    }, 500);
                @endif

                // Profile image preview
                window.previewProfileImage = function(input) {
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('profileImagePreview').src = e.target.result;
                        };
                        reader.readAsDataURL(input.files[0]);
                    }
                };

                // Profile settings form submission
                $('#profileSettingsForm').on('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const btn = $('#saveProfileBtn');
                    const originalText = btn.html();

                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            btn.prop('disabled', false).html(originalText);
                            if (typeof showSuccess !== 'undefined') {
                                showSuccess('Profile updated successfully!', 'Success');
                            } else if (typeof toastr !== 'undefined') {
                                toastr.success('Profile updated successfully!', 'Success');
                            }
                            // Reload page after 1 second to show updated data
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false).html(originalText);
                            let errorMsg = 'Failed to update profile. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            if (typeof showError !== 'undefined') {
                                showError(errorMsg, 'Error');
                            } else if (typeof toastr !== 'undefined') {
                                toastr.error(errorMsg, 'Error');
                            }
                        }
                    });
                });
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

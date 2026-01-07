@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Profile - {{ $appName }}</title>
    <meta name="description" content="View and manage your profile on {{ $appName }}.">
@endsection
@section('content')
    <main>
        <div class="container mt-5">
            <div class="row d-flex justify-content-between m-auto">
                <!-- Left Column - Portfolio -->
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="portfolio-panel"
                        style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; position: relative;">
                        <!-- Header -->
                        <div
                            style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-ban" style="color: var(--text-secondary); font-size: 0.875rem;"></i>
                                <span
                                    style="font-size: 0.875rem; font-weight: 500; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px;">Portfolio</span>
                            </div>
                            <!-- Balance Badge -->
                            <div
                                style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 6px; padding: 0.35rem 0.6rem; display: flex; align-items: center; gap: 0.4rem;">
                                <i class="fas fa-stack" style="color: #10b981; font-size: 0.75rem;"></i>
                                <span
                                    style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary);">${{ number_format($balance, 2) }}</span>
                            </div>
                        </div>

                        <!-- Current Value -->
                        <div style="margin-bottom: 0.5rem;">
                            <div style="font-size: 1.75rem; font-weight: 700; color: var(--text-primary); line-height: 1.2;">
                                ${{ number_format($stats['positions_value'], 2) }}
                            </div>
                        </div>

                        <!-- Timeframe -->
                        <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 1.5rem;">Today</div>

                        <!-- Action Buttons -->
                        <div style="display: flex; gap: 0.75rem;">
                            <button
                                onclick="if(typeof openDepositModal === 'function') { openDepositModal(); } else if(typeof window.openDepositModal === 'function') { window.openDepositModal(); }"
                                style="flex: 1; background: #3b82f6; color: #ffffff; border: none; border-radius: 8px; padding: 0.75rem 1rem; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s;"
                                onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
                                <i class="fas fa-arrow-down"></i>
                                <span>Deposit</span>
                            </button>
                            <button onclick="handleWithdrawalClick()"
                                style="flex: 1; background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border); border-radius: 8px; padding: 0.75rem 1rem; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s;"
                                onmouseover="this.style.background='var(--hover)'; this.style.borderColor='var(--primary-color, #ffb11a)'"
                                onmouseout="this.style.background='var(--bg-secondary)'; this.style.borderColor='var(--border)'">
                                <i class="fas fa-arrow-up"></i>
                                <span>Withdraw</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Profit/Loss  -->
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="profit-loss-panel"
                        style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; position: relative;">
                        <!-- Header -->
                        <div
                            style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-arrow-up" style="color: #10b981; font-size: 0.875rem;"></i>
                                <span
                                    style="font-size: 0.875rem; font-weight: 500; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px;">Profit/Loss</span>
                            </div>
                            <!-- Time Filters -->
                            <div style="display: flex; gap: 0.4rem;">
                                <button type="button" class="pl-time-filter" data-pl-period="1D"
                                    style="padding: 0.35rem 0.6rem; background: transparent; color: var(--text-secondary); border: none; border-radius: 4px; font-weight: 500; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;">1D</button>
                                <button type="button" class="pl-time-filter" data-pl-period="1W"
                                    style="padding: 0.35rem 0.6rem; background: transparent; color: var(--text-secondary); border: none; border-radius: 4px; font-weight: 500; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;">1W</button>
                                <button type="button" class="pl-time-filter active" data-pl-period="1M"
                                    style="padding: 0.35rem 0.6rem; background: rgba(59, 130, 246, 0.2); color: var(--text-primary); border: none; border-radius: 4px; font-weight: 600; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;">1M</button>
                                <button type="button" class="pl-time-filter" data-pl-period="ALL"
                                    style="padding: 0.35rem 0.6rem; background: transparent; color: var(--text-secondary); border: none; border-radius: 4px; font-weight: 500; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;">ALL</button>
                            </div>
                        </div>

                        <!-- Current Value -->
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <div style="font-size: 1.75rem; font-weight: 700; color: var(--text-primary); line-height: 1.2;">
                                <span id="profitLossAmount">${{ number_format($stats['total_profit_loss'] ?? 0, 2) }}</span>
                            </div>
                            <i class="fas fa-info-circle" style="color: var(--text-secondary); font-size: 0.875rem; cursor: pointer;"
                                title="Profit/Loss for the selected time period"></i>
                        </div>

                        <!-- Timeframe -->
                        <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 1rem;" id="profitLossTimeframe">Past
                            Month</div>

                        <!-- Polyrion Logo -->
                        <div
                            style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);">
                            <div
                                style="width: 24px; height: 24px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 4px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.7rem; color: #ffffff;">
                                PM</div>
                            <span style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 500;">Polyrion</span>
                        </div>

                        <!-- Chart Placeholder -->
                        <div
                            style="height: 60px; background: linear-gradient(90deg, rgba(59, 130, 246, 0.2) 0%, rgba(139, 92, 246, 0.1) 100%); border-radius: 6px; position: relative; overflow: hidden;">
                            <div
                                style="position: absolute; bottom: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent 0%, rgba(59, 130, 246, 0.5) 50%, transparent 100%);">
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
                            <button type="button" class="content-tab" data-tab="deposits">Deposits</button>
                            <button type="button" class="content-tab" data-tab="withdrawals">Withdrawal</button>
                            <button type="button" class="content-tab" data-tab="referral">Referral</button>
                            <button type="button" class="content-tab" data-tab="settings">Profile</button>
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
                                            <a href="#" class="filter-dropdown-item"
                                                data-sort="profit">Profit/Loss</a>
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
                                <!-- Desktop Table View -->
                                <div class="positions-table-container desktop-view" style="padding: 0;">
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
                                                    $tradeStatus = strtoupper($trade->status ?? 'PENDING');
                                                    $isTradeClosed = $tradeStatus === 'CLOSED';
                                                    $isTradeSettled = in_array($tradeStatus, [
                                                        'WON',
                                                        'WIN',
                                                        'LOST',
                                                        'LOSS',
                                                    ]);
                                                    $isActive =
                                                        $position &&
                                                        $position['is_open'] &&
                                                        !$isTradeClosed &&
                                                        !$isTradeSettled;
                                                    $isClosed =
                                                        $position &&
                                                        ($position['is_closed'] || $isTradeClosed) &&
                                                        !$position['has_result'];
                                                    $hasResult = $position && $position['has_result'];

                                                    // Calculate current value and P/L (Polyrion style - matching first image)
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

                                                    // Calculate shares: amount / price per share (Polyrion formula)
                                                    // Example: $10 at 0.01¢ (0.0001) = 100,000 shares
                                                    $shares =
                                                        $avgPrice > 0 ? $trade->amount / $avgPrice : $trade->amount;

                                                    // Current value = shares * current price
                                                    // Example: 100,000 shares * 0.0095 = $950
                                                    $currentValue = $shares * $currentPrice;

                                                    // Calculate P/L (matching first image format)
                                                    $tradeStatusUpper = strtoupper($trade->status ?? 'PENDING');
                                                    if (
                                                        ($tradeStatusUpper === 'WON' || $tradeStatusUpper === 'WIN') &&
                                                        ($trade->payout_amount || $trade->payout)
                                                    ) {
                                                        // Already settled - use actual payout
                                                        $payout = $trade->payout ?? ($trade->payout_amount ?? 0);
                                                        $profitLoss = $payout - $trade->amount;
                                                        // P/L % based on price change: ((current_price - avg_price) / avg_price) * 100
                                                        $profitLossPct =
                                                            $avgPrice > 0
                                                                ? (($currentPrice - $avgPrice) / $avgPrice) * 100
                                                                : 0;
                                                    } elseif (
                                                        $tradeStatusUpper === 'LOST' ||
                                                        $tradeStatusUpper === 'LOSS'
                                                    ) {
                                                        // Lost - lost the full amount
                                                        $profitLoss = -$trade->amount;
                                                        $profitLossPct = -100;
                                                    } elseif ($tradeStatusUpper === 'CLOSED' && $trade->payout) {
                                                        // Closed position - use payout as sell value
                                                        $profitLoss = $trade->payout - $trade->amount;
                                                        $profitLossPct =
                                                            $trade->amount > 0
                                                                ? ($profitLoss / $trade->amount) * 100
                                                                : 0;
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
                                                    data-subtab="{{ strtoupper($trade->status ?? '') === 'PENDING' && $isActive ? 'active' : 'closed' }}"
                                                    data-market="{{ strtolower($position['market']->question ?? '') }}"
                                                    data-value="{{ $currentValue }}" data-profit="{{ $profitLoss }}"
                                                    data-profit-pct="{{ $profitLossPct }}"
                                                    data-bet="{{ $trade->amount }}" data-avg-price="{{ $avgPrice }}"
                                                    data-current-price="{{ $currentPrice }}">
                                                    <td style="padding: 1rem; color: var(--text-primary);">
                                                        <div
                                                            style="font-weight: 500; font-size: 0.95rem; max-width: 450px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; line-height: 1.4;">
                                                            @if($position['market'] && $position['market']->event)
                                                                <a href="{{ route('market.details', $position['market']->event->slug ?? $position['market']->event->id) }}" 
                                                                   style="color: var(--text-primary); text-decoration: none; transition: color 0.2s;"
                                                                   onmouseover="this.style.color='var(--primary-color, #3b82f6)'" 
                                                                   onmouseout="this.style.color='var(--text-primary)'">
                                                                    {{ $position['market']->question ?? 'N/A' }}
                                                                </a>
                                                            @else
                                                                {{ $position['market']->question ?? 'N/A' }}
                                                            @endif
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
                                                            style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem; margin-bottom: 0.3rem;">
                                                            <span
                                                                style="font-weight: 600; font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: 4px; 
                                                                background: {{ $profitLoss >= 0 ? '#10b98120' : '#ef444420' }};
                                                                color: {{ $profitLoss >= 0 ? '#10b981' : '#ef4444' }};
                                                                border: 1px solid {{ $profitLoss >= 0 ? '#10b98140' : '#ef444440' }};
                                                            ">
                                                                {{ $profitLoss >= 0 ? 'Profit' : 'Loss' }}
                                                            </span>
                                                        </div>
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
                                                        @if ($isActive && strtoupper($trade->status) === 'PENDING')
                                                            <span
                                                                style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.9rem; color: #10b981;">
                                                                <span
                                                                    style="margin-right: 0.4rem; font-size: 1rem; line-height: 1;">•</span>
                                                                Open
                                                            </span>
                                                        @elseif(strtoupper($trade->status ?? '') === 'CLOSED')
                                                            <span
                                                                style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.9rem; color: #f59e0b;">
                                                                <span
                                                                    style="margin-right: 0.4rem; font-size: 1rem; line-height: 1;">•</span>
                                                                Closed
                                                            </span>
                                                        @elseif($isClosed)
                                                            <span
                                                                style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.9rem; color: #f59e0b;">
                                                                <span
                                                                    style="margin-right: 0.4rem; font-size: 1rem; line-height: 1;">•</span>
                                                                Closed
                                                            </span>
                                                        @elseif(strtoupper($trade->status ?? '') === 'WON' || strtoupper($trade->status ?? '') === 'WIN')
                                                            <span
                                                                style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.9rem; color: #10b981;">
                                                                <span
                                                                    style="margin-right: 0.4rem; font-size: 1rem; line-height: 1;">•</span>
                                                                Win
                                                            </span>
                                                        @elseif(strtoupper($trade->status ?? '') === 'LOST' || strtoupper($trade->status ?? '') === 'LOSS')
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

                                <!-- Mobile Card View -->
                                <div class="positions-cards-container mobile-view" style="display: none;">
                                    @foreach ($trades as $trade)
                                        @php
                                            $position = $activePositions->firstWhere('trade.id', $trade->id);
                                            $tradeStatus = strtoupper($trade->status ?? 'PENDING');
                                            $isTradeClosed = $tradeStatus === 'CLOSED';
                                            $isTradeSettled = in_array($tradeStatus, ['WON', 'WIN', 'LOST', 'LOSS']);
                                            $isActive =
                                                $position &&
                                                $position['is_open'] &&
                                                !$isTradeClosed &&
                                                !$isTradeSettled;
                                            $isClosed =
                                                $position &&
                                                ($position['is_closed'] || $isTradeClosed) &&
                                                !$position['has_result'];
                                            $hasResult = $position && $position['has_result'];

                                            // Calculate current value and P/L
                                            $avgPrice = $trade->price ?? 0.0001;

                                            if ($position && $position['market']) {
                                                $outcomePrices = json_decode($position['market']->outcome_prices, true);
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

                                            $shares = $avgPrice > 0 ? $trade->amount / $avgPrice : $trade->amount;
                                            $currentValue = $shares * $currentPrice;

                                            $tradeStatusUpper = strtoupper($trade->status ?? 'PENDING');
                                            if (
                                                ($tradeStatusUpper === 'WON' || $tradeStatusUpper === 'WIN') &&
                                                ($trade->payout_amount || $trade->payout)
                                            ) {
                                                $payout = $trade->payout ?? ($trade->payout_amount ?? 0);
                                                $profitLoss = $payout - $trade->amount;
                                                $profitLossPct =
                                                    $avgPrice > 0 ? (($currentPrice - $avgPrice) / $avgPrice) * 100 : 0;
                                            } elseif ($tradeStatusUpper === 'LOST' || $tradeStatusUpper === 'LOSS') {
                                                $profitLoss = -$trade->amount;
                                                $profitLossPct = -100;
                                            } elseif ($tradeStatusUpper === 'CLOSED' && $trade->payout) {
                                                $profitLoss = $trade->payout - $trade->amount;
                                                $profitLossPct =
                                                    $trade->amount > 0 ? ($profitLoss / $trade->amount) * 100 : 0;
                                            } else {
                                                $profitLoss = $currentValue - $trade->amount;
                                                $profitLossPct =
                                                    $avgPrice > 0 ? (($currentPrice - $avgPrice) / $avgPrice) * 100 : 0;
                                            }
                                        @endphp
                                        <div class="position-card position-row"
                                            data-subtab="{{ strtoupper($trade->status ?? '') === 'PENDING' && $isActive ? 'active' : 'closed' }}"
                                            data-market="{{ strtolower($position['market']->question ?? '') }}"
                                            data-value="{{ $currentValue }}" data-profit="{{ $profitLoss }}"
                                            data-profit-pct="{{ $profitLossPct }}" data-bet="{{ $trade->amount }}"
                                            data-avg-price="{{ $avgPrice }}"
                                            data-current-price="{{ $currentPrice }}"
                                            style="background: var(--card-bg); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 1rem; margin-bottom: 0; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 1rem;">
                                            <!-- Left Icon -->
                                            <div style="flex-shrink: 0;">
                                                <div
                                                    style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%); display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-chart-line"
                                                        style="color: #ffffff; font-size: 20px;"></i>
                                                </div>
                                            </div>

                                            <!-- Middle Content -->
                                            <div style="flex: 1; min-width: 0;">
                                                @php
                                                    $marketQuestion = $position['market']->question ?? 'N/A';
                                                    $words = explode(' ', $marketQuestion);
                                                    $firstPart = '';
                                                    $secondPart = '';

                                                    // Try to split into two parts if long
                                                    if (count($words) > 3) {
                                                        $midPoint = ceil(count($words) / 2);
                                                        $firstPart = implode(' ', array_slice($words, 0, $midPoint));
                                                        $secondPart = implode(' ', array_slice($words, $midPoint));
                                                    } else {
                                                        $firstPart = $marketQuestion;
                                                    }

                                                    $tradeNumber = $loop->iteration;
                                                    $tradeDate = $trade->created_at ?? now();
                                                @endphp

                                                <!-- Trade # and First Line -->
                                                <div
                                                    style="font-weight: 700; font-size: 0.95rem; color: var(--text-primary); margin-bottom: 0.25rem; line-height: 1.3;">
                                                    Trade #{{ $tradeNumber }} - 
                                                    @if($position['market'] && $position['market']->event)
                                                        <a href="{{ route('market.details', $position['market']->event->slug ?? $position['market']->event->id) }}" 
                                                           style="color: var(--text-primary); text-decoration: none; transition: color 0.2s;"
                                                           onmouseover="this.style.color='var(--primary-color, #3b82f6)'" 
                                                           onmouseout="this.style.color='var(--text-primary)'">
                                                            {{ $firstPart }}
                                                        </a>
                                                    @else
                                                        {{ $firstPart }}
                                                    @endif
                                                </div>

                                                @if ($secondPart)
                                                    <!-- Second Line -->
                                                    <div
                                                        style="font-weight: 700; font-size: 0.95rem; color: var(--text-primary); margin-bottom: 0.25rem; line-height: 1.3;">
                                                        {{ $secondPart }}
                                                    </div>
                                                @endif

                                                <!-- Date, Time and Option -->
                                                <div
                                                    style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.25rem; line-height: 1.4;">
                                                    {{ $tradeDate->format('M d, Y h:i A') }} •
                                                    {{ strtoupper($trade->option) }}
                                                </div>
                                            </div>

                                            <!-- Right Side - P/L Info -->
                                            <div style="flex-shrink: 0; text-align: right;">
                                                <!-- Profit/Loss Badge -->
                                                <div style="margin-bottom: 0.5rem;">
                                                    <span
                                                        style="display: inline-block; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem; 
                                                        background: {{ $profitLoss >= 0 ? '#10b981' : '#ef4444' }};
                                                        color: #ffffff;
                                                    ">
                                                        {{ $profitLoss >= 0 ? 'Profit' : 'Loss' }}
                                                    </span>
                                                </div>

                                                <!-- P/L Amount -->
                                                <div
                                                    style="font-weight: 700; font-size: 1rem; color: {{ $profitLoss >= 0 ? '#10b981' : '#ef4444' }}; margin-bottom: 0.25rem; line-height: 1.2;">
                                                    {{ $profitLoss >= 0 ? '+' : '' }}${{ number_format($profitLoss, 2) }}
                                                </div>

                                                <!-- Status -->
                                                <div style="margin-top: 0.25rem;">
                                                    @if ($isActive && strtoupper($trade->status) === 'PENDING')
                                                        <span
                                                            style="font-weight: 500; font-size: 0.85rem; color: #f59e0b;">
                                                            Pending
                                                        </span>
                                                    @elseif(strtoupper($trade->status ?? '') === 'CLOSED')
                                                        <span
                                                            style="font-weight: 500; font-size: 0.85rem; color: #f59e0b;">
                                                            Closed
                                                        </span>
                                                    @elseif($isClosed)
                                                        <span
                                                            style="font-weight: 500; font-size: 0.85rem; color: #f59e0b;">
                                                            Closed
                                                        </span>
                                                    @elseif(strtoupper($trade->status ?? '') === 'WON' || strtoupper($trade->status ?? '') === 'WIN')
                                                        <span
                                                            style="font-weight: 500; font-size: 0.85rem; color: #10b981;">
                                                            Won
                                                        </span>
                                                    @elseif(strtoupper($trade->status ?? '') === 'LOST' || strtoupper($trade->status ?? '') === 'LOSS')
                                                        <span
                                                            style="font-weight: 500; font-size: 0.85rem; color: #ef4444;">
                                                            Lost
                                                        </span>
                                                    @else
                                                        <span
                                                            style="font-weight: 500; font-size: 0.85rem; color: #f59e0b;">
                                                            Pending
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
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
                                            @php
                                                $trade = $activity['data'];
                                                // Use amount_invested (primary) or fallback to amount
                                                $amountInvested = $trade->amount_invested ?? ($trade->amount ?? 0);
                                                $profitLoss = 0;
                                                $profitLossLabel = '';

                                                $tradeStatusUpper = strtoupper($trade->status ?? 'PENDING');
                                                
                                                if ($tradeStatusUpper === 'WON' || $tradeStatusUpper === 'WIN') {
                                                    // WON: payout = token_amount * 1.00, profit = payout - amount_invested
                                                    $tokenAmount = $trade->token_amount ?? ($trade->shares ?? 0);
                                                    
                                                    // If token_amount not set, calculate from payout
                                                    if (!$tokenAmount || $tokenAmount <= 0) {
                                                        $payout = $trade->payout ?? ($trade->payout_amount ?? 0);
                                                        if ($payout > 0) {
                                                            $tokenAmount = $payout; // payout = token_amount * 1.00
                                                        } else {
                                                            // Fallback: calculate from amount and price
                                                            $priceAtBuy = $trade->price_at_buy ?? ($trade->price ?? 0.0001);
                                                            $tokenAmount = $priceAtBuy > 0 ? $amountInvested / $priceAtBuy : 0;
                                                        }
                                                    }
                                                    
                                                    // Calculate payout: token_amount * $1.00
                                                    $payout = $tokenAmount * 1.00;
                                                    
                                                    // Profit = payout - amount_invested
                                                    $profitLoss = $payout - $amountInvested;
                                                    $profitLossLabel = 'Profit';
                                                    
                                                } elseif ($tradeStatusUpper === 'LOST' || $tradeStatusUpper === 'LOSS') {
                                                    // LOST: no payout, loss = -amount_invested
                                                    $profitLoss = -$amountInvested;
                                                    $profitLossLabel = 'Loss';
                                                    
                                                } elseif ($tradeStatusUpper === 'CLOSED' && $trade->payout) {
                                                    // Closed position with payout
                                                    $payout = $trade->payout ?? ($trade->payout_amount ?? 0);
                                                    $profitLoss = $payout - $amountInvested;
                                                    $profitLossLabel = $profitLoss >= 0 ? 'Profit' : 'Loss';
                                                    
                                                } else {
                                                    // PENDING - calculate based on current market price
                                                    if ($trade->market) {
                                                        $market = $trade->market;
                                                        $outcomePrices = is_string(
                                                            $market->outcome_prices ?? ($market->outcomePrices ?? null),
                                                        )
                                                            ? json_decode(
                                                                $market->outcome_prices ?? $market->outcomePrices,
                                                                true,
                                                            )
                                                            : $market->outcome_prices ??
                                                                ($market->outcomePrices ?? [0.5, 0.5]);

                                                        // Get price at buy
                                                        $priceAtBuy = $trade->price_at_buy ?? ($trade->price ?? 0.5);
                                                        
                                                        // Get token amount
                                                        $tokenAmount = $trade->token_amount ?? ($trade->shares ?? 0);
                                                        if (!$tokenAmount || $tokenAmount <= 0) {
                                                            $tokenAmount = $priceAtBuy > 0 ? $amountInvested / $priceAtBuy : 0;
                                                        }
                                                        
                                                        // Get current price based on outcome
                                                        $outcome = strtoupper(
                                                            $trade->outcome ?? ($trade->side ?? 'YES'),
                                                        );
                                                        $currentPrice =
                                                            $outcome === 'YES' && isset($outcomePrices[1])
                                                                ? (float) $outcomePrices[1]
                                                                : ($outcome === 'NO' && isset($outcomePrices[0])
                                                                    ? (float) $outcomePrices[0]
                                                                    : $priceAtBuy);

                                                        // Current value = token_amount * current_price
                                                        $currentValue = $tokenAmount * $currentPrice;
                                                        
                                                        // Profit/Loss = current_value - amount_invested
                                                        $profitLoss = $currentValue - $amountInvested;
                                                        $profitLossLabel = $profitLoss >= 0 ? 'Profit' : 'Loss';
                                                    } else {
                                                        $profitLoss = 0;
                                                        $profitLossLabel = 'Pending';
                                                    }
                                                }
                                            @endphp
                                            <div class="activity-item"
                                                style="padding: 1rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; transition: background 0.2s; cursor: pointer;"
                                                data-activity-type="trade" data-activity-id="{{ $trade->id }}"
                                                data-amount="{{ $amountInvested }}">
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
                                                                    <a href="{{ route('market.details', $trade->market->event->slug ?? $trade->market->event->id) }}" 
                                                                       style="color: var(--text-primary); text-decoration: none; transition: color 0.2s;"
                                                                       onmouseover="this.style.color='var(--primary-color, #3b82f6)'" 
                                                                       onmouseout="this.style.color='var(--text-primary)'">
                                                                        {{ \Illuminate\Support\Str::limit($trade->market->event->title, 40) }}
                                                                    </a>
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
                                                        style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem; margin-bottom: 0.25rem;">
                                                        <span
                                                            style="font-weight: 600; font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: 4px; 
                                                            background: {{ $profitLoss >= 0 ? '#10b98120' : '#ef444420' }};
                                                            color: {{ $profitLoss >= 0 ? '#10b981' : '#ef4444' }};
                                                            border: 1px solid {{ $profitLoss >= 0 ? '#10b98140' : '#ef444440' }};
                                                        ">
                                                            {{ $profitLossLabel }}
                                                        </span>
                                                    </div>
                                                    <div
                                                        style="font-weight: 600; color: {{ $profitLoss >= 0 ? '#10b981' : '#ef4444' }}; margin-bottom: 0.25rem;">
                                                        {{ $profitLoss >= 0 ? '+' : '' }}${{ number_format($profitLoss, 2) }}
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

                        <!-- Deposits History Tab -->
                        <div class="tab-content-wrapper d-none" id="deposits-tab">
                            <div class="positions-controls">
                                <div class="search-wrapper" style="flex: 1; max-width: 500px;">
                                    <i class="fas fa-search search-icon"></i>
                                    <input type="text" class="search-input" id="depositSearchInput"
                                        placeholder="Search deposits...">
                                </div>
                            </div>

                            <div class="deposits-table-container" style="margin-top: 1.5rem;">
                                @if ($deposits->count() > 0)
                                    <!-- Desktop Table View -->
                                    <div class="deposits-table-wrapper desktop-view">
                                        <div class="table-responsive">
                                            <table class="table deposits-table"
                                                style="width: 100%; border-collapse: collapse;">
                                                <thead>
                                                    <tr
                                                        style="border-bottom: 1px solid var(--border); background: var(--bg-secondary);">
                                                        <th
                                                            style="padding: 1rem; text-align: left; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                            Date</th>
                                                        <th
                                                            style="padding: 1rem; text-align: left; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                            Amount</th>
                                                        <th
                                                            style="padding: 1rem; text-align: left; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                            Payment Method</th>
                                                        <th
                                                            style="padding: 1rem; text-align: left; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                            Transaction ID</th>
                                                        <th
                                                            style="padding: 1rem; text-align: center; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                            Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($deposits as $deposit)
                                                        <tr class="deposit-row"
                                                            style="border-bottom: 1px solid var(--border); transition: background 0.2s;"
                                                            data-amount="{{ $deposit->amount }}"
                                                            data-method="{{ strtolower($deposit->payment_method ?? '') }}"
                                                            data-status="{{ strtolower($deposit->status ?? '') }}"
                                                            data-transaction="{{ strtolower($deposit->transaction_id ?? ($deposit->merchant_trade_no ?? '')) }}">
                                                            <td style="padding: 1rem; color: var(--text-primary);">
                                                                <div style="font-weight: 500; font-size: 0.95rem;">
                                                                    {{ $deposit->created_at->format('M d, Y') }}
                                                                </div>
                                                                <div
                                                                    style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">
                                                                    {{ $deposit->created_at->format('h:i A') }}
                                                                </div>
                                                            </td>
                                                            <td
                                                                style="padding: 1rem; color: var(--text-primary); font-weight: 600; font-size: 0.95rem;">
                                                                ${{ number_format($deposit->amount, 2) }}
                                                            </td>
                                                            <td style="padding: 1rem; color: var(--text-primary);">
                                                                <span
                                                                    style="text-transform: capitalize; font-size: 0.9rem;">
                                                                    @if ($deposit->payment_method === 'binancepay')
                                                                        <i class="fas fa-coins"
                                                                            style="margin-right: 0.5rem; color: #f0b90b;"></i>Binance
                                                                        Pay
                                                                    @elseif($deposit->payment_method === 'metamask')
                                                                        <i class="fas fa-mask"
                                                                            style="margin-right: 0.5rem; color: #f6851b;"></i>MetaMask
                                                                    @elseif($deposit->payment_method === 'manual')
                                                                        <i class="fas fa-keyboard"
                                                                            style="margin-right: 0.5rem; color: var(--text-secondary);"></i>Manual
                                                                        Payment
                                                                    @else
                                                                        {{ $deposit->payment_method ?? 'N/A' }}
                                                                    @endif
                                                                </span>
                                                            </td>
                                                            <td style="padding: 1rem; color: var(--text-primary);">
                                                                <div
                                                                    style="font-size: 0.85rem; font-family: monospace; word-break: break-all; max-width: 200px;">
                                                                    {{ $deposit->transaction_id ?? ($deposit->merchant_trade_no ?? 'N/A') }}
                                                                </div>
                                                            </td>
                                                            <td style="padding: 1rem; text-align: center;">
                                                                @if ($deposit->status === 'completed')
                                                                    <span
                                                                        style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.85rem; color: #10b981;">
                                                                        <span
                                                                            style="margin-right: 0.4rem; font-size: 1rem; line-height: 1;">•</span>
                                                                        Completed
                                                                    </span>
                                                                @elseif($deposit->status === 'pending')
                                                                    <span
                                                                        style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.85rem; color: #f59e0b;">
                                                                        <span
                                                                            style="margin-right: 0.4rem; font-size: 1rem; line-height: 1;">•</span>
                                                                        Pending
                                                                    </span>
                                                                @elseif($deposit->status === 'failed')
                                                                    <span
                                                                        style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.85rem; color: #ef4444;">
                                                                        <span
                                                                            style="margin-right: 0.4rem; font-size: 1rem; line-height: 1;">•</span>
                                                                        Failed
                                                                    </span>
                                                                @elseif($deposit->status === 'expired')
                                                                    <span
                                                                        style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.85rem; color: #6b7280;">
                                                                        <span
                                                                            style="margin-right: 0.4rem; font-size: 1rem; line-height: 1;">•</span>
                                                                        Expired
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.85rem; color: var(--text-secondary);">
                                                                        <span
                                                                            style="margin-right: 0.4rem; font-size: 1rem; line-height: 1;">•</span>
                                                                        {{ ucfirst($deposit->status ?? 'N/A') }}
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Mobile Card View -->
                                    <div class="deposits-cards-container mobile-view" style="display: none;">
                                        @foreach ($deposits as $deposit)
                                            <div class="deposit-card deposit-row" data-amount="{{ $deposit->amount }}"
                                                data-method="{{ strtolower($deposit->payment_method ?? '') }}"
                                                data-status="{{ strtolower($deposit->status ?? '') }}"
                                                data-transaction="{{ strtolower($deposit->transaction_id ?? ($deposit->merchant_trade_no ?? '')) }}"
                                                style="background: var(--card-bg); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 1rem; margin-bottom: 1rem;">
                                                <!-- Header: Date and Status -->
                                                <div
                                                    style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                                    <div>
                                                        <div
                                                            style="font-weight: 600; font-size: 0.9rem; color: var(--text-primary); margin-bottom: 0.25rem;">
                                                            {{ $deposit->created_at->format('M d, Y') }}
                                                        </div>
                                                        <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                                            {{ $deposit->created_at->format('h:i A') }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        @if ($deposit->status === 'completed')
                                                            <span
                                                                style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.8rem; color: #10b981;">
                                                                <span
                                                                    style="margin-right: 0.3rem; font-size: 0.9rem; line-height: 1;">•</span>
                                                                Completed
                                                            </span>
                                                        @elseif($deposit->status === 'pending')
                                                            <span
                                                                style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.8rem; color: #f59e0b;">
                                                                <span
                                                                    style="margin-right: 0.3rem; font-size: 0.9rem; line-height: 1;">•</span>
                                                                Pending
                                                            </span>
                                                        @elseif($deposit->status === 'failed')
                                                            <span
                                                                style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.8rem; color: #ef4444;">
                                                                <span
                                                                    style="margin-right: 0.3rem; font-size: 0.9rem; line-height: 1;">•</span>
                                                                Failed
                                                            </span>
                                                        @elseif($deposit->status === 'expired')
                                                            <span
                                                                style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.8rem; color: #6b7280;">
                                                                <span
                                                                    style="margin-right: 0.3rem; font-size: 0.9rem; line-height: 1;">•</span>
                                                                Expired
                                                            </span>
                                                        @else
                                                            <span
                                                                style="display: inline-flex; align-items: center; font-weight: 500; font-size: 0.8rem; color: var(--text-secondary);">
                                                                <span
                                                                    style="margin-right: 0.3rem; font-size: 0.9rem; line-height: 1;">•</span>
                                                                {{ ucfirst($deposit->status ?? 'N/A') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Amount -->
                                                <div style="margin-bottom: 1rem;">
                                                    <div
                                                        style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Amount
                                                    </div>
                                                    <div
                                                        style="font-weight: 700; font-size: 1.25rem; color: var(--text-primary);">
                                                        ${{ number_format($deposit->amount, 2) }}
                                                    </div>
                                                </div>

                                                <!-- Payment Method -->
                                                <div style="margin-bottom: 1rem;">
                                                    <div
                                                        style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Payment Method
                                                    </div>
                                                    <div style="font-size: 0.9rem; color: var(--text-primary);">
                                                        @if ($deposit->payment_method === 'binancepay')
                                                            <i class="fas fa-coins"
                                                                style="margin-right: 0.5rem; color: #f0b90b;"></i>Binance
                                                            Pay
                                                        @elseif($deposit->payment_method === 'metamask')
                                                            <i class="fas fa-mask"
                                                                style="margin-right: 0.5rem; color: #f6851b;"></i>MetaMask
                                                        @elseif($deposit->payment_method === 'manual')
                                                            <i class="fas fa-keyboard"
                                                                style="margin-right: 0.5rem; color: var(--text-secondary);"></i>Manual
                                                            Payment
                                                        @else
                                                            {{ $deposit->payment_method ?? 'N/A' }}
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Transaction ID -->
                                                <div style="padding-top: 1rem; border-top: 1px solid var(--border);">
                                                    <div
                                                        style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Transaction ID
                                                    </div>
                                                    <div
                                                        style="font-size: 0.8rem; font-family: monospace; word-break: break-all; color: var(--text-primary);">
                                                        {{ $deposit->transaction_id ?? ($deposit->merchant_trade_no ?? 'N/A') }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="no-deposits-message" style="padding: 3rem; text-align: center;">
                                        <i class="fas fa-arrow-down"
                                            style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem; opacity: 0.5;"></i>
                                        <h4 style="color: var(--text-primary); margin-bottom: 0.5rem;">No Deposits Yet</h4>
                                        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">You haven't made
                                            any deposits yet.</p>
                                        <button
                                            onclick="if(typeof openDepositModal === 'function') { openDepositModal(); } else if(typeof window.openDepositModal === 'function') { window.openDepositModal(); }"
                                            style="background: #3b82f6; color: #ffffff; border: none; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 600; font-size: 0.875rem; cursor: pointer; transition: all 0.2s;"
                                            onmouseover="this.style.background='#2563eb'"
                                            onmouseout="this.style.background='#3b82f6'">
                                            <i class="fas fa-arrow-down"></i> Make Your First Deposit
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Withdrawal Requests Tab -->
                        <div class="tab-content-wrapper d-none" id="withdrawals-tab">
                            <div class="positions-controls">
                                <div class="search-wrapper" style="flex: 1; max-width: 500px;">
                                    <i class="fas fa-search search-icon"></i>
                                    <input type="text" class="search-input" id="withdrawalSearchInput"
                                        placeholder="Search by amount or transaction ID">
                                </div>
                                <div class="wallet-list-wrapper d-flex align-items-center gap-2">
                                    <button type="button" onclick="openWalletListModal()"
                                        style="padding: 10px 16px; background: var(--secondary); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 8px; margin-right: 12px;"
                                        onmouseover="this.style.background='var(--hover)'; this.style.borderColor='var(--accent)'"
                                        onmouseout="this.style.background='var(--secondary)'; this.style.borderColor='var(--border)'">
                                        <i class="fas fa-wallet"></i> Wallets
                                    </button>
                                    <div class="filter-dropdown-wrapper">
                                        <button type="button" class="filter-dropdown-btn"
                                            id="withdrawalStatusFilterBtn">
                                            <span>All Status</span>
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                        <div class="filter-dropdown-menu" id="withdrawalStatusFilterMenu">
                                            <a href="#" class="filter-dropdown-item active" data-status="all">All
                                                Status</a>
                                            <a href="#" class="filter-dropdown-item"
                                                data-status="pending">Pending</a>
                                            <a href="#" class="filter-dropdown-item"
                                                data-status="approved">Approved</a>
                                            <a href="#" class="filter-dropdown-item"
                                                data-status="completed">Completed</a>
                                            <a href="#" class="filter-dropdown-item"
                                                data-status="rejected">Rejected</a>
                                        </div>
                                    </div>
                                </div>
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

                                </div>
                            @endif
                        </div>

                        <!-- Referral Tab Content -->
                        <div class="tab-content-wrapper d-none" id="referral-tab">
                            <div style="padding: 2rem; background: var(--card-bg); border-radius: 12px;">
                                <h3 style="font-size: 1.5rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-users" style="color: #ffb11a;"></i>
                                    Referral Program
                                </h3>

                                <!-- Referral Link Section -->
                                <div style="background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.75rem;">
                                        Your Referral Link
                                    </label>
                                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                                        <input type="text" id="referralLinkInput" readonly 
                                            value="{{ $referralLink }}"
                                            style="flex: 1; padding: 0.75rem; background: var(--card-bg); border: 1px solid var(--border); border-radius: 6px; color: var(--text-primary); font-size: 0.875rem; font-family: monospace;">
                                        <button type="button" id="copyReferralLinkBtn" 
                                            onclick="copyReferralLink()"
                                            style="padding: 0.75rem; background: #ffb11a; color: #000; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; justify-content: center; width: 45px; height: 45px;"
                                            onmouseover="if(this.style.background !== 'rgb(16, 185, 129)') this.style.background='#e6a017'"
                                            onmouseout="if(this.style.background !== 'rgb(16, 185, 129)') this.style.background='#ffb11a'"
                                            title="Copy referral link">
                                            <i class="fas fa-copy" id="copyIcon"></i>
                                        </button>
                                    </div>
                                    <p style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.5rem;">
                                        Share this link with friends and earn commissions when they deposit!
                                    </p>
                                </div>

                                <!-- Referral Stats -->
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                                    <div style="background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 8px; padding: 1.25rem;">
                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Total Referrals
                                        </div>
                                        <div style="font-size: 1.75rem; font-weight: 700; color: var(--text-primary);">
                                            {{ $referralStats['total_referrals'] ?? 0 }}
                                        </div>
                                    </div>
                                    <div style="background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 8px; padding: 1.25rem;">
                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Total Commissions
                                        </div>
                                        <div style="font-size: 1.75rem; font-weight: 700; color: #10b981;">
                                            ${{ number_format($referralStats['total_commissions'] ?? 0, 2) }}
                                        </div>
                                    </div>
                                    <div style="background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 8px; padding: 1.25rem;">
                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Level 1 Commissions
                                        </div>
                                        <div style="font-size: 1.75rem; font-weight: 700; color: var(--text-primary);">
                                            ${{ number_format($referralStats['level_1_commissions'] ?? 0, 2) }}
                                        </div>
                                    </div>
                                    <div style="background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 8px; padding: 1.25rem;">
                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Level 2 Commissions
                                        </div>
                                        <div style="font-size: 1.75rem; font-weight: 700; color: var(--text-primary);">
                                            ${{ number_format($referralStats['level_2_commissions'] ?? 0, 2) }}
                                        </div>
                                    </div>
                                    <div style="background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 8px; padding: 1.25rem;">
                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Level 3 Commissions
                                        </div>
                                        <div style="font-size: 1.75rem; font-weight: 700; color: var(--text-primary);">
                                            ${{ number_format($referralStats['level_3_commissions'] ?? 0, 2) }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Commissions -->
                                @if(!empty($referralStats['recent_commissions']))
                                <div>
                                    <h4 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">
                                        Recent Commissions
                                    </h4>
                                    <div style="background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 8px; overflow: hidden;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <thead>
                                                <tr style="background: var(--card-bg); border-bottom: 1px solid var(--border);">
                                                    <th style="padding: 0.75rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Date</th>
                                                    <th style="padding: 0.75rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Source</th>
                                                    <th style="padding: 0.75rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Level</th>
                                                    <th style="padding: 0.75rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($referralStats['recent_commissions'] as $commission)
                                                <tr style="border-bottom: 1px solid var(--border);">
                                                    <td style="padding: 0.75rem; font-size: 0.875rem; color: var(--text-primary);">
                                                        {{ \Carbon\Carbon::parse($commission['created_at'])->format('M d, Y') }}
                                                    </td>
                                                    <td style="padding: 0.75rem; font-size: 0.875rem; color: var(--text-primary);">
                                                        {{ $commission['source_user'] }}
                                                    </td>
                                                    <td style="padding: 0.75rem; font-size: 0.875rem; color: var(--text-primary);">
                                                        <span style="background: rgba(255, 177, 26, 0.15); color: #ffb11a; padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: 600;">
                                                            Level {{ $commission['level'] }}
                                                        </span>
                                                    </td>
                                                    <td style="padding: 0.75rem; font-size: 0.875rem; color: #10b981; font-weight: 600; text-align: right;">
                                                        +${{ number_format($commission['amount'], 2) }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @else
                                <div style="text-align: center; padding: 2rem; background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 8px;">
                                    <i class="fas fa-inbox" style="font-size: 2rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                                    <p style="color: var(--text-secondary);">No commissions yet. Start referring friends to earn!</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Settings Tab Content -->
                        <div class="tab-content-wrapper d-none" id="settings-tab">
                            @if (!$hasWithdrawalPassword)
                                <div
                                    class="p-4 mb-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md">
                                    <p class="text-sm text-yellow-800 dark:text-yellow-200 mb-2">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <strong>Withdrawal password not set.</strong> Please set your withdrawal password
                                        before making withdrawals.
                                    </p>
                                    <a href="{{ route('withdrawal-settings.edit') }}"
                                        class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md font-semibold text-sm hover:bg-yellow-700 transition">
                                        Set Withdrawal Password
                                    </a>
                                </div>
                            @endif
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
                                            <label for="username" class="form-label"
                                                style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">Username</label>
                                            <input type="text" class="form-control" id="username" name="username"
                                                placeholder="Enter your username" value="{{ $user->username ?? '' }}"
                                                style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--card-bg); color: var(--text-primary);">
                                        </div>

                                        <div class="form-field">
                                            <label for="email" class="form-label"
                                                style="display:block;font-weight:500;color:var(--text-primary);margin-bottom:0.5rem;">
                                                Email </label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                placeholder="Enter your email" value="{{ $user->email }}"
                                                {{ $user->email ? 'readonly' : '' }}
                                                style="width:100%;padding:0.75rem;border:1px solid var(--border);border-radius:6px; background:var(--card-bg);color:var(--text-primary);  {{ $user->email ? 'cursor:not-allowed;opacity:0.8;' : '' }}">
                                        </div>

                                        <div class="form-field">
                                            <label for="number" class="form-label"
                                                style="display:block;font-weight:500;color:var(--text-primary);margin-bottom:0.5rem;">
                                                Phone Number
                                            </label>
                                            <input type="text" class="form-control" id="number" name="number"
                                                placeholder="Enter your phone number" value="{{ $user->number ?? '' }}"
                                                {{ !empty($user->number) ? 'readonly' : '' }}
                                                style="width:100%;padding:0.75rem;border:1px solid var(--border);border-radius:6px;  background:var(--card-bg);color:var(--text-primary); {{ !empty($user->number) ? 'cursor:not-allowed;opacity:0.8;' : '' }}">
                                        </div>

                                    </div>
                                    <!-- Save Button -->
                                    <div class="profile-save-section mt-4"
                                        style="display: flex; justify-content: flex-end; gap: 1rem; flex-wrap: wrap;">
                                        <button type="button" onclick="openPasswordModal()" class="btn-save-changes">
                                            Change Password
                                        </button>
                                        <button type="submit" id="saveProfileBtn" class="btn-save-changes"
                                            style="padding: 0.875rem 2rem; background: linear-gradient(135deg, #ffb11a 0%, #ff9500 100%); color: #000; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(255, 177, 26, 0.3);"
                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(255, 177, 26, 0.4)'; this.style.background='linear-gradient(135deg, #ff9500 0%, #ffb11a 100%)'"
                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(255, 177, 26, 0.3)'; this.style.background='linear-gradient(135deg, #ffb11a 0%, #ff9500 100%)'">
                                            <i class="fas fa-save"></i> Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="settings-card"
                                style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 2rem; margin-top: 2rem;">
                                <h3 class="card-title"
                                    style="font-size: 1.5rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.5rem;">
                                    ID Verification
                                </h3>

                                @if ($kycVerification)
                                    <!-- Submitted KYC Details View - Table Format -->
                                    <div class="kyc-details-container">
                                        <!-- Desktop Table View -->
                                        <div class="kyc-table-wrapper desktop-view">
                                            <table class="kyc-details-table" style="width: 100%; border-collapse: collapse;">
                                                <tbody>
                                                    <tr style="border-bottom: 1px solid var(--border);">
                                                        <td style="padding: 1rem; width: 200px; font-weight: 600; color: var(--text-secondary); font-size: 0.9rem; vertical-align: top;">
                                                            ID Type
                                                        </td>
                                                        <td style="padding: 1rem; color: var(--text-primary); font-weight: 500;">
                                                            {{ $kycVerification->id_type }}
                                                        </td>
                                                    </tr>
                                                    <tr style="border-bottom: 1px solid var(--border);">
                                                        <td style="padding: 1rem; font-weight: 600; color: var(--text-secondary); font-size: 0.9rem; vertical-align: top;">
                                                            Status
                                                        </td>
                                                        <td style="padding: 1rem;">
                                                            <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 6px; background: {{ $kycVerification->status === 'approved' ? 'rgba(16, 185, 129, 0.2)' : ($kycVerification->status === 'rejected' ? 'rgba(239, 68, 68, 0.2)' : 'rgba(245, 158, 11, 0.2)') }}; color: {{ $kycVerification->status === 'approved' ? '#10b981' : ($kycVerification->status === 'rejected' ? '#ef4444' : '#f59e0b') }}; font-weight: 600; font-size: 0.9rem;">
                                                                {{ ucfirst($kycVerification->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    
                                                    @if ($kycVerification->id_type === 'NID')
                                                        @if ($kycVerification->nid_front_photo || $kycVerification->nid_back_photo)
                                                            <tr style="border-bottom: 1px solid var(--border);">
                                                                <td style="padding: 1rem; font-weight: 600; color: var(--text-secondary); font-size: 0.9rem; vertical-align: top;">
                                                                    Photos
                                                                </td>
                                                                <td style="padding: 1rem;">
                                                                    <div style="display: flex; flex-direction: row; gap: 1rem; flex-wrap: wrap;">
                                                                        @if ($kycVerification->nid_front_photo)
                                                                            <div style="flex: 1; min-width: 150px;">
                                                                                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Front Photo</p>
                                                                                <img src="{{ asset('storage/' . $kycVerification->nid_front_photo) }}" alt="NID Front" style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid var(--border); object-fit: contain; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                                                            </div>
                                                                        @endif
                                                                        @if ($kycVerification->nid_back_photo)
                                                                            <div style="flex: 1; min-width: 150px;">
                                                                                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Back Photo</p>
                                                                                <img src="{{ asset('storage/' . $kycVerification->nid_back_photo) }}" alt="NID Back" style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid var(--border); object-fit: contain; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @elseif($kycVerification->id_type === 'Driving License')
                                                        @if ($kycVerification->full_name)
                                                            <tr style="border-bottom: 1px solid var(--border);">
                                                                <td style="padding: 1rem; font-weight: 600; color: var(--text-secondary); font-size: 0.9rem; vertical-align: top;">
                                                                    Full Name
                                                                </td>
                                                                <td style="padding: 1rem; color: var(--text-primary); font-weight: 500;">
                                                                    {{ $kycVerification->full_name }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if ($kycVerification->license_number)
                                                            <tr style="border-bottom: 1px solid var(--border);">
                                                                <td style="padding: 1rem; font-weight: 600; color: var(--text-secondary); font-size: 0.9rem; vertical-align: top;">
                                                                    License Number
                                                                </td>
                                                                <td style="padding: 1rem; color: var(--text-primary); font-weight: 500;">
                                                                    {{ $kycVerification->license_number }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if ($kycVerification->dob)
                                                            <tr style="border-bottom: 1px solid var(--border);">
                                                                <td style="padding: 1rem; font-weight: 600; color: var(--text-secondary); font-size: 0.9rem; vertical-align: top;">
                                                                    Date of Birth
                                                                </td>
                                                                <td style="padding: 1rem; color: var(--text-primary); font-weight: 500;">
                                                                    {{ $kycVerification->dob->format('d M, Y') }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if ($kycVerification->license_front_photo)
                                                            <tr style="border-bottom: 1px solid var(--border);">
                                                                <td style="padding: 1rem; font-weight: 600; color: var(--text-secondary); font-size: 0.9rem; vertical-align: top;">
                                                                    Front Page Photo
                                                                </td>
                                                                <td style="padding: 1rem;">
                                                                    <img src="{{ asset('storage/' . $kycVerification->license_front_photo) }}" alt="License Front" style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid var(--border); object-fit: contain; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @elseif($kycVerification->id_type === 'Passport')
                                                        @if ($kycVerification->full_name)
                                                            <tr style="border-bottom: 1px solid var(--border);">
                                                                <td style="padding: 1rem; font-weight: 600; color: var(--text-secondary); font-size: 0.9rem; vertical-align: top;">
                                                                    Full Name
                                                                </td>
                                                                <td style="padding: 1rem; color: var(--text-primary); font-weight: 500;">
                                                                    {{ $kycVerification->full_name }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if ($kycVerification->passport_number)
                                                            <tr style="border-bottom: 1px solid var(--border);">
                                                                <td style="padding: 1rem; font-weight: 600; color: var(--text-secondary); font-size: 0.9rem; vertical-align: top;">
                                                                    Passport Number
                                                                </td>
                                                                <td style="padding: 1rem; color: var(--text-primary); font-weight: 500;">
                                                                    {{ $kycVerification->passport_number }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if ($kycVerification->passport_expiry_date)
                                                            <tr style="border-bottom: 1px solid var(--border);">
                                                                <td style="padding: 1rem; font-weight: 600; color: var(--text-secondary); font-size: 0.9rem; vertical-align: top;">
                                                                    Expiry Date
                                                                </td>
                                                                <td style="padding: 1rem; color: var(--text-primary); font-weight: 500;">
                                                                    {{ $kycVerification->passport_expiry_date->format('d M, Y') }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if ($kycVerification->passport_biodata_photo || $kycVerification->passport_cover_photo)
                                                            <tr style="border-bottom: 1px solid var(--border);">
                                                                <td style="padding: 1rem; font-weight: 600; color: var(--text-secondary); font-size: 0.9rem; vertical-align: top;">
                                                                    Photos
                                                                </td>
                                                                <td style="padding: 1rem;">
                                                                    <div style="display: flex; flex-direction: row; gap: 1rem; flex-wrap: wrap;">
                                                                        @if ($kycVerification->passport_biodata_photo)
                                                                            <div style="flex: 1; min-width: 150px;">
                                                                                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Biodata Page Photo</p>
                                                                                <img src="{{ asset('storage/' . $kycVerification->passport_biodata_photo) }}" alt="Passport Biodata" style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid var(--border); object-fit: contain; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                                                            </div>
                                                                        @endif
                                                                        @if ($kycVerification->passport_cover_photo)
                                                                            <div style="flex: 1; min-width: 150px;">
                                                                                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Cover Page Photo</p>
                                                                                <img src="{{ asset('storage/' . $kycVerification->passport_cover_photo) }}" alt="Passport Cover" style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid var(--border); object-fit: contain; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Mobile Card View -->
                                        <div class="kyc-cards-container mobile-view" style="display: none;">
                                            <div class="kyc-card-item" style="padding: 1rem; background: var(--secondary); border: 1px solid var(--border); border-radius: 8px; margin-bottom: 0.75rem;">
                                                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem; font-weight: 600;">ID Type</div>
                                                <div style="font-size: 0.95rem; color: var(--text-primary); font-weight: 500;">{{ $kycVerification->id_type }}</div>
                                            </div>
                                            <div class="kyc-card-item" style="padding: 1rem; background: var(--secondary); border: 1px solid var(--border); border-radius: 8px; margin-bottom: 0.75rem;">
                                                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem; font-weight: 600;">Status</div>
                                                <div>
                                                    <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 6px; background: {{ $kycVerification->status === 'approved' ? 'rgba(16, 185, 129, 0.2)' : ($kycVerification->status === 'rejected' ? 'rgba(239, 68, 68, 0.2)' : 'rgba(245, 158, 11, 0.2)') }}; color: {{ $kycVerification->status === 'approved' ? '#10b981' : ($kycVerification->status === 'rejected' ? '#ef4444' : '#f59e0b') }}; font-weight: 600; font-size: 0.9rem;">
                                                        {{ ucfirst($kycVerification->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            @if ($kycVerification->id_type === 'NID')
                                                @if ($kycVerification->nid_front_photo || $kycVerification->nid_back_photo)
                                                    <div class="kyc-card-item" style="padding: 1rem; background: var(--secondary); border: 1px solid var(--border); border-radius: 8px; margin-bottom: 0.75rem;">
                                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.75rem; font-weight: 600;">Photos</div>
                                                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                                                            @if ($kycVerification->nid_front_photo)
                                                                <div>
                                                                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Front Photo</p>
                                                                    <img src="{{ asset('storage/' . $kycVerification->nid_front_photo) }}" alt="NID Front" style="width: 100%; max-width: 100%; height: auto; border-radius: 8px; border: 1px solid var(--border); object-fit: contain; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                                                </div>
                                                            @endif
                                                            @if ($kycVerification->nid_back_photo)
                                                                <div>
                                                                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Back Photo</p>
                                                                    <img src="{{ asset('storage/' . $kycVerification->nid_back_photo) }}" alt="NID Back" style="width: 100%; max-width: 100%; height: auto; border-radius: 8px; border: 1px solid var(--border); object-fit: contain; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            @elseif($kycVerification->id_type === 'Driving License')
                                                @if ($kycVerification->full_name)
                                                    <div class="kyc-card-item" style="padding: 1rem; background: var(--secondary); border: 1px solid var(--border); border-radius: 8px; margin-bottom: 0.75rem;">
                                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem; font-weight: 600;">Full Name</div>
                                                        <div style="font-size: 0.95rem; color: var(--text-primary); font-weight: 500;">{{ $kycVerification->full_name }}</div>
                                                    </div>
                                                @endif
                                                @if ($kycVerification->license_number)
                                                    <div class="kyc-card-item" style="padding: 1rem; background: var(--secondary); border: 1px solid var(--border); border-radius: 8px; margin-bottom: 0.75rem;">
                                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem; font-weight: 600;">License Number</div>
                                                        <div style="font-size: 0.95rem; color: var(--text-primary); font-weight: 500;">{{ $kycVerification->license_number }}</div>
                                                    </div>
                                                @endif
                                                @if ($kycVerification->dob)
                                                    <div class="kyc-card-item" style="padding: 1rem; background: var(--secondary); border: 1px solid var(--border); border-radius: 8px; margin-bottom: 0.75rem;">
                                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem; font-weight: 600;">Date of Birth</div>
                                                        <div style="font-size: 0.95rem; color: var(--text-primary); font-weight: 500;">{{ $kycVerification->dob->format('d M, Y') }}</div>
                                                    </div>
                                                @endif
                                                @if ($kycVerification->license_front_photo)
                                                    <div class="kyc-card-item" style="padding: 1rem; background: var(--secondary); border: 1px solid var(--border); border-radius: 8px; margin-bottom: 0.75rem;">
                                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.75rem; font-weight: 600;">Front Page Photo</div>
                                                        <img src="{{ asset('storage/' . $kycVerification->license_front_photo) }}" alt="License Front" style="width: 100%; max-width: 100%; height: auto; border-radius: 8px; border: 1px solid var(--border); object-fit: contain; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                                    </div>
                                                @endif
                                            @elseif($kycVerification->id_type === 'Passport')
                                                @if ($kycVerification->full_name)
                                                    <div class="kyc-card-item" style="padding: 1rem; background: var(--secondary); border: 1px solid var(--border); border-radius: 8px; margin-bottom: 0.75rem;">
                                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem; font-weight: 600;">Full Name</div>
                                                        <div style="font-size: 0.95rem; color: var(--text-primary); font-weight: 500;">{{ $kycVerification->full_name }}</div>
                                                    </div>
                                                @endif
                                                @if ($kycVerification->passport_number)
                                                    <div class="kyc-card-item" style="padding: 1rem; background: var(--secondary); border: 1px solid var(--border); border-radius: 8px; margin-bottom: 0.75rem;">
                                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem; font-weight: 600;">Passport Number</div>
                                                        <div style="font-size: 0.95rem; color: var(--text-primary); font-weight: 500;">{{ $kycVerification->passport_number }}</div>
                                                    </div>
                                                @endif
                                                @if ($kycVerification->passport_expiry_date)
                                                    <div class="kyc-card-item" style="padding: 1rem; background: var(--secondary); border: 1px solid var(--border); border-radius: 8px; margin-bottom: 0.75rem;">
                                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem; font-weight: 600;">Expiry Date</div>
                                                        <div style="font-size: 0.95rem; color: var(--text-primary); font-weight: 500;">{{ $kycVerification->passport_expiry_date->format('d M, Y') }}</div>
                                                    </div>
                                                @endif
                                                @if ($kycVerification->passport_biodata_photo || $kycVerification->passport_cover_photo)
                                                    <div class="kyc-card-item" style="padding: 1rem; background: var(--secondary); border: 1px solid var(--border); border-radius: 8px; margin-bottom: 0.75rem;">
                                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.75rem; font-weight: 600;">Photos</div>
                                                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                                                            @if ($kycVerification->passport_biodata_photo)
                                                                <div>
                                                                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Biodata Page Photo</p>
                                                                    <img src="{{ asset('storage/' . $kycVerification->passport_biodata_photo) }}" alt="Passport Biodata" style="width: 100%; max-width: 100%; height: auto; border-radius: 8px; border: 1px solid var(--border); object-fit: contain; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                                                </div>
                                                            @endif
                                                            @if ($kycVerification->passport_cover_photo)
                                                                <div>
                                                                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Cover Page Photo</p>
                                                                    <img src="{{ asset('storage/' . $kycVerification->passport_cover_photo) }}" alt="Passport Cover" style="width: 100%; max-width: 100%; height: auto; border-radius: 8px; border: 1px solid var(--border); object-fit: contain; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <style>
                                        /* KYC Details Table Responsive Styles */
                                        .kyc-table-wrapper.desktop-view {
                                            display: block;
                                        }

                                        .kyc-cards-container.mobile-view {
                                            display: none;
                                        }

                                        .kyc-details-table {
                                            background: var(--card-bg);
                                        }

                                        .kyc-details-table tr:hover {
                                            background: var(--secondary);
                                        }

                                        /* Mobile Responsive - Show Cards, Hide Table */
                                        @media (max-width: 768px) {
                                            .kyc-table-wrapper.desktop-view {
                                                display: none !important;
                                            }

                                            .kyc-cards-container.mobile-view {
                                                display: block !important;
                                            }
                                        }

                                        @media (min-width: 769px) {
                                            .kyc-table-wrapper.desktop-view {
                                                display: block !important;
                                            }

                                            .kyc-cards-container.mobile-view {
                                                display: none !important;
                                            }
                                        }
                                    </style>
                                @else
                                    <!-- KYC Submission Form -->
                                    <form id="idVerificationForm" method="POST"
                                        action="{{ route('profile.id-verification') }}" enctype="multipart/form-data">
                                        @csrf

                                        <div class="form-field" style="margin-bottom: 1.5rem;">
                                            <label for="id_verification_type" class="form-label"
                                                style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                                ID Type <span style="color: #ef4444;">*</span>
                                            </label>
                                            <select class="form-control" id="id_verification_type"
                                                name="id_verification_type" required
                                                {{ $kycVerification ? 'disabled' : '' }}
                                                style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--card-bg); color: var(--text-primary); {{ $kycVerification ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                                                <option value="">Select ID Type</option>
                                                <option value="nid"
                                                    {{ $kycVerification && $kycVerification->id_type === 'NID' ? 'selected' : '' }}>
                                                    NID</option>
                                                <option value="driving_license"
                                                    {{ $kycVerification && $kycVerification->id_type === 'Driving License' ? 'selected' : '' }}>
                                                    Driving License</option>
                                                <option value="passport"
                                                    {{ $kycVerification && $kycVerification->id_type === 'Passport' ? 'selected' : '' }}>
                                                    Passport</option>
                                            </select>
                                            @if ($kycVerification)
                                                <p
                                                    style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.5rem;">
                                                    Status: <span
                                                        style="font-weight: 600; color: {{ $kycVerification->status === 'approved' ? '#10b981' : ($kycVerification->status === 'rejected' ? '#ef4444' : '#f59e0b') }};">{{ ucfirst($kycVerification->status) }}</span>
                                                </p>
                                            @endif
                                            <div id="error_id_verification_type"
                                                style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: none;">
                                            </div>
                                        </div>

                                        <!-- NID Fields -->
                                        <div id="nid_fields" style="display: none;">
                                            @if ($kycVerification && $kycVerification->id_type === 'NID' && $kycVerification->nid_front_photo)
                                                <div class="form-field" style="margin-bottom: 1.5rem;">
                                                    <div
                                                        style="display: flex; flex-direction: row; gap: 1rem; flex-wrap: wrap;">
                                                        <div style="flex: 1; min-width: 150px;">
                                                            <label class="form-label"
                                                                style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                                                Front Photo
                                                            </label>
                                                            <img src="{{ asset('storage/' . $kycVerification->nid_front_photo) }}"
                                                                alt="NID Front"
                                                                style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid var(--border); object-fit: contain;">
                                                        </div>
                                                        @if ($kycVerification->nid_back_photo)
                                                            <div style="flex: 1; min-width: 150px;">
                                                                <label class="form-label"
                                                                    style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                                                    Back Photo
                                                                </label>
                                                                <img src="{{ asset('storage/' . $kycVerification->nid_back_photo) }}"
                                                                    alt="NID Back"
                                                                    style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid var(--border); object-fit: contain;">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="form-field" style="margin-bottom: 1.5rem;">
                                                    <label for="nid_front_photo" class="form-label"
                                                        style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                                        Front Photo <span style="color: #ef4444;">*</span>
                                                    </label>
                                                    <input type="file" class="form-control" id="nid_front_photo"
                                                        name="nid_front_photo" accept="image/jpeg,image/png"
                                                        onchange="previewImage(this, 'nid_front_preview')"
                                                        {{ $kycVerification ? 'disabled' : '' }}
                                                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--card-bg); color: var(--text-primary); {{ $kycVerification ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                                                    <div id="nid_front_preview" style="margin-top: 0.5rem;"></div>
                                                    <div id="error_nid_front_photo"
                                                        style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: none;">
                                                    </div>
                                                </div>
                                                <div class="form-field" style="margin-bottom: 1.5rem;">
                                                    <label for="nid_back_photo" class="form-label"
                                                        style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                                        Back Photo <span style="color: #ef4444;">*</span>
                                                    </label>
                                                    <input type="file" class="form-control" id="nid_back_photo"
                                                        name="nid_back_photo" accept="image/jpeg,image/png"
                                                        onchange="previewImage(this, 'nid_back_preview')"
                                                        {{ $kycVerification ? 'disabled' : '' }}
                                                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--card-bg); color: var(--text-primary); {{ $kycVerification ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                                                    <div id="nid_back_preview" style="margin-top: 0.5rem;"></div>
                                                    <div id="error_nid_back_photo"
                                                        style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: none;">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Driving License Fields -->
                                        <div id="driving_license_fields" style="display: none;">

                                            <div class="form-field" style="margin-bottom: 1.5rem;">
                                                <label for="id_full_name_dl" class="form-label"
                                                    style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                                    Full Name <span style="color: #ef4444;">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="id_full_name_dl"
                                                    name="id_full_name" placeholder="Enter full name"
                                                    value="{{ $kycVerification ? $kycVerification->full_name : '' }}"
                                                    {{ $kycVerification ? 'readonly' : '' }}
                                                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--card-bg); color: var(--text-primary); {{ $kycVerification ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                                                <div id="error_id_full_name"
                                                    style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: none;">
                                                </div>
                                            </div>

                                            <div class="form-field" style="margin-bottom: 1.5rem;">
                                                <label for="id_license_number" class="form-label"
                                                    style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                                    License Number <span style="color: #ef4444;">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="id_license_number"
                                                    name="id_license_number" placeholder="Enter license number"
                                                    value="{{ $kycVerification && $kycVerification->id_type === 'Driving License' ? $kycVerification->license_number : '' }}"
                                                    {{ $kycVerification ? 'readonly' : '' }}
                                                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--card-bg); color: var(--text-primary); {{ $kycVerification ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                                                <div id="error_id_license_number"
                                                    style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: none;">
                                                </div>
                                            </div>
                                            <div class="form-field" style="margin-bottom: 1.5rem;">
                                                <label for="id_date_of_birth_dl" class="form-label"
                                                    style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                                    Date of Birth <span style="color: #ef4444;">*</span>
                                                </label>
                                                <input type="date" class="form-control" id="id_date_of_birth_dl"
                                                    name="id_date_of_birth"
                                                    value="{{ $kycVerification && $kycVerification->dob ? $kycVerification->dob->format('Y-m-d') : '' }}"
                                                    {{ $kycVerification ? 'readonly' : '' }}
                                                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--card-bg); color: var(--text-primary); {{ $kycVerification ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                                                <div id="error_id_date_of_birth"
                                                    style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: none;">
                                                </div>
                                            </div>
                                            @if ($kycVerification && $kycVerification->id_type === 'Driving License' && $kycVerification->license_front_photo)
                                                <div class="form-field" style="margin-bottom: 1.5rem;">
                                                    <label class="form-label"
                                                        style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                                        Front Page Photo
                                                    </label>
                                                    <img src="{{ asset('storage/' . $kycVerification->license_front_photo) }}"
                                                        alt="License Front"
                                                        style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid var(--border); object-fit: contain;">
                                                </div>
                                            @else
                                                <div class="form-field" style="margin-bottom: 1.5rem;">
                                                    <label for="dl_front_photo" class="form-label"
                                                        style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                                        Front Page Photo <span style="color: #ef4444;">*</span>
                                                    </label>
                                                    <input type="file" class="form-control" id="dl_front_photo"
                                                        name="dl_front_photo" accept="image/jpeg,image/png"
                                                        onchange="previewImage(this, 'dl_front_preview')"
                                                        {{ $kycVerification ? 'disabled' : '' }}
                                                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--card-bg); color: var(--text-primary); {{ $kycVerification ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                                                    <div id="dl_front_preview" style="margin-top: 0.5rem;"></div>
                                                    <div id="error_dl_front_photo"
                                                        style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: none;">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Passport Fields -->
                                        <div id="passport_fields" style="display: none;">

                                            <div class="form-field" style="margin-bottom: 1.5rem;">
                                                <label for="id_full_name_pp" class="form-label"
                                                    style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                                    Full Name <span style="color: #ef4444;">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="id_full_name_pp"
                                                    name="id_full_name" placeholder="Enter full name"
                                                    value="{{ $kycVerification ? $kycVerification->full_name : '' }}"
                                                    {{ $kycVerification ? 'readonly' : '' }}
                                                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--card-bg); color: var(--text-primary); {{ $kycVerification ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                                                <div id="error_id_full_name_pp"
                                                    style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: none;">
                                                </div>
                                            </div>
                                            <div class="form-field" style="margin-bottom: 1.5rem;">
                                                <label for="id_passport_number" class="form-label"
                                                    style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                                    Passport Number <span style="color: #ef4444;">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="id_passport_number"
                                                    name="id_passport_number" placeholder="Enter passport number"
                                                    value="{{ $kycVerification && $kycVerification->id_type === 'Passport' ? $kycVerification->passport_number : '' }}"
                                                    {{ $kycVerification ? 'readonly' : '' }}
                                                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--card-bg); color: var(--text-primary); {{ $kycVerification ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                                                <div id="error_id_passport_number"
                                                    style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: none;">
                                                </div>
                                            </div>
                                            <div class="form-field" style="margin-bottom: 1.5rem;">
                                                <label for="id_passport_expiry_date" class="form-label"
                                                    style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                                    Expiry Date <span style="color: #ef4444;">*</span>
                                                </label>
                                                <input type="date" class="form-control" id="id_passport_expiry_date"
                                                    name="id_passport_expiry_date"
                                                    value="{{ $kycVerification && $kycVerification->passport_expiry_date ? $kycVerification->passport_expiry_date->format('Y-m-d') : '' }}"
                                                    min="{{ now()->addMonth()->format('Y-m-d') }}"
                                                    {{ $kycVerification ? 'readonly' : '' }}
                                                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--card-bg); color: var(--text-primary); {{ $kycVerification ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                                                <small
                                                    style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem; display: block;">
                                                    Expiry date must be at least 1 month from today
                                                </small>
                                                <div id="error_id_passport_expiry_date"
                                                    style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: none;">
                                                </div>
                                            </div>
                                            @if (
                                                $kycVerification &&
                                                    $kycVerification->id_type === 'Passport' &&
                                                    ($kycVerification->passport_biodata_photo || $kycVerification->passport_cover_photo))
                                                <div class="form-field" style="margin-bottom: 1.5rem;">
                                                    <p
                                                        style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.75rem;">
                                                        Photos</p>
                                                    <div
                                                        style="display: flex; flex-direction: row; gap: 1rem; flex-wrap: wrap;">
                                                        @if ($kycVerification->passport_biodata_photo)
                                                            <div style="flex: 1; min-width: 150px;">
                                                                <p
                                                                    style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                                                                    Biodata Page Photo</p>
                                                                <img src="{{ asset('storage/' . $kycVerification->passport_biodata_photo) }}"
                                                                    alt="Passport Biodata"
                                                                    style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid var(--border); object-fit: contain;">
                                                            </div>
                                                        @endif
                                                        @if ($kycVerification->passport_cover_photo)
                                                            <div style="flex: 1; min-width: 150px;">
                                                                <p
                                                                    style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                                                                    Cover Page Photo</p>
                                                                <img src="{{ asset('storage/' . $kycVerification->passport_cover_photo) }}"
                                                                    alt="Passport Cover"
                                                                    style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid var(--border); object-fit: contain;">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="form-field" style="margin-bottom: 1.5rem;">
                                                    <label for="passport_biodata_photo" class="form-label"
                                                        style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                                        Biodata Page Photo <span style="color: #ef4444;">*</span>
                                                    </label>
                                                    <input type="file" class="form-control"
                                                        id="passport_biodata_photo" name="passport_biodata_photo"
                                                        accept="image/jpeg,image/png"
                                                        onchange="previewImage(this, 'passport_biodata_preview')"
                                                        {{ $kycVerification ? 'disabled' : '' }}
                                                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--card-bg); color: var(--text-primary); {{ $kycVerification ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                                                    <div id="passport_biodata_preview" style="margin-top: 0.5rem;"></div>
                                                    <div id="error_passport_biodata_photo"
                                                        style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: none;">
                                                    </div>
                                                </div>
                                                <div class="form-field" style="margin-bottom: 1.5rem;">
                                                    <label for="passport_cover_photo" class="form-label"
                                                        style="display: block; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                                        Cover Page Photo <span style="color: #ef4444;">*</span>
                                                    </label>
                                                    <input type="file" class="form-control" id="passport_cover_photo"
                                                        name="passport_cover_photo" accept="image/jpeg,image/png"
                                                        onchange="previewImage(this, 'passport_cover_preview')"
                                                        {{ $kycVerification ? 'disabled' : '' }}
                                                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--card-bg); color: var(--text-primary); {{ $kycVerification ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                                                    <div id="passport_cover_preview" style="margin-top: 0.5rem;"></div>
                                                    <div id="error_passport_cover_photo"
                                                        style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: none;">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div id="idVerificationMessage"
                                            style="display: none; padding: 0.75rem; margin-bottom: 1rem; border-radius: 6px;">
                                        </div>

                                        <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                                            <button type="submit" id="submitIdVerificationBtn" class="btn-save-changes"
                                                style="padding: 0.875rem 2rem; background: linear-gradient(135deg, #ffb11a 0%, #ff9500 100%); color: #000; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(255, 177, 26, 0.3);"
                                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(255, 177, 26, 0.4)'; this.style.background='linear-gradient(135deg, #ff9500 0%, #ffb11a 100%)'"
                                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(255, 177, 26, 0.3)'; this.style.background='linear-gradient(135deg, #ffb11a 0%, #ff9500 100%)'">
                                                <i class="fas fa-check-circle"></i> Submit Verification
                                            </button>
                                        </div>
                                    </form>
                                @endif

                                <script>
                                    function toggleIdFields() {
                                        try {
                                            const idType = document.getElementById('id_verification_type');
                                            if (!idType) {
                                                return;
                                            }

                                            const selectedType = idType.value;
                                            const nidFields = document.getElementById('nid_fields');
                                            const dlFields = document.getElementById('driving_license_fields');
                                            const passportFields = document.getElementById('passport_fields');

                                            // Hide all fields first
                                            if (nidFields) nidFields.style.display = 'none';
                                            if (dlFields) dlFields.style.display = 'none';
                                            if (passportFields) passportFields.style.display = 'none';

                                            // Show relevant fields based on selection
                                            if (selectedType === 'nid' && nidFields) {
                                                nidFields.style.display = 'block';
                                            } else if (selectedType === 'driving_license' && dlFields) {
                                                dlFields.style.display = 'block';
                                            } else if (selectedType === 'passport' && passportFields) {
                                                passportFields.style.display = 'block';
                                            }
                                        } catch (e) {
                                            console.error('Error in toggleIdFields:', e);
                                        }
                                    }

                                    document.addEventListener('DOMContentLoaded', function() {
                                        const idTypeSelect = document.getElementById('id_verification_type');
                                        if (idTypeSelect) {
                                            if (!idTypeSelect.disabled) {
                                                idTypeSelect.addEventListener('change', toggleIdFields);
                                            }
                                            if (idTypeSelect.value) {
                                                toggleIdFields();
                                            }
                                        }

                                        @if ($kycVerification)
                                            toggleIdFields();
                                        @endif
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div>

        <div id="passwordModal" class="modal-overlay">
            <div class="modal-box">
                <h3>{{ !empty($user->google_id) || !empty($user->facebook_id) ? 'Set Password' : 'Change Password' }}</h3>
                <div id="passwordMessage"
                    style="display: none; padding: 0.75rem; margin-bottom: 1rem; border-radius: 6px;"></div>
                <form id="passwordChangeForm" method="POST" action="{{ route('user.password.update') }}">
                    @csrf

                    @if (empty($user->google_id) && empty($user->facebook_id))
                        <div class="form-field">
                            <label>Current Password <span style="color: red;">*</span></label>
                            <input type="password" name="current_password" id="current_password" required>
                            <div id="error_current_password"
                                style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; display: none;"></div>
                        </div>
                    @else
                        <!-- OTP Step (Initially shown) -->
                        <div id="otpStep" style="display: block;">
                            <div class="form-field"
                                style="padding: 0.75rem; background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; margin-bottom: 1rem;">
                                <p style="margin: 0; color: #856404; font-size: 0.875rem;">
                                    <i class="fas fa-info-circle"></i> You logged in with
                                    {{ !empty($user->google_id) ? 'Google' : 'Facebook' }}.
                                    To change your password, we'll send an OTP to your email address
                                    ({{ $user->email }}).
                                </p>
                            </div>

                            <div class="modal-actions">
                                <button type="button" onclick="closePasswordModal()" class="btn-cancel">
                                    Cancel
                                </button>
                                <button type="button" id="sendOtpBtn" class="btn-save-changes">
                                    <i class="fas fa-paper-plane"></i> Send OTP to Email
                                </button>
                            </div>
                        </div>

                        <!-- Password Form (Hidden initially, shown after OTP sent) -->
                        <div id="passwordFormStep" style="display: none;">
                            <div class="form-field">
                                <label>OTP Code <span style="color: red;">*</span></label>
                                <input type="text" name="otp" id="otp" required maxlength="6"
                                    pattern="[0-9]{6}" placeholder="Enter 6-digit OTP" inputmode="numeric">
                                <small style="color: var(--text-secondary); font-size: 0.75rem;">Check your email for the
                                    OTP code</small>
                                <div id="error_otp"
                                    style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; display: none;"></div>
                            </div>
                        </div>
                    @endif

                    <div class="form-field" id="newPasswordField">
                        <label>New Password <span style="color: red;">*</span></label>
                        <input type="password" name="password" id="new_password" required minlength="8">
                        <small style="color: var(--text-secondary); font-size: 0.75rem;">Minimum 8 characters</small>
                        <div id="error_password"
                            style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; display: none;"></div>
                    </div>

                    <div class="form-field" id="confirmPasswordField">
                        <label>Confirm Password <span style="color: red;">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            minlength="8">
                        <div id="error_password_confirmation"
                            style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; display: none;"></div>
                    </div>

                    <div class="modal-actions" id="formActions">
                        <button type="button" onclick="closePasswordModal()" class="btn-cancel">
                            Cancel
                        </button>
                        @if (!empty($user->google_id) || !empty($user->facebook_id))
                            <button type="button" id="resendOtpBtn" class="btn-cancel"
                                style="background: #6c757d; display: none;">
                                <i class="fas fa-redo"></i> Resend OTP
                            </button>
                        @endif
                        <button type="submit" id="passwordUpdateBtn" class="btn-save-changes">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Withdrawal Modal Overlay -->
    <div class="withdrawal-modal-overlay" id="withdrawalModalOverlay"></div>

    <!-- Password Set Modal -->
    <div id="passwordSetModal" class="withdrawal-modal-popup">
        @livewire('set-withdrawal-password-modal')
    </div>

    <!-- Add Wallet Modal -->
    <div id="addWalletModal" class="withdrawal-modal-popup" style="z-index: 9001; max-width: 450px;">
        @livewire('add-wallet-modal')
    </div>

    <!-- Wallet List Modal -->
    <div id="walletListModal" class="withdrawal-modal-popup" style="max-width: 500px;">
        @livewire('wallet-list-modal')
    </div>

    <!-- Withdrawal Modal -->
    <div id="withdrawalModal" class="withdrawal-modal-popup">
        @livewire('withdrawal-request', [
            'has_withdrawal_password' => $hasWithdrawalPassword ?? false,
        ])
    </div>

    <style>
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal-box {
            background: var(--card-bg, #fff);
            padding: 1.5rem;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-box h3 {
            margin-bottom: 1rem;
            margin-top: 0;
            color: var(--text-primary);
        }

        .form-field {
            margin-bottom: 1rem;
        }

        .form-field label {
            display: block;
            margin-bottom: .4rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-field input {
            width: 100%;
            padding: .6rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            background: var(--card-bg);
            color: var(--text-primary);
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: .5rem;
            margin-top: 1.5rem;
        }

        .btn-cancel {
            background: #ccc;
            border: none;
            padding: .5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-cancel:hover {
            background: #b3b3b3;
        }

        .btn-save {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: .5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-save:hover {
            background: var(--);
        }

        /* MARKET Table Responsive Styles */
        .positions-table-container.desktop-view {
            display: block;
        }

        .positions-cards-container.mobile-view {
            display: none;
        }

        /* Mobile Responsive - Show Cards, Hide Table */
        @media (max-width: 768px) {
            .positions-table-container.desktop-view {
                display: none !important;
            }

            .positions-cards-container.mobile-view {
                display: block !important;
            }

            .position-card {
                border-left: none !important;
                border-right: none !important;
            }

            .position-card:hover {
                background: rgba(255, 255, 255, 0.03) !important;
            }
        }

        @media (min-width: 769px) {
            .positions-table-container.desktop-view {
                display: block !important;
            }

            .positions-cards-container.mobile-view {
                display: none !important;
            }
        }

        /* Deposits Table Responsive Styles */
        .deposits-table-wrapper.desktop-view {
            display: block;
        }

        .deposits-cards-container.mobile-view {
            display: none;
        }

        @media (max-width: 768px) {
            .deposits-table-wrapper.desktop-view {
                display: none !important;
            }

            .deposits-cards-container.mobile-view {
                display: block !important;
            }

            .deposit-card {
                transition: all 0.2s;
            }

            .deposit-card:hover {
                background: rgba(255, 255, 255, 0.03) !important;
            }
        }

        @media (min-width: 769px) {
            .deposits-table-wrapper.desktop-view {
                display: block !important;
            }

            .deposits-cards-container.mobile-view {
                display: none !important;
            }
        }

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

        /* Withdrawal Modal Popup - Matching Deposit Design */
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
            overflow-y: auto;
        }

        /* Add Wallet Modal - Higher z-index to appear above withdrawal modal */
        #addWalletModal {
            z-index: 9001 !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .withdrawal-modal-popup {
                width: 95%;
                max-width: none;
                border-radius: 20px 20px 0 0;
                top: auto;
                left: 0;
                right: 0;
                bottom: 0;
                transform: translate(0, 100%);
                max-height: 85vh;
            }

            .withdrawal-modal-popup.active {
                transform: translate(0, 0) !important;
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
            function openPasswordModal() {
                $('#passwordModal').fadeIn(200).css('display', 'flex');
                // Reset form and messages when opening
                $('#passwordChangeForm')[0].reset();
                $('#passwordMessage').hide().removeClass('alert-success alert-error').text('');
                // Clear all field errors
                $('[id^="error_"]').hide().text('');
                // Reset input borders
                $('input').css('border-color', '');

                // Reset OTP step for social login users
                const isSocialLogin = $('#otpStep').length > 0;
                if (isSocialLogin) {
                    $('#otpStep').show();
                    $('#passwordFormStep').hide();
                    $('#newPasswordField').hide();
                    $('#confirmPasswordField').hide();
                    $('#formActions').hide();
                    $('#resendOtpBtn').hide();
                    $('#passwordUpdateBtn').hide();
                }
            }

            function closePasswordModal() {
                $('#passwordModal').fadeOut(200);
                // Reset form when closing
                $('#passwordChangeForm')[0].reset();
                $('#passwordMessage').hide().removeClass('alert-success alert-error').text('');
                // Clear all field errors
                $('[id^="error_"]').hide().text('');
                // Reset input borders
                $('input').css('border-color', '');
            }

            // Close modal when clicking outside
            $(document).on('click', '#passwordModal.modal-overlay', function(e) {
                if ($(e.target).is('#passwordModal.modal-overlay')) {
                    closePasswordModal();
                }
            });

            $(document).ready(function() {
                // Send OTP button click (for social login users)
                $('#sendOtpBtn').on('click', function() {
                    const btn = $(this);
                    const originalText = btn.html();
                    const messageDiv = $('#passwordMessage');

                    messageDiv.hide().removeClass('alert-success alert-error').text('');
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');

                    $.ajax({
                        url: '{{ route('password.send.otp') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            btn.prop('disabled', false).html(originalText);
                            messageDiv.removeClass('alert-error').addClass('alert-success')
                                .css({
                                    'display': 'block',
                                    'background': '#d4edda',
                                    'color': '#155724',
                                    'border': '1px solid #c3e6cb'
                                })
                                .text(response.message || 'OTP sent successfully!');

                            // Show password form step
                            $('#otpStep').hide();
                            $('#passwordFormStep').show();
                            $('#newPasswordField').show();
                            $('#confirmPasswordField').show();
                            $('#formActions').show();
                            $('#resendOtpBtn').show();
                            $('#passwordUpdateBtn').show();
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false).html(originalText);
                            let errorMsg = 'Failed to send OTP. Please try again.';

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }

                            messageDiv.removeClass('alert-success').addClass('alert-error')
                                .css({
                                    'display': 'block',
                                    'background': '#f8d7da',
                                    'color': '#721c24',
                                    'border': '1px solid #f5c6cb'
                                })
                                .text(errorMsg);
                        }
                    });
                });

                // Resend OTP button click
                $('#resendOtpBtn').on('click', function() {
                    $('#sendOtpBtn').click();
                });

                // OTP input - only allow numbers
                $(document).on('input', '#otp', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });

                // Password change form submission
                $('#passwordChangeForm').on('submit', function(e) {
                    e.preventDefault();
                    const formData = $(this).serialize();
                    const btn = $('#passwordUpdateBtn');
                    const originalText = btn.html();
                    const messageDiv = $('#passwordMessage');

                    // Hide previous messages
                    messageDiv.hide().removeClass('alert-success alert-error').text('');

                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            console.log('Password update success:', response);

                            if (!response || !response.success) {
                                // If response doesn't have success flag, treat as error
                                messageDiv.removeClass('alert-success').addClass('alert-error')
                                    .css({
                                        'display': 'block',
                                        'background': '#f8d7da',
                                        'color': '#721c24',
                                        'border': '1px solid #f5c6cb'
                                    })
                                    .text(response.message ||
                                        'Password update failed. Please try again.');
                                btn.prop('disabled', false).html(originalText);
                                return;
                            }

                            btn.prop('disabled', false).html(originalText);
                            messageDiv.removeClass('alert-error').addClass('alert-success')
                                .css({
                                    'display': 'block',
                                    'background': '#d4edda',
                                    'color': '#155724',
                                    'border': '1px solid #c3e6cb'
                                })
                                .text(response.message || 'Password updated successfully!');

                            // Clear form
                            $('#passwordChangeForm')[0].reset();

                            // Close modal after 2 seconds
                            setTimeout(function() {
                                closePasswordModal();
                                if (typeof showSuccess !== 'undefined') {
                                    showSuccess('Password updated successfully!',
                                        'Success');
                                } else if (typeof toastr !== 'undefined') {
                                    toastr.success('Password updated successfully!',
                                        'Success');
                                }
                            }, 2000);
                        },
                        error: function(xhr) {
                            console.error('Password update error:', xhr);
                            console.error('Response:', xhr.responseJSON);
                            btn.prop('disabled', false).html(originalText);

                            // Clear all field errors first
                            $('[id^="error_"]').hide().text('');

                            let errorMsg = 'Failed to update password. Please try again.';
                            let hasFieldErrors = false;

                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.errors) {
                                    // Handle validation errors - show field-specific errors
                                    const errors = xhr.responseJSON.errors;
                                    hasFieldErrors = true;

                                    // Show errors for each field
                                    for (let field in errors) {
                                        const errorFieldId = '#error_' + field;
                                        const $errorField = $(errorFieldId);
                                        if ($errorField.length) {
                                            $errorField.text(errors[field][0]).show();
                                        }

                                        // Highlight the input field - map field names to input IDs
                                        let inputFieldId = '';
                                        if (field === 'current_password') {
                                            inputFieldId = '#current_password';
                                        } else if (field === 'password') {
                                            inputFieldId = '#new_password';
                                        } else if (field === 'password_confirmation') {
                                            inputFieldId = '#password_confirmation';
                                        } else if (field === 'otp') {
                                            inputFieldId = '#otp';
                                        }

                                        if (inputFieldId) {
                                            $(inputFieldId).css('border-color', '#dc3545');
                                        }
                                    }

                                    // Collect all error messages for general message
                                    const errorMessages = [];
                                    for (let field in errors) {
                                        errorMessages.push(errors[field][0]);
                                    }
                                    errorMsg = errorMessages.join('<br>');
                                } else if (xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                }
                            }

                            // Show general error message
                            messageDiv.removeClass('alert-success').addClass('alert-error')
                                .css({
                                    'display': 'block',
                                    'background': '#f8d7da',
                                    'color': '#721c24',
                                    'border': '1px solid #f5c6cb'
                                })
                                .html(errorMsg);

                            // Show toast notification
                            if (typeof showError !== 'undefined') {
                                showError(errorMsg.replace(/<br>/g, ' '), 'Error');
                            } else if (typeof toastr !== 'undefined') {
                                toastr.error(errorMsg.replace(/<br>/g, ' '), 'Error');
                            }

                            // Remove error highlighting after 5 seconds
                            setTimeout(function() {
                                $('input').css('border-color', '');
                            }, 5000);
                        }
                    });
                });
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

                    // Filter positions by subtab - works with search
                    const searchTerm = $('.search-input').val().toLowerCase();
                    $('.position-row').each(function() {
                        const $row = $(this);
                        const rowSubtab = $row.data('subtab');
                        const marketText = ($row.data('market') || '').toLowerCase();
                        const matchesSubtab = subtab === 'active' ? rowSubtab === 'active' :
                            rowSubtab === 'closed';
                        const matchesSearch = !searchTerm || marketText.includes(searchTerm);

                        $row.toggle(matchesSubtab && matchesSearch);
                    });
                });

                // Filter dropdown
                $('#sortFilterBtn').on('click', function(e) {
                    e.stopPropagation();
                    const $wrapper = $(this).closest('.filter-dropdown-wrapper');
                    $('.filter-dropdown-wrapper').not($wrapper).removeClass('active');
                    $wrapper.toggleClass('active');
                });

                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.filter-dropdown-wrapper').length) {
                        $('.filter-dropdown-wrapper').removeClass('active');
                    }
                });

                $('.filter-dropdown-item[data-sort]').on('click', function(e) {
                    e.preventDefault();
                    $('.filter-dropdown-item[data-sort]').removeClass('active');
                    $(this).addClass('active');
                    const sort = $(this).data('sort');
                    $('#sortFilterBtn span').text($(this).text());
                    $(this).closest('.filter-dropdown-wrapper').removeClass('active');

                    // Sort positions - only sort visible rows
                    const $visibleRows = $('.position-row:visible').toArray();
                    if ($visibleRows.length === 0) {
                        // If no visible rows, sort all rows
                        const $allRows = $('.position-row').toArray();
                        $allRows.sort(function(a, b) {
                            const $a = $(a);
                            const $b = $(b);
                            let valA, valB;

                            switch (sort) {
                                case 'value':
                                    valA = parseFloat($a.data('value')) || 0;
                                    valB = parseFloat($b.data('value')) || 0;
                                    return valB - valA; // Descending
                                case 'profit':
                                    valA = parseFloat($a.data('profit')) || 0;
                                    valB = parseFloat($b.data('profit')) || 0;
                                    return valB - valA; // Descending
                                case 'profit_pct':
                                    valA = parseFloat($a.data('profitPct')) || 0;
                                    valB = parseFloat($b.data('profitPct')) || 0;
                                    return valB - valA; // Descending
                                case 'bet':
                                    valA = parseFloat($a.data('bet')) || 0;
                                    valB = parseFloat($b.data('bet')) || 0;
                                    return valB - valA; // Descending
                                case 'alphabetical':
                                    valA = ($a.data('market') || '').toLowerCase();
                                    valB = ($b.data('market') || '').toLowerCase();
                                    return valA.localeCompare(valB); // Ascending
                                case 'avg_price':
                                    valA = parseFloat($a.data('avgPrice')) || 0;
                                    valB = parseFloat($b.data('avgPrice')) || 0;
                                    return valB - valA; // Descending
                                case 'current_price':
                                    valA = parseFloat($a.data('currentPrice')) || 0;
                                    valB = parseFloat($b.data('currentPrice')) || 0;
                                    return valB - valA; // Descending
                                default:
                                    return 0;
                            }
                        });

                        // Re-append sorted rows (both table and cards)
                        const $tbody = $('.positions-table tbody');
                        if ($tbody.length) {
                            $tbody.empty();
                            $allRows.filter(row => $(row).closest('.positions-table-container').length).forEach(
                                row => $tbody.append(row));
                        }

                        const $cardsContainer = $('.positions-cards-container');
                        if ($cardsContainer.length) {
                            $cardsContainer.empty();
                            $allRows.filter(row => $(row).hasClass('position-card')).forEach(row =>
                                $cardsContainer.append(row));
                        }
                    } else {
                        // Sort only visible rows
                        $visibleRows.sort(function(a, b) {
                            const $a = $(a);
                            const $b = $(b);
                            let valA, valB;

                            switch (sort) {
                                case 'value':
                                    valA = parseFloat($a.data('value')) || 0;
                                    valB = parseFloat($b.data('value')) || 0;
                                    return valB - valA; // Descending
                                case 'profit':
                                    valA = parseFloat($a.data('profit')) || 0;
                                    valB = parseFloat($b.data('profit')) || 0;
                                    return valB - valA; // Descending
                                case 'profit_pct':
                                    valA = parseFloat($a.data('profitPct')) || 0;
                                    valB = parseFloat($b.data('profitPct')) || 0;
                                    return valB - valA; // Descending
                                case 'bet':
                                    valA = parseFloat($a.data('bet')) || 0;
                                    valB = parseFloat($b.data('bet')) || 0;
                                    return valB - valA; // Descending
                                case 'alphabetical':
                                    valA = ($a.data('market') || '').toLowerCase();
                                    valB = ($b.data('market') || '').toLowerCase();
                                    return valA.localeCompare(valB); // Ascending
                                case 'avg_price':
                                    valA = parseFloat($a.data('avgPrice')) || 0;
                                    valB = parseFloat($b.data('avgPrice')) || 0;
                                    return valB - valA; // Descending
                                case 'current_price':
                                    valA = parseFloat($a.data('currentPrice')) || 0;
                                    valB = parseFloat($b.data('currentPrice')) || 0;
                                    return valB - valA; // Descending
                                default:
                                    return 0;
                            }
                        });

                        // Re-append sorted visible rows, then append hidden rows (both table and cards)
                        const $hiddenRows = $('.position-row:hidden').toArray();

                        const $tbody = $('.positions-table tbody');
                        if ($tbody.length) {
                            $tbody.empty();
                            $visibleRows.filter(row => $(row).closest('.positions-table-container').length)
                                .forEach(row => $tbody.append(row));
                            $hiddenRows.filter(row => $(row).closest('.positions-table-container').length)
                                .forEach(row => $tbody.append(row));
                        }

                        const $cardsContainer = $('.positions-cards-container');
                        if ($cardsContainer.length) {
                            $cardsContainer.empty();
                            $visibleRows.filter(row => $(row).hasClass('position-card')).forEach(row =>
                                $cardsContainer.append(row));
                            $hiddenRows.filter(row => $(row).hasClass('position-card')).forEach(row =>
                                $cardsContainer.append(row));
                        }
                    }
                });

                // Amount filter dropdown
                $('#amountFilterBtn').on('click', function(e) {
                    e.stopPropagation();
                    const $wrapper = $(this).closest('.filter-dropdown-wrapper');
                    $('.filter-dropdown-wrapper').not($wrapper).removeClass('active');
                    $wrapper.toggleClass('active');
                });

                $('.filter-dropdown-item[data-amount]').on('click', function(e) {
                    e.preventDefault();
                    $('.filter-dropdown-item[data-amount]').removeClass('active');
                    $(this).addClass('active');
                    const amount = $(this).data('amount');
                    $('#amountFilterBtn span').text($(this).text());
                    $(this).closest('.filter-dropdown-wrapper').removeClass('active');

                    // Filter activity by amount - works with search
                    const searchTerm = $('#activitySearchInput').val().toLowerCase();
                    const $activityItems = $('.activity-item');

                    $activityItems.each(function() {
                        const $item = $(this);
                        const itemAmount = parseFloat($item.data('amount')) || 0;
                        const activityId = $item.data('activity-id') || '';
                        const activityText = $item.text().toLowerCase();

                        // Check amount filter
                        let matchesAmount = true;
                        if (amount !== 'all') {
                            const minAmount = parseFloat(amount);
                            matchesAmount = itemAmount >= minAmount;
                        }

                        // Check search filter
                        const matchesSearch = !searchTerm || activityId.toString().includes(
                            searchTerm) || activityText.includes(searchTerm);

                        $item.toggle(matchesAmount && matchesSearch);
                    });
                });

                // Search functionality for positions - works with Active/Closed filter
                $('.search-input').on('input', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    const activeSubtab = $('.subtab-btn.active').data('subtab') || 'active';

                    $('.position-row').each(function() {
                        const $row = $(this);
                        const marketText = ($row.data('market') || '').toLowerCase();
                        const rowSubtab = $row.data('subtab');
                        const matchesSearch = !searchTerm || marketText.includes(searchTerm);
                        const matchesSubtab = activeSubtab === 'active' ? rowSubtab === 'active' :
                            rowSubtab === 'closed';

                        $row.toggle(matchesSearch && matchesSubtab);
                    });
                });

                // Deposit search (works with both table rows and cards)
                $('#depositSearchInput').on('input', function() {
                    const searchTerm = $(this).val().toLowerCase();

                    $('.deposit-row').each(function() {
                        const $row = $(this);
                        const amount = $row.data('amount') || '';
                        const method = $row.data('method') || '';
                        const status = $row.data('status') || '';
                        const transaction = $row.data('transaction') || '';

                        const matchesSearch = !searchTerm ||
                            amount.toString().includes(searchTerm) ||
                            method.includes(searchTerm) ||
                            status.includes(searchTerm) ||
                            transaction.includes(searchTerm);

                        $row.toggle(matchesSearch);
                    });
                });

                // Activity search - works with amount filter
                $('#activitySearchInput').on('input', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    const selectedAmount = $('.filter-dropdown-item[data-amount].active').data('amount') ||
                        'all';

                    $('.activity-item').each(function() {
                        const $item = $(this);
                        const activityId = $item.data('activity-id') || '';
                        const activityText = $item.text().toLowerCase();
                        const matchesSearch = !searchTerm || activityId.toString().includes(
                            searchTerm) || activityText.includes(searchTerm);

                        // Check amount filter
                        let matchesAmount = true;
                        if (selectedAmount !== 'all') {
                            const itemAmount = parseFloat($item.data('amount')) || 0;
                            matchesAmount = itemAmount >= parseFloat(selectedAmount);
                        }

                        $item.toggle(matchesSearch && matchesAmount);
                    });
                });

                // Withdrawal search - works with status filter
                $('#withdrawalSearchInput').on('input', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    const selectedStatus = $('.filter-dropdown-item[data-status].active').data('status') ||
                        'all';

                    $('.withdrawal-item').each(function() {
                        const $item = $(this);
                        const withdrawalId = $item.data('withdrawal-id') || '';
                        const withdrawalText = $item.text().toLowerCase();
                        const matchesSearch = !searchTerm || withdrawalId.toString().includes(
                            searchTerm) || withdrawalText.includes(searchTerm);

                        // Check status filter
                        let matchesStatus = true;
                        if (selectedStatus !== 'all') {
                            const itemStatus = $item.data('status');
                            matchesStatus = itemStatus === selectedStatus;
                        }

                        $item.toggle(matchesSearch && matchesStatus);
                    });
                });

                // Withdrawal status filter
                $('#withdrawalStatusFilterBtn').on('click', function(e) {
                    e.stopPropagation();
                    const $wrapper = $(this).closest('.filter-dropdown-wrapper');
                    $('.filter-dropdown-wrapper').not($wrapper).removeClass('active');
                    $wrapper.toggleClass('active');
                });

                $('.filter-dropdown-item[data-status]').on('click', function(e) {
                    e.preventDefault();
                    $('.filter-dropdown-item[data-status]').removeClass('active');
                    $(this).addClass('active');
                    const status = $(this).data('status');
                    $('#withdrawalStatusFilterBtn span').text($(this).text());
                    $(this).closest('.filter-dropdown-wrapper').removeClass('active');

                    // Filter withdrawal by status - works with search
                    const searchTerm = $('#withdrawalSearchInput').val().toLowerCase();
                    const $withdrawalItems = $('.withdrawal-item');

                    $withdrawalItems.each(function() {
                        const $item = $(this);
                        const itemStatus = $item.data('status');
                        const withdrawalId = $item.data('withdrawal-id') || '';
                        const withdrawalText = $item.text().toLowerCase();

                        // Check status filter
                        let matchesStatus = true;
                        if (status !== 'all') {
                            matchesStatus = itemStatus === status;
                        }

                        // Check search filter
                        const matchesSearch = !searchTerm || withdrawalId.toString().includes(
                            searchTerm) || withdrawalText.includes(searchTerm);

                        $item.toggle(matchesStatus && matchesSearch);
                    });
                });

                // Initialize profit/loss
                updateProfitLoss('1D');

                // Withdrawal Modal Functions (Matching Deposit Modal)
                window.handleWithdrawalClick = function() {
                    @if (!$hasWithdrawalPassword)
                        openPasswordSetModal();
                        return;
                    @endif
                    openWithdrawalModal();
                };

                window.openPasswordSetModal = function() {
                    const $modal = $('#passwordSetModal');
                    const $overlay = $('#withdrawalModalOverlay');
                    $modal.addClass('active');
                    $overlay.addClass('active');
                    $('body').css('overflow', 'hidden');
                };

                window.closePasswordSetModal = function() {
                    const $modal = $('#passwordSetModal');
                    const $overlay = $('#withdrawalModalOverlay');
                    $modal.removeClass('active');
                    $overlay.removeClass('active');
                    $('body').css('overflow', '');
                };

                window.openWithdrawalModal = function() {
                    const $modal = $('#withdrawalModal');
                    const $overlay = $('#withdrawalModalOverlay');
                    $modal.addClass('active');
                    $overlay.addClass('active');
                    $('body').css('overflow', 'hidden');

                    // Refresh wallets when modal opens
                    setTimeout(() => {
                        const withdrawalModal = document.querySelector('#withdrawalModal');
                        if (withdrawalModal) {
                            const wireId = withdrawalModal.getAttribute('wire:id');
                            if (wireId) {
                                const withdrawalComponent = Livewire.find(wireId);
                                if (withdrawalComponent) {
                                    withdrawalComponent.call('refreshWallets');
                                }
                            }
                        }
                    }, 100);
                };

                window.closeWithdrawalModal = function() {
                    const $modal = $('#withdrawalModal');
                    const $overlay = $('#withdrawalModalOverlay');
                    $modal.removeClass('active');
                    $overlay.removeClass('active');
                    $('body').css('overflow', '');
                };

                window.openAddWalletModal = function() {
                    const $modal = $('#addWalletModal');
                    const $overlay = $('#withdrawalModalOverlay');
                    $modal.addClass('active');
                    $overlay.addClass('active');
                    $overlay.css('z-index', '9000');
                    $('body').css('overflow', 'hidden');
                };

                window.closeAddWalletModal = function() {
                    const $modal = $('#addWalletModal');
                    const $overlay = $('#withdrawalModalOverlay');
                    $modal.removeClass('active');
                    // Only reset overlay z-index if withdrawal modal is not active
                    if (!$('#withdrawalModal').hasClass('active')) {
                        $overlay.removeClass('active');
                        $overlay.css('z-index', '7000');
                        $('body').css('overflow', '');
                    } else {
                        // Keep overlay active but reset z-index for withdrawal modal
                        $overlay.css('z-index', '7000');
                    }
                };

                window.openWalletListModal = function() {
                    const $modal = $('#walletListModal');
                    const $overlay = $('#withdrawalModalOverlay');
                    $modal.addClass('active');
                    $overlay.addClass('active');
                    $('body').css('overflow', 'hidden');

                    // Refresh wallets when modal opens
                    setTimeout(() => {
                        const walletListModal = document.querySelector('#walletListModal');
                        if (walletListModal) {
                            const wireId = walletListModal.getAttribute('wire:id');
                            if (wireId) {
                                const walletListComponent = Livewire.find(wireId);
                                if (walletListComponent) {
                                    walletListComponent.call('loadWallets');
                                }
                            }
                        }
                    }, 100);
                };

                window.closeWalletListModal = function() {
                    const $modal = $('#walletListModal');
                    const $overlay = $('#withdrawalModalOverlay');
                    $modal.removeClass('active');
                    $overlay.removeClass('active');
                    $('body').css('overflow', '');
                };

                // Close modal on overlay click
                $('#withdrawalModalOverlay').on('click', function() {
                    if ($('#addWalletModal').hasClass('active')) {
                        closeAddWalletModal();
                    } else if ($('#walletListModal').hasClass('active')) {
                        closeWalletListModal();
                    } else if ($('#withdrawalModal').hasClass('active')) {
                        closeWithdrawalModal();
                    } else if ($('#passwordSetModal').hasClass('active')) {
                        closePasswordSetModal();
                    }
                });


                // Open modal if redirected from withdrawal page
                @if (session('open_withdrawal_modal'))
                    setTimeout(function() {
                        openWithdrawalModal();
                    }, 500);
                @endif

                // Listen for password set event and wallet events
                document.addEventListener('livewire:init', () => {
                    Livewire.on('withdrawal-password-set', () => {
                        closePasswordSetModal();
                        setTimeout(() => {
                            openWithdrawalModal();
                        }, 300);
                    });

                    Livewire.on('wallet-added', () => {
                        closeAddWalletModal();
                    });

                    Livewire.on('refresh-withdrawal-wallets', () => {
                        // Try to find and refresh the withdrawal component
                        const withdrawalModal = document.querySelector('#withdrawalModal');
                        if (withdrawalModal) {
                            const wireId = withdrawalModal.getAttribute('wire:id');
                            if (wireId) {
                                const withdrawalComponent = Livewire.find(wireId);
                                if (withdrawalComponent) {
                                    withdrawalComponent.call('refreshWallets');
                                }
                            }
                        }
                    });

                    Livewire.on('withdrawal-submitted', (event) => {
                        const data = Array.isArray(event) ? event[0] : event;

                        setTimeout(() => {
                            if (typeof closeWithdrawalModal === 'function') {
                                closeWithdrawalModal();
                            }

                            // Use showSuccess function if available
                            if (typeof showSuccess !== 'undefined') {
                                showSuccess(
                                    data.message || 'Withdrawal request submitted successfully! It will be reviewed by admin and processed within 24-48 hours.',
                                    'Withdrawal Submitted'
                                );
                            } else if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Withdrawal Submitted',
                                    text: data.message || 'Withdrawal request submitted successfully! It will be reviewed by admin and processed within 24-48 hours.',
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 4000,
                                    timerProgressBar: true,
                                    toast: true,
                                    confirmButtonColor: '#ffb11a',
                                });
                            } else if (typeof toastr !== 'undefined') {
                                toastr.success(
                                    data.message || 'Withdrawal request submitted successfully!',
                                    'Withdrawal Submitted'
                                );
                            } else {
                                alert(data.message || 'Withdrawal request submitted successfully!');
                            }

                            // Reload after a delay to update balance
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }, 100);
                    });
                });

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

            let profitLossChart = null;

            // Real profit/loss data from backend
            const profitLossData = @json($profitLossData ?? []);

            function getProfitLossDataForTimeframe(timeframe) {
                if (!profitLossData || profitLossData.length === 0) {
                    return {
                        labels: [],
                        data: []
                    };
                }

                const today = new Date();
                let cutoffDate = new Date();

                if (timeframe === '1D') {
                    cutoffDate.setDate(today.getDate() - 1);
                } else if (timeframe === '1W') {
                    cutoffDate.setDate(today.getDate() - 7);
                } else if (timeframe === '1M') {
                    cutoffDate.setDate(today.getDate() - 30);
                } else {
                    // ALL - use all data
                    cutoffDate = null;
                }

                // Filter data based on timeframe
                let filteredData = profitLossData;
                if (cutoffDate) {
                    filteredData = profitLossData.filter(item => {
                        const itemDate = new Date(item.date);
                        return itemDate >= cutoffDate;
                    });
                }

                // If no data for selected timeframe, return empty
                if (filteredData.length === 0) {
                    return {
                        labels: [],
                        data: []
                    };
                }

                // Extract labels and values
                const labels = filteredData.map(item => {
                    const date = new Date(item.date);
                    if (timeframe === '1D') {
                        return date.toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    } else {
                        return item.label || date.toLocaleDateString('en-US', {
                            month: 'short',
                            day: 'numeric'
                        });
                    }
                });

                const data = filteredData.map(item => parseFloat(item.value) || 0);

                return {
                    labels,
                    data
                };
            }

            function initProfitLossChart(timeframe = '1D') {
                const ctx = document.getElementById('profitLossChart');
                if (!ctx || typeof Chart === 'undefined') {
                    setTimeout(() => initProfitLossChart(timeframe), 100);
                    return;
                }

                // Destroy existing chart if it exists
                if (profitLossChart) {
                    profitLossChart.destroy();
                }

                const chartData = getProfitLossDataForTimeframe(timeframe);

                // If no data, show zero
                if (chartData.data.length === 0) {
                    $('#profitLossAmount').text('$0.00');
                    // Create empty chart
                    profitLossChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'Profit/Loss',
                                data: [],
                                borderColor: '#00d4aa',
                                backgroundColor: 'rgba(0, 212, 170, 0.2)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: false
                                }
                            },
                            scales: {
                                x: {
                                    display: false
                                },
                                y: {
                                    display: false
                                }
                            }
                        }
                    });
                    return;
                }

                // Calculate total profit/loss (last value - first value, or just last value if starting from 0)
                const firstValue = chartData.data[0] || 0;
                const lastValue = chartData.data[chartData.data.length - 1] || 0;
                const totalProfitLoss = lastValue - firstValue;

                // Update amount display
                $('#profitLossAmount').text('$' + lastValue.toFixed(2));

                profitLossChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Profit/Loss',
                            data: chartData.data,
                            borderColor: '#00d4aa', // Teal-green color
                            backgroundColor: 'rgba(0, 212, 170, 0.2)', // Teal-green with transparency
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            pointHoverBackgroundColor: '#00d4aa',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true,
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#ffffff',
                                bodyColor: '#00d4aa',
                                borderColor: '#00d4aa',
                                borderWidth: 1,
                                padding: 12,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return '$' + context.parsed.y.toFixed(2);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                display: true,
                                grid: {
                                    display: true,
                                    color: 'rgba(255, 255, 255, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    color: 'var(--text-secondary)',
                                    font: {
                                        size: 10
                                    },
                                    maxRotation: 0,
                                    autoSkip: true,
                                    maxTicksLimit: timeframe === '1D' ? 12 : timeframe === '1W' ? 7 : 10
                                }
                            },
                            y: {
                                display: true,
                                grid: {
                                    display: true,
                                    color: 'rgba(255, 255, 255, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    color: 'var(--text-secondary)',
                                    font: {
                                        size: 10
                                    },
                                    callback: function(value) {
                                        return '$' + value.toFixed(0);
                                    }
                                },
                                beginAtZero: false
                            }
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        elements: {
                            point: {
                                radius: 0,
                                hoverRadius: 4
                            }
                        }
                    }
                });
            }

            function updateProfitLoss(timeframe) {
                const timeframes = {
                    '1D': 'Past Day',
                    '1W': 'Past Week',
                    '1M': 'Past Month',
                    'ALL': 'All Time'
                };

                $('#profitLossTimeframe').text(timeframes[timeframe] || 'Past Day');

                // Update chart with new timeframe data
                initProfitLossChart(timeframe);
            }

            // Initialize chart on page load
            $(document).ready(function() {
                // Wait for Chart.js to load
                function checkChartJS() {
                    if (typeof Chart !== 'undefined') {
                        initProfitLossChart('1D');
                    } else {
                        setTimeout(checkChartJS, 50);
                    }
                }
                checkChartJS();

            });
        </script>

        <script>
            // Profit/Loss Time Filter Handler
            (function() {
                const plFilters = document.querySelectorAll('.pl-time-filter');
                plFilters.forEach(btn => {
                    btn.addEventListener('click', function() {
                        // Remove active class from all buttons
                        plFilters.forEach(b => b.classList.remove('active'));
                        // Add active class to clicked button
                        this.classList.add('active');

                        // Update button styles
                        plFilters.forEach(b => {
                            b.style.background = 'transparent';
                            b.style.color = '#9ca3af';
                            b.style.fontWeight = '500';
                        });
                        this.style.background = 'rgba(59, 130, 246, 0.2)';
                        this.style.color = '#ffffff';
                        this.style.fontWeight = '600';

                        const period = this.getAttribute('data-pl-period');
                        console.log('Profit/Loss filter changed to:', period);

                        // Update timeframe text
                        const timeframeMap = {
                            '1D': 'Today',
                            '1W': 'Past Week',
                            '1M': 'Past Month',
                            'ALL': 'All Time'
                        };

                        const timeframeEl = document.getElementById('profitLossTimeframe');
                        if (timeframeEl) {
                            timeframeEl.textContent = timeframeMap[period] || 'Past Month';
                        }

                        // Update profit/loss calculation if function exists
                        if (typeof initProfitLossChart === 'function') {
                            initProfitLossChart(period);
                        }
                    });
                });
            })();

            // Handle logout form submission with confirmation
            $(document).ready(function() {
                $('#logoutForm').on('submit', function(e) {
                    e.preventDefault();

                    // Show confirmation popup using SweetAlert
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: 'Do you want to logout?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, Logout',
                            cancelButtonText: 'Cancel',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Show loading toast
                                if (typeof showInfo !== 'undefined') {
                                    showInfo('Logging out...', 'Logout');
                                } else if (typeof toastr !== 'undefined') {
                                    toastr.info('Logging out...', 'Logout');
                                }

                                // Submit form after a short delay
                                setTimeout(function() {
                                    $('#logoutForm')[0].submit();
                                }, 300);
                            }
                        });
                    } else {
                        // Fallback to browser confirm if SweetAlert is not available
                        if (confirm('Are you sure you want to logout?')) {
                            $('#logoutForm')[0].submit();
                        }
                    }
                });
            });
        </script>
        <script>
            window.previewImage = function(input, previewId) {
                const preview = document.getElementById(previewId);
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = '<img src="' + e.target.result +
                            '" style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; border: 1px solid var(--border); margin-top: 0.5rem; object-fit: contain;">';
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $(document).ready(function() {

                $('#idVerificationForm').on('submit', function(e) {
                    e.preventDefault();
                    const btn = $('#submitIdVerificationBtn');
                    if (btn.prop('disabled')) {
                        return false;
                    }

                    const formData = new FormData(this);
                    const originalText = btn.html();
                    const messageDiv = $('#idVerificationMessage');

                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
                    messageDiv.hide().removeClass('alert-success alert-error');
                    $('.error-text').hide();

                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                btn.prop('disabled', false).html(originalText);
                                $('.error-text').hide();

                                const message = response.message ||
                                    'ID verification submitted successfully!';

                                // Show toast notification - prioritize showSuccess function, then toastr, then Swal
                                if (typeof showSuccess !== 'undefined') {
                                    showSuccess(message, 'Success');
                                } else if (typeof toastr !== 'undefined') {
                                    toastr.success(message, 'Success');
                                } else if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: message,
                                        confirmButtonColor: '#ffb11a',
                                        confirmButtonText: 'OK',
                                        allowOutsideClick: false,
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 3000,
                                        timerProgressBar: true
                                    });
                                } else {
                                    messageDiv.removeClass('alert-error').addClass('alert-success')
                                        .css({
                                            'background': '#d4edda',
                                            'color': '#155724',
                                            'border': '1px solid #c3e6cb'
                                        })
                                        .text(message)
                                        .show();
                                }

                                setTimeout(function() {
                                    window.location.reload();
                                }, 2500);
                            } else {
                                messageDiv.removeClass('alert-success').addClass('alert-error')
                                    .css({
                                        'background': '#f8d7da',
                                        'color': '#721c24',
                                        'border': '1px solid #f5c6cb'
                                    })
                                    .text(response.message ||
                                        'Submission failed. Please try again.')
                                    .show();
                                btn.prop('disabled', false).html(originalText);
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            if (response && response.errors) {
                                $.each(response.errors, function(key, value) {
                                    // Map field names to error div IDs
                                    let errorFieldId = '#error_' + key.replace(/\./g, '_');

                                    // Handle special field name mappings
                                    if (key === 'id_passport_expiry_date') {
                                        errorFieldId = '#error_id_passport_expiry_date';
                                    } else if (key === 'passport_cover_photo') {
                                        errorFieldId = '#error_passport_cover_photo';
                                    }

                                    const errorDiv = $(errorFieldId);
                                    if (errorDiv.length) {
                                        errorDiv.text(value[0]).show();
                                    }
                                });
                            }

                            const errorMsg = response?.message ||
                                'An error occurred. Please try again.';

                            // Show toast notification for errors
                            if (typeof toastr !== 'undefined') {
                                toastr.error(errorMsg, 'Error', {
                                    timeOut: 5000,
                                    progressBar: true,
                                    positionClass: 'toast-top-right'
                                });
                            } else if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: errorMsg,
                                    confirmButtonColor: '#ef4444',
                                    confirmButtonText: 'OK',
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 5000,
                                    timerProgressBar: true
                                });
                            } else {
                                messageDiv.removeClass('alert-success').addClass('alert-error')
                                    .css({
                                        'background': '#f8d7da',
                                        'color': '#721c24',
                                        'border': '1px solid #f5c6cb'
                                    })
                                    .text(errorMsg)
                                    .show();
                            }

                            btn.prop('disabled', false).html(originalText);
                        }
                    });
                });
            });

            // Copy Referral Link Function
            function copyReferralLink() {
                event.preventDefault();
                event.stopPropagation();
                
                const input = document.getElementById('referralLinkInput');
                const referralLink = input.value;
                const copyBtn = document.getElementById('copyReferralLinkBtn');
                const copyIcon = document.getElementById('copyIcon');

                // Function to update icon state
                function updateIconState(success) {
                    if (success) {
                        // Change to success state
                        copyIcon.classList.remove('fa-copy');
                        copyIcon.classList.add('fa-check');
                        copyBtn.style.background = '#10b981';
                        copyBtn.style.color = '#fff';
                        copyBtn.title = 'Copied!';
                        
                        // Show success notification
                        if (typeof showSuccess !== 'undefined') {
                            showSuccess('Referral link copied to clipboard!', 'Copied');
                        } else if (typeof toastr !== 'undefined') {
                            toastr.success('Referral link copied to clipboard!', 'Copied');
                        }

                        // Reset icon after 2 seconds
                        setTimeout(function() {
                            copyIcon.classList.remove('fa-check');
                            copyIcon.classList.add('fa-copy');
                            copyBtn.style.background = '#ffb11a';
                            copyBtn.style.color = '#000';
                            copyBtn.title = 'Copy referral link';
                        }, 2000);
                    } else {
                        // Show error
                        if (typeof showError !== 'undefined') {
                            showError('Failed to copy link. Please copy manually.', 'Error');
                        } else if (typeof toastr !== 'undefined') {
                            toastr.error('Failed to copy link. Please copy manually.', 'Error');
                        } else {
                            alert('Failed to copy. Please copy manually.');
                        }
                    }
                }

                // Try modern clipboard API first
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(referralLink).then(function() {
                        updateIconState(true);
                    }).catch(function(err) {
                        console.error('Clipboard API failed:', err);
                        // Fallback to execCommand
                        tryFallbackCopy();
                    });
                } else {
                    // Use fallback method
                    tryFallbackCopy();
                }

                // Fallback copy method using execCommand
                function tryFallbackCopy() {
                    try {
                        // Select the input text
                        input.focus();
                        input.select();
                        input.setSelectionRange(0, 99999); // For mobile devices
                        
                        // Copy using execCommand
                        const successful = document.execCommand('copy');
                        
                        if (successful) {
                            updateIconState(true);
                        } else {
                            updateIconState(false);
                        }
                    } catch (err) {
                        console.error('Copy failed:', err);
                        updateIconState(false);
                    }
                }
            }
        @endpush
    @endsection

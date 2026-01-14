@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Profile - {{ $appName }}</title>
    <meta name="description" content="View and manage your profile on {{ $appName }}.">
@endsection
@section('content')
    <main>
        <div class="container mt-5">
            <div class="row d-flex justify-content-between m-auto">
                <!-- Left Column - Wallets -->
                <div class="col-lg-6 col-md-12 mb-4">
                    <!-- Main Wallet -->
                    <div class="portfolio-panel"
                        style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; position: relative; margin-bottom: 1rem;">
                        <!-- Header -->
                        <div
                            style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-wallet" style="color: #3b82f6; font-size: 0.875rem;"></i>
                                <span
                                    style="font-size: 0.875rem; font-weight: 500; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px;">Main Wallet</span>
                            </div>
                            <!-- Balance Badge -->
                            <div
                                style="background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 6px; padding: 0.35rem 0.6rem; display: flex; align-items: center; gap: 0.4rem;">
                                <i class="fas fa-dollar-sign" style="color: #3b82f6; font-size: 0.75rem;"></i>
                                <span
                                    style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary);">${{ number_format($mainBalance ?? 0, 2) }}</span>
                            </div>
                        </div>

                        <!-- Current Value -->
                        <div style="margin-bottom: 0.5rem;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); line-height: 1.2;">
                                ${{ number_format(($mainBalance ?? 0) + $portfolio, 2) }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">
                                Balance + Portfolio
                            </div>
                        </div>

                        <!-- Description -->
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 1rem;">
                            <i class="fas fa-info-circle" style="font-size: 0.7rem;"></i>
                            <span>For trading & deposits</span>
                        </div>

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

                    <!-- Earning Wallet -->
                    <div class="earning-panel"
                        style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; position: relative;">
                        <!-- Header -->
                        <div
                            style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-trophy" style="color: #10b981; font-size: 0.875rem;"></i>
                                <span
                                    style="font-size: 0.875rem; font-weight: 500; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px;">Earning Wallet</span>
                            </div>
                            <!-- Balance Badge -->
                            <div
                                style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 6px; padding: 0.35rem 0.6rem; display: flex; align-items: center; gap: 0.4rem;">
                                <i class="fas fa-coins" style="color: #10b981; font-size: 0.75rem;"></i>
                                <span
                                    style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary);">${{ number_format($earningBalance ?? 0, 2) }}</span>
                            </div>
                        </div>

                        <!-- Current Value -->
                        <div style="margin-bottom: 0.5rem;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); line-height: 1.2;">
                                ${{ number_format($earningBalance ?? 0, 2) }}
                            </div>
                        </div>

                        <!-- Description -->
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 1rem;">
                            <i class="fas fa-info-circle" style="font-size: 0.7rem;"></i>
                            <span>Trade wins, referrals & earnings</span>
                        </div>

                        <!-- Transfer Buttons -->
                        <div style="display: flex; gap: 0.75rem; {{ $transferHistoryCount > 0 ? '' : 'flex-direction: column;' }}">
                            <button onclick="transferEarningToMain()"
                                style="{{ $transferHistoryCount > 0 ? 'flex: 1;' : 'width: 100%;' }} background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; border: none; border-radius: 8px; padding: 0.75rem 1rem; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s;"
                                onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                                <i class="fas fa-exchange-alt"></i>
                                <span>Transfer to Main Wallet</span>
                            </button>
                            @if($transferHistoryCount > 0)
                            <button onclick="openTransferHistoryModal()"
                                style="flex: 1; background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border); border-radius: 8px; padding: 0.75rem 1rem; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s;"
                                onmouseover="this.style.background='var(--hover)'; this.style.borderColor='#10b981'; this.style.color='#10b981'"
                                onmouseout="this.style.background='var(--bg-secondary)'; this.style.borderColor='var(--border)'; this.style.color='var(--text-primary)'">
                                <i class="fas fa-history"></i>
                                <span>History</span>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Total Markets Chart  -->
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="profit-loss-panel"
                        style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; position: relative;">
                        <!-- Top Section -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                            <!-- Left: Profit/Loss Display -->
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                    <i class="fas fa-arrow-up" style="color: #10b981; font-size: 0.75rem;"></i>
                                    <span style="font-size: 0.75rem; font-weight: 500; color: var(--text-secondary);">Profit/Loss</span>
                                    <i class="fas fa-info-circle" style="color: var(--text-secondary); font-size: 0.7rem; cursor: pointer; opacity: 0.6;" 
                                       title="Your total profit/loss from trades"></i>
                                </div>
                                <div style="font-size: 2rem; font-weight: 700; color: {{ ($stats30Days['net_profit_loss'] ?? 0) >= 0 ? '#10b981' : '#ef4444' }}; line-height: 1.2; margin-bottom: 0.25rem;" id="profitLossValue">
                                    ${{ number_format($stats30Days['net_profit_loss'] ?? 0, 2) }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);" id="profitLossTimeframe">
                                    Last 30 Days
                                </div>
                            </div>

                            <!-- Right: Time Filters & Branding -->
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 1rem;">
                                <!-- Time Filter Buttons -->
                                <div style="display: flex; gap: 0.25rem; background: rgba(255, 255, 255, 0.05); border-radius: 6px; padding: 0.25rem;">
                                    <button class="pl-time-filter active" data-period="30" 
                                        style="padding: 0.4rem 0.6rem; font-size: 0.7rem; font-weight: 600; border: none; background: rgba(59, 130, 246, 0.2); color: #3b82f6; border-radius: 4px; cursor: pointer; transition: all 0.2s;"
                                        onmouseover="this.style.background='rgba(59, 130, 246, 0.3)'" 
                                        onmouseout="this.style.background='rgba(59, 130, 246, 0.2)'">
                                        30D
                                    </button>
                                    <button class="pl-time-filter" data-period="7"
                                        style="padding: 0.4rem 0.6rem; font-size: 0.7rem; font-weight: 600; border: none; background: transparent; color: var(--text-secondary); border-radius: 4px; cursor: pointer; transition: all 0.2s;"
                                        onmouseover="this.style.background='rgba(255, 255, 255, 0.1)'" 
                                        onmouseout="this.style.background='transparent'">
                                        7D
                                    </button>
                                    <button class="pl-time-filter" data-period="1"
                                        style="padding: 0.4rem 0.6rem; font-size: 0.7rem; font-weight: 600; border: none; background: transparent; color: var(--text-secondary); border-radius: 4px; cursor: pointer; transition: all 0.2s;"
                                        onmouseover="this.style.background='rgba(255, 255, 255, 0.1)'" 
                                        onmouseout="this.style.background='transparent'">
                                        1D
                                    </button>
                                    <button class="pl-time-filter" data-period="all"
                                        style="padding: 0.4rem 0.6rem; font-size: 0.7rem; font-weight: 600; border: none; background: transparent; color: var(--text-secondary); border-radius: 4px; cursor: pointer; transition: all 0.2s;"
                                        onmouseover="this.style.background='rgba(255, 255, 255, 0.1)'" 
                                        onmouseout="this.style.background='transparent'">
                                        ALL
                                    </button>
                                </div>

                                <!-- Polyrion Logo -->
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 3px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.65rem; color: #ffffff;">
                                        PM
                                    </div>
                                    <span style="font-size: 0.7rem; color: var(--text-secondary); font-weight: 500;">Polyrion</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom: Chart Bar Placeholder -->
                        <div style="height: 60px; position: relative; margin-top: 1rem; border-radius: 6px; overflow: hidden;">
                            <div id="profitLossChartBar" style="width: 100%; height: 100%; border-radius: 6px; position: relative;">
                                <!-- Chart will be rendered here -->
                                <canvas id="profitLossMiniChart" width="400" height="60" style="width: 100%; height: 100%; display: block;"></canvas>
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
                                                        // Handle both string (JSON) and array formats
                                                        $outcomePricesRaw = $position['market']->outcome_prices ?? null;
                                                        $outcomePrices = is_string($outcomePricesRaw) 
                                                            ? json_decode($outcomePricesRaw, true) 
                                                            : ($outcomePricesRaw ?? []);
                                                        
                                                        if (is_array($outcomePrices) && count($outcomePrices) >= 2) {
                                                            if ($trade->option === 'yes') {
                                                                $currentPrice = $outcomePrices[1] ?? $avgPrice; // YES is at index 1
                                                            } else {
                                                                $currentPrice = $outcomePrices[0] ?? $avgPrice; // NO is at index 0
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
                                                // Handle both string (JSON) and array formats
                                                $outcomePricesRaw = $position['market']->outcome_prices ?? null;
                                                $outcomePrices = is_string($outcomePricesRaw) 
                                                    ? json_decode($outcomePricesRaw, true) 
                                                    : ($outcomePricesRaw ?? []);
                                                
                                                if (is_array($outcomePrices) && count($outcomePrices) >= 2) {
                                                    if ($trade->option === 'yes') {
                                                        $currentPrice = $outcomePrices[1] ?? $avgPrice; // YES is at index 1
                                                    } else {
                                                        $currentPrice = $outcomePrices[0] ?? $avgPrice; // NO is at index 0
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

                                <!-- Commission History -->
                                <div>
                                    <h4 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between;">
                                        <span>Commission History</span>
                                        <span style="font-size: 0.875rem; font-weight: 500; color: var(--text-secondary);">
                                            Total: ${{ number_format($referralStats['total_commissions'] ?? 0, 2) }}
                                        </span>
                                    </h4>
                                    @if($referralCommissionHistory && $referralCommissionHistory->count() > 0)
                                    <div style="background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 8px; overflow: hidden;">
                                        <div style="max-height: 500px; overflow-y: auto;">
                                            <table style="width: 100%; border-collapse: collapse;">
                                                <thead style="position: sticky; top: 0; background: var(--card-bg); z-index: 10;">
                                                    <tr style="border-bottom: 1px solid var(--border);">
                                                        <th style="padding: 0.75rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Date</th>
                                                        <th style="padding: 0.75rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Referred User</th>
                                                        <th style="padding: 0.75rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Trade Amount</th>
                                                        <th style="padding: 0.75rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Level</th>
                                                        <th style="padding: 0.75rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Rate</th>
                                                        <th style="padding: 0.75rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Commission</th>
                                                        <th style="padding: 0.75rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($referralCommissionHistory as $commission)
                                                    <tr style="border-bottom: 1px solid var(--border); transition: background 0.2s;" onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='transparent'">
                                                        <td style="padding: 0.75rem; font-size: 0.875rem; color: var(--text-primary);">
                                                            {{ $commission->created_at->format('M d, Y') }}<br>
                                                            <span style="font-size: 0.75rem; color: var(--text-secondary);">{{ $commission->created_at->format('h:i A') }}</span>
                                                        </td>
                                                        <td style="padding: 0.75rem; font-size: 0.875rem; color: var(--text-primary);">
                                                            @if($commission->fromUser)
                                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                                    <i class="fas fa-user" style="color: var(--text-secondary); font-size: 0.75rem;"></i>
                                                                    <span>{{ $commission->fromUser->name ?? 'N/A' }}</span>
                                                                </div>
                                                                @if($commission->trade)
                                                                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">
                                                                        Trade #{{ $commission->trade->id }}
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <span style="color: var(--text-secondary);">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td style="padding: 0.75rem; font-size: 0.875rem; color: var(--text-primary);">
                                                            ${{ number_format($commission->trade_amount ?? ($commission->trade->amount_invested ?? $commission->trade->amount ?? 0), 2) }}
                                                        </td>
                                                        <td style="padding: 0.75rem; font-size: 0.875rem; color: var(--text-primary);">
                                                            <span style="background: rgba(255, 177, 26, 0.15); color: #ffb11a; padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">
                                                                Level {{ $commission->level }}
                                                            </span>
                                                        </td>
                                                        <td style="padding: 0.75rem; font-size: 0.875rem; color: var(--text-primary);">
                                                            {{ number_format($commission->percentage_applied, 2) }}%
                                                        </td>
                                                        <td style="padding: 0.75rem; font-size: 0.875rem; color: #10b981; font-weight: 600; text-align: right;">
                                                            +${{ number_format($commission->amount, 2) }}
                                                        </td>
                                                        <td style="padding: 0.75rem; text-align: center;">
                                                            @if($commission->status === 'completed')
                                                                <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">
                                                                    <i class="fas fa-check-circle"></i> Completed
                                                                </span>
                                                            @elseif($commission->status === 'pending')
                                                                <span style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">
                                                                    <i class="fas fa-clock"></i> Pending
                                                                </span>
                                                            @else
                                                                <span style="background: rgba(239, 68, 68, 0.15); color: #ef4444; padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">
                                                                    <i class="fas fa-times-circle"></i> Cancelled
                                                                </span>
                                                            @endif
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
                                        <p style="color: var(--text-secondary); margin-bottom: 0.5rem;">No commission history yet.</p>
                                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Commission is earned when your referred users place winning trades!</p>
                                    </div>
                                    @endif
                                </div>
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

    <!-- Transfer Modal -->
    <div class="transfer-modal-overlay" id="transferModalOverlay">
        <div class="transfer-modal-popup" id="transferModalPopup">
        <div class="transfer-modal-header">
            <h3>Transfer to Main Wallet</h3>
            <button type="button" class="transfer-modal-close" onclick="closeTransferModal()" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="transfer-modal-content">
            <form id="transferForm" class="transfer-form-container">
                <div class="transfer-balance-info">
                    <div class="balance-item">
                        <span class="balance-label">Available Balance</span>
                        <span class="balance-value" id="transferAvailableBalance">${{ number_format($earningBalance ?? 0, 2) }} USDT</span>
                    </div>
                </div>

                <div class="transfer-input-group">
                    <label class="transfer-input-label">Amount <span style="color: #ef4444;">*</span></label>
                    <div class="transfer-input-wrapper">
                        <span class="transfer-currency">$</span>
                        <input type="number" id="transferAmount" step="0.01" placeholder="0.00" class="transfer-input ps-4" min="0.01" max="{{ $earningBalance ?? 0 }}" value="{{ $earningBalance ?? 0 }}">
                    </div>
                    <p id="transferAmountError" style="color: #ef4444; font-size: 0.85rem; display: none; margin-top: 0.5rem;"></p>
                </div>

                <div class="transfer-quick-amounts">
                    <button type="button" class="quick-amount-btn" data-percent="25">25%</button>
                    <button type="button" class="quick-amount-btn" data-percent="50">50%</button>
                    <button type="button" class="quick-amount-btn" data-percent="75">75%</button>
                    <button type="button" class="quick-amount-btn" data-percent="100">100%</button>
                </div>

                <div class="transfer-input-group">
                    <label class="transfer-input-label">Wallet Password <span style="color: #ef4444;">*</span></label>
                    <div class="transfer-input-wrapper">
                        <input type="password" id="transferWalletPassword" class="transfer-input" placeholder="Enter your wallet password" required>
                        <button type="button" class="transfer-password-toggle" id="transferPasswordToggle" onclick="toggleTransferPassword()" aria-label="Toggle password visibility">
                            <i class="fas fa-eye" id="transferPasswordIcon"></i>
                        </button>
                    </div>
                    <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">
                        <i class="fas fa-info-circle"></i> Enter your wallet password to confirm transfer
                    </small>
                    <p id="transferPasswordError" style="color: #ef4444; font-size: 0.85rem; display: none; margin-top: 0.5rem;"></p>
                </div>

                <div class="transfer-submit-section">
                    <button type="submit" id="transferSubmitBtn" class="transfer-submit-btn">
                        <i class="fas fa-exchange-alt"></i> Transfer
                    </button>
                </div>
            </form>
        </div>
        </div>
    </div>

    <!-- Transfer History Modal -->
    <div class="transfer-modal-overlay" id="transferHistoryModalOverlay">
        <div class="transfer-modal-popup" id="transferHistoryModalPopup" style="max-width: 700px;">
            <div class="transfer-modal-header">
                <h3>Transfer History</h3>
                <button type="button" class="transfer-modal-close" onclick="closeTransferHistoryModal()" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="transfer-modal-content">
                <div id="transferHistoryContent" style="min-height: 200px;">
                    <div style="display: flex; justify-content: center; align-items: center; padding: 2rem;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Transfer Modal Styles - Matching Deposit Modal */
        .transfer-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9998;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .transfer-modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .transfer-modal-popup {
            background: var(--card-bg);
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            z-index: 9999;
            transform: scale(0.9) translateY(-20px);
            opacity: 0;
            transition: all 0.3s ease;
            position: relative;
            margin: auto;
        }

        .transfer-modal-overlay.active .transfer-modal-popup {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        .transfer-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 24px 20px;
            border-bottom: 1px solid var(--border);
        }

        .transfer-modal-header h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .transfer-modal-close {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
        }

        .transfer-modal-close:hover {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .transfer-modal-content {
            padding: 24px;
        }

        .transfer-balance-info {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .transfer-balance-info .balance-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .transfer-balance-info .balance-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .transfer-balance-info .balance-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #10b981;
        }

        .transfer-input-group {
            margin-bottom: 20px;
        }

        .transfer-input-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .transfer-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .transfer-currency {
            position: absolute;
            left: 16px;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-secondary);
            z-index: 1;
        }

        .transfer-input {
            width: 100%;
            padding: 14px 16px 14px 32px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            background: var(--card-bg);
            color: var(--text-primary);
            transition: all 0.2s;
        }

        .transfer-input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .transfer-input-wrapper input.transfer-input {
            padding-left: 16px;
            padding-right: 48px;
        }

        .transfer-password-toggle {
            position: absolute;
            right: 12px;
            background: transparent;
            border: none;
            color: var(--text-secondary);
            font-size: 1rem;
            cursor: pointer;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 6px;
            transition: all 0.2s;
            z-index: 2;
        }

        .transfer-password-toggle:hover {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .transfer-password-toggle:focus {
            outline: none;
            background: var(--bg-secondary);
        }

        .transfer-quick-amounts {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
        }

        .transfer-quick-amounts .quick-amount-btn {
            flex: 1;
            padding: 10px 16px;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s;
        }

        .transfer-quick-amounts .quick-amount-btn:hover {
            background: var(--hover);
            border-color: #10b981;
            color: #10b981;
        }

        .transfer-quick-amounts .quick-amount-btn.active {
            background: rgba(16, 185, 129, 0.15);
            border-color: #10b981;
            color: #10b981;
        }

        .transfer-submit-section {
            margin-top: 24px;
        }

        .transfer-submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .transfer-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }

        .transfer-submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .transfer-submit-btn i {
            font-size: 1rem;
        }
    </style>

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

        /* Positions Tab Fixed Height */
        #positions-tab {
            display: flex;
            flex-direction: column;
            max-height: 600px;
            overflow: hidden;
        }
        
        #positions-tab .positions-controls {
            flex-shrink: 0;
        }
        
        #positions-tab .positions-table-container,
        #positions-tab .positions-cards-container {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            min-height: 0;
        }
        
        #positions-tab .positions-table-container table thead {
            position: sticky;
            top: 0;
            z-index: 10;
            background: var(--card-bg);
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
                    const searchTerm = $(this).val().toLowerCase().trim();
                    const activeSubtab = $('.subtab-btn.active').data('subtab') || 'active';

                    $('.position-row').each(function() {
                        const $row = $(this);
                        const marketText = ($row.data('market') || '').toLowerCase();
                        const rowText = $row.text().toLowerCase();
                        const rowSubtab = $row.data('subtab');
                        const matchesSearch = !searchTerm || marketText.includes(searchTerm) || rowText.includes(searchTerm);
                        const matchesSubtab = activeSubtab === 'active' ? rowSubtab === 'active' :
                            rowSubtab === 'closed';

                        $row.toggle(matchesSearch && matchesSubtab);
                    });
                    
                    // Also filter mobile cards
                    $('.position-card').each(function() {
                        const $card = $(this);
                        const marketText = ($card.data('market') || '').toLowerCase();
                        const cardText = $card.text().toLowerCase();
                        const rowSubtab = $card.data('subtab');
                        const matchesSearch = !searchTerm || marketText.includes(searchTerm) || cardText.includes(searchTerm);
                        const matchesSubtab = activeSubtab === 'active' ? rowSubtab === 'active' :
                            rowSubtab === 'closed';

                        $card.toggle(matchesSearch && matchesSubtab);
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

                    // Handle Withdrawal Success - Livewire 3 format with named arguments
                    Livewire.on('withdrawal-submitted', (event) => {
                        // Livewire 3 named arguments are passed directly as object properties
                        let message = 'Withdrawal request submitted successfully! It will be reviewed by admin and processed within 24-48 hours.';
                        
                        // Handle different event formats
                        if (event && typeof event === 'object') {
                            // Livewire 3 named arguments format: event.message
                            if (event.message) {
                                message = event.message;
                            } 
                            // Array format: [0].message
                            else if (Array.isArray(event) && event[0] && event[0].message) {
                                message = event[0].message;
                            }
                            // Detail format: event.detail.message
                            else if (event.detail && event.detail.message) {
                                message = event.detail.message;
                            }
                        }
                        
                        console.log('Withdrawal submitted event received:', event, 'Message:', message);

                        setTimeout(() => {
                            if (typeof closeWithdrawalModal === 'function') {
                                closeWithdrawalModal();
                            }

                            // Use showSuccess function (now defined globally)
                            if (typeof showSuccess !== 'undefined') {
                                showSuccess(message, 'Withdrawal Submitted');
                            } else if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Withdrawal Submitted',
                                    text: message,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 4000,
                                    timerProgressBar: true,
                                    toast: true,
                                    confirmButtonColor: '#ffb11a',
                                });
                            } else if (typeof toastr !== 'undefined') {
                                toastr.success(message, 'Withdrawal Submitted');
                            } else {
                                alert(message);
                            }

                            // Reload after a delay to update balance
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }, 100);
                    });
                    
                    // Also listen after Livewire is fully initialized
                    document.addEventListener('livewire:initialized', () => {
                        window.Livewire.on('withdrawal-submitted', (event) => {
                            let message = 'Withdrawal request submitted successfully! It will be reviewed by admin and processed within 24-48 hours.';
                            
                            if (event && typeof event === 'object') {
                                if (event.message) {
                                    message = event.message;
                                } else if (Array.isArray(event) && event[0] && event[0].message) {
                                    message = event[0].message;
                                } else if (event.detail && event.detail.message) {
                                    message = event.detail.message;
                                }
                            }
                            
                            console.log('Withdrawal submitted event (initialized):', event, 'Message:', message);

                            setTimeout(() => {
                                if (typeof closeWithdrawalModal === 'function') {
                                    closeWithdrawalModal();
                                }

                                if (typeof showSuccess !== 'undefined') {
                                    showSuccess(message, 'Withdrawal Submitted');
                                } else if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Withdrawal Submitted',
                                        text: message,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 4000,
                                        timerProgressBar: true,
                                        toast: true,
                                        confirmButtonColor: '#ffb11a',
                                    });
                                } else if (typeof toastr !== 'undefined') {
                                    toastr.success(message, 'Withdrawal Submitted');
                                } else {
                                    alert(message);
                                }

                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            }, 100);
                        });
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
            
            // Debug: Log data structure (remove in production if needed)
            console.log('=== Profit/Loss Chart Debug ===');
            console.log('Data type:', typeof profitLossData);
            console.log('Is array:', Array.isArray(profitLossData));
            if (profitLossData && profitLossData.length > 0) {
                console.log('Profit/Loss data loaded:', profitLossData.length, 'data points');
                console.log('First data point:', profitLossData[0]);
                console.log('Last data point:', profitLossData[profitLossData.length - 1]);
            } else {
                console.log('No profit/loss data available');
                console.log('This could mean:');
                console.log('1. User has no trades yet');
                console.log('2. User has no completed trades');
                console.log('3. Data format issue');
            }
            console.log('==============================');

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
                console.log('initProfitLossChart called with timeframe:', timeframe);
                const ctx = document.getElementById('profitLossChart');
                if (!ctx) {
                    console.error('Profit/Loss chart canvas not found');
                    return;
                }
                
                if (typeof Chart === 'undefined') {
                    console.warn('Chart.js not loaded yet, retrying...');
                    setTimeout(() => initProfitLossChart(timeframe), 100);
                    return;
                }

                // Destroy existing chart if it exists
                if (profitLossChart) {
                    profitLossChart.destroy();
                    profitLossChart = null;
                }

                const chartData = getProfitLossDataForTimeframe(timeframe);
                console.log('Chart data for timeframe:', timeframe, chartData);

                // If no data, show zero and "No trades yet" message
                if (chartData.data.length === 0) {
                    console.log('No chart data available');
                    $('#profitLossAmount').text('$0.00');
                    $('#profitLossAmount').css('color', 'var(--text-primary)');
                    $('#profitLossNoData').show();
                    
                    // Create empty chart (hidden, but needed to prevent errors)
                    if (profitLossChart) {
                        profitLossChart.destroy();
                        profitLossChart = null;
                    }
                    try {
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
                    } catch (error) {
                        console.error('Error creating empty chart:', error);
                    }
                    return;
                }
                
                // Hide "No trades yet" message if data exists
                $('#profitLossNoData').hide();

                // Calculate total profit/loss for the selected timeframe
                // The data is cumulative, so we show the last value (total cumulative profit/loss)
                const lastValue = chartData.data[chartData.data.length - 1] || 0;

                // Update amount display with the cumulative profit/loss
                const sign = lastValue >= 0 ? '' : '';
                $('#profitLossAmount').text(sign + '$' + Math.abs(lastValue).toFixed(2));
                
                // Update color based on profit/loss
                if (lastValue >= 0) {
                    $('#profitLossAmount').css('color', '#10b981');
                } else {
                    $('#profitLossAmount').css('color', '#ef4444');
                }

                // Determine chart color based on profit/loss
                const isPositive = lastValue >= 0;
                const borderColor = isPositive ? '#10b981' : '#ef4444'; // Green for profit, red for loss
                const backgroundColor = isPositive ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)';

                try {
                    profitLossChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: chartData.labels,
                            datasets: [{
                                label: 'Profit/Loss',
                                data: chartData.data,
                                borderColor: borderColor,
                                backgroundColor: backgroundColor,
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 0,
                                pointHoverRadius: 5,
                                pointHoverBackgroundColor: borderColor,
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
                                    backgroundColor: 'rgba(0, 0, 0, 0.9)',
                                    titleColor: '#ffffff',
                                    bodyColor: borderColor,
                                    borderColor: borderColor,
                                    borderWidth: 1,
                                    padding: 12,
                                    displayColors: false,
                                    callbacks: {
                                        label: function(context) {
                                            const value = context.parsed.y;
                                            const sign = value >= 0 ? '+' : '';
                                            return sign + '$' + value.toFixed(2);
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    display: false,
                                    grid: {
                                        display: false
                                    }
                                },
                                y: {
                                    display: false,
                                    grid: {
                                        display: false
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
                                    hoverRadius: 5
                                }
                            }
                        }
                    });
                } catch (error) {
                    console.error('Error creating profit/loss chart:', error);
                    $('#profitLossNoData').show();
                    $('#profitLossNoData').html('<i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i><div>Error loading chart</div>');
                }
            }

            function updateProfitLoss(timeframe) {
                const timeframes = {
                    '1D': 'Past Day',
                    '1W': 'Past Week',
                    '1M': 'Past Month',
                    'ALL': 'All Time'
                };

                $('#profitLossTimeframe').text(timeframes[timeframe] || 'Past Day');

                // Update chart with new timeframe data (initProfitLossChart already updates the display)
                // Don't call it again here to avoid duplicate initialization
            }

            // Initialize chart on page load
            function initializeChart() {
                console.log('Initializing profit/loss chart...');
                console.log('Chart.js available:', typeof Chart !== 'undefined');
                console.log('Profit/Loss data:', profitLossData);
                
                // Wait for Chart.js to load
                let attempts = 0;
                const maxAttempts = 40; // Increased to 2 seconds
                
                function checkChartJS() {
                    const ctx = document.getElementById('profitLossChart');
                    if (!ctx) {
                        console.warn('Chart canvas element not found, retrying...');
                        if (attempts < maxAttempts) {
                            attempts++;
                            setTimeout(checkChartJS, 50);
                        } else {
                            console.error('Chart canvas not found after 2 seconds');
                            $('#profitLossNoData').show();
                            $('#profitLossNoData').html('<i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i><div>Chart element not found</div>');
                        }
                        return;
                    }
                    
                    if (typeof Chart !== 'undefined') {
                        console.log('Chart.js loaded, initializing chart...');
                        // Initialize with '1M' to match the active button
                        try {
                            initProfitLossChart('1M');
                            updateProfitLoss('1M');
                            console.log('Chart initialized successfully');
                        } catch (error) {
                            console.error('Error initializing chart:', error);
                            $('#profitLossNoData').show();
                            $('#profitLossNoData').html('<i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i><div>Error loading chart</div>');
                        }
                    } else {
                        // Retry after 50ms, but limit to maxAttempts
                        if (attempts < maxAttempts) {
                            attempts++;
                            setTimeout(checkChartJS, 50);
                        } else {
                            console.error('Chart.js failed to load after 2 seconds');
                            $('#profitLossNoData').show();
                            $('#profitLossNoData').html('<i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i><div>Chart library not loaded</div>');
                        }
                    }
                }
                checkChartJS();
            }
            
            // Try multiple initialization methods
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeChart);
            } else {
                // DOM already loaded
                initializeChart();
            }
            
            // Also try on window load as fallback (only if chart not initialized)
            let chartInitialized = false;
            window.addEventListener('load', function() {
                if (!profitLossChart && !chartInitialized) {
                    console.log('Window loaded, retrying chart initialization...');
                    chartInitialized = true;
                    initializeChart();
                }
            });
        </script>

        <script>
            // Trade Stats Time Filter Handler
            (function() {
                // Store stats data
                const statsData = {
                    '30': @json($stats30Days ?? []),
                    '7': null, // Will be loaded via AJAX
                    'all': null // Will be loaded via AJAX
                };

                // Update stats display
                function updateStatsDisplay(period) {
                    const stats = statsData[period];
                    if (!stats) {
                        // Load stats via AJAX
                        loadStatsForPeriod(period);
                        return;
                    }

                    // Update all stat elements
                    document.getElementById('winTradesCount').textContent = stats.win_trades || 0;
                    document.getElementById('winTradesAmount').textContent = '$' + parseFloat(stats.total_payout || 0).toFixed(2);
                    document.getElementById('lossTradesCount').textContent = stats.loss_trades || 0;
                    document.getElementById('lossTradesAmount').textContent = '$' + parseFloat(stats.total_loss || 0).toFixed(2);
                    document.getElementById('netProfitLoss').textContent = '$' + parseFloat(stats.net_profit_loss || 0).toFixed(2);
                    document.getElementById('netProfitLoss').style.color = (stats.net_profit_loss || 0) >= 0 ? '#10b981' : '#ef4444';
                    document.getElementById('totalProfit').textContent = '$' + parseFloat(stats.total_profit || 0).toFixed(2);
                    document.getElementById('totalLoss').textContent = '$' + parseFloat(stats.total_loss || 0).toFixed(2);
                    document.getElementById('totalTradesCount').textContent = stats.total_trades || 0;
                    document.getElementById('pendingTradesCount').textContent = stats.pending_trades || 0;
                    document.getElementById('winRateDisplay').textContent = 'Win Rate: ' + (stats.win_rate || 0) + '%';
                }

                // Load stats for a specific period
                function loadStatsForPeriod(period) {
                    fetch('{{ route("profile.index") }}?stats_period=' + period, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.stats) {
                            statsData[period] = data.stats;
                            updateStatsDisplay(period);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading stats:', error);
                    });
                }

                // Handle time filter button clicks
                document.querySelectorAll('.time-filter-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const period = this.getAttribute('data-period');
                        
                        // Update active state
                        document.querySelectorAll('.time-filter-btn').forEach(b => {
                            b.classList.remove('active');
                            b.style.background = 'transparent';
                            b.style.color = 'var(--text-secondary)';
                            b.style.borderColor = 'var(--border)';
                        });
                        
                        this.classList.add('active');
                        this.style.background = period === '30' ? 'rgba(59, 130, 246, 0.15)' : 'rgba(16, 185, 129, 0.15)';
                        this.style.color = period === '30' ? '#3b82f6' : '#10b981';
                        this.style.borderColor = period === '30' ? 'rgba(59, 130, 246, 0.3)' : 'rgba(16, 185, 129, 0.3)';
                        
                        // Update stats
                        updateStatsDisplay(period);
                    });
                });
            })();
        </script>

        <script>
            // Markets Chart Initialization (kept for backward compatibility if needed elsewhere)
            let marketsChart = null;
            
            function initMarketsChart() {
                const ctx = document.getElementById('marketsChart');
                if (!ctx) {
                    // Chart element removed, skip initialization
                    return;
                }

                if (typeof Chart === 'undefined') {
                    console.warn('Chart.js not loaded yet, retrying...');
                    setTimeout(() => initMarketsChart(), 100);
                    return;
                }

                // Destroy existing chart if any
                if (marketsChart) {
                    marketsChart.destroy();
                    marketsChart = null;
                }

                try {
                    // Chart data from backend
                    const chartData = @json($marketsChartData ?? ['labels' => [], 'markets' => []]);
                    
                    // Create gradient
                    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 200);
                    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
                    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.05)');

                    marketsChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: chartData.labels || [],
                            datasets: [{
                                label: 'Markets',
                                data: chartData.markets || [],
                                borderColor: '#10b981',
                                backgroundColor: gradient,
                                fill: true,
                                tension: 0.5,
                                borderWidth: 2.5,
                                pointRadius: 0,
                                pointHoverRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: { 
                                    enabled: true,
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 8,
                                    titleFont: { size: 11 },
                                    bodyFont: { size: 11 },
                                    cornerRadius: 6,
                                    displayColors: false
                                }
                            },
                            scales: {
                                x: { 
                                    display: false,
                                    grid: { display: false }
                                },
                                y: { 
                                    display: false,
                                    grid: { display: false }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            }
                        }
                    });
                    console.log('Markets chart initialized successfully');
                } catch (e) {
                    console.error('Error initializing Markets chart:', e);
                }
            }

            // Initialize markets chart when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(initMarketsChart, 500); // Wait a bit for Chart.js to load
                });
            } else {
                setTimeout(initMarketsChart, 500);
            }

            // Also try on window load
            window.addEventListener('load', function() {
                if (!marketsChart) {
                    setTimeout(initMarketsChart, 500);
                }
            });
        </script>

        <script>
            // Transfer Modal Functions
            const earningBalance = {{ $earningBalance ?? 0 }};
            
            function openTransferModal() {
                const modal = document.getElementById('transferModalPopup');
                const overlay = document.getElementById('transferModalOverlay');
                
                if (earningBalance <= 0) {
                    if (typeof showError !== 'undefined') {
                        showError('No balance available in earning wallet.', 'Transfer Failed');
                    } else if (typeof toastr !== 'undefined') {
                        toastr.error('No balance available in earning wallet.', 'Transfer Failed');
                    } else {
                        alert('No balance available in earning wallet.');
                    }
                    return;
                }
                
                // Reset form
                document.getElementById('transferAmount').value = earningBalance;
                document.getElementById('transferWalletPassword').value = '';
                document.getElementById('transferAmountError').style.display = 'none';
                document.getElementById('transferPasswordError').style.display = 'none';
                document.querySelectorAll('.quick-amount-btn').forEach(btn => btn.classList.remove('active'));
                
                modal.classList.add('active');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            
            function closeTransferModal() {
                const modal = document.getElementById('transferModalPopup');
                const overlay = document.getElementById('transferModalOverlay');
                
                modal.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
            
            // Make functions globally accessible
            window.transferEarningToMain = openTransferModal;
            window.openTransferModal = openTransferModal;
            window.closeTransferModal = closeTransferModal;
            
            // Initialize event listeners when DOM is ready
            document.addEventListener('DOMContentLoaded', function() {
                // Quick amount buttons
                document.querySelectorAll('.transfer-quick-amounts .quick-amount-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const percent = parseFloat(this.getAttribute('data-percent'));
                        const amount = (earningBalance * percent / 100).toFixed(2);
                        document.getElementById('transferAmount').value = amount;
                        document.querySelectorAll('.quick-amount-btn').forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                    });
                });
                
                // Close modal on overlay click
                document.getElementById('transferModalOverlay').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeTransferModal();
                    }
                });
                
                // Close modal on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && document.getElementById('transferModalPopup').classList.contains('active')) {
                        closeTransferModal();
                    }
                });
                
                // Form submission
                document.getElementById('transferForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const amount = parseFloat(document.getElementById('transferAmount').value);
                    const walletPassword = document.getElementById('transferWalletPassword').value;
                    
                    // Validation
                    let isValid = true;
                    const amountError = document.getElementById('transferAmountError');
                    const passwordError = document.getElementById('transferPasswordError');
                    
                    amountError.style.display = 'none';
                    passwordError.style.display = 'none';
                    
                    if (!amount || amount <= 0) {
                        amountError.textContent = 'Please enter a valid amount';
                        amountError.style.display = 'block';
                        isValid = false;
                    } else if (amount > earningBalance) {
                        amountError.textContent = `Amount cannot exceed available balance: $${earningBalance.toFixed(2)}`;
                        amountError.style.display = 'block';
                        isValid = false;
                    }
                    
                    if (!walletPassword || walletPassword.trim() === '') {
                        passwordError.textContent = 'Wallet password is required';
                        passwordError.style.display = 'block';
                        isValid = false;
                    }
                    
                    if (!isValid) {
                        return;
                    }
                    
                    // Disable submit button
                    const btn = document.getElementById('transferSubmitBtn');
                    const originalHtml = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                    
                    // Perform transfer
                    performTransfer(amount, walletPassword, btn, originalHtml);
                });
            });
            
            function transferEarningToMain() {
                openTransferModal();
            }
            
            // Toggle password visibility
            function toggleTransferPassword() {
                const passwordInput = document.getElementById('transferWalletPassword');
                const passwordIcon = document.getElementById('transferPasswordIcon');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    passwordIcon.classList.remove('fa-eye');
                    passwordIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    passwordIcon.classList.remove('fa-eye-slash');
                    passwordIcon.classList.add('fa-eye');
                }
            }
            
            // Make function globally accessible
            window.toggleTransferPassword = toggleTransferPassword;
            
            // Transfer History Modal Functions
            function openTransferHistoryModal() {
                const modal = document.getElementById('transferHistoryModalPopup');
                const overlay = document.getElementById('transferHistoryModalOverlay');
                
                overlay.classList.add('active');
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                
                // Load transfer history
                loadTransferHistory();
            }
            
            function closeTransferHistoryModal() {
                const modal = document.getElementById('transferHistoryModalPopup');
                const overlay = document.getElementById('transferHistoryModalOverlay');
                
                modal.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
            
            function loadTransferHistory() {
                const content = document.getElementById('transferHistoryContent');
                content.innerHTML = '<div style="display: flex; justify-content: center; align-items: center; padding: 2rem;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
                
                fetch('{{ route("wallet.transfer.history") }}', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderTransferHistory(data.history);
                    } else {
                        content.innerHTML = '<div style="text-align: center; padding: 2rem; color: var(--text-secondary);">Failed to load transfer history.</div>';
                    }
                })
                .catch(error => {
                    console.error('Transfer history error:', error);
                    content.innerHTML = '<div style="text-align: center; padding: 2rem; color: var(--text-secondary);">Failed to load transfer history.</div>';
                });
            }
            
            function renderTransferHistory(history) {
                const content = document.getElementById('transferHistoryContent');
                
                if (!history || history.length === 0) {
                    content.innerHTML = '<div style="text-align: center; padding: 2rem; color: var(--text-secondary);">No transfer history found.</div>';
                    return;
                }
                
                let html = '<div style="max-height: 500px; overflow-y: auto;">';
                html += '<table style="width: 100%; border-collapse: collapse;">';
                html += '<thead style="position: sticky; top: 0; background: var(--card-bg); z-index: 10; border-bottom: 2px solid var(--border);">';
                html += '<tr>';
                html += '<th style="padding: 1rem; text-align: left; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Type</th>';
                html += '<th style="padding: 1rem; text-align: right; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Amount</th>';
                html += '<th style="padding: 1rem; text-align: right; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Balance After</th>';
                html += '<th style="padding: 1rem; text-align: left; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Date</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';
                
                history.forEach(function(item) {
                    const isTransferOut = item.type === 'transfer_out';
                    const typeColor = isTransferOut ? '#ef4444' : '#10b981';
                    const typeIcon = isTransferOut ? 'fa-arrow-up' : 'fa-arrow-down';
                    const typeText = isTransferOut ? 'Transfer Out' : 'Transfer In';
                    
                    html += '<tr style="border-bottom: 1px solid var(--border);">';
                    html += '<td style="padding: 1rem;">';
                    html += '<div style="display: flex; align-items: center; gap: 0.5rem;">';
                    html += '<i class="fas ' + typeIcon + '" style="color: ' + typeColor + ';"></i>';
                    html += '<span style="font-weight: 600; color: var(--text-primary);">' + typeText + '</span>';
                    html += '</div>';
                    html += '</td>';
                    html += '<td style="padding: 1rem; text-align: right;">';
                    html += '<span style="font-weight: 700; color: ' + typeColor + '; font-size: 1rem;">';
                    html += (isTransferOut ? '-' : '+') + '$' + parseFloat(item.amount).toFixed(2);
                    html += '</span>';
                    html += '</td>';
                    html += '<td style="padding: 1rem; text-align: right; color: var(--text-secondary);">';
                    html += '$' + parseFloat(item.balance_after).toFixed(2);
                    html += '</td>';
                    html += '<td style="padding: 1rem;">';
                    html += '<div style="color: var(--text-primary); font-weight: 500;">' + item.created_at + '</div>';
                    html += '<div style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 0.25rem;">' + item.created_at_human + '</div>';
                    html += '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody>';
                html += '</table>';
                html += '</div>';
                
                content.innerHTML = html;
            }
            
            // Make functions globally accessible
            window.openTransferHistoryModal = openTransferHistoryModal;
            window.closeTransferHistoryModal = closeTransferHistoryModal;
            
            // Close modal on overlay click
            document.addEventListener('DOMContentLoaded', function() {
                const historyOverlay = document.getElementById('transferHistoryModalOverlay');
                if (historyOverlay) {
                    historyOverlay.addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeTransferHistoryModal();
                        }
                    });
                }
                
                // Close modal on Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && document.getElementById('transferHistoryModalPopup') && document.getElementById('transferHistoryModalPopup').classList.contains('active')) {
                        closeTransferHistoryModal();
                    }
                });
            });

            function performTransfer(amount, walletPassword, btn, originalHtml) {
                // Make API call
                fetch('{{ route("wallet.transfer.earning.to.main") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        amount: amount,
                        wallet_password: walletPassword
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Network error occurred');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Close modal
                        closeTransferModal();
                        
                        // Show success message
                        if (typeof showSuccess !== 'undefined') {
                            showSuccess(`$${amount.toFixed(2)} successfully transferred to Main Wallet`, 'Transfer Successful');
                        } else if (typeof toastr !== 'undefined') {
                            toastr.success(`$${amount.toFixed(2)} successfully transferred to Main Wallet`, 'Transfer Successful');
                        } else {
                            alert(`Transfer successful! $${amount.toFixed(2)} transferred to Main Wallet`);
                        }
                        
                        // Reload page after delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        // Re-enable button
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = originalHtml;
                        }
                        
                        // Show error
                        const passwordError = document.getElementById('transferPasswordError');
                        const amountError = document.getElementById('transferAmountError');
                        
                        if (data.message && data.message.includes('password')) {
                            if (passwordError) {
                                passwordError.textContent = data.message;
                                passwordError.style.display = 'block';
                            }
                        } else {
                            if (amountError) {
                                amountError.textContent = data.message || 'Transfer failed. Please try again.';
                                amountError.style.display = 'block';
                            }
                        }
                        
                        if (typeof showError !== 'undefined') {
                            showError(data.message || 'Transfer failed. Please try again.', 'Transfer Failed');
                        } else if (typeof toastr !== 'undefined') {
                            toastr.error(data.message || 'Transfer failed. Please try again.', 'Transfer Failed');
                        } else {
                            alert('Transfer failed: ' + (data.message || 'An error occurred. Please try again.'));
                        }
                    }
                })
                .catch(error => {
                    console.error('Transfer error:', error);
                    
                    // Re-enable button
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    }
                    
                    // Show error
                    const amountError = document.getElementById('transferAmountError');
                    const passwordError = document.getElementById('transferPasswordError');
                    
                    if (amountError) {
                        amountError.textContent = error.message || 'Network error. Please try again.';
                        amountError.style.display = 'block';
                    }
                    
                    if (passwordError) {
                        passwordError.style.display = 'none';
                    }
                    
                    if (typeof showError !== 'undefined') {
                        showError(error.message || 'Network error. Please check your connection and try again.', 'Transfer Failed');
                    } else if (typeof toastr !== 'undefined') {
                        toastr.error(error.message || 'Network error. Please try again.', 'Transfer Failed');
                    } else {
                        alert('Transfer failed: ' + (error.message || 'Please try again.'));
                    }
                });
            }
        </script>

        <script>
            // Profit/Loss Time Filter Handler with Real Data and Chart
            (function() {
                // Store stats and chart data
                const statsData = {
                    '30': @json($stats30Days ?? []),
                    '7': null,
                    '1': null,
                    'all': null
                };

                const chartData = {
                    '30': @json($initialChartData30Days ?? []),
                    '7': null,
                    '1': null,
                    'all': null
                };

                const timeframeMap = {
                    '1': 'Past Day',
                    '7': 'Past Week',
                    '30': 'Last 30 Days',
                    'all': 'All Time'
                };

                let miniChart = null;

                // Update stats and chart display
                function updateStatsDisplay(period) {
                    const stats = statsData[period];
                    const chart = chartData[period];
                    
                    if (!stats || !chart) {
                        // Load stats and chart via AJAX
                        loadStatsForPeriod(period);
                        return;
                    }

                    // Update profit/loss value
                    const netPL = parseFloat(stats.net_profit_loss || 0);
                    const plElement = document.getElementById('profitLossValue');
                    if (plElement) {
                        plElement.textContent = '$' + netPL.toFixed(2);
                        plElement.style.color = netPL >= 0 ? '#10b981' : '#ef4444';
                    }

                    // Update timeframe text
                    const timeframeElement = document.getElementById('profitLossTimeframe');
                    if (timeframeElement) {
                        timeframeElement.textContent = timeframeMap[period] || 'Last 30 Days';
                    }

                    // Update mini chart with real data
                    updateMiniChart(chart, stats, period);
                }

                // Update mini chart with real data
                function updateMiniChart(chartDataArray, stats, period) {
                    console.log('updateMiniChart called', { chartDataArray, stats, period });
                    
                    const canvas = document.getElementById('profitLossMiniChart');
                    if (!canvas) {
                        console.error('Canvas element not found: profitLossMiniChart');
                        return;
                    }

                    if (typeof Chart === 'undefined') {
                        console.warn('Chart.js not loaded, retrying...');
                        // Retry after Chart.js loads
                        setTimeout(() => updateMiniChart(chartDataArray, stats, period), 100);
                        return;
                    }

                    // Destroy existing chart
                    if (miniChart) {
                        miniChart.destroy();
                        miniChart = null;
                    }

                    if (!chartDataArray || chartDataArray.length === 0) {
                        console.warn('No chart data available');
                        // Show placeholder or empty state
                        const ctx = canvas.getContext('2d');
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        ctx.fillStyle = 'rgba(255, 255, 255, 0.1)';
                        ctx.font = '12px sans-serif';
                        ctx.textAlign = 'center';
                        ctx.fillText('No data available', canvas.width / 2, canvas.height / 2);
                        return;
                    }

                    console.log('Chart data:', chartDataArray);

                    // Extract labels and values from chart data
                    const labels = chartDataArray.map(item => item.label || item.date || '');
                    const values = chartDataArray.map(item => parseFloat(item.value || 0));

                    console.log('Extracted data:', { labels, values });

                    // Determine color based on final value
                    const finalValue = values[values.length - 1] || 0;
                    const borderColor = finalValue >= 0 ? '#10b981' : '#ef4444';
                    
                    // Ensure canvas has proper dimensions
                    const container = canvas.parentElement;
                    if (container) {
                        const rect = container.getBoundingClientRect();
                        if (rect.width > 0 && rect.height > 0) {
                            canvas.width = rect.width;
                            canvas.height = rect.height;
                        }
                    }
                    
                    // Create gradient like dashboard chart
                    const ctx = canvas.getContext('2d');
                    const chartHeight = canvas.height || 60;
                    const gradient = ctx.createLinearGradient(0, 0, 0, chartHeight);
                    if (finalValue >= 0) {
                        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
                        gradient.addColorStop(1, 'rgba(16, 185, 129, 0.05)');
                    } else {
                        gradient.addColorStop(0, 'rgba(239, 68, 68, 0.3)');
                        gradient.addColorStop(1, 'rgba(239, 68, 68, 0.05)');
                    }

                    console.log('Creating chart with data:', { labels: labels.length, values: values.length });

                    // Dashboard-style chart configuration
                    try {
                        miniChart = new Chart(canvas, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'P/L',
                                data: values,
                                borderColor: borderColor,
                                backgroundColor: gradient,
                                fill: true,
                                tension: 0.5,
                                borderWidth: 2.5,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: borderColor,
                                pointBorderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: { 
                                    enabled: true,
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 8,
                                    titleFont: { size: 11 },
                                    bodyFont: { size: 11 },
                                    cornerRadius: 6,
                                    displayColors: false,
                                    callbacks: {
                                        label: function(context) {
                                            return '$' + parseFloat(context.parsed.y).toFixed(2);
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: { 
                                    display: false,
                                    grid: { display: false }
                                },
                                y: { 
                                    display: false,
                                    grid: { display: false }
                                }
                            },
                            elements: {
                                point: { 
                                    radius: 0,
                                    hoverRadius: 4
                                },
                                line: { 
                                    borderWidth: 2.5,
                                    tension: 0.5
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },
                            animation: {
                                duration: 1500,
                                easing: 'easeInOutQuart'
                            }
                        }
                    });
                    console.log('Chart created successfully');
                    } catch (error) {
                        console.error('Error creating chart:', error);
                    }
                }

                // Load stats and chart for a specific period
                function loadStatsForPeriod(period) {
                    fetch('{{ route("profile.index") }}?stats_period=' + period, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.stats) {
                            statsData[period] = data.stats;
                        }
                        if (data.chartData) {
                            chartData[period] = data.chartData;
                        }
                        updateStatsDisplay(period);
                    })
                    .catch(error => {
                        console.error('Error loading stats:', error);
                    });
                }

                // Handle time filter button clicks
                const plFilters = document.querySelectorAll('.pl-time-filter');
                plFilters.forEach(btn => {
                    btn.addEventListener('click', function() {
                        // Remove active class from all buttons
                        plFilters.forEach(b => {
                            b.classList.remove('active');
                            b.style.background = 'transparent';
                            b.style.color = 'var(--text-secondary)';
                        });
                        
                        // Add active class to clicked button
                        this.classList.add('active');
                        this.style.background = 'rgba(59, 130, 246, 0.2)';
                        this.style.color = '#3b82f6';

                        const period = this.getAttribute('data-period');
                        
                        // Update stats and chart
                        updateStatsDisplay(period);
                    });
                });

                // Initialize with 30 days data on page load
                function initializeChart() {
                    console.log('Initializing profit/loss chart...');
                    const canvas = document.getElementById('profitLossMiniChart');
                    if (!canvas) {
                        console.error('Canvas not found, retrying...');
                        setTimeout(initializeChart, 200);
                        return;
                    }

                    if (typeof Chart === 'undefined') {
                        console.warn('Chart.js not loaded, waiting...');
                        setTimeout(initializeChart, 200);
                        return;
                    }

                    const stats = statsData['30'];
                    const chart = chartData['30'];
                    
                    console.log('Initial data:', { stats, chart });
                    
                    if (chart && chart.length > 0) {
                        updateMiniChart(chart, stats || {}, '30');
                    } else if (stats) {
                        // If we have stats but no chart data, try to load it
                        console.log('No initial chart data, loading...');
                        loadStatsForPeriod('30');
                    } else {
                        console.warn('No initial data available');
                    }
                }

                // Try multiple initialization methods
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function() {
                        setTimeout(initializeChart, 500);
                    });
                } else {
                    setTimeout(initializeChart, 500);
                }

                // Also try on window load
                window.addEventListener('load', function() {
                    setTimeout(initializeChart, 1000);
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


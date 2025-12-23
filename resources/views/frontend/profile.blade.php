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
               style="background: var(--card-bg); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 1.25rem; position: relative;">
               <!-- Header -->
               <div
                  style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                  <div style="display: flex; align-items: center; gap: 0.5rem;">
                     <i class="fas fa-ban" style="color: #9ca3af; font-size: 0.875rem;"></i>
                     <span
                        style="font-size: 0.875rem; font-weight: 500; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px;">Portfolio</span>
                  </div>
                  <!-- Balance Badge -->
                  <div
                     style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 6px; padding: 0.35rem 0.6rem; display: flex; align-items: center; gap: 0.4rem;">
                     <i class="fas fa-stack" style="color: #10b981; font-size: 0.75rem;"></i>
                     <span
                        style="font-size: 0.8rem; font-weight: 600; color: #ffffff;">${{ number_format($balance, 2) }}</span>
                  </div>
               </div>

               <!-- Current Value -->
               <div style="margin-bottom: 0.5rem;">
                  <div style="font-size: 1.75rem; font-weight: 700; color: #ffffff; line-height: 1.2;">
                     ${{ number_format($stats['positions_value'], 2) }}
                  </div>
               </div>

               <!-- Timeframe -->
               <div style="font-size: 0.8rem; color: #9ca3af; margin-bottom: 1.5rem;">Today</div>

               <!-- Action Buttons -->
               <div style="display: flex; gap: 0.75rem;">
                  <button
                     onclick="if(typeof openDepositModal === 'function') { openDepositModal(); } else if(typeof window.openDepositModal === 'function') { window.openDepositModal(); }"
                     style="flex: 1; background: #3b82f6; color: #ffffff; border: none; border-radius: 8px; padding: 0.75rem 1rem; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s;"
                     onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
                     <i class="fas fa-arrow-down"></i>
                     <span>Deposit</span>
                  </button>
                  <button onclick="openWithdrawalModal()"
                     style="flex: 1; background: rgba(255, 255, 255, 0.05); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; padding: 0.75rem 1rem; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s;"
                     onmouseover="this.style.background='rgba(255, 255, 255, 0.1)'"
                     onmouseout="this.style.background='rgba(255, 255, 255, 0.05)'">
                     <i class="fas fa-arrow-up"></i>
                     <span>Withdraw</span>
                  </button>
               </div>
            </div>
         </div>

         <!-- Right Column - Profit/Loss  -->
         <div class="col-lg-6 col-md-12 mb-4">
            <div class="profit-loss-panel"
               style="background: var(--card-bg); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 1.25rem; position: relative;">
               <!-- Header -->
               <div
                  style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                  <div style="display: flex; align-items: center; gap: 0.5rem;">
                     <i class="fas fa-arrow-up" style="color: #10b981; font-size: 0.875rem;"></i>
                     <span
                        style="font-size: 0.875rem; font-weight: 500; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px;">Profit/Loss</span>
                  </div>
                  <!-- Time Filters -->
                  <div style="display: flex; gap: 0.4rem;">
                     <button type="button" class="pl-time-filter" data-pl-period="1D"
                        style="padding: 0.35rem 0.6rem; background: transparent; color: #9ca3af; border: none; border-radius: 4px; font-weight: 500; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;">1D</button>
                     <button type="button" class="pl-time-filter" data-pl-period="1W"
                        style="padding: 0.35rem 0.6rem; background: transparent; color: #9ca3af; border: none; border-radius: 4px; font-weight: 500; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;">1W</button>
                     <button type="button" class="pl-time-filter active" data-pl-period="1M"
                        style="padding: 0.35rem 0.6rem; background: rgba(59, 130, 246, 0.2); color: #ffffff; border: none; border-radius: 4px; font-weight: 600; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;">1M</button>
                     <button type="button" class="pl-time-filter" data-pl-period="ALL"
                        style="padding: 0.35rem 0.6rem; background: transparent; color: #9ca3af; border: none; border-radius: 4px; font-weight: 500; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;">ALL</button>
                  </div>
               </div>

               <!-- Current Value -->
               <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                  <div style="font-size: 1.75rem; font-weight: 700; color: #ffffff; line-height: 1.2;">
                     <span id="profitLossAmount">${{ number_format($stats['total_profit_loss'] ?? 0, 2) }}</span>
                  </div>
                  <i class="fas fa-info-circle" style="color: #9ca3af; font-size: 0.875rem; cursor: pointer;"
                     title="Profit/Loss for the selected time period"></i>
               </div>

               <!-- Timeframe -->
               <div style="font-size: 0.8rem; color: #9ca3af; margin-bottom: 1rem;" id="profitLossTimeframe">Past
                  Month</div>

               <!-- Polyrion Logo -->
               <div
                  style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                  <div
                     style="width: 24px; height: 24px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 4px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.7rem; color: #ffffff;">
                     PM</div>
                  <span style="font-size: 0.75rem; color: #9ca3af; font-weight: 500;">Polyrion</span>
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
                        style="background: var(--card-bg); border-top: 1px solid rgba(255, 255, 255, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding: 1rem; margin-bottom: 0; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 1rem;">
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
                              style="font-weight: 700; font-size: 0.95rem; color: #ffffff; margin-bottom: 0.25rem; line-height: 1.3;">
                              Trade #{{ $tradeNumber }} - {{ $firstPart }}
                           </div>

                           @if ($secondPart)
                           <!-- Second Line -->
                           <div
                              style="font-weight: 700; font-size: 0.95rem; color: #ffffff; margin-bottom: 0.25rem; line-height: 1.3;">
                              {{ $secondPart }}
                           </div>
                           @endif

                           <!-- Date, Time and Option -->
                           <div
                              style="font-size: 0.8rem; color: #9ca3af; margin-top: 0.25rem; line-height: 1.4;">
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
                     $amount = $trade->amount_invested ?? ($trade->amount ?? 0);
                     $profitLoss = 0;
                     $profitLossLabel = '';

                     $tradeStatusUpper = strtoupper($trade->status ?? 'PENDING');
                     if ($tradeStatusUpper === 'WON' || $tradeStatusUpper === 'WIN') {
                     $payout = $trade->payout ?? ($trade->payout_amount ?? 0);
                     $profitLoss = $payout - $amount;
                     $profitLossLabel = 'Profit';
                     } elseif (
                     $tradeStatusUpper === 'LOST' ||
                     $tradeStatusUpper === 'LOSS'
                     ) {
                     $profitLoss = -$amount;
                     $profitLossLabel = 'Loss';
                     } elseif ($tradeStatusUpper === 'CLOSED' && $trade->payout) {
                     // Closed position
                     $profitLoss = $trade->payout - $amount;
                     $profitLossLabel = $profitLoss >= 0 ? 'Profit' : 'Loss';
                     } else {
                     // Pending - calculate based on current market price if available
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

                     $avgPrice = $trade->price_at_buy ?? ($trade->price ?? 0.5);
                     $outcome = strtoupper(
                     $trade->outcome ?? ($trade->side ?? 'YES'),
                     );
                     $currentPrice =
                     $outcome === 'YES' && isset($outcomePrices[1])
                     ? $outcomePrices[1]
                     : ($outcome === 'NO' && isset($outcomePrices[0])
                     ? $outcomePrices[0]
                     : $avgPrice);

                     $shares = $avgPrice > 0 ? $amount / $avgPrice : $amount;
                     $currentValue = $shares * $currentPrice;
                     $profitLoss = $currentValue - $amount;
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
                        data-amount="{{ $amount }}">
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
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      z-index: 7000;
      display: none;
      opacity: 0;
      transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
   }

   .withdrawal-modal-overlay.active {
      display: block;
      opacity: 1;
   }

   /* Withdrawal Modal Popup - Matching Withdrawal Request Design */
   .withdrawal-modal-popup {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0.9);
      width: 90%;
      max-width: 520px;
      max-height: 90vh;
      background: #2a2a2a;
      border-radius: 12px;
      border: 1px solid #ffb11a;
      box-shadow: 0 25px 80px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 177, 26, 0.3);
      z-index: 7001;
      display: none;
      opacity: 0;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      overflow: hidden;
   }

   /* Ensure Livewire component styles are applied */
   .withdrawal-modal-popup .withdrawal-modal-header,
   .withdrawal-modal-popup .withdrawal-modal-content {
      background: transparent;
   }

   .withdrawal-modal-popup.active {
      display: block;
      opacity: 1;
      transform: translate(-50%, -50%) scale(1);
      overflow-y: scroll;
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
      @if(session('open_withdrawal_modal'))
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
</script>
@endpush
@endsection
<style>
   .trade-summary {
      margin-top: 1rem;
      padding: 1rem;
      background: var(--bg-secondary, #1a1a1a);
      border-radius: 8px;
      border: 1px solid var(--border, #2a2a2a);
   }
   .summary-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem 0;
      border-bottom: 1px solid var(--border, #2a2a2a);
   }
   .summary-row:last-child {
      border-bottom: none;
   }
   .summary-label {
      display: flex;
      align-items: center;
      font-size: 0.875rem;
      color: var(--text-secondary, #9ca3af);
   }
   .summary-value {
      font-size: 0.95rem;
      font-weight: 600;
      color: var(--text-primary, #ffffff);
   }
   .summary-value.profit {
      color: #10b981;
   }
   .summary-value.loss {
      color: #ef4444;
   }
   .portfolio-row {
      font-size: 0.8rem;
   }
   .portfolio-row .summary-label {
      font-size: 0.8rem;
   }
   .portfolio-row .summary-value {
      font-size: 0.85rem;
   }
   .ev-row {
      margin-top: 0.5rem;
      padding-top: 0.5rem;
      border-top: 2px solid var(--border, #2a2a2a);
   }
   .trade-btn.disabled {
      opacity: 0.5;
      cursor: not-allowed;
   }
</style>

<div class="trading-panel" id="tradingPanel" data-market-id="{{ $event->markets->first()->id ?? '' }}">
   <div class="panel-header">
      <div class="market-profile-img">
         <img src="{{ $event->image }}" alt="Profile">
      </div>
      <div class="panel-title-section">
         <h2 class="panel-market-title" id="panelMarketTitle">{{ $event->title ?? '' }}</h2>
         <span class="panel-outcome-name" id="panelOutcomeName">
            {{ $event->markets->first()->groupItem_title ?? '' }}
         </span>
      </div>
   </div>
   <button class="panel-close-btn hide-desktop" id="panelCloseBtn" aria-label="Close">
      <i class="fas fa-times"></i>
   </button>

   <div class="limit-order-fields" id="limitOrderFields">
      <div class="limit-input-group">
         <label class="limit-input-label">Limit Price</label>
         <input type="text" class="limit-input" id="limitPrice" placeholder="0.0¢">
      </div>
   </div>
   <div class="outcome-selection">
      <div class="outcome-buttons">
         @php
            $market = $event->markets->first();
            $prices = is_string($market->outcome_prices ?? null) 
                ? json_decode($market->outcome_prices, true) 
                : ($market->outcome_prices ?? []);
            // Polymarket format - prices[0] = NO, prices[1] = YES
            // Prices from Polymarket API are stored as decimals (0-1 range)
            $yesPrice = isset($prices[1]) ? floatval($prices[1]) : 0.5;
            $noPrice = isset($prices[0]) ? floatval($prices[0]) : 0.5;

            // Use bestAsk/bestBid if available (more accurate from Polymarket API order book)
            // best_ask and best_bid are stored as decimals (0-1 range)
            if ($market->best_ask !== null && $market->best_ask > 0) {
               $yesPrice = floatval($market->best_ask);
            }
            if ($market->best_bid !== null && $market->best_bid > 0) {
               // bestBid is for YES, so NO price = 1 - bestBid
               $noPrice = 1 - floatval($market->best_bid);
            }

            // Ensure prices are in valid range (0-1 for decimal format)
            $yesPrice = max(0.001, min(0.999, $yesPrice));
            $noPrice = max(0.001, min(0.999, $noPrice));

            // Format prices in cents (Polymarket style) - prices are already decimals
            $yesPriceCents = number_format($yesPrice * 100, 1);
            $noPriceCents = number_format($noPrice * 100, 1);
         @endphp
         <button class="outcome-btn-yes active" id="yesBtn" data-price="{{ $yesPrice }}">
            Yes {{ $yesPriceCents }}¢
         </button>
         <button class="outcome-btn-no" id="noBtn" data-price="{{ $noPrice }}">
            No {{ $noPriceCents }}¢
         </button>
      </div>
   </div>
   <div class="shares-input-group">
      <!-- Amount Section -->
      <div class="amount-section">
         <div class="amount-header my-2">
            <label class="input-label">Amount</label>
            <div class="amount-display-wrapper">
               <input type="number" class="shares-input" id="sharesInput" min="0" step="0.01" value="" placeholder="0"
                  aria-label="Investment amount in USD">
               <span class="amount-currency">$</span>
            </div>
         </div>
         <div class="price-buttons">
            <button class="shares-price" data-price="1" onclick="updateShares(1)">+$1</button>
            <button class="shares-price" data-price="20" onclick="updateShares(20)">+$20</button>
            <button class="shares-price" data-price="50" onclick="updateShares(50)">+$50</button>
            <button class="shares-price" data-price="100" onclick="updateShares(100)">+$100</button>
         </div>
      </div>

      <!-- To Win Section -->
      <div class="trade-summary">
         <div class="to-win-section">
            <div class="to-win-left">
               <div class="to-win-label">
                  <span>To win</span>
                  <i class="fas fa-coins" style="color: var(--success); margin-left: 4px;"></i>
               </div>
            </div>
            <div class="to-win-value" id="potentialPayout">$0</div>
         </div>
      </div>

      <!-- Trade Button -->
      <button class="trade-btn" id="executeTrade">Trade</button>
   </div>
</div>
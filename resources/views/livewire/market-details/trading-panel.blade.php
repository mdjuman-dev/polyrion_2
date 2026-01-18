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
         <img src="{{ $event->image ? (str_starts_with($event->image, 'http') ? $event->image : asset('storage/' . $event->image)) : asset('frontend/assets/images/default-market.png') }}" 
              alt="Profile"
              onerror="this.src='{{ asset('frontend/assets/images/default-market.png') }}'">
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
            
            // Get actual outcomes from market
            $outcomes = is_string($market->outcomes) 
                ? json_decode($market->outcomes, true) 
                : ($market->outcomes ?? ['Yes', 'No']);
            
            // Default to Yes/No if outcomes array is empty or invalid
            if (empty($outcomes) || !is_array($outcomes)) {
               $outcomes = ['Yes', 'No'];
            }
            
            // Get first and second outcome
            $firstOutcome = isset($outcomes[0]) ? $outcomes[0] : 'Yes';
            $secondOutcome = isset($outcomes[1]) ? $outcomes[1] : 'No';
            
            // Polymarket format - prices[0] = second outcome, prices[1] = first outcome
            // Prices from Polymarket API are stored as decimals (0-1 range)
            $firstPrice = isset($prices[1]) ? floatval($prices[1]) : 0.5;
            $secondPrice = isset($prices[0]) ? floatval($prices[0]) : 0.5;

            // Use bestAsk/bestBid if available (more accurate from Polymarket API order book)
            // best_ask and best_bid are stored as decimals (0-1 range)
            if ($market->best_ask !== null && $market->best_ask > 0) {
               $firstPrice = floatval($market->best_ask);
               // If we have best_ask, calculate complementary second price
               // But only if best_bid is not available
               if ($market->best_bid === null || $market->best_bid <= 0) {
                  $secondPrice = 1.0 - $firstPrice;
               }
            }
            if ($market->best_bid !== null && $market->best_bid > 0) {
               // bestBid is for first outcome, so second outcome price = 1 - bestBid
               $secondPrice = 1.0 - floatval($market->best_bid);
               // If we have best_bid, calculate complementary first price
               // But only if best_ask is not available
               if ($market->best_ask === null || $market->best_ask <= 0) {
                  $firstPrice = 1.0 - $secondPrice;
               }
            }

            // Ensure prices are complementary (firstPrice + secondPrice = 1.0)
            // This is critical for binary markets
            $priceSum = $firstPrice + $secondPrice;
            if (abs($priceSum - 1.0) > 0.01) {
               // If prices don't sum to 1, make them complementary
               // Use firstPrice as base and calculate secondPrice
               $firstPrice = max(0.001, min(0.999, $firstPrice));
               $secondPrice = 1.0 - $firstPrice;
            } else {
               // Ensure prices are in valid range (0.001 - 0.999)
               $firstPrice = max(0.001, min(0.999, $firstPrice));
               $secondPrice = max(0.001, min(0.999, $secondPrice));
               // Re-normalize to ensure they sum to 1.0
               $priceSum = $firstPrice + $secondPrice;
               if ($priceSum > 0) {
                  $firstPrice = $firstPrice / $priceSum;
                  $secondPrice = $secondPrice / $priceSum;
               }
            }

            // Format prices in cents (Polymarket style) - prices are already decimals
            // Show at least 1 decimal place, but don't round to 0.1 if actual price is different
            $firstPriceCents = $firstPrice * 100;
            $secondPriceCents = $secondPrice * 100;
            
            // Format with appropriate precision
            // If price is very small (< 1¢), show 2 decimal places
            // If price is larger, show 1 decimal place
            if ($firstPriceCents < 1) {
               $firstPriceCentsDisplay = number_format($firstPriceCents, 2, '.', '');
            } else {
               $firstPriceCentsDisplay = number_format($firstPriceCents, 1, '.', '');
            }
            
            if ($secondPriceCents < 1) {
               $secondPriceCentsDisplay = number_format($secondPriceCents, 2, '.', '');
            } else {
               $secondPriceCentsDisplay = number_format($secondPriceCents, 1, '.', '');
            }
            
            // Remove trailing zeros
            $firstPriceCentsDisplay = rtrim(rtrim($firstPriceCentsDisplay, '0'), '.');
            $secondPriceCentsDisplay = rtrim(rtrim($secondPriceCentsDisplay, '0'), '.');
         @endphp
         <button class="outcome-btn-yes active" id="yesBtn" data-price="{{ $firstPrice }}" 
            data-outcome="{{ $firstOutcome }}" data-outcome-index="0">
            {{ $firstOutcome }} {{ $firstPriceCentsDisplay }}¢
         </button>
         <button class="outcome-btn-no" id="noBtn" data-price="{{ $secondPrice }}"
            data-outcome="{{ $secondOutcome }}" data-outcome-index="1">
            {{ $secondOutcome }} {{ $secondPriceCentsDisplay }}¢
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
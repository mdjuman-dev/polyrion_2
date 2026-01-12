<div class="outcome-section">
   <div class="outcome-section-header">
      <span class="outcome-label">OUTCOME</span>
      <div class="outcome-chance-header">
         <span>% CHANCE</span>
         <button class="refresh-btn" aria-label="Refresh outcomes">
            <i class="fas fa-sync-alt"></i>
         </button>
      </div>
   </div>

   @php
      // Separate active and ended markets
      $activeMarkets = $event->markets
         ->where('closed', false)
         ->filter(fn($market) => !$market->isClosed())
         ->values();

      $endedMarkets = $event->markets
         ->where('closed', true)
         ->values();
   @endphp

   {{-- ACTIVE MARKETS (Can Trade) --}}
   @if ($activeMarkets->count() > 0)
      @foreach ($activeMarkets as $index => $market)
         @php
            // ====================================
            // POLYMARKET PRICE CALCULATION (ACCURATE)
            // ====================================

            // Parse outcome prices (Polymarket format: [NO, YES])
            // Handle both string (JSON) and array formats
            $prices = is_string($market->outcome_prices) 
                ? json_decode($market->outcome_prices, true) 
                : ($market->outcome_prices ?? [0.5, 0.5]);

            // Initialize with fallback mid-market prices
            $yesPrice = isset($prices[1]) ? (float) $prices[1] : 0.5;
            $noPrice = isset($prices[0]) ? (float) $prices[0] : 0.5;

            // PRIORITY 1: Use order book data (most accurate)
            // best_ask = price to BUY YES (what you pay)
            // best_bid = price to SELL YES (what you receive) = price to BUY NO
            if (!is_null($market->best_ask) && $market->best_ask > 0) {
               $yesPrice = (float) $market->best_ask;
            }

            if (!is_null($market->best_bid) && $market->best_bid > 0) {
               // In Polymarket: buying NO costs (1 - best_bid) because:
               // If YES bid is 0.65, then NO ask should be 0.35 (complementary)
               $noPrice = 1.0 - (float) $market->best_bid;
            }

            // Clamp prices to valid range (0.001 - 0.999)
            // Avoid 0 or 1 to prevent division by zero and maintain market liquidity
            $yesPrice = max(0.001, min(0.999, $yesPrice));
            $noPrice = max(0.001, min(0.999, $noPrice));

            // ====================================
            // PROBABILITY CALCULATION
            // ====================================

            // Convert decimal prices to probability percentages
            $yesProb = $yesPrice * 100;
            $noProb = $noPrice * 100;

            // Round to 1 decimal place (Polymarket standard)
            $yesProbRounded = round($yesProb, 1);
            $noProbRounded = round($noProb, 1);

            // ====================================
            // PRICE DISPLAY (CENTS FORMAT)
            // ====================================

            // Polymarket displays prices in cents (0-100¢)
            // Example: 0.653 = 65.3¢
            $yesPriceCents = $yesPrice * 100;
            $noPriceCents = $noPrice * 100;

            // Format with 1 decimal place, remove trailing zeros
            $yesPriceDisplay = rtrim(rtrim(number_format($yesPriceCents, 1, '.', ''), '0'), '.');
            $noPriceDisplay = rtrim(rtrim(number_format($noPriceCents, 1, '.', ''), '0'), '.');

            // ====================================
            // PROBABILITY DISPLAY (WITH EDGE CASES)
            // ====================================

            // Handle edge cases: <1% and >99%
            if ($yesProbRounded < 1) {
               $yesProbDisplay = '<1';
            } elseif ($yesProbRounded >= 99) {
               $yesProbDisplay = '>99';
            } else {
               // Remove decimal if it's .0 (e.g., 45.0% → 45%)
               $yesProbDisplay = $yesProbRounded == floor($yesProbRounded)
                  ? (int) $yesProbRounded
                  : $yesProbRounded;
            }

            if ($noProbRounded < 1) {
               $noProbDisplay = '<1';
            } elseif ($noProbRounded >= 99) {
               $noProbDisplay = '>99';
            } else {
               $noProbDisplay = $noProbRounded == floor($noProbRounded)
                  ? (int) $noProbRounded
                  : $noProbRounded;
            }

            // ====================================
            // PRICE CHANGE CALCULATION (24H)
            // ====================================

            // one_day_price_change should store the change as decimal (e.g., 0.053 = +5.3%)
            $priceChange = $market->one_day_price_change ?? 0;

            // Convert to percentage and round
            $percentChange = round(abs($priceChange) * 100, 1);

            // Determine direction and styling
            if ($priceChange > 0.001) { // Positive change (>0.1%)
               $arrow = '▲';
               $changeClass = 'text-success';
            } elseif ($priceChange < -0.001) { // Negative change (<-0.1%)
               $arrow = '▼';
               $changeClass = 'text-danger';
            } else { // No significant change
               $arrow = '';
               $changeClass = '';
               $percentChange = 0;
            }

            // Format change display (remove .0 decimals)
            $changeDisplay = $percentChange == floor($percentChange)
               ? (int) $percentChange
               : $percentChange;

            // Check if this is the first market
            $isFirst = $index === 0;
         @endphp

         {{-- ACTIVE Market Outcome Row --}}
         <div class="outcome-row {{ $isFirst ? 'first-market' : '' }}" data-market-id="{{ $market->id }}"
            data-market-status="active" data-yes-price="{{ $yesPrice }}" data-no-price="{{ $noPrice }}">

            <div class="outcome-row-content">
               {{-- Left: Market Info --}}
               <div class="outcome-info">
                  <div class="d-flex align-items-center gap-3">
                     @if ($market->icon)
                        <img src="{{ $market->icon }}" alt="{{ $market->groupItem_title }}" class="outcome-icon" loading="lazy"
                           onerror="this.style.display='none'">
                     @endif

                     <div class="outcome-text">
                        <div class="outcome-name">{{ $market->groupItem_title }}</div>
                        <div class="outcome-volume">${{ formatVolume($market->volume) }} Vol.</div>
                     </div>
                  </div>
               </div>

               {{-- Right: Probability & Change --}}
               <div class="outcome-percent-wrapper">
                  <span class="outcome-percent">{{ $yesProbDisplay }}%</span>

                  @if ($percentChange > 0)
                     <span class="percent-change {{ $changeClass }}">
                        {{ $arrow }}{{ $changeDisplay }}%
                     </span>
                  @endif
               </div>
            </div>

            {{-- Action Buttons (Only for Active Markets) --}}
            <div class="outcome-actions">
               <button class="btn-yes" data-action="buy-yes" data-market-id="{{ $market->id }}" data-price="{{ $yesPrice }}"
                  data-outcome="YES">
                  Buy Yes {{ $yesPriceDisplay }}¢
               </button>

               <button class="btn-no" data-action="buy-no" data-market-id="{{ $market->id }}" data-price="{{ $noPrice }}"
                  data-outcome="NO">
                  Buy No {{ $noPriceDisplay }}¢
               </button>
            </div>
         </div>
      @endforeach
   @endif

   {{-- ENDED MARKETS (Show Results Only, No Trading) --}}
   @if ($endedMarkets->count() > 0)
      <div class="ended-markets-section">
         <div class="ended-markets-header">
            <span class="ended-label">ENDED MARKETS</span>
         </div>

         @foreach ($endedMarkets as $index => $market)
            @php
               // Get final result from market - use getFinalOutcome() method
               $winningOutcome = $market->getFinalOutcome(); // Returns 'YES', 'NO', or null

               // Get final prices at market close
               $prices = is_string($market->outcome_prices ?? null) 
                   ? json_decode($market->outcome_prices, true) 
                   : ($market->outcome_prices ?? [0.5, 0.5]);
               $finalYesPrice = isset($prices[1]) ? (float) $prices[1] : 0.5;
               $finalNoPrice = isset($prices[0]) ? (float) $prices[0] : 0.5;

               // Calculate final probabilities
               $finalYesProb = round($finalYesPrice * 100, 1);
               $finalNoProb = round($finalNoPrice * 100, 1);

               // Format display
               if ($finalYesProb < 1) {
                  $finalYesProbDisplay = '<1';
               } elseif ($finalYesProb >= 99) {
                  $finalYesProbDisplay = '>99';
               } else {
                  $finalYesProbDisplay = $finalYesProb == floor($finalYesProb)
                     ? (int) $finalYesProb
                     : $finalYesProb;
               }

               // Determine result display - show which outcome won
               if ($winningOutcome === 'YES') {
                  $resultText = 'YES Won';
                  $resultClass = 'result-yes';
               } elseif ($winningOutcome === 'NO') {
                  $resultText = 'NO Won';
                  $resultClass = 'result-no';
               } else {
                  $resultText = 'Resolving...';
                  $resultClass = 'result-pending';
               }
            @endphp

            {{-- ENDED Market Row --}}
            <div class="outcome-row outcome-ended" data-market-id="{{ $market->id }}" data-market-status="ended"
               data-winning-outcome="{{ $winningOutcome }}">

               <div class="outcome-row-content">
                  {{-- Left: Market Info --}}
                  <div class="outcome-info">
                     <div class="d-flex align-items-center gap-3">
                        @if ($market->icon)
                           <img src="{{ $market->icon }}" alt="{{ $market->groupItem_title }}"
                              class="outcome-icon outcome-icon-ended" loading="lazy" onerror="this.style.display='none'">
                        @endif

                        <div class="outcome-text">
                           <div class="outcome-name outcome-name-ended">
                              {{ $market->groupItem_title }}
                           </div>
                           <div class="outcome-volume">
                              ${{ formatVolume($market->volume) }} Vol. · Ended
                           </div>
                        </div>
                     </div>
                  </div>

                  {{-- Right: Final Probability & Result --}}
                  <div class="outcome-percent-wrapper">
                     <span class="outcome-percent outcome-percent-ended">
                        {{ $finalYesProbDisplay }}%
                     </span>
                  </div>
               </div>

               {{-- Result Display (Instead of Trade Buttons) --}}
               <div class="outcome-result">
                  <div class="result-badge {{ $resultClass }}">
                     @if ($winningOutcome === 'YES')
                        <i class="fas fa-check-circle"></i> <span>YES Won</span>
                     @elseif ($winningOutcome === 'NO')
                        <i class="fas fa-times-circle"></i> <span>NO Won</span>
                     @else
                        <i class="fas fa-clock"></i> <span>Resolving...</span>
                     @endif
                  </div>

                  @if ($winningOutcome)
                     <div class="final-probability">
                        Final: {{ $finalYesProbDisplay }}% YES
                     </div>
                  @endif
               </div>
            </div>
         @endforeach
      </div>
   @endif

   {{-- No Markets Available --}}
   @if ($activeMarkets->count() === 0 && $endedMarkets->count() === 0)
      <div class="no-markets-message">
         <p>No markets available at this time.</p>
      </div>
   @endif

   {{-- Additional CSS for Ended Markets (Dark theme matching active markets) --}}
   <style>
      /* Ended Markets Section */
      .ended-markets-section {
         margin-top: 24px;
         padding-top: 24px;
         border-top: 1px solid rgba(255, 255, 255, 0.1);
      }

      .ended-markets-header {
         padding: 12px 20px;
         background: rgba(255, 255, 255, 0.03);
         border-radius: 8px;
         margin-bottom: 16px;
         border: 1px solid rgba(255, 255, 255, 0.05);
      }

      .ended-label {
         font-weight: 600;
         font-size: 13px;
         color: rgba(255, 255, 255, 0.7);
         letter-spacing: 0.5px;
         text-transform: uppercase;
      }

      /* Ended Market Row Styling - Match active market theme */
      .outcome-row.outcome-ended {
         background: rgba(255, 255, 255, 0.02);
         border: 1px solid rgba(255, 255, 255, 0.08);
         border-radius: 8px;
         padding: 16px 20px;
         margin-bottom: 12px;
         transition: all 0.2s;
      }

      .outcome-row.outcome-ended:hover {
         background: rgba(255, 255, 255, 0.04);
         border-color: rgba(255, 255, 255, 0.12);
      }

      .outcome-icon-ended {
         width: 40px;
         height: 40px;
         border-radius: 8px;
         object-fit: cover;
         opacity: 0.9;
      }

      .outcome-name-ended {
         color: rgba(255, 255, 255, 0.9);
         font-weight: 500;
         font-size: 15px;
         margin-bottom: 4px;
      }

      .outcome-volume {
         color: rgba(255, 255, 255, 0.6);
         font-size: 13px;
      }

      .outcome-percent-ended {
         color: rgba(255, 255, 255, 0.9);
         font-weight: 600;
         font-size: 15px;
      }

      /* Result Display - Match image design */
      .outcome-result {
         display: flex;
         flex-direction: column;
         align-items: flex-start;
         gap: 8px;
         margin-top: 12px;
         padding-top: 12px;
         border-top: 1px solid rgba(255, 255, 255, 0.08);
      }

      .result-badge {
         padding: 6px 12px;
         border-radius: 6px;
         font-weight: 600;
         font-size: 13px;
         display: inline-flex;
         align-items: center;
         gap: 6px;
         border-width: 1px;
         border-style: solid;
      }

      .result-badge.result-yes {
         background: rgba(16, 185, 129, 0.15);
         color: #10b981;
         border-color: #10b981;
      }

      .result-badge.result-no {
         background: rgba(239, 68, 68, 0.15);
         color: #ef4444;
         border-color: #ef4444;
      }

      .result-badge.result-pending {
         background: rgba(245, 158, 11, 0.15);
         color: #f59e0b;
         border-color: #f59e0b;
      }

      .result-badge i {
         font-size: 14px;
      }

      .final-probability {
         font-size: 12px;
         color: rgba(255, 255, 255, 0.5);
         font-weight: 500;
      }

      .no-markets-message {
         padding: 40px 20px;
         text-align: center;
         color: rgba(255, 255, 255, 0.6);
      }

      /* Responsive adjustments */
      @media (max-width: 768px) {
         .outcome-row.outcome-ended {
            padding: 12px 16px;
         }

         .outcome-name-ended {
            font-size: 14px;
         }

         .outcome-percent-ended {
            font-size: 14px;
         }

         .result-badge {
            font-size: 12px;
            padding: 5px 10px;
         }
      }
   </style>

   {{-- Optional: Add JavaScript for refresh button --}}
   <script>
      document.querySelector('.refresh-btn')?.addEventListener('click', function () {
         const icon = this.querySelector('i');
         icon.style.animation = 'spin 0.5s linear';

         // Add your refresh logic here
         // Example: fetch updated market data

         setTimeout(() => {
            icon.style.animation = '';
         }, 500);
      });
   </script>
</div>
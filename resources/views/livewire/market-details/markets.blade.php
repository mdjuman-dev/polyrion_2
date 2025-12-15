<div class="outcome-section">
    <div class="outcome-section-header">
        <span class="outcome-label">OUTCOME</span>
        <div class="outcome-chance-header">
            <span>% CHANCE</span>
            <button class="refresh-btn" aria-label="Refresh">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>
    @if ($event->markets->count() > 0)
        @foreach ($event->markets as $index => $market)
            @php
                $prices = json_decode($market->outcome_prices, true);
                // Polymarket format - prices[0] = NO, prices[1] = YES
                // Prices from Polymarket API are stored as decimals (0-1 range)
                $yesPrice = isset($prices[1]) ? floatval($prices[1]) : 0.5;
                $noPrice = isset($prices[0]) ? floatval($prices[0]) : 0.5;

                // Use bestAsk/bestBid if available (more accurate from Polymarket API order book)
                // best_ask and best_bid are stored as decimals (0-1 range)
                if ($market->best_ask !== null && $market->best_ask > 0) {
                    $yesPrice = floatval($market->best_ask); // Best ask is the price to buy YES
                }
                if ($market->best_bid !== null && $market->best_bid > 0) {
                    // bestBid is for YES, so NO price = 1 - bestBid
                    $noPrice = 1 - floatval($market->best_bid);
                }

                // Ensure prices are in valid range (0-1 for decimal format)
                $yesPrice = max(0.001, min(0.999, $yesPrice));
                $noPrice = max(0.001, min(0.999, $noPrice));

                // Convert decimal prices (0-1) to percentages (0-100)
                $yesProb = round($yesPrice * 100, 1);
                $noProb = round($noPrice * 100, 1);

                // Format prices in cents (Polymarket style) - prices are already decimals
                $yesPriceCents = number_format($yesPrice * 100, 1);
                $noPriceCents = number_format($noPrice * 100, 1);

                // Format percentage display
                $yesProbDisplay = $yesProb < 1 ? '<1%' : ($yesProb >= 99 ? '>99%' : $yesProb . '%');
                $noProbDisplay = $noProb < 1 ? '<1%' : ($noProb >= 99 ? '>99%' : $noProb . '%');

                // Get price change from database (one_day_price_change)
                $priceChange = $market->one_day_price_change ?? 0;
                $percentChange = round(abs($priceChange) * 100, 1);
                $arrow = $priceChange > 0 ? '▲' : ($priceChange < 0 ? '▼' : '');
                $changeClass = $priceChange > 0 ? 'text-success' : ($priceChange < 0 ? 'text-danger' : '');
                $isFirst = $index === 0;
            @endphp

            <div class="outcome-row {{ $isFirst ? 'first-market' : '' }}" data-market-id="{{ $market->id }}">
                <div class="outcome-row-content">
                    <div class="outcome-info">
                        <div class="d-flex align-items-center gap-3">
                            @if ($market->icon)
                                <img src="{{ $market->icon }}" alt="{{ $market->groupItem_title }}" class="outcome-icon"
                                    onerror="this.style.display='none'">
                            @endif
                            <div class="outcome-text">
                                <div class="outcome-name">{{ $market->groupItem_title }}</div>
                                <div class="outcome-volume">${{ formatVolume($market->volume) }} Vol.</div>
                            </div>
                        </div>
                    </div>

                    <div class="outcome-percent-wrapper">
                        <span class="outcome-percent">{{ $yesProbDisplay }}</span>
                        @if ($priceChange != 0 && $percentChange > 0)
                            <span class="percent-change {{ $changeClass }}">
                                {{ $arrow }}{{ $percentChange }}%
                            </span>
                        @endif
                    </div>
                </div>

                <div class="outcome-actions">
                    <button class="btn-yes" data-yes-price="{{ $yesPrice }}"
                        data-no-price="{{ $noPrice }}">Buy Yes {{ $yesPriceCents }}¢</button>
                    <button class="btn-no" data-yes-price="{{ $yesPrice }}"
                        data-no-price="{{ $noPrice }}">Buy No {{ $noPriceCents }}¢</button>
                </div>
            </div>
        @endforeach
    @endif
</div>

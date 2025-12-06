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
                $yesPrice = isset($prices[0]) ? floatval($prices[0]) : 0;
                $noPrice = isset($prices[1]) ? floatval($prices[1]) : 0;
                $yesProb = round($yesPrice * 100, 1);
                $noProb = round($noPrice * 100, 1);

                // Format prices in cents (Polymarket style)
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
                    <button class="btn-yes">Buy Yes {{ $yesPriceCents }}¢</button>
                    <button class="btn-no">Buy No {{ $noPriceCents }}¢</button>
                </div>
            </div>
        @endforeach
    @endif
</div>

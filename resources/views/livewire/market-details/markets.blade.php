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
        @foreach ($event->markets as $market)
            @php
                $prices = json_decode($market->outcome_prices, true);
                $yesProb = isset($prices[0]) ? round($prices[0] * 100) : 0;
                $noProb = isset($prices[1]) ? round($prices[1] * 100) : 0;

                // যদি percent change না থাকে, always 0%
                $percentChange = 0;
                $arrow = $percentChange > 0 ? '▲' : '▼';
                $changeClass = $percentChange > 0 ? 'text-success' : 'text-danger';
            @endphp

            <div class="outcome-row" data-market-id="{{ $market->id }}">
                <div class="d-flex justify-content-between align-items-center w-100">

                    <div class="outcome-info">
                        <div class="outcome-name">{{ $market->question }}</div>
                        <div class="outcome-volume">${{ formatVolume($market->volume) }} Vol.</div>
                    </div>

                    <div class="outcome-percent-wrapper">
                        <span class="outcome-percent">{{ $yesProb }}%</span>
                        <span class="percent-change {{ $changeClass }}">
                            {{ $arrow }}{{ $percentChange }}%
                        </span>
                    </div>
                </div>

                <div class="outcome-actions">
                    <button class="btn-yes">Buy Yes {{ $yesProb }}$</button>
                    <button class="btn-no">Buy No {{ $noProb }}$</button>
                </div>
            </div>
        @endforeach
    @endif
</div>

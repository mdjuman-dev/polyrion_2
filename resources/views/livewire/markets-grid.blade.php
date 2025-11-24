<div wire:poll.3s>
    <!-- Markets Grid -->
    <div class="markets-grid mt-3 mt-lg-0">
        <!-- Market Card 2 -->
        @foreach ($events as $event)
            @php
                $isMultiMarket = $event->markets->count() > 1;
            @endphp

            @if ($isMultiMarket)
                {{-- Type 2: Multiple Markets (Fed rate cuts style) --}}
                <div class="market-card multi-market">
                    <div class="market-card-header">
                        <div class="market-profile-img">
                            <img src="{{ $event->image }}" alt="{{ $event->title }}">
                        </div>
                        <a href="{{ route('market.details', $event->slug) }}" class="market-card-title">{{ $event->title }}</a>
                    </div>
                    <div class="market-card-body">
                        @foreach ($event->markets as $market)
                            @php
                                $prices = json_decode($market->outcome_prices, true);
                                $yesProb = isset($prices[0]) ? round($prices[0] * 100) : 0;
                                $outcomes = json_decode($market->outcomes, true);
                            @endphp

                            @if ($outcomes !== null)
                                <div class="market-card-outcome-row">
                                    <span class="market-card-outcome-label">{{ $market->groupItem_title }}</span>
                                    <span class="market-card-outcome-probability">{{ $yesProb }}%</span>
                                    <button class="market-card-yes-btn">{{ $outcomes[0] ?? 'Yes' }}</button>
                                    <button class="market-card-no-btn">{{ $outcomes[1] ?? 'No' }}</button>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="market-footer">
                        <span class="market-card-volume">
                            <i class="fas fa-money-bill-wave"></i>
                            ${{ formatVolume($event->volume) }} Vol.
                        </span>
                        <div class="market-actions d-flex gap-2">
                            <button class="market-card-action-btn"><i class="fas fa-bookmark"></i></button>
                        </div>
                    </div>
                </div>
            @else
                {{-- Type 1: Single Market (Ukraine ceasefire style) --}}
                @php
                    $market = $event->markets->first();
                    if ($market) {
                        $prices = json_decode($market->outcome_prices, true);
                        $yesProb = isset($prices[0]) ? round($prices[0] * 100) : 0;
                        $outcomes = json_decode($market->outcomes, true);
                    }
                @endphp

                <div class="market-card single-market">
                    <div class="market-card-header">
                        <div class="market-profile-img">
                            <img src="{{ $event->image }}" alt="{{ $event->title }}">
                        </div>
                        <div class="market-title-section">
                            <a href="{{ route('market.details', $event->slug) }}" class="market-card-title">{{ $event->title }}</a>
                            <div class="market-chance">
                                <span class="chance-arrow">↓</span>
                                <span class="chance-value">{{ $yesProb }}%</span>
                                <span class="chance-label">chance</span>
                            </div>
                        </div>
                    </div>

                    <div class="market-card-body-single">
                        <button class="market-card-yes-btn-large">{{ $outcomes[0] ?? 'Yes' }}</button>
                        <button class="market-card-no-btn-large">{{ $outcomes[1] ?? 'No' }}</button>
                    </div>

                    <div class="market-footer">
                        <span class="market-card-volume">
                            <i class="fas fa-money-bill-wave"></i>
                            ${{ formatVolume($event->volume) }} Vol.
                        </span>
                        <div class="market-actions d-flex gap-2">
                            <button class="market-card-action-btn"><i class="fas fa-redo"></i></button>
                            <button class="market-card-action-btn"><i class="fas fa-bookmark"></i></button>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="text-center">
        {{ $events->links('pagination::bootstrap-5') }}
    </div>
</div>

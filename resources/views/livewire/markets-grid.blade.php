<div wire:poll.5s="refreshEvents" data-component="markets-grid">
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
                        <a href="{{ route('market.details', $event->slug) }}"
                            class="market-card-title">{{ \Illuminate\Support\Str::limit($event->title, 60) }}</a>
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
                                    <button class="market-card-yes-btn">{{ 'Yes' }}</button>
                                    <button class="market-card-no-btn">{{ 'No' }}</button>
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
                            <livewire:save-event :event="$event" :key="'grid-' . $event->id" />
                        </div>
                    </div>
                </div>
            @else
                @php
                    $market = $event->markets->first();
                    if ($market) {
                        $prices = json_decode($market->outcome_prices, true);
                        $yesProb = isset($prices[0]) ? round($prices[0] * 100) : 0;
                        $outcomes = json_decode($market->outcomes, true);
                    }
                @endphp

                <div class="market-card single-market">
                    <div class="d-flex">
                        <div class="market-card-header me-3">
                            <div class="market-profile-img">
                                <img src="{{ $event->image }}" alt="{{ $event->title }}">
                            </div>
                            <div class="market-title-section">
                                <a href="{{ route('market.details', $event->slug) }}"
                                    class="market-card-title">{{ \Illuminate\Support\Str::limit($event->title, 100) }}</a>
                            </div>
                        </div>
                        <div class="market-chance">
                            <span
                                class="chance-value {{ $yesProb <= 30 ? 'danger' : ($yesProb <= 50 ? 'warning' : 'success') }}">
                                {{ $yesProb }}%
                            </span>
                            <div class="chance-label">chance</div>
                        </div>
                    </div>

                    <div class="market-card-body-single">
                        <button class="market-card-yes-btn-large">{{ 'Up' }}</button>
                        <button class="market-card-no-btn-large">{{ 'Down' }}</button>
                    </div>

                    <div class="market-footer">
                        <span class="market-card-volume">
                            <i class="fas fa-money-bill-wave"></i>
                            ${{ formatVolume($event->volume) }} Vol.
                        </span>
                        <div class="market-actions d-flex gap-2">
                            <livewire:save-event :event="$event" :key="'grid-single-' . $event->id" />
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    @if ($hasMore)
        <div x-intersect.threshold.10="$wire.loadMore()" class="text-center">
            <div wire:loading wire:target="loadMore" class="d-flex align-items-center justify-content-center gap-2">
                <span class="loader"></span>
            </div>
        </div>
    @endif

</div>

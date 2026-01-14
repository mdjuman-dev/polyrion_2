@if (!$event)
    <div class="market-card">
        <p>Event not found</p>
    </div>
@else
    @php
        $activeMarkets = $event->markets->filter(function($market) {
            return $market->active && !$market->closed;
        });
        $isMultiMarket = $activeMarkets->count() > 1;
        $singleMarket = $activeMarkets->count() === 1 ? $activeMarkets->first() : null;
        $marketLink = $singleMarket && $singleMarket->slug 
            ? route('market.single', $singleMarket->slug) 
            : route('market.details', $event->slug);
    @endphp

    @if ($isMultiMarket)
        {{-- Type 2: Multiple Markets (Fed rate cuts style) --}}
        <div class="market-card multi-market">
            <div class="market-card-header">
                <div class="market-profile-img">
                    <img src="{{ $event->image ?? asset('frontend/assets/images/default-market.png') }}"
                        alt="{{ $event->title }}"
                        onerror="this.src='{{ asset('frontend/assets/images/default-market.png') }}'">
                </div>
                <a href="{{ route('market.details', $event->slug) }}"
                    class="market-card-title">{{ \Illuminate\Support\Str::limit($event->title, $titleLength) }}</a>
            </div>
            <div class="market-card-body">
                @foreach ($event->markets as $market)
                    @php
                        // Handle both string (JSON) and array formats
                        $prices = is_string($market->outcome_prices) 
                            ? json_decode($market->outcome_prices, true) 
                            : ($market->outcome_prices ?? [0.5, 0.5]);
                        // Polymarket format - prices[0] = NO, prices[1] = YES
                        // Prices are stored as decimals (0-1 range)
                        $yesPrice = isset($prices[1]) ? floatval($prices[1]) : 0.5;

                        // Use best_ask if available (more accurate from Polymarket API)
                        if ($market->best_ask !== null && $market->best_ask > 0) {
                            $yesPrice = floatval($market->best_ask);
                        }

                        // Ensure price is in valid range and convert to percentage
                        $yesPrice = max(0.001, min(0.999, $yesPrice));
                        $yesProb = round($yesPrice * 100, 1);
                        $outcomes = is_string($market->outcomes) 
                            ? json_decode($market->outcomes, true) 
                            : ($market->outcomes ?? []);
                        
                        // Default to Yes/No if outcomes array is empty or invalid
                        if (empty($outcomes) || !is_array($outcomes)) {
                           $outcomes = ['Yes', 'No'];
                        }
                        
                        // Get first and second outcome
                        $firstOutcome = isset($outcomes[0]) ? $outcomes[0] : 'Yes';
                        $secondOutcome = isset($outcomes[1]) ? $outcomes[1] : 'No';
                    @endphp

                    @if ($outcomes !== null)
                        <div class="market-card-outcome-row">
                            <span class="market-card-outcome-label "
                                style="color:#fff">{{ $market->groupItem_title }}</span>
                            <span class="market-card-outcome-probability">{{ $yesProb }}%</span>
                            <button class="market-card-yes-btn">{{ $firstOutcome }}</button>
                            <button class="market-card-no-btn">{{ $secondOutcome }}</button>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="market-footer">
                @if ($showNewBadge && $event->volume <= $newBadgeThreshold)
                    <span class="market-card-volume" style="color: #ffb11a;">
                        <i class="fas fa-clock" style="color: #ffb11a;"></i>
                        New
                    </span>
                @else
                    <span class="market-card-volume">
                        <i class="fas fa-money-bill-wave"></i>
                        ${{ formatVolume($event->volume) }} Vol.
                    </span>
                @endif
                <div class="market-actions d-flex gap-2">
                    <livewire:save-event :event="$event" :key="$keyPrefix . '-' . $event->id" />
                </div>
            </div>
        </div>
    @else
        @php
            $market = $event->markets->first();
            if ($market) {
                // Handle both string (JSON) and array formats
                $prices = is_string($market->outcome_prices) 
                    ? json_decode($market->outcome_prices, true) 
                    : ($market->outcome_prices ?? [0.5, 0.5]);
                // Polymarket format - prices[0] = NO, prices[1] = YES
                // Prices are stored as decimals (0-1 range)
                $yesPrice = isset($prices[1]) ? floatval($prices[1]) : 0.5;

                // Use best_ask if available (more accurate from Polymarket API)
                if ($market->best_ask !== null && $market->best_ask > 0) {
                    $yesPrice = floatval($market->best_ask);
                }

                // Ensure price is in valid range and convert to percentage
                $yesPrice = max(0.001, min(0.999, $yesPrice));
                $yesProb = round($yesPrice * 100, 1);
                $outcomes = is_string($market->outcomes) 
                    ? json_decode($market->outcomes, true) 
                    : ($market->outcomes ?? []);
                
                // Default to Yes/No if outcomes array is empty or invalid
                if (empty($outcomes) || !is_array($outcomes)) {
                   $outcomes = ['Yes', 'No'];
                }
                
                // Get first and second outcome
                $firstOutcome = isset($outcomes[0]) ? $outcomes[0] : 'Yes';
                $secondOutcome = isset($outcomes[1]) ? $outcomes[1] : 'No';
            }
        @endphp

        <div class="market-card single-market">
            <div class="d-flex">
                <div class="market-card-header me-3">
                    <div class="market-profile-img">
                        <img src="{{ $event->image ?? asset('frontend/assets/images/default-market.png') }}"
                            alt="{{ $event->title }}">
                    </div>
                    <div class="market-title-section">
                        <a href="{{ $marketLink }}" class="market-card-title"
                            style="color:#fff">{{ \Illuminate\Support\Str::limit($event->title, $titleLength) }}</a>
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
                <button class="market-card-yes-btn-large">{{ $firstOutcome }}</button>
                <button class="market-card-no-btn-large">{{ $secondOutcome }}</button>
            </div>

            <div class="market-footer">
                @if ($showNewBadge && $event->volume <= $newBadgeThreshold)
                    <span class="market-card-volume" style="color: #ffb11a;">
                        <i class="fas fa-clock" style="color: #ffb11a;"></i>
                        New
                    </span>
                @else
                    <span class="market-card-volume">
                        <i class="fas fa-money-bill-wave"></i>
                        ${{ formatVolume($event->volume) }} Vol.
                    </span>
                @endif
                <div class="market-actions d-flex gap-2">
                    <livewire:save-event :event="$event" :key="$keyPrefix . '-single-' . $event->id" />
                </div>
            </div>
        </div>
    @endif
@endif

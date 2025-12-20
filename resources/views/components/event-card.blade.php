@props(['event', 'titleLength' => 60, 'keyPrefix' => 'event', 'showNewBadge' => true, 'newBadgeThreshold' => 10])

@php
    // Get all markets (including closed) for win/loss check
    $allMarkets = $event->markets;

    // Filter out closed markets for active display
    $activeMarkets = $allMarkets->filter(function ($market) {
        return !$market->isClosed() && !$market->closed;
    });

    // Check if event has ended
    $eventEnded = false;
    if ($event->end_date && $event->end_date < now()) {
        $eventEnded = true;
    } elseif (
        $allMarkets->every(function ($market) {
            return $market->isClosed() || $market->closed;
        })
    ) {
        $eventEnded = true;
    }

    // Get final result for ended markets
    $finalResult = null;
    if ($eventEnded) {
        // Get result from first market (for single market events) or check all markets
        $marketWithResult = $allMarkets->first(function ($market) {
            return $market->hasResult();
        });

        if ($marketWithResult) {
            $finalResult = $marketWithResult->getFinalOutcome(); // Returns 'YES' or 'NO'
        }
    }

    // Check user's trade status for this event (if logged in)
$userTradeStatus = null;
$userTrades = collect();
if (auth()->check()) {
    $userTrades = \App\Models\Trade::where('user_id', auth()->id())
        ->whereIn('market_id', $allMarkets->pluck('id'))
        ->with('market')
        ->get();

    if ($userTrades->isNotEmpty() && $eventEnded) {
        // Check if user has any winning trades
        $winTrades = $userTrades->filter(function ($trade) {
            return $trade->isWin();
        });
        $lossTrades = $userTrades->filter(function ($trade) {
            return $trade->isLoss();
        });
        $pendingTrades = $userTrades->filter(function ($trade) {
            return $trade->isPending();
        });

        if ($winTrades->isNotEmpty() && $lossTrades->isEmpty() && $pendingTrades->isEmpty()) {
            $userTradeStatus = 'win';
        } elseif ($lossTrades->isNotEmpty() && $winTrades->isEmpty() && $pendingTrades->isEmpty()) {
            $userTradeStatus = 'loss';
        } elseif ($winTrades->isNotEmpty() || $lossTrades->isNotEmpty()) {
            $userTradeStatus = 'mixed'; // Some won, some lost
        }
    }
}

// Show event card if it has active markets OR if it's ended and user has trades
    $shouldShow = $activeMarkets->count() > 0 || ($eventEnded && $userTrades->isNotEmpty());

    // For ended events, use all markets for display
    $displayMarkets = $eventEnded && $activeMarkets->isEmpty() ? $allMarkets : $activeMarkets;
    $isMultiMarket = $displayMarkets->count() > 1;
@endphp

@if ($shouldShow)
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
                @foreach ($displayMarkets as $market)
                    @php
                        $prices = json_decode($market->outcome_prices, true);
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
                        $outcomes = json_decode($market->outcomes, true);
                    @endphp

                    @if ($outcomes !== null)
                        @php
                            // Check if market has ended
                            $marketEnded =
                                $market->isClosed() ||
                                $market->closed ||
                                ($market->close_time && $market->close_time < now()) ||
                                ($market->end_date && $market->end_date < now()) ||
                                $eventEnded;

                            // Get final result - try multiple methods
                            $marketResult = $market->getFinalOutcome();

                            // If no result from getFinalOutcome, try to determine from lastTradePrice
                            if (!$marketResult && $marketEnded && $market->last_trade_price !== null) {
                                $marketResult = $market->determineOutcomeFromLastTradePrice();
                            }

                            $isYesWinner = $marketResult === 'YES';
                            $isNoWinner = $marketResult === 'NO';
                        @endphp
                        <div class="market-card-outcome-row">
                            <span class="market-card-outcome-label"
                                style="color:#fff">{{ $market->groupItem_title }}</span>
                            @if ($marketEnded && $marketResult)
                                {{-- Show percentage and buttons with winner/loser indication --}}
                                <span class="market-card-outcome-probability">{{ $yesProb }}%</span>
                                @if ($isYesWinner)
                                    <button class="market-card-yes-btn"
                                        style="background: rgba(50, 210, 150, 0.2); border-color: #32d296; color: #32d296; position: relative;">
                                        <i class="fas fa-check" style="color: #32d296; margin-right: 4px;"></i> Yes
                                    </button>
                                    <button class="market-card-no-btn" style="opacity: 0.5;">
                                        <i class="fas fa-times" style="color: #ff4d4f; margin-right: 4px;"></i> No
                                    </button>
                                @elseif ($isNoWinner)
                                    <button class="market-card-yes-btn" style="opacity: 0.5;">
                                        <i class="fas fa-times" style="color: #ff4d4f; margin-right: 4px;"></i> Yes
                                    </button>
                                    <button class="market-card-no-btn"
                                        style="background: rgba(255, 77, 79, 0.2); border-color: #ff4d4f; color: #ff4d4f; position: relative;">
                                        <i class="fas fa-check" style="color: #ff4d4f; margin-right: 4px;"></i> No
                                    </button>
                                @else
                                    <button class="market-card-yes-btn">{{ 'Yes' }}</button>
                                    <button class="market-card-no-btn">{{ 'No' }}</button>
                                @endif
                            @else
                                {{-- Active market: show percentage and buttons --}}
                                <span class="market-card-outcome-probability">{{ $yesProb }}%</span>
                                <button class="market-card-yes-btn">{{ 'Yes' }}</button>
                                <button class="market-card-no-btn">{{ 'No' }}</button>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="market-footer">
                <div class="d-flex align-items-center gap-2">
                    @if ($eventEnded)
                        <span class="market-card-volume" style="color: #9ca3af; font-size: 0.85rem;">
                            <i class="fas fa-calendar-times"></i>
                            Ended {{ $event->end_date ? $event->end_date->format('M d, Y') : 'Closed' }}
                        </span>
                        @if ($finalResult)
                            <span class="badge {{ $finalResult === 'YES' ? 'bg-success' : 'bg-danger' }}"
                                style="font-size: 0.75rem; padding: 4px 8px; font-weight: bold;">
                                {{ $finalResult }}
                            </span>
                        @endif
                    @elseif ($showNewBadge && $event->volume <= $newBadgeThreshold)
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

                    @if ($userTradeStatus === 'win')
                        <span class="badge bg-success" style="font-size: 0.75rem; padding: 4px 8px;">
                            <i class="fas fa-check-circle"></i> Win
                        </span>
                    @elseif ($userTradeStatus === 'loss')
                        <span class="badge bg-danger" style="font-size: 0.75rem; padding: 4px 8px;">
                            <i class="fas fa-times-circle"></i> Loss
                        </span>
                    @elseif ($userTradeStatus === 'mixed')
                        <span class="badge bg-warning" style="font-size: 0.75rem; padding: 4px 8px;">
                            <i class="fas fa-exchange-alt"></i> Mixed
                        </span>
                    @endif
                </div>
                <div class="market-actions d-flex gap-2">
                    <livewire:save-event :event="$event" :key="$keyPrefix . '-' . $event->id" />
                </div>
            </div>
        </div>
    @else
        @php
            $market = $displayMarkets->first();
            if ($market) {
                $prices = json_decode($market->outcome_prices, true);
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
                $outcomes = json_decode($market->outcomes, true);
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
                        <a href="{{ route('market.details', $event->slug) }}"
                            class="market-card-title">{{ \Illuminate\Support\Str::limit($event->title, $titleLength) }}</a>
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

            @php
                // Check if market has ended
                $marketEnded = $market
                    ? $market->isClosed() ||
                        $market->closed ||
                        ($market->close_time && $market->close_time < now()) ||
                        ($market->end_date && $market->end_date < now()) ||
                        $eventEnded
                    : false;

                // Get final result - try multiple methods
                $marketResult = $market ? $market->getFinalOutcome() : null;

                // If no result from getFinalOutcome, try to determine from lastTradePrice
                if (!$marketResult && $market && $marketEnded && $market->last_trade_price !== null) {
                    $marketResult = $market->determineOutcomeFromLastTradePrice();
                }

                $isYesWinner = $marketResult === 'YES';
                $isNoWinner = $marketResult === 'NO';
            @endphp
            <div class="market-card-body-single">
                @if ($marketEnded && $marketResult)
                    {{-- Show ONLY the winning outcome button (full width) --}}
                    @if ($isYesWinner)
                        <button class="market-card-yes-btn-large"
                            style="background: rgba(50, 210, 150, 0.2); border-color: #32d296; color: #32d296; position: relative; width: 100%;">
                            <i class="fas fa-check" style="color: #32d296; margin-right: 6px;"></i> Yes
                        </button>
                    @elseif ($isNoWinner)
                        <button class="market-card-no-btn-large"
                            style="background: rgba(255, 77, 79, 0.2); border-color: #ff4d4f; color: #ff4d4f; position: relative; width: 100%;">
                            <i class="fas fa-times" style="color: #ff4d4f; margin-right: 6px;"></i> No
                        </button>
                    @else
                        <button class="market-card-yes-btn-large">Up</button>
                        <button class="market-card-no-btn-large">Down</button>
                    @endif
                @else
                    <button class="market-card-yes-btn-large">Up</button>
                    <button class="market-card-no-btn-large">Down</button>
                @endif
            </div>

            <div class="market-footer">
                <div class="d-flex align-items-center gap-2">
                    @if ($eventEnded)
                        <span class="market-card-volume" style="color: #9ca3af; font-size: 0.85rem;">
                            <i class="fas fa-calendar-times"></i>
                            Ended {{ $event->end_date ? $event->end_date->format('M d, Y') : 'Closed' }}
                        </span>
                        @if ($finalResult)
                            <span class="badge {{ $finalResult === 'YES' ? 'bg-success' : 'bg-danger' }}"
                                style="font-size: 0.75rem; padding: 4px 8px; font-weight: bold;">
                                {{ $finalResult }}
                            </span>
                        @endif
                    @elseif ($showNewBadge && $event->volume <= $newBadgeThreshold)
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

                    @if ($userTradeStatus === 'win')
                        <span class="badge bg-success" style="font-size: 0.75rem; padding: 4px 8px;">
                            <i class="fas fa-check-circle"></i> Win
                        </span>
                    @elseif ($userTradeStatus === 'loss')
                        <span class="badge bg-danger" style="font-size: 0.75rem; padding: 4px 8px;">
                            <i class="fas fa-times-circle"></i> Loss
                        </span>
                    @elseif ($userTradeStatus === 'mixed')
                        <span class="badge bg-warning" style="font-size: 0.75rem; padding: 4px 8px;">
                            <i class="fas fa-exchange-alt"></i> Mixed
                        </span>
                    @endif
                </div>
                <div class="market-actions d-flex gap-2">
                    <livewire:save-event :event="$event" :key="$keyPrefix . '-single-' . $event->id" />
                </div>
            </div>
        </div>
    @endif
@endif

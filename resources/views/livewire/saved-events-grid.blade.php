<div>
    <!-- Search Bar with Home Button -->
    <div class="secondary-filters mb-3">
        <div class="filter-top-bar d-lg-flex d-block">
            <div class="row align-items-center justify-content-between w-100">
                <div class="secondary-search-bar" style="flex: 1; max-width: 100%; margin-right: 12px;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="Search saved events" class="secondary-search-input"
                        wire:model.live.debounce.300ms="search" style="width: 100%;">
                    @if ($search)
                        <button type="button"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 5px; z-index: 10;"
                            wire:click="$set('search', '')">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>
                <a href="{{ route('home') }}" class="bookmark-icon-btn" title="Back to Home" style="flex-shrink: 0;">
                    <i class="fas fa-home"></i>
                </a>
            </div>
        </div>
    </div>

    @if ($events->count() > 0)
        <!-- Markets Grid -->
        <div class="markets-grid mt-3 mt-lg-0">
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
                                <livewire:save-event :event="$event" :key="'saved-' . $event->id" />
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
                                <livewire:save-event :event="$event" :key="'saved-' . $event->id" />
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
    @else
        <div class="market-card empty-state-card">
            <div class="empty-state-icon-wrapper">
                <div class="empty-state-icon-circle">
                    <i class="fas fa-bookmark"></i>
                </div>
            </div>
            <h3 class="empty-state-title">No Saved Events</h3>
            <p class="empty-state-description">
                You haven't saved any events yet. Start saving events to access them quickly from your saved list!
            </p>
            <a href="{{ route('home') }}" class="empty-state-btn">
                <i class="fas fa-home"></i>
                <span>Browse Events</span>
            </a>
        </div>
    @endif

</div>

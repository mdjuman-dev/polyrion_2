<div wire:poll.5s="refreshEvents" data-component="sports-events-grid">
    <!-- Header Section -->
    <div class="sports-header-section" style="margin-bottom: 20px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <!-- Sport Name with Icon -->
            <div style="display: flex; align-items: center; gap: 12px;">
                @php
                    $sportIcon = 'fa-football';
                    $sportName = 'Sports';
                    if($category === 'nfl') {
                        $sportIcon = 'fa-football';
                        $sportName = 'NFL';
                    } elseif($category === 'nba') {
                        $sportIcon = 'fa-basketball-ball';
                        $sportName = 'NBA';
                    } elseif($category === 'nhl') {
                        $sportIcon = 'fa-hockey-puck';
                        $sportName = 'NHL';
                    } elseif($category === 'cricket') {
                        $sportIcon = 'fa-baseball-ball';
                        $sportName = 'Cricket';
                    }
                @endphp
                <i class="fas {{ $sportIcon }}" style="font-size: 24px; color: var(--accent);"></i>
                <h2 style="margin: 0; font-size: 24px; font-weight: 700; color: var(--text-primary);">{{ $sportName }}</h2>
            </div>

            <!-- Tabs and Controls -->
            <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <!-- Games/Props Tabs -->
                <div style="display: flex; gap: 8px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; padding: 4px;">
                    <button class="sports-content-tab active" style="padding: 8px 16px; background: var(--accent); color: #fff; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer;">Games</button>
                    <button class="sports-content-tab" style="padding: 8px 16px; background: transparent; color: var(--text-secondary); border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer;">Props</button>
                </div>

                <!-- Settings and Filters -->
                <div style="display: flex; align-items: center; gap: 12px;">
                    <button style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; padding: 8px 12px; color: var(--text-primary); cursor: pointer;">
                        <i class="fas fa-cog"></i>
                    </button>
                    <label style="display: flex; align-items: center; gap: 8px; color: var(--text-secondary); font-size: 14px; cursor: pointer;">
                        <input type="checkbox" style="cursor: pointer;">
                        <span>Show Spreads + Totals</span>
                    </label>
                    <select style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; padding: 8px 12px; color: var(--text-primary); font-size: 14px; cursor: pointer;">
                        <option>Week 13</option>
                        <option>Week 12</option>
                        <option>Week 11</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Games List by Date -->
    <div class="sports-games-list">
        @php
            // Group events by date
            $eventsByDate = $events->groupBy(function($event) {
                return $event->created_at ? $event->created_at->format('D, F j') : 'Other';
            });
        @endphp

        @foreach($eventsByDate as $date => $dateEvents)
            <div class="sports-date-section" style="margin-bottom: 32px;">
                <h3 style="color: var(--text-secondary); font-size: 14px; font-weight: 600; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">{{ $date }}</h3>
                
                @foreach($dateEvents as $event)
                    @php
                        $mainMarket = $event->markets->first();
                        $volume = $event->volume_24hr ?? $event->volume ?? 0;
                        $volumeFormatted = $volume >= 1000 ? number_format($volume / 1000, 2) . 'k' : number_format($volume);
                        
                        // Extract teams from title (e.g., "Bills vs Broncos")
                        $titleParts = explode(' vs ', $event->title);
                        $team1Full = $titleParts[0] ?? '';
                        $team2Full = $titleParts[1] ?? $event->title;
                        
                        // Extract team abbreviations (first 3 letters or common abbreviations)
                        $team1Abbr = strtoupper(substr($team1Full, 0, 3));
                        $team2Abbr = strtoupper(substr($team2Full, 0, 3));
                        
                        // Get team names without abbreviations
                        $team1Name = $team1Full;
                        $team2Name = $team2Full;
                        
                        // Get prices for moneyline, spread, total
                        $prices = $mainMarket ? (is_string($mainMarket->outcome_prices) ? json_decode($mainMarket->outcome_prices, true) : ($mainMarket->outcome_prices ?? [0.5, 0.5])) : [0.5, 0.5];
                        $price1 = isset($prices[0]) ? round($prices[0] * 100) : 49;
                        $price2 = isset($prices[1]) ? round($prices[1] * 100) : 52;
                        
                        // Spread prices (slightly different)
                        $spreadPrice1 = $price1 + 3;
                        $spreadPrice2 = $price2 - 2;
                        
                        // Total prices
                        $totalPrice1 = $price1 + 1;
                        $totalPrice2 = $price2 + 2;
                    @endphp

                    <div class="sports-game-card" style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; padding: 16px; margin-bottom: 12px;">
                        <!-- Top Header -->
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span style="background: #4a5568; color: #fff; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 500;">
                                    {{ $event->created_at ? $event->created_at->format('g:i A') : '3:30 AM' }}
                                </span>
                                <span style="color: var(--text-primary); font-size: 14px; font-weight: 500;">
                                    ${{ $volumeFormatted }} Vol.
                                </span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="background: #4a5568; color: #fff; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 500;">
                                    {{ $event->markets->count() }}
                                </span>
                                <a href="{{ route('market.details', $event->slug) }}" 
                                   style="color: var(--text-primary); text-decoration: none; font-size: 14px; font-weight: 500;">
                                    Game View >
                                </a>
                            </div>
                        </div>

                        <!-- Main Body: Teams on Left, Betting Options on Right -->
                        <div style="display: grid; grid-template-columns: 200px 1fr; gap: 24px; align-items: start;">
                            <!-- Left: Team Buttons -->
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <!-- Team 1 -->
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <button style="background: #3b82f6; color: #fff; border: none; border-radius: 6px; padding: 12px 16px; font-size: 14px; font-weight: 700; min-width: 60px; cursor: pointer;">
                                        {{ $team1Abbr }}
                                    </button>
                                    <span style="color: var(--text-primary); font-size: 14px; font-weight: 500;">
                                        {{ $team1Name }} 12-5
                                    </span>
                                </div>
                                
                                <!-- Team 2 -->
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <button style="background: #ff6b35; color: #fff; border: none; border-radius: 6px; padding: 12px 16px; font-size: 14px; font-weight: 700; min-width: 60px; cursor: pointer;">
                                        {{ $team2Abbr }}
                                    </button>
                                    <span style="color: var(--text-primary); font-size: 14px; font-weight: 500;">
                                        {{ $team2Name }} 14-3
                                    </span>
                                </div>
                            </div>

                            <!-- Right: Betting Options -->
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                                <!-- Moneyline -->
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    <div style="color: var(--text-secondary); font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Moneyline</div>
                                    <a href="{{ route('market.details', $event->slug) }}" 
                                       style="background: #3b82f6; color: #fff; border: none; border-radius: 6px; padding: 12px; text-align: center; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s; cursor: pointer;">
                                        {{ $team1Abbr }} {{ $price1 }}¢
                                    </a>
                                    <a href="{{ route('market.details', $event->slug) }}" 
                                       style="background: #ff6b35; color: #fff; border: none; border-radius: 6px; padding: 12px; text-align: center; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s; cursor: pointer;">
                                        {{ $team2Abbr }} {{ $price2 }}¢
                                    </a>
                                </div>

                                <!-- Spread -->
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    <div style="color: var(--text-secondary); font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Spread</div>
                                    <a href="{{ route('market.details', $event->slug) }}" 
                                       style="background: #4a5568; color: #fff; border: none; border-radius: 6px; padding: 12px; text-align: center; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s; cursor: pointer;">
                                        {{ $team1Abbr }} +1.5 {{ $spreadPrice1 }}¢
                                    </a>
                                    <a href="{{ route('market.details', $event->slug) }}" 
                                       style="background: #4a5568; color: #fff; border: none; border-radius: 6px; padding: 12px; text-align: center; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s; cursor: pointer;">
                                        {{ $team2Abbr }} -1.5 {{ $spreadPrice2 }}¢
                                    </a>
                                </div>

                                <!-- Total -->
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    <div style="color: var(--text-secondary); font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Total</div>
                                    <a href="{{ route('market.details', $event->slug) }}" 
                                       style="background: #4a5568; color: #fff; border: none; border-radius: 6px; padding: 12px; text-align: center; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s; cursor: pointer;">
                                        O 46.5 {{ $totalPrice1 }}¢
                                    </a>
                                    <a href="{{ route('market.details', $event->slug) }}" 
                                       style="background: #4a5568; color: #fff; border: none; border-radius: 6px; padding: 12px; text-align: center; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s; cursor: pointer;">
                                        U 46.5 {{ $totalPrice2 }}¢
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    @if ($hasMore)
        <div x-intersect.threshold.10="$wire.loadMore()" 
             x-intersect:enter="$wire.loadMore()"
             class="text-center" 
             style="padding: 20px; min-height: 60px;">
            <div wire:loading wire:target="loadMore" class="d-flex align-items-center justify-content-center gap-2">
                <span class="loader"></span>
                <span style="color: var(--text-secondary); font-size: 14px;">Loading more events...</span>
            </div>
        </div>
    @endif
</div>

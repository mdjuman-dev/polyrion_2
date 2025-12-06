<div class="trading-panel" id="tradingPanel" data-market-id="{{ $event->markets->first()->id ?? '' }}">
    <div class="panel-header">
        <div class="market-profile-img">
            <img src="{{ $event->image }}" alt="Profile">
        </div>
        <div class="panel-title-section">
            <h2 class="panel-market-title" id="panelMarketTitle">{{ $event->title ?? '' }}</h2>
            <span class="panel-outcome-name" id="panelOutcomeName">
                {{ $event->markets->first()->groupItem_title ?? '' }}
            </span>
        </div>
    </div>
    <button class="panel-close-btn hide-desktop" id="panelCloseBtn" aria-label="Close">
        <i class="fas fa-times"></i>
    </button>

    <div class="limit-order-fields" id="limitOrderFields">
        <div class="limit-input-group">
            <label class="limit-input-label">Limit Price</label>
            <input type="text" class="limit-input" id="limitPrice" placeholder="0.0¢">
        </div>
    </div>
    <div class="outcome-selection">
        <div class="outcome-buttons">
            @php
                $market = $event->markets->first();
                $prices = json_decode($market->outcome_prices ?? '[]', true);
                $yesPrice = isset($prices[1]) ? floatval($prices[1]) : 0.5;
                $noPrice = isset($prices[0]) ? floatval($prices[0]) : 0.5;

                $yesPriceCents = number_format($yesPrice * 100, 1);
                $noPriceCents = number_format($noPrice * 100, 1);
            @endphp
            <button class="outcome-btn-yes active" id="yesBtn" data-price="{{ $yesPrice }}">
                Yes {{ $yesPriceCents }}¢
            </button>
            <button class="outcome-btn-no" id="noBtn" data-price="{{ $noPrice }}">
                No {{ $noPriceCents }}¢
            </button>
        </div>
    </div>
    <div class="shares-input-group">
        <!-- Amount Section -->
        <div class="amount-section">
            <div class="amount-header my-2">
                <label class="input-label">Amount</label>
                <div class="amount-display-wrapper">
                    <input type="number" class="shares-input" id="sharesInput" min="0" value=""
                        placeholder="0" aria-label="Number of shares">
                    <span class="amount-currency">$</span>
                </div>
            </div>
            <div class="price-buttons">
                <button class="shares-price" data-price="1" onclick="updateShares(1)">+$1</button>
                <button class="shares-price" data-price="20" onclick="updateShares(20)">+$20</button>
                <button class="shares-price" data-price="50" onclick="updateShares(50)">+$50</button>
                <button class="shares-price" data-price="100" onclick="updateShares(100)">+$100</button>
            </div>
        </div>

        <!-- To Win Section -->
        <div class="trade-summary">
            <div class="to-win-section">
                <div class="to-win-left">
                    <div class="to-win-label">
                        <span>To win</span>
                        <i class="fas fa-coins" style="color: var(--success); margin-left: 4px;"></i>
                    </div>
                </div>
                <div class="to-win-value" id="potentialWin">$0</div>
            </div>
        </div>

        <!-- Trade Button -->
        <button class="trade-btn" id="executeTrade">Trade</button>
    </div>
</div>

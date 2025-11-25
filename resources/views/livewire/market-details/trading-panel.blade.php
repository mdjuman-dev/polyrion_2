<div class="trading-panel" id="tradingPanel">
    <div class="panel-header">
        <div class="market-profile-img">
            <img src="{{ $event->image }}" alt="Profile">
        </div>
        <div class="panel-title-wrapper">
            <div class="panel-title" id="panelOutcomeTitle">
                {{ $event->markets[0]->question }}</div>
        </div>
        <button class="panel-close-btn hide-desktop" id="panelCloseBtn" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="limit-order-fields" id="limitOrderFields">
        <div class="limit-input-group">
            <label class="limit-input-label">Limit Price</label>
            <input type="text" class="limit-input" id="limitPrice" placeholder="0.0¢">
        </div>
    </div>
    <div class="outcome-selection">
        <div class="outcome-buttons">
            <button class="outcome-btn-yes active" id="yesBtn">Yes</button>
            <button class="outcome-btn-no" id="noBtn">No</button>
        </div>
    </div>
    <div class="shares-input-group">
        <div class="shares-controls">
            <label class="input-label">Shares</label>
            <input type="number" class="shares-input" id="sharesInput" min="0" placeholder="0"
                aria-label="Number of shares">
        </div>
        <div class="price-buttons">
            <button class="shares-price" data-price="10" onclick="updateShares(10)">+10$</button>
            <button class="shares-price" data-price="100" onclick="updateShares(100)">+100$</button>
            <button class="shares-price" data-price="-10" onclick="updateShares(-10)">-10$</button>
            <button class="shares-price" data-price="-100" onclick="updateShares(-100)">-100$</button>
        </div>

        <div class="trade-summary">
            <div class="summary-row">
                <span>Total</span>
                <span class="summary-value" id="totalCost">$0</span>
            </div>
            <div class="summary-row">
                <span>To Win</span>
                <span class="summary-value" id="potentialWin">$0</span>
            </div>
        </div>
        <button class="trade-btn" id="executeTrade">Bay Yes</button>
    </div>
</div>

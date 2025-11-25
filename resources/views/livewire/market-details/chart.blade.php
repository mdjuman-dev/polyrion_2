<div wire:poll.3s="refreshEvent">
    <div class="market-detail-header">
        <div class="market-header-top">
            <div class="market-header-left">
                <div class="market-profile-img">
                    <img src="{{ $event->image }}" alt="Profile">
                </div>
                <div class="market-header-info">
                    <h1 class="market-title">{{ $event->title }}</h1>
                    <div class="market-header-meta">
                        <span class="market-volume">${{ number_format($event->volume) }} Vol.</span>
                        <span class="market-date">{{ format_date($event->start_date) }}</span>
                    </div>
                </div>
            </div>
            <div class="market-header-actions">
                <button class="market-action-btn" aria-label="Share">
                    <i class="fas fa-link"></i>
                </button>
                <button class="market-action-btn" aria-label="Bookmark">
                    <i class="fas fa-bookmark"></i>
                </button>
            </div>
        </div>

    </div>

    <div class="chart-container">
        <div id="chart-container" style="width: 100%; height: 360px;"></div>
        <div class="chart-controls">
            <button class="chart-btn">1H</button>
            <button class="chart-btn">6H</button>
            <button class="chart-btn">1D</button>
            <button class="chart-btn">1W</button>
            <button class="chart-btn">1M</button>
            <button class="chart-btn active">ALL</button>
        </div>
    </div>
</div>

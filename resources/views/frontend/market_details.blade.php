@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Polymarket - Market Details</title>
@endsection
@section('content')
    <main>
        <div class="main-layout">
            <div class="main-content">
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
                            <livewire:save-event :event="$event" />
                        </div>
                    </div>
                </div>


                <div class="chart-container">
                    <!-- Custom Legend with Icons (Above Chart - Polymarket style) -->
                    <div id="chart-legend-custom" class="chart-legend-custom" style="display: none; margin-bottom: 1rem;">
                        <!-- Will be populated by JavaScript -->
                    </div>

                    <!-- Chart (Polymarket style) -->
                    <div id="polyChart"
                        style="width:100%; height:400px; background: #111b2b; border-radius: 8px; margin-bottom: 1rem;">
                    </div>

                    <div class="chart-controls">
                        <button class="chart-btn" data-period="1h">1H</button>
                        <button class="chart-btn" data-period="6h">6H</button>
                        <button class="chart-btn" data-period="1d">1D</button>
                        <button class="chart-btn" data-period="1w">1W</button>
                        <button class="chart-btn" data-period="1m">1M</button>
                        <button class="chart-btn active" data-period="all">ALL</button>
                    </div>
                </div>

                <livewire:market-details.markets :event="$event" />
                <div class="tab-container">
                    <div class="tab-nav">
                        <livewire:market-details.comments-count :event="$event" />
                    </div>

                    <div class="tab-content active" id="comments">
                        <livewire:market-details.comments :event="$event" />
                    </div>
                </div>
            </div>
            <!-- Mobile Panel Overlay -->
            <div class="mobile-panel-overlay" id="mobilePanelOverlay"></div>
            <livewire:market-details.trading-panel :event="$event" />
        </div>
    </main>

    @push('script')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"
            integrity="sha512-k37wQcV4v2h6jgYf5IUz1MoSKPpDs630XGSmCaCCOXxy2awgAWKHGZWr9nMyGgk3IOxA1NxdkN8r1JHgkUtMoQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @endpush

    @push('script')
        {{-- <script>
            function initChart() {
                const container = document.getElementById('chart-container');
                if (!container || typeof echarts === 'undefined') return;

                const textPrimary = getComputedStyle(document.documentElement).getPropertyValue('--text-primary').trim() ||
                    '#fff';
                const textSecondary = getComputedStyle(document.documentElement).getPropertyValue('--text-secondary').trim() ||
                    '#aaa';
                const accentColor = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#ffb11a';
                const borderColor = getComputedStyle(document.documentElement).getPropertyValue('--border').trim() ||
                    '#ffb11a33';

                const chart = echarts.init(container);

                let currentPeriod = 'all';
                const btns = document.querySelectorAll('.chart-btn');
                const priceEl = document.getElementById('chart-price-value');
                const timeEl = document.getElementById('chart-time-value');

                @php
                    $initialPrice = 50;
                    $eventVolume = $event->volume ?? 0;
                    $volume24hr = $event->volume_24hr ?? 0;
                    $volume1wk = $event->volume_1wk ?? 0;
                    $volume1mo = $event->volume_1mo ?? 0;
                    $eventStartDate = $event->start_date ? $event->start_date->toIso8601String() : null;

                    if ($event->markets && $event->markets->count() > 0) {
                        $firstMarket = $event->markets->first();
                        if ($firstMarket->outcome_prices) {
                            $prices = json_decode($firstMarket->outcome_prices, true);
                            if (is_array($prices) && isset($prices[0])) {
                                $initialPrice = $prices[0] * 100;
                            }
                        }
                    }
                @endphp
                let basePrice = {{ $initialPrice }};
                let eventVolume = {{ $eventVolume }};
                let volume24hr = {{ $volume24hr }};
                let volume1wk = {{ $volume1wk }};
                let volume1mo = {{ $volume1mo }};
                let eventStartDate = @json($eventStartDate);
                let history = [];
                let liveMarketsData = []; // Store live data for all markets from Polymarket

                // Color palette for different markets (Polymarket style)
                const marketColors = [
                    '#ff9500', // Orange (first market - most common)
                    '#007aff', // Blue
                    '#5ac8fa', // Light Blue
                    '#ffcc00', // Yellow
                    '#ff2d55', // Pink/Red
                    '#34c759', // Green
                    '#af52de', // Purple
                    '#ff9500', // Orange (repeat if needed)
                ];

                // Fetch live historical data from Polymarket API
                async function fetchLiveHistoryData() {
                    try {
                        const response = await fetch('{{ route('api.market.history.data', $event->slug) }}');
                        if (!response.ok) {
                            console.warn('Failed to fetch history data');
                            return null;
                        }

                        const data = await response.json();
                        if (data.success && data.markets && Array.isArray(data.markets) && data.markets.length > 0) {
                            liveMarketsData = data.markets.map(market => ({
                                market_id: market.market_id,
                                question: market.question,
                                current_price: parseFloat(market.current_price || 50),
                                history: (market.history || []).map(item => ({
                                    time: new Date(item.time),
                                    price: parseFloat(item.price)
                                }))
                            }));

                            // Update basePrice from first market
                            if (liveMarketsData.length > 0 && liveMarketsData[0].current_price !== undefined) {
                                basePrice = liveMarketsData[0].current_price;
                            }

                            // Update legend with initial data
                            if (liveMarketsData.length > 0) {
                                const marketsSeriesData = liveMarketsData.map((market, index) => ({
                                    market: market,
                                    history: market.history || [],
                                    color: marketColors[index % marketColors.length]
                                })).filter(m => m.history.length > 0);
                                updateChartLegend(marketsSeriesData);
                            }

                            return liveMarketsData;
                        }
                    } catch (error) {
                        console.warn('Error fetching live history data:', error);
                    }
                    return null;
                }

                function buildHistory(period) {
                    const now = new Date();
                    const startTime = eventStartDate ? new Date(eventStartDate) : new Date(now.getTime() - 30 * 24 * 60 * 60 *
                        1000);

                    // If we have live data for markets, process them
                    if (liveMarketsData && liveMarketsData.length > 0) {
                        // Process each market's history
                        liveMarketsData.forEach(market => {
                            market.filteredHistory = filterHistoryByPeriod(market.history, period, startTime, now);

                            // Ensure last point is current market price
                            if (market.filteredHistory.length > 0) {
                                const last = market.filteredHistory[market.filteredHistory.length - 1];
                                last.price = parseFloat(market.current_price.toFixed(2));
                                last.time = now;
                            }
                        });
                        return;
                    }

                    // Fallback: Generate data if live data not available
                    const config = {
                        '1h': {
                            points: 60,
                            interval: 1,
                            volumeMultiplier: volume24hr / 24
                        },
                        '6h': {
                            points: 72,
                            interval: 5,
                            volumeMultiplier: volume24hr / 4
                        },
                        '1d': {
                            points: 96,
                            interval: 15,
                            volumeMultiplier: volume24hr
                        },
                        '1w': {
                            points: 168,
                            interval: 60,
                            volumeMultiplier: volume1wk
                        },
                        '1m': {
                            points: 30,
                            interval: 1440,
                            volumeMultiplier: volume1mo
                        },
                        'all': {
                            points: 60,
                            interval: 1440,
                            volumeMultiplier: eventVolume
                        }
                    };

                    const {
                        points,
                        interval,
                        volumeMultiplier
                    } = config[period] || config['all'];
                    history = [];

                    let startPrice = basePrice + (Math.random() * 12 - 6);
                    startPrice = Math.max(2, Math.min(98, startPrice));

                    // Use volume data to influence price movement
                    const volumeFactor = Math.min(1, Math.max(0.1, volumeMultiplier / (eventVolume || 1)));

                    for (let i = points; i >= 0; i--) {
                        const t = new Date(now.getTime() - i * interval * 60 * 1000);
                        if (t < startTime && period === 'all') continue;

                        const progress = (points - i) / points;
                        const p = startPrice + (basePrice - startPrice) * progress;

                        // Volume-based volatility
                        const volatility = (Math.random() - 0.5) * 2 * volumeFactor;
                        let price = Math.max(1, Math.min(99, p + volatility));

                        // Ensure the last point is exactly at basePrice (current market price)
                        if (i === 0) {
                            price = basePrice;
                        }

                        history.push({
                            time: t,
                            price: parseFloat(price.toFixed(2))
                        });
                    }

                    // Ensure we have at least some data
                    if (history.length === 0) {
                        history.push({
                            time: now,
                            price: parseFloat(basePrice.toFixed(2))
                        });
                    }

                    // Always ensure the last point is exactly at current market price
                    const last = history[history.length - 1];
                    last.price = parseFloat(basePrice.toFixed(2));
                    last.time = now;

                    // Also ensure second-to-last point leads smoothly to current price
                    if (history.length > 1) {
                        const secondLast = history[history.length - 2];
                        const priceDiff = basePrice - secondLast.price;
                        // Smooth transition to current price (within last 10% of data)
                        if (Math.abs(priceDiff) > 5) {
                            secondLast.price = parseFloat((basePrice - priceDiff * 0.3).toFixed(2));
                        }
                    }
                }

                function filterHistoryByPeriod(data, period, startTime, now) {
                    let timeRange;
                    switch (period) {
                        case '1h':
                            timeRange = 60 * 60 * 1000; // 1 hour
                            break;
                        case '6h':
                            timeRange = 6 * 60 * 60 * 1000; // 6 hours
                            break;
                        case '1d':
                            timeRange = 24 * 60 * 60 * 1000; // 1 day
                            break;
                        case '1w':
                            timeRange = 7 * 24 * 60 * 60 * 1000; // 1 week
                            break;
                        case '1m':
                            timeRange = 30 * 24 * 60 * 60 * 1000; // 1 month
                            break;
                        default: // 'all'
                            timeRange = null;
                    }

                    const cutoffTime = timeRange ? new Date(now.getTime() - timeRange) : startTime;

                    return data.filter(item => {
                        const itemTime = new Date(item.time);
                        return itemTime >= cutoffTime && itemTime <= now;
                    }).map(item => ({
                        time: new Date(item.time),
                        price: item.price
                    }));
                }

                function formatTime(t, period) {
                    if (['1h', '6h', '1d'].includes(period)) {
                        return t.toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: false
                        });
                    }
                    if (period === '1w') {
                        return t.toLocaleDateString('en-US', {
                            month: 'short',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: false
                        });
                    }
                    return t.toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: period === 'all' ? 'numeric' : undefined
                    });
                }

                function updateChart(period) {
                    // Always rebuild history for the selected period
                    buildHistory(period);

                    // Prepare data for all markets
                    let allTimePoints = new Set();
                    let marketsSeriesData = [];

                    if (liveMarketsData && liveMarketsData.length > 0) {
                        // Process each market
                        liveMarketsData.forEach((market, index) => {
                            const marketHistory = market.filteredHistory || market.history || [];

                            if (marketHistory.length > 0) {
                                // Collect all time points
                                marketHistory.forEach(item => {
                                    allTimePoints.add(item.time.getTime());
                                });

                                marketsSeriesData.push({
                                    market: market,
                                    history: marketHistory,
                                    color: marketColors[index % marketColors.length]
                                });
                            }
                        });

                        // Sort time points
                        const sortedTimes = Array.from(allTimePoints).sort((a, b) => a - b);
                        const x = sortedTimes.map(t => formatTime(new Date(t), period));

                        // Create series for each market with gradient
                        const series = marketsSeriesData.map((marketData, index) => {
                            const marketHistory = marketData.history;
                            const y = sortedTimes.map(time => {
                                // Find closest data point for this time
                                const timeMs = time;
                                let closest = null;
                                let minDiff = Infinity;

                                marketHistory.forEach(item => {
                                    const diff = Math.abs(item.time.getTime() - timeMs);
                                    if (diff < minDiff) {
                                        minDiff = diff;
                                        closest = item;
                                    }
                                });

                                return closest ? closest.price : null;
                            });

                            // Solid line color with vertical gradient area (like Polymarket)
                            const baseColor = marketData.color;

                            return {
                                name: marketData.market.question || `Market ${index + 1}`,
                                type: 'line',
                                smooth: true,
                                showSymbol: false,
                                data: y,
                                lineStyle: {
                                    width: 2,
                                    color: baseColor // Solid color line (no gradient)
                                },
                                areaStyle: {
                                    show: index === 0, // Only show area for first market
                                    color: {
                                        type: 'linear',
                                        x: 0,
                                        y: 0,
                                        x2: 0,
                                        y2: 1,
                                        colorStops: [{
                                                offset: 0,
                                                color: baseColor + '80' // Darker at bottom (more opaque)
                                            },
                                            {
                                                offset: 0.5,
                                                color: baseColor + '50' // Medium in middle
                                            },
                                            {
                                                offset: 1,
                                                color: baseColor + '10' // Lighter at top (more transparent)
                                            }
                                        ]
                                    }
                                },
                                emphasis: {
                                    focus: 'series',
                                    lineStyle: {
                                        width: 3
                                    }
                                }
                            };
                        });

                        // Update price display with first market's last price
                        if (marketsSeriesData.length > 0 && marketsSeriesData[0].history.length > 0) {
                            const firstMarketLast = marketsSeriesData[0].history[marketsSeriesData[0].history.length - 1];
                            if (priceEl) priceEl.textContent = firstMarketLast.price.toFixed(2) + '%';
                            if (timeEl) timeEl.textContent = formatTime(firstMarketLast.time, period);
                        }

                        // Update legend with market data
                        updateChartLegend(marketsSeriesData);

                        // Update chart with multiple series
                        updateChartWithSeries(x, series, period);
                        return;
                    }

                    // Fallback: single market (old behavior)
                    const x = history.map(d => formatTime(d.time, period));
                    const y = history.map(d => d.price);

                    if (x.length === 0 || y.length === 0) {
                        console.warn('No chart data available, using fallback');
                        const now = new Date();
                        x.push(formatTime(now, period));
                        y.push(basePrice);
                    }

                    const last = history[history.length - 1];
                    if (priceEl) priceEl.textContent = last.price.toFixed(2) + '%';
                    if (timeEl) timeEl.textContent = formatTime(last.time, period);

                    // Use old single series update with solid line and vertical gradient area
                    updateChartWithSeries(x, [{
                        name: 'Price',
                        type: 'line',
                        smooth: true,
                        showSymbol: false,
                        data: y,
                        lineStyle: {
                            width: 2,
                            color: accentColor // Solid color line (no gradient)
                        },
                        areaStyle: {
                            color: {
                                type: 'linear',
                                x: 0,
                                y: 0,
                                x2: 0,
                                y2: 1,
                                colorStops: [{
                                        offset: 0,
                                        color: accentColor + '80' // Darker at bottom
                                    },
                                    {
                                        offset: 0.5,
                                        color: accentColor + '50' // Medium in middle
                                    },
                                    {
                                        offset: 1,
                                        color: accentColor + '10' // Lighter at top
                                    }
                                ]
                            }
                        },
                        emphasis: {
                            focus: 'series',
                            lineStyle: {
                                width: 3
                            }
                        }
                    }], period);
                }

                function updateChartLegend(marketsSeriesData) {
                    const legendWrapper = document.getElementById('chart-legend-wrapper');
                    const legendEl = document.getElementById('chart-legend');
                    if (!legendEl || !legendWrapper) return;

                    // Only show legend if there are multiple markets
                    if (marketsSeriesData.length <= 1) {
                        legendWrapper.style.display = 'none';
                        return;
                    }

                    legendWrapper.style.display = 'block';
                    legendEl.innerHTML = '';

                    marketsSeriesData.forEach((marketData, index) => {
                        const market = marketData.market;
                        const currentPrice = market.current_price || 0;
                        let priceText;
                        if (currentPrice < 1) {
                            priceText = '<1%';
                        } else if (currentPrice >= 99) {
                            priceText = '>99%';
                        } else {
                            priceText = currentPrice.toFixed(0) + '%';
                        }

                        // Truncate market question if too long (Polymarket style - shorter names)
                        let marketName = market.question || `Market ${index + 1}`;
                        if (marketName.length > 35) {
                            marketName = marketName.substring(0, 32) + '...';
                        }

                        const legendItem = document.createElement('div');
                        legendItem.className = 'chart-legend-item';

                        legendItem.innerHTML = `
                            <span class="chart-legend-color" style="background: ${marketData.color};"></span>
                            <span class="chart-legend-text">${marketName} ${priceText}</span>
                        `;

                        legendEl.appendChild(legendItem);
                    });
                }

                function updateChartWithSeries(x, series, period) {
                    const option = {
                        backgroundColor: 'transparent',
                        tooltip: {
                            trigger: 'axis',
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            borderColor: borderColor,
                            borderWidth: 1,
                            textStyle: {
                                color: textPrimary
                            },
                            formatter: function(params) {
                                let result = `<div style="padding:6px;font-size:13px;">`;
                                params.forEach((param, index) => {
                                    if (param.value !== null && param.value !== undefined) {
                                        const color = param.color || accentColor;
                                        result += `
                                            <div style="margin-bottom:4px;">
                                                <span style="display:inline-block;width:10px;height:10px;background:${color};border-radius:50%;margin-right:6px;"></span>
                                                <span style="color:${textPrimary};">${param.seriesName}: </span>
                                                <span style="color:${color};font-weight:600;">${param.value}%</span>
                                            </div>`;
                                    }
                                });
                                result +=
                                    `<div style="margin-top:4px;color:${textSecondary};font-size:11px;">${params[0].name}</div>`;
                                result += `</div>`;
                                return result;
                            }
                        },
                        legend: {
                            show: false // Use custom legend instead
                        },
                        grid: {
                            left: '3%',
                            right: '3%',
                            top: '8%',
                            bottom: '15%',
                            containLabel: false
                        },
                        xAxis: {
                            type: 'category',
                            boundaryGap: false,
                            data: x,
                            axisLine: {
                                show: false
                            },
                            axisTick: {
                                show: false
                            },
                            axisLabel: {
                                color: textSecondary,
                                fontSize: 11,
                                interval: 'auto'
                            },
                            splitLine: {
                                show: false
                            }
                        },
                        yAxis: {
                            type: 'value',
                            min: 0,
                            max: 100,
                            axisLine: {
                                show: false
                            },
                            axisTick: {
                                show: false
                            },
                            axisLabel: {
                                color: textSecondary,
                                fontSize: 11,
                                formatter: '{value}%'
                            },
                            splitLine: {
                                show: true,
                                lineStyle: {
                                    color: borderColor,
                                    type: 'dashed',
                                    opacity: 0.3
                                }
                            }
                        },
                        series: series
                    };

                    chart.setOption(option, true);

                    // Ensure chart is visible
                    chart.resize();
                }

                btns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        btns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');

                        currentPeriod = this.dataset.period;
                        // Rebuild history for new period
                        buildHistory(currentPeriod);
                        updateChart(currentPeriod);
                    });
                });

                async function fetchMarketData() {
                    try {
                        // Fetch both current price and history data
                        const [priceResponse, historyResponse] = await Promise.all([
                            fetch('{{ route('api.market.price.data', $event->slug) }}'),
                            fetch('{{ route('api.market.history.data', $event->slug) }}')
                        ]);

                        // Update current price
                        if (priceResponse.ok) {
                            const d = await priceResponse.json();
                            if (d.current_price !== undefined) {
                                const newPrice = parseFloat(d.current_price);
                                // Only update if price actually changed
                                if (Math.abs(newPrice - basePrice) > 0.01) {
                                    basePrice = newPrice;
                                }
                            }
                        }

                        // Update history data
                        if (historyResponse.ok) {
                            const historyData = await historyResponse.json();
                            if (historyData.success && historyData.markets && Array.isArray(historyData.markets) &&
                                historyData.markets.length > 0) {
                                liveMarketsData = historyData.markets.map(market => ({
                                    market_id: market.market_id,
                                    question: market.question,
                                    current_price: parseFloat(market.current_price || 50),
                                    history: (market.history || []).map(item => ({
                                        time: new Date(item.time),
                                        price: parseFloat(item.price)
                                    }))
                                }));

                                // Update basePrice from first market
                                if (liveMarketsData.length > 0 && liveMarketsData[0].current_price !== undefined) {
                                    basePrice = liveMarketsData[0].current_price;
                                }

                                // Rebuild history for current period with live data
                                buildHistory(currentPeriod);
                                updateChart(currentPeriod);

                                // Update legend
                                if (liveMarketsData.length > 0) {
                                    const marketsSeriesData = liveMarketsData.map((market, index) => ({
                                        market: market,
                                        history: market.filteredHistory || market.history || [],
                                        color: marketColors[index % marketColors.length]
                                    })).filter(m => m.history.length > 0);
                                    updateChartLegend(marketsSeriesData);
                                }
                                return;
                            }
                        }

                        // Fallback: update chart with current price only
                        buildHistory(currentPeriod);
                        updateChart(currentPeriod);
                    } catch (error) {
                        console.warn('Failed to fetch market data:', error);
                        // Still try to update chart with existing data
                        buildHistory(currentPeriod);
                        updateChart(currentPeriod);
                    }
                }

                // Also listen for market price updates from the page
                function syncChartWithMarketPrice() {
                    // Try to get current market price from the first market row
                    const firstMarketRow = document.querySelector('.outcome-row.first-market');
                    if (firstMarketRow) {
                        const percentElement = firstMarketRow.querySelector('.outcome-percent');
                        if (percentElement) {
                            const marketPrice = parseFloat(percentElement.textContent.replace('%', ''));
                            if (!isNaN(marketPrice) && Math.abs(marketPrice - basePrice) > 0.01) {
                                basePrice = marketPrice;
                                buildHistory(currentPeriod);
                                updateChart(currentPeriod);
                            }
                        }
                    }
                }

                // Sync chart when market prices update (check every 2 seconds)
                setInterval(syncChartWithMarketPrice, 2000);

                // Fetch live data first, then initialize chart
                fetchLiveHistoryData().then(() => {
                    // Initialize chart with live data
                    buildHistory(currentPeriod);
                    updateChart(currentPeriod);

                    // Set up periodic updates (every 5 seconds for live data)
                    setInterval(fetchMarketData, 5000);
                }).catch(() => {
                    // Fallback if live data fetch fails
                    buildHistory(currentPeriod);
                    updateChart(currentPeriod);
                    setInterval(fetchMarketData, 5000);
                });

                // Handle resize
                window.addEventListener('resize', () => {
                    if (chart) {
                        chart.resize();
                    }
                });

                // Update on hover
                chart.on('mousemove', function(params) {
                    if (params.seriesData && params.seriesData.length > 0) {
                        const dataIndex = params.dataIndex;
                        // Get first market's data for display
                        if (liveMarketsData && liveMarketsData.length > 0) {
                            const firstMarket = liveMarketsData[0];
                            const filteredHistory = firstMarket.filteredHistory || firstMarket.history || [];
                            if (filteredHistory[dataIndex]) {
                                if (priceEl) priceEl.textContent = filteredHistory[dataIndex].price.toFixed(2) + '%';
                                if (timeEl) timeEl.textContent = formatTime(filteredHistory[dataIndex].time,
                                    currentPeriod);
                            }
                        } else if (history[dataIndex]) {
                            if (priceEl) priceEl.textContent = history[dataIndex].price.toFixed(2) + '%';
                            if (timeEl) timeEl.textContent = formatTime(history[dataIndex].time, currentPeriod);
                        }
                    }
                });

                chart.on('mouseout', function() {
                    if (liveMarketsData && liveMarketsData.length > 0) {
                        const firstMarket = liveMarketsData[0];
                        const filteredHistory = firstMarket.filteredHistory || firstMarket.history || [];
                        if (filteredHistory.length > 0) {
                            const last = filteredHistory[filteredHistory.length - 1];
                            if (priceEl) priceEl.textContent = last.price.toFixed(2) + '%';
                            if (timeEl) timeEl.textContent = formatTime(last.time, currentPeriod);
                        }
                    } else if (history.length > 0) {
                        const last = history[history.length - 1];
                        if (priceEl) priceEl.textContent = last.price.toFixed(2) + '%';
                        if (timeEl) timeEl.textContent = formatTime(last.time, currentPeriod);
                    }
                });

                return chart;
            }

            function tryInitChart() {
                const container = document.getElementById('chart-container');
                if (!container) {
                    console.warn('Chart container not found');
                    return;
                }

                if (typeof echarts !== 'undefined') {
                    console.log('ECharts loaded, initializing chart...');
                    const chart = initChart();
                    if (chart) {
                        console.log('Chart initialized successfully');
                    } else {
                        console.error('Chart initialization returned null');
                    }
                } else {
                    console.warn('ECharts not loaded yet, retrying...');
                    setTimeout(tryInitChart, 500);
                }
            }

            // Wait for both DOM and ECharts
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    setTimeout(tryInitChart, 200);
                });
            } else {
                setTimeout(tryInitChart, 200);
            }

            // Auto-select first market and scroll to trading panel
            function selectFirstMarket() {
                const firstMarketRow = document.querySelector('.outcome-row.first-market');
                if (firstMarketRow) {
                    // Trigger click on first market's Yes button to populate trading panel
                    const yesBtn = firstMarketRow.querySelector('.btn-yes');
                    if (yesBtn) {
                        setTimeout(() => {
                            yesBtn.click();
                            // Scroll to trading panel on mobile
                            if (window.innerWidth < 768) {
                                const tradingPanel = document.getElementById('tradingPanel');
                                if (tradingPanel) {
                                    tradingPanel.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'nearest'
                                    });
                                }
                            }
                        }, 500);
                    }
                }
            }

            // Wait for page to fully load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    setTimeout(selectFirstMarket, 800);
                });
            } else {
                setTimeout(selectFirstMarket, 800);
            }
        </script> --}}

        <script>
            // Data from Laravel backend
            const seriesData = @json($seriesData ?? []);
            const labels = @json($labels ?? []);

            // Wait for ECharts to load and DOM to be ready
            function initPolyChart() {
                if (typeof echarts === 'undefined') {
                    console.warn('ECharts not loaded yet, retrying...');
                    setTimeout(initPolyChart, 100);
                    return;
                }

                const chartElement = document.getElementById('polyChart');
                if (!chartElement) {
                    console.error('Chart element not found');
                    return;
                }

                if (!seriesData || seriesData.length === 0) {
                    console.warn('No series data available');
                    chartElement.innerHTML =
                        '<p style="color: var(--text-secondary); text-align: center; padding: 2rem;">No market data available</p>';
                    return;
                }

                let chart = echarts.init(chartElement);

                // Calculate dynamic y-axis max based on data
                let maxValue = 0;
                seriesData.forEach(item => {
                    if (item.data && Array.isArray(item.data)) {
                        const itemMax = Math.max(...item.data.filter(v => v !== null && v !== undefined));
                        if (itemMax > maxValue) {
                            maxValue = itemMax;
                        }
                    }
                });
                // Round up to nearest 5 and add 5% padding, but cap at 100
                const yAxisMax = Math.min(100, Math.ceil((maxValue + 5) / 5) * 5);

                let option = {
                    backgroundColor: "#111b2b",

                    tooltip: {
                        trigger: 'axis',
                        backgroundColor: "#1d2b3a",
                        borderColor: "#1d2b3a",
                        borderWidth: 0,
                        textStyle: {
                            color: "#fff",
                            fontSize: 12
                        },
                        padding: [8, 12],
                        formatter: function(params) {
                            let result = '';
                            params.forEach((param, index) => {
                                if (param.value !== null && param.value !== undefined) {
                                    const color = param.color || '#fff';
                                    result += `<div style="margin-bottom: 4px;">
                                        <span style="display: inline-block; width: 8px; height: 8px; background: ${color}; border-radius: 50%; margin-right: 6px;"></span>
                                        <span style="color: #fff;">${param.seriesName}: </span>
                                        <span style="color: ${color}; font-weight: 600;">${param.value}%</span>
                                    </div>`;
                                }
                            });
                            if (params[0]) {
                                result +=
                                    `<div style="margin-top: 6px; color: #9ab1c6; font-size: 11px;">${params[0].name}</div>`;
                            }
                            return result;
                        }
                    },

                    legend: {
                        show: false // Use custom legend with icons instead
                    },

                    grid: {
                        left: "3%",
                        right: "4%",
                        bottom: "8%",
                        top: "15%", // Less space needed since custom legend is below
                        containLabel: true
                    },

                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: labels,
                        axisLine: {
                            show: false
                        },
                        axisTick: {
                            show: false
                        },
                        axisLabel: {
                            color: "#9ab1c6",
                            fontSize: 11
                        }
                    },

                    yAxis: {
                        type: 'value',
                        min: 0,
                        max: yAxisMax,
                        axisLine: {
                            show: false
                        },
                        axisTick: {
                            show: false
                        },
                        axisLabel: {
                            color: "#9ab1c6",
                            fontSize: 11,
                            formatter: '{value}%'
                        },
                        splitLine: {
                            show: true,
                            lineStyle: {
                                color: "#1f2f44",
                                type: 'solid',
                                width: 1
                            }
                        }
                    },

                    series: seriesData.map((item) => ({
                        name: item.name,
                        type: 'line',
                        smooth: true,
                        showSymbol: false,
                        data: item.data,
                        lineStyle: {
                            width: 2,
                            color: item.color
                        },
                        areaStyle: {
                            show: true, // Show area for all markets (like image)
                            color: {
                                type: 'linear',
                                x: 0,
                                y: 0,
                                x2: 0,
                                y2: 1,
                                colorStops: [{
                                        offset: 0,
                                        color: item.color + '40' // More transparent for all lines
                                    },
                                    {
                                        offset: 1,
                                        color: item.color + '05' // Very transparent at top
                                    }
                                ]
                            }
                        },
                        emphasis: {
                            focus: 'series',
                            lineStyle: {
                                width: 3
                            }
                        }
                    }))
                };

                chart.setOption(option);
                window.addEventListener("resize", () => chart.resize());

                // Create custom legend with icons
                createCustomLegend(seriesData);
            }



            // Initialize chart when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initPolyChart);
            } else {
                initPolyChart();
            }
        </script>
    @endpush
@endsection

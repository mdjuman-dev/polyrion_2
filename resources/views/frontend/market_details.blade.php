@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>{{ $event->title }}</title>
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

                // Fetch live historical data from Polymarket API - deferred for faster initial load
                async function fetchLiveHistoryData() {
                    // Defer API call to not block initial page render
                    await new Promise(resolve => setTimeout(resolve, 500));
                    
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

                // Initialize chart immediately with static data, then fetch live data in background
                    buildHistory(currentPeriod);
                    updateChart(currentPeriod);

                // Fetch live data in background after initial render (deferred)
                setTimeout(() => {
                    fetchLiveHistoryData().then(() => {
                    buildHistory(currentPeriod);
                    updateChart(currentPeriod);
                        // Set up periodic updates (every 10 seconds for live data - increased from 5s)
                        setInterval(fetchMarketData, 10000);
                    }).catch(() => {
                        // Fallback if live data fetch fails
                        setInterval(fetchMarketData, 10000);
                    });
                }, 1000); // Wait 1 second after page load

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

                // Polymarket always uses 0-100% scale
                const yAxisMax = 100;

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
                function createCustomLegend(data) {
                    const legendContainer = document.getElementById('chart-legend-custom');
                    if (!legendContainer || !data || data.length <= 1) {
                        if (legendContainer) legendContainer.style.display = 'none';
                        return;
                    }

                    legendContainer.style.display = 'flex';
                    legendContainer.style.flexWrap = 'wrap';
                    legendContainer.style.gap = '16px';
                    legendContainer.style.padding = '12px 0';
                    legendContainer.style.borderBottom = '1px solid rgba(255, 255, 255, 0.1)';
                    legendContainer.style.marginBottom = '16px';
                    legendContainer.innerHTML = '';

                    data.forEach((item, index) => {
                        const legendItem = document.createElement('div');
                        legendItem.style.display = 'flex';
                        legendItem.style.alignItems = 'center';
                        legendItem.style.gap = '8px';
                        legendItem.style.cursor = 'pointer';
                        legendItem.style.padding = '4px 8px';
                        legendItem.style.borderRadius = '4px';
                        legendItem.style.transition = 'background-color 0.2s';

                        const colorBox = document.createElement('div');
                        colorBox.style.width = '12px';
                        colorBox.style.height = '12px';
                        colorBox.style.borderRadius = '2px';
                        colorBox.style.backgroundColor = item.color;
                        colorBox.style.flexShrink = '0';

                        const label = document.createElement('span');
                        label.style.color = '#ffffff';
                        label.style.fontSize = '12px';
                        label.style.fontFamily = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                        label.textContent = item.name;

                        legendItem.appendChild(colorBox);
                        legendItem.appendChild(label);

                        // Hover effect
                        legendItem.addEventListener('mouseenter', () => {
                            legendItem.style.backgroundColor = 'rgba(255, 255, 255, 0.05)';
                        });
                        legendItem.addEventListener('mouseleave', () => {
                            legendItem.style.backgroundColor = 'transparent';
                        });

                        legendContainer.appendChild(legendItem);
                    });
                }

                createCustomLegend(seriesData);

                // Store chart instance globally for period filtering
                window.polyChart = chart;
                window.originalSeriesData = seriesData;
                window.originalLabels = labels;

                // Handle chart period button clicks
                const chartButtons = document.querySelectorAll('.chart-btn');
                chartButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        // Remove active class from all buttons
                        chartButtons.forEach(b => b.classList.remove('active'));
                        // Add active class to clicked button
                        this.classList.add('active');

                        const period = this.getAttribute('data-period');
                        filterChartByPeriod(period);
                    });
                });
            }

            // Filter chart data by period
            function filterChartByPeriod(period) {
                if (!window.polyChart || !window.originalSeriesData || !window.originalLabels) {
                    return;
                }

                const chart = window.polyChart;
                const originalData = window.originalSeriesData;
                const originalLabels = window.originalLabels;

                // Calculate how many data points to show based on period
                let dataPointsToShow = originalLabels.length;

                switch (period) {
                    case '1h':
                        dataPointsToShow = Math.min(60, originalLabels.length); // Last 60 points (1 hour)
                        break;
                    case '6h':
                        dataPointsToShow = Math.min(360, originalLabels.length); // Last 360 points (6 hours)
                        break;
                    case '1d':
                        dataPointsToShow = Math.min(1440, originalLabels.length); // Last 1440 points (1 day)
                        break;
                    case '1w':
                        dataPointsToShow = Math.min(10080, originalLabels.length); // Last 10080 points (1 week)
                        break;
                    case '1m':
                        dataPointsToShow = Math.min(43200, originalLabels.length); // Last 43200 points (1 month)
                        break;
                    case 'all':
                    default:
                        dataPointsToShow = originalLabels.length; // Show all
                        break;
                }

                // Get the last N data points
                const startIndex = Math.max(0, originalLabels.length - dataPointsToShow);
                const filteredLabels = originalLabels.slice(startIndex);

                // Filter series data
                const filteredSeriesData = originalData.map(item => ({
                    ...item,
                    data: item.data ? item.data.slice(startIndex) : []
                }));

                // Recalculate y-axis max
                let maxValue = 0;
                filteredSeriesData.forEach(item => {
                    if (item.data && Array.isArray(item.data)) {
                        const itemMax = Math.max(...item.data.filter(v => v !== null && v !== undefined));
                        if (itemMax > maxValue) {
                            maxValue = itemMax;
                        }
                    }
                });
                // Always use 0-100% scale like Polymarket
                const yAxisMax = 100;

                // Update chart with Polymarket style
                chart.setOption({
                    xAxis: {
                        data: filteredLabels
                    },
                    yAxis: {
                        max: yAxisMax
                    },
                    series: filteredSeriesData.map((item) => ({
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
                            show: true,
                            color: {
                                type: 'linear',
                                x: 0,
                                y: 0,
                                x2: 0,
                                y2: 1,
                                colorStops: [{
                                        offset: 0,
                                        color: item.color + '40'
                                    },
                                    {
                                        offset: 1,
                                        color: item.color + '05'
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
                }, true);
            }

            // Initialize chart when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initPolyChart);
            } else {
                initPolyChart();
            }
        </script>
    @endpush

    @push('script')
        <script>
            // Polymarket-style trading calculation (moved to blade file for better control)
            (function() {
                'use strict';
                
                let yesBtn, noBtn, sharesInput, potentialWin;
                let selectedPrice = 0.5; // Default price
                
                // Initialize when DOM is ready
                function initTradingCalculation() {
                    yesBtn = document.getElementById("yesBtn");
                    noBtn = document.getElementById("noBtn");
                    sharesInput = document.getElementById("sharesInput");
                    potentialWin = document.getElementById("potentialWin");

                if (!yesBtn || !noBtn || !sharesInput || !potentialWin) {
                        console.warn('Trading panel elements not found');
                    return;
                }

                    // Get initial price from YES button
                    updateSelectedPrice();

                // YES button click handler
                yesBtn.addEventListener("click", function() {
                        updateSelectedPrice();
                    yesBtn.classList.add("active");
                    noBtn.classList.remove("active");
                        calculatePayout();
                });

                // NO button click handler
                noBtn.addEventListener("click", function() {
                        updateSelectedPrice();
                    noBtn.classList.add("active");
                    yesBtn.classList.remove("active");
                        calculatePayout();
                });

                // Input change handler
                    sharesInput.addEventListener("input", function() {
                        calculatePayout();
                    });

                    // Listen for market selection changes (when populateTradingPanel updates prices)
                    const observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.type === 'attributes' && mutation.attributeName === 'data-price') {
                                console.log('Price attribute changed, recalculating...');
                                setTimeout(function() {
                                    updateSelectedPrice();
                                    calculatePayout();
                                }, 50);
                            }
                        });
                    });

                    // Observe price changes on buttons
                    if (yesBtn) observer.observe(yesBtn, { attributes: true, attributeFilter: ['data-price'] });
                    if (noBtn) observer.observe(noBtn, { attributes: true, attributeFilter: ['data-price'] });
                    
                    // Also listen for custom event from populateTradingPanel
                    document.addEventListener('tradingPriceUpdated', function(event) {
                        console.log('Trading price updated event received', event.detail);
                        setTimeout(function() {
                            updateSelectedPrice();
                            calculatePayout();
                        }, 100);
                    });

                    // Initial calculation
                    calculatePayout();
                }

                // Update selected price based on active button
                function updateSelectedPrice() {
                    if (!yesBtn || !noBtn) return;
                    
                    // Check which button is active
                    const yesActive = yesBtn.classList.contains("active");
                    const noActive = noBtn.classList.contains("active");
                    
                    let rawPrice;
                    if (yesActive) {
                        rawPrice = parseFloat(yesBtn.getAttribute("data-price"));
                    } else if (noActive) {
                        rawPrice = parseFloat(noBtn.getAttribute("data-price"));
                    } else {
                        // Default to YES if neither is active (shouldn't happen, but safety)
                        rawPrice = parseFloat(yesBtn.getAttribute("data-price"));
                    }
                    
                    // Fix: If price is >= 1, it might be in cents format (e.g., 70 for 70¢ = 0.70)
                    // But if it's > 100, it's definitely wrong (e.g., 7000 for 70¢ would be wrong)
                    // Polymarket prices should be between 0.001 and 0.999 (0.1¢ to 99.9¢)
                    if (rawPrice >= 1 && rawPrice <= 100) {
                        // Price is in cents (e.g., 70 = 70¢), convert to decimal (0.70)
                        selectedPrice = rawPrice / 100;
                        console.warn('Price was in cents format, converted:', rawPrice, '→', selectedPrice);
                    } else if (rawPrice > 100) {
                        // Price is way too high, might be in wrong format (e.g., 7000)
                        // Try dividing by 10000 to get cents, then by 100 to get decimal
                        selectedPrice = (rawPrice / 10000) / 100;
                        console.warn('Price was in wrong format, attempting conversion:', rawPrice, '→', selectedPrice);
                    } else {
                        // Price is already in decimal format (0.001 to 0.999)
                        selectedPrice = rawPrice;
                    }
                    
                    // Validate the price is within valid range (0.001 to 0.999)
                    if (isNaN(selectedPrice) || selectedPrice <= 0 || selectedPrice >= 1) {
                        console.warn('Invalid price detected after conversion:', selectedPrice, 'from raw:', rawPrice);
                        // Try one more conversion: if rawPrice is very small (like 0.007), it might be correct
                        if (rawPrice > 0 && rawPrice < 0.01) {
                            selectedPrice = rawPrice; // Very small prices (< 1¢) are likely correct
                            console.log('Using very small price as-is:', selectedPrice);
                        } else {
                            selectedPrice = 0.5; // Fallback
                        }
                    }
                    
                    console.log('Price updated:', {
                        raw: rawPrice,
                        decimal: selectedPrice,
                        cents: (selectedPrice * 100).toFixed(1) + '¢',
                        activeButton: yesActive ? 'YES' : (noActive ? 'NO' : 'NONE'),
                        buttonText: yesActive ? yesBtn?.textContent : (noActive ? noBtn?.textContent : 'N/A')
                    });
                }

                // Calculate payout using Polymarket formula
                function calculatePayout() {
                    if (!sharesInput || !potentialWin) {
                        console.warn('Trading elements not found');
                        return;
                    }

                    // Always update price before calculating (in case it changed)
                    updateSelectedPrice();

                    const amount = parseFloat(sharesInput.value) || 0;

                    // If no amount entered, show $0
                    if (amount <= 0 || isNaN(amount)) {
                        potentialWin.textContent = "$0.00";
                        return;
                    }

                    // Validate price (should be decimal between 0.001 and 0.999)
                    if (!selectedPrice || selectedPrice <= 0 || selectedPrice >= 1 || isNaN(selectedPrice)) {
                        potentialWin.textContent = "$0.00";
                        console.warn('Invalid price in calculation:', selectedPrice, {
                            yesBtn: yesBtn ? yesBtn.getAttribute("data-price") : 'missing',
                            noBtn: noBtn ? noBtn.getAttribute("data-price") : 'missing',
                            yesActive: yesBtn?.classList.contains("active"),
                            noActive: noBtn?.classList.contains("active")
                        });
                        return;
                    }

                    // Polymarket calculation formula (verified):
                    // Example 1: Spend $1 at 0.7¢ per share (0.007 decimal)
                    //   Shares = $1 / 0.007 = 142.857 shares
                    //   Payout = 142.857 × $1.00 = $142.86
                    //
                    // Example 2: Spend $10 at 50¢ per share (0.50 decimal)
                    //   Shares = $10 / 0.50 = 20 shares
                    //   Payout = 20 × $1.00 = $20.00
                    //
                    // Example 3: Spend $1 at 92.5¢ per share (0.925 decimal)
                    //   Shares = $1 / 0.925 = 1.081 shares
                    //   Payout = 1.081 × $1.00 = $1.08
                    
                    // Ensure price is within valid range (0.001 to 0.999)
                    const pricePerShare = Math.max(0.001, Math.min(0.999, selectedPrice));
                    
                    // Calculate shares you receive
                    const shares = amount / pricePerShare;
                    
                    // Calculate total payout (each share pays $1.00 if you win)
                    const totalPayout = shares * 1.0;
                    
                    // Round to 2 decimal places to avoid floating point errors
                    const payout = Math.round(totalPayout * 100) / 100;

                    // Display "To win" - this is what you receive if you win (total payout)
                    potentialWin.textContent = "$" + payout.toFixed(2);
                    
                    // Debug log for verification
                    const rawYesPrice = yesBtn?.getAttribute("data-price");
                    const rawNoPrice = noBtn?.getAttribute("data-price");
                    const buttonText = (yesBtn?.classList.contains("active") ? yesBtn : noBtn)?.textContent || 'N/A';
                    
                    console.log('Trading Calculation:', {
                        amount: '$' + amount.toFixed(2),
                        priceCents: (pricePerShare * 100).toFixed(1) + '¢',
                        priceDecimal: pricePerShare,
                        rawYesPrice: rawYesPrice,
                        rawNoPrice: rawNoPrice,
                        selectedPrice: selectedPrice,
                        buttonText: buttonText,
                        shares: shares.toFixed(6),
                        payout: '$' + payout.toFixed(2),
                        expectedPayoutFor1Dollar: '$' + (1 / pricePerShare).toFixed(2),
                        formula: `$${amount} / ${pricePerShare} = ${shares.toFixed(4)} shares × $1.00 = $${payout.toFixed(2)}`
                    });
                    
                    // Warn if calculation seems wrong (payout should be > amount for prices < 0.50)
                    if (pricePerShare < 0.5 && amount === 1 && payout < 2) {
                        console.error('⚠️ POTENTIAL CALCULATION ERROR:', {
                            message: 'For $1 at ' + (pricePerShare * 100).toFixed(1) + '¢, payout should be > $2',
                            actualPayout: '$' + payout.toFixed(2),
                            priceUsed: pricePerShare,
                            suggestion: 'Price might be in wrong format (e.g., 0.70 instead of 0.007)'
                        });
                    }
                }
                
                // Make calculatePayout globally accessible for external calls
                window.calculatePayout = calculatePayout;

                // Global function for updateShares buttons (+$1, +$20, etc.)
                window.updateShares = function(amount) {
                    if (!sharesInput) return;
                    const currentValue = parseFloat(sharesInput.value) || 0;
                    const newValue = Math.max(0, currentValue + amount);
                    sharesInput.value = newValue;
                    calculatePayout();
                };

                // Initialize when DOM is ready
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initTradingCalculation);
                } else {
                    initTradingCalculation();
                }

                // Also reinitialize after Livewire updates (if using Livewire)
                document.addEventListener('livewire:load', initTradingCalculation);
                document.addEventListener('livewire:update', function() {
                    setTimeout(initTradingCalculation, 100);
                });
            })();
        </script>
    @endpush
@endsection

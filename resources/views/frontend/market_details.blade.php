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
                    <div class="chart-header">
                        <div class="chart-price-info">
                            <span class="chart-price-label">Price</span>
                            <span class="chart-price-value" id="chart-price-value">--</span>
                        </div>
                        <div class="chart-time-info" id="chart-time-value">--</div>
                    </div>
                    <div id="chart-container"
                        style="width: 100%; height: 380px; min-height: 380px; background: transparent;"></div>
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

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"
            integrity="sha512-Ih4vqKQylvR5pDYxJ3H3OXHAMvNjl54hYDo6Ur5cDIrS+Fft+QrbVGnL3e2vBwpu7VQqGQDjCYXyCEhPLrM1EA=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @endpush

    @push('scripts')
        <script>
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

                function buildHistory(period) {
                    const now = new Date();
                    const startTime = eventStartDate ? new Date(eventStartDate) : new Date(now.getTime() - 30 * 24 * 60 * 60 *
                        1000);

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
                        const price = Math.max(1, Math.min(99, p + volatility));

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

                    const last = history[history.length - 1];
                    last.price = parseFloat(basePrice.toFixed(2));
                    last.time = now;
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

                    const x = history.map(d => formatTime(d.time, period));
                    const y = history.map(d => d.price);

                    if (x.length === 0 || y.length === 0) {
                        console.warn('No chart data available, using fallback');
                        // Fallback: create at least one data point
                        const now = new Date();
                        x.push(formatTime(now, period));
                        y.push(basePrice);
                    }

                    const last = history[history.length - 1];
                    if (priceEl) priceEl.textContent = last.price.toFixed(2) + '%';
                    if (timeEl) timeEl.textContent = formatTime(last.time, period);

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
                            formatter: p => {
                                let v = p[0].value;
                                let t = p[0].name;
                                return `
                        <div style="padding:6px;font-size:13px;">
                            <div style="margin-bottom:4px;">${t}</div>
                            <div style="color:${accentColor};font-weight:600;font-size:14px;">${v}%</div>
                        </div>`;
                            }
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
                        series: [{
                            name: 'Price',
                            type: 'line',
                            smooth: true,
                            symbol: 'none',
                            data: y,
                            lineStyle: {
                                color: accentColor,
                                width: 2
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
                                        color: accentColor + '40'
                                    }, {
                                        offset: 1,
                                        color: accentColor + '05'
                                    }]
                                }
                            },
                            emphasis: {
                                focus: 'series',
                                lineStyle: {
                                    width: 3
                                }
                            }
                        }]
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
                        const response = await fetch('{{ route('api.market.price.data', $event->slug) }}');
                        if (!response.ok) return;

                        const d = await response.json();
                        if (d.current_price !== undefined) {
                            basePrice = d.current_price;
                            buildHistory(currentPeriod);
                            updateChart(currentPeriod);
                        }
                    } catch (error) {
                        console.warn('Failed to fetch market data:', error);
                    }
                }

                // Initialize chart
                buildHistory(currentPeriod);
                updateChart(currentPeriod);

                // Fetch updated data
                fetchMarketData();

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
                        if (history[dataIndex]) {
                            if (priceEl) priceEl.textContent = history[dataIndex].price.toFixed(2) + '%';
                            if (timeEl) timeEl.textContent = formatTime(history[dataIndex].time, currentPeriod);
                        }
                    }
                });

                chart.on('mouseout', function() {
                    if (history.length > 0) {
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
        </script>
    @endpush
@endsection

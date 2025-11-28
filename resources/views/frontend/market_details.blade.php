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
                    <div id="chart-container" style="width: 100%; height: 360px;"></div>
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

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('chart-container');
                if (!container) {
                    console.warn('Chart container element not found.');
                    return;
                }
                if (typeof echarts === 'undefined') {
                    console.error('ECharts library failed to load.');
                    return;
                }

                const chart = echarts.init(container, null, {
                    renderer: 'canvas',
                    useDirtyRect: false
                });

                let currentPeriod = 'all';
                const chartBtns = document.querySelectorAll('.chart-btn');

                // Generate sample data based on period
                function generateChartData(period) {
                    const now = new Date();
                    const data = [];
                    let points = 50;
                    let interval = 1;

                    switch (period) {
                        case '1h':
                            points = 60;
                            interval = 1; // 1 minute intervals
                            break;
                        case '6h':
                            points = 72;
                            interval = 5; // 5 minute intervals
                            break;
                        case '1d':
                            points = 96;
                            interval = 15; // 15 minute intervals
                            break;
                        case '1w':
                            points = 168;
                            interval = 60; // 1 hour intervals
                            break;
                        case '1m':
                            points = 30;
                            interval = 1440; // 1 day intervals
                            break;
                        case 'all':
                            points = 100;
                            interval = 1440; // 1 day intervals
                            break;
                    }

                    // Get base price from event data (if available)
                    @php
                        $basePrice = 50;
                        if ($event->markets && $event->markets->count() > 0) {
                            $firstMarket = $event->markets->first();
                            if ($firstMarket->outcome_prices) {
                                $prices = json_decode($firstMarket->outcome_prices, true);
                                if (is_array($prices) && isset($prices[0])) {
                                    $basePrice = $prices[0] * 100;
                                }
                            }
                        }
                    @endphp
                    const basePrice = {{ $basePrice }};

                    for (let i = points; i >= 0; i--) {
                        const time = new Date(now.getTime() - i * interval * 60 * 1000);
                        const price = basePrice + (Math.random() * 20 - 10); // Random variation
                        const volume = Math.random() * 1000000 + 100000;

                        data.push([
                            time.toLocaleTimeString('en-US', {
                                hour: '2-digit',
                                minute: '2-digit',
                                month: period === '1m' || period === 'all' ? 'short' : undefined,
                                day: period === '1m' || period === 'all' ? 'numeric' : undefined
                            }),
                            parseFloat(price.toFixed(2)),
                            parseFloat(volume.toFixed(2))
                        ]);
                    }

                    return data;
                }

                function updateChart(period) {
                    const data = generateChartData(period);
                    const dates = data.map(item => item[0]);
                    const prices = data.map(item => item[1]);
                    const volumes = data.map(item => item[2]);

                    const option = {
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: {
                                type: 'cross'
                            },
                            formatter: function(params) {
                                let result = params[0].name + '<br/>';
                                params.forEach(function(item) {
                                    if (item.seriesName === 'Price') {
                                        result += item.marker + ' ' + item.seriesName + ': $' + item
                                            .value + '<br/>';
                                    } else {
                                        result += item.marker + ' ' + item.seriesName + ': $' +
                                            parseFloat(item.value).toLocaleString() + '<br/>';
                                    }
                                });
                                return result;
                            }
                        },
                        legend: {
                            data: ['Price', 'Volume'],
                            top: 10
                        },
                        grid: {
                            left: '3%',
                            right: '4%',
                            bottom: '3%',
                            top: '15%',
                            containLabel: true
                        },
                        xAxis: {
                            type: 'category',
                            boundaryGap: false,
                            data: dates,
                            axisLine: {
                                lineStyle: {
                                    color: '#666'
                                }
                            }
                        },
                        yAxis: [{
                                type: 'value',
                                name: 'Price (%)',
                                position: 'left',
                                axisLine: {
                                    lineStyle: {
                                        color: '#5470c6'
                                    }
                                },
                                axisLabel: {
                                    formatter: '{value}%'
                                }
                            },
                            {
                                type: 'value',
                                name: 'Volume ($)',
                                position: 'right',
                                axisLine: {
                                    lineStyle: {
                                        color: '#91cc75'
                                    }
                                },
                                axisLabel: {
                                    formatter: function(value) {
                                        if (value >= 1000000) {
                                            return (value / 1000000).toFixed(1) + 'M';
                                        } else if (value >= 1000) {
                                            return (value / 1000).toFixed(1) + 'K';
                                        }
                                        return value;
                                    }
                                }
                            }
                        ],
                        series: [{
                                name: 'Price',
                                type: 'line',
                                smooth: true,
                                data: prices,
                                itemStyle: {
                                    color: '#5470c6'
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
                                                color: 'rgba(84, 112, 198, 0.3)'
                                            },
                                            {
                                                offset: 1,
                                                color: 'rgba(84, 112, 198, 0.1)'
                                            }
                                        ]
                                    }
                                },
                                yAxisIndex: 0
                            },
                            {
                                name: 'Volume',
                                type: 'bar',
                                data: volumes,
                                itemStyle: {
                                    color: 'rgba(145, 204, 117, 0.6)'
                                },
                                yAxisIndex: 1
                            }
                        ]
                    };

                    chart.setOption(option, true);
                }

                // Chart button click handlers
                chartBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        chartBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        currentPeriod = this.getAttribute('data-period');
                        updateChart(currentPeriod);
                    });
                });

                // Initial chart load
                updateChart(currentPeriod);

                // Handle window resize
                window.addEventListener('resize', function() {
                    chart.resize();
                });
            });
        </script>
    @endpush
@endsection

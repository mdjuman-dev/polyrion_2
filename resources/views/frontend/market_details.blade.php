@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>{{ $event->title }} - {{ $appName }}</title>
    <meta name="description" content="{{ Str::limit($event->description ?? $event->title, 160) }}">
    <meta property="og:title" content="{{ $event->title }} - {{ $appName }}">
    <meta property="og:description" content="{{ Str::limit($event->description ?? $event->title, 160) }}">
    <link rel="canonical" href="{{ $appUrl }}/market/details/{{ $event->slug ?? $event->id }}">
@endsection
@section('content')
    <style>
        /* Mobile Responsive Styles for Market Details */
        .chart-container {
            display: block;
            width: 100%;
        }

        .poly-chart-wrapper {
            width: 100%;
            height: 400px;
            background: #111b2b;
            border-radius: 8px;
            margin-bottom: 1rem;
            position: relative;
        }

        /* Medium screens (600px - 768px) - like 629px in image */
        @media (min-width: 600px) and (max-width: 768px) {
            .poly-chart-wrapper {
                height: 320px;
            }

            .chart-container {
                padding: 1rem;
                margin-bottom: 1.5rem;
            }

            .chart-controls {
                gap: 0.5rem;
                flex-wrap: wrap;
                justify-content: center;
            }

            .chart-btn {
                padding: 0.5rem 0.7rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 768px) {
            .poly-chart-wrapper {
                height: 300px;
                margin-bottom: 0.75rem;
            }

            .chart-container {
                padding: 1rem;
                margin-bottom: 1rem;
            }

            .chart-controls {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                -ms-overflow-style: none;
                padding-bottom: 0.5rem;
                gap: 0.4rem;
                flex-wrap: nowrap;
            }

            .chart-controls::-webkit-scrollbar {
                display: none;
            }

            .chart-btn {
                padding: 0.45rem 0.65rem;
                font-size: 0.8rem;
                white-space: nowrap;
                flex-shrink: 0;
            }

            .market-detail-header {
                padding: 0 1rem;
            }

            .market-header-top {
                gap: 0.75rem;
            }

            .market-profile-img {
                width: 36px !important;
                height: 36px !important;
            }

            .market-title {
                font-size: 1.1rem !important;
                line-height: 1.3;
            }

            .market-header-meta {
                font-size: 0.8rem;
                gap: 10px;
            }

            .market-header-actions {
                gap: 6px;
            }

            .main-content {
                padding-bottom: 80px;
            }
        }

        @media (max-width: 480px) {
            .poly-chart-wrapper {
                height: 250px;
                margin-bottom: 0.5rem;
            }

            .chart-container {
                padding: 0.75rem;
            }

            .chart-controls {
                gap: 0.3rem;
            }

            .chart-btn {
                padding: 0.4rem 0.5rem;
                font-size: 0.75rem;
            }

            .market-title {
                font-size: 1rem !important;
            }

            .market-header-meta {
                font-size: 0.75rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .main-content {
                padding: 0.75rem 0.5rem;
                padding-bottom: 80px;
            }
        }

        /* Ensure chart resizes on orientation change */
        @media (orientation: landscape) and (max-width: 768px) {
            .poly-chart-wrapper {
                height: 250px;
            }
        }

        /* Chart legend custom container */
        #chart-legend-custom {
            padding: 0 1rem;
        }

        @media (max-width: 768px) {
            #chart-legend-custom {
                padding: 0 1rem;
            }
        }

        @media (max-width: 480px) {
            #chart-legend-custom {
                padding: 0 0.75rem;
            }
        }

        /* Countdown Timer Styles - Matching Image Design */
        .countdown-container {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0;
            background: transparent;
            border: none;
            margin: 0;
        }

        .countdown-wrapper {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .countdown-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .countdown-number {
            font-size: 18px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            background: transparent;
            padding: 0;
            border-radius: 0;
            min-width: auto;
            text-align: center;
            border: none;
            line-height: 1.2;
        }

        .countdown-label {
            font-size: 9px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1;
        }

        @media (max-width: 768px) {
            .countdown-wrapper {
                gap: 10px;
            }

            .countdown-number {
                font-size: 16px;
            }

            .countdown-label {
                font-size: 8px;
            }
        }

        @media (max-width: 480px) {
            .countdown-wrapper {
                gap: 8px;
            }

            .countdown-number {
                font-size: 14px;
            }

            .countdown-label {
                font-size: 7px;
            }
        }
    </style>
    <main>
        <div class="main-layout">
            <div class="main-content">
                <div class="market-detail-header">
                    <div class="market-header-top">
                        <div class="market-header-left">
                            <div class="market-profile-img" style="width: 65px; height: 65px; border-radius: 10%;">
                                <img src="{{ $event->image }}" alt="Profile">
                            </div>
                            <div class="market-header-info">
                                <h1 class="market-title">{{ $event->title }}</h1>
                                <div class="market-header-meta">
                                    <span class="market-volume">${{ number_format($event->volume) }} Vol.</span>
                                    <span class="market-date">{{ format_date($event->end_date) }}</span>
                                </div>
                            </div>
                        </div>
                        @if ($event->end_date && \Carbon\Carbon::parse($event->end_date)->diffInDays(now()) < 30)
                            <div class="countdown-container"
                                data-end-date="{{ \Carbon\Carbon::parse($event->end_date)->toIso8601String() }}">
                                <div class="countdown-wrapper">
                                    <div class="countdown-item">
                                        <span class="countdown-number" id="countdown-days">00</span>
                                        <span class="countdown-label">DAYS</span>
                                    </div>
                                    <div class="countdown-item">
                                        <span class="countdown-number" id="countdown-hours">00</span>
                                        <span class="countdown-label">HRS</span>
                                    </div>
                                    <div class="countdown-item">
                                        <span class="countdown-number" id="countdown-minutes">00</span>
                                        <span class="countdown-label">MINS</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="market-header-actions">
                            <livewire:save-event :event="$event" />
                        </div>
                    </div>
                </div>


                <div class="chart-container" style="display: inline-block;">
                    <!-- Chart (Polymarket style) -->
                    <div id="polyChart" class="poly-chart-wrapper">
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
        <script>
            // Data from Laravel backend
            const seriesData = @json($seriesData ?? []);
            const labels = @json($labels ?? []);
            console.log(seriesData);

            // Default color palette if color is missing
            const defaultColors = [
                '#ff7b2c', // Orange
                '#4c8df5', // Blue
                '#9cdbff', // Light Blue
                '#ffe04d', // Yellow
                '#ff6b9d', // Pink
                '#4ecdc4', // Teal
                '#a8e6cf', // Green
                '#ff8b94', // Coral
            ];

            // Helper function to ensure valid color
            function ensureValidColor(color, index) {
                if (!color || color.trim() === '' || !color.match(/^#?[0-9A-Fa-f]{6}$/)) {
                    // Use default color based on index
                    return defaultColors[index % defaultColors.length];
                }
                // Ensure # prefix
                return color.startsWith('#') ? color : '#' + color;
            }

            // Helper function to add opacity to hex color
            function addOpacityToHex(hexColor, opacity) {
                // Ensure valid color first
                if (!hexColor || hexColor.trim() === '') {
                    hexColor = defaultColors[0]; // Fallback to first default color
                }

                // Remove # if present
                hexColor = hexColor.replace('#', '');

                // Validate hex color format
                if (!hexColor.match(/^[0-9A-Fa-f]{6}$/)) {
                    console.warn('Invalid hex color format:', hexColor, 'using fallback');
                    hexColor = 'ff7b2c'; // Default orange
                }

                // Convert hex to RGB
                const r = parseInt(hexColor.substring(0, 2), 16);
                const g = parseInt(hexColor.substring(2, 4), 16);
                const b = parseInt(hexColor.substring(4, 6), 16);

                // Validate RGB values
                if (isNaN(r) || isNaN(g) || isNaN(b)) {
                    console.warn('Invalid RGB values, using fallback');
                    return `rgba(255, 123, 44, ${opacity / 255})`; // Default orange
                }

                // Convert opacity from 0-255 to 0-1
                const opacityDecimal = opacity / 255;

                // Return rgba format
                return `rgba(${r}, ${g}, ${b}, ${opacityDecimal})`;
            }

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

                // Use 0-100% scale like in image
                const yAxisMax = 100;

                // Detect mobile device
                const isMobile = window.innerWidth <= 768;
                const isSmallMobile = window.innerWidth <= 480;

                let option = {
                    backgroundColor: "#111b2b",

                    tooltip: {
                        trigger: 'axis',
                        backgroundColor: "#1d2b3a",
                        borderColor: "#1d2b3a",
                        borderWidth: 0,
                        textStyle: {
                            color: "#fff",
                            fontSize: isMobile ? 11 : 12
                        },
                        padding: isMobile ? [6, 10] : [8, 12],
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
                                    `<div style="margin-top: 6px; color: #9ab1c6; font-size: ${isMobile ? '10px' : '11px'};">${params[0].name}</div>`;
                            }
                            return result;
                        }
                    },

                    legend: {
                        show: false // Use custom legend with icons instead
                    },

                    grid: {
                        left: isMobile ? (isSmallMobile ? "10%" : "8%") : "5%",
                        right: isMobile ? (isSmallMobile ? "8%" : "6%") : "5%",
                        bottom: isMobile ? "15%" : "12%",
                        top: isMobile ? "12%" : "10%",
                        containLabel: false
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
                            fontSize: isMobile ? (isSmallMobile ? 9 : 10) : 11,
                            interval: 'auto',
                            rotate: -45, // Always rotate labels like in the image
                            margin: 12
                        }
                    },

                    yAxis: {
                        type: 'value',
                        min: 0,
                        max: 100, // 0-100% scale like in image
                        axisLine: {
                            show: false
                        },
                        axisTick: {
                            show: false
                        },
                        axisLabel: {
                            color: "#9ab1c6",
                            fontSize: isMobile ? (isSmallMobile ? 9 : 10) : 11,
                            formatter: '{value}%',
                            margin: 10 // Add padding for Y-axis labels
                        },
                        splitLine: {
                            show: true,
                            lineStyle: {
                                color: "rgba(154, 177, 198, 0.15)", // Faint grid lines like in image
                                type: 'solid',
                                width: 1
                            }
                        },
                        splitNumber: 5 // Show 5 major grid lines (0, 20, 40, 60, 80, 100)
                    },

                    series: seriesData.map((item, index) => {
                        // Ensure valid color
                        const validColor = ensureValidColor(item.color, index);

                        return {
                            name: item.name,
                            type: 'line',
                            smooth: true,
                            showSymbol: false,
                            data: item.data,
                            lineStyle: {
                                width: 2,
                                color: validColor
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
                                            color: addOpacityToHex(validColor,
                                                64) // 40 in hex = 64 in decimal, ~25% opacity
                                        },
                                        {
                                            offset: 1,
                                            color: addOpacityToHex(validColor,
                                                5) // 05 in hex = 5 in decimal, ~2% opacity
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
                    })
                };

                chart.setOption(option);

                // Handle resize with mobile detection
                function handleResize() {
                    chart.resize();
                    // Update chart options on resize if mobile state changed
                    const wasMobile = isMobile;
                    const nowMobile = window.innerWidth <= 768;
                    if (wasMobile !== nowMobile) {
                        // Reinitialize with new mobile settings
                        initPolyChart();
                    }
                }

                window.addEventListener("resize", handleResize);

                // Create custom legend with icons
                function createCustomLegend(data) {
                    const legendContainer = document.getElementById('chart-legend-custom');
                    if (!legendContainer || !data || data.length <= 1) {
                        if (legendContainer) legendContainer.style.display = 'none';
                        return;
                    }

                    const isMobile = window.innerWidth <= 768;
                    const isSmallMobile = window.innerWidth <= 480;

                    legendContainer.style.display = 'flex';
                    legendContainer.style.flexWrap = 'wrap';
                    legendContainer.style.gap = isMobile ? (isSmallMobile ? '8px' : '12px') : '16px';
                    legendContainer.style.padding = isMobile ? '8px 0' : '12px 0';
                    legendContainer.style.borderBottom = '1px solid rgba(255, 255, 255, 0.1)';
                    legendContainer.style.marginBottom = isMobile ? '12px' : '16px';
                    legendContainer.innerHTML = '';

                    data.forEach((item, index) => {
                        const legendItem = document.createElement('div');
                        legendItem.style.display = 'flex';
                        legendItem.style.alignItems = 'center';
                        legendItem.style.gap = isMobile ? '6px' : '8px';
                        legendItem.style.cursor = 'pointer';
                        legendItem.style.padding = isMobile ? '3px 6px' : '4px 8px';
                        legendItem.style.borderRadius = '4px';
                        legendItem.style.transition = 'background-color 0.2s';

                        const colorBox = document.createElement('div');
                        colorBox.style.width = isMobile ? '10px' : '12px';
                        colorBox.style.height = isMobile ? '10px' : '12px';
                        colorBox.style.borderRadius = '2px';
                        colorBox.style.backgroundColor = item.color;
                        colorBox.style.flexShrink = '0';

                        const label = document.createElement('span');
                        label.style.color = '#ffffff';
                        label.style.fontSize = isMobile ? (isSmallMobile ? '10px' : '11px') : '12px';
                        label.style.fontFamily = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                        // Truncate long names on mobile
                        let displayName = item.name;
                        if (isSmallMobile && displayName.length > 20) {
                            displayName = displayName.substring(0, 17) + '...';
                        } else if (isMobile && displayName.length > 30) {
                            displayName = displayName.substring(0, 27) + '...';
                        }
                        label.textContent = displayName;

                        legendItem.appendChild(colorBox);
                        legendItem.appendChild(label);

                        // Hover effect (only on non-touch devices)
                        if (!('ontouchstart' in window)) {
                            legendItem.addEventListener('mouseenter', () => {
                                legendItem.style.backgroundColor = 'rgba(255, 255, 255, 0.05)';
                            });
                            legendItem.addEventListener('mouseleave', () => {
                                legendItem.style.backgroundColor = 'transparent';
                            });
                        }

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
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Remove active class from all buttons
                        chartButtons.forEach(b => b.classList.remove('active'));
                        // Add active class to clicked button
                        this.classList.add('active');

                        const period = this.getAttribute('data-period');
                        console.log('Chart filter button clicked:', period);

                        if (period) {
                            filterChartByPeriod(period);
                        } else {
                            console.warn('No period attribute found on button');
                        }
                    });
                });
            }

            // Filter chart data by period
            function filterChartByPeriod(period) {
                console.log('filterChartByPeriod called with period:', period);

                if (!window.polyChart) {
                    console.error('Chart instance not found');
                    return;
                }

                if (!window.originalSeriesData || !window.originalLabels) {
                    console.error('Original chart data not found');
                    return;
                }

                const chart = window.polyChart;
                const originalData = window.originalSeriesData;
                const originalLabels = window.originalLabels;

                // Detect mobile for responsive grid
                const isMobile = window.innerWidth <= 768;
                const isSmallMobile = window.innerWidth <= 480;

                // Calculate how many data points to show based on period
                // Since labels are date-based (typically 2 days apart), adjust accordingly
                let dataPointsToShow = originalLabels.length;

                switch (period) {
                    case '1h':
                        // For 1 hour, show last 3-4 points (since labels are typically 2 days apart)
                        // This will show approximately last 6-8 days of data
                        dataPointsToShow = Math.min(4, originalLabels.length);
                        break;
                    case '6h':
                        // For 6 hours, show last 5-6 points
                        dataPointsToShow = Math.min(6, originalLabels.length);
                        break;
                    case '1d':
                        // For 1 day, show last 7-8 points
                        dataPointsToShow = Math.min(8, originalLabels.length);
                        break;
                    case '1w':
                        // For 1 week, show last 10-12 points
                        dataPointsToShow = Math.min(12, originalLabels.length);
                        break;
                    case '1m':
                        // For 1 month, show last 15-20 points
                        dataPointsToShow = Math.min(20, originalLabels.length);
                        break;
                    case 'all':
                    default:
                        dataPointsToShow = originalLabels.length; // Show all
                        break;
                }

                // Ensure minimum 2 points are shown for any filter
                if (dataPointsToShow < 2 && originalLabels.length >= 2) {
                    dataPointsToShow = 2;
                }

                // Get the last N data points
                const startIndex = Math.max(0, originalLabels.length - dataPointsToShow);
                const filteredLabels = originalLabels.slice(startIndex);

                // Filter series data
                const filteredSeriesData = originalData.map(item => ({
                    ...item,
                    data: item.data ? item.data.slice(startIndex) : []
                }));

                // Debug log
                console.log('Filter applied:', {
                    period: period,
                    totalPoints: originalLabels.length,
                    showingPoints: dataPointsToShow,
                    startIndex: startIndex,
                    filteredLabels: filteredLabels,
                    filteredDataLength: filteredSeriesData[0]?.data?.length || 0
                });

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
                // Always use 0-100% scale like in image
                const yAxisMax = 100;

                // Update chart with Polymarket style
                chart.setOption({
                    grid: {
                        left: isMobile ? (isSmallMobile ? "10%" : "8%") : "5%",
                        right: isMobile ? (isSmallMobile ? "8%" : "6%") : "5%",
                        bottom: isMobile ? "15%" : "12%",
                        top: isMobile ? "12%" : "10%",
                        containLabel: false
                    },
                    xAxis: {
                        data: filteredLabels,
                        axisLabel: {
                            fontSize: isMobile ? (isSmallMobile ? 9 : 10) : 11,
                            rotate: -45, // Always rotate labels like in the image
                            margin: 12
                        }
                    },
                    yAxis: {
                        max: 100, // 0-100% scale like in image
                        axisLabel: {
                            fontSize: isMobile ? (isSmallMobile ? 9 : 10) : 11,
                            margin: 10 // Add padding for Y-axis labels
                        },
                        splitLine: {
                            show: true,
                            lineStyle: {
                                color: "rgba(154, 177, 198, 0.15)", // Faint grid lines like in image
                                type: 'solid',
                                width: 1
                            }
                        },
                        splitNumber: 5 // Show 5 major grid lines (0, 20, 40, 60, 80, 100)
                    },
                    series: filteredSeriesData.map((item, index) => {
                        // Ensure valid color
                        const validColor = ensureValidColor(item.color, index);

                        return {
                            name: item.name,
                            type: 'line',
                            smooth: true,
                            showSymbol: false,
                            data: item.data,
                            lineStyle: {
                                width: 2,
                                color: validColor
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
                                            color: addOpacityToHex(validColor,
                                                64) // 40 in hex = 64 in decimal, ~25% opacity
                                        },
                                        {
                                            offset: 1,
                                            color: addOpacityToHex(validColor,
                                                5) // 05 in hex = 5 in decimal, ~2% opacity
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
                    })
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
                                setTimeout(function() {
                                    updateSelectedPrice();
                                    calculatePayout();
                                }, 50);
                            }
                        });
                    });

                    // Observe price changes on buttons
                    if (yesBtn) observer.observe(yesBtn, {
                        attributes: true,
                        attributeFilter: ['data-price']
                    });
                    if (noBtn) observer.observe(noBtn, {
                        attributes: true,
                        attributeFilter: ['data-price']
                    });

                    // Also listen for custom event from populateTradingPanel
                    document.addEventListener('tradingPriceUpdated', function(event) {
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
                        } else {
                            selectedPrice = 0.5; // Fallback
                        }
                    }
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

            // Countdown Timer - Event End Date পর্যন্ত remaining time দেখাবে
            (function() {
                'use strict';

                let countdownInterval = null;

                function initCountdown() {
                    // Clear existing interval if any
                    if (countdownInterval) {
                        clearInterval(countdownInterval);
                        countdownInterval = null;
                    }

                    const countdownContainer = document.querySelector('.countdown-container');
                    if (!countdownContainer) {
                        console.warn('Countdown: Container not found');
                        return;
                    }

                    const endDateStr = countdownContainer.getAttribute('data-end-date');
                    if (!endDateStr) {
                        console.warn('Countdown: Event end date not found');
                        return;
                    }

                    // Parse event end date
                    const endDate = new Date(endDateStr);
                    if (isNaN(endDate.getTime())) {
                        console.error('Countdown: Invalid end date format', endDateStr);
                        return;
                    }

                    // Get countdown elements
                    const daysEl = document.getElementById('countdown-days');
                    const hoursEl = document.getElementById('countdown-hours');
                    const minutesEl = document.getElementById('countdown-minutes');

                    if (!daysEl || !hoursEl || !minutesEl) {
                        console.warn('Countdown: Elements not found', {
                            daysEl,
                            hoursEl,
                            minutesEl
                        });
                        return;
                    }

                    console.log('Countdown initialized for event end:', {
                        endDate: endDate.toISOString(),
                        endDateLocal: endDate.toLocaleString(),
                        now: new Date().toLocaleString(),
                        timeRemaining: Math.floor((endDate.getTime() - new Date().getTime()) / (1000 * 60 * 60 *
                            24)) + ' days'
                    });

                    function updateCountdown() {
                        const now = new Date().getTime();
                        const endTime = endDate.getTime();
                        const distance = endTime - now; // Event end পর্যন্ত remaining time

                        // Event শেষ হয়ে গেছে
                        if (distance < 0) {
                            daysEl.textContent = '00';
                            hoursEl.textContent = '00';
                            minutesEl.textContent = '00';
                            if (countdownInterval) {
                                clearInterval(countdownInterval);
                                countdownInterval = null;
                            }
                            return;
                        }

                        // Calculate remaining time until event end
                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

                        // Update display
                        daysEl.textContent = String(days).padStart(2, '0');
                        hoursEl.textContent = String(hours).padStart(2, '0');
                        minutesEl.textContent = String(minutes).padStart(2, '0');
                    }

                    // Immediately update
                    updateCountdown();

                    // Update every minute
                    countdownInterval = setInterval(updateCountdown, 60000);
                }

                // Initialize countdown when DOM is ready
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initCountdown);
                } else {
                    // Try immediately, but also try after a short delay to ensure DOM is ready
                    setTimeout(initCountdown, 100);
                }

                // Reinitialize after Livewire updates
                document.addEventListener('livewire:load', function() {
                    setTimeout(initCountdown, 200);
                });
                document.addEventListener('livewire:update', function() {
                    setTimeout(initCountdown, 200);
                });

                // Also try on window load as fallback
                window.addEventListener('load', function() {
                    setTimeout(initCountdown, 100);
                });
            })();
        </script>
    @endpush
@endsection

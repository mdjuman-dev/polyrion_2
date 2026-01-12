<div wire:poll.3s="refreshEvent" wire:ignore>
    <div class="chart-container">
        <!-- Highcharts Stock Chart -->
        <div id="marketChartLivewire" class="poly-chart-wrapper" style="height: 500px;"></div>
        
        <div class="chart-controls">
            <button class="chart-btn" data-period="1h">1H</button>
            <button class="chart-btn" data-period="6h">6H</button>
            <button class="chart-btn" data-period="1d">1D</button>
            <button class="chart-btn" data-period="1w">1W</button>
            <button class="chart-btn" data-period="1m">1M</button>
            <button class="chart-btn active" data-period="all">ALL</button>
        </div>
    </div>
</div>

@push('script')
<script>
    (function() {
        let chartInitialized = false;
        
        function waitForHighcharts() {
            if (typeof Highcharts !== 'undefined' && typeof Highcharts.stockChart !== 'undefined') {
                initLivewireChart();
            } else {
                setTimeout(waitForHighcharts, 100);
            }
        }
        
        // Initialize on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', waitForHighcharts);
        } else {
            waitForHighcharts();
        }
        
        // Livewire events
        document.addEventListener('livewire:load', function () {
            waitForHighcharts();
        });
        
        document.addEventListener('livewire:update', function () {
            setTimeout(waitForHighcharts, 200);
        });
        
        function initLivewireChart() {
            if (chartInitialized && window.livewireChartInstance) {
                return; // Already initialized
            }
            
            const seriesData = @json($seriesData ?? []);
            const chartId = 'marketChartLivewire';
            
            console.log('Initializing Livewire chart with data:', seriesData);
            
            if (typeof Highcharts === 'undefined' || typeof Highcharts.stockChart === 'undefined') {
                console.warn('Highcharts not loaded yet');
                setTimeout(initLivewireChart, 100);
                return;
            }
            
            const chartElement = document.getElementById(chartId);
            if (!chartElement) {
                console.warn('Chart element not found:', chartId);
                return;
            }
            
            if (!seriesData || seriesData.length === 0) {
                console.warn('No series data available');
                chartElement.innerHTML = '<p style="color: rgba(255, 255, 255, 0.6); text-align: center; padding: 2rem;">No market data available</p>';
                return;
            }
            
            // Validate data points
            const hasValidData = seriesData.some(item => item.data && item.data.length > 0);
            if (!hasValidData) {
                console.warn('No valid data points in series');
                chartElement.innerHTML = '<p style="color: rgba(255, 255, 255, 0.6); text-align: center; padding: 2rem;">No chart data available</p>';
                return;
            }
        
            // Destroy existing chart
            if (window.livewireChartInstance) {
                try {
                    window.livewireChartInstance.destroy();
                } catch(e) {
                    console.warn('Error destroying chart:', e);
                }
            }
        
        const isMobile = window.innerWidth <= 768;
        const defaultColors = ['#ff7b2c', '#4c8df5', '#9cdbff', '#ffe04d', '#ff6b9d', '#4ecdc4', '#a8e6cf', '#ff8b94'];
        
        function ensureValidColor(color, index) {
            if (!color || color.trim() === '' || !color.match(/^#?[0-9A-Fa-f]{6}$/)) {
                return defaultColors[index % defaultColors.length];
            }
            return color.startsWith('#') ? color : '#' + color;
        }
        
        const chartSeries = seriesData.map((item, index) => {
            const validColor = ensureValidColor(item.color, index);
            const hex = validColor.replace('#', '');
            const r = parseInt(hex.substring(0, 2), 16);
            const g = parseInt(hex.substring(2, 4), 16);
            const b = parseInt(hex.substring(4, 6), 16);
            
            return {
                name: item.name,
                type: 'areaspline',
                color: validColor,
                lineWidth: 2,
                marker: { enabled: false },
                data: item.data || [],
                fillColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, `rgba(${r}, ${g}, ${b}, 0.25)`],
                        [1, `rgba(${r}, ${g}, ${b}, 0.02)`]
                    ]
                },
                fillOpacity: 0.5,
                tooltip: { valueDecimals: 2, valueSuffix: '%' }
            };
        });
        
            try {
            console.log('Creating chart with', chartSeries.length, 'series');
            window.livewireChartInstance = Highcharts.stockChart(chartId, {
                chart: {
                    backgroundColor: '#111b2b',
                    height: isMobile ? 300 : 500,
                    spacing: [10, 10, 10, 10]
                },
                title: { text: null },
                legend: {
                    enabled: true,
                    align: 'left',
                    verticalAlign: 'top',
                    itemStyle: {
                        color: '#ffffff',
                        fontSize: isMobile ? '11px' : '12px',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    },
                    itemHoverStyle: { color: '#9ab1c6' },
                    itemHiddenStyle: { color: 'rgba(255, 255, 255, 0.3)' }
                },
                rangeSelector: { enabled: false },
                navigator: { enabled: false },
                scrollbar: { enabled: false },
                xAxis: {
                    type: 'datetime',
                    labels: {
                        style: {
                            color: '#9ab1c6',
                            fontSize: isMobile ? '10px' : '11px'
                        }
                    },
                    lineColor: 'rgba(154, 177, 198, 0.2)',
                    tickColor: 'rgba(154, 177, 198, 0.2)'
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    title: { text: null },
                    labels: {
                        style: {
                            color: '#9ab1c6',
                            fontSize: isMobile ? '10px' : '11px'
                        },
                        format: '{value}%'
                    },
                    gridLineColor: 'rgba(154, 177, 198, 0.15)',
                    lineColor: 'rgba(154, 177, 198, 0.2)'
                },
                tooltip: {
                    backgroundColor: '#1d2b3a',
                    borderColor: '#1d2b3a',
                    style: {
                        color: '#ffffff',
                        fontSize: isMobile ? '11px' : '12px'
                    },
                    shared: true,
                    split: false,
                    formatter: function() {
                        let tooltip = '<div style="margin-bottom: 4px;">';
                        tooltip += '<span style="color: #9ab1c6; font-size: ' + (isMobile ? '10px' : '11px') + ';">' + 
                                   Highcharts.dateFormat('%b %e, %Y %H:%M', this.x) + '</span></div>';
                        this.points.forEach(point => {
                            const color = point.color || '#ffffff';
                            tooltip += '<div style="margin-bottom: 4px;">';
                            tooltip += '<span style="display: inline-block; width: 8px; height: 8px; background: ' + color + 
                                       '; border-radius: 50%; margin-right: 6px;"></span>';
                            tooltip += '<span style="color: #fff;">' + point.series.name + ': </span>';
                            tooltip += '<span style="color: ' + color + '; font-weight: 600;">' + point.y.toFixed(2) + '%</span>';
                            tooltip += '</div>';
                        });
                        return tooltip;
                    }
                },
                plotOptions: {
                    areaspline: {
                        lineWidth: 2,
                        marker: { enabled: false },
                        states: { hover: { lineWidth: 3 } }
                    }
                },
                series: chartSeries,
                credits: { enabled: false }
            }, function(chart) {
                console.log('Chart created successfully', chart);
                chartInitialized = true;
            });
            
            console.log('Chart instance created:', window.livewireChartInstance);
            
            // Handle period buttons
            document.querySelectorAll('#marketChartLivewire').forEach(container => {
                const buttons = container.parentElement.querySelectorAll('.chart-btn');
                buttons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        buttons.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        const period = this.getAttribute('data-period');
                        filterChartByPeriod(period);
                    });
                });
            });
            
            function filterChartByPeriod(period) {
                if (!window.livewireChartInstance) return;
                const now = new Date().getTime();
                let timeRange = 0;
                switch (period) {
                    case '1h': timeRange = 60 * 60 * 1000; break;
                    case '6h': timeRange = 6 * 60 * 60 * 1000; break;
                    case '1d': timeRange = 24 * 60 * 60 * 1000; break;
                    case '1w': timeRange = 7 * 24 * 60 * 60 * 1000; break;
                    case '1m': timeRange = 30 * 24 * 60 * 60 * 1000; break;
                    case 'all': 
                    default:
                        window.livewireChartInstance.xAxis[0].setExtremes(null, null);
                        return;
                }
                window.livewireChartInstance.xAxis[0].setExtremes(now - timeRange, now);
            }
        } catch (error) {
            console.error('Error creating Livewire chart:', error);
            chartElement.innerHTML = '<p style="color: rgba(255, 255, 255, 0.6); text-align: center; padding: 2rem;">Error loading chart. Please refresh.</p>';
        }
    }
    })();
</script>
@endpush

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
         min-height: 400px;
         height: 400px;
         background: #111b2b;
         border-radius: 8px;
         margin-bottom: 1rem;
         position: relative;
      }
      
      #marketChart {
         width: 100% !important;
         height: 100% !important;
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


            <div class="chart-container">
               <!-- Highcharts Stock Chart -->
               <div id="marketChart" class="poly-chart-wrapper" style="height: 500px;"></div>
               
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

            {{-- Rules/Event Description Section --}}
            @if($event->description)
            <div class="rules-section" style="
               margin-top: 2rem;
               background: transparent;
               border: none;
               padding: 0;
            ">
               <div class="rules-header" style="
                  display: flex;
                  align-items: center;
                  justify-content: space-between;
                  margin-bottom: 1rem;
                  padding-bottom: 0.75rem;
                  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
               ">
                  <h3 style="
                     font-size: 1.125rem;
                     font-weight: 600;
                     color: var(--text-primary);
                     margin: 0;
                  ">Rules</h3>
                  <button class="toggle-rules-btn" onclick="toggleRules()" style="
                     background: rgba(255, 255, 255, 0.05);
                     border: 1px solid rgba(255, 255, 255, 0.15);
                     color: var(--text-secondary);
                     padding: 0.4rem 0.8rem;
                     border-radius: 6px;
                     cursor: pointer;
                     font-size: 0.875rem;
                     transition: all 0.2s;
                     display: flex;
                     align-items: center;
                     gap: 0.5rem;
                  " onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                     <span class="rules-toggle-text">Show less</span>
                     <i class="fas fa-chevron-up" style="font-size: 0.75rem;"></i>
                  </button>
               </div>
               
               <div class="rules-content" id="rulesContent" style="
                  color: rgba(255, 255, 255, 0.8);
                  line-height: 1.8;
                  font-size: 0.95rem;
                  padding-bottom: 1rem;
               ">
                  <div style="white-space: pre-wrap; word-wrap: break-word;">{{ $event->description }}</div>
                  
                  @if($event->created_at)
                  <div style="
                     margin-top: 1.5rem;
                     padding-top: 1rem;
                     border-top: 1px solid rgba(255, 255, 255, 0.1);
                     font-size: 0.875rem;
                     color: rgba(255, 255, 255, 0.6);
                  ">
                     <strong style="color: rgba(255, 255, 255, 0.8);">Created At:</strong> {{ $event->created_at->format('M d, Y, g:i A') }} {{ $event->created_at->timezoneName }}
                  </div>
                  @endif
               </div>
            </div>
            @endif

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
      <script src="https://code.highcharts.com/stock/highstock.js"></script>
      <script src="https://code.highcharts.com/modules/accessibility.js"></script>
   @endpush

   @push('script')
      <script>
         // Data from Laravel backend - already formatted for Highcharts Stock
         const seriesData = @json($seriesData ?? []);
         
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
               return defaultColors[index % defaultColors.length];
            }
            return color.startsWith('#') ? color : '#' + color;
         }

         // Helper function to convert hex to rgba
         function hexToRgba(hex, opacity) {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            if (!result) {
               return `rgba(255, 123, 44, ${opacity})`; // Default orange fallback
            }
            const r = parseInt(result[1], 16);
            const g = parseInt(result[2], 16);
            const b = parseInt(result[3], 16);
            return `rgba(${r}, ${g}, ${b}, ${opacity})`;
         }

         let chartInstance = null;

         // Initialize Highcharts Stock Chart
         function initMarketChart() {
            if (typeof Highcharts === 'undefined' || typeof Highcharts.stockChart === 'undefined') {
               console.warn('Highcharts Stock not loaded yet, retrying...');
               setTimeout(initMarketChart, 100);
               return;
            }

            const chartElement = document.getElementById('marketChart');
            if (!chartElement) {
               console.error('Chart element not found');
               return;
            }

            console.log('Chart initialization started');
            console.log('Series data:', seriesData);
            
            if (!seriesData || seriesData.length === 0) {
               console.warn('No series data available', seriesData);
               chartElement.innerHTML =
                  '<p style="color: rgba(255, 255, 255, 0.6); text-align: center; padding: 2rem;">No market data available</p>';
               return;
            }

            // Validate that we have data points
            const hasValidData = seriesData.some(item => item.data && Array.isArray(item.data) && item.data.length > 0);
            if (!hasValidData) {
               console.warn('No valid data points in series', seriesData);
               chartElement.innerHTML =
                  '<p style="color: rgba(255, 255, 255, 0.6); text-align: center; padding: 2rem;">No chart data available</p>';
               return;
            }

            console.log('Initializing chart with data:', seriesData);
            console.log('Chart container:', chartElement);
            console.log('Container dimensions:', chartElement.offsetWidth, 'x', chartElement.offsetHeight);
            
            // Log data points for each series
            seriesData.forEach((series, idx) => {
               console.log(`Series ${idx} (${series.name}): ${series.data ? series.data.length : 0} data points`);
               if (series.data && series.data.length > 0) {
                  console.log(`  First point: [${series.data[0][0]}, ${series.data[0][1]}]`);
                  console.log(`  Last point: [${series.data[series.data.length - 1][0]}, ${series.data[series.data.length - 1][1]}]`);
               }
            });

            // Detect mobile
            const isMobile = window.innerWidth <= 768;

            // Destroy existing chart if any
            if (chartInstance) {
               chartInstance.destroy();
            }

            // Prepare series for Highcharts Stock
            const chartSeries = seriesData.map((item, index) => {
               const validColor = ensureValidColor(item.color, index);
               const hex = validColor.replace('#', '');
               const r = parseInt(hex.substring(0, 2), 16);
               const g = parseInt(hex.substring(2, 4), 16);
               const b = parseInt(hex.substring(4, 6), 16);

               // Ensure data is in correct format and clean it
               let chartData = item.data || [];
               
               // Clean and validate data: ensure timestamps are integers (milliseconds) and values are numeric
               chartData = chartData.map(point => {
                  if (!Array.isArray(point) || point.length !== 2) {
                     return null;
                  }
                  
                  // Ensure timestamp is integer in milliseconds
                  let timestamp = point[0];
                  if (typeof timestamp === 'string') {
                     timestamp = parseInt(timestamp);
                  } else if (typeof timestamp === 'number') {
                     timestamp = Math.floor(timestamp);
                  } else {
                     return null;
                  }
                  
                  // If timestamp is in seconds (less than year 2000 in milliseconds), convert to milliseconds
                  if (timestamp < 946684800000) { // Jan 1, 2000 in milliseconds
                     timestamp = timestamp * 1000;
                  }
                  
                  // Ensure value is numeric and between 0-100
                  let value = point[1];
                  if (typeof value === 'string') {
                     value = parseFloat(value);
                  }
                  if (isNaN(value) || value < 0 || value > 100) {
                     return null;
                  }
                  value = Math.max(0, Math.min(100, parseFloat(value))); // Clamp to 0-100
                  
                  return [timestamp, parseFloat(value.toFixed(2))];
               }).filter(point => point !== null); // Remove invalid points
               
               // Sort by timestamp to ensure chronological order
               chartData.sort((a, b) => a[0] - b[0]);
               
               // Ensure minimum 2 data points
               if (chartData.length < 2) {
                  console.warn(`Series ${item.name} has less than 2 data points, skipping`);
                  return null;
               }
               
               console.log(`Prepared series ${index}: ${item.name} with ${chartData.length} data points`);
               console.log(`  First: [${new Date(chartData[0][0]).toISOString()}, ${chartData[0][1]}]`);
               console.log(`  Last: [${new Date(chartData[chartData.length - 1][0]).toISOString()}, ${chartData[chartData.length - 1][1]}]`);

               return {
                  name: item.name,
                  type: 'areaspline', // MUST be areaspline for Polymarket style
                  color: validColor,
                  lineWidth: 2, // Thin smooth lines
                  marker: {
                     enabled: false // No point markers
                  },
                  data: chartData, // Format: [[timestamp_ms, value], ...]
                  // Area fill with gradient and low opacity (Polymarket style)
                  fillColor: {
                     linearGradient: {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                     },
                     stops: [
                        [0, `rgba(${r}, ${g}, ${b}, 0.2)`], // Top: 20% opacity
                        [1, `rgba(${r}, ${g}, ${b}, 0.02)`] // Bottom: 2% opacity
                     ]
                  },
                  fillOpacity: 0.3, // Low opacity area fill (Polymarket style)
                  threshold: 0, // Fill from 0
                  tooltip: {
                     valueDecimals: 2,
                     valueSuffix: '%'
                  },
                  enableMouseTracking: true,
                  states: {
                     hover: {
                        lineWidth: 3,
                        lineColor: validColor
                     }
                  },
                  turboThreshold: 0 // Render all points for smooth spline curves
               };
            }).filter(series => series !== null); // Remove null series

            // Validate we have series with data
            if (chartSeries.length === 0) {
               console.error('No valid chart series to display');
               chartElement.innerHTML = '<p style="color: rgba(255, 255, 255, 0.6); text-align: center; padding: 2rem;">No chart data available</p>';
               return;
            }
            
            console.log(`Creating chart with ${chartSeries.length} series`);
            
            // Create Highcharts Stock Chart
            try {
               chartInstance = Highcharts.stockChart('marketChart', {
                  chart: {
                     backgroundColor: '#111b2b',
                     height: isMobile ? 300 : 500,
                     spacing: [10, 10, 10, 10]
                  },

                  title: {
                     text: null
                  },

                  legend: {
                     enabled: true,
                     align: 'left',
                     verticalAlign: 'top',
                     itemStyle: {
                        color: '#ffffff',
                        fontSize: isMobile ? '11px' : '12px',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                     },
                     itemHoverStyle: {
                        color: '#9ab1c6'
                     },
                     itemHiddenStyle: {
                        color: 'rgba(255, 255, 255, 0.3)'
                     }
                  },

                  rangeSelector: {
                     enabled: false // We use custom buttons instead
                  },

                  navigator: {
                     enabled: false
                  },

                  scrollbar: {
                     enabled: false
                  },

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
                     title: {
                        text: null
                     },
                     labels: {
                        style: {
                           color: '#9ab1c6',
                           fontSize: isMobile ? '10px' : '11px'
                        },
                        format: '{value}%'
                     },
                     gridLineColor: 'rgba(154, 177, 198, 0.15)',
                     lineColor: 'rgba(154, 177, 198, 0.2)',
                     plotLines: [{
                        value: 50,
                        color: 'rgba(154, 177, 198, 0.3)',
                        width: 1,
                        dashStyle: 'dash',
                        zIndex: 5,
                        label: {
                           text: '50%',
                           align: 'right',
                           x: -10,
                           style: {
                              color: '#9ab1c6',
                              fontSize: isMobile ? '10px' : '11px'
                           }
                        }
                     }]
                  },

                  tooltip: {
                     backgroundColor: '#1d2b3a',
                     borderColor: '#1d2b3a',
                     borderWidth: 0,
                     style: {
                        color: '#ffffff',
                        fontSize: isMobile ? '11px' : '12px'
                     },
                     shared: true,
                     split: false,
                     formatter: function() {
                        let tooltip = '<div style="margin-bottom: 6px; color: #9ab1c6; font-size: ' + (isMobile ? '10px' : '11px') + ';">' + 
                                   Highcharts.dateFormat('%b %e, %Y %H:%M', this.x) + '</div>';
                        
                        this.points.forEach(point => {
                           const color = point.color || '#ffffff';
                           tooltip += '<div style="margin-bottom: 4px;">';
                           tooltip += '<span style="display: inline-block; width: 8px; height: 8px; background: ' + color + 
                                      '; border-radius: 50%; margin-right: 6px; vertical-align: middle;"></span>';
                           tooltip += '<span style="color: #fff;">' + point.series.name + ': </span>';
                           tooltip += '<span style="color: ' + color + '; font-weight: 600;">' + point.y.toFixed(2) + '%</span>';
                           tooltip += '</div>';
                        });
                        return tooltip;
                     }
                  },

               plotOptions: {
                  areaspline: {
                     // Polymarket-style areaspline configuration
                     lineWidth: 2, // Thin smooth lines
                     marker: {
                        enabled: false // No point markers
                     },
                     states: {
                        hover: {
                           lineWidth: 3,
                           brightness: 0.1
                        }
                     },
                     fillOpacity: 0.3, // Low opacity area fill
                     threshold: 0, // Fill from 0
                     enableMouseTracking: true,
                     turboThreshold: 0 // Render all points for smooth spline curves
                  }
               },

               series: chartSeries,

               credits: {
                  enabled: false
               }
            }, function(chart) {
               // Callback after chart is created
               console.log('Chart callback executed', chart);
               if (chart && chart.series) {
                  console.log('Chart has', chart.series.length, 'series');
                  chart.series.forEach((s, idx) => {
                     console.log(`Chart series ${idx}: ${s.name}, points: ${s.points ? s.points.length : 0}`);
                  });
               }
            });

               console.log('Chart created successfully', chartInstance);
               if (chartSeries.length === 0) {
                  console.error('No series data to display');
               } else {
                  console.log('Chart series count:', chartSeries.length);
                  chartSeries.forEach((series, idx) => {
                     console.log(`Series ${idx}: ${series.name}, data points: ${series.data ? series.data.length : 0}`);
                     if (series.data && series.data.length > 0) {
                        console.log(`  Data range: ${new Date(series.data[0][0]).toISOString()} to ${new Date(series.data[series.data.length - 1][0]).toISOString()}`);
                        console.log(`  Value range: ${Math.min(...series.data.map(d => d[1]))}% to ${Math.max(...series.data.map(d => d[1]))}%`);
                     }
                  });
               }
               
               // Force chart to redraw
               if (chartInstance) {
                  chartInstance.reflow();
               }
            } catch (error) {
               console.error('Error creating chart:', error);
               chartElement.innerHTML = '<p style="color: rgba(255, 255, 255, 0.6); text-align: center; padding: 2rem;">Error loading chart. Please refresh the page.</p>';
            }

            // Store chart instance globally
            window.marketChart = chartInstance;
            window.originalSeriesData = seriesData;
            
            console.log('Chart initialization complete');

            // Handle window resize
            function handleResize() {
               if (chartInstance) {
                  chartInstance.reflow();
               }
            }
            window.addEventListener('resize', handleResize);

            // Handle chart period button clicks
            const chartButtons = document.querySelectorAll('.chart-btn');
            chartButtons.forEach(btn => {
               btn.addEventListener('click', function (e) {
                  e.preventDefault();
                  e.stopPropagation();

                  // Remove active class from all buttons
                  chartButtons.forEach(b => b.classList.remove('active'));
                  // Add active class to clicked button
                  this.classList.add('active');

                  const period = this.getAttribute('data-period');
                  if (period) {
                     filterChartByPeriod(period);
                  }
               });
            });
         }

         // Filter chart data by period using Highcharts xAxis.setExtremes
         function filterChartByPeriod(period) {
            if (!chartInstance || !window.originalSeriesData) {
               return;
            }

            const now = new Date().getTime();
            let timeRange = 0;

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
               case 'all':
               default:
                  // Show all data - reset extremes
                  chartInstance.xAxis[0].setExtremes(null, null);
                  return;
            }

            const cutoffTime = now - timeRange;
            chartInstance.xAxis[0].setExtremes(cutoffTime, now);
         }

         // Initialize chart when DOM is ready and Highcharts is loaded
         function waitForHighcharts() {
            if (typeof Highcharts !== 'undefined' && typeof Highcharts.stockChart !== 'undefined') {
               if (document.readyState === 'loading') {
                  document.addEventListener('DOMContentLoaded', initMarketChart);
               } else {
                  // Small delay to ensure DOM is fully ready
                  setTimeout(initMarketChart, 100);
               }
            } else {
               setTimeout(waitForHighcharts, 100);
            }
         }
         
         waitForHighcharts();
      </script>
   @endpush
   @push('script')
      <script>
         // Polymarket-style trading calculation (moved to blade file for better control)
         (function () {
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
               yesBtn.addEventListener("click", function () {
                  updateSelectedPrice();
                  yesBtn.classList.add("active");
                  noBtn.classList.remove("active");
                  calculatePayout();
               });

               // NO button click handler
               noBtn.addEventListener("click", function () {
                  updateSelectedPrice();
                  noBtn.classList.add("active");
                  yesBtn.classList.remove("active");
                  calculatePayout();
               });

               // Input change handler
               sharesInput.addEventListener("input", function () {
                  calculatePayout();
               });

               // Listen for market selection changes (when populateTradingPanel updates prices)
               const observer = new MutationObserver(function (mutations) {
                  mutations.forEach(function (mutation) {
                     if (mutation.type === 'attributes' && mutation.attributeName === 'data-price') {
                        setTimeout(function () {
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
               document.addEventListener('tradingPriceUpdated', function (event) {
                  setTimeout(function () {
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
            window.updateShares = function (amount) {
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
            document.addEventListener('livewire:update', function () {
               setTimeout(initTradingCalculation, 100);
            });
         })();

         // Countdown Timer - Event End Date পর্যন্ত remaining time দেখাবে
         (function () {
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
            document.addEventListener('livewire:load', function () {
               setTimeout(initCountdown, 200);
            });
            document.addEventListener('livewire:update', function () {
               setTimeout(initCountdown, 200);
            });

            // Also try on window load as fallback
            window.addEventListener('load', function () {
               setTimeout(initCountdown, 100);
            });
         })();
      </script>
   @endpush

   @push('script')
      <script>
         // Rules section toggle
         function toggleRules() {
            const content = document.getElementById('rulesContent');
            const btn = document.querySelector('.toggle-rules-btn');
            const text = btn.querySelector('.rules-toggle-text');
            const icon = btn.querySelector('i');
            
            if (content.style.display === 'none') {
               content.style.display = 'block';
               text.textContent = 'Show less';
               icon.classList.remove('fa-chevron-down');
               icon.classList.add('fa-chevron-up');
            } else {
               content.style.display = 'none';
               text.textContent = 'Show more';
               icon.classList.remove('fa-chevron-up');
               icon.classList.add('fa-chevron-down');
            }
         }
      </script>
   @endpush
@endsection
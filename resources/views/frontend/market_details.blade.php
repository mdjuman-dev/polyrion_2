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

      /* Chart filter button */
      .chart-filter-btn {
         padding: 0.45rem 0.65rem;
         background: rgba(255, 255, 255, 0.05);
         border: 1px solid rgba(255, 255, 255, 0.15);
         border-radius: 6px;
         color: rgba(255, 255, 255, 0.9);
         cursor: pointer;
         transition: all 0.3s ease;
         display: flex;
         align-items: center;
         justify-content: center;
         margin-left: auto;
      }

      .chart-filter-btn:hover {
         background: rgba(76, 141, 245, 0.1);
         border-color: #4c8df5;
         transform: translateY(-1px);
      }

      .chart-filter-btn svg {
         width: 18px;
         height: 18px;
      }

      /* Chart market selection modal */
      .chart-modal-overlay {
         position: fixed;
         top: 0;
         left: 0;
         right: 0;
         bottom: 0;
         background: rgba(0, 0, 0, 0.75);
         display: none;
         align-items: center;
         justify-content: center;
         z-index: 9999;
         padding: 1rem;
      }

      .chart-modal-overlay.active {
         display: flex;
      }

      .chart-modal {
         background: #1a2332;
         border-radius: 12px;
         max-width: 450px;
         width: 100%;
         max-height: 80vh;
         overflow: hidden;
         display: flex;
         flex-direction: column;
         border: 1px solid rgba(255, 255, 255, 0.1);
         box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
      }

      .chart-modal-header {
         padding: 1.25rem 1.5rem;
         border-bottom: 1px solid rgba(255, 255, 255, 0.1);
         display: flex;
         align-items: center;
         justify-content: space-between;
      }

      .chart-modal-title {
         font-size: 1.125rem;
         font-weight: 600;
         color: rgba(255, 255, 255, 0.95);
         margin: 0;
      }

      .chart-modal-subtitle {
         font-size: 0.875rem;
         color: rgba(255, 255, 255, 0.5);
         margin-top: 0.25rem;
      }

      .chart-modal-close {
         background: transparent;
         border: none;
         color: rgba(255, 255, 255, 0.6);
         cursor: pointer;
         padding: 0.5rem;
         display: flex;
         align-items: center;
         justify-content: center;
         border-radius: 6px;
         transition: all 0.2s;
      }

      .chart-modal-close:hover {
         background: rgba(255, 255, 255, 0.1);
         color: rgba(255, 255, 255, 0.9);
      }

      .chart-modal-body {
         padding: 1rem;
         overflow-y: auto;
         flex: 1;
      }

      .chart-market-item {
         display: flex;
         align-items: center;
         gap: 0.75rem;
         padding: 0.875rem;
         background: rgba(255, 255, 255, 0.03);
         border: 1px solid rgba(255, 255, 255, 0.08);
         border-radius: 8px;
         margin-bottom: 0.625rem;
         cursor: pointer;
         transition: all 0.2s;
      }

      .chart-market-item:hover {
         background: rgba(255, 255, 255, 0.06);
         border-color: rgba(255, 255, 255, 0.15);
      }

      .chart-market-item.disabled {
         opacity: 0.5;
         cursor: not-allowed;
      }

      .chart-market-checkbox {
         width: 18px;
         height: 18px;
         border-radius: 4px;
         border: 2px solid rgba(255, 255, 255, 0.3);
         background: transparent;
         display: flex;
         align-items: center;
         justify-content: center;
         transition: all 0.2s;
         flex-shrink: 0;
      }

      .chart-market-item input[type="checkbox"] {
         display: none;
      }

      .chart-market-item input[type="checkbox"]:checked + .chart-market-checkbox {
         background: #4c8df5;
         border-color: #4c8df5;
      }

      .chart-market-checkbox svg {
         width: 12px;
         height: 12px;
         color: white;
         display: none;
      }

      .chart-market-item input[type="checkbox"]:checked + .chart-market-checkbox svg {
         display: block;
      }

      .chart-market-color {
         width: 4px;
         height: 40px;
         border-radius: 2px;
         flex-shrink: 0;
      }

      .chart-market-info {
         flex: 1;
         min-width: 0;
      }

      .chart-market-name {
         font-size: 0.9rem;
         font-weight: 500;
         color: rgba(255, 255, 255, 0.95);
         margin-bottom: 0.25rem;
         line-height: 1.4;
         display: -webkit-box;
         -webkit-line-clamp: 2;
         -webkit-box-orient: vertical;
         overflow: hidden;
         text-overflow: ellipsis;
         word-break: break-word;
      }

      .chart-market-price {
         font-size: 0.8rem;
         color: rgba(255, 255, 255, 0.6);
      }

      @media (max-width: 480px) {
         .chart-modal {
            max-width: 95%;
            max-height: 85vh;
         }

         .chart-modal-header {
            padding: 1rem;
         }

         .chart-modal-title {
            font-size: 1rem;
         }

         .chart-market-item {
            padding: 0.75rem;
         }

         .chart-market-name {
            font-size: 0.85rem;
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
                        <img src="{{ $event->image ? (str_starts_with($event->image, 'http') ? $event->image : asset('storage/' . $event->image)) : asset('frontend/assets/images/default-market.png') }}" 
                             alt="Profile"
                             onerror="this.src='{{ asset('frontend/assets/images/default-market.png') }}'">
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
               <!-- Chart (Chart.js) -->
               <div class="poly-chart-wrapper">
                  <canvas id="marketChart"></canvas>
               </div>
               
               <div class="chart-controls">
                  <button class="chart-btn" data-period="1h">1H</button>
                  <button class="chart-btn" data-period="6h">6H</button>
                  <button class="chart-btn" data-period="1d">1D</button>
                  <button class="chart-btn" data-period="1w">1W</button>
                  <button class="chart-btn" data-period="1m">1M</button>
                  <button class="chart-btn active" data-period="all">ALL</button>
                  <button class="chart-filter-btn" id="chartFilterBtn" title="Select markets to display">
                     <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="4" y1="21" x2="4" y2="14"></line>
                        <line x1="4" y1="10" x2="4" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12" y2="3"></line>
                        <line x1="20" y1="21" x2="20" y2="16"></line>
                        <line x1="20" y1="12" x2="20" y2="3"></line>
                        <line x1="1" y1="14" x2="7" y2="14"></line>
                        <line x1="9" y1="8" x2="15" y2="8"></line>
                        <line x1="17" y1="16" x2="23" y2="16"></line>
                     </svg>
                  </button>
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

   <!-- Chart Market Selection Modal -->
   <div class="chart-modal-overlay" id="chartModalOverlay">
      <div class="chart-modal">
         <div class="chart-modal-header">
            <div>
               <h3 class="chart-modal-title">Show on chart</h3>
               <p class="chart-modal-subtitle">Select a maximum of 4</p>
            </div>
            <button class="chart-modal-close" id="chartModalClose">
               <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
               </svg>
            </button>
         </div>
         <div class="chart-modal-body" id="chartModalBody">
            <!-- Markets will be populated here dynamically -->
         </div>
      </div>
   </div>

   @push('script')
      <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"
         crossorigin="anonymous"
         onload="console.log('📦 Chart.js CDN loaded');"
         onerror="console.error('❌ Failed to load Chart.js CDN');"></script>
      
      <script>
         // ========================================
         // Chart.js Data & Configuration
         // ========================================
         
         // Data from Laravel backend
         const seriesData = @json($seriesData ?? []);
         const labels = @json($labels ?? []);
         
         // Store original data globally
         window.originalSeriesData = seriesData;
         window.originalLabels = labels;
         window.currentSeriesData = seriesData; // Currently displayed series (updated on modal selection)
         window.currentPeriod = 'all'; // Track current active time period
         window.marketChart = null;
         window.selectedMarketIds = []; // Track selected markets for chart
         window.allAvailableMarkets = []; // Store all markets

         // Default color palette
         const defaultColors = [
            '#ff7b2c', // Orange
            '#4c8df5', // Blue
            '#10b981', // Green
            '#f59e0b', // Yellow
            '#8b5cf6', // Purple
            '#ec4899', // Pink
            '#14b8a6', // Teal
            '#f97316', // Red-Orange
         ];

         // ========================================
         // Chart Initialization
         // ========================================
         
         function initMarketChart() {
            console.log('🎨 Initializing Market Chart...');
            
            const ctx = document.getElementById('marketChart');
            if (!ctx) {
               console.error('❌ Chart canvas element not found with ID "marketChart"');
               return;
            }
            console.log('✅ Chart canvas found:', ctx);

            if (!seriesData || seriesData.length === 0) {
               console.warn('⚠️ No series data available for chart');
               ctx.parentElement.innerHTML = '<p style="color: var(--text-secondary); text-align: center; padding: 2rem;">No market data available</p>';
               return;
            }
            console.log('✅ Series data available:', seriesData.length, 'series');

            // Destroy existing chart instance if it exists
            if (window.marketChart) {
               console.log('🔄 Destroying existing chart instance');
               window.marketChart.destroy();
               window.marketChart = null;
            }

            // Prepare datasets for Chart.js - Show only first 4 markets initially
            const maxInitialMarkets = 4;
            const datasetsToShow = seriesData.slice(0, maxInitialMarkets);
            const datasets = datasetsToShow.map((series, index) => {
               const color = series.color || defaultColors[index % defaultColors.length];
               // Use groupItem_title if available
               const displayLabel = series.groupItem_title || series.name || `Outcome ${index + 1}`;
               const fullName = series.groupItem_title || series.full_name || series.question || series.name;
               
               return {
                  label: displayLabel,
                  fullName: fullName, // For tooltip
                  data: series.data,
                  borderColor: color,
                  backgroundColor: color + '20',
                  borderWidth: 2,
                  fill: false,
                  tension: 0.4,
                  pointRadius: 0,
                  pointHoverRadius: 6,
                  pointHoverBackgroundColor: color,
                  pointHoverBorderColor: '#fff',
                  pointHoverBorderWidth: 2,
               };
            });

            console.log(`📊 Initial chart: Showing ${datasets.length} of ${seriesData.length} markets`);

            // Create chart
            window.marketChart = new Chart(ctx, {
               type: 'line',
               data: {
                  labels: labels,
                  datasets: datasets
               },
               options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                     legend: {
                        display: true,
                        position: 'top',
                        labels: {
                           color: '#9ab1c6',
                           font: { size: 12 },
                           padding: 15,
                           usePointStyle: true,
                           pointStyle: 'circle'
                        }
                     },
                     tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1d2b3a',
                        titleColor: '#fff',
                        bodyColor: '#9ab1c6',
                        borderColor: '#374151',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                           label: function(context) {
                              return context.dataset.label + ': ' + context.parsed.y.toFixed(1) + '%';
                           },
                           title: function(tooltipItems) {
                              // For tooltip, show more detailed date/time
                              const label = tooltipItems[0].label;
                              
                              // If it's just a month name, show it as is
                              if (label && label.length <= 3) {
                                 return label; // "Jan", "Feb", etc
                              }
                              
                              // Otherwise show the date
                              return label;
                           }
                        }
                     }
                  },
                  scales: {
                     x: {
                        grid: {
                           color: '#374151',
                           drawBorder: false
                        },
                        ticks: {
                           color: '#9ab1c6',
                           font: { size: 11 },
                           maxRotation: 45,
                           minRotation: 0,
                           autoSkip: true,
                           maxTicksLimit: 12,
                           callback: function(value, index, ticks) {
                              const label = this.getLabelForValue(value);
                              return label;
                           }
                        }
                     },
                     y: {
                        min: 0,
                        max: 100,
                        grid: {
                           color: '#374151',
                           drawBorder: false
                        },
                        position: 'right',
                        ticks: {
                           color: '#9ab1c6',
                           font: { size: 11 },
                           callback: function(value) {
                              return value + '%';
                           }
                        }
                     }
                  },
                  interaction: {
                     mode: 'nearest',
                     axis: 'x',
                     intersect: false
                  }
               }
            });

            console.log('🎉 Market Chart created successfully!', window.marketChart);

            // Handle period button clicks
            const chartButtons = document.querySelectorAll('.chart-btn');
            chartButtons.forEach(btn => {
               btn.addEventListener('click', function(e) {
                  e.preventDefault();
                  e.stopPropagation();

                  // Update active state
                  chartButtons.forEach(b => b.classList.remove('active'));
                  this.classList.add('active');

                  const period = this.getAttribute('data-period');
                  if (period) {
                     filterChartByPeriod(period);
                  }
               });
            });

            console.log('🎉 Market Chart created successfully!', window.marketChart);
            
            // Initialize market selection after chart is ready
            setTimeout(function() {
               if (typeof initializeMarketSelection === 'function') {
                  initializeMarketSelection();
               }
            }, 100);
         }

         // ========================================
         // Chart Loading States
         // ========================================
         
         function showChartLoading() {
            if (window.marketChart && window.marketChart.canvas) {
               window.marketChart.canvas.style.opacity = '0.5';
            }
         }

         function hideChartLoading() {
            if (window.marketChart && window.marketChart.canvas) {
               window.marketChart.canvas.style.opacity = '1';
            }
         }

         // ========================================
         // Chart Period Filtering
         // ========================================
         
         function filterChartByPeriod(period) {
            if (!window.marketChart) {
               console.error('❌ Chart instance not found');
               return;
            }

            console.log('⏱️ Filtering chart by period:', period);
            console.log('📊 Selected markets:', window.selectedMarketIds);

            // Update current period tracker
            window.currentPeriod = period;

            // Show loading
            showChartLoading();

            // Fetch new data from API for this period with selected markets
            const marketIdsParam = window.selectedMarketIds && window.selectedMarketIds.length > 0 
               ? window.selectedMarketIds.join(',')
               : '';
            
            const apiUrl = `/api/market/{{ $event->slug }}/chart-data?period=${period}&market_ids=${marketIdsParam}`;
            console.log('🌐 Fetching data from:', apiUrl);

            fetch(apiUrl)
               .then(response => {
                  console.log('📡 API Response status:', response.status);
                  if (!response.ok) {
                     throw new Error(`HTTP error! status: ${response.status}`);
                  }
                  return response.json();
               })
               .then(response => {
                  console.log('✅ Period data fetched:', response);
                  
                  // Handle both response formats
                  const chartData = response.data || response;
                  
                  console.log('📊 Series count:', chartData.series ? chartData.series.length : 0);
                  console.log('🏷️ Labels count:', chartData.labels ? chartData.labels.length : 0);

                  // Update stored data
                  window.originalLabels = chartData.labels || [];
                  window.currentSeriesData = chartData.series || [];

                  // Update chart labels
                  window.marketChart.data.labels = chartData.labels || [];

                  // Prepare datasets
                  const datasets = chartData.series.map((series, index) => {
                     const color = series.color || defaultColors[index % defaultColors.length];
                     const displayLabel = series.groupItem_title || series.name || `Outcome ${index + 1}`;
                     const fullName = series.groupItem_title || series.full_name || series.question || series.name;
                     
                     // Add direction indicators to point styles
                     const pointBackgroundColors = [];
                     const pointBorderColors = [];
                     const pointRadii = [];
                     
                     if (series.directions && Array.isArray(series.directions)) {
                        series.directions.forEach((direction, i) => {
                           if (direction === 'up') {
                              pointBackgroundColors.push('#10b981');
                              pointBorderColors.push('#10b981');
                              pointRadii.push(3);
                           } else if (direction === 'down') {
                              pointBackgroundColors.push('#ef4444');
                              pointBorderColors.push('#ef4444');
                              pointRadii.push(3);
                           } else {
                              pointBackgroundColors.push(color);
                              pointBorderColors.push(color);
                              pointRadii.push(0);
                           }
                        });
                     }

                     return {
                        label: displayLabel,
                        fullName: fullName,
                        data: series.data,
                        borderColor: color,
                        backgroundColor: color + '20',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        pointRadius: pointRadii.length > 0 ? pointRadii : 0,
                        pointBackgroundColor: pointBackgroundColors.length > 0 ? pointBackgroundColors : color,
                        pointBorderColor: pointBorderColors.length > 0 ? pointBorderColors : color,
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: color,
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2,
                        directions: series.directions
                     };
                  });

                  console.log('🎨 Prepared datasets for period', period, ':', datasets.map(d => ({ label: d.label, dataPoints: d.data.length })));

                  // Update chart
                  window.marketChart.data.datasets = datasets;
                  window.marketChart.update('active');

                  console.log(`✅ Chart filtered to ${period} with ${datasets.length} markets`);

                  // Update summary
                  updateChartSummary(chartData.series);
               })
               .catch(error => {
                  console.error('❌ Error fetching period data:', error);
               })
               .finally(() => {
                  hideChartLoading();
               });
         }

         // ========================================
         // Chart Initialization Handler
         // ========================================
         
         let chartInitialized = false;
         function safeInitChart() {
            if (!chartInitialized) {
               chartInitialized = true;
               initMarketChart();
            }
         }

         // ========================================
         // Rules Section Toggle
         // ========================================
         
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

         // ========================================
         // Countdown Timer Function
         // ========================================
         
         function updateCountdown() {
            const countdownContainer = document.querySelector('.countdown-container');
            if (!countdownContainer) {
               return; // No countdown container found
            }

            const endDateStr = countdownContainer.getAttribute('data-end-date');
            if (!endDateStr) {
               return; // No end date found
            }

            const endDate = new Date(endDateStr);
            const now = new Date();
            const diff = endDate - now;

            if (diff <= 0) {
               // Timer has expired
               document.getElementById('countdown-days').textContent = '00';
               document.getElementById('countdown-hours').textContent = '00';
               document.getElementById('countdown-minutes').textContent = '00';
               return;
            }

            // Calculate time difference
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

            // Update display with zero padding
            document.getElementById('countdown-days').textContent = String(days).padStart(2, '0');
            document.getElementById('countdown-hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('countdown-minutes').textContent = String(minutes).padStart(2, '0');
         }

         // ========================================
         // DOM Ready & Event Listeners
         // ========================================
         
         // Wait for Chart.js to be loaded before initializing
         function waitForChartJS() {
            if (typeof Chart !== 'undefined') {
               console.log('✅ Chart.js loaded successfully');
               safeInitChart();
            } else {
               console.log('⏳ Waiting for Chart.js to load...');
               setTimeout(waitForChartJS, 50);
            }
         }
         
         // Initialize chart when DOM is ready
         if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
               console.log('DOM Content Loaded');
               waitForChartJS();
               updateCountdown();
               setInterval(updateCountdown, 60000);
            });
         } else {
            console.log('DOM Already Loaded');
            waitForChartJS();
            updateCountdown();
            setInterval(updateCountdown, 60000);
         }

         // Also update when Livewire updates the page
         document.addEventListener('livewire:load', function() {
            updateCountdown();
            setInterval(updateCountdown, 60000);
         });

         // ========================================
         // Chart Market Selection Modal
         // ========================================
         
         function initializeMarketSelection() {
            // Store all available markets from original data
            window.allAvailableMarkets = window.originalSeriesData.map((series, index) => ({
               id: series.market_id || series.id || index,
               market_id: series.market_id || series.id || index,
               name: series.name, // Name with price for display
               full_name: series.full_name || series.groupItem_title || series.question || series.name, // Full title
               groupItem_title: series.groupItem_title || null, // Group item title
               question: series.question || series.name, // Market question
               price_text: series.price_text || '', // Price percentage
               color: series.color,
               data: series.data,
               directions: series.directions || [],
               selected: index < 4 // Only first 4 selected initially
            }));

            // Try to load from localStorage first
            let savedMarketIds = null;
            try {
               const saved = localStorage.getItem('selectedMarketIds_{{ $event->slug }}');
               if (saved) {
                  savedMarketIds = JSON.parse(saved);
                  console.log('📦 Loaded from localStorage:', savedMarketIds);
               }
            } catch (e) {
               console.warn('⚠️ Failed to load from localStorage:', e);
            }

            // Initialize selected markets (from localStorage or first 4 by default)
            if (savedMarketIds && Array.isArray(savedMarketIds) && savedMarketIds.length > 0) {
               // Validate that saved IDs exist in available markets
               window.selectedMarketIds = savedMarketIds.filter(id => 
                  window.allAvailableMarkets.some(m => m.id === id || m.market_id === id)
               );
               // If no valid IDs, fallback to first 4
               if (window.selectedMarketIds.length === 0) {
                  window.selectedMarketIds = window.allAvailableMarkets
                     .slice(0, 4)
                     .map(m => m.id);
               }
            } else {
               // Default: first 4 markets
               window.selectedMarketIds = window.allAvailableMarkets
                  .slice(0, 4)
                  .map(m => m.id);
            }

            // Initialize current series data with selected markets
            window.currentSeriesData = window.allAvailableMarkets.filter(m => 
               window.selectedMarketIds.includes(m.id) || window.selectedMarketIds.includes(m.market_id)
            );

            // Populate modal
            populateMarketModal();

            // Setup event listeners
            setupModalEventListeners();
            
            console.log('✅ Market selection initialized');
            console.log('Total markets:', window.allAvailableMarkets.length);
            console.log('Selected markets:', window.selectedMarketIds);
         }

         function populateMarketModal() {
            const modalBody = document.getElementById('chartModalBody');
            if (!modalBody) {
               console.error('❌ Modal body not found');
               return;
            }

            console.log('📝 Populating modal with', window.allAvailableMarkets.length, 'markets');
            console.log('✓ Selected IDs:', window.selectedMarketIds);

            modalBody.innerHTML = '';

            window.allAvailableMarkets.forEach((market, index) => {
               const isSelected = window.selectedMarketIds.includes(market.id) || window.selectedMarketIds.includes(market.market_id);
               const isDisabled = !isSelected && window.selectedMarketIds.length >= 4;

               const marketItem = document.createElement('label');
               marketItem.className = `chart-market-item ${isDisabled ? 'disabled' : ''}`;
               
               // Use groupItem_title if available, otherwise use full name
               const displayName = market.groupItem_title || market.full_name || market.question || market.name;
               const priceText = market.price_text || '';
               
               marketItem.innerHTML = `
                  <input type="checkbox" 
                         data-market-id="${market.id}" 
                         ${isSelected ? 'checked' : ''} 
                         ${isDisabled ? 'disabled' : ''}>
                  <div class="chart-market-checkbox">
                     <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20 6 9 17 4 12"></polyline>
                     </svg>
                  </div>
                  <div class="chart-market-color" style="background-color: ${market.color}"></div>
                  <div class="chart-market-info">
                     <div class="chart-market-name" title="${displayName}">${displayName}</div>
                     <div class="chart-market-price">${priceText}</div>
                  </div>
               `;

               // Add click handler
               const checkbox = marketItem.querySelector('input[type="checkbox"]');
               checkbox.addEventListener('change', function(e) {
                  e.stopPropagation();
                  const marketIdToUse = market.id || market.market_id;
                  console.log('☑️ Checkbox changed:', marketIdToUse, 'checked:', this.checked);
                  handleMarketSelection(marketIdToUse, this.checked);
               });

               modalBody.appendChild(marketItem);
            });
         }

         function handleMarketSelection(marketId, isSelected) {
            console.log('🔘 Market selection changed:', marketId, 'selected:', isSelected);
            
            if (isSelected) {
               // Add to selected if not already there and under limit
               if (!window.selectedMarketIds.includes(marketId) && window.selectedMarketIds.length < 4) {
                  window.selectedMarketIds.push(marketId);
               }
            } else {
               // Remove from selected
               window.selectedMarketIds = window.selectedMarketIds.filter(id => id !== marketId);
            }

            console.log('📊 Selected market IDs:', window.selectedMarketIds);

            // Save to localStorage
            try {
               localStorage.setItem('selectedMarketIds_{{ $event->slug }}', JSON.stringify(window.selectedMarketIds));
               console.log('💾 Saved to localStorage');
            } catch (e) {
               console.warn('⚠️ Failed to save to localStorage:', e);
            }

            // Update modal UI
            populateMarketModal();

            // Update chart with selected markets
            console.log('🔄 Updating chart with selected markets...');
            updateChartWithSelectedMarkets();
         }

         function updateChartWithSelectedMarkets() {
            if (!window.marketChart) {
               console.error('❌ Chart not initialized');
               return;
            }

            if (window.selectedMarketIds.length === 0) {
               console.warn('⚠️ No markets selected');
               return;
            }

            console.log('🔄 Fetching real data for selected markets:', window.selectedMarketIds);

            // Get current active period (default to 'all')
            const activePeriod = window.currentPeriod || 'all';
            const apiUrl = `/api/market/{{ $event->slug }}/chart-data?period=${activePeriod}&market_ids=${window.selectedMarketIds.join(',')}`;
            
            console.log('🌐 API URL:', apiUrl);

            // Show loading state
            showChartLoading();

            // Fetch real data from API for selected markets
            fetch(apiUrl)
               .then(response => {
                  console.log('📡 API Response status:', response.status);
                  if (!response.ok) {
                     throw new Error(`HTTP error! status: ${response.status}`);
                  }
                  return response.json();
               })
               .then(response => {
                  console.log('✅ Real data fetched:', response);
                  
                  // Handle both response formats: {success, data: {labels, series}} or {labels, series}
                  const chartData = response.data || response;
                  
                  console.log('📊 Series count:', chartData.series ? chartData.series.length : 0);
                  console.log('🏷️ Labels count:', chartData.labels ? chartData.labels.length : 0);
                  console.log('📈 Series data:', chartData.series);

                  // Update labels
                  window.originalLabels = chartData.labels || [];
                  window.marketChart.data.labels = chartData.labels || [];

                  // Store series data for filtering
                  window.originalSeriesData = chartData.series || [];
                  window.currentSeriesData = chartData.series || [];

                  // Update allAvailableMarkets with new data
                  window.allAvailableMarkets = window.allAvailableMarkets.map(market => {
                     const updatedMarket = chartData.series.find(s => s.id === market.id || s.market_id === market.id);
                     if (updatedMarket) {
                        return {
                           ...market,
                           data: updatedMarket.data,
                           directions: updatedMarket.directions,
                        };
                     }
                     return market;
                  });

                  // Prepare datasets
                  const datasets = chartData.series.map((series, index) => {
                     const color = series.color || defaultColors[index % defaultColors.length];
                     const displayLabel = series.groupItem_title || series.name || `Outcome ${index + 1}`;
                     const fullName = series.groupItem_title || series.full_name || series.question || series.name;
                     
                     // Add direction indicators to point styles
                     const pointBackgroundColors = [];
                     const pointBorderColors = [];
                     const pointRadii = [];
                     
                     if (series.directions && Array.isArray(series.directions)) {
                        series.directions.forEach((direction, i) => {
                           if (direction === 'up') {
                              pointBackgroundColors.push('#10b981');
                              pointBorderColors.push('#10b981');
                              pointRadii.push(3);
                           } else if (direction === 'down') {
                              pointBackgroundColors.push('#ef4444');
                              pointBorderColors.push('#ef4444');
                              pointRadii.push(3);
                           } else {
                              pointBackgroundColors.push(color);
                              pointBorderColors.push(color);
                              pointRadii.push(0);
                           }
                        });
                     }

                     return {
                        label: displayLabel,
                        fullName: fullName,
                        data: series.data,
                        borderColor: color,
                        backgroundColor: color + '20',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        pointRadius: pointRadii.length > 0 ? pointRadii : 0,
                        pointBackgroundColor: pointBackgroundColors.length > 0 ? pointBackgroundColors : color,
                        pointBorderColor: pointBorderColors.length > 0 ? pointBorderColors : color,
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: color,
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2,
                        directions: series.directions
                     };
                  });

                  console.log('🎨 Prepared datasets:', datasets.map(d => ({ label: d.label, dataPoints: d.data.length })));

                  // Update chart
                  window.marketChart.data.datasets = datasets;
                  window.marketChart.update('active');

                  console.log(`✅ Chart updated with ${datasets.length} selected markets (real data)`);
               })
               .catch(error => {
                  console.error('❌ Error fetching chart data:', error);
               })
               .finally(() => {
                  hideChartLoading();
               });
         }

         function setupModalEventListeners() {
            const filterBtn = document.getElementById('chartFilterBtn');
            const modalOverlay = document.getElementById('chartModalOverlay');
            const modalClose = document.getElementById('chartModalClose');

            console.log('Setting up modal listeners...');
            console.log('Filter button:', filterBtn);
            console.log('Modal overlay:', modalOverlay);

            if (filterBtn) {
               filterBtn.addEventListener('click', function(e) {
                  e.preventDefault();
                  e.stopPropagation();
                  console.log('Filter button clicked!');
                  if (modalOverlay) {
                     modalOverlay.classList.add('active');
                     console.log('Modal opened');
                  }
               });
            } else {
               console.error('❌ Filter button not found!');
            }

            if (modalClose) {
               modalClose.addEventListener('click', function(e) {
                  e.preventDefault();
                  e.stopPropagation();
                  modalOverlay.classList.remove('active');
                  console.log('Modal closed via close button');
               });
            }

            if (modalOverlay) {
               modalOverlay.addEventListener('click', function(e) {
                  if (e.target === modalOverlay) {
                     modalOverlay.classList.remove('active');
                     console.log('Modal closed via overlay click');
                  }
               });
            }

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
               if (e.key === 'Escape' && modalOverlay && modalOverlay.classList.contains('active')) {
                  modalOverlay.classList.remove('active');
                  console.log('Modal closed via Escape key');
               }
            });
         }
      </script>
   @endpush
@endsection
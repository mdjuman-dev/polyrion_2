@extends('backend.layouts.master')
@section('title', 'Event Details')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <!-- Success Message -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Back Button -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back to Events
                            </a>
                            <div>
                                @if ($event->markets->count() == 0)
                                    <a href="{{ route('admin.events.add-markets', $event) }}"
                                        class="btn btn-success btn-lg">
                                        <i class="fa fa-plus-circle"></i> Add Markets to Event
                                    </a>
                                @else
                                    <a href="{{ route('admin.events.add-markets', $event) }}" class="btn btn-primary">
                                        <i class="fa fa-plus-circle"></i> Add More Markets
                                    </a>
                                @endif
                                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-info">
                                    <i class="fa fa-edit"></i> Edit Event
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Main Content Area -->
                    <div class="col-lg-8">
                        <!-- Event Header -->
                        <div class="event-header-modern">
                            <div class="event-header-left">
                                <div class="event-icon-wrapper">
                                    <img src="{{ $event->icon ? (str_starts_with($event->icon, 'http') ? $event->icon : asset('storage/' . $event->icon)) : asset('backend/assets/images/avatar.png') }}"
                                        alt="{{ $event->title }}" class="event-icon"
                                        onerror="this.src='{{ asset('backend/assets/images/avatar.png') }}'">
                                </div>
                                <div class="event-title-wrapper">
                                    <h1 class="event-title-modern">{{ $event->title }}</h1>
                                </div>
                            </div>
                            <div class="event-header-right">
                                <div class="event-actions-modern">
                                    <button class="action-icon-btn" title="Comments">
                                        <i class="fa fa-comment"></i>
                                    </button>
                                    <button class="action-icon-btn" title="Share">
                                        <i class="fa fa-share-alt"></i>
                                    </button>
                                    <button class="action-icon-btn" title="Download">
                                        <i class="fa fa-download"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Markets Overview -->
                        @if ($event->markets->count() > 0)
                            @php
                                $markets = $event->markets;
                                $topMarkets = $markets->take(3);
                            @endphp

                            <!-- Top Markets Legend -->
                            <div class="markets-legend">
                                @foreach ($topMarkets as $index => $market)
                                    @php
                                        $outcomePrices = json_decode($market->outcome_prices, true) ?? ['0.5', '0.5'];
                                        $yesPrice = isset($outcomePrices[1]) ? (float) $outcomePrices[1] : 0.5;
                                        $chance = round($yesPrice * 100);
                                        $colors = ['#4caf50', '#2196f3', '#000000', '#ff9800', '#9c27b0'];
                                        $color = $colors[$index % count($colors)];
                                    @endphp
                                    <div class="legend-item">
                                        <div class="legend-dot" style="background: {{ $color }};"></div>
                                        <span class="legend-name">{{ Str::limit($market->question, 30) }}</span>
                                        <span class="legend-chance">{{ $chance }}%</span>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Historical Chart Section -->
                            <div class="chart-section">
                                <div class="chart-container">
                                    <canvas id="chanceChart" height="200"></canvas>
                                </div>
                                <div class="chart-footer">
                                    <div class="trading-volume">
                                        <i class="fa fa-chart-line"></i>
                                        ${{ number_format($event->volume ?? 0, 0) }} vol
                                    </div>
                                    <div class="time-filters">
                                        <button class="time-filter-btn active" data-period="1D">1D</button>
                                        <button class="time-filter-btn" data-period="1W">1W</button>
                                        <button class="time-filter-btn" data-period="1M">1M</button>
                                        <button class="time-filter-btn" data-period="ALL">ALL</button>
                                        <button class="time-filter-btn" title="More options">
                                            <i class="fa fa-sliders-h"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Markets List -->
                            <div class="markets-section-modern">
                                <h3 class="section-title-modern">
                                    <i class="fa fa-chart-bar"></i> Chance
                                </h3>
                                <div class="markets-list-modern">
                                    @foreach ($markets as $index => $market)
                                        @php
                                            $outcomePrices = json_decode($market->outcome_prices, true) ?? [
                                                '0.5',
                                                '0.5',
                                            ];
                                            $noPrice = isset($outcomePrices[0]) ? (float) $outcomePrices[0] : 0.5;
                                            $yesPrice = isset($outcomePrices[1]) ? (float) $outcomePrices[1] : 0.5;
                                            $chance = round($yesPrice * 100);
                                            $noPriceCents = round($noPrice * 100);
                                            $yesPriceCents = round($yesPrice * 100);
                                            $change = rand(-5, 5);
                                        @endphp
                                        <div class="market-card-modern">
                                            <div class="market-card-header">
                                                <div class="market-image-wrapper">
                                                    <img src="{{ $market->icon ? (str_starts_with($market->icon, 'http') ? $market->icon : asset('storage/' . $market->icon)) : asset('backend/assets/images/avatar.png') }}"
                                                        alt="{{ $market->question }}"
                                                        onerror="this.src='{{ asset('backend/assets/images/avatar.png') }}'">
                                                </div>
                                                <div class="market-info">
                                                    <h4 class="market-question-modern">{{ $market->question }}</h4>
                                                    @if ($market->groupItem_title)
                                                        <p class="market-group-title">{{ $market->groupItem_title }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="market-card-body">
                                                <div class="market-chance">
                                                    <span class="chance-value">{{ $chance }}%</span>
                                                    @if ($change > 0)
                                                        <span class="chance-change positive">▲{{ abs($change) }}</span>
                                                    @elseif ($change < 0)
                                                        <span class="chance-change negative">▼{{ abs($change) }}</span>
                                                    @endif
                                                </div>
                                                <div class="market-actions">
                                                    <button class="btn-yes" data-market-id="{{ $market->id }}">
                                                        Yes {{ $yesPriceCents }}¢
                                                    </button>
                                                    <button class="btn-no" data-market-id="{{ $market->id }}">
                                                        No {{ $noPriceCents }}¢
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($markets->count() > 3)
                                    <div class="more-markets-link">
                                        <a href="#">
                                            <i class="fa fa-chevron-down"></i> More markets
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="no-markets-box">
                                <i class="fa fa-chart-line fa-3x"></i>
                                <h3>No Markets Added Yet</h3>
                                <p>Add markets to enable trading for this event.</p>
                                <a href="{{ route('admin.events.add-markets', $event) }}" class="btn btn-primary">
                                    <i class="fa fa-plus-circle"></i> Add Markets
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Right Sidebar - Trading Widget -->
                    <div class="col-lg-4">
                        @if ($event->markets->count() > 0)
                            @php
                                $selectedMarket = $event->markets->first();
                                $outcomePrices = json_decode($selectedMarket->outcome_prices, true) ?? ['0.5', '0.5'];
                                $noPrice = isset($outcomePrices[0]) ? (float) $outcomePrices[0] : 0.5;
                                $yesPrice = isset($outcomePrices[1]) ? (float) $outcomePrices[1] : 0.5;
                                $noPriceCents = round($noPrice * 100);
                                $yesPriceCents = round($yesPrice * 100);
                            @endphp
                            <div class="trading-widget">
                                <div class="trading-widget-header">
                                    <div class="trading-market-image">
                                        <img src="{{ $selectedMarket->icon ? (str_starts_with($selectedMarket->icon, 'http') ? $selectedMarket->icon : asset('storage/' . $selectedMarket->icon)) : asset('backend/assets/images/avatar.png') }}"
                                            alt="{{ $selectedMarket->question }}"
                                            onerror="this.src='{{ asset('backend/assets/images/avatar.png') }}'">
                                    </div>
                                    <div class="trading-market-info">
                                        <h4 class="trading-event-title">{{ $event->title }}</h4>
                                        <p class="trading-market-selection">
                                            Buy No - {{ Str::limit($selectedMarket->question, 30) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="trading-widget-body">
                                    <div class="trading-tabs">
                                        <button class="trading-tab active" data-tab="buy">Buy</button>
                                        <button class="trading-tab" data-tab="sell">Sell</button>
                                    </div>
                                    <div class="currency-selector">
                                        <select class="form-control">
                                            <option>Dollars</option>
                                        </select>
                                    </div>
                                    <div class="trading-options">
                                        <button class="trading-option-btn" data-option="yes">
                                            Yes {{ $yesPriceCents }}¢
                                        </button>
                                        <button class="trading-option-btn active" data-option="no">
                                            No {{ $noPriceCents }}¢
                                        </button>
                                    </div>
                                    <div class="amount-input-group">
                                        <label>Amount</label>
                                        <div class="amount-input-wrapper">
                                            <span class="currency-symbol">$</span>
                                            <input type="number" class="amount-input" value="0" min="0"
                                                step="0.01">
                                        </div>
                                        <small class="interest-note">
                                            <i class="fa fa-info-circle"></i> Earn 3.5% Interest
                                        </small>
                                    </div>
                                    <button class="btn-trade-primary">
                                        <i class="fa fa-sign-in-alt"></i> Sign up to trade
                                    </button>
                                </div>
                            </div>
                        @endif

                        <!-- Event Info Card -->
                        <div class="info-card-modern">
                            <h4 class="info-card-title">
                                <i class="fa fa-info-circle"></i> Event Information
                            </h4>
                            <div class="info-list-modern">
                                <div class="info-item-modern">
                                    <span class="info-label-modern">Status:</span>
                                    <span class="info-value-modern">
                                        @if ($event->active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                        @if ($event->featured)
                                            <span class="badge badge-warning ml-1">Featured</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="info-item-modern">
                                    <span class="info-label-modern">Markets:</span>
                                    <span class="info-value-modern">{{ $event->markets->count() }}</span>
                                </div>
                                <div class="info-item-modern">
                                    <span class="info-label-modern">Volume:</span>
                                    <span class="info-value-modern">${{ number_format($event->volume ?? 0, 2) }}</span>
                                </div>
                                <div class="info-item-modern">
                                    <span class="info-label-modern">Liquidity:</span>
                                    <span class="info-value-modern">${{ number_format($event->liquidity ?? 0, 2) }}</span>
                                </div>
                                @if ($event->start_date)
                                    <div class="info-item-modern">
                                        <span class="info-label-modern">Start Date:</span>
                                        <span class="info-value-modern">{{ format_date($event->start_date) }}</span>
                                    </div>
                                @endif
                                @if ($event->end_date)
                                    <div class="info-item-modern">
                                        <span class="info-label-modern">End Date:</span>
                                        <span class="info-value-modern">{{ format_date($event->end_date) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Initialize Chart
        @if ($event->markets->count() > 0)
            const ctx = document.getElementById('chanceChart');
            if (ctx) {
                const markets = @json($event->markets->take(3));
                const colors = ['#4caf50', '#2196f3', '#000000'];

                const datasets = markets.map((market, index) => {
                    const outcomePrices = JSON.parse(market.outcome_prices || '["0.5", "0.5"]');
                    const baseChance = parseFloat(outcomePrices[1] || 0.5) * 100;

                    // Generate sample data for last 30 days
                    const data = [];
                    for (let i = 30; i >= 0; i--) {
                        const variation = (Math.random() - 0.5) * 10;
                        data.push(Math.max(0, Math.min(100, baseChance + variation)));
                    }

                    return {
                        label: market.question.substring(0, 20) + '...',
                        data: data,
                        borderColor: colors[index % colors.length],
                        backgroundColor: colors[index % colors.length] + '20',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4
                    };
                });

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: Array.from({
                            length: 31
                        }, (_, i) => {
                            const date = new Date();
                            date.setDate(date.getDate() - (30 - i));
                            return date.toLocaleDateString('en-US', {
                                month: 'short',
                                day: 'numeric'
                            });
                        }),
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 40,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        @endif

        // Time filter buttons
        document.querySelectorAll('.time-filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.time-filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                // Here you would update the chart data based on the selected period
            });
        });

        // Trading tabs
        document.querySelectorAll('.trading-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.trading-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Trading option buttons
        document.querySelectorAll('.trading-option-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.trading-option-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>

    <style>
        /* Event Header Modern */
        .event-header-modern {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding: 20px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .event-header-left {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
        }

        .event-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .event-icon {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .event-title-modern {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
            line-height: 1.3;
        }

        .event-header-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .event-actions-modern {
            display: flex;
            gap: 8px;
        }

        .action-icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            background: #ffffff;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .action-icon-btn:hover {
            background: #f5f5f5;
            border-color: #d0d0d0;
        }

        /* Markets Legend */
        .markets-legend {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .legend-name {
            font-size: 14px;
            color: #333;
            font-weight: 500;
        }

        .legend-chance {
            font-size: 14px;
            font-weight: 700;
            color: #333;
        }

        /* Chart Section */
        .chart-section {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .chart-container {
            height: 250px;
            margin-bottom: 15px;
        }

        .chart-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .trading-volume {
            font-size: 14px;
            color: #666;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .time-filters {
            display: flex;
            gap: 8px;
        }

        .time-filter-btn {
            padding: 6px 12px;
            border: 1px solid #e0e0e0;
            background: #ffffff;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #666;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .time-filter-btn:hover {
            background: #f5f5f5;
        }

        .time-filter-btn.active {
            background: #667eea;
            color: #ffffff;
            border-color: #667eea;
        }

        /* Markets Section Modern */
        .markets-section-modern {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .section-title-modern {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .markets-list-modern {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .market-card-modern {
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 15px;
            transition: all 0.2s ease;
        }

        .market-card-modern:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        }

        .market-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .market-image-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
        }

        .market-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .market-info {
            flex: 1;
        }

        .market-question-modern {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0 0 4px 0;
        }

        .market-group-title {
            font-size: 13px;
            color: #666;
            margin: 0;
        }

        .market-card-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .market-chance {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chance-value {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .chance-change {
            font-size: 13px;
            font-weight: 600;
        }

        .chance-change.positive {
            color: #4caf50;
        }

        .chance-change.negative {
            color: #f44336;
        }

        .market-actions {
            display: flex;
            gap: 10px;
        }

        .btn-yes,
        .btn-no {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-yes {
            background: #e3f2fd;
            color: #1976d2;
        }

        .btn-yes:hover {
            background: #bbdefb;
        }

        .btn-no {
            background: #667eea;
            color: #ffffff;
        }

        .btn-no:hover {
            background: #5568d3;
        }

        .more-markets-link {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }

        .more-markets-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Trading Widget */
        .trading-widget {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .trading-widget-header {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .trading-market-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
        }

        .trading-market-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .trading-event-title {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0 0 4px 0;
        }

        .trading-market-selection {
            font-size: 13px;
            color: #667eea;
            margin: 0;
            font-weight: 500;
        }

        .trading-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
        }

        .trading-tab {
            flex: 1;
            padding: 10px;
            border: 1px solid #e0e0e0;
            background: #ffffff;
            border-radius: 8px;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .trading-tab.active {
            background: #667eea;
            color: #ffffff;
            border-color: #667eea;
        }

        .currency-selector {
            margin-bottom: 15px;
        }

        .trading-options {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .trading-option-btn {
            flex: 1;
            padding: 12px;
            border: 2px solid #e0e0e0;
            background: #ffffff;
            border-radius: 8px;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .trading-option-btn.active {
            background: #667eea;
            color: #ffffff;
            border-color: #667eea;
        }

        .amount-input-group {
            margin-bottom: 20px;
        }

        .amount-input-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            margin-bottom: 8px;
        }

        .amount-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .currency-symbol {
            position: absolute;
            left: 12px;
            font-weight: 600;
            color: #666;
            z-index: 1;
        }

        .amount-input {
            width: 100%;
            padding: 12px 12px 12px 24px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
        }

        .interest-note {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #4caf50;
            font-size: 12px;
            margin-top: 6px;
        }

        .btn-trade-primary {
            width: 100%;
            padding: 14px;
            background: #4caf50;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-trade-primary:hover {
            background: #45a049;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        }

        /* Info Card Modern */
        .info-card-modern {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .info-card-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-list-modern {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .info-item-modern {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-item-modern:last-child {
            border-bottom: none;
        }

        .info-label-modern {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .info-value-modern {
            font-size: 14px;
            color: #1a1a1a;
            font-weight: 600;
        }

        /* No Markets Box */
        .no-markets-box {
            text-align: center;
            padding: 60px 20px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .no-markets-box i {
            color: #ccc;
            margin-bottom: 20px;
        }

        .no-markets-box h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .no-markets-box p {
            color: #666;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .event-header-modern {
                flex-direction: column;
                gap: 15px;
            }

            .markets-legend {
                flex-direction: column;
                gap: 10px;
            }

            .chart-footer {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .market-card-body {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
@endsection

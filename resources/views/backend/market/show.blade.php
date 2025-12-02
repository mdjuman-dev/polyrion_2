@extends('backend.layouts.master')
@section('title', 'Market Details')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <!-- Back Button -->
                <div class="row mb-3">
                    <div class="col-12">
                        <a href="{{ route('admin.events.show', $market->event_id) }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Event
                        </a>
                    </div>
                </div>

                <div class="row">
                    <!-- Market Details Section -->
                    <div class="col-lg-8">
                        <!-- Market Header Card -->
                        <div class="box market-detail-card">
                            <div class="box-body">
                                <!-- Market Image -->
                                @if ($market->image)
                                    <div class="market-detail-image-wrapper">
                                        <img src="{{ str_starts_with($market->image, 'http') ? $market->image : asset('storage/' . $market->image) }}"
                                            alt="{{ $market->question }}"
                                            onerror="this.src='{{ asset('backend/assets/images/avatar.png') }}'">
                                        <div class="market-detail-overlay">
                                            <div class="market-detail-badges">
                                                @if ($market->active)
                                                    <span class="badge badge-success badge-pulse">Active</span>
                                                @endif
                                                @if ($market->featured)
                                                    <span class="badge badge-warning">Featured</span>
                                                @endif
                                                @if ($market->new)
                                                    <span class="badge badge-info">New</span>
                                                @endif
                                                @if ($market->closed)
                                                    <span class="badge badge-danger">Closed</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Market Question -->
                                <h1 class="market-detail-title">
                                    <i class="fa fa-question-circle"></i>
                                    {{ $market->question }}
                                </h1>

                                @if ($market->groupItem_title)
                                    <div class="market-group-title">
                                        <i class="fa fa-tag"></i>
                                        {{ $market->groupItem_title }}
                                    </div>
                                @endif

                                <!-- Market Description -->
                                @if ($market->description)
                                    <div class="market-detail-description">
                                        <p>{{ $market->description }}</p>
                                    </div>
                                @endif

                                <!-- Market Metrics -->
                                <div class="row market-detail-metrics">
                                    <div class="col-md-3 col-sm-6">
                                        <div class="metric-box">
                                            <div class="metric-icon-box">
                                                <i class="fa fa-dollar-sign"></i>
                                            </div>
                                            <div class="metric-content">
                                                <span class="metric-label">Liquidity</span>
                                                <span
                                                    class="metric-value">${{ number_format($market->liquidity_clob ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="metric-box">
                                            <div class="metric-icon-box">
                                                <i class="fa fa-chart-bar"></i>
                                            </div>
                                            <div class="metric-content">
                                                <span class="metric-label">Volume</span>
                                                <span
                                                    class="metric-value">${{ number_format($market->volume ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="metric-box">
                                            <div class="metric-icon-box">
                                                <i class="fa fa-clock"></i>
                                            </div>
                                            <div class="metric-content">
                                                <span class="metric-label">24H Volume</span>
                                                <span
                                                    class="metric-value">${{ number_format($market->volume24hr ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="metric-box">
                                            <div class="metric-icon-box">
                                                <i class="fa fa-calendar-week"></i>
                                            </div>
                                            <div class="metric-content">
                                                <span class="metric-label">1W Volume</span>
                                                <span
                                                    class="metric-value">${{ number_format($market->volume1wk ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Outcome Prices -->
                                @if (!empty($outcomePrices))
                                    <div class="outcome-prices-section">
                                        <h3 class="section-title">
                                            <i class="fa fa-percentage"></i>
                                            Outcome Prices
                                        </h3>
                                        <div class="outcome-prices-grid">
                                            @foreach ($outcomePrices as $index => $price)
                                                <div class="outcome-price-item">
                                                    <div class="outcome-label">
                                                        @if (isset($outcomes[$index]))
                                                            {{ $outcomes[$index] }}
                                                        @else
                                                            Outcome {{ $index + 1 }}
                                                        @endif
                                                    </div>
                                                    <div class="outcome-price-value">
                                                        {{ number_format($price * 100, 2) }}%
                                                    </div>
                                                    <div class="outcome-price-amount">
                                                        ${{ number_format($price, 4) }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Market Dates -->
                                <div class="market-detail-dates">
                                    @if ($market->start_date)
                                        <div class="date-box">
                                            <i class="fa fa-calendar-check"></i>
                                            <div>
                                                <span class="date-label">Start Date</span>
                                                <span class="date-value">{{ format_date($market->start_date) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($market->end_date)
                                        <div class="date-box">
                                            <i class="fa fa-calendar-times"></i>
                                            <div>
                                                <span class="date-label">End Date</span>
                                                <span class="date-value">{{ format_date($market->end_date) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>


                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Event Info Card -->
                        @if ($market->event)
                            <div class="box sidebar-card">
                                <div class="box-body">
                                    <h4 class="sidebar-title">
                                        <i class="fa fa-calendar"></i>
                                        Related Event
                                    </h4>
                                    <div class="event-info-box">
                                        <div class="event-info-image">
                                            <img src="{{ $market->event->image ? (str_starts_with($market->event->image, 'http') ? $market->event->image : asset('storage/' . $market->event->image)) : asset('backend/assets/images/avatar.png') }}"
                                                alt="{{ $market->event->title }}"
                                                onerror="this.src='{{ asset('backend/assets/images/avatar.png') }}'">
                                        </div>
                                        <h5 class="event-info-title">{{ Str::limit($market->event->title, 50) }}</h5>
                                        <a href="{{ route('admin.events.show', $market->event->id) }}"
                                            class="btn btn-sm btn-info btn-block">
                                            <i class="fa fa-eye"></i> View Event
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Market Info Card -->
                        <div class="box sidebar-card">
                            <div class="box-body">
                                <h4 class="sidebar-title">
                                    <i class="fa fa-info-circle"></i>
                                    Market Information
                                </h4>
                                <div class="info-list">
                                    <div class="info-item">
                                        <span class="info-label">Status:</span>
                                        <span class="info-value">
                                            @if ($market->active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Closed:</span>
                                        <span class="info-value">
                                            @if ($market->closed)
                                                <span class="badge badge-danger">Yes</span>
                                            @else
                                                <span class="badge badge-success">No</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Featured:</span>
                                        <span class="info-value">
                                            @if ($market->featured)
                                                <span class="badge badge-warning">Yes</span>
                                            @else
                                                <span class="badge badge-secondary">No</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Restricted:</span>
                                        <span class="info-value">
                                            @if ($market->restricted)
                                                <span class="badge badge-danger">Yes</span>
                                            @else
                                                <span class="badge badge-success">No</span>
                                            @endif
                                        </span>
                                    </div>
                                    @if ($market->resolution_source)
                                        <div class="info-item">
                                            <span class="info-label">Resolution Source:</span>
                                            <span class="info-value">{{ $market->resolution_source }}</span>
                                        </div>
                                    @endif
                                    <div class="info-item">
                                        <span class="info-label">Created:</span>
                                        <span class="info-value">{{ format_date($market->created_at) }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Updated:</span>
                                        <span class="info-value">{{ format_date($market->updated_at) }}</span>
                                    </div>
                                    @if ($market->slug)
                                        <div class="info-item">
                                            <span class="info-label">Slug:</span>
                                            <span class="info-value"><code>{{ $market->slug }}</code></span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Volume Stats Card -->
                        <div class="box sidebar-card">
                            <div class="box-body">
                                <h4 class="sidebar-title">
                                    <i class="fa fa-chart-line"></i>
                                    Volume Statistics
                                </h4>
                                <div class="volume-stats">
                                    <div class="volume-stat-item">
                                        <span class="volume-stat-label">24 Hours</span>
                                        <span
                                            class="volume-stat-value">${{ number_format($market->volume24hr ?? 0, 2) }}</span>
                                    </div>
                                    <div class="volume-stat-item">
                                        <span class="volume-stat-label">1 Week</span>
                                        <span
                                            class="volume-stat-value">${{ number_format($market->volume1wk ?? 0, 2) }}</span>
                                    </div>
                                    <div class="volume-stat-item">
                                        <span class="volume-stat-label">1 Month</span>
                                        <span
                                            class="volume-stat-value">${{ number_format($market->volume1mo ?? 0, 2) }}</span>
                                    </div>
                                    <div class="volume-stat-item">
                                        <span class="volume-stat-label">1 Year</span>
                                        <span
                                            class="volume-stat-value">${{ number_format($market->volume1yr ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @push('styles')
        <style>
            /* Market Detail Card */
            .market-detail-card {
                border-radius: 16px;
                margin-bottom: 30px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            }

            .market-detail-image-wrapper {
                position: relative;
                width: 100%;
                height: 300px;
                overflow: hidden;
                border-radius: 12px;
                margin-bottom: 20px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .market-detail-image-wrapper img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .market-detail-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 100%);
                display: flex;
                align-items: flex-start;
                justify-content: flex-end;
                padding: 20px;
            }

            .market-detail-badges {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .market-detail-title {
                font-size: 32px;
                font-weight: 700;
                color: #2c3e50;
                margin-bottom: 15px;
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .market-detail-title i {
                color: #667eea;
            }

            .market-group-title {
                background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
                padding: 12px 20px;
                border-radius: 10px;
                margin-bottom: 20px;
                color: #1976d2;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 10px;
            }

            .market-group-title i {
                color: #1565c0;
            }

            .market-detail-description {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 10px;
                margin-bottom: 25px;
                color: #495057;
                line-height: 1.8;
            }

            /* Metrics */
            .market-detail-metrics {
                margin-bottom: 25px;
            }

            .metric-box {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border-radius: 12px;
                padding: 20px;
                display: flex;
                align-items: center;
                gap: 15px;
                margin-bottom: 15px;
                transition: all 0.3s ease;
            }

            .metric-box:hover {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
            }

            .metric-box:hover .metric-icon-box,
            .metric-box:hover .metric-label,
            .metric-box:hover .metric-value {
                color: white;
            }

            .metric-icon-box {
                width: 50px;
                height: 50px;
                border-radius: 12px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 20px;
                flex-shrink: 0;
            }

            .metric-content {
                flex: 1;
            }

            .metric-label {
                display: block;
                font-size: 12px;
                color: #6c757d;
                margin-bottom: 5px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-weight: 600;
            }

            .metric-value {
                display: block;
                font-size: 20px;
                font-weight: 700;
                color: #2c3e50;
            }

            /* Outcome Prices */
            .outcome-prices-section {
                margin-bottom: 25px;
                padding: 20px;
                background: #f8f9fa;
                border-radius: 12px;
            }

            .section-title {
                font-size: 20px;
                font-weight: 700;
                color: #2c3e50;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .section-title i {
                color: #667eea;
            }

            .outcome-prices-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
            }

            .outcome-price-item {
                background: white;
                padding: 20px;
                border-radius: 10px;
                text-align: center;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }

            .outcome-price-item:hover {
                transform: translateY(-5px);
                box-shadow: 0 5px 20px rgba(102, 126, 234, 0.2);
            }

            .outcome-label {
                display: block;
                font-size: 14px;
                color: #6c757d;
                margin-bottom: 10px;
                font-weight: 600;
            }

            .outcome-price-value {
                display: block;
                font-size: 28px;
                font-weight: 700;
                color: #667eea;
                margin-bottom: 5px;
            }

            .outcome-price-amount {
                display: block;
                font-size: 12px;
                color: #6c757d;
            }

            /* Dates */
            .market-detail-dates {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 15px;
                margin-bottom: 25px;
            }

            .date-box {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .date-box i {
                font-size: 24px;
                color: #667eea;
            }

            .date-label {
                display: block;
                font-size: 12px;
                color: #6c757d;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .date-value {
                display: block;
                font-size: 16px;
                font-weight: 600;
                color: #2c3e50;
            }

            /* Actions */
            .market-detail-actions {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }

            /* Sidebar */
            .sidebar-card {
                border-radius: 16px;
                margin-bottom: 30px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            }

            .sidebar-title {
                font-size: 18px;
                font-weight: 700;
                color: #2c3e50;
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 2px solid #e9ecef;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .sidebar-title i {
                color: #667eea;
            }

            .event-info-box {
                text-align: center;
            }

            .event-info-image {
                width: 100%;
                height: 150px;
                border-radius: 10px;
                overflow: hidden;
                margin-bottom: 15px;
            }

            .event-info-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .event-info-title {
                font-size: 16px;
                font-weight: 600;
                color: #2c3e50;
                margin-bottom: 15px;
            }

            .info-list {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .info-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px;
                background: #f8f9fa;
                border-radius: 8px;
            }

            .info-label {
                font-weight: 600;
                color: #495057;
            }

            .info-value {
                color: #2c3e50;
            }

            .info-value code {
                background: #e9ecef;
                padding: 3px 8px;
                border-radius: 4px;
                font-size: 12px;
            }

            .volume-stats {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .volume-stat-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px;
                background: #f8f9fa;
                border-radius: 8px;
                transition: all 0.3s ease;
            }

            .volume-stat-item:hover {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }

            .volume-stat-item:hover .volume-stat-label,
            .volume-stat-item:hover .volume-stat-value {
                color: white;
            }

            .volume-stat-label {
                font-weight: 600;
                color: #495057;
            }

            .volume-stat-value {
                font-weight: 700;
                color: #2c3e50;
                font-size: 16px;
            }

            @media (max-width: 768px) {
                .market-detail-title {
                    font-size: 24px;
                }

                .market-detail-metrics {
                    grid-template-columns: 1fr;
                }

                .outcome-prices-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    @endpush
@endsection

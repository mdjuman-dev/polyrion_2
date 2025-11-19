@extends('backend.layouts.master')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <!-- Header Section -->
                        <div class="box list-header-box">
                            <div class="box-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h2 class="list-title">
                                            <i class="fa fa-list"></i> Events List
                                        </h2>
                                        <p class="list-subtitle">Manage all your events and markets</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="{{ route('admin.market.index') }}" class="btn btn-primary">
                                            <i class="fa fa-plus"></i> Add New Event
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Events Grid -->
                        <div class="row events-grid">
                            @forelse ($events as $event)
                                <div class="col-lg-6 col-xl-4 event-card-wrapper">
                                    <div class="box event-card">
                                        <!-- Card Header -->
                                        <div class="event-card-header">
                                            <div class="event-image-wrapper">
                                                <img src="{{ $event->image ? (str_starts_with($event->image, 'http') ? $event->image : asset('storage/' . $event->image)) : asset('backend/assets/images/avatar.png') }}"
                                                    alt="{{ $event->title }}"
                                                    onerror="this.src='{{ asset('backend/assets/images/avatar.png') }}'">
                                                <div class="event-overlay">
                                                    <div class="event-status-badges">
                                                        @if ($event->active)
                                                            <span class="badge badge-success badge-pulse">Active</span>
                                                        @endif
                                                        @if ($event->featured)
                                                            <span class="badge badge-warning">Featured</span>
                                                        @endif
                                                        @if ($event->new)
                                                            <span class="badge badge-info">New</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Card Body -->
                                        <div class="event-card-body">
                                            <h4 class="event-title">
                                                <i class="fa fa-calendar"></i>
                                                {{ Str::limit($event->title, 60) }}
                                            </h4>
                                            <p class="event-description">
                                                {{ Str::limit($event->description ?? 'No description', 100) }}
                                            </p>

                                            <!-- Key Metrics -->
                                            <div class="event-metrics">
                                                <div class="metric-item">
                                                    <div class="metric-icon">
                                                        <i class="fa fa-dollar-sign"></i>
                                                    </div>
                                                    <div class="metric-info">
                                                        <span class="metric-label">Liquidity</span>
                                                        <span class="metric-value">
                                                            ${{ number_format($event->liquidity ?? 0, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="metric-item">
                                                    <div class="metric-icon">
                                                        <i class="fa fa-chart-bar"></i>
                                                    </div>
                                                    <div class="metric-info">
                                                        <span class="metric-label">Volume</span>
                                                        <span class="metric-value">
                                                            ${{ number_format($event->volume ?? 0, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Markets Count -->
                                            <div class="markets-count">
                                                <i class="fa fa-list"></i>
                                                <span>{{ $event->markets->count() }} Markets</span>
                                            </div>

                                            <!-- Dates -->
                                            <div class="event-dates">
                                                @if ($event->start_date)
                                                    <div class="date-item">
                                                        <i class="fa fa-calendar-check"></i>
                                                        <span>{{ format_date($event->start_date) }}</span>
                                                    </div>
                                                @endif
                                                @if ($event->end_date)
                                                    <div class="date-item">
                                                        <i class="fa fa-calendar-times"></i>
                                                        <span>{{ format_date($event->end_date) }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="event-actions">
                                                <a href="{{ route('admin.market.edit', $event->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fa fa-edit"></i> Edit
                                                </a>
                                                <a href="{{ route('admin.market.save', $event->slug) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="box empty-state-box">
                                        <div class="box-body text-center py-5">
                                            <i class="fa fa-inbox fa-4x text-muted mb-3"></i>
                                            <h4>No Events Found</h4>
                                            <p class="text-muted">Start by adding your first event</p>
                                            <a href="{{ route('admin.market.index') }}" class="btn btn-primary">
                                                <i class="fa fa-plus"></i> Add New Event
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <!-- Pagination -->
                        @if ($events->hasPages())
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-center">
                                        {{ $events->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>

    @push('styles')
        <style>
            /* Header */
            .list-header-box {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                margin-bottom: 30px;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

            .list-title {
                font-size: 28px;
                font-weight: 700;
                margin: 0;
                color: white;
            }

            .list-title i {
                margin-right: 10px;
            }

            .list-subtitle {
                margin: 5px 0 0 0;
                opacity: 0.9;
                font-size: 14px;
            }

            /* Events Grid */
            .events-grid {
                margin-top: 20px;
            }

            .event-card-wrapper {
                margin-bottom: 30px;
            }

            .event-card {
                border-radius: 16px;
                overflow: hidden;
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
                height: 100%;
                display: flex;
                flex-direction: column;
                background: #ffffff;
                border: 1px solid rgba(0, 0, 0, 0.05);
            }

            .event-card:hover {
                transform: translateY(-8px) scale(1.02);
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
                border-color: rgba(102, 126, 234, 0.3);
            }

            /* Card Header */
            .event-card-header {
                position: relative;
                overflow: hidden;
            }

            .event-image-wrapper {
                position: relative;
                width: 100%;
                height: 180px;
                overflow: hidden;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .event-image-wrapper img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }

            .event-card:hover .event-image-wrapper img {
                transform: scale(1.15);
            }

            .event-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 100%);
                display: flex;
                align-items: flex-start;
                justify-content: flex-end;
                padding: 15px;
            }

            .event-status-badges {
                display: flex;
                flex-direction: column;
                gap: 8px;
                z-index: 2;
            }

            .badge-pulse {
                animation: pulse 2s infinite;
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.7;
                }
            }

            /* Card Body */
            .event-card-body {
                padding: 20px;
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            .event-title {
                font-size: 18px;
                font-weight: 700;
                color: #2c3e50;
                margin-bottom: 12px;
                line-height: 1.4;
                display: flex;
                align-items: flex-start;
                gap: 10px;
            }

            .event-title i {
                color: #667eea;
                margin-top: 3px;
                font-size: 16px;
            }

            .event-description {
                color: #6c757d;
                font-size: 14px;
                line-height: 1.6;
                margin-bottom: 20px;
                flex: 1;
            }

            /* Metrics */
            .event-metrics {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                margin-bottom: 15px;
            }

            .metric-item {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border-radius: 10px;
                padding: 12px;
                display: flex;
                align-items: center;
                gap: 10px;
                transition: all 0.3s ease;
            }

            .metric-item:hover {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            }

            .metric-item:hover .metric-icon,
            .metric-item:hover .metric-label,
            .metric-item:hover .metric-value {
                color: white;
            }

            .metric-icon {
                width: 35px;
                height: 35px;
                border-radius: 8px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 14px;
                transition: all 0.3s ease;
                flex-shrink: 0;
            }

            .metric-info {
                flex: 1;
                min-width: 0;
            }

            .metric-label {
                display: block;
                font-size: 10px;
                color: #6c757d;
                margin-bottom: 2px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-weight: 600;
                transition: color 0.3s ease;
            }

            .metric-value {
                display: block;
                font-size: 14px;
                font-weight: 700;
                color: #2c3e50;
                transition: color 0.3s ease;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* Markets Count */
            .markets-count {
                background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
                padding: 10px 15px;
                border-radius: 8px;
                margin-bottom: 15px;
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 14px;
                font-weight: 600;
                color: #1976d2;
            }

            .markets-count i {
                font-size: 16px;
            }

            /* Dates */
            .event-dates {
                display: flex;
                flex-direction: column;
                gap: 8px;
                margin-bottom: 15px;
                padding: 12px;
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border-radius: 8px;
            }

            .date-item {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 13px;
                color: #495057;
            }

            .date-item i {
                color: #667eea;
                width: 16px;
            }

            /* Actions */
            .event-actions {
                display: flex;
                gap: 10px;
                margin-top: auto;
            }

            .event-actions .btn {
                flex: 1;
                border-radius: 8px;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .event-actions .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            }

            /* Empty State */
            .empty-state-box {
                border-radius: 12px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            }

            /* Pagination */
            .pagination {
                justify-content: center;
            }

            .page-link {
                border-radius: 8px;
                margin: 0 4px;
                border: 2px solid #e9ecef;
                color: #667eea;
                transition: all 0.3s ease;
            }

            .page-link:hover {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border-color: #667eea;
                transform: translateY(-2px);
            }

            .page-item.active .page-link {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-color: #667eea;
            }

            @media (max-width: 768px) {
                .list-title {
                    font-size: 22px;
                }

                .event-metrics {
                    grid-template-columns: 1fr;
                }

                .event-image-wrapper {
                    height: 150px;
                }

                .event-card-body {
                    padding: 15px;
                }

                .event-card:hover {
                    transform: translateY(-4px) scale(1.01);
                }
            }
        </style>
    @endpush
@endsection

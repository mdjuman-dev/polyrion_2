@extends('backend.layouts.master')
@section('title', 'All Events')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <!-- Action Header -->
                        <div class="box mb-3">
                            <div class="box-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">
                                        <i class="fa fa-calendar"></i> Events Management
                                    </h4>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.events.create-with-markets') }}" class="btn btn-success">
                                            <i class="fa fa-plus-circle"></i> Create Event with Markets
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search and Filter Section -->
                        <div class="box search-filter-box">
                            <div class="box-body">
                                <form method="GET" action="{{ route('admin.events.index') }}" class="search-filter-form">
                                    @if (request('status'))
                                        <input type="hidden" name="status" value="{{ request('status') }}">
                                    @endif
                                    <div class="row align-items-center">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label class="form-label">
                                                    <i class="fa fa-search"></i> Search Events
                                                </label>
                                                <x-backend.search-box name="search"
                                                    placeholder="Search by title, description, category or slug..."
                                                    value="{{ request('search') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">
                                                    <i class="fa fa-filter"></i> Category
                                                </label>
                                                <x-backend.filter-dropdown name="category" label="" :options="array_combine(
                                                    $categories,
                                                    array_map('ucfirst', $categories),
                                                )"
                                                    allText="All Categories" :currentValue="request('category')" />
                                            </div>
                                        </div>
                                        <div class="col-md-4 mt-2">
                                            <div class="form-group mb-0">
                                                <x-backend.form-button type="submit" variant="primary" size="sm"
                                                    icon="search">
                                                    Search
                                                </x-backend.form-button>
                                                @if (request('search') || (request('category') && request('category') != 'all') || request('status'))
                                                    <a href="{{ route('admin.events.index') }}"
                                                        class="btn btn-secondary btn-sm btn-reset">
                                                        <i class="fa fa-refresh"></i> Reset
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Filter Buttons -->
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="form-group mb-0">
                                                <label class="form-label"
                                                    style="font-weight: 600; color: #374151; margin-bottom: 12px;">
                                                    <i class="fa fa-filter me-2" style="color: #667eea;"></i> Filter by
                                                    Status
                                                </label>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <a href="{{ route('admin.events.index', array_merge(request()->except('status'), ['status' => ''])) }}"
                                                        class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}"
                                                        style="border-radius: 10px; padding: 10px 20px; font-weight: 600; transition: all 0.3s; border: 2px solid;"
                                                        onmouseover="this.style.transform='translateY(-2px)'"
                                                        onmouseout="this.style.transform='translateY(0)'">
                                                        <i class="fa fa-list me-1"></i> All
                                                    </a>
                                                    <a href="{{ route('admin.events.index', array_merge(request()->except('status'), ['status' => 'active'])) }}"
                                                        class="btn {{ request('status') === 'active' ? 'btn-success' : 'btn-outline-success' }}"
                                                        style="border-radius: 10px; padding: 10px 20px; font-weight: 600; transition: all 0.3s; border: 2px solid;"
                                                        onmouseover="this.style.transform='translateY(-2px)'"
                                                        onmouseout="this.style.transform='translateY(0)'">
                                                        <i class="fa fa-check-circle me-1"></i> Active
                                                    </a>
                                                    <a href="{{ route('admin.events.index', array_merge(request()->except('status'), ['status' => 'inactive'])) }}"
                                                        class="btn {{ request('status') === 'inactive' ? 'btn-warning' : 'btn-outline-warning' }}"
                                                        style="border-radius: 10px; padding: 10px 20px; font-weight: 600; transition: all 0.3s; border: 2px solid;"
                                                        onmouseover="this.style.transform='translateY(-2px)'"
                                                        onmouseout="this.style.transform='translateY(0)'">
                                                        <i class="fa fa-exclamation-circle me-1"></i> Inactive
                                                    </a>
                                                    <a href="{{ route('admin.events.index', array_merge(request()->except('status'), ['status' => 'closed'])) }}"
                                                        class="btn {{ request('status') === 'closed' ? 'btn-danger' : 'btn-outline-danger' }}"
                                                        style="border-radius: 10px; padding: 10px 20px; font-weight: 600; transition: all 0.3s; border: 2px solid;"
                                                        onmouseover="this.style.transform='translateY(-2px)'"
                                                        onmouseout="this.style.transform='translateY(0)'">
                                                        <i class="fa fa-times-circle me-1"></i> Closed
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                @if (request('search') || (request('category') && request('category') != 'all') || request('status'))
                                    <div class="search-results-info">
                                        <i class="fa fa-info-circle"></i>
                                        <span>
                                            @if (request('search'))
                                                Searching for: <strong>"{{ request('search') }}"</strong>
                                            @endif
                                            @if (request('search') && (request('category') && request('category') != 'all'))
                                                <span class="separator">|</span>
                                            @endif
                                            @if (request('category') && request('category') != 'all')
                                                Category: <strong>{{ ucfirst(request('category')) }}</strong>
                                            @endif
                                            @if ((request('search') || (request('category') && request('category') != 'all')) && request('status'))
                                                <span class="separator">|</span>
                                            @endif
                                            @if (request('status'))
                                                Status: <strong>{{ ucfirst(request('status')) }}</strong>
                                            @endif
                                            <span class="separator">|</span>
                                            Found: <strong>{{ $events->total() }}</strong> event(s)
                                        </span>
                                    </div>
                                @endif
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
                                                        @if ($event->category)
                                                            <span
                                                                class="badge badge-primary">{{ ucfirst($event->category) }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Card Body -->
                                        <div class="event-card-body">
                                            <!-- Category Badge (Prominent) -->
                                            @if ($event->category)
                                                <div class="event-category-badge mb-2">
                                                    <i class="fa fa-tag"></i>
                                                    <span class="category-name">{{ ucfirst($event->category) }}</span>
                                                </div>
                                            @else
                                                <div class="event-category-badge mb-2 text-muted">
                                                    <i class="fa fa-tag"></i>
                                                    <span class="category-name">Uncategorized</span>
                                                </div>
                                            @endif

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

                                            <!-- Category and Markets Info -->
                                            <div class="event-info-row">
                                                @if ($event->category)
                                                    <div class="info-item category-info">
                                                        <i class="fa fa-tag"></i>
                                                        <span>{{ ucfirst($event->category) }}</span>
                                                    </div>
                                                @endif
                                                <div class="markets-count">
                                                    <i class="fa fa-list"></i>
                                                    <span>{{ $event->markets->count() }} Markets</span>
                                                </div>
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
                                                <a href="{{ route('admin.events.edit', $event->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fa fa-edit"></i> Edit
                                                </a>
                                                <a href="{{ route('admin.events.show', $event->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('admin.events.add-markets', $event->id) }}"
                                                    class="btn btn-sm btn-success">
                                                    <i class="fa fa-plus-circle"></i> Add Markets
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="box empty-state-box">
                                        <div class="box-body text-center py-5">
                                            <i
                                                class="fa fa-{{ request('search') || (request('category') && request('category') != 'all') ? 'search' : 'inbox' }} fa-4x text-muted mb-3"></i>
                                            <h4>{{ request('search') || (request('category') && request('category') != 'all') ? 'No Events Found' : 'No Events Found' }}
                                            </h4>
                                            <p class="text-muted">
                                                @if (request('search') || (request('category') && request('category') != 'all'))
                                                    No events match your search criteria. Try adjusting your filters.
                                                @else
                                                    Start by adding your first event or fetching events from the API
                                                @endif
                                            </p>
                                            <div class="mt-3">
                                                @if (request('search') || (request('category') && request('category') != 'all'))
                                                    <a href="{{ route('admin.events.index') }}"
                                                        class="btn btn-secondary">
                                                        <i class="fa fa-refresh"></i> Clear Filters
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.events.create-with-markets') }}"
                                                        class="btn btn-success">
                                                        <i class="fa fa-plus-circle"></i> Create Event with Markets
                                                    </a>
                                                    <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                                                        <i class="fa fa-plus"></i> Create Event Only
                                                    </a>
                                                    <a href="{{ route('admin.event.fetch') }}" class="btn btn-info">
                                                        <i class="fa fa-download"></i> Fetch Events
                                                    </a>
                                                @endif
                                            </div>
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

            /* Search and Filter Box */
            .search-filter-box {
                background: #ffffff;
                border-radius: 12px;
                margin-bottom: 30px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                border: 1px solid #e9ecef;
            }

            .search-filter-form {
                margin-bottom: 0;
            }

            .form-label {
                font-weight: 600;
                color: #495057;
                margin-bottom: 8px;
                display: block;
                font-size: 13px;
            }

            .form-label i {
                color: #667eea;
                margin-right: 5px;
            }

            .search-input-wrapper {
                position: relative;
            }

            .search-input {
                border-radius: 10px;
                border: 2px solid #e9ecef;
                padding: 12px 45px 12px 15px;
                font-size: 14px;
                transition: all 0.3s ease;
            }

            .search-input:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            }

            .search-clear-btn {
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: #6c757d;
                text-decoration: none;
                font-size: 16px;
                transition: color 0.3s ease;
            }

            .search-clear-btn:hover {
                color: #e74c3c;
            }

            .category-select {
                border-radius: 10px;
                border: 2px solid #e9ecef;
                padding: 12px 15px;
                font-size: 14px;
                transition: all 0.3s ease;
            }

            .category-select:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            }

            .btn-search {
                border-radius: 10px;
                padding: 12px 25px;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            }

            .btn-search:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            }

            .btn-reset {
                border-radius: 10px;
                padding: 12px 25px;
                font-weight: 600;
                margin-left: 10px;
                transition: all 0.3s ease;
            }

            .btn-reset:hover {
                transform: translateY(-2px);
            }

            .search-results-info {
                margin-top: 20px;
                padding: 15px;
                background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
                border-radius: 10px;
                color: #1976d2;
                font-size: 14px;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .search-results-info i {
                font-size: 18px;
            }

            .search-results-info .separator {
                margin: 0 10px;
                opacity: 0.5;
            }

            .search-results-info strong {
                font-weight: 700;
                color: #1565c0;
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

            /* Category Badge */
            .event-category-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 14px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #ffffff;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
                transition: all 0.3s ease;
                margin-bottom: 12px;
            }

            .event-category-badge:hover {
                transform: scale(1.05);
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            }

            .event-category-badge i {
                font-size: 11px;
            }

            .event-category-badge .category-name {
                font-weight: 700;
            }

            .event-category-badge.text-muted {
                background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
                box-shadow: 0 2px 8px rgba(149, 165, 166, 0.2);
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

            /* Event Info Row */
            .event-info-row {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 15px;
                flex-wrap: wrap;
            }

            .category-info {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #ffffff;
                border-radius: 8px;
                padding: 6px 12px;
                font-size: 12px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                box-shadow: 0 2px 6px rgba(102, 126, 234, 0.3);
            }

            .category-info i {
                font-size: 11px;
            }

            /* Markets Count */
            .markets-count {
                background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
                padding: 10px 15px;
                border-radius: 8px;
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

                .list-header-box .d-flex {
                    flex-direction: column;
                    gap: 10px;
                }

                .list-header-box .btn {
                    width: 100%;
                }

                .search-filter-form .row>div {
                    margin-bottom: 15px;
                }

                .search-filter-form .col-md-4 {
                    margin-bottom: 0;
                }

                .btn-reset {
                    margin-left: 0;
                    margin-top: 10px;
                    width: 100%;
                }

                .search-results-info {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 5px;
                }

                .search-results-info .separator {
                    display: none;
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

                .event-actions {
                    flex-direction: column;
                }
            }
        </style>
    @endpush
@endsection

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
                                        $outcomePrices = is_string($market->outcome_prices ?? null) 
                                            ? json_decode($market->outcome_prices, true) 
                                            : ($market->outcome_prices ?? ['0.5', '0.5']);
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

                            <!-- Markets Details Section -->
                            <div class="box markets-container-new">
                                <div class="box-body">
                                    <h3 class="section-title-modern">
                                        <i class="fa fa-list"></i> All Markets ({{ $markets->count() }})
                                    </h3>
                                    <div class="markets-list-modern">
                                        @foreach ($markets as $index => $market)
                                            @php
                                                $outcomePrices = is_string($market->outcome_prices ?? null) 
                                                    ? json_decode($market->outcome_prices, true) 
                                                    : ($market->outcome_prices ?? ['0.5', '0.5']);
                                                $noPrice = isset($outcomePrices[0]) ? (float) $outcomePrices[0] : 0.5;
                                                $yesPrice = isset($outcomePrices[1]) ? (float) $outcomePrices[1] : 0.5;
                                                $chance = round($yesPrice * 100);
                                                $noPriceCents = round($noPrice * 100);
                                                $yesPriceCents = round($yesPrice * 100);
                                            @endphp
                                            <div class="market-detail-card">
                                                <div class="market-detail-header">
                                                    <div class="market-image-wrapper">
                                                        <img src="{{ $market->icon ? (str_starts_with($market->icon, 'http') ? $market->icon : asset('storage/' . $market->icon)) : asset('backend/assets/images/avatar.png') }}"
                                                            alt="{{ $market->question }}"
                                                            onerror="this.src='{{ asset('backend/assets/images/avatar.png') }}'">
                                                    </div>
                                                    <div class="market-detail-info">
                                                        <h4 class="market-question-modern">
                                                            <a href="{{ route('admin.market.show', $market->id) }}"
                                                                class="market-link">
                                                                {{ $market->question }}
                                                            </a>
                                                        </h4>
                                                        @if ($market->groupItem_title)
                                                            <p class="market-group-title">
                                                                <i class="fa fa-tag"></i> {{ $market->groupItem_title }}
                                                            </p>
                                                        @endif
                                                        @if ($market->description)
                                                            <p class="market-description">
                                                                {{ Str::limit($market->description, 150) }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="market-detail-body">
                                                    <div class="market-details-grid">
                                                        <div class="market-detail-item">
                                                            <span class="detail-label">Yes Price:</span>
                                                            <span class="detail-value">{{ $yesPriceCents }}¢</span>
                                                        </div>
                                                        <div class="market-detail-item">
                                                            <span class="detail-label">No Price:</span>
                                                            <span class="detail-value">{{ $noPriceCents }}¢</span>
                                                        </div>
                                                        <div class="market-detail-item">
                                                            <span class="detail-label">Chance:</span>
                                                            <span
                                                                class="detail-value chance-badge">{{ $chance }}%</span>
                                                        </div>
                                                        @if ($market->volume)
                                                            <div class="market-detail-item">
                                                                <span class="detail-label">Volume:</span>
                                                                <span
                                                                    class="detail-value">${{ number_format($market->volume, 2) }}</span>
                                                            </div>
                                                        @endif
                                                        @if ($market->liquidity)
                                                            <div class="market-detail-item">
                                                                <span class="detail-label">Liquidity:</span>
                                                                <span
                                                                    class="detail-value">${{ number_format($market->liquidity, 2) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="market-actions-admin">
                                                        <a href="{{ route('admin.market.show', $market->id) }}"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fa fa-eye"></i> View Details
                                                        </a>
                                                        <a href="{{ route('admin.market.edit', $market->id) }}"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Comments Section -->
                            <div class="comments-section-box">
                                <div class="box-body">
                                    <h3 class="comments-section-title">
                                        <i class="fa fa-comments"></i>
                                        Event Comments
                                        <span class="comments-count-info">
                                            (Total: {{ $event->comments->count() }})
                                        </span>
                                    </h3>

                                    @forelse($event->comments as $comment)
                                        <div
                                            class="comment-item {{ isset($comment->is_active) && !$comment->is_active ? 'comment-inactive' : '' }}">
                                            <div class="comment-header">
                                                <div class="comment-avatar">
                                                    @if ($comment->user && $comment->user->avatar)
                                                        <img src="{{ $comment->user->avatar }}"
                                                            alt="{{ $comment->user->name }}">
                                                    @else
                                                        <div class="comment-avatar-initials">
                                                            {{ $comment->user ? strtoupper(substr($comment->user->name, 0, 1)) : 'U' }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="comment-meta">
                                                    <div class="comment-author-name">
                                                        {{ $comment->user ? $comment->user->name : 'Unknown User' }}
                                                    </div>
                                                    <div class="comment-date">
                                                        <i class="fa fa-clock"></i>
                                                        {{ $comment->created_at->format('M d, Y h:i A') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="comment-body">
                                                {{ $comment->comment_text }}
                                            </div>
                                            <div class="comment-stats">
                                                <span class="comment-likes">
                                                    <i class="fa fa-heart"></i>
                                                    {{ $comment->likes_count ?? 0 }} likes
                                                </span>
                                                @if ($comment->replies->count() > 0)
                                                    <span class="comment-replies-count">
                                                        <i class="fa fa-reply"></i>
                                                        {{ $comment->replies->count() }}
                                                        {{ $comment->replies->count() == 1 ? 'reply' : 'replies' }}
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Comment Actions -->
                                            <div class="comment-actions-admin">
                                                <livewire:backend.comment-actions :commentId="$comment->id" :isActive="$comment->is_active ?? true"
                                                    :eventId="$event->id" :key="'comment-actions-' . $comment->id" />
                                            </div>

                                            <!-- Replies -->
                                            @if ($comment->replies->count() > 0)
                                                <div class="comment-replies">
                                                    @foreach ($comment->replies as $reply)
                                                        <div class="comment-reply-item">
                                                            <div class="comment-reply-header">
                                                                <div class="comment-reply-avatar">
                                                                    @if ($reply->user && $reply->user->avatar)
                                                                        <img src="{{ $reply->user->avatar }}"
                                                                            alt="{{ $reply->user->name }}">
                                                                    @else
                                                                        <div class="comment-reply-avatar-initials">
                                                                            {{ $reply->user ? strtoupper(substr($reply->user->name, 0, 1)) : 'U' }}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="comment-reply-meta">
                                                                    <div class="comment-reply-author-name">
                                                                        {{ $reply->user ? $reply->user->name : 'Unknown User' }}
                                                                    </div>
                                                                    <div class="comment-reply-date">
                                                                        <i class="fa fa-clock"></i>
                                                                        {{ $reply->created_at->format('M d, Y h:i A') }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="comment-reply-body">
                                                                {{ $reply->comment_text }}
                                                            </div>
                                                            <div class="comment-reply-stats">
                                                                <span class="comment-reply-likes">
                                                                    <i class="fa fa-heart"></i>
                                                                    {{ $reply->likes_count ?? 0 }} likes
                                                                </span>
                                                            </div>
                                                            <!-- Reply Actions -->
                                                            <div class="comment-reply-actions-admin">
                                                                <livewire:backend.comment-actions :commentId="$reply->id"
                                                                    :isActive="$reply->is_active ?? true" :eventId="$event->id" :key="'reply-actions-' . $reply->id" />
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="no-comments">
                                            <i class="fa fa-comment-slash fa-3x"></i>
                                            <p>No comments yet</p>
                                        </div>
                                    @endforelse
                                </div>
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

                    <!-- Right Sidebar -->
                    <div class="col-lg-4">
                        <!-- No Markets Alert -->
                        @if ($event->markets->count() == 0)
                            <div class="box mb-3"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                <div class="box-body text-center py-4">
                                    <i class="fa fa-chart-line fa-3x text-white mb-3"></i>
                                    <h4 class="text-white mb-2">No Markets Added Yet</h4>
                                    <p class="text-white mb-3">This event doesn't have any markets. Add
                                        markets to enable
                                        trading.</p>
                                    <a href="{{ route('admin.events.add-markets', $event) }}"
                                        class="btn btn-light btn-lg">
                                        <i class="fa fa-plus-circle"></i> Add Markets Now
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Markets Card -->
                        @if ($event->markets->count() > 0)
                            <div class="box sidebar-card">
                                <div class="box-body">
                                    <h4 class="sidebar-title">
                                        <i class="fa fa-list"></i>
                                        All Markets ({{ $event->markets->count() }})
                                    </h4>
                                    <div class="markets-list">
                                        @foreach ($event->markets as $market)
                                            <a href="{{ route('admin.market.show', $market->id) }}">
                                                <div class="market-item d-flex align-items-center gap-2">
                                                    <img src="{{ $market->icon }}" width="40" height="40"
                                                        class="rounded-circle" alt="{{ $market->question }}">
                                                    <div class="market-question">
                                                        <i class="fa fa-question-circle"></i>
                                                        {{ Str::limit($market->question ?? 'N/A', 60) }}
                                                    </div>
                                                    @if ($market->groupItem_title)
                                                        <div class="market-group">
                                                            <small>{{ $market->groupItem_title }}</small>
                                                        </div>
                                                    @endif

                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Event Info Card -->
                        <div class="info-card-modern">
                            <h4 class="info-card-title">
                                <i class="fa fa-info-circle"></i> Event Details
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
    @push('styles')
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

            .markets-list {
                max-height: 400px;
                overflow-y: auto;
            }

            .market-item {
                padding: 15px;
                background: #f8f9fa;
                border-radius: 10px;
                margin-bottom: 10px;
                transition: all 0.3s ease;
            }

            .market-item:hover {
                background: #e9ecef;
            }

            .market-question {
                font-weight: 600;
                color: #2c3e50;
                margin-bottom: 5px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .market-question i {
                color: #667eea;
            }

            .market-group {
                color: #6c757d;
                font-size: 12px;
                margin-bottom: 10px;
            }
        </style>
    @endpush
    <!-- No Markets Alert -->
    @if ($event->markets->count() == 0)
        <div class="box mb-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
            <div class="box-body text-center py-4">
                <i class="fa fa-chart-line fa-3x text-white mb-3"></i>
                <h4 class="text-white mb-2">No Markets Added Yet</h4>
                <p class="text-white mb-3">This event doesn't have any markets. Add
                    markets to enable
                    trading.</p>
                <a href="{{ route('admin.events.add-markets', $event) }}" class="btn btn-light btn-lg">
                    <i class="fa fa-plus-circle"></i> Add Markets Now
                </a>
            </div>
        </div>
    @endif

    @push('styles')
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

            <style>

            /* Event Detail Card */
            .event-detail-card {
                border-radius: 16px;
                margin-bottom: 30px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            }

            .event-detail-image-wrapper {
                position: relative;
                width: 100%;
                height: 300px;
                overflow: hidden;
                border-radius: 12px;
                margin-bottom: 20px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .event-detail-image-wrapper img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .event-detail-overlay {
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

            .event-detail-badges {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .event-detail-title {
                font-size: 32px;
                font-weight: 700;
                color: #2c3e50;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .event-detail-title i {
                color: #667eea;
            }

            .event-detail-description {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 10px;
                margin-bottom: 25px;
                color: #495057;
                line-height: 1.8;
            }

            /* Metrics */
            .event-detail-metrics {
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

            /* Dates */
            .event-detail-dates {
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

            /* Tags */
            .event-detail-tags {
                margin-bottom: 25px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 10px;
            }

            .tags-label {
                font-weight: 600;
                margin-right: 10px;
                color: #495057;
            }

            .tag-badge {
                display: inline-block;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 5px 12px;
                border-radius: 20px;
                font-size: 12px;
                margin-right: 8px;
                margin-bottom: 5px;
            }

            /* Actions */
            .event-detail-actions {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }

            /* Comments Section */
            .comments-section-box {
                border-radius: 16px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            }

            .comments-section-title {
                font-size: 24px;
                font-weight: 700;
                color: #2c3e50;
                margin-bottom: 25px;
                padding-bottom: 15px;
                border-bottom: 2px solid #e9ecef;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .comments-section-title i {
                color: #667eea;
            }

            .comment-item {
                padding: 20px;
                border-bottom: 1px solid #e9ecef;
                margin-bottom: 20px;
            }

            .comment-item.comment-inactive {
                background: #fff3cd;
                opacity: 0.8;
                border-left: 4px solid #ffc107;
            }

            .comment-reply-item.comment-reply-inactive {
                background: #fff3cd;
                opacity: 0.8;
                border-left: 3px solid #ffc107;
            }

            .comments-count-info {
                font-size: 16px;
                font-weight: 400;
                color: #6c757d;
            }

            .comment-item:last-child {
                border-bottom: none;
                margin-bottom: 0;
            }

            .comment-header {
                display: flex;
                align-items: center;
                gap: 15px;
                margin-bottom: 15px;
            }

            .comment-avatar {
                width: 45px;
                height: 45px;
                border-radius: 50%;
                overflow: hidden;
                flex-shrink: 0;
            }

            .comment-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .comment-avatar-initials {
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 18px;
            }

            .comment-meta {
                flex: 1;
            }

            .comment-author-name {
                font-weight: 600;
                color: #2c3e50;
                margin-bottom: 5px;
            }

            .comment-date {
                font-size: 12px;
                color: #6c757d;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .comment-body {
                color: #495057;
                line-height: 1.6;
                margin-bottom: 15px;
                padding-left: 60px;
            }

            .comment-stats {
                display: flex;
                gap: 20px;
                padding-left: 60px;
                font-size: 14px;
                color: #6c757d;
            }

            .comment-likes,
            .comment-replies-count {
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .comment-likes i {
                color: #e74c3c;
            }

            /* Comment Admin Actions */
            .comment-actions-admin {
                display: flex;
                gap: 10px;
                align-items: center;
                padding-left: 60px;
                margin-top: 10px;
                padding-top: 10px;
                border-top: 1px solid #e9ecef;
            }

            .comment-actions-admin .btn {
                font-size: 12px;
                padding: 5px 12px;
            }

            .comment-reply-actions-admin {
                display: flex;
                gap: 8px;
                align-items: center;
                padding-left: 47px;
                margin-top: 8px;
                padding-top: 8px;
                border-top: 1px solid #e9ecef;
            }

            .comment-reply-actions-admin .btn-xs {
                font-size: 11px;
                padding: 3px 8px;
            }

            .badge-sm {
                font-size: 10px;
                padding: 3px 6px;
            }

            /* Replies */
            .comment-replies {
                margin-top: 20px;
                margin-left: 60px;
                padding-left: 20px;
                border-left: 3px solid #e9ecef;
            }

            .comment-reply-item {
                padding: 15px;
                background: #f8f9fa;
                border-radius: 10px;
                margin-bottom: 15px;
            }

            .comment-reply-header {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 10px;
            }

            .comment-reply-avatar {
                width: 35px;
                height: 35px;
                border-radius: 50%;
                overflow: hidden;
                flex-shrink: 0;
            }

            .comment-reply-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .comment-reply-avatar-initials {
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                font-size: 14px;
            }

            .comment-reply-author-name {
                font-weight: 600;
                color: #2c3e50;
                font-size: 14px;
            }

            .comment-reply-date {
                font-size: 11px;
                color: #6c757d;
            }

            .comment-reply-body {
                color: #495057;
                font-size: 14px;
                line-height: 1.5;
                margin-bottom: 10px;
                padding-left: 47px;
            }

            .comment-reply-stats {
                padding-left: 47px;
            }

            .no-comments {
                text-align: center;
                padding: 60px 20px;
                color: #6c757d;
            }

            .no-comments i {
                margin-bottom: 15px;
                opacity: 0.5;
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

            .markets-list {
                max-height: 400px;
                overflow-y: auto;
            }

            .market-item {
                padding: 15px;
                background: #f8f9fa;
                border-radius: 10px;
                margin-bottom: 10px;
                transition: all 0.3s ease;
            }

            .market-item:hover {
                background: #e9ecef;
            }

            .market-question {
                font-weight: 600;
                color: #2c3e50;
                margin-bottom: 5px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .market-question i {
                color: #667eea;
            }

            .market-group {
                color: #6c757d;
                font-size: 12px;
                margin-bottom: 10px;
            }

            .market-item-actions {
                margin-top: 10px;
                padding-top: 10px;
                border-top: 1px solid #dee2e6;
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

            @media (max-width: 768px) {
                .event-detail-title {
                    font-size: 24px;
                }

                .event-detail-metrics {
                    grid-template-columns: 1fr;
                }

                .comment-body,
                .comment-stats,
                .comment-replies {
                    padding-left: 0;
                    margin-left: 0;
                }

                .comment-reply-body,
                .comment-reply-stats {
                    padding-left: 0;
                }
            }

            /* Market Detail Card */
            .market-detail-card {
                border: 1px solid #e0e0e0;
                border-radius: 12px;
                padding: 20px;
                margin-bottom: 20px;
                background: #ffffff;
                transition: all 0.2s ease;
            }

            .market-detail-card:hover {
                border-color: #667eea;
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
            }

            .market-detail-header {
                display: flex;
                align-items: flex-start;
                gap: 15px;
                margin-bottom: 15px;
                padding-bottom: 15px;
                border-bottom: 1px solid #f0f0f0;
            }

            .market-detail-info {
                flex: 1;
            }

            .market-link {
                color: #1a1a1a;
                text-decoration: none;
                transition: color 0.2s ease;
            }

            .market-link:hover {
                color: #667eea;
            }

            .market-description {
                font-size: 14px;
                color: #666;
                margin-top: 8px;
                line-height: 1.6;
            }

            .market-detail-body {
                margin-top: 15px;
            }

            .market-details-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 15px;
                margin-bottom: 15px;
            }

            .market-detail-item {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            .detail-label {
                font-size: 12px;
                color: #666;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-weight: 600;
            }

            .detail-value {
                font-size: 16px;
                font-weight: 700;
                color: #1a1a1a;
            }

            .chance-badge {
                display: inline-block;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 14px;
            }

            .market-actions-admin {
                display: flex;
                gap: 10px;
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid #f0f0f0;
            }

            /* Markets Container New */
            .markets-container-new {
                border-radius: 16px;
                margin-bottom: 30px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
                background: #ffffff;
                width: 100%;
                max-width: 100%;
                overflow: hidden;
                box-sizing: border-box;
            }

            .markets-container-new .box-body {
                padding: 25px;
                width: 100%;
                box-sizing: border-box;
            }

            .markets-list-modern {
                width: 100%;
                max-width: 100%;
                overflow-x: hidden;
                box-sizing: border-box;
            }

            .market-detail-card {
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }

            .market-detail-header {
                width: 100%;
                box-sizing: border-box;
            }

            .market-detail-info {
                min-width: 0;
                flex: 1;
            }

            .market-question-modern {
                word-wrap: break-word;
                overflow-wrap: break-word;
                max-width: 100%;
            }
        </style>
    @endpush

@endsection

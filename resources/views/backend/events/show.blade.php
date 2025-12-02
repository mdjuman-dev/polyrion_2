@extends('backend.layouts.master')
@section('title', 'Event Details')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <!-- Back Button -->
                <div class="row mb-3">
                    <div class="col-12">
                        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Events
                        </a>
                    </div>
                </div>

                <div class="row">
                    <!-- Event Details Section -->
                    <div class="col-lg-8">
                        <!-- Event Header Card -->
                        <div class="box event-detail-card">
                            <div class="box-body">
                                <!-- Event Image -->
                                <div class="event-detail-image-wrapper">
                                    <img src="{{ $event->image ? (str_starts_with($event->image, 'http') ? $event->image : asset('storage/' . $event->image)) : asset('backend/assets/images/avatar.png') }}"
                                        alt="{{ $event->title }}"
                                        onerror="this.src='{{ asset('backend/assets/images/avatar.png') }}'">
                                    <div class="event-detail-overlay">
                                        <div class="event-detail-badges">
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
                                                <span class="badge badge-primary">{{ ucfirst($event->category) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Event Title -->
                                <h1 class="event-detail-title">
                                    <i class="fa fa-calendar"></i>
                                    {{ $event->title }}
                                </h1>

                                <!-- Event Description -->
                                @if ($event->description)
                                    <div class="event-detail-description">
                                        <p>{{ $event->description }}</p>
                                    </div>
                                @endif

                                <!-- Event Metrics -->
                                <div class="row event-detail-metrics">
                                    <div class="col-md-3 col-sm-6">
                                        <div class="metric-box">
                                            <div class="metric-icon-box">
                                                <i class="fa fa-dollar-sign"></i>
                                            </div>
                                            <div class="metric-content">
                                                <span class="metric-label">Liquidity</span>
                                                <span
                                                    class="metric-value">${{ number_format($event->liquidity ?? 0, 2) }}</span>
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
                                                    class="metric-value">${{ number_format($event->volume ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="metric-box">
                                            <div class="metric-icon-box">
                                                <i class="fa fa-list"></i>
                                            </div>
                                            <div class="metric-content">
                                                <span class="metric-label">Markets</span>
                                                <span class="metric-value">{{ $event->markets->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="metric-box">
                                            <div class="metric-icon-box">
                                                <i class="fa fa-comments"></i>
                                            </div>
                                            <div class="metric-content">
                                                <span class="metric-label">Comments</span>
                                                <span
                                                    class="metric-value">{{ $totalCommentsCount ?? $event->comments->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Event Dates -->
                                <div class="event-detail-dates">
                                    @if ($event->start_date)
                                        <div class="date-box">
                                            <i class="fa fa-calendar-check"></i>
                                            <div>
                                                <span class="date-label">Start Date</span>
                                                <span class="date-value">{{ format_date($event->start_date) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($event->end_date)
                                        <div class="date-box">
                                            <i class="fa fa-calendar-times"></i>
                                            <div>
                                                <span class="date-label">End Date</span>
                                                <span class="date-value">{{ format_date($event->end_date) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Tags -->
                                @if ($event->tags->count() > 0)
                                    <div class="event-detail-tags">
                                        <span class="tags-label">Tags:</span>
                                        @foreach ($event->tags as $tag)
                                            <span class="tag-badge">{{ $tag->label }}</span>
                                        @endforeach
                                    </div>
                                @endif


                            </div>
                        </div>

                        <!-- Comments Section -->
                        <div class="box comments-section-box">
                            <div class="box-body">
                                <h3 class="comments-section-title">
                                    <i class="fa fa-comments"></i>
                                    Comments
                                    <span class="comments-count-info">
                                        (Total: {{ $totalCommentsCount ?? $event->comments->count() }},
                                        Active:
                                        {{ $activeCommentsCount ?? $event->comments->where('is_active', '!=', false)->count() }})
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
                                                    {{ format_date($comment->created_at) }}
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
                                                                    {{ format_date($reply->created_at) }}
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
                                        <p>No comments</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Markets Card -->
                        @if ($event->markets->count() > 0)
                            <div class="box sidebar-card">
                                <div class="box-body">
                                    <h4 class="sidebar-title">
                                        <i class="fa fa-list"></i>
                                        Markets ({{ $event->markets->count() }})
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
                        <div class="box sidebar-card">
                            <div class="box-body">
                                <h4 class="sidebar-title">
                                    <i class="fa fa-info-circle"></i>
                                    Event Information
                                </h4>
                                <div class="info-list">
                                    <div class="info-item">
                                        <span class="info-label">Status:</span>
                                        <span class="info-value">
                                            @if ($event->active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Created:</span>
                                        <span class="info-value">{{ format_date($event->created_at) }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Updated:</span>
                                        <span class="info-value">{{ format_date($event->updated_at) }}</span>
                                    </div>
                                    @if ($event->slug)
                                        <div class="info-item">
                                            <span class="info-label">Slug:</span>
                                            <span class="info-value"><code>{{ $event->slug }}</code></span>
                                        </div>
                                    @endif
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
        </style>
    @endpush
@endsection

@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>{{ $tag->label }} - {{ $appName }}</title>
    <meta name="description" content="Events tagged with '{{ $tag->label }}' on {{ $appName }}.">
    <meta property="og:title" content="{{ $tag->label }} - {{ $appName }}">
    <link rel="canonical" href="{{ $appUrl }}/tag/{{ $tag->slug }}">
@endsection
@section('content')
    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Tag Header -->
            <div class="tag-header mb-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h1 class="tag-title"
                            style="color: var(--text-primary); font-size: 28px; font-weight: 700; margin-bottom: 8px;">
                            <i class="fas fa-tag" style="color: var(--accent); margin-right: 8px;"></i>
                            {{ $tag->label }}
                        </h1>
                        <p class="tag-description" style="color: var(--text-secondary); font-size: 14px;">
                            Events tagged with "{{ $tag->label }}"
                        </p>
                    </div>
                    <a href="{{ route('home') }}" class="bookmark-icon-btn" title="Back to Home">
                        <i class="fas fa-home"></i>
                    </a>
                </div>
            </div>

            <!-- Tagged Events Grid - Livewire Component -->
            <livewire:tagged-events-grid :tagSlug="$tag->slug" />
        </div>
    </main>
    @push('style')
        <style>
            /* Common Styles */
            .market-card {
                border-radius: 12px;
                padding: 16px;
                margin-bottom: 16px;
            }

            .market-card-header {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 16px;
            }

            .market-profile-img {
                width: 48px;
                height: 48px;
                border-radius: 8px;
                overflow: hidden;
            }

            .market-profile-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .market-card-title {
                color: var(--text-primary);
                text-decoration: none;
                font-size: 16px;
                font-weight: 600;
            }

            /* Type 1: Single Market */
            .single-market .market-card-header {
                justify-content: space-between;
            }

            .market-title-section {
                flex: 1;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .market-chance {
                display: flex;
                align-items: center;
                gap: 4px;
                padding: 4px 12px;
                border-radius: 6px;
            }

            .chance-value {
                color: var(--danger);
                font-weight: 700;
                font-size: 18px;
            }

            .market-card-body-single {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                margin-bottom: 16px;
            }

            .market-card-yes-btn-large,
            .market-card-no-btn-large {
                padding: 10px 16px;
                border-radius: 8px;
                border: none;
                font-size: 18px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
            }

            .market-card-yes-btn-large {
                background: rgba(0, 200, 83, 0.1);
                color: var(--success);
                border: 2px solid rgba(0, 200, 83, 0.3);
            }

            .market-card-yes-btn-large:hover {
                background: rgba(0, 200, 83, 0.2);
                border-color: var(--success);
            }

            .market-card-no-btn-large {
                background: rgba(255, 71, 87, 0.1);
                color: var(--danger);
                border: 2px solid rgba(255, 71, 87, 0.3);
            }

            .market-card-no-btn-large:hover {
                background: rgba(255, 71, 87, 0.2);
                border-color: var(--danger);
            }

            /* Type 2: Multi Market */
            .multi-market .market-card-body {
                display: flex;
                flex-direction: column;
                gap: 8px;
                margin-bottom: 16px;
            }

            .market-card-outcome-row {
                display: grid;
                grid-template-columns: 2fr auto auto auto;
                align-items: center;
                gap: 12px;
                padding: 8px;
                background: var(--secondary);
                border-radius: 6px;
            }

            .market-card-outcome-label {
                color: var(--text-secondary);
                font-size: 14px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .market-card-outcome-probability {
                color: var(--text-primary);
                font-weight: 700;
                font-size: 16px;
            }

            .market-card-yes-btn,
            .market-card-no-btn {
                padding: 6px 12px;
                border-radius: 6px;
                border: none;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
                min-width: 45px;
                white-space: nowrap;
            }

            .market-card-yes-btn {
                background: rgba(0, 200, 83, 0.1);
                color: var(--success);
            }

            .market-card-yes-btn:hover {
                background: rgba(0, 200, 83, 0.2);
            }

            .market-card-no-btn {
                background: rgba(255, 71, 87, 0.1);
                color: var(--danger);
            }

            .market-card-no-btn:hover {
                background: rgba(255, 71, 87, 0.2);
            }

            /* Footer */
            .market-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding-top: 12px;
                border-top: 1px solid var(--border);
            }

            .market-card-volume {
                color: var(--text-secondary);
                font-size: 14px;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .market-actions {
                display: flex;
                gap: 8px;
            }

            .loader,
            .loader:before,
            .loader:after {
                border-radius: 50%;
                width: 2.5em;
                height: 2.5em;
                animation-fill-mode: both;
                animation: bblFadInOut 1.8s infinite ease-in-out;
            }

            .loader {
                color: #FFF;
                font-size: 7px;
                position: relative;
                text-indent: -9999em;
                transform: translateZ(0);
                animation-delay: -0.16s;
            }

            .loader:before,
            .loader:after {
                content: '';
                position: absolute;
                top: 0;
            }

            .loader:before {
                left: -3.5em;
                animation-delay: -0.32s;
            }

            .loader:after {
                left: 3.5em;
            }

            @keyframes bblFadInOut {

                0%,
                80%,
                100% {
                    box-shadow: 0 2.5em 0 -1.3em
                }

                40% {
                    box-shadow: 0 2.5em 0 0
                }
            }

            /* Empty State Card */
            .empty-state-card {
                background: var(--secondary);
                border: 1px solid var(--border);
                border-radius: 12px;
                transition: all 0.3s ease;
                text-align: center;
                padding: 48px 24px;
            }

            .empty-state-card:hover {
                border-color: var(--accent);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .empty-state-icon-wrapper {
                margin-bottom: 24px;
                animation: float 3s ease-in-out infinite;
            }

            .empty-state-icon-circle {
                width: 120px;
                height: 120px;
                margin: 0 auto;
                background: var(--secondary);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 3px solid var(--border);
            }

            .empty-state-icon-circle i {
                font-size: 56px;
                color: var(--text-secondary);
            }

            .empty-state-title {
                color: var(--text-primary);
                margin-bottom: 12px;
                font-size: 24px;
                font-weight: 600;
            }

            .empty-state-description {
                color: var(--text-secondary);
                font-size: 16px;
                margin-bottom: 32px;
                max-width: 400px;
                margin-left: auto;
                margin-right: auto;
                line-height: 1.6;
            }

            .empty-state-btn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 12px 24px;
                font-size: 16px;
                font-weight: 600;
                text-decoration: none;
                border-radius: 8px;
                background: var(--accent);
                color: var(--text-primary);
                border: 1px solid var(--border);
                transition: all 0.3s ease;
            }

            .empty-state-btn:hover {
                background: var(--hover);
                border-color: var(--accent);
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            }

            @keyframes float {

                0%,
                100% {
                    transform: translateY(0px);
                }

                50% {
                    transform: translateY(-10px);
                }
            }

            /* Mobile Responsive */
            @media (max-width: 768px) {
                .tag-title {
                    font-size: 22px !important;
                }

                .tag-description {
                    font-size: 12px !important;
                }

                .empty-state-card {
                    padding: 32px 16px;
                }

                .empty-state-icon-circle {
                    width: 100px;
                    height: 100px;
                }

                .empty-state-icon-circle i {
                    font-size: 48px;
                }

                .empty-state-title {
                    font-size: 20px;
                }

                .empty-state-description {
                    font-size: 14px;
                    padding: 0 16px;
                }

                .empty-state-btn {
                    padding: 10px 20px;
                    font-size: 14px;
                    width: 100%;
                    max-width: 280px;
                    justify-content: center;
                }

                .secondary-search-bar {
                    width: 100% !important;
                    margin-right: 8px !important;
                }
            }
        </style>
    @endpush
@endsection

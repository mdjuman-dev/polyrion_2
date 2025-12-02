@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Trending - Polymarket</title>
@endsection
@section('content')
    <main>
        <div class="container">
            <livewire:trending-events-grid />
        </div>
    </main>
    @push('style')
        <style>
            /* Common Styles - Same as home page */
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
                width: 40px;
                height: 40px;
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
        </style>
    @endpush
@endsection

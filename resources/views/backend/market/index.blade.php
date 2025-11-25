@extends('backend.layouts.master')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Search Section -->
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="box search-header-box">
                            <div class="box-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h2 class="search-title">
                                            <i class="fa fa-search"></i> Market Search
                                        </h2>
                                        <p class="search-subtitle">Search for markets by slug, title or keyword</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="d-flex align-items-center gap-3 justify-content-end">
                                            <a href="{{ route('admin.market.list') }}" class="btn btn-light">
                                                <i class="fa fa-list"></i> View All Events
                                            </a>
                                            @if (isset($data))
                                                <span class="badge badge-success">
                                                    <i class="fa fa-check-circle"></i> Results Found
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="box modern-search-box">
                            <div class="box-body">
                                <form action="{{ route('admin.market.search') }}" method="post" id="searchForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="form-group modern-form-group">
                                                <label for="search" class="modern-label">
                                                    <i class="fa fa-search"></i> Search Market
                                                </label>
                                                <div class="input-group modern-input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-link"></i>
                                                    </span>
                                                    <input type="text" name="search" id="search"
                                                        class="form-control modern-input"
                                                        placeholder="Enter market slug or keyword..."
                                                        value="{{ old('search') }}" autocomplete="off">
                                                </div>
                                                <small class="form-text text-muted">
                                                    <i class="fa fa-info-circle"></i> Enter the market slug from Polymarket
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group modern-form-group">
                                                <label class="modern-label">&nbsp;</label>
                                                <button type="submit" class="btn btn-primary btn-block btn-modern-search">
                                                    <i class="fa fa-search"></i> Search
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Results Section -->
            @if (isset($data))
                <section class="content">
                    <div class="row">
                        <div class="col-12">
                            <!-- Main Market Header -->
                            <div class="box market-header-box">
                                <div class="box-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <div class="market-main-image">
                                                <img src="{{ $data->image ?? asset('backend/assets/images/avatar.png') }}"
                                                    alt="{{ $data->title ?? 'Market' }}"
                                                    onerror="this.src='{{ asset('backend/assets/images/avatar.png') }}'">
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h2 class="market-main-title">{{ $data->title ?? 'Market Title' }}
                                                    </h2>
                                                    <p class="market-main-description">
                                                        {{ $data->description ?? 'No description available' }}
                                                    </p>
                                                    <div class="market-dates">
                                                        @if (isset($data->startDate))
                                                            <span class="badge badge-primary">
                                                                <i class="fa fa-calendar-check"></i> Start:
                                                                {{ format_date($data->startDate) }}
                                                            </span>
                                                        @endif
                                                        @if (isset($data->endDate))
                                                            <span class="badge badge-danger ml-2">
                                                                <i class="fa fa-calendar-times"></i> End:
                                                                {{ format_date($data->endDate) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Save Button -->
                                        <div class="text-end col-md-2">
                                            <a href="{{ route('admin.market.save', $data->slug ?? '#') }}"
                                                class="btn btn-primary btn-lg btn-modern">
                                                <i class="fa fa-save"></i> Save Market
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Markets Grid -->
                            <div class="row market-grid" id="marketsGrid">
                                @foreach ($data->markets ?? [] as $index => $item)
                                    <div class="col-lg-6 col-xl-4 market-card-wrapper" data-market-index="{{ $index }}">
                                        <div class="box market-card modern-market-card">
                                            <!-- Card Header with Image -->
                                            <div class="market-card-header-modern">
                                                <div class="market-card-image-wrapper">
                                                    <div class="market-card-image">
                                                        <img src="{{ $item->icon ?? asset('backend/assets/images/avatar.png') }}"
                                                            alt="{{ $item->question ?? 'Market' }}"
                                                            onerror="this.src='{{ asset('backend/assets/images/avatar.png') }}'">
                                                    </div>
                                                    <div class="market-card-overlay">
                                                        <div class="market-status-badge">
                                                            @if (isset($item->active) && $item->active)
                                                                <span class="badge badge-success badge-pulse">Active</span>
                                                            @else
                                                                <span class="badge badge-secondary">Inactive</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Card Body -->
                                            <div class="market-card-body-modern">
                                                <!-- Question Section -->
                                                <div class="market-question-section">
                                                    <h4 class="market-question">
                                                        <i class="fa fa-question-circle"></i>
                                                        {{ $item->question ?? 'Market Question' }}
                                                    </h4>
                                                </div>

                                                <!-- Key Metrics - Modern Design -->
                                                <div class="market-metrics-modern">
                                                    <div class="metric-card">
                                                        <div class="metric-icon">
                                                            <i class="fa fa-dollar-sign"></i>
                                                        </div>
                                                        <div class="metric-content">
                                                            <span class="metric-label-modern">Liquidity</span>
                                                            <span class="metric-value-modern">
                                                                ${{ number_format($item->liquidity ?? 0, 2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="metric-card">
                                                        <div class="metric-icon">
                                                            <i class="fa fa-chart-bar"></i>
                                                        </div>
                                                        <div class="metric-content">
                                                            <span class="metric-label-modern">Volume</span>
                                                            <span class="metric-value-modern">
                                                                ${{ number_format($item->volume ?? 0, 2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Volume Chart -->
                                                <div class="volume-chart-container">
                                                    <canvas id="volumeChart{{ $index }}" height="120"></canvas>
                                                </div>

                                                <!-- Outcome Prices -->
                                                @if (isset($item->outcomePrices))
                                                    @php
                                                        $prices = json_decode($item->outcomePrices, true);

                                                        if ($prices && is_array($prices) && count($prices) >= 2) {
                                                            $yesPercent = format_number($prices[0] * 100, 2);
                                                            $noPercent = format_number($prices[1] * 100, 2);
                                                        } else {
                                                            $yesPercent = 'N/A';
                                                            $noPercent = 'N/A';
                                                        }
                                                    @endphp

                                                    <div class="outcome-prices mt-3">
                                                        <div class="outcome-cards">
                                                            <div class="outcome-card outcome-yes">
                                                                <div class="outcome-label">YES</div>
                                                                <div class="outcome-percent">{{ $yesPercent }}%
                                                                </div>
                                                            </div>
                                                            <div class="outcome-card outcome-no">
                                                                <div class="outcome-label">NO</div>
                                                                <div class="outcome-percent">{{ $noPercent }}%
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>


                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>

    @push('styles')
        <style>
            /* Search Header */
            .search-header-box {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                margin-bottom: 25px;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

            .search-title {
                font-size: 28px;
                font-weight: 700;
                margin: 0;
                color: white;
            }

            .search-title i {
                margin-right: 10px;
            }

            .search-subtitle {
                margin: 5px 0 0 0;
                opacity: 0.9;
                font-size: 14px;
            }

            /* Modern Search Box */
            .modern-search-box {
                border-radius: 12px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
                border: none;
                margin-bottom: 30px;
            }

            .modern-form-group {
                margin-bottom: 0;
            }

            .modern-label {
                font-weight: 600;
                color: #495057;
                margin-bottom: 8px;
                display: block;
                font-size: 14px;
            }

            .modern-label i {
                margin-right: 8px;
                color: #667eea;
            }

            .modern-input-group {
                position: relative;
            }

            .modern-input-group .input-group-text {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                border-radius: 8px 0 0 8px;
            }

            .modern-input {
                border-radius: 0 8px 8px 0;
                border: 2px solid #e9ecef;
                padding: 12px 15px;
                transition: all 0.3s ease;
                font-size: 14px;
            }

            .modern-input:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
                outline: none;
            }

            .btn-modern-search {
                border-radius: 8px;
                padding: 12px 20px;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            }

            .btn-modern-search:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            }

            /* Market Header */
            .market-header-box {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                margin-bottom: 30px;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

            .market-main-image {
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            }

            .market-main-image img {
                width: 100%;
                height: 200px;
                object-fit: cover;
            }

            .market-main-title {
                font-size: 32px;
                font-weight: 700;
                margin-bottom: 15px;
                color: white;
            }

            .market-main-description {
                font-size: 16px;
                margin-bottom: 20px;
                opacity: 0.95;
            }

            .market-dates .badge {
                font-size: 14px;
                padding: 8px 15px;
                border-radius: 20px;
            }

            /* Market Cards - Modern Design */
            .market-grid {
                margin-top: 30px;
            }

            .market-card-wrapper {
                position: relative;
                margin-bottom: 30px;
            }

            .modern-market-card {
                border-radius: 16px;
                overflow: hidden;
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
                height: 100%;
                display: flex;
                flex-direction: column;
                position: relative;
                background: #ffffff;
                border: 1px solid rgba(0, 0, 0, 0.05);
            }

            .modern-market-card:hover {
                transform: translateY(-8px) scale(1.02);
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
                border-color: rgba(102, 126, 234, 0.3);
            }

            /* Card Header */
            .market-card-header-modern {
                position: relative;
                overflow: hidden;
            }

            .market-card-image-wrapper {
                position: relative;
                width: 100%;
                height: 140px;
                overflow: hidden;
            }

            .market-card-image {
                width: 100%;
                height: 100%;
                overflow: hidden;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                position: relative;
            }

            .market-card-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }

            .modern-market-card:hover .market-card-image img {
                transform: scale(1.15);
            }

            .market-card-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 100%);
                display: flex;
                align-items: flex-start;
                justify-content: flex-end;
                padding: 10px;
            }

            .market-status-badge {
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
            .market-card-body-modern {
                padding: 15px;
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            .market-question-section {
                margin-bottom: 15px;
            }

            .market-question {
                font-size: 16px;
                font-weight: 700;
                color: #2c3e50;
                margin-bottom: 0;
                line-height: 1.4;
                display: flex;
                align-items: flex-start;
                gap: 8px;
            }

            .market-question i {
                color: #667eea;
                margin-top: 3px;
                font-size: 14px;
            }

            /* Modern Metrics */
            .market-metrics-modern {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                margin-bottom: 15px;
            }

            .metric-card {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border-radius: 10px;
                padding: 12px;
                display: flex;
                align-items: center;
                gap: 10px;
                transition: all 0.3s ease;
                border: 1px solid rgba(0, 0, 0, 0.05);
            }

            .metric-card:hover {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            }

            .metric-card:hover .metric-icon {
                background: rgba(255, 255, 255, 0.2);
                color: white;
            }

            .metric-card:hover .metric-label-modern,
            .metric-card:hover .metric-value-modern {
                color: white;
            }

            .metric-icon {
                width: 38px;
                height: 38px;
                border-radius: 8px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 16px;
                transition: all 0.3s ease;
                flex-shrink: 0;
            }

            .metric-content {
                flex: 1;
                min-width: 0;
            }

            .metric-label-modern {
                display: block;
                font-size: 11px;
                color: #6c757d;
                margin-bottom: 4px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-weight: 600;
                transition: color 0.3s ease;
            }

            .metric-value-modern {
                display: block;
                font-size: 14px;
                font-weight: 700;
                color: #2c3e50;
                transition: color 0.3s ease;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .volume-chart-container {
                margin: 15px 0;
                padding: 12px;
                background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
                position: relative;
                overflow: hidden;
            }

            .volume-chart-container::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
                pointer-events: none;
            }

            .volume-chart-container canvas {
                position: relative;
                z-index: 1;
            }

            /* Outcome Prices */
            .outcome-prices {
                margin-top: 15px;
            }

            .outcome-cards {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                margin-top: 0;
            }

            .outcome-card {
                padding: 10px;
                border-radius: 8px;
                text-align: center;
                transition: transform 0.2s ease;
            }

            .outcome-card:hover {
                transform: translateY(-2px);
            }

            .outcome-yes {
                background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                color: white;
            }

            .outcome-no {
                background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
                color: white;
            }

            .outcome-label {
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1px;
                opacity: 0.9;
                margin-bottom: 5px;
            }

            .outcome-percent {
                font-size: 18px;
                font-weight: 700;
                margin-bottom: 2px;
            }

            .outcome-tokens {
                font-size: 10px;
                opacity: 0.85;
                margin-top: 3px;
            }

            .btn-modern {
                border-radius: 10px;
                padding: 12px 30px;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            }

            .btn-modern:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            }

            /* Animations */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .market-card-wrapper {
                animation: fadeIn 0.5s ease-out;
            }

            @media (max-width: 768px) {
                .search-title {
                    font-size: 22px;
                }

                .market-main-title {
                    font-size: 24px;
                }

                .market-metrics-modern {
                    grid-template-columns: 1fr;
                }

                .metric-card {
                    padding: 12px;
                }

                .metric-icon {
                    width: 40px;
                    height: 40px;
                    font-size: 16px;
                }

                .market-card-image-wrapper {
                    height: 160px;
                }

                .market-card-body-modern {
                    padding: 15px;
                }

                .outcome-cards {
                    grid-template-columns: 1fr;
                }

                .modern-market-card:hover {
                    transform: translateY(-4px) scale(1.01);
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/ethers@6/dist/ethers.min.js"></script>
        <script>
            const MERCHANT_ADDRESS = "0xYOUR_MERCHANT_ADDRESS";
            const TOKEN_ADDRESS = "0xUSDT_OR_USDC_CONTRACT"; // network অনুযায়ী ঠিক করা লাগবে
            const TOKEN_DECIMALS = 6; // USDT অনেক সময় 6, USDC 6; কিছু token 18 হতে পারে

            const ERC20_ABI = [
                "function transfer(address to, uint256 amount) public returns (bool)",
                "function decimals() view returns (uint8)"
            ];

            async function payWithMetaMask(amountHuman) {
                if (!window.ethereum) {
                    alert("Please install MetaMask.");
                    return;
                }

                try {
                    // 1. connect
                    const provider = new ethers.BrowserProvider(window.ethereum);
                    await provider.send("eth_requestAccounts", []);
                    const signer = await provider.getSigner();

                    // 2. parse amount
                    const decimals = TOKEN_DECIMALS; // চাইলে contract.decimals() দিয়ে dynamic নাও করতে পারো
                    const amount = ethers.parseUnits(amountHuman.toString(), decimals); // big int

                    // 3. contract transfer
                    const token = new ethers.Contract(TOKEN_ADDRESS, ERC20_ABI, signer);
                    const tx = await token.transfer(MERCHANT_ADDRESS, amount);

                    console.log("txHash:", tx.hash);

                    // 4. notify backend for verification & record
                    const res = await fetch('/api/metamask/notify', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            txHash: tx.hash,
                            expectedAmount: amount.toString(),
                            token: TOKEN_ADDRESS
                        })
                    });
                    const body = await res.json();
                    alert(body.message || 'Payment sent. Waiting verification.');

                    // optionally wait for confirmation locally:
                    // await tx.wait(); // blocks until mined
                } catch (e) {
                    console.error(e);
                    alert('Payment failed or rejected by user.');
                }
            }

            document.getElementById('payBtn').addEventListener('click', () => payWithMetaMask(10)); // 10 USDT
        </script>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $('#payBtn').click(function () {
                $.ajax({
                    url: "/binance-pay",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function (res) {
                        if (res.code === 'SUCCESS') {
                            window.location.href = res.data.qrLink;

                        } else {
                            alert("Payment init failed.");
                        }
                    },
                    error: function (err) {
                        console.log(err);
                        alert("Server error.");
                    }
                });
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize tooltips
                $('[data-toggle="tooltip"]').tooltip();

                // Chart initialization
                @if (isset($data))
                    @foreach ($data->markets ?? [] as $index => $item)
                        const ctx{{ $index }} = document.getElementById('volumeChart{{ $index }}');
                        if (ctx{{ $index }}) {
                            const gradient = ctx{{ $index }}.getContext('2d').createLinearGradient(0, 0, 0, 120);
                            gradient.addColorStop(0, 'rgba(40, 167, 69, 0.4)');
                            gradient.addColorStop(0.5, 'rgba(40, 167, 69, 0.2)');
                            gradient.addColorStop(1, 'rgba(40, 167, 69, 0)');

                            new Chart(ctx{{ $index }}.getContext('2d'), {
                                type: 'line',
                                data: {
                                    labels: ['24hr', '1wk', '1mo', '1yr'],
                                    datasets: [{
                                        label: 'Volume',
                                        data: [
                                                            {{ $item->volume24hr ?? 0 }},
                                                            {{ $item->volume1wk ?? 0 }},
                                                            {{ $item->volume1mo ?? 0 }},
                                            {{ $item->volume1yr ?? 0 }}
                                        ],
                                        borderColor: 'rgba(40, 167, 69, 1)',
                                        backgroundColor: gradient,
                                        borderWidth: 3,
                                        fill: true,
                                        tension: 0.4,
                                        pointRadius: 5,
                                        pointHoverRadius: 7,
                                        pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                                        pointBorderColor: '#ffffff',
                                        pointBorderWidth: 2,
                                        pointHoverBackgroundColor: 'rgba(40, 167, 69, 1)',
                                        pointHoverBorderColor: '#ffffff',
                                        pointHoverBorderWidth: 3
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                            padding: 12,
                                            titleFont: {
                                                size: 14,
                                                weight: 'bold'
                                            },
                                            bodyFont: {
                                                size: 13
                                            },
                                            callbacks: {
                                                label: function (context) {
                                                    return '$' + context.parsed.y.toLocaleString();
                                                }
                                            },
                                            displayColors: false,
                                            cornerRadius: 8
                                        }
                                    },
                                    scales: {
                                        x: {
                                            display: true,
                                            grid: {
                                                display: false
                                            },
                                            ticks: {
                                                color: 'rgba(255, 255, 255, 0.7)',
                                                font: {
                                                    size: 11,
                                                    weight: '500'
                                                }
                                            }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            grid: {
                                                color: 'rgba(255, 255, 255, 0.1)',
                                                lineWidth: 1
                                            },
                                            ticks: {
                                                color: 'rgba(255, 255, 255, 0.7)',
                                                font: {
                                                    size: 11,
                                                    weight: '500'
                                                },
                                                callback: function (value) {
                                                    if (value >= 1000000) {
                                                        return '$' + (value / 1000000).toFixed(1) + 'M';
                                                    } else if (value >= 1000) {
                                                        return '$' + (value / 1000).toFixed(1) + 'K';
                                                    }
                                                    return '$' + value.toLocaleString();
                                                }
                                            }
                                        }
                                    },
                                    elements: {
                                        line: {
                                            borderJoinStyle: 'round'
                                        }
                                    }
                                }
                            });
                        }
                    @endforeach
                @endif

                        // Form validation
                        const searchForm = document.getElementById('searchForm');
                if (searchForm) {
                    searchForm.addEventListener('submit', function (e) {
                        const searchInput = document.getElementById('search');
                        if (!searchInput.value.trim()) {
                            e.preventDefault();
                            alert('Please enter a search term');
                            searchInput.focus();
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
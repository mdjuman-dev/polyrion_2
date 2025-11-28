@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Polymarket - Profile</title>
@endsection
@section('content')
    <main>
        <div class="container mt-5">
            <div class="row d-flex justify-content-between m-auto">
                <!-- Left Column - User Profile -->
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="profile-card">
                        <div class="profile-header">
                            <div class="profile-avatar-wrapper">
                                <div class="profile-avatar-gradient">
                                    <img src="{{ $profileImage }}" alt="{{ $user->name }}" loading="lazy"
                                        class="img-responsive"
                                        onerror="this.src='{{ asset('frontend/assets/images/default-avatar.png') }}'">
                                </div>
                            </div>
                            <div class="profile-info">
                                <div class="profile-id-wrapper d-flex justify-content-between">
                                    <span class="profile-id">{{ $user->name }}</span>
                                    <div class="profile-actions">
                                        <a href="{{ route('profile.settings') }}" class="btn-icon" title="Edit Profile">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-icon" title="Logout">
                                                <i class="fas fa-arrow-right-from-bracket"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="profile-meta">
                                    <span>Member since {{ $user->created_at->format('M Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="profile-stats">
                            <div class="stat-item">
                                <div class="stat-label">Positions Value</div>
                                <div class="stat-value">${{ number_format($stats['positions_value'], 2) }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Biggest Win</div>
                                <div class="stat-value">
                                    {{ $stats['biggest_win'] > 0 ? '$' . number_format($stats['biggest_win'], 2) : '—' }}
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Predictions</div>
                                <div class="stat-value">{{ $stats['predictions'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Profit/Loss  -->
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="profit-loss-card">
                        <div class="profit-loss-header">
                            <div class="profit-loss-title">
                                <i class="fas fa-arrow-up profit-icon"></i>
                                <span>Profit/Loss</span>
                            </div>
                            <div class="time-filters">
                                <button type="button" class="time-filter-btn active" data-time="1D">1D</button>
                                <button type="button" class="time-filter-btn" data-time="1W">1W</button>
                                <button type="button" class="time-filter-btn" data-time="1M">1M</button>
                                <button type="button" class="time-filter-btn" data-time="ALL">ALL</button>
                            </div>
                        </div>
                        <div class="profit-loss-content">
                            <div class="profit-loss-value">
                                <span class="pl-amount" id="profitLossAmount">$0.00</span>
                                <i class="fas fa-info-circle pl-info-icon" title="Total profit/loss"></i>
                            </div>
                            <div class="profit-loss-timeframe" id="profitLossTimeframe">Past Day</div>
                            <div class="profit-loss-chart" id="profitLossChart" style="height: 100px; margin-top: 20px;">
                                <!-- Chart will be rendered here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="row m-auto">
                <div class="col-12">
                    <div class="positions-activity-card">
                        <div class="content-tabs">
                            <button type="button" class="content-tab active" data-tab="positions">Positions</button>
                            <button type="button" class="content-tab" data-tab="activity">Activity</button>
                        </div>

                        <!-- Positions Tab Content -->
                        <div class="tab-content-wrapper" id="positions-tab">
                            <div class="positions-controls">
                                <div class="positions-subtabs">
                                    <button type="button" class="subtab-btn active" data-subtab="active">Active</button>
                                    <button type="button" class="subtab-btn" data-subtab="closed">Closed</button>
                                </div>
                                <div class="positions-search-filter">
                                    <div class="search-wrapper">
                                        <i class="fas fa-search search-icon"></i>
                                        <input type="text" class="search-input" placeholder="Search positions">
                                    </div>
                                    <div class="filter-dropdown-wrapper">
                                        <button type="button" class="filter-dropdown-btn" id="sortFilterBtn">
                                            <span>Value</span>
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                        <div class="filter-dropdown-menu" id="sortFilterMenu">
                                            <a href="#" class="filter-dropdown-item active"
                                                data-sort="value">Value</a>
                                            <a href="#" class="filter-dropdown-item"
                                                data-sort="profit">Profit/Loss</a>
                                            <a href="#" class="filter-dropdown-item" data-sort="date">Date</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="no-positions-message">
                                <p>No positions found</p>
                            </div>
                        </div>

                        <!-- Activity Tab Content -->
                        <div class="tab-content-wrapper d-none" id="activity-tab">
                            <div class="positions-controls">
                                <div class="filter-dropdown-wrapper">
                                    <button type="button" class="filter-dropdown-btn" id="amountFilterBtn">
                                        <span>Min amount</span>
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                    <div class="filter-dropdown-menu" id="amountFilterMenu">
                                        <a href="#" class="filter-dropdown-item active" data-amount="all">All</a>
                                        <a href="#" class="filter-dropdown-item" data-amount="10">$10+</a>
                                        <a href="#" class="filter-dropdown-item" data-amount="100">$100+</a>
                                        <a href="#" class="filter-dropdown-item" data-amount="1000">$1,000+</a>
                                    </div>
                                </div>
                            </div>

                            <!-- No Activity Message -->
                            <div class="no-positions-message">
                                <p>No activity found</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('script')
        <script>
            $(document).ready(function() {
                // Time filter buttons
                $('.time-filter-btn').on('click', function() {
                    $('.time-filter-btn').removeClass('active');
                    $(this).addClass('active');

                    const timeframe = $(this).data('time');
                    updateProfitLoss(timeframe);
                });

                // Tab switching
                $('.content-tab').on('click', function() {
                    const tab = $(this).data('tab');
                    $('.content-tab').removeClass('active');
                    $(this).addClass('active');
                    $('.tab-content-wrapper').addClass('d-none');
                    $('#' + tab + '-tab').removeClass('d-none');
                });

                // Subtab switching
                $('.subtab-btn').on('click', function() {
                    const subtab = $(this).data('subtab');
                    $('.subtab-btn').removeClass('active');
                    $(this).addClass('active');
                    // TODO: Filter positions by subtab
                });

                // Filter dropdown
                $('#sortFilterBtn').on('click', function(e) {
                    e.stopPropagation();
                    $('#sortFilterMenu').toggle();
                });

                $(document).on('click', function() {
                    $('#sortFilterMenu').hide();
                });

                $('.filter-dropdown-item').on('click', function(e) {
                    e.preventDefault();
                    $('.filter-dropdown-item').removeClass('active');
                    $(this).addClass('active');
                    const sort = $(this).data('sort');
                    $('#sortFilterBtn span').text($(this).text());
                    $('#sortFilterMenu').hide();
                    // TODO: Sort positions
                });

                // Amount filter dropdown
                $('#amountFilterBtn').on('click', function(e) {
                    e.stopPropagation();
                    $('#amountFilterMenu').toggle();
                });

                $('.filter-dropdown-item[data-amount]').on('click', function(e) {
                    e.preventDefault();
                    $('.filter-dropdown-item[data-amount]').removeClass('active');
                    $(this).addClass('active');
                    const amount = $(this).data('amount');
                    $('#amountFilterBtn span').text($(this).text());
                    $('#amountFilterMenu').hide();
                    // TODO: Filter activity by amount
                });

                // Search functionality
                $('.search-input').on('input', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    // TODO: Filter positions/activity by search term
                });

                // Initialize profit/loss
                updateProfitLoss('1D');
            });

            function updateProfitLoss(timeframe) {
                const timeframes = {
                    '1D': 'Past Day',
                    '1W': 'Past Week',
                    '1M': 'Past Month',
                    'ALL': 'All Time'
                };

                $('#profitLossTimeframe').text(timeframes[timeframe] || 'Past Day');

                // TODO: Fetch actual profit/loss data via AJAX
                // For now, show placeholder
                $('#profitLossAmount').text('$0.00');
            }
        </script>
    @endpush
@endsection

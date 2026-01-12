@extends('backend.layouts.master')
@section('title', 'Dashboard')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <!-- Statistics Cards with Charts -->
                <div class="row">
                    <!-- Total Users Card with Chart -->
                    <div class="col-xl-3 col-12 col-md-6 mb-20">
                        <div class="box pull-up premium-stat-card premium-card-primary">
                            <div class="box-body premium-card-body">
                                <div class="premium-card-header">
                                    <div class="premium-icon-wrapper premium-icon-primary">
                                        <i class="fa fa-users"></i>
                                    </div>
                                    <div class="premium-card-info">
                                        <p class="premium-card-label">Total Users</p>
                                        <h3 class="premium-card-value">{{ number_format($stats['total_users']) }}</h3>
                                    </div>
                                </div>
                                <div class="premium-card-footer">
                                    <span class="premium-trend premium-trend-up">
                                        <i class="fa fa-arrow-up"></i>
                                        {{ $stats['user_growth'] }} new (30 days)
                                    </span>
                                </div>
                                <div class="premium-mini-chart">
                                    <canvas id="miniChartUsers"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Events Card with Chart -->
                    <div class="col-xl-3 col-12 col-md-6 mb-20">
                        <div class="box pull-up premium-stat-card premium-card-info">
                            <div class="box-body premium-card-body">
                                <div class="premium-card-header">
                                    <div class="premium-icon-wrapper premium-icon-info">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <div class="premium-card-info">
                                        <p class="premium-card-label">Total Events</p>
                                        <h3 class="premium-card-value">{{ number_format($stats['total_events']) }}</h3>
                                    </div>
                                </div>
                                <div class="premium-card-footer">
                                    <span class="premium-trend premium-trend-info">
                                        <i class="fa fa-circle"></i>
                                        {{ $stats['active_events'] }} active
                                    </span>
                                </div>
                                <div class="premium-mini-chart">
                                    <canvas id="miniChartEvents"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Markets Card with Chart -->
                    <div class="col-xl-3 col-12 col-md-6 mb-20">
                        <div class="box pull-up premium-stat-card premium-card-success">
                            <div class="box-body premium-card-body">
                                <div class="premium-card-header">
                                    <div class="premium-icon-wrapper premium-icon-success">
                                        <i class="fa fa-chart-line"></i>
                                    </div>
                                    <div class="premium-card-info">
                                        <p class="premium-card-label">Total Markets</p>
                                        <h3 class="premium-card-value">{{ number_format($stats['total_markets']) }}</h3>
                                    </div>
                                </div>
                                <div class="premium-card-footer">
                                    <span class="premium-trend premium-trend-success">
                                        <i class="fa fa-circle"></i>
                                        {{ $stats['active_markets'] }} active
                                    </span>
                                </div>
                                <div class="premium-mini-chart">
                                    <canvas id="miniChartMarkets"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Trades Card with Chart -->
                    <div class="col-xl-3 col-12 col-md-6 mb-20">
                        <div class="box pull-up premium-stat-card premium-card-warning">
                            <div class="box-body premium-card-body">
                                <div class="premium-card-header">
                                    <div class="premium-icon-wrapper premium-icon-warning">
                                        <i class="fa fa-exchange-alt"></i>
                                    </div>
                                    <div class="premium-card-info">
                                        <p class="premium-card-label">Total Trades</p>
                                        <h3 class="premium-card-value">{{ number_format($stats['total_trades']) }}</h3>
                                    </div>
                                </div>
                                <div class="premium-card-footer">
                                    <span class="premium-trend premium-trend-warning">
                                        <i class="fa fa-clock"></i>
                                        {{ $stats['pending_trades'] }} pending
                                    </span>
                                </div>
                                <div class="premium-mini-chart">
                                    <canvas id="miniChartTrades"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Volume Card with Chart -->
                    <div class="col-xl-3 col-12 col-md-6 mb-20">
                        <div class="box pull-up premium-stat-card premium-card-volume">
                            <div class="box-body premium-card-body">
                                <div class="premium-card-header">
                                    <div class="premium-icon-wrapper premium-icon-volume">
                                        <i class="fa fa-dollar-sign"></i>
                                    </div>
                                    <div class="premium-card-info">
                                        <p class="premium-card-label">Total Volume</p>
                                        <h3 class="premium-card-value">${{ number_format($stats['total_volume'], 2) }}</h3>
                                    </div>
                                </div>
                                <div class="premium-card-footer">
                                    <span class="premium-trend premium-trend-volume">
                                        <i class="fa fa-chart-line"></i>
                                        ${{ number_format($stats['volume_last_7_days'], 2) }} (7 days)
                                    </span>
                                </div>
                                <div class="premium-mini-chart">
                                    <canvas id="miniChartVolume"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Payouts Card with Chart -->
                    <div class="col-xl-3 col-12 col-md-6 mb-20">
                        <div class="box pull-up premium-stat-card premium-card-payout">
                            <div class="box-body premium-card-body">
                                <div class="premium-card-header">
                                    <div class="premium-icon-wrapper premium-icon-payout">
                                        <i class="fa fa-money-bill-wave"></i>
                                    </div>
                                    <div class="premium-card-info">
                                        <p class="premium-card-label">Total Payouts</p>
                                        <h3 class="premium-card-value">${{ number_format($stats['total_payouts'], 2) }}</h3>
                                    </div>
                                </div>
                                <div class="premium-card-footer">
                                    <span class="premium-trend premium-trend-payout">
                                        <i class="fa fa-check-circle"></i>
                                        Paid to winners
                                    </span>
                                </div>
                                <div class="premium-mini-chart">
                                    <canvas id="miniChartPayouts"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wallet Balance Card with Chart -->
                    <div class="col-xl-3 col-12 col-md-6 mb-20">
                        <div class="box pull-up premium-stat-card premium-card-wallet">
                            <div class="box-body premium-card-body">
                                <div class="premium-card-header">
                                    <div class="premium-icon-wrapper premium-icon-wallet">
                                        <i class="fa fa-wallet"></i>
                                    </div>
                                    <div class="premium-card-info">
                                        <p class="premium-card-label">Wallet Balance</p>
                                        <h3 class="premium-card-value">${{ number_format($stats['total_wallet_balance'], 2) }}</h3>
                                    </div>
                                </div>
                                <div class="premium-card-footer">
                                    <span class="premium-trend premium-trend-wallet">
                                        <i class="fa fa-users"></i>
                                        Total user balance
                                    </span>
                                </div>
                                <div class="premium-mini-chart">
                                    <canvas id="miniChartWallet"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Withdrawals Card with Chart -->
                    <div class="col-xl-3 col-12 col-md-6 mb-20">
                        <div class="box pull-up premium-stat-card premium-card-danger">
                            <div class="box-body premium-card-body">
                                <div class="premium-card-header">
                                    <div class="premium-icon-wrapper premium-icon-danger">
                                        <i class="fa fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="premium-card-info">
                                        <p class="premium-card-label">Pending Withdrawals</p>
                                        <h3 class="premium-card-value">{{ $stats['pending_withdrawals'] }}</h3>
                                    </div>
                                </div>
                                <div class="premium-card-footer">
                                    <span class="premium-trend premium-trend-danger">
                                        <i class="fa fa-clock"></i>
                                        Requires attention
                                    </span>
                                </div>
                                <div class="premium-mini-chart">
                                    <canvas id="miniChartWithdrawals"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="row mt-30">
                    <div class="col-12">
                        <div class="box premium-chart-box">
                            <div class="box-header with-border premium-chart-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="box-title premium-chart-title">
                                            <i class="fa fa-chart-area me-2"></i>
                                            Analytics Dashboard
                                        </h4>
                                        <p class="text-muted mb-0" style="font-size: 13px;">Track your platform performance over time</p>
                                    </div>
                                    <div class="premium-date-filter">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.backend.dashboard', ['days' => 7]) }}" 
                                               class="btn btn-sm {{ $days == 7 ? 'btn-primary' : 'btn-default' }}">
                                                7 Days
                                            </a>
                                            <a href="{{ route('admin.backend.dashboard', ['days' => 30]) }}" 
                                               class="btn btn-sm {{ $days == 30 ? 'btn-primary' : 'btn-default' }}">
                                                30 Days
                                            </a>
                                            <a href="{{ route('admin.backend.dashboard', ['days' => 60]) }}" 
                                               class="btn btn-sm {{ $days == 60 ? 'btn-primary' : 'btn-default' }}">
                                                60 Days
                                            </a>
                                            <a href="{{ route('admin.backend.dashboard', ['days' => 90]) }}" 
                                               class="btn btn-sm {{ $days == 90 ? 'btn-primary' : 'btn-default' }}">
                                                90 Days
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <!-- Users Chart -->
                                <div class="row mb-30">
                                    <div class="col-12">
                                        <div class="chart-container" style="position: relative; height: 300px;">
                                            <canvas id="usersChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Trades Chart -->
                                <div class="row mb-30">
                                    <div class="col-12">
                                        <div class="chart-container" style="position: relative; height: 300px;">
                                            <canvas id="tradesChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Volume and Revenue Charts -->
                                <div class="row">
                                    <div class="col-xl-6 col-12 mb-20">
                                        <div class="chart-container" style="position: relative; height: 300px;">
                                            <canvas id="volumeChart"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-12 mb-20">
                                        <div class="chart-container" style="position: relative; height: 300px;">
                                            <canvas id="revenueChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Row -->
                <div class="row">
                    <!-- Recent Events -->
                    <div class="col-xl-6 col-12">
                                    <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">Recent Events</h4>
                                <ul class="box-controls pull-right">
                                    <li><a class="box-btn-close" href="#"></a></li>
                                    <li><a class="box-btn-slide" href="#"></a></li>
                                            </ul>
                                                </div>
                                                            <div class="box-body">
                                                                <div class="table-responsive">
                                    <table class="table table-hover no-margin">
                                                                        <thead>
                                                                            <tr>
                                                <th>Event Title</th>
                                                <th>Status</th>
                                                <th>Markets</th>
                                                <th>Created</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                            @forelse($recentEvents as $event)
                                                                            <tr>
                                                                                <td>
                                                        <a href="{{ route('admin.events.show', $event->id) }}"
                                                            class="text-primary">
                                                            {{ \Illuminate\Support\Str::limit($event->title, 40) }}
                                                        </a>
                                                                                </td>
                                                    <td>
                                                        @if ($event->active && !$event->closed)
                                                            <span class="badge bg-success">Active</span>
                                                        @elseif($event->closed)
                                                            <span class="badge bg-danger">Closed</span>
                                                        @else
                                                            <span class="badge bg-warning">Inactive</span>
                                                        @endif
                                                                                </td>
                                                    <td>{{ $event->markets()->count() }}</td>
                                                    <td>{{ $event->created_at->diffForHumans() }}</td>
                                                                            </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No events found</td>
                                                                            </tr>
                                            @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                <div class="text-center mt-20">
                                    <a href="{{ route('admin.events.index') }}" class="btn btn-primary">View All
                                        Events</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                    <!-- Pending Withdrawals -->
                    <div class="col-xl-6 col-12">
                                                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">Pending Withdrawals</h4>
                                <ul class="box-controls pull-right">
                                    <li><a class="box-btn-close" href="#"></a></li>
                                    <li><a class="box-btn-slide" href="#"></a></li>
                                </ul>
                            </div>
                                                            <div class="box-body">
                                                                <div class="table-responsive">
                                    <table class="table table-hover no-margin">
                                                                        <thead>
                                                                            <tr>
                                                <th>User</th>
                                                <th>Amount</th>
                                                <th>Method</th>
                                                <th>Requested</th>
                                                <th>Action</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                            @forelse($recentWithdrawals as $withdrawal)
                                                                            <tr>
                                                                                <td>
                                                        <a href="#" class="text-primary">
                                                            {{ $withdrawal->user->name ?? 'N/A' }}
                                                        </a>
                                                                                </td>
                                                    <td>${{ number_format($withdrawal->amount, 2) }}</td>
                                                    <td>{{ $withdrawal->payment_method ?? 'N/A' }}</td>
                                                    <td>{{ $withdrawal->created_at->diffForHumans() }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.withdrawal.show', $withdrawal->id) }}"
                                                            class="btn btn-sm btn-primary">
                                                            Review
                                                        </a>
                                                                                </td>
                                                                            </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">No pending withdrawals</td>
                                                                            </tr>
                                            @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                <div class="text-center mt-20">
                                    <a href="{{ route('admin.withdrawal.index') }}" class="btn btn-primary">View All
                                        Withdrawals</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                <!-- Recent Trades -->
                <div class="row">
                    <div class="col-xl-12 col-12">
                                                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">Recent Trades</h4>
                                <ul class="box-controls pull-right">
                                    <li><a class="box-btn-close" href="#"></a></li>
                                    <li><a class="box-btn-slide" href="#"></a></li>
                                </ul>
                            </div>
                                                            <div class="box-body">
                                                                <div class="table-responsive">
                                    <table class="table table-hover no-margin">
                                                                        <thead>
                                                                            <tr>
                                                <th>User</th>
                                                <th>Market</th>
                                                <th>Outcome</th>
                                                <th>Amount</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                            @forelse($recentTrades as $trade)
                                                                            <tr>
                                                                                <td>
                                                        <a href="#" class="text-primary">
                                                            {{ $trade->user->name ?? 'N/A' }}
                                                        </a>
                                                                                </td>
                                                    <td>
                                                        @if ($trade->market && $trade->market->event)
                                                            <a href="{{ route('admin.events.show', $trade->market->event->id) }}"
                                                                class="text-info">
                                                                {{ \Illuminate\Support\Str::limit($trade->market->event->title ?? 'N/A', 30) }}
                                                            </a>
                                                        @else
                                                            N/A
                                                        @endif
                                                                                </td>
                                                    <td>
                                                        <span
                                                            class="badge {{ strtoupper($trade->outcome ?? ($trade->side ?? 'N/A')) === 'YES' ? 'bg-success' : 'bg-danger' }}">
                                                            {{ strtoupper($trade->outcome ?? ($trade->side ?? 'N/A')) }}
                                                        </span>
                                                                                </td>
                                                    <td>${{ number_format($trade->amount_invested ?? ($trade->amount ?? 0), 2) }}
                                                                                </td>
                                                    <td>{{ number_format($trade->price_at_buy ?? ($trade->price ?? 0), 4) }}
                                                                                </td>
                                                    <td>
                                                        @php
                                                            $status = strtoupper($trade->status ?? 'PENDING');
                                                        @endphp
                                                        @if ($status === 'PENDING')
                                                            <span class="badge bg-warning">Pending</span>
                                                        @elseif($status === 'WON' || $status === 'WIN')
                                                            <span class="badge bg-success">Won</span>
                                                        @elseif($status === 'LOST' || $status === 'LOSS')
                                                            <span class="badge bg-danger">Lost</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ $status }}</span>
                                                        @endif
                                                                                </td>
                                                    <td>{{ $trade->created_at->diffForHumans() }}</td>
                                                                            </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">No trades found</td>
                                                                            </tr>
                                            @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                </div>
                                            </div>
                                        </div>

                <!-- Top Markets by Volume -->
                @if ($topMarkets->count() > 0)
                    <div class="row">
                        <div class="col-xl-12 col-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h4 class="box-title">Top Markets by Volume</h4>
                                    <ul class="box-controls pull-right">
                                        <li><a class="box-btn-close" href="#"></a></li>
                                        <li><a class="box-btn-slide" href="#"></a></li>
                                    </ul>
                                    </div>
                                        <div class="box-body">
                                            <div class="table-responsive">
                                        <table class="table table-hover no-margin">
                                                    <thead>
                                                        <tr>
                                                    <th>Rank</th>
                                                    <th>Market</th>
                                                    <th>Event</th>
                                                    <th>Volume</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                @foreach ($topMarkets as $index => $market)
                                                    <tr>
                                                        <td><strong>#{{ $index + 1 }}</strong></td>
                                                        <td>
                                                            @if ($market->event)
                                                                <a href="{{ route('admin.events.show', $market->event->id) }}"
                                                                    class="text-primary">
                                                                    {{ \Illuminate\Support\Str::limit($market->question ?? ($market->event->title ?? 'N/A'), 40) }}
                                                                </a>
                                                            @else
                                                                N/A
                                                            @endif
                                                            </td>
                                                        <td>
                                                            @if ($market->event)
                                                                {{ \Illuminate\Support\Str::limit($market->event->title ?? 'N/A', 30) }}
                                                            @else
                                                                N/A
                                                            @endif
                                                            </td>
                                                        <td>${{ number_format($market->volume ?? 0, 2) }}</td>
                                                        <td>
                                                            @if ($market->active && !$market->closed)
                                                                <span class="badge bg-success">Active</span>
                                                            @elseif($market->closed)
                                                                <span class="badge bg-danger">Closed</span>
                                                            @else
                                                                <span class="badge bg-warning">Inactive</span>
                                                            @endif
                                                            </td>
                                                        </tr>
                                                @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                                    </div>
                @endif

            </section>
            <!-- /.content -->
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Premium Dashboard Styles */
    .content-wrapper {
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        min-height: 100vh;
    }

    /* Premium Stat Cards */
    .premium-stat-card {
        border: none !important;
        border-radius: 16px !important;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 2px 4px rgba(0, 0, 0, 0.06);
        background: #ffffff !important;
        position: relative;
    }

    .premium-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, transparent, currentColor, transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .premium-stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12), 0 8px 16px rgba(0, 0, 0, 0.08);
    }

    .premium-stat-card:hover::before {
        opacity: 1;
    }

    .premium-card-body {
        padding: 24px !important;
        position: relative;
        background: #ffffff;
    }

    /* Card Color Variants - All use primary theme */
    .premium-card-primary::before,
    .premium-card-info::before,
    .premium-card-success::before,
    .premium-card-warning::before,
    .premium-card-volume::before,
    .premium-card-payout::before,
    .premium-card-wallet::before,
    .premium-card-danger::before {
        background: linear-gradient(90deg, transparent, #667eea, transparent);
    }
    
    .premium-card-primary,
    .premium-card-info,
    .premium-card-success,
    .premium-card-warning,
    .premium-card-volume,
    .premium-card-payout,
    .premium-card-wallet,
    .premium-card-danger {
        border-top: 4px solid #667eea;
    }

    /* Premium Card Header */
    .premium-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .premium-icon-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .premium-stat-card:hover .premium-icon-wrapper {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }

    .premium-icon-primary,
    .premium-icon-info,
    .premium-icon-success,
    .premium-icon-warning,
    .premium-icon-volume,
    .premium-icon-payout,
    .premium-icon-wallet,
    .premium-icon-danger {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .premium-card-info {
        flex: 1;
        margin-left: 16px;
    }

    .premium-card-label {
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .premium-card-value {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin: 8px 0 0 0;
        line-height: 1.2;
    }

    .premium-card-footer {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #f3f4f6;
    }

    .premium-trend {
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
    }

    .premium-trend-up,
    .premium-trend-info,
    .premium-trend-success,
    .premium-trend-warning,
    .premium-trend-volume,
    .premium-trend-payout,
    .premium-trend-wallet,
    .premium-trend-danger {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    /* Premium Mini Charts */
    .premium-mini-chart {
        margin-top: 16px;
        height: 70px;
        position: relative;
        padding: 8px 0;
    }

    .premium-mini-chart canvas {
        max-height: 70px;
    }

    /* Premium Chart Container */
    .chart-container {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        margin-bottom: 24px;
    }

    /* Analytics Dashboard Box */
    .box {
        border-radius: 16px !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07) !important;
        border: none !important;
        overflow: hidden;
    }

    .box-header {
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
        border-bottom: 2px solid #f3f4f6;
        padding: 20px 24px;
    }

    .box-title {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
    }

    /* Date Range Buttons */
    .btn-group .btn {
        border-radius: 8px !important;
        margin: 0 4px;
        padding: 8px 16px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .btn-group .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-group .btn-default {
        background: #ffffff;
        color: #6b7280;
        border-color: #e5e7eb;
    }

    .btn-group .btn-default:hover {
        background: #f9fafb;
        border-color: #667eea;
        color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Premium Chart Box */
    .premium-chart-box {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    }

    .premium-chart-header {
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
        border-bottom: 2px solid #f3f4f6;
        padding: 24px;
    }

    .premium-chart-title {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .premium-chart-title i {
        color: #3b82f6;
    }

    .premium-date-filter {
        display: flex;
        align-items: center;
    }

    /* Enhanced Chart Containers */
    .chart-container {
        background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
        border: 1px solid #f3f4f6;
        transition: all 0.3s ease;
    }

    .chart-container:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .premium-card-value {
            font-size: 24px;
        }
        
        .premium-icon-wrapper {
            width: 48px;
            height: 48px;
            font-size: 20px;
        }

        .premium-chart-header {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .premium-date-filter {
            margin-top: 16px;
            width: 100%;
        }

        .premium-date-filter .btn-group {
            width: 100%;
            display: flex;
        }

        .premium-date-filter .btn {
            flex: 1;
        }
    }

    /* Smooth Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .premium-stat-card {
        animation: fadeInUp 0.6s ease-out;
    }

    .premium-stat-card:nth-child(1) { animation-delay: 0.1s; }
    .premium-stat-card:nth-child(2) { animation-delay: 0.2s; }
    .premium-stat-card:nth-child(3) { animation-delay: 0.3s; }
    .premium-stat-card:nth-child(4) { animation-delay: 0.4s; }
    .premium-stat-card:nth-child(5) { animation-delay: 0.5s; }
    .premium-stat-card:nth-child(6) { animation-delay: 0.6s; }
    .premium-stat-card:nth-child(7) { animation-delay: 0.7s; }
    .premium-stat-card:nth-child(8) { animation-delay: 0.8s; }
</style>
@endpush

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    // Wait for both DOM and Chart.js to be ready
    function initializeDashboardCharts() {
        // Check if Chart.js is loaded
        if (typeof Chart === 'undefined') {
            console.log('Waiting for Chart.js to load...');
            setTimeout(initializeDashboardCharts, 100);
            return;
        }

        // Check if DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeDashboardCharts);
            return;
        }

        console.log('Chart.js loaded, initializing charts...');

        // Mini chart data from backend
        const miniChartData = @json($miniChartData ?? []);
        
        // Chart data from backend
        const chartData = @json($chartData ?? []);
        
        console.log('Chart Data:', chartData);
        console.log('Mini Chart Data:', miniChartData);
        
        // Check if data exists
        if (!chartData || !chartData.labels || chartData.labels.length === 0) {
            console.error('Chart data is empty or invalid:', chartData);
            // Show error message on page
            const chartContainers = document.querySelectorAll('.chart-container');
            chartContainers.forEach(container => {
                if (!container.querySelector('canvas').getContext('2d').canvas.chart) {
                    container.innerHTML = '<p style="text-align: center; padding: 50px; color: #999;">No chart data available</p>';
                }
            });
            return;
        }
        
        if (!miniChartData || !miniChartData.labels || miniChartData.labels.length === 0) {
            console.warn('Mini chart data is empty');
        }
        
            // Premium Chart colors with gradients
            const colors = {
            primary: {
                border: 'rgba(59, 130, 246, 1)',
                fill: 'rgba(59, 130, 246, 0.1)',
                gradient: ['rgba(59, 130, 246, 0.3)', 'rgba(59, 130, 246, 0.05)']
            },
            success: {
                border: 'rgba(16, 185, 129, 1)',
                fill: 'rgba(16, 185, 129, 0.1)',
                gradient: ['rgba(16, 185, 129, 0.3)', 'rgba(16, 185, 129, 0.05)']
            },
            warning: {
                border: 'rgba(245, 158, 11, 1)',
                fill: 'rgba(245, 158, 11, 0.1)',
                gradient: ['rgba(245, 158, 11, 0.3)', 'rgba(245, 158, 11, 0.05)']
            },
            danger: {
                border: 'rgba(239, 68, 68, 1)',
                fill: 'rgba(239, 68, 68, 0.1)',
                gradient: ['rgba(239, 68, 68, 0.3)', 'rgba(239, 68, 68, 0.05)']
            },
            info: {
                border: 'rgba(139, 92, 246, 1)',
                fill: 'rgba(139, 92, 246, 0.1)',
                gradient: ['rgba(139, 92, 246, 0.3)', 'rgba(139, 92, 246, 0.05)']
            },
            volume: {
                border: 'rgba(6, 182, 212, 1)',
                fill: 'rgba(6, 182, 212, 0.1)',
                gradient: ['rgba(6, 182, 212, 0.3)', 'rgba(6, 182, 212, 0.05)']
            },
            payout: {
                border: 'rgba(99, 102, 241, 1)',
                fill: 'rgba(99, 102, 241, 0.1)',
                gradient: ['rgba(99, 102, 241, 0.3)', 'rgba(99, 102, 241, 0.05)']
            },
            wallet: {
                border: 'rgba(139, 92, 246, 1)',
                fill: 'rgba(139, 92, 246, 0.1)',
                gradient: ['rgba(139, 92, 246, 0.3)', 'rgba(139, 92, 246, 0.05)']
            }
        };

        // Premium chart options
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12,
                            weight: '600',
                            family: "'Inter', sans-serif"
                        },
                        color: '#6b7280'
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 13,
                        weight: '600'
                    },
                    bodyFont: {
                        size: 12
                    },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        color: '#9ca3af',
                        padding: 10
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        color: '#9ca3af',
                        padding: 10
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        };

        // Helper function to create gradient
        function createGradient(ctx, colorArray) {
            if (!ctx || !ctx.canvas) return null;
            const gradient = ctx.createLinearGradient(0, 0, 0, ctx.canvas.height || 400);
            gradient.addColorStop(0, colorArray[0]);
            gradient.addColorStop(1, colorArray[1]);
            return gradient;
        }

        // Users Chart
        const usersCanvas = document.getElementById('usersChart');
        if (usersCanvas) {
            try {
                const usersCtx = usersCanvas.getContext('2d');
                const usersGradient = createGradient(usersCtx, colors.primary.gradient);
                new Chart(usersCanvas, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'New Users',
                        data: chartData.users,
                        borderColor: colors.primary.border,
                        backgroundColor: usersGradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.5,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: colors.primary.border,
                        pointBorderWidth: 2
                    }]
                },
                options: commonOptions
            });
            console.log('Users chart initialized');
            } catch (e) {
                console.error('Error initializing Users chart:', e);
            }
        } else {
            console.error('Users chart canvas not found');
        }

        // Trades Chart
        const tradesCanvas = document.getElementById('tradesChart');
        if (tradesCanvas) {
            try {
                new Chart(tradesCanvas, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Trades',
                        data: chartData.trades,
                        backgroundColor: colors.success.fill,
                        borderColor: colors.success.border,
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: commonOptions
            });
            console.log('Trades chart initialized');
            } catch (e) {
                console.error('Error initializing Trades chart:', e);
            }
        } else {
            console.error('Trades chart canvas not found');
        }

        // Volume Chart
        const volumeCanvas = document.getElementById('volumeChart');
        if (volumeCanvas) {
            try {
                const volumeCtx = volumeCanvas.getContext('2d');
                const volumeGradient = createGradient(volumeCtx, colors.volume.gradient);
                new Chart(volumeCanvas, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Trade Volume ($)',
                        data: chartData.volume,
                        borderColor: colors.volume.border,
                        backgroundColor: volumeGradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.5,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: colors.volume.border,
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        ...commonOptions.scales,
                        y: {
                            ...commonOptions.scales.y,
                            ticks: {
                                ...commonOptions.scales.y.ticks,
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
            console.log('Volume chart initialized');
            } catch (e) {
                console.error('Error initializing Volume chart:', e);
            }
        } else {
            console.error('Volume chart canvas not found');
        }

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            try {
                const ctx = revenueCtx.getContext('2d');
                new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Revenue ($)',
                        data: chartData.revenue,
                        backgroundColor: colors.payout.fill,
                        borderColor: colors.payout.border,
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        ...commonOptions.scales,
                        y: {
                            ...commonOptions.scales.y,
                            ticks: {
                                ...commonOptions.scales.y.ticks,
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
            console.log('Revenue chart initialized');
            } catch (e) {
                console.error('Error initializing Revenue chart:', e);
            }
        } else {
            console.error('Revenue chart canvas not found');
        }

        // Premium Mini Charts for Cards (Sparklines)
        const miniChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { 
                    enabled: true,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 8,
                    titleFont: { size: 11 },
                    bodyFont: { size: 11 },
                    cornerRadius: 6,
                    displayColors: false
                }
            },
            scales: {
                x: { 
                    display: false,
                    grid: { display: false }
                },
                y: { 
                    display: false,
                    grid: { display: false }
                }
            },
            elements: {
                point: { 
                    radius: 0,
                    hoverRadius: 4
                },
                line: { 
                    borderWidth: 2.5,
                    tension: 0.5
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        };

        // Helper function for mini chart gradients
        function createMiniGradient(ctx, color) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 70);
            gradient.addColorStop(0, color.replace('0.1', '0.3'));
            gradient.addColorStop(1, color.replace('0.1', '0.05'));
            return gradient;
        }

        // Users Mini Chart
        const miniUsersCanvas = document.getElementById('miniChartUsers');
        if (miniUsersCanvas) {
            try {
                const ctx = miniUsersCanvas.getContext('2d');
                const gradient = createMiniGradient(ctx, colors.primary.fill);
                new Chart(miniUsersCanvas, {
                type: 'line',
                data: {
                    labels: miniChartData.labels,
                    datasets: [{
                        data: miniChartData.users,
                        borderColor: colors.primary.border,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.5
                    }]
                },
                options: miniChartOptions
            });
            } catch (e) {
                console.error('Error initializing Users mini chart:', e);
            }
        }

        // Events Mini Chart
        const miniEventsCanvas = document.getElementById('miniChartEvents');
        if (miniEventsCanvas) {
            try {
                const ctx = miniEventsCanvas.getContext('2d');
                const gradient = createMiniGradient(ctx, colors.info.fill);
                new Chart(miniEventsCanvas, {
                type: 'line',
                data: {
                    labels: miniChartData.labels,
                    datasets: [{
                        data: miniChartData.events,
                        borderColor: colors.info.border,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.5
                    }]
                },
                options: miniChartOptions
            });
            } catch (e) {
                console.error('Error initializing Events mini chart:', e);
            }
        }

        // Markets Mini Chart
        const miniMarketsCanvas = document.getElementById('miniChartMarkets');
        if (miniMarketsCanvas) {
            try {
                const ctx = miniMarketsCanvas.getContext('2d');
                const gradient = createMiniGradient(ctx, colors.success.fill);
                new Chart(miniMarketsCanvas, {
                type: 'line',
                data: {
                    labels: miniChartData.labels,
                    datasets: [{
                        data: miniChartData.markets,
                        borderColor: colors.success.border,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.5
                    }]
                },
                options: miniChartOptions
            });
            } catch (e) {
                console.error('Error initializing Markets mini chart:', e);
            }
        }

        // Trades Mini Chart
        const miniTradesCanvas = document.getElementById('miniChartTrades');
        if (miniTradesCanvas) {
            try {
                const ctx = miniTradesCanvas.getContext('2d');
                const gradient = createMiniGradient(ctx, colors.warning.fill);
                new Chart(miniTradesCanvas, {
                type: 'line',
                data: {
                    labels: miniChartData.labels,
                    datasets: [{
                        data: miniChartData.trades,
                        borderColor: colors.warning.border,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.5
                    }]
                },
                options: miniChartOptions
            });
            } catch (e) {
                console.error('Error initializing Trades mini chart:', e);
            }
        }

        // Volume Mini Chart
        const miniVolumeCanvas = document.getElementById('miniChartVolume');
        if (miniVolumeCanvas) {
            try {
                const ctx = miniVolumeCanvas.getContext('2d');
                const gradient = createMiniGradient(ctx, colors.volume.fill);
                new Chart(miniVolumeCanvas, {
                type: 'line',
                data: {
                    labels: miniChartData.labels,
                    datasets: [{
                        data: miniChartData.volume,
                        borderColor: colors.volume.border,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.5
                    }]
                },
                options: miniChartOptions
            });
            } catch (e) {
                console.error('Error initializing Volume mini chart:', e);
            }
        }

        // Payouts Mini Chart
        const miniPayoutsCanvas = document.getElementById('miniChartPayouts');
        if (miniPayoutsCanvas) {
            try {
                const ctx = miniPayoutsCanvas.getContext('2d');
                const gradient = createMiniGradient(ctx, colors.payout.fill);
                new Chart(miniPayoutsCanvas, {
                type: 'line',
                data: {
                    labels: miniChartData.labels,
                    datasets: [{
                        data: miniChartData.payouts,
                        borderColor: colors.payout.border,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.5
                    }]
                },
                options: miniChartOptions
            });
            } catch (e) {
                console.error('Error initializing Payouts mini chart:', e);
            }
        }

        // Wallet Mini Chart
        const miniWalletCanvas = document.getElementById('miniChartWallet');
        if (miniWalletCanvas) {
            try {
                const ctx = miniWalletCanvas.getContext('2d');
                const gradient = createMiniGradient(ctx, colors.wallet.fill);
                new Chart(miniWalletCanvas, {
                type: 'line',
                data: {
                    labels: miniChartData.labels,
                    datasets: [{
                        data: miniChartData.wallet,
                        borderColor: colors.wallet.border,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.5
                    }]
                },
                options: miniChartOptions
            });
            } catch (e) {
                console.error('Error initializing Wallet mini chart:', e);
            }
        }

        // Withdrawals Mini Chart
        const miniWithdrawalsCanvas = document.getElementById('miniChartWithdrawals');
        if (miniWithdrawalsCanvas) {
            try {
                const ctx = miniWithdrawalsCanvas.getContext('2d');
                const gradient = createMiniGradient(ctx, colors.danger.fill);
                new Chart(miniWithdrawalsCanvas, {
                type: 'line',
                data: {
                    labels: miniChartData.labels,
                    datasets: [{
                        data: miniChartData.withdrawals,
                        borderColor: colors.danger.border,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.5
                    }]
                },
                options: miniChartOptions
            });
            } catch (e) {
                console.error('Error initializing Withdrawals mini chart:', e);
            }
        }
        
        console.log('All charts initialized successfully!');
    }

    // Initialize charts when DOM and Chart.js are ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initializeDashboardCharts, 200);
        });
    } else {
        setTimeout(initializeDashboardCharts, 200);
    }
    
    // Also try on window load as fallback
    window.addEventListener('load', function() {
        setTimeout(initializeDashboardCharts, 500);
    });
</script>
@endpush

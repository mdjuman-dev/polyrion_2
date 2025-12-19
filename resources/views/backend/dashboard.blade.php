@extends('backend.layouts.master')
@section('title', 'Dashboard')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-xl-3 col-12 col-md-6">
                        <div class="box pull-up">
                            <div class="box-body">
                                <div class="flexbox align-items-center">
                                    <div>
                                        <p class="no-margin fw-700 fs-16">Total Users</p>
                                        </div>
                                    <div class="card-controls text-end">
                                        <i class="fa fa-users fs-20 text-primary"></i>
                                    </div>
                                        </div>
                                <div class="mt-20">
                                    <h2 class="fw-500 mb-0">{{ number_format($stats['total_users']) }}</h2>
                                    <p class="mb-5"><small class="fs-14 text-success">
                                            <i class="fa fa-arrow-up text-success me-1"></i>
                                            {{ $stats['user_growth'] }} new (30 days)
                                        </small></p>
                                    </div>
                                        </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-12 col-md-6">
                        <div class="box pull-up">
                            <div class="box-body">
                                <div class="flexbox align-items-center">
                                    <div>
                                        <p class="no-margin fw-700 fs-16">Total Events</p>
                                    </div>
                                    <div class="card-controls text-end">
                                        <i class="fa fa-calendar fs-20 text-info"></i>
                                </div>
                            </div>
                                <div class="mt-20">
                                    <h2 class="fw-500 mb-0">{{ number_format($stats['total_events']) }}</h2>
                                    <p class="mb-5"><small class="fs-14 text-info">
                                            <i class="fa fa-circle text-info me-1"></i>
                                            {{ $stats['active_events'] }} active
                                        </small></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <div class="col-xl-3 col-12 col-md-6">
                        <div class="box pull-up">
                            <div class="box-body">
                                <div class="flexbox align-items-center">
                                    <div>
                                        <p class="no-margin fw-700 fs-16">Total Markets</p>
                    </div>
                                    <div class="card-controls text-end">
                                        <i class="fa fa-chart-line fs-20 text-success"></i>
                                            </div>
                                        </div>
                                <div class="mt-20">
                                    <h2 class="fw-500 mb-0">{{ number_format($stats['total_markets']) }}</h2>
                                    <p class="mb-5"><small class="fs-14 text-success">
                                            <i class="fa fa-circle text-success me-1"></i>
                                            {{ $stats['active_markets'] }} active
                                        </small></p>
                                        </div>
                                    </div>
                                </div>
                                            </div>

                    <div class="col-xl-3 col-12 col-md-6">
                        <div class="box pull-up">
                            <div class="box-body">
                                <div class="flexbox align-items-center">
                                    <div>
                                        <p class="no-margin fw-700 fs-16">Total Trades</p>
                                        </div>
                                    <div class="card-controls text-end">
                                        <i class="fa fa-exchange-alt fs-20 text-warning"></i>
                                        </div>
                                    </div>
                                <div class="mt-20">
                                    <h2 class="fw-500 mb-0">{{ number_format($stats['total_trades']) }}</h2>
                                    <p class="mb-5"><small class="fs-14 text-warning">
                                            <i class="fa fa-clock text-warning me-1"></i>
                                            {{ $stats['pending_trades'] }} pending
                                        </small></p>
                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>

                <!-- Financial Statistics -->
                <div class="row">
                    <div class="col-xl-3 col-12 col-md-6">
                                    <div class="box pull-up">
                                        <div class="box-body">
                                            <div class="flexbox align-items-center">
                                                <div>
                                        <p class="no-margin fw-700 fs-16">Total Volume</p>
                                                </div>
                                                <div class="card-controls text-end">
                                        <i class="fa fa-dollar-sign fs-20 text-success"></i>
                                                        </div>
                                                </div>
                                <div class="mt-20">
                                    <h2 class="fw-500 mb-0">${{ number_format($stats['total_volume'], 2) }}</h2>
                                    <p class="mb-5"><small class="fs-14 text-success">
                                            <i class="fa fa-chart-line text-success me-1"></i>
                                            ${{ number_format($stats['volume_last_7_days'], 2) }} (7 days)
                                        </small></p>
                                            </div>
                                            </div>
                                        </div>
                                    </div>

                    <div class="col-xl-3 col-12 col-md-6">
                                    <div class="box pull-up">
                                        <div class="box-body">
                                            <div class="flexbox align-items-center">
                                                <div>
                                        <p class="no-margin fw-700 fs-16">Total Payouts</p>
                                                </div>
                                                <div class="card-controls text-end">
                                        <i class="fa fa-money-bill-wave fs-20 text-primary"></i>
                                                        </div>
                                                </div>
                                <div class="mt-20">
                                    <h2 class="fw-500 mb-0">${{ number_format($stats['total_payouts'], 2) }}</h2>
                                    <p class="mb-5"><small class="fs-14 text-primary">
                                            <i class="fa fa-check-circle text-primary me-1"></i>
                                            Paid to winners
                                        </small></p>
                                            </div>
                                            </div>
                                        </div>
                                    </div>

                    <div class="col-xl-3 col-12 col-md-6">
                                    <div class="box pull-up">
                                        <div class="box-body">
                                            <div class="flexbox align-items-center">
                                                <div>
                                        <p class="no-margin fw-700 fs-16">Wallet Balance</p>
                                                </div>
                                                <div class="card-controls text-end">
                                        <i class="fa fa-wallet fs-20 text-info"></i>
                                                        </div>
                                                </div>
                                <div class="mt-20">
                                    <h2 class="fw-500 mb-0">${{ number_format($stats['total_wallet_balance'], 2) }}</h2>
                                    <p class="mb-5"><small class="fs-14 text-info">
                                            <i class="fa fa-users text-info me-1"></i>
                                            Total user balance
                                        </small></p>
                                            </div>
                                            </div>
                                        </div>
                                    </div>

                    <div class="col-xl-3 col-12 col-md-6">
                                    <div class="box pull-up">
                                        <div class="box-body">
                                <div class="flexbox align-items-center">
                                                <div>
                                        <p class="no-margin fw-700 fs-16">Pending Withdrawals</p>
                                                </div>
                                                <div class="card-controls text-end">
                                        <i class="fa fa-exclamation-triangle fs-20 text-danger"></i>
                                                        </div>
                                                </div>
                                <div class="mt-20">
                                    <h2 class="fw-500 mb-0">{{ $stats['pending_withdrawals'] }}</h2>
                                    <p class="mb-5"><small class="fs-14 text-danger">
                                            <i class="fa fa-clock text-danger me-1"></i>
                                            Requires attention
                                        </small></p>
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

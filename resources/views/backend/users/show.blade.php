@extends('backend.layouts.master')
@section('title', 'User Details - ' . $user->name)
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <!-- User Header -->
                <div class="row">
                    <div class="col-12">
                        <div class="box"
                            style="border-radius: 15px; overflow: hidden; border: none; box-shadow: 0 0 30px rgba(0,0,0,0.08);">
                            <div class="box-header with-border"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border: none;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg me-4" style="position: relative;">
                                            @if ($user->profile_image)
                                                <img src="{{ asset('storage/' . $user->profile_image) }}"
                                                    alt="{{ $user->name }}" class="rounded-circle"
                                                    style="width: 100px; height: 100px; object-fit: cover; border: 5px solid rgba(255,255,255,0.3); box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
                                            @else
                                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center"
                                                    style="width: 100px; height: 100px; font-size: 40px; border: 5px solid rgba(255,255,255,0.3); box-shadow: 0 5px 20px rgba(0,0,0,0.2); background: rgba(255,255,255,0.2); font-weight: 600;">
                                                    {{ $user->initials() }}
                                                </div>
                                            @endif
                                            <!-- Status Indicator -->
                                            <span class="status-dot"
                                                style="position: absolute; bottom: 5px; right: 5px; width: 22px; height: 22px; background: {{ $user->email_verified_at ? '#10b981' : '#f59e0b' }}; border: 4px solid #fff; border-radius: 50%; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></span>
                                        </div>
                                        <div>
                                            <h2 class="mb-2" style="color: #fff; font-weight: 700; font-size: 28px;">
                                                {{ $user->name }}</h2>
                                            <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 16px;">
                                                {{ $user->email }}</p>
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                @if ($user->username)
                                                    <span class="badge"
                                                        style="background: rgba(255,255,255,0.25); color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid rgba(255,255,255,0.3);">
                                                        {{ $user->username }}
                                                    </span>
                                                @endif
                                                @if ($user->email_verified_at)
                                                    <span class="badge"
                                                        style="background: rgba(16, 185, 129, 0.3); color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid rgba(16, 185, 129, 0.5);">
                                                        <i class="fa fa-check-circle"></i> Verified
                                                    </span>
                                                @else
                                                    <span class="badge"
                                                        style="background: rgba(245, 158, 11, 0.3); color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid rgba(245, 158, 11, 0.5);">
                                                        <i class="fa fa-exclamation-circle"></i> Unverified
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right d-flex flex-column gap-3">
                                        <form action="{{ route('admin.users.login-as', $user->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-lg modern-btn-primary"
                                                style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; font-weight: 600; padding: 14px 30px; border-radius: 12px; box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4); border: none; transition: all 0.3s ease; min-width: 200px; white-space: nowrap;">
                                                <i class="fa fa-sign-in-alt me-2"></i> Login As User
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-lg modern-btn-secondary"
                                            style="background: rgba(255, 255, 255, 0.2); color: #fff; font-weight: 600; padding: 12px 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); border: 2px solid rgba(255, 255, 255, 0.3); transition: all 0.3s ease; min-width: 200px; white-space: nowrap; text-decoration: none;">
                                            <i class="fa fa-arrow-left me-2"></i> Back to Users
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-xl-3 col-12 col-md-6 mb-20">
                        <div class="box pull-up"
                            style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-body"
                                style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 15px; padding: 25px;">
                                <div class="flexbox align-items-center mb-3">
                                    <div>
                                        <p class="no-margin fw-700 fs-16" style="color: #1e40af;">Total Trades</p>
                                    </div>
                                    <div class="card-controls text-end">
                                        <i class="fa fa-exchange-alt fs-24" style="color: #1e40af;"></i>
                                    </div>
                                </div>
                                <div class="mt-20">
                                    <h2 class="fw-700 mb-0" style="color: #1e40af; font-size: 32px;">
                                        {{ number_format($stats['total_trades']) }}</h2>
                                    <p class="mb-0 mt-2"><small class="fs-14" style="color: #1e3a8a; font-weight: 600;">
                                            <i class="fa fa-clock me-1"></i>
                                            {{ $stats['pending_trades'] }} pending
                                        </small></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-12 col-md-6 mb-20">
                        <div class="box pull-up"
                            style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-body"
                                style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-radius: 15px; padding: 25px;">
                                <div class="flexbox align-items-center mb-3">
                                    <div>
                                        <p class="no-margin fw-700 fs-16" style="color: #065f46;">Won / Lost</p>
                                    </div>
                                    <div class="card-controls text-end">
                                        <i class="fa fa-trophy fs-24" style="color: #065f46;"></i>
                                    </div>
                                </div>
                                <div class="mt-20">
                                    <h2 class="fw-700 mb-0" style="font-size: 32px;">
                                        <span style="color: #059669;">{{ $stats['won_trades'] }}</span> /
                                        <span style="color: #dc2626;">{{ $stats['lost_trades'] }}</span>
                                    </h2>
                                    <p class="mb-0 mt-2"><small class="fs-14" style="color: #047857; font-weight: 600;">
                                            Win rate:
                                            {{ $stats['total_trades'] > 0 ? round(($stats['won_trades'] / $stats['total_trades']) * 100, 1) : 0 }}%
                                        </small></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-12 col-md-6 mb-20">
                        <div class="box pull-up"
                            style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-body"
                                style="background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); border-radius: 15px; padding: 25px;">
                                <div class="flexbox align-items-center mb-3">
                                    <div>
                                        <p class="no-margin fw-700 fs-16" style="color: #4338ca;">Wallet Balance</p>
                                    </div>
                                    <div class="card-controls text-end">
                                        <i class="fa fa-wallet fs-24" style="color: #4338ca;"></i>
                                    </div>
                                </div>
                                <div class="mt-20">
                                    <h2 class="fw-700 mb-0" style="color: #4338ca; font-size: 32px;">
                                        ${{ number_format($stats['wallet_balance'], 2) }}</h2>
                                    @if ($stats['locked_balance'] > 0)
                                        <p class="mb-0 mt-2"><small class="fs-14"
                                                style="color: #6366f1; font-weight: 600;">
                                                <i class="fa fa-lock me-1"></i>
                                                ${{ number_format($stats['locked_balance'], 2) }} locked
                                            </small></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-12 col-md-6 mb-20">
                        <div class="box pull-up"
                            style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-body"
                                style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 15px; padding: 25px;">
                                <div class="flexbox align-items-center mb-3">
                                    <div>
                                        <p class="no-margin fw-700 fs-16" style="color: #92400e;">Total Invested</p>
                                    </div>
                                    <div class="card-controls text-end">
                                        <i class="fa fa-dollar-sign fs-24" style="color: #92400e;"></i>
                                    </div>
                                </div>
                                <div class="mt-20">
                                    <h2 class="fw-700 mb-0" style="color: #92400e; font-size: 32px;">
                                        ${{ number_format($stats['total_invested'], 2) }}</h2>
                                    <p class="mb-0 mt-2"><small class="fs-14" style="color: #78350f; font-weight: 600;">
                                            <i class="fa fa-money-bill-wave me-1"></i>
                                            ${{ number_format($stats['total_payouts'], 2) }} payouts
                                        </small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Summary & User Info -->
                <div class="row">
                    <div class="col-xl-6 col-12 mb-20">
                        <div class="box"
                            style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-header with-border"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                <h4 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                    <i class="fa fa-chart-line me-2"></i> Financial Summary
                                </h4>
                            </div>
                            <div class="box-body" style="padding: 25px;">
                                <div class="table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td style="padding: 15px 0;"><strong style="color: #6b7280;">Total
                                                        Deposits</strong></td>
                                                <td class="text-end" style="padding: 15px 0;">
                                                    <span
                                                        style="color: #059669; font-weight: 700; font-size: 16px;">${{ number_format($stats['total_deposits'], 2) }}</span>
                                                </td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td style="padding: 15px 0;"><strong style="color: #6b7280;">Total
                                                        Withdrawals</strong></td>
                                                <td class="text-end" style="padding: 15px 0;">
                                                    <span
                                                        style="color: #dc2626; font-weight: 700; font-size: 16px;">${{ number_format($stats['total_withdrawals'], 2) }}</span>
                                                </td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td style="padding: 15px 0;"><strong style="color: #6b7280;">Total
                                                        Invested</strong></td>
                                                <td class="text-end" style="padding: 15px 0;">
                                                    <span
                                                        style="color: #1f2937; font-weight: 700; font-size: 16px;">${{ number_format($stats['total_invested'], 2) }}</span>
                                                </td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td style="padding: 15px 0;"><strong style="color: #6b7280;">Total
                                                        Payouts</strong></td>
                                                <td class="text-end" style="padding: 15px 0;">
                                                    <span
                                                        style="color: #059669; font-weight: 700; font-size: 16px;">${{ number_format($stats['total_payouts'], 2) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 15px 0;"><strong
                                                        style="color: #1f2937; font-size: 18px;">Current Balance</strong>
                                                </td>
                                                <td class="text-end" style="padding: 15px 0;">
                                                    <span
                                                        style="color: #667eea; font-weight: 700; font-size: 20px;">${{ number_format($stats['wallet_balance'], 2) }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-12 mb-20">
                        <div class="box"
                            style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-header with-border"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                <h4 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                    <i class="fa fa-user me-2"></i> User Information
                                </h4>
                            </div>
                            <div class="box-body" style="padding: 25px;">
                                <div class="table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td style="padding: 15px 0;"><strong style="color: #6b7280;">User
                                                        ID</strong></td>
                                                <td class="text-end" style="padding: 15px 0;">
                                                    <span class="badge"
                                                        style="background: #f3f4f6; color: #374151; padding: 6px 12px; border-radius: 8px;">{{ $user->id }}</span>
                                                </td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td style="padding: 15px 0;"><strong style="color: #6b7280;">Name</strong>
                                                </td>
                                                <td class="text-end"
                                                    style="padding: 15px 0; color: #1f2937; font-weight: 600;">
                                                    {{ $user->name }}</td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td style="padding: 15px 0;"><strong
                                                        style="color: #6b7280;">Email</strong></td>
                                                <td class="text-end" style="padding: 15px 0; color: #1f2937;">
                                                    {{ $user->email }}</td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td style="padding: 15px 0;"><strong
                                                        style="color: #6b7280;">Username</strong></td>
                                                <td class="text-end" style="padding: 15px 0;">
                                                    @if ($user->username)
                                                        <span class="badge bg-info">{{ $user->username }}</span>
                                                    @else
                                                        <span style="color: #9ca3af;">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td style="padding: 15px 0;"><strong
                                                        style="color: #6b7280;">Phone</strong></td>
                                                <td class="text-end" style="padding: 15px 0; color: #1f2937;">
                                                    {{ $user->number ?? 'N/A' }}</td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td style="padding: 15px 0;"><strong style="color: #6b7280;">Email
                                                        Verified</strong></td>
                                                <td class="text-end" style="padding: 15px 0;">
                                                    @if ($user->email_verified_at)
                                                        <span class="badge bg-success">Yes</span>
                                                        <small
                                                            class="text-muted ms-2">({{ $user->email_verified_at->format('M d, Y') }})</small>
                                                    @else
                                                        <span class="badge bg-warning">No</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td style="padding: 15px 0;"><strong
                                                        style="color: #6b7280;">Joined</strong></td>
                                                <td class="text-end" style="padding: 15px 0; color: #1f2937;">
                                                    {{ $user->created_at->format('M d, Y h:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 15px 0;"><strong style="color: #6b7280;">Last
                                                        Updated</strong></td>
                                                <td class="text-end" style="padding: 15px 0; color: #1f2937;">
                                                    {{ $user->updated_at->format('M d, Y h:i A') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Trades -->
                <div class="row">
                    <div class="col-xl-12 col-12">
                        <div class="box"
                            style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-header with-border"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                <h4 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                    <i class="fa fa-exchange-alt me-2"></i> Recent Trades
                                </h4>
                            </div>
                            <div class="box-body" style="padding: 25px;">
                                <div class="table-responsive">
                                    <table class="table table-hover no-margin">
                                        <thead>
                                            <tr style="background: #f9fafb;">
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">ID</th>
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Market</th>
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Outcome</th>
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Amount</th>
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Price</th>
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Status</th>
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentTrades as $trade)
                                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                                    <td style="padding: 15px;">{{ $trade->id }}</td>
                                                    <td style="padding: 15px;">
                                                        @if ($trade->market && $trade->market->event)
                                                            <a href="{{ route('admin.events.show', $trade->market->event->id) }}"
                                                                class="text-info" style="font-weight: 500;">
                                                                {{ \Illuminate\Support\Str::limit($trade->market->event->title ?? 'N/A', 40) }}
                                                            </a>
                                                        @else
                                                            <span style="color: #9ca3af;">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td style="padding: 15px;">
                                                        <span
                                                            class="badge {{ strtoupper($trade->outcome ?? ($trade->side ?? 'N/A')) === 'YES' ? 'bg-success' : 'bg-danger' }}"
                                                            style="padding: 6px 12px; border-radius: 8px; font-weight: 600;">
                                                            {{ strtoupper($trade->outcome ?? ($trade->side ?? 'N/A')) }}
                                                        </span>
                                                    </td>
                                                    <td style="padding: 15px; font-weight: 600; color: #1f2937;">
                                                        ${{ number_format($trade->amount_invested ?? ($trade->amount ?? 0), 2) }}
                                                    </td>
                                                    <td style="padding: 15px; color: #6b7280;">
                                                        {{ number_format($trade->price_at_buy ?? ($trade->price ?? 0), 4) }}
                                                    </td>
                                                    <td style="padding: 15px;">
                                                        @php
                                                            $status = strtoupper($trade->status ?? 'PENDING');
                                                        @endphp
                                                        @if ($status === 'PENDING')
                                                            <span class="badge bg-warning"
                                                                style="padding: 6px 12px; border-radius: 8px; font-weight: 600;">Pending</span>
                                                        @elseif($status === 'WON' || $status === 'WIN')
                                                            <span class="badge bg-success"
                                                                style="padding: 6px 12px; border-radius: 8px; font-weight: 600;">Won</span>
                                                        @elseif($status === 'LOST' || $status === 'LOSS')
                                                            <span class="badge bg-danger"
                                                                style="padding: 6px 12px; border-radius: 8px; font-weight: 600;">Lost</span>
                                                        @else
                                                            <span class="badge bg-secondary"
                                                                style="padding: 6px 12px; border-radius: 8px; font-weight: 600;">{{ $status }}</span>
                                                        @endif
                                                    </td>
                                                    <td style="padding: 15px; color: #6b7280;">
                                                        {{ $trade->created_at->diffForHumans() }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center" style="padding: 40px;">
                                                        <i class="fa fa-exchange-alt fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted mb-0">No trades found</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Deposits & Withdrawals -->
                <div class="row">
                    <div class="col-xl-6 col-12 mb-20">
                        <div class="box"
                            style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-header with-border"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                <h4 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                    <i class="fa fa-arrow-down me-2"></i> Recent Deposits
                                </h4>
                            </div>
                            <div class="box-body" style="padding: 25px;">
                                <div class="table-responsive">
                                    <table class="table table-hover no-margin">
                                        <thead>
                                            <tr style="background: #f9fafb;">
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Amount</th>
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Status</th>
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentDeposits as $deposit)
                                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                                    <td style="padding: 15px; font-weight: 600; color: #059669;">
                                                        ${{ number_format($deposit->amount ?? 0, 2) }}</td>
                                                    <td style="padding: 15px;">
                                                        <span
                                                            class="badge bg-{{ $deposit->status === 'completed' ? 'success' : ($deposit->status === 'pending' ? 'warning' : 'danger') }}"
                                                            style="padding: 6px 12px; border-radius: 8px; font-weight: 600;">
                                                            {{ ucfirst($deposit->status ?? 'N/A') }}
                                                        </span>
                                                    </td>
                                                    <td style="padding: 15px; color: #6b7280;">
                                                        {{ $deposit->created_at->diffForHumans() }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center" style="padding: 40px;">
                                                        <i class="fa fa-arrow-down fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted mb-0">No deposits found</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-12 mb-20">
                        <div class="box"
                            style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-header with-border"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                <h4 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                    <i class="fa fa-arrow-up me-2"></i> Recent Withdrawals
                                </h4>
                            </div>
                            <div class="box-body" style="padding: 25px;">
                                <div class="table-responsive">
                                    <table class="table table-hover no-margin">
                                        <thead>
                                            <tr style="background: #f9fafb;">
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Amount</th>
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Status</th>
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentWithdrawals as $withdrawal)
                                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                                    <td style="padding: 15px; font-weight: 600; color: #dc2626;">
                                                        ${{ number_format($withdrawal->amount ?? 0, 2) }}</td>
                                                    <td style="padding: 15px;">
                                                        <span
                                                            class="badge bg-{{ $withdrawal->status === 'approved' ? 'success' : ($withdrawal->status === 'pending' ? 'warning' : 'danger') }}"
                                                            style="padding: 6px 12px; border-radius: 8px; font-weight: 600;">
                                                            {{ ucfirst($withdrawal->status ?? 'N/A') }}
                                                        </span>
                                                    </td>
                                                    <td style="padding: 15px; color: #6b7280;">
                                                        {{ $withdrawal->created_at->diffForHumans() }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center" style="padding: 40px;">
                                                        <i class="fa fa-arrow-up fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted mb-0">No withdrawals found</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>
            <!-- /.content -->
        </div>
    </div>

    <style>
        .status-dot {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
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

        .box.pull-up:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12) !important;
        }

        .gap-2 {
            gap: 8px;
        }

        table.table-hover tbody tr:hover {
            background-color: #f9fafb;
            transition: background-color 0.2s;
        }

        .modern-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5) !important;
            background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        }

        .modern-btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4) !important;
        }

        .modern-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3) !important;
            border-color: rgba(255, 255, 255, 0.5) !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15) !important;
        }

        .modern-btn-secondary:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        }

        .gap-3 {
            gap: 12px;
        }
    </style>
@endsection

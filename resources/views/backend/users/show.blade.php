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
                        <div class="box premium-user-header"
                            style="border-radius: 20px; overflow: hidden; border: none; box-shadow: 0 8px 30px rgba(102, 126, 234, 0.2);">
                            <div class="box-header with-border" 
                                style="padding: 35px 40px; border: none; position: relative; overflow: hidden; background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 50%, #4f46e5 100%);">
                                <!-- Background Pattern -->
                                <div class="header-pattern" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.08; background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.4) 1px, transparent 0); background-size: 50px 50px;"></div>
                                
                                <div class="d-flex align-items-start justify-content-between" style="position: relative; z-index: 2;">
                                    <!-- Left Section: Avatar and Email -->
                                    <div class="d-flex align-items-start" style="flex: 1;">
                                        <div class="me-4" style="display: flex; flex-direction: column; align-items: center; min-width: 120px;">
                                            <div style="position: relative; margin-bottom: 12px;">
                                                @if ($user->profile_image)
                                                    <img src="{{ asset('storage/' . $user->profile_image) }}"
                                                        alt="{{ $user->name }}" class="rounded-circle"
                                                        style="width: 90px; height: 90px; object-fit: cover; border: 4px solid rgba(255,255,255,0.7); box-shadow: 0 6px 20px rgba(0,0,0,0.25);">
                                                @else
                                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center"
                                                        style="width: 90px; height: 90px; font-size: 36px; background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%); border: 4px solid rgba(255,255,255,0.7); box-shadow: 0 6px 20px rgba(0,0,0,0.25); font-weight: 700; letter-spacing: 1px;">
                                                        {{ $user->initials() }}
                                                    </div>
                                                @endif
                                                <!-- Email Icon Overlay -->
                                                <i class="fa fa-envelope" style="position: absolute; top: -2px; right: -2px; background: #fff; color: #6366f1; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); border: 2px solid #6366f1; z-index: 3;"></i>
                                                <!-- Status Indicator -->
                                                <span style="position: absolute; bottom: -2px; right: -2px; width: 22px; height: 22px; background: {{ $user->email_verified_at ? '#10b981' : '#f59e0b' }}; border: 3px solid #fff; border-radius: 50%; box-shadow: 0 3px 10px rgba(0,0,0,0.3); z-index: 3;"></span>
                                            </div>
                                            <!-- Email below avatar -->
                                            <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 13px; font-weight: 500; text-align: center; max-width: 140px; word-break: break-word; line-height: 1.4;">
                                                {{ $user->email }}
                                            </p>
                                        </div>
                                        
                                        <!-- Right Section: Name and Badges -->
                                        <div style="flex: 1;">
                                            <h2 class="mb-2" style="color: #fff; font-weight: 800; font-size: 30px; text-shadow: 0 2px 10px rgba(0,0,0,0.2); letter-spacing: -0.5px; margin-bottom: 12px !important;">
                                                {{ $user->name }}
                                            </h2>
                                            @if ($user->username)
                                            <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 14px; margin-bottom: 8px !important;">
                                                {{ substr($user->username, 0, 5) }}
                                            </p>
                                            @endif
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                @if ($user->username || $user->number)
                                                    <span class="badge"
                                                        style="background: rgba(255,255,255,0.25); color: #fff; padding: 6px 14px; border-radius: 18px; font-size: 12px; font-weight: 600; border: 1px solid rgba(255,255,255,0.4); backdrop-filter: blur(8px);">
                                                        <i class="fa fa-user me-1" style="font-size: 10px;"></i>{{ $user->username ?: ($user->number ? $user->number : 'User ID') }}
                                                    </span>
                                                @endif
                                                @if (!$user->email_verified_at)
                                                    <span class="badge"
                                                        style="background: rgba(245, 158, 11, 0.3); color: #fff; padding: 6px 14px; border-radius: 18px; font-size: 12px; font-weight: 600; border: 1px solid rgba(245, 158, 11, 0.6); backdrop-filter: blur(8px);">
                                                        <i class="fa fa-info-circle me-1" style="font-size: 10px;"></i> Unverified
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Right Section: Buttons -->
                                    <div class="d-flex flex-column gap-2" style="min-width: 180px; margin-left: 20px;">
                                        <form action="{{ route('admin.users.login-as', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn w-100"
                                                style="padding: 12px 24px; border-radius: 10px; font-weight: 700; font-size: 13px; white-space: nowrap; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); border: none; transition: all 0.3s ease; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff;">
                                                <i class="fa fa-arrow-right me-2"></i> Login As User
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.users.index') }}" class="btn w-100"
                                            style="background: rgba(255, 255, 255, 0.22); color: #fff; font-weight: 700; padding: 11px 24px; border-radius: 10px; box-shadow: 0 3px 12px rgba(0, 0, 0, 0.15); border: 1px solid rgba(255, 255, 255, 0.35); transition: all 0.3s ease; white-space: nowrap; text-decoration: none; backdrop-filter: blur(8px); font-size: 13px;">
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
                            <div class="box-body stat-card-primary"
                                style="border-radius: 15px; padding: 25px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);">
                                <div class="flexbox align-items-center mb-3">
                                    <div>
                                        <p class="no-margin fw-700 fs-16 text-primary-theme">Total Trades</p>
                                    </div>
                                    <div class="card-controls text-end">
                                        <i class="fa fa-exchange-alt fs-24 icon-primary"></i>
                                    </div>
                                </div>
                                <div class="mt-20">
                                    <h2 class="fw-700 mb-0 stat-value text-primary-theme" style="font-size: 32px;">
                                        {{ number_format($stats['total_trades']) }}</h2>
                                    <p class="mb-0 mt-2"><small class="fs-14 text-primary-theme" style="font-weight: 600;">
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
                            <div class="box-body stat-card-primary"
                                style="border-radius: 15px; padding: 25px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);">
                                <div class="flexbox align-items-center mb-3">
                                    <div>
                                        <p class="no-margin fw-700 fs-16 text-primary-theme">Won / Lost</p>
                                    </div>
                                    <div class="card-controls text-end">
                                        <i class="fa fa-trophy fs-24 icon-primary"></i>
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
                            <div class="box-body stat-card-primary"
                                style="border-radius: 15px; padding: 25px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);">
                                <div class="flexbox align-items-center mb-3">
                                    <div>
                                        <p class="no-margin fw-700 fs-16 text-primary-theme">Wallet Balance</p>
                                    </div>
                                    <div class="card-controls text-end">
                                        <i class="fa fa-wallet fs-24 icon-primary"></i>
                                    </div>
                                </div>
                                <div class="mt-20">
                                    <h2 class="fw-700 mb-0 stat-value text-primary-theme" style="font-size: 32px;">
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
                            <div class="box-body stat-card-primary"
                                style="border-radius: 15px; padding: 25px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);">
                                <div class="flexbox align-items-center mb-3">
                                    <div>
                                        <p class="no-margin fw-700 fs-16 text-primary-theme">Total Invested</p>
                                    </div>
                                    <div class="card-controls text-end">
                                        <i class="fa fa-dollar-sign fs-24 icon-primary"></i>
                                    </div>
                                </div>
                                <div class="mt-20">
                                    <h2 class="fw-700 mb-0 stat-value text-primary-theme" style="font-size: 32px;">
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
                            <div class="box-header with-border primary-gradient" style="padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                <h4 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                    <i class="fa fa-chart-line me-2"></i> Financial Summary
                                </h4>
                            </div>
                            <div class="box-body" style="padding: 25px;">
                                <!-- Test Deposit Button -->
                                <div class="mb-4" style="padding: 15px; background: #fef3c7; border-radius: 10px; border: 2px dashed #fbbf24;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="mb-1" style="color: #92400e; font-weight: 700;">
                                                <i class="fa fa-flask me-2"></i>Test Deposit
                                            </h5>
                                            <p class="mb-0" style="color: #78350f; font-size: 13px;">
                                                Add test funds to user wallet for testing
                                            </p>
                                        </div>
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#testDepositModal"
                                            style="font-weight: 600; padding: 10px 20px; border-radius: 8px;">
                                            <i class="fa fa-plus me-2"></i>Add Test Deposit
                                        </button>
                                    </div>
                                </div>
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
                            <div class="box-header with-border primary-gradient" style="padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
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
                                                            class="text-muted ms-2">({{ $user->email_verified_at ? $user->email_verified_at->format('M d, Y') : 'N/A' }})</small>
                                                    @else
                                                        <span class="badge bg-warning">No</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td style="padding: 15px 0;"><strong
                                                        style="color: #6b7280;">Joined</strong></td>
                                                <td class="text-end" style="padding: 15px 0; color: #1f2937;">
                                                    {{ $user->created_at ? $user->created_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td style="padding: 15px 0;"><strong style="color: #6b7280;">Last
                                                        Updated</strong></td>
                                                <td class="text-end" style="padding: 15px 0; color: #1f2937;">
                                                    {{ $user->updated_at ? $user->updated_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 15px 0;"><strong style="color: #6b7280;">Wallet Password</strong></td>
                                                <td class="text-end" style="padding: 15px 0;">
                                                    <button type="button" class="btn btn-sm btn-primary-gradient" 
                                                        data-bs-toggle="modal" data-bs-target="#editWalletPasswordModal"
                                                        style="padding: 6px 16px; font-size: 13px;">
                                                        <i class="fa fa-edit me-1"></i> Edit Password
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Activities -->
                <div class="row">
                    <div class="col-xl-12 col-12">
                        <div class="box"
                            style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-header with-border primary-gradient" style="padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                <h4 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                    <i class="fa fa-history me-2"></i> All User Activities
                                </h4>
                            </div>
                            <div class="box-body" style="padding: 25px;">
                                <div class="table-responsive">
                                    <table id="allActivitiesTable" class="table table-hover no-margin" style="width:100%">
                                        <thead>
                                            <tr style="background: #f9fafb;">
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Type</th>
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Description</th>
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Amount</th>
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Status</th>
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($allActivities as $activity)
                                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                                    <td style="padding: 15px;">
                                                        <span class="badge bg-{{ $activity['color'] }}" 
                                                            style="padding: 6px 12px; border-radius: 8px; font-weight: 600;">
                                                            <i class="fa fa-{{ $activity['icon'] }} me-1"></i>
                                                            {{ $activity['title'] }}
                                                        </span>
                                                    </td>
                                                    <td style="padding: 15px; color: #1f2937;">
                                                        {{ $activity['description'] }}
                                                    </td>
                                                    <td style="padding: 15px; font-weight: 600; color: #1f2937;">
                                                        @if($activity['amount'] > 0)
                                                            ${{ number_format($activity['amount'], 2) }}
                                                        @else
                                                            <span style="color: #9ca3af;">-</span>
                                                        @endif
                                                    </td>
                                                    <td style="padding: 15px;">
                                                        @php
                                                            $status = strtoupper($activity['status'] ?? 'COMPLETED');
                                                        @endphp
                                                        @if($status === 'PENDING')
                                                            <span class="badge bg-warning" style="padding: 6px 12px; border-radius: 8px; font-weight: 600;">Pending</span>
                                                        @elseif($status === 'COMPLETED' || $status === 'APPROVED' || $status === 'WON')
                                                            <span class="badge bg-success" style="padding: 6px 12px; border-radius: 8px; font-weight: 600;">Completed</span>
                                                        @elseif($status === 'REJECTED' || $status === 'LOST')
                                                            <span class="badge bg-danger" style="padding: 6px 12px; border-radius: 8px; font-weight: 600;">{{ ucfirst(strtolower($status)) }}</span>
                                                        @else
                                                            <span class="badge bg-secondary" style="padding: 6px 12px; border-radius: 8px; font-weight: 600;">{{ ucfirst(strtolower($status)) }}</span>
                                                        @endif
                                                    </td>
                                                    <td style="padding: 15px; color: #6b7280;">
                                                        {{ \Carbon\Carbon::parse($activity['date'])->format('M d, Y h:i A') }}
                                                        <br>
                                                        <small style="color: #9ca3af;">{{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}</small>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center" style="padding: 40px;">
                                                        <i class="fa fa-history fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted mb-0">No activities found</p>
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

                <!-- Recent Trades -->
                <div class="row">
                    <div class="col-xl-12 col-12">
                        <div class="box"
                            style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-header with-border primary-gradient" style="padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                <h4 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                    <i class="fa fa-exchange-alt me-2"></i> Recent Trades
                                </h4>
                            </div>
                            <div class="box-body" style="padding: 25px;">
                                <div class="table-responsive">
                                    <table id="recentTradesTable" class="table table-hover no-margin" style="width:100%">
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
                                            @forelse($recentTrades as $key=>$trade)
                                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                                    <td style="padding: 15px;">{{  ++$key }}</td>
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
                                                            class="badge {{ strtoupper($trade->outcome ?? ($trade->side ?? 'YES')) === 'YES' ? 'bg-success' : 'bg-danger' }}"
                                                            style="padding: 6px 12px; border-radius: 8px; font-weight: 600;">
                                                            {{ $trade->getDisplayOutcomeName() }}
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
                            <div class="box-header with-border primary-gradient" style="padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                <h4 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                    <i class="fa fa-arrow-down me-2"></i> Recent Deposits
                                </h4>
                            </div>
                            <div class="box-body" style="padding: 25px;">
                                <div class="table-responsive">
                                    <table id="recentDepositsTable" class="table table-hover no-margin" style="width:100%">
                                        <thead>
                                            <tr style="background: #f9fafb;">
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Amount</th>
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Status</th>
                                                <th style="padding: 15px; font-weight: 600; color: #374151;">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentDeposits as $key=>$deposit)
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
                            <div class="box-header with-border primary-gradient" style="padding: 20px 25px; border: none; border-radius: 15px 15px 0 0;">
                                <h4 class="box-title" style="color: #fff; font-weight: 600; font-size: 18px; margin: 0;">
                                    <i class="fa fa-arrow-up me-2"></i> Recent Withdrawals
                                </h4>
                            </div>
                            <div class="box-body" style="padding: 25px;">
                                <div class="table-responsive">
                                    <table id="recentWithdrawalsTable" class="table table-hover no-margin" style="width:100%">
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
        /* Premium Background */
        .content-wrapper {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
            min-height: 100vh;
        }

        /* Premium User Header */
        .premium-user-header {
            position: relative;
            overflow: visible;
        }

        .premium-user-header .box-header {
            position: relative;
        }

        .premium-user-header:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.2) !important;
        }

        .user-header-avatar {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .premium-user-header:hover .user-header-avatar {
            transform: scale(1.05);
            box-shadow: 0 12px 35px rgba(0,0,0,0.3) !important;
        }

        .user-header-name {
            transition: all 0.3s ease;
        }

        .premium-user-header:hover .user-header-name {
            transform: translateX(5px);
        }

        .badge-header-primary,
        .badge-header-success,
        .badge-header-warning {
            transition: all 0.3s ease;
        }

        .premium-user-header:hover .badge-header-primary,
        .premium-user-header:hover .badge-header-success,
        .premium-user-header:hover .badge-header-warning {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        /* Status Dot Animation */
        .status-dot,
        .status-dot-header {
            animation: pulseGlow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }

        @keyframes pulseGlow {
            0% {
                opacity: 1;
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            50% {
                opacity: 0.9;
                box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
            }
            100% {
                opacity: 1;
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        /* Premium Stat Cards */
        .box.pull-up {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeInUp 0.6s ease-out;
            animation-fill-mode: both;
        }

        .box.pull-up:nth-child(1) { animation-delay: 0.1s; }
        .box.pull-up:nth-child(2) { animation-delay: 0.2s; }
        .box.pull-up:nth-child(3) { animation-delay: 0.3s; }
        .box.pull-up:nth-child(4) { animation-delay: 0.4s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .box.pull-up:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15) !important;
        }

        .box.pull-up .box-body {
            position: relative;
            overflow: hidden;
        }

        .box.pull-up .box-body::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .box.pull-up:hover .box-body::before {
            left: 100%;
        }

        /* Premium Buttons */
        .modern-btn-primary {
            position: relative;
            overflow: hidden;
        }

        .modern-btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .modern-btn-primary:hover::before {
            width: 400px;
            height: 400px;
        }

        .modern-btn-primary:hover {
            transform: translateY(-3px);
        }

        .modern-btn-primary:active {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5) !important;
        }

        .modern-btn-secondary {
            position: relative;
            overflow: hidden;
        }

        .modern-btn-secondary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            transform: translate(-50%, -50%);
            transition: width 0.5s, height 0.5s;
        }

        .modern-btn-secondary:hover::before {
            width: 300px;
            height: 300px;
        }

        .modern-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.35) !important;
            border-color: rgba(255, 255, 255, 0.6) !important;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2) !important;
        }

        .modern-btn-secondary:active {
            transform: translateY(-1px);
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.15) !important;
        }

        /* Premium Tables */
        table.table-hover tbody tr {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        table.table-hover tbody tr::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transform: scaleY(0);
            transition: transform 0.3s;
        }

        table.table-hover tbody tr:hover {
            background-color: #f9fafb;
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        table.table-hover tbody tr:hover::before {
            transform: scaleY(1);
        }

        /* Premium Box Headers */
        .box-header {
            position: relative;
            overflow: hidden;
        }

        .box-header::after {
            content: '';
            position: absolute;
            top: 0;
            right: -50%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { right: -50%; }
            100% { right: 150%; }
        }

        /* Premium Badges */
        .badge {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .badge:hover::before {
            left: 100%;
        }

        .badge:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Premium Modal */
        .modal-content {
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Test Deposit Section */
        .alert {
            position: relative;
            overflow: hidden;
        }

        .alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }

        /* Utility Classes */
        .gap-2 {
            gap: 8px;
        }

        .gap-3 {
            gap: 12px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .box.pull-up:hover {
                transform: translateY(-4px) scale(1.01);
            }
            
            .modern-btn-primary,
            .modern-btn-secondary {
                min-width: auto;
                width: 100%;
            }
        }
    </style>

    @push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <style>
        /* DataTables Custom Styling */
        .dataTables_wrapper {
            padding: 0;
        }
        
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 20px;
        }
        
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            font-weight: 600;
            color: #374151;
        }
        
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 14px;
        }
        
        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .dataTables_wrapper .dataTables_info {
            color: #6b7280;
            font-weight: 500;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 8px 12px;
            margin: 0 2px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            color: #374151 !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: #fff !important;
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: #fff !important;
            border-color: #667eea;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .dataTables_wrapper table.dataTable thead th {
            border-bottom: 2px solid #e5e7eb;
            font-weight: 600;
            color: #374151;
            position: relative;
        }
        
        /* Remove sorting indicators from first and last columns */
        .dataTables_wrapper table.dataTable thead th:first-child.sorting::before,
        .dataTables_wrapper table.dataTable thead th:first-child.sorting::after,
        .dataTables_wrapper table.dataTable thead th:first-child.sorting_asc::before,
        .dataTables_wrapper table.dataTable thead th:first-child.sorting_asc::after,
        .dataTables_wrapper table.dataTable thead th:first-child.sorting_desc::before,
        .dataTables_wrapper table.dataTable thead th:first-child.sorting_desc::after,
        .dataTables_wrapper table.dataTable thead th:last-child.sorting::before,
        .dataTables_wrapper table.dataTable thead th:last-child.sorting::after,
        .dataTables_wrapper table.dataTable thead th:last-child.sorting_asc::before,
        .dataTables_wrapper table.dataTable thead th:last-child.sorting_asc::after,
        .dataTables_wrapper table.dataTable thead th:last-child.sorting_desc::before,
        .dataTables_wrapper table.dataTable thead th:last-child.sorting_desc::after {
            display: none !important;
        }
        
        /* Remove red border/box from first and last columns */
        .dataTables_wrapper table.dataTable tbody td:first-child,
        .dataTables_wrapper table.dataTable thead th:first-child {
            border-left: none !important;
            box-shadow: none !important;
            outline: none !important;
        }
        
        .dataTables_wrapper table.dataTable tbody td:last-child,
        .dataTables_wrapper table.dataTable thead th:last-child {
            border-right: none !important;
            box-shadow: none !important;
            outline: none !important;
        }
        
        .dataTables_wrapper table.dataTable tbody tr:hover {
            background-color: #f9fafb;
        }
        
        .dataTables_wrapper table.dataTable.no-footer {
            border-bottom: 1px solid #e5e7eb;
        }
        
        /* Fix any red highlighting on cells */
        .dataTables_wrapper table.dataTable tbody td:first-child,
        .dataTables_wrapper table.dataTable tbody td:last-child {
            background-color: transparent !important;
            border-color: #e5e7eb !important;
        }
        
        .dataTables_wrapper table.dataTable tbody tr:hover td:first-child,
        .dataTables_wrapper table.dataTable tbody tr:hover td:last-child {
            background-color: #f9fafb !important;
        }
        
        .dataTables_processing {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-radius: 8px;
            padding: 15px;
            font-weight: 600;
        }
    </style>
    @endpush

    <!-- Edit Wallet Password Modal -->
    <div class="modal fade" id="editWalletPasswordModal" tabindex="-1" aria-labelledby="editWalletPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                <div class="modal-header primary-gradient"
                    style="border-radius: 15px 15px 0 0; border: none; padding: 20px 25px;">
                    <h5 class="modal-title" id="editWalletPasswordModalLabel" style="color: #fff; font-weight: 700;">
                        <i class="fa fa-key me-2"></i>Edit Wallet Password
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="editWalletPasswordForm">
                    @csrf
                    <div class="modal-body" style="padding: 25px;">
                        <div class="mb-3">
                            <label for="walletPassword" class="form-label" style="font-weight: 600; color: #374151;">
                                New Wallet Password <span style="color: #ef4444;">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: #f3f4f6; border-right: none;">
                                    <i class="fa fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="walletPassword" name="withdrawal_password"
                                    minlength="6" maxlength="255" placeholder="Enter new wallet password" required
                                    style="border-left: none; padding: 12px 15px; font-size: 16px; font-weight: 500;">
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword"
                                    style="border-left: none;">
                                    <i class="fa fa-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimum: 6 characters</small>
                        </div>
                        <div class="mb-3">
                            <label for="confirmWalletPassword" class="form-label" style="font-weight: 600; color: #374151;">
                                Confirm Password <span style="color: #ef4444;">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: #f3f4f6; border-right: none;">
                                    <i class="fa fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="confirmWalletPassword" name="confirm_password"
                                    minlength="6" maxlength="255" placeholder="Confirm new wallet password" required
                                    style="border-left: none; padding: 12px 15px; font-size: 16px; font-weight: 500;">
                            </div>
                        </div>
                        <div class="alert alert-warning mb-0" style="background: #fef3c7; border: 1px solid #fbbf24; color: #92400e;">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> This will change the user's withdrawal password. The user will need to use this new password for future withdrawals.
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 20px 25px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            style="font-weight: 600; padding: 10px 20px; border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary-gradient" id="submitWalletPassword"
                            style="font-weight: 600; padding: 10px 25px; border-radius: 8px;">
                            <i class="fa fa-check me-2"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Test Deposit Modal -->
    <div class="modal fade" id="testDepositModal" tabindex="-1" aria-labelledby="testDepositModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                <div class="modal-header"
                    style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); border-radius: 15px 15px 0 0; border: none; padding: 20px 25px;">
                    <h5 class="modal-title" id="testDepositModalLabel" style="color: #fff; font-weight: 700;">
                        <i class="fa fa-flask me-2"></i>Add Test Deposit
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="testDepositForm">
                    @csrf
                    <div class="modal-body" style="padding: 25px;">
                        <div class="mb-3">
                            <label for="testDepositAmount" class="form-label" style="font-weight: 600; color: #374151;">
                                Amount <span style="color: #ef4444;">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: #f3f4f6; border-right: none;">
                                    <i class="fa fa-dollar-sign"></i>
                                </span>
                                <input type="number" class="form-control" id="testDepositAmount" name="amount"
                                    step="0.01" min="0.01" max="100000" placeholder="0.00" required
                                    style="border-left: none; padding: 12px 15px; font-size: 16px; font-weight: 500;">
                            </div>
                            <small class="text-muted">Minimum: $0.01 | Maximum: $100,000</small>
                        </div>
                        <div class="mb-3">
                            <label for="testDepositNote" class="form-label" style="font-weight: 600; color: #374151;">
                                Note (Optional)
                            </label>
                            <textarea class="form-control" id="testDepositNote" name="note" rows="3"
                                placeholder="Add a note for this test deposit..."
                                style="padding: 12px 15px; font-size: 14px; resize: vertical;"></textarea>
                        </div>
                        <div class="alert alert-warning mb-0" style="background: #fef3c7; border: 1px solid #fbbf24; color: #92400e;">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            <strong>Test Deposit:</strong> This will add funds directly to the user's wallet. This is for testing purposes only.
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 20px 25px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            style="font-weight: 600; padding: 10px 20px; border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="submitTestDeposit"
                            style="font-weight: 600; padding: 10px 25px; border-radius: 8px; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); border: none;">
                            <i class="fa fa-check me-2"></i>Add Deposit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Wallet Password Edit
            const walletPasswordForm = document.getElementById('editWalletPasswordForm');
            const submitWalletPasswordBtn = document.getElementById('submitWalletPassword');
            const togglePasswordBtn = document.getElementById('togglePassword');
            const walletPasswordInput = document.getElementById('walletPassword');
            const togglePasswordIcon = document.getElementById('togglePasswordIcon');

            // Toggle password visibility
            if (togglePasswordBtn) {
                togglePasswordBtn.addEventListener('click', function() {
                    const type = walletPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    walletPasswordInput.setAttribute('type', type);
                    togglePasswordIcon.classList.toggle('fa-eye');
                    togglePasswordIcon.classList.toggle('fa-eye-slash');
                });
            }

            // Wallet Password Form Submission
            if (walletPasswordForm) {
                walletPasswordForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const password = document.getElementById('walletPassword').value;
                    const confirmPassword = document.getElementById('confirmWalletPassword').value;

                    if (!password || password.length < 6) {
                        alert('Password must be at least 6 characters long');
                        return;
                    }

                    if (password !== confirmPassword) {
                        alert('Passwords do not match');
                        return;
                    }

                    // Disable submit button
                    submitWalletPasswordBtn.disabled = true;
                    submitWalletPasswordBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Updating...';

                    // Make AJAX request
                    fetch('/admin/users/{{ $user->id }}/update-wallet-password', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            withdrawal_password: password
                        })
                    })
                    .then(response => {
                        // Check if response is ok
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.message || 'Failed to update wallet password');
                            }).catch(() => {
                                throw new Error('Failed to update wallet password. Please try again.');
                            });
                        }
                        return response.json();
                    })
                    .catch(error => {
                        // Handle network errors
                        if (error instanceof TypeError) {
                            throw new Error('Network error. Please check your connection and try again.');
                        }
                        throw error;
                    })
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Wallet password updated successfully',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#667eea'
                                }).then(() => {
                                    // Close modal
                                    const modal = bootstrap.Modal.getInstance(document.getElementById('editWalletPasswordModal'));
                                    if (modal) {
                                        modal.hide();
                                    }
                                    // Reset form
                                    walletPasswordForm.reset();
                                });
                            } else {
                                alert('Wallet password updated successfully!');
                                const modal = bootstrap.Modal.getInstance(document.getElementById('editWalletPasswordModal'));
                                if (modal) {
                                    modal.hide();
                                }
                                walletPasswordForm.reset();
                            }
                        } else {
                            throw new Error(data.message || 'Failed to update wallet password');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        console.error('Error details:', error);
                        let errorMessage = 'Failed to update wallet password. Please try again.';
                        if (error.message) {
                            errorMessage = error.message;
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#ef4444'
                            });
                        } else {
                            alert('Error: ' + errorMessage);
                        }
                    })
                    .finally(() => {
                        // Re-enable submit button
                        submitWalletPasswordBtn.disabled = false;
                        submitWalletPasswordBtn.innerHTML = '<i class="fa fa-check me-2"></i>Update Password';
                    });
                });
            }

            // Test Deposit Form
            const form = document.getElementById('testDepositForm');
            const submitBtn = document.getElementById('submitTestDeposit');
            const modal = new bootstrap.Modal(document.getElementById('testDepositModal'));

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const amount = document.getElementById('testDepositAmount').value;
                const note = document.getElementById('testDepositNote').value;

                if (!amount || parseFloat(amount) <= 0) {
                    alert('Please enter a valid amount');
                    return;
                }

                // Disable submit button
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Processing...';

                // Make AJAX request
                fetch('{{ route('admin.users.test-deposit', $user->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        amount: parseFloat(amount),
                        note: note || null
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                html: `
                                    <div style="text-align: left;">
                                        <p><strong>Test deposit added successfully!</strong></p>
                                        <p style="margin-top: 10px;">Amount: <strong>$${data.data.amount}</strong></p>
                                        <p>Previous Balance: <strong>$${data.data.balance_before}</strong></p>
                                        <p>New Balance: <strong>$${data.data.balance_after}</strong></p>
                                    </div>
                                `,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#fbbf24'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            alert('Test deposit added successfully!\nAmount: $' + data.data.amount + '\nNew Balance: $' + data.data.balance_after);
                            location.reload();
                        }
                    } else {
                        throw new Error(data.message || 'Failed to add test deposit');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Failed to add test deposit. Please try again.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#ef4444'
                        });
                    } else {
                        alert('Error: ' + error.message);
                    }
                })
                .finally(() => {
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa fa-check me-2"></i>Add Deposit';
                });
            });

            // Initialize DataTables for all tables
            // Recent Trades Table
            if ($.fn.DataTable.isDataTable('#recentTradesTable')) {
                $('#recentTradesTable').DataTable().destroy();
            }
            $('#recentTradesTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                order: [[6, 'desc']], // Sort by date (last column)
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                drawCallback: function() {
                    // Re-apply hover effects after DataTables redraw
                    $('table.dataTable tbody tr').hover(
                        function() {
                            $(this).css('background-color', '#f9fafb');
                        },
                        function() {
                            $(this).css('background-color', '');
                        }
                    );
                }
            });

            // Recent Deposits Table
            if ($.fn.DataTable.isDataTable('#recentDepositsTable')) {
                $('#recentDepositsTable').DataTable().destroy();
            }
                $('#recentDepositsTable').DataTable({
                    responsive: true,
                    pageLength: 5,
                    lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]],
                    order: [[2, 'desc']], // Sort by date (last column)
                    columnDefs: [
                        { orderable: false, targets: [0] }, // Disable sorting on first column
                    ],
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });

            // Recent Withdrawals Table
            if ($.fn.DataTable.isDataTable('#recentWithdrawalsTable')) {
                $('#recentWithdrawalsTable').DataTable().destroy();
            }
                $('#recentWithdrawalsTable').DataTable({
                    responsive: true,
                    pageLength: 5,
                    lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]],
                    order: [[2, 'desc']], // Sort by date (last column)
                    columnDefs: [
                        { orderable: false, targets: [0] }, // Disable sorting on first column
                    ],
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });

            // All User Activities Table
            if ($.fn.DataTable.isDataTable('#allActivitiesTable')) {
                $('#allActivitiesTable').DataTable().destroy();
            }
                $('#allActivitiesTable').DataTable({
                    responsive: true,
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    order: [[4, 'desc']], // Sort by date (last column)
                    columnDefs: [
                        { orderable: false, targets: [0] }, // Disable sorting on first column (Type)
                        { orderable: true, targets: '_all' }
                    ],
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });
        });
    </script>
    @endpush
@endsection

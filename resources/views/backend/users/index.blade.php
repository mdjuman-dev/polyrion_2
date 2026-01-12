@extends('backend.layouts.master')
@section('title', 'All Users')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="box"
                            style="border-radius: 15px; overflow: hidden; border: none; box-shadow: 0 0 30px rgba(0,0,0,0.08);">
                            <div class="box-header with-border primary-gradient" style="padding: 25px 30px; border: none;">
                                <h3 class="box-title" style="color: #fff; font-weight: 600; font-size: 24px; margin: 0;">
                                    <i class="fa fa-users me-2"></i> All Users
                                </h3>
                            </div>
                            <div class="box-body" style="padding: 30px;">
                                <!-- Search Form -->
                                <form method="GET" action="{{ route('admin.users.index') }}" class="mb-30">
                                    <div class="row align-items-center">
                                        <div class="col-md-8 mb-3 mb-md-0">
                                            <div class="form-group mb-0">
                                                <div class="input-group modern-search">
                                                    <span class="input-group-text"
                                                        style="background: #f8f9fa; border: 2px solid #e9ecef; border-right: none;">
                                                        <i class="fa fa-search text-muted"></i>
                                                    </span>
                                                    <input type="text" name="search" class="form-control"
                                                        style="border: 2px solid #e9ecef; border-left: none; padding: 12px 15px; font-size: 15px;"
                                                        placeholder="Search by name, email, username, or phone..."
                                                        value="{{ request('search') }}">
                                                    <button type="submit" class="btn btn-primary"
                                                        style="padding: 12px 25px; font-weight: 500;">
                                                        Search
                                                    </button>
                                                    @if (request('search'))
                                                        <a href="{{ route('admin.users.index') }}" class="btn btn-light"
                                                            style="padding: 12px 20px; border: 2px solid #e9ecef;">
                                                            <i class="fa fa-times"></i> Clear
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            <div class="total-users-badge">
                                                <span class="badge badge-primary" style="font-size: 16px; padding: 12px 20px;">
                                                    <i class="fa fa-users me-2"></i>
                                                    Total: {{ $users->total() }} users
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <!-- Users Cards Grid -->
                                @if ($users->count() > 0)
                                    <div class="row">
                                        @foreach ($users as $user)
                                            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-25">
                                                <div class="box box-body user-card modern-card"
                                                    style="border-radius: 20px; border: 1px solid #e5e7eb; box-shadow: 0 4px 20px rgba(0,0,0,0.08); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden; background: #fff; padding: 0;">

                                                    <!-- Gradient Header -->
                                                    <div class="card-gradient primary-gradient"
                                                        style="position: relative; height: 100px; z-index: 0; border-radius: 20px 20px 0 0;">
                                                    </div>

                                                        <!-- User Header -->
                                                        <div class="text-center"
                                                            style="position: relative; z-index: 2; margin-top: -50px; padding: 0 20px;">
                                                            <div class="user-avatar mb-3"
                                                                style="position: relative; display: inline-block;">
                                                                @if ($user->profile_image)
                                                                    <img src="{{ asset('storage/' . $user->profile_image) }}"
                                                                        alt="{{ $user->name }}" class="rounded-circle"
                                                                        style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.15); background: #fff;">
                                                                @else
                                                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center mx-auto primary-gradient"
                                                                        style="width: 100px; height: 100px; font-size: 40px; border: 4px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.15); font-weight: 600;">
                                                                        {{ $user->initials() }}
                                                                    </div>
                                                                @endif
                                                                <!-- Status Indicator -->
                                                                <span class="status-dot"
                                                                    style="position: absolute; bottom: 2px; right: 2px; width: 20px; height: 20px; background: {{ $user->email_verified_at ? '#10b981' : '#f59e0b' }}; border: 3px solid #fff; border-radius: 50%; box-shadow: 0 2px 8px rgba(0,0,0,0.2);"></span>
                                                            </div>
                                                            <h5 class="mb-1"
                                                                style="font-weight: 700; color: #1f2937; font-size: 19px; margin-top: 10px;">
                                                                {{ $user->name }}</h5>
                                                            <p class="text-muted mb-2"
                                                                style="font-size: 13px; color: #6b7280;">
                                                                {{ \Illuminate\Support\Str::limit($user->email, 25) }}</p>
                                                            <div class="mb-3">
                                                                @if ($user->email_verified_at)
                                                                    <span class="badge"
                                                                        style="background-color: #d1fae5; color: #065f46; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 600; margin: 2px;">
                                                                        <i class="fa fa-check-circle"></i> Verified
                                                                    </span>
                                                                @else
                                                                    <span class="badge"
                                                                        style="background-color: #fef3c7; color: #92400e; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 600; margin: 2px;">
                                                                        <i class="fa fa-exclamation-circle"></i> Unverified
                                                                    </span>
                                                                @endif
                                                                @if ($user->username)
                                                                    <span class="badge"
                                                                        style="background-color: #dbeafe; color: #1e40af; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 600; margin: 2px;">
                                                                        {{ $user->username }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- User Stats -->
                                                        <div class="row g-0 mb-3 px-3"
                                                            style="position: relative; z-index: 2; margin-top: 10px;">
                                                            <div class="col-6">
                                                                <div class="stat-card"
                                                                    style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); padding: 12px; border-radius: 12px; margin-right: 6px; text-align: center;">
                                                                    <i class="fa fa-wallet"
                                                                        style="color: #065f46; font-size: 22px; display: block; margin-bottom: 5px;"></i>
                                                                    <span
                                                                        style="font-size: 10px; color: #065f46; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 4px;">Balance</span>
                                                                    <p class="mb-0"
                                                                        style="font-size: 16px; font-weight: 700; color: #065f46;">
                                                                        ${{ number_format($user->wallet ? $user->wallet->balance : 0, 2) }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="stat-card"
                                                                    style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); padding: 12px; border-radius: 12px; margin-left: 6px; text-align: center;">
                                                                    <i class="fa fa-exchange-alt"
                                                                        style="color: #1e40af; font-size: 22px; display: block; margin-bottom: 5px;"></i>
                                                                    <span
                                                                        style="font-size: 10px; color: #1e40af; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 4px;">Trades</span>
                                                                    <p class="mb-0"
                                                                        style="font-size: 16px; font-weight: 700; color: #1e40af;">
                                                                        {{ $user->trades()->count() }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        @if ($user->wallet && $user->wallet->locked_balance > 0)
                                                            <div class="mx-3 mb-3" style="position: relative; z-index: 2;">
                                                                <div class="alert mb-0"
                                                                    style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: none; border-radius: 12px; padding: 10px 15px;">
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="fa fa-lock me-2"
                                                                            style="color: #92400e; font-size: 14px;"></i>
                                                                        <span
                                                                            style="color: #92400e; font-size: 13px; font-weight: 600;">
                                                                            Locked:
                                                                            ${{ number_format($user->wallet->locked_balance, 2) }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <!-- User Info -->
                                                        <div class="px-3 mb-3" style="position: relative; z-index: 2;">
                                                            @if ($user->number)
                                                                <p class="mb-2"
                                                                    style="font-size: 13px; color: #6b7280;">
                                                                    <i class="fa fa-phone me-2"
                                                                        style="width: 16px; text-align: center;"></i>
                                                                    {{ $user->number }}
                                                                </p>
                                                            @endif
                                                            <p class="mb-0" style="font-size: 13px; color: #6b7280;">
                                                                <i class="fa fa-calendar me-2"
                                                                    style="width: 16px; text-align: center;"></i>
                                                                Joined {{ $user->created_at->format('M d, Y') }}
                                                            </p>
                                                        </div>

                                                    <!-- Action Buttons - Side by Side -->
                                                    <div class="px-3 pb-3" style="position: relative; z-index: 2;">
                                                        <form action="{{ route('admin.users.login-as', $user->id) }}" method="POST" class="d-inline" style="width: 100%;">
                                                            @csrf
                                                            <div class="d-flex gap-2">
                                                                <button type="submit" class="btn login-btn btn-primary-gradient flex-fill"
                                                                    style="font-size: 14px; padding: 13px;"
                                                                    title="Login as {{ $user->name }}">
                                                                    <i class="fa fa-sign-in-alt me-2"></i> Login As User
                                                                </button>
                                                                <a href="{{ route('admin.users.show', $user->id) }}"
                                                                    class="btn action-btn flex-fill"
                                                                    style="background: #f3f4f6; color: #374151; font-weight: 600; padding: 13px; border: none; border-radius: 12px; transition: all 0.3s; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;"
                                                                    title="View Details">
                                                                    <i class="fa fa-eye me-2"></i> View Details
                                                                </a>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <div class="empty-state" style="max-width: 400px; margin: 0 auto;">
                                            <div class="mb-4"
                                                style="width: 120px; height: 120px; margin: 0 auto; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                <i class="fa fa-users" style="font-size: 50px; color: #9ca3af;"></i>
                                            </div>
                                            <h4 style="color: #6b7280; font-weight: 600; margin-bottom: 10px;">No users
                                                found</h4>
                                            @if (request('search'))
                                                <p class="text-muted mb-4">Try adjusting your search criteria</p>
                                                <a href="{{ route('admin.users.index') }}" class="btn btn-primary-gradient"
                                                    style="padding: 12px 30px;">
                                                    <i class="fa fa-arrow-left me-2"></i> View All Users
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Pagination -->
                                @if ($users->hasPages())
                                    <div class="mt-4 pagination-wrapper">
                                        {{ $users->links() }}
                                    </div>
                                @endif
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

        /* Premium Card Styles */
        .modern-card {
            position: relative;
            display: flex;
            flex-direction: column;
            animation: fadeInUp 0.6s ease-out;
            animation-fill-mode: both;
            cursor: default;
        }

        .modern-card:nth-child(1) { animation-delay: 0.1s; }
        .modern-card:nth-child(2) { animation-delay: 0.2s; }
        .modern-card:nth-child(3) { animation-delay: 0.3s; }
        .modern-card:nth-child(4) { animation-delay: 0.4s; }
        .modern-card:nth-child(5) { animation-delay: 0.5s; }
        .modern-card:nth-child(6) { animation-delay: 0.6s; }

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

        .modern-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 60px rgba(102, 126, 234, 0.3) !important;
            border-color: #667eea !important;
        }

        .modern-card:hover .card-gradient {
            transform: scale(1.05);
        }

        .card-gradient {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .card-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .modern-card:hover .card-gradient::before {
            left: 100%;
        }


        .user-avatar img,
        .user-avatar div {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modern-card:hover .user-avatar img,
        .modern-card:hover .user-avatar div {
            transform: scale(1.15) rotate(3deg);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }

        .stat-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .modern-card:hover .stat-card {
            transform: scale(1.05) translateY(-2px);
        }

        .modern-card:hover .stat-card::before {
            opacity: 1;
        }

        .login-btn {
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
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

        .login-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5) !important;
        }

        .login-btn:active {
            transform: translateY(-1px);
        }

        .action-btn {
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .action-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(102, 126, 234, 0.1);
            transform: translate(-50%, -50%);
            transition: width 0.4s, height 0.4s;
        }

        .action-btn:hover::after {
            width: 200px;
            height: 200px;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            background: #e5e7eb !important;
        }

        /* Premium Search */
        .modern-search {
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-radius: 12px;
            overflow: hidden;
        }

        .modern-search .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .modern-search .input-group-text {
            transition: all 0.3s;
        }

        .modern-search:focus-within .input-group-text {
            background: #667eea !important;
            color: #fff !important;
        }

        /* Premium Badge */
        .total-users-badge {
            animation: pulseScale 2s ease-in-out infinite;
        }

        @keyframes pulseScale {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        /* Status Dot */
        .status-dot {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }

        @keyframes pulse {
            0% {
                opacity: 1;
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            50% {
                opacity: 0.8;
                box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
            }
            100% {
                opacity: 1;
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        /* Premium Pagination */
        .pagination-wrapper .pagination {
            margin: 0;
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 20px 0;
        }

        .pagination-wrapper .page-link {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            color: #667eea;
            font-weight: 600;
            padding: 12px 18px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .pagination-wrapper .page-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: left 0.3s;
            z-index: -1;
        }

        .pagination-wrapper .page-link:hover::before {
            left: 0;
        }

        .pagination-wrapper .page-link:hover {
            color: #fff;
            border-color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        }

        .pagination-wrapper .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: #fff;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        /* Empty State */
        .empty-state {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .modern-card {
                margin-bottom: 20px;
            }
            
            .modern-card:hover {
                transform: translateY(-5px) scale(1.01);
            }
        }

        /* Premium Box Header */
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
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { right: -50%; }
            100% { right: 150%; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cards are no longer clickable - only buttons work
            // This ensures only the "Login As User" button triggers login
        });
    </script>
@endsection

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
                            <div class="box-header with-border"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px 30px; border: none;">
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
                                                <span class="badge"
                                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-size: 16px; padding: 12px 20px; border-radius: 10px; font-weight: 500;">
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
                                                <form action="{{ route('admin.users.login-as', $user->id) }}" method="POST"
                                                    class="user-card-form " style="margin: 0;">
                                                    @csrf
                                                    <div class="box box-body user-card modern-card "
                                                        style="border-radius: 20px; border: 1px solid #e5e7eb; box-shadow: 0 4px 20px rgba(0,0,0,0.08); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; position: relative; overflow: hidden; background: #fff; padding: 0;">

                                                        <!-- Gradient Header -->
                                                        <div class="card-gradient"
                                                            style="position: relative; height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); z-index: 0; border-radius: 20px 20px 0 0;">
                                                        </div>

                                                        <!-- Login Click Area -->
                                                        <div class="card-click-area"
                                                            style="position: absolute; top: 0; left: 0; right: 0; bottom: 80px; z-index: 1; cursor: pointer;">
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
                                                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center mx-auto"
                                                                        style="width: 100px; height: 100px; font-size: 40px; border: 4px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.15); background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-weight: 600;">
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

                                                        <!-- Login Button (Prominent) -->
                                                        <div class="px-3 mb-3" style="position: relative; z-index: 2;">
                                                            <button type="submit" class="btn btn-block login-btn"
                                                                style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; font-weight: 600; font-size: 14px; padding: 13px; border: none; border-radius: 12px; transition: all 0.3s; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); width: 100%;"
                                                                title="Click anywhere on card or this button to login as {{ $user->name }}">
                                                                <i class="fa fa-sign-in-alt me-2"></i> Login As User
                                                            </button>
                                                        </div>

                                                        <!-- Action Buttons -->
                                                        <div class="px-3 pb-3" style="position: relative; z-index: 3;">
                                                            <a href="{{ route('admin.users.show', $user->id) }}"
                                                                class="btn btn-block action-btn"
                                                                style="background: #f3f4f6; color: #374151; font-weight: 600; padding: 12px; border: none; border-radius: 10px; transition: all 0.3s; text-align: center;"
                                                                title="View Details" onclick="event.stopPropagation();">
                                                                <i class="fa fa-eye me-2"></i> View Details
                                                            </a>
                                                        </div>
                                                    </div>
                                                </form>
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
                                                <a href="{{ route('admin.users.index') }}" class="btn"
                                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 12px 30px; border-radius: 10px; font-weight: 600; border: none;">
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
        .modern-card {
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .modern-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(102, 126, 234, 0.25) !important;
            border-color: #667eea !important;
        }

        .modern-card:hover .card-gradient {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%) !important;
        }

        .card-gradient {
            transition: all 0.4s ease;
        }

        .card-click-area {
            cursor: pointer;
        }

        .card-click-area:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .user-avatar img,
        .user-avatar div {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modern-card:hover .user-avatar img,
        .modern-card:hover .user-avatar div {
            transform: scale(1.1) rotate(2deg);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .stat-card {
            transition: all 0.3s ease;
        }

        .modern-card:hover .stat-card {
            transform: scale(1.03);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4) !important;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .flex-1 {
            flex: 1;
        }

        .gap-2 {
            gap: 8px;
        }

        .modern-search .form-control:focus,
        .modern-search .input-group-text {
            box-shadow: none;
        }

        .modern-search .form-control:focus {
            border-color: #667eea;
        }

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

        /* Pagination styling */
        .pagination-wrapper .pagination {
            margin: 0;
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .pagination-wrapper .page-link {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            color: #667eea;
            font-weight: 600;
            padding: 10px 16px;
            transition: all 0.3s;
        }

        .pagination-wrapper .page-link:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-color: #667eea;
            transform: translateY(-2px);
        }

        .pagination-wrapper .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
        }

        @media (max-width: 768px) {
            .modern-card {
                margin-bottom: 20px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Make card clickable - click anywhere on card to login
            document.querySelectorAll('.card-click-area').forEach(function(area) {
                area.addEventListener('click', function(e) {
                    const form = this.closest('.user-card-form');
                    if (form) {
                        form.submit();
                    }
                });
            });

            // Also make the entire card clickable (except action buttons)
            document.querySelectorAll('.modern-card').forEach(function(card) {
                card.addEventListener('click', function(e) {
                    // Don't trigger if clicking on action buttons or login button
                    if (!e.target.closest('.action-btn') &&
                        !e.target.closest('button[type="submit"].login-btn') &&
                        !e.target.closest('a[href*="show"]')) {
                        const form = this.closest('.user-card-form');
                        if (form) {
                            form.submit();
                        }
                    }
                });
            });
        });
    </script>
@endsection

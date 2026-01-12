<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    @if ($favicon)
        <link rel="icon" type="image/png"
            href="{{ str_starts_with($favicon, 'http') ? $favicon : asset('storage/' . $favicon) }}">
    @endif

    <!-- Default SEO Meta Tags -->
    <meta name="description"
        content="Trade on prediction markets, bet on real-world events, and explore thousands of markets on {{ $appName }}. Join the future of decentralized prediction markets.">
    <meta name="keywords" content="prediction markets, trading, betting, events, markets, polymarket, decentralized">
    <meta name="author" content="{{ $appName }}">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $appUrl }}">
    <meta property="og:title" content="{{ $appName }} - Prediction Markets & Trading Platform">
    <meta property="og:description"
        content="Trade on prediction markets, bet on real-world events, and explore thousands of markets on {{ $appName }}.">
    @if ($logo)
        <meta property="og:image" content="{{ str_starts_with($logo, 'http') ? $logo : asset('storage/' . $logo) }}">
    @endif
    <meta property="og:site_name" content="{{ $appName }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ $appUrl }}">
    <meta property="twitter:title" content="{{ $appName }} - Prediction Markets & Trading Platform">
    <meta property="twitter:description"
        content="Trade on prediction markets, bet on real-world events, and explore thousands of markets on {{ $appName }}.">
    @if ($logo)
        <meta property="twitter:image"
            content="{{ str_starts_with($logo, 'http') ? $logo : asset('storage/' . $logo) }}">
    @endif

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ $appUrl }}">

    @yield('meta_derails')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/responsive.css') }}">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="{{ asset('global/toastr/toastr.min.css') }}">
    <!-- Custom Styles (Production Optimized) -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/custom.min.css') }}">
    @livewireStyles
    @stack('style')
    
    <!-- Page Loader Styles -->
    <style>
        /* Page Loader - Only Spinner with Blurred Background */
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 99999;
            transition: opacity 0.5s ease, visibility 0.5s ease, backdrop-filter 0.5s ease;
        }
        
        #page-loader.hidden {
            opacity: 0;
            visibility: hidden;
            backdrop-filter: blur(0px);
            -webkit-backdrop-filter: blur(0px);
        }
        
        .loader-content {
            text-align: center;
            position: relative;
            z-index: 1;
        }
        
        .loader-spinner {
            width: 35px;
            height: 35px;
            border: 4px solid rgba(160, 174, 192, 0.2);
            border-top-color: var(--text-secondary, #a0aec0);
            border-radius: 50%;
            animation: spin 0.3s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .loader-text {
            display: none;
        }
        
        /* Blur body content when loader is active */
        body:not(.page-loaded) > *:not(#page-loader) {
            filter: blur(2px);
            transition: filter 0.3s ease;
        }
        
        body.page-loaded > *:not(#page-loader) {
            filter: blur(0px);
        }
        
        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Body fade in after loader */
        body.page-loaded {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>

<body class="dark-theme has-bottom-nav">
    <!-- Page Loader -->
    <div id="page-loader">
        <div class="loader-content">
            <div class="loader-spinner"></div>
        </div>
    </div>
    @if (session('admin_id'))
        <div class="admin-impersonation-banner"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 0; text-align: center; position: sticky; top: 0; z-index: 9999; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-shield me-2"></i>
                        <strong>You are logged in as: {{ $authUser->name ?? 'User' }}</strong>
                    </div>
                    <form action="{{ route('admin.users.return-to-admin') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Return to Admin Panel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <div id="header">
        <!-- Header -->
        <header>
            <div class="container">
                <div class="header-content d-lg-flex d-none">
                    <a href="{{ route('home') }}" class="logo">
                        @php
                            $logoExists = false;
                            if ($logo) {
                                if (str_starts_with($logo, 'http')) {
                                    $logoExists = true;
                                } else {
                                    $logoExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($logo);
                                }
                            }
                        @endphp
                        @if ($logo && $logoExists)
                            <img src="{{ str_starts_with($logo, 'http') ? $logo : asset('storage/' . $logo) }}"
                                alt="{{ $appName }}"
                                style="max-height: 40px; max-width: 150px; object-fit: contain;">
                        @else
                            <div class="logo-icon"><i class="fas fa-chart-line"></i></div>
                            <span>{{ $appName }}</span>
                        @endif
                    </a>
                    <div class="search-bar" style="position: relative;">
                        <livewire:header-search />
                    </div>
                    <div class="header-actions d-flex align-items-center justify-content-end">
                        @if (auth()->check())
                            @php
                                $wallet = $authUser?->wallet;
                                $portfolio = $wallet->portfolio ?? 0;
                                $cash = $wallet->balance ?? 0;
                            @endphp

                            <div class="wallet-summary">
                                <div class="wallet-item">
                                    <span class="wallet-label">Cash</span>
                                    <span class="wallet-value">${{ number_format($cash, 2) }}</span>
                                </div>
                            </div>

                            <button class="btn-header btn-deposit" id="depositBtn">
                                Deposit
                            </button>
                        @else
                            <a href="{{ route('login') }}" id="loginBtn" class="btn-header">Log In</a>
                            <a href="{{ route('register') }}" class="btn-header btn-sign-up">Sign Up</a>
                        @endif

                        <div class="header-menu-wrapper">
                            @if (auth()->check())
                                <div class="header-user-avatar" id="headerMenuTrigger" style="cursor: pointer;">
                                    <img src="{{ $authUser?->profile_image ? asset('storage/' . $authUser->profile_image) : asset('frontend/assets/images/default-avatar.png') }}"
                                        alt="{{ $authUser?->username ?? 'User' }}">
                                </div>
                            @else
                                <button class="header-menu-trigger" id="headerMenuTrigger" aria-label="More">
                                    <i class="fas fa-bars"></i>
                                </button>
                            @endif
                            <div class="header-menu-dropdown " id="headerMenuDropdown">
                                <div class="header-menu-section">
                                    @if (auth()->check())
                                        <div class="header-user">
                                            <div class="header-user-avatar">
                                                <img src="{{ $authUser?->profile_image ? asset('storage/' . $authUser->profile_image) : asset('frontend/assets/images/default-avatar.png') }}"
                                                    alt="{{ $authUser?->username ?? 'User' }}">
                                            </div>
                                            <div class="header-user-info">
                                                <a href="{{ route('profile.index') }}">
                                                    @if ($authUser?->name)
                                                        <div class="header-user-name">
                                                            {{ $authUser->name }}
                                                        </div>
                                                    @endif

                                                    <div class="header-user-name text-muted">
                                                        {{ $authUser?->username ?? 'User' }}
                                                    </div>
                                                </a>

                                            </div>
                                        </div>
                                        <div class="header-menu-divider">
                                        </div>
                                        <a href="{{ route('profile.index') }}">
                                            <div
                                                class="header-menu-item d-flex align-items-center justify-content-between w-100 ">
                                                <span class="header-menu-icon">
                                                    <i class="fas fa-user"></i>

                                                </span>
                                                <span class="header-menu-label">Profile</span>
                                            </div>
                                        </a>
                                    @endif

                                    <a href="#">
                                        <div
                                            class="header-menu-item d-flex align-items-center justify-content-between w-100 ">
                                            <span class="header-menu-icon">
                                                <i class="fas fa-trophy"></i>
                                            </span>
                                            <span class="header-menu-label">Leader Board</span>
                                        </div>
                                    </a>
                                    <div
                                        class="header-menu-item d-flex align-items-center justify-content-between w-100">
                                        <span class="header-menu-icon">
                                            <i class="fas fa-moon" style="color: var(--accent);"></i>
                                        </span>
                                        <span class="header-menu-label">Dark mode</span>
                                        <button class="theme-toggle-mobile" id="themeToggleMobile"
                                            aria-label="Toggle theme">
                                            <span class="header-menu-switch header-menu-switch--on">
                                                <span class="header-menu-switch-knob"></span>
                                            </span>
                                        </button>
                                    </div>

                                    <div class="header-menu-divider"></div>

                                    <a href="{{ route('terms-of-use') }}">
                                        <div
                                            class="header-menu-item d-flex align-items-center justify-content-between ">

                                            <span class="header-menu-label">Terms and Conditions</span>
                                        </div>
                                    </a>
                                    <a href="{{ route('privacy-policy') }}">
                                        <div
                                            class="header-menu-item d-flex align-items-center justify-content-between ">
                                            <span class="header-menu-label">Privacy Policy</span>
                                        </div>
                                    </a>


                                    @if (auth()->check())
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="header-menu-item d-flex align-items-center justify-content-between text-da ">
                                                <span class="header-menu-icon">
                                                    <i class="fas fa-sign-out-alt"></i>
                                                </span>
                                                <span class="header-menu-label">Logout</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="header-content d-lg-none d-block" style='padding: 10px 0 0 0;'>
                    <div class="row align-items-center justify-content-between">
                        <div class="col-5 text-start">
                            <a href="{{ route('home') }}" class="logo text-start">
                                @php
                                    $logoExistsMobile = false;
                                    if ($logo) {
                                        if (str_starts_with($logo, 'http')) {
                                            $logoExistsMobile = true;
                                        } else {
                                            $logoExistsMobile = \Illuminate\Support\Facades\Storage::disk(
                                                'public',
                                            )->exists($logo);
                                        }
                                    }
                                @endphp
                                @if ($logo && $logoExistsMobile)
                                    <img src="{{ str_starts_with($logo, 'http') ? $logo : asset('storage/' . $logo) }}"
                                        alt="{{ $appName }}"
                                        style="max-height: 35px; max-width: 120px; object-fit: contain;">
                                @else
                                    <div class="logo-icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <span>{{ $appName }}</span>
                                @endif
                            </a>
                        </div>
                        <div class="col-7">
                            <div class="header-actions d-flex align-items-center justify-content-end">

                                <div class="header-menu-wrapper">
                                    @if (auth()->check())
                                        <div class="header-user-avatar" id="moreMenuBtn" style="cursor: pointer;">
                                            <img src="{{ $authUser?->profile_image ? asset('storage/' . $authUser->profile_image) : asset('frontend/assets/images/default-avatar.png') }}"
                                                alt="{{ $authUser?->username ?? 'User' }}">
                                        </div>
                                    @else
                                        <a href="{{ route('login') }}" id="loginBtn" class="btn-header">Log In</a>
                                        <a href="{{ route('register') }}" class="btn-header btn-sign-up">Sign Up</a>
                                    @endif
                                    <div class="header-menu-dropdown " id="headerMenuDropdown">
                                        <div class="header-menu-section">
                                            @if (auth()->check())
                                                <div class="header-user">
                                                    <div class="header-user-avatar">
                                                        <img src="{{ $authUser?->profile_image ? asset('storage/' . $authUser->profile_image) : asset('frontend/assets/images/default-avatar.png') }}"
                                                            alt="{{ $authUser?->username ?? 'User' }}">
                                                    </div>
                                                    <div class="header-user-info">
                                                        <a href="{{ route('profile.index') }}">
                                                            <div class="header-user-name">
                                                                {{ $authUser?->username ?? 'User' }}
                                                            </div>
                                                        </a>
                                                        @if ($authUser?->email)
                                                            <div class="header-user-sub">
                                                                {{ $authUser->email }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="header-menu-divider"></div>
                                            @endif

                                            <a href="#">
                                                <div
                                                    class="header-menu-item d-flex align-items-center justify-content-between w-100 ">
                                                    <span class="header-menu-icon">
                                                        <i class="fas fa-trophy"></i>
                                                    </span>
                                                    <span class="header-menu-label">Leader Board</span>
                                                </div>
                                            </a>
                                            <div
                                                class="header-menu-item d-flex align-items-center justify-content-between w-100">
                                                <span class="header-menu-icon">
                                                    <i class="fas fa-moon" style="color: var(--accent);"></i>
                                                </span>
                                                <span class="header-menu-label">Dark mode</span>
                                                <button class="theme-toggle-mobile" id="themeToggleMobile"
                                                    aria-label="Toggle theme">
                                                    <span class="header-menu-switch header-menu-switch--on">
                                                        <span class="header-menu-switch-knob"></span>
                                                    </span>
                                                </button>
                                            </div>

                                            <div class="header-menu-divider"></div>

                                            <a href="#">
                                                <div
                                                    class="header-menu-item d-flex align-items-center justify-content-between ">

                                                    <span class="header-menu-label">Terms and Conditions</span>
                                                </div>
                                            </a>
                                            <a href="#">
                                                <div
                                                    class="header-menu-item d-flex align-items-center justify-content-between ">
                                                    <span class="header-menu-label">Privacy Policy</span>
                                                </div>
                                            </a>


                                            @if (auth()->check())
                                                <form action="{{ route('logout') }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="header-menu-item d-flex align-items-center justify-content-between text-danger ">
                                                        <span class="header-menu-icon">
                                                            <i class="fas fa-sign-out-alt"></i>
                                                        </span>
                                                        <span class="header-menu-label">Logout</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Navigation -->
        <div class="container nav-container">
            <div class="nav-content">
                <livewire:category-navigation :category="request()->route('category')" />
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="mobile-bottom-nav d-lg-none d-flex">
        <a href="{{ route('home') }}" class="mobile-nav-item active">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="#" class="mobile-nav-item">
            <i class="fas fa-search"></i>
            <span>Search</span>
        </a>
        <a href="{{ route('breaking') }}" class="mobile-nav-item">
            <i class="fas fa-sync-alt"></i>
            <span>Breaking</span>
        </a>
        <a href="{{ route('profile.index') }}" class="mobile-nav-item">
            <i class="fas fa-wallet"></i>
            <span>${{ $authUser?->wallet?->balance ?? '0.00' }}</span>
        </a>
    </div>

    <!-- Mobile Search Popup -->
    <div class="mobile-search-overlay" id="mobileSearchOverlay"></div>
    <div class="mobile-search-popup" id="mobileSearchPopup">
        <livewire:mobile-search />
    </div>

    <!-- More Menu Sidebar -->
    <div class="more-menu-overlay" id="moreMenuOverlay"></div>
    <div class="more-menu-sidebar" id="moreMenuSidebar">
        <div class="more-menu-header">
            @php
                $logoExistsSidebar = false;
                if ($logo) {
                    if (str_starts_with($logo, 'http')) {
                        $logoExistsSidebar = true;
                    } else {
                        $logoExistsSidebar = \Illuminate\Support\Facades\Storage::disk('public')->exists($logo);
                    }
                }
            @endphp
            @if ($logo && $logoExistsSidebar)
                <a href="{{ route('home') }}" class="logo-link">
                    <img src="{{ str_starts_with($logo, 'http') ? $logo : asset('storage/' . $logo) }}"
                        alt="{{ $appName }}" style="max-height: 35px; max-width: 120px; object-fit: contain;">
                </a>
            @else
                <h3>Menu</h3>
            @endif
            <button class="close-menu-btn" id="closeMenuBtn"><i class="fas fa-times"></i></button>
        </div>

        @if (auth()->check())
            <div class="header-user p-0">
                <div class="header-user-avatar">
                    <img src="{{ isset($authUser->profile_image) ? asset('storage/' . $authUser->profile_image) : asset('frontend/assets/images/default-avatar.png') }}"
                        alt="{{ $authUser?->username ?? 'User' }}">
                </div>
                <div class="header-user-info">
                    <a href="{{ route('profile.index') }}">
                        <div class="header-user-name">
                            {{ $authUser?->username ?? 'User' }}
                        </div>
                        @if ($authUser?->email)
                            <div class="header-user-sub">
                                {{ $authUser->email }}
                            </div>
                        @endif
                    </a>
                </div>
            </div>
            <div class="more-menu-divider"></div>
        @endif

        <div class="more-menu-links">
            <a href="#"><i class="fas fa-trophy" style="color: #ffb11a;"></i> Leaderboard</a>
            <a href="#"><i class="fas fa-dollar-sign" style="color: #00c853;"></i> Rewards</a>
        </div>
        <div class="more-menu-divider"></div>
        <div class="more-menu-links">
            <a href="{{ route('terms-of-use') }}">Terms of Use</a>
        </div>
        <div class="more-menu-social" style="justify-content: start;">
            <button class="theme-toggle-mobile" id="themeToggleMobile" aria-label="Toggle theme">
                <span class="header-menu-switch header-menu-switch--on">
                    <span class="header-menu-switch-knob"></span>
                </span>
            </button>
        </div>
        <div class="more-menu-divider"></div>

        @if (auth()->check())
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="header-menu-item header-menu-item-danger">
                    <span class="header-menu-icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </span>
                    <span class="header-menu-label">Logout</span>
                </button>
            </form>
        @else
            <div class="header-menu-section">
                <a href="{{ route('login') }}">
                    <div class="header-menu-item d-flex align-items-center justify-content-between w-100">
                        <span class="header-menu-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </span>
                        <span class="header-menu-label">Log in</span>
                    </div>
                </a>
                <a href="{{ route('register') }}">
                    <div class="header-menu-item d-flex align-items-center justify-content-between w-100 text-primary">
                        <span class="header-menu-icon">
                            <i class="fas fa-user-plus"></i>
                        </span>
                        <span class="header-menu-label">Sign up</span>
                    </div>
                </a>
            </div>
        @endif

    </div>

    @yield('content')

    <!-- Deposit Modal -->
    <div class="deposit-modal-overlay" id="depositModalOverlay"></div>
    <div class="deposit-modal-popup" id="depositModalPopup">
        @livewire('deposit-request')
    </div>



    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-left">
                    <p class="footer-copyright">Adventure One QSS Inc. © 2025</p>
                    <div class="footer-links">
                        <a href="{{ route('privacy-policy') }}" class="footer-link">Privacy Policy</a>
                        <span class="footer-separator">•</span>
                        <a href="{{ route('terms-of-use') }}" class="footer-link">Terms of Use</a>
                        <span class="footer-separator">•</span>
                        <a href="{{ route('faq') }}" class="footer-link">FAQ</a>
                        <span class="footer-separator">•</span>
                        <a href="{{ route('contact') }}" class="footer-link">Contact</a>
                    </div>
                </div>
                <div class="footer-right">
                    @if ($socialMediaLinks && $socialMediaLinks->count() > 0)
                        <div class="footer-social">
                            @foreach ($socialMediaLinks as $link)
                                @php
                                    $iconClass = match ($link->platform) {
                                        'facebook' => 'fab fa-facebook',
                                        'twitter' => 'fa-brands fa-x',
                                        'instagram' => 'fab fa-instagram',
                                        'telegram' => 'fab fa-telegram',
                                        'whatsapp' => 'fab fa-whatsapp',
                                        'youtube' => 'fab fa-youtube',
                                        'linkedin' => 'fab fa-linkedin',
                                        default => 'fas fa-link',
                                    };
                                    $ariaLabel = ucfirst(
                                        $link->platform === 'twitter' ? 'X (Twitter)' : $link->platform,
                                    );
                                @endphp
                                <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                                    class="footer-social-link" aria-label="{{ $ariaLabel }}">
                                    <i class="{{ $iconClass }}"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('frontend/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/bootstrap.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <!-- Frontend App JS (Production Optimized) -->
    <script src="{{ asset('frontend/assets/js/frontend-app.min.js') }}"></script>
    
    <!-- Page Loader Script -->
    <script>
        // Page Loader - Hide when page is fully loaded
        window.addEventListener('load', function() {
            const loader = document.getElementById('page-loader');
            const body = document.body;
            
            // Add fade out animation
            if (loader) {
                loader.classList.add('hidden');
                body.classList.add('page-loaded');
                
                // Remove loader from DOM after animation
                setTimeout(function() {
                    loader.style.display = 'none';
                }, 500);
            }
        });
        
        // Fallback: Hide loader after 3 seconds even if page hasn't fully loaded
        setTimeout(function() {
            const loader = document.getElementById('page-loader');
            if (loader && !loader.classList.contains('hidden')) {
                loader.classList.add('hidden');
                document.body.classList.add('page-loaded');
                setTimeout(function() {
                    loader.style.display = 'none';
                }, 500);
            }
        }, 3000);
        
        // Show loader on Livewire navigation
        document.addEventListener('livewire:navigate', function() {
            const loader = document.getElementById('page-loader');
            if (loader) {
                loader.style.display = 'flex';
                loader.classList.remove('hidden');
            }
        });
        
        // Hide loader when Livewire finishes loading
        document.addEventListener('livewire:navigated', function() {
            setTimeout(function() {
                const loader = document.getElementById('page-loader');
                if (loader) {
                    loader.classList.add('hidden');
                    setTimeout(function() {
                        loader.style.display = 'none';
                    }, 500);
                }
            }, 300);
        });
    </script>
    
    <script>
        // Initialize user balance for trading panel
        @auth
        @php
            $userWallet = $authUser?->wallet;
            $userBalance = $userWallet ? $userWallet->balance : 0;
        @endphp
        window.userBalance = {{ $userBalance }};
        @else
        window.userBalance = 0;
        @endauth
    </script>
    <!-- Legacy inline script (kept for Blade variables) -->
    <script>
        // FRONTEND JAVASCRIPT - Legacy inline code
        (function($) {
            'use strict';

            // Cache frequently used selectors
            const $doc = $(document);
            const $win = $(window);
            const $html = $('html');
            const $body = $('body');
            const $themeToggle = $('#themeToggle');
            const $headerMenuTrigger = $('#headerMenuTrigger');
            const $headerMenuDropdown = $('#headerMenuDropdown');

            // THEME TOGGLE 
            (function() {
                function setHeaderThemeSwitch(theme) {
                    const $switch = $('.header-menu-switch');
                    if (!$switch.length) return;
                    $switch.toggleClass('header-menu-switch--on', theme === 'dark');
                }

                function applyTheme(theme) {
                    const isLight = theme === 'light';
                    $html.toggleClass('light-mode', isLight);
                    $body.toggleClass('light-theme', isLight).toggleClass('dark-theme', !isLight);
                    $themeToggle.html(`<i class="fas fa-${isLight ? 'sun' : 'moon'}"></i>`);
                    $('#themeToggleMobile').html(`<i class="fas fa-${isLight ? 'moon' : 'sun'}"></i>`);
                    setHeaderThemeSwitch(theme);
                    localStorage.setItem('theme', theme);
                }

                function initTheme() {
                    applyTheme(localStorage.getItem('theme') || 'dark');
                }

                function toggleTheme() {
                    applyTheme($html.hasClass('light-mode') ? 'dark' : 'light');
                }

                initTheme();
                $themeToggle.on('click', e => {
                    e.preventDefault();
                    toggleTheme();
                });
                $doc.on('click', '.header-menu-toggle', e => {
                    e.preventDefault();
                    e.stopPropagation();
                    $themeToggle.trigger('click');
                });
                $('#themeToggleMobile').on('click', toggleTheme);
            })();

            // HEADER MENU (Optimized)
            (function() {
                if ($headerMenuTrigger.length && $headerMenuDropdown.length) {
                    const closeHeaderMenu = () => $headerMenuDropdown.removeClass('active');
                    $headerMenuTrigger.on('click', e => {
                        e.preventDefault();
                        e.stopPropagation();
                        $headerMenuDropdown.toggleClass('active');
                    });
                    $doc.on('click', closeHeaderMenu);
                    $headerMenuDropdown.on('click', e => e.stopPropagation());
                    $doc.on('keydown', e => {
                        if (e.key === 'Escape') closeHeaderMenu();
                    });
                }
            })();

            // FILTERS & DROPDOWNS (Optimized with delegation)
            (function() {
                const adjustDropdownPosition = ($dropdown) => {
                    if (!$dropdown.length) return;
                    $dropdown.css({
                        left: 'auto',
                        right: 'auto'
                    });
                    setTimeout(() => {
                        const offset = $dropdown.offset();
                        if (offset && offset.left + $dropdown.outerWidth() > $win.width() - 20) {
                            $dropdown.css({
                                left: 'auto',
                                right: '0'
                            });
                        }
                    }, 10);
                };

                $doc.on('click', '#filterToggleBtn', function() {
                    $(this).toggleClass('active');
                    $('.filter-options-row').toggleClass('active');
                });

                $doc.on('click', '.filter-btn', function() {
                    $('.filter-btn').removeClass('active');
                    $(this).addClass('active');
                });

                $doc.on('click', '.sort-by-btn, .frequency-btn, .status-btn', function(e) {
                    e.stopPropagation();
                    const $wrapper = $(this).closest('.filter-dropdown-wrapper');
                    $('.filter-dropdown-wrapper').not($wrapper).removeClass('active');
                    $wrapper.toggleClass('active');
                    if ($wrapper.hasClass('active')) {
                        adjustDropdownPosition($wrapper.find(
                            '.sort-dropdown-menu, .frequency-dropdown-menu, .status-dropdown-menu'));
                    }
                });

                $doc.on('click', '.sort-option, .frequency-option, .status-option', function(e) {
                    e.stopPropagation();
                    const $this = $(this);
                    const isSort = $this.hasClass('sort-option');
                    const isFreq = $this.hasClass('frequency-option');
                    const text = isSort ? $this.find('span').first().text() : $this.text();
                    $this.siblings().removeClass('active');
                    $this.addClass('active');
                    if (isSort) $('.sort-by-btn .filter-option-text').text(text);
                    if (isFreq) $('.frequency-btn .filter-option-text').text(text);
                    if (!$this.hasClass('status-option')) $this.closest('.filter-dropdown-wrapper')
                        .removeClass('active');
                });

                $doc.on('click', e => {
                    if (!$(e.target).closest('.filter-dropdown-wrapper').length) {
                        $('.filter-dropdown-wrapper').removeClass('active');
                    }
                });

                $win.on('resize', () => {
                    $('.filter-dropdown-wrapper.active').each(function() {
                        adjustDropdownPosition($(this).find(
                            '.sort-dropdown-menu, .frequency-dropdown-menu, .status-dropdown-menu'
                        ));
                    });
                });

                // Filter scroll
                const $filtersSection = $('#filtersSection');
                if ($filtersSection.length) {
                    const updateScrollButtons = () => {
                        const scrollLeft = $filtersSection.scrollLeft();
                        const maxScroll = $filtersSection[0].scrollWidth - $filtersSection[0].clientWidth;
                        $('#filterScrollLeft').toggleClass('disabled', scrollLeft <= 0);
                        $('#filterScrollRight').toggleClass('disabled', scrollLeft >= maxScroll - 1);
                    };
                    $('#filterScrollLeft, #filterScrollRight').on('click', function() {
                        const dir = $(this).attr('id') === 'filterScrollLeft' ? -1 : 1;
                        $filtersSection.animate({
                            scrollLeft: $filtersSection.scrollLeft() + (dir * 200)
                        }, 300, updateScrollButtons);
                    });
                    $filtersSection.on('scroll', updateScrollButtons);
                    $win.on('resize', () => setTimeout(updateScrollButtons, 100));
                    updateScrollButtons();
                }
            })();

            // NAVIGATION (Optimized with Overflow Detection)
            (function() {
                const $navContent = $('.nav-content');
                const $navItemsWrapper = $('.nav-items-wrapper');
                const $moreDropdown = $('#moreNavDropdown');
                const $moreDropdownMenu = $('#moreDropdownMenu');
                const $moreBtn = $('#moreNavBtn');

                function handleNavOverflow() {
                    // Only handle overflow on desktop (lg and above)
                    if ($win.width() < 992) {
                        // On mobile, restore all items and hide "More" button
                        $moreDropdown.hide();
                        $navItemsWrapper.find('.nav-item').each(function() {
                            const $item = $(this);
                            if ($item.data('moved-to-dropdown')) {
                                $item.data('moved-to-dropdown', false);
                            }
                        });
                        return;
                    }

                    // Show "More" button on desktop
                    $moreDropdown.show();

                    // Reset: move all items back to main nav (before More button)
                    const $moreDropdownInWrapper = $navItemsWrapper.find('#moreNavDropdown');
                    $moreDropdownMenu.find('.dropdown-item').each(function() {
                        const $item = $(this);
                        const originalItem = $item.data('original-item');
                        if (originalItem) {
                            $item.remove();
                            // Insert before More button if it exists, otherwise append
                            if ($moreDropdownInWrapper.length) {
                                originalItem.show().insertBefore($moreDropdownInWrapper);
                            } else {
                                originalItem.show().appendTo($navItemsWrapper);
                            }
                            originalItem.removeData('moved-to-dropdown');
                        }
                    });

                    // Also handle any nav-items that might be directly in dropdown
                    $moreDropdownMenu.find('.nav-item').each(function() {
                        const $item = $(this);
                        if (!$item.closest('.nav-items-wrapper').length) {
                            if ($moreDropdownInWrapper.length) {
                                $item.insertBefore($moreDropdownInWrapper);
                            } else {
                                $item.appendTo($navItemsWrapper);
                            }
                            $item.removeData('moved-to-dropdown');
                        }
                    });

                    // Ensure "More" button is inside nav-items-wrapper
                    if (!$moreDropdown.closest('.nav-items-wrapper').length) {
                        $moreDropdown.appendTo($navItemsWrapper);
                    }

                    // Get all nav items (excluding Trending, New, divider, and More button)
                    const $allNavItems = $navItemsWrapper.find('.nav-item').filter(function() {
                        const href = $(this).attr('href') || '';
                        const isMoreBtn = $(this).attr('id') === 'moreNavBtn';
                        return !href.includes('trending') && !href.includes('new') && !isMoreBtn;
                    });

                    const totalItems = $allNavItems.length;
                    const maxVisibleItems = 12; // Show first 12 items, rest go to "More"

                    // If we have more than 12 items, move the rest to dropdown
                    if (totalItems > maxVisibleItems) {
                        let visibleCount = 0;
                        let movedCount = 0;
                        let $lastVisibleItem = null;

                        $allNavItems.each(function() {
                            const $item = $(this);

                            if ($item.data('moved-to-dropdown')) {
                                // Already moved, skip
                                return;
                            }

                            visibleCount++;

                            if (visibleCount > maxVisibleItems) {
                                // Create dropdown item
                                const $dropdownItem = $('<a>')
                                    .addClass('dropdown-item')
                                    .attr('href', $item.attr('href'))
                                    .html($item.html())
                                    .data('original-item', $item);

                                // Copy active class
                                if ($item.hasClass('active')) {
                                    $dropdownItem.addClass('active');
                                }

                                // Add click handler
                                $dropdownItem.on('click', function(e) {
                                    const href = $(this).attr('href');
                                    if (href && href !== '#' && href !== 'index.html') {
                                        $('.nav-item, .dropdown-item').removeClass('active');
                                        $(this).addClass('active');
                                        $moreDropdownMenu.removeClass('active');
                                        $moreDropdown.removeClass('active');
                                    }
                                });

                                $moreDropdownMenu.append($dropdownItem);
                                $item.hide().data('moved-to-dropdown', true);
                                movedCount++;
                            } else {
                                // Track the last visible item
                                $lastVisibleItem = $item;
                            }
                        });

                        // Position "More" button after the last visible item
                        if (movedCount > 0 && $lastVisibleItem && $lastVisibleItem.length) {
                            $moreDropdown.insertAfter($lastVisibleItem);
                            $moreBtn.show();
                        } else {
                            $moreBtn.hide();
                        }
                    } else {
                        // Less than 12 items, hide "More" button
                        $moreBtn.hide();
                    }
                }

                // Function to adjust dropdown position
                function adjustDropdownPosition() {
                    if (!$moreDropdownMenu.hasClass('active')) return;

                    const $dropdown = $moreDropdownMenu;
                    const dropdownWidth = $dropdown.outerWidth();
                    const dropdownLeft = $moreDropdown.offset().left;
                    const windowWidth = $win.width();

                    // Reset positioning
                    $dropdown.css({
                        left: '0',
                        right: 'auto'
                    });

                    // Check if dropdown goes off-screen to the right
                    if (dropdownLeft + dropdownWidth > windowWidth - 20) {
                        // Position from right edge
                        const rightPosition = windowWidth - dropdownLeft - $moreDropdown.outerWidth();
                        $dropdown.css({
                            left: 'auto',
                            right: rightPosition + 'px'
                        });
                    }
                }

                // Handle navigation clicks
                $doc.on('click', '.nav-item', function(e) {
                    const $this = $(this);
                    const href = $this.attr('href');
                    const isMoreBtn = $this.attr('id') === 'moreNavBtn';
                    const isProfileBtn = $this.attr('id') === 'profileNavBtn';

                    if (isMoreBtn || isProfileBtn) {
                        e.preventDefault();
                        if (isMoreBtn) {
                            $moreDropdownMenu.toggleClass('active');
                            $moreDropdown.toggleClass('active');
                            $('#profileNavDropdownMenu').removeClass('active');

                            // Adjust position when opening
                            if ($moreDropdownMenu.hasClass('active')) {
                                setTimeout(adjustDropdownPosition, 10);
                            }
                        } else {
                            $('#profileNavDropdownMenu').toggleClass('active');
                            $moreDropdownMenu.removeClass('active');
                            $moreDropdown.removeClass('active');
                        }
                        return;
                    }

                    if (!href || href === '' || href === '#') e.preventDefault();
                    if (!$this.closest('.more-dropdown-menu, .profile-nav-dropdown-menu').length) {
                        $('.nav-item, .dropdown-item').removeClass('active');
                        $this.addClass('active');
                        $moreDropdownMenu.removeClass('active');
                    }
                });

                $doc.on('click', e => {
                    if (!$(e.target).closest('.nav-item-dropdown').length) {
                        $moreDropdownMenu.removeClass('active');
                        $moreDropdown.removeClass('active');
                    }
                });

                // Handle overflow on load and resize
                $win.on('resize', () => {
                    setTimeout(() => {
                        handleNavOverflow();
                        adjustDropdownPosition();
                    }, 100);
                });

                // Initial check
                setTimeout(handleNavOverflow, 100);
            })();

            // MOBILE NAVIGATION (Optimized)
            (function() {
                const checkScreenSize = () => {
                    const isMobile = $win.width() <= 768;
                    $body.toggleClass('mobile', isMobile);
                    $('.mobile-bottom-nav').toggleClass('active', isMobile);
                };

                $doc.on('click', '.mobile-nav-item', function(e) {
                    const text = $(this).find('span').text().trim();

                    if (text === 'Search') {
                        e.preventDefault();
                        const isOpen = $('#mobileSearchPopup').hasClass('active');
                        if (isOpen) {
                            // Close if already open
                            $('#mobileSearchPopup, #mobileSearchOverlay').removeClass('active');
                            $body.css('overflow', '');
                            $('.mobile-nav-item').removeClass('active');
                            $('.mobile-nav-item:has(span:contains("Home"))').addClass('active');
                        } else {
                            // Open search popup
                            $('.mobile-nav-item').removeClass('active');
                            $(this).addClass('active');
                            $('#moreMenuSidebar, #moreMenuOverlay').removeClass('active');
                            $('#mobileSearchPopup, #mobileSearchOverlay').addClass('active');
                            $body.css('overflow', 'hidden');
                            setTimeout(() => $('#mobileSearchInput').focus(), 300);
                        }
                    } else if (text === 'More') {
                        e.preventDefault();
                        $('.mobile-nav-item').removeClass('active');
                        $(this).addClass('active');
                        $('#mobileSearchPopup, #mobileSearchOverlay').removeClass('active');
                        $('#moreMenuSidebar, #moreMenuOverlay').addClass('active');
                        $body.css('overflow', 'hidden');
                    }
                });

                $doc.on('click', '#moreMenuBtn, #closeMenuBtn, #moreMenuOverlay', function(e) {
                    if ($(this).attr('id') === 'moreMenuBtn') {
                        e.preventDefault();
                        $('#mobileSearchPopup, #mobileSearchOverlay').removeClass('active');
                        $('.mobile-nav-item').removeClass('active');
                        $('#moreMenuSidebar, #moreMenuOverlay').addClass('active');
                        $body.css('overflow', 'hidden');
                    } else {
                        $('#moreMenuSidebar, #moreMenuOverlay').removeClass('active');
                        $body.css('overflow', '');
                    }
                });

                $doc.on('click', '#mobileSearchClose, #mobileSearchOverlay', function(e) {
                    e.preventDefault();
                    $('#mobileSearchPopup, #mobileSearchOverlay').removeClass('active');
                    $body.css('overflow', '');
                    $('.mobile-nav-item').removeClass('active');
                    $('.mobile-nav-item:has(span:contains("Home"))').addClass('active');
                });

                $win.on('resize', checkScreenSize);
                checkScreenSize();
            })();

            // SEARCH KEYBOARD SHORTCUTS (Livewire handles search)
            (function() {
                $doc.on('keydown', function(e) {
                    const $search = $(
                        '#marketSearchInput, input[wire\\:model\\.live\\.debounce\\.300ms="search"]');
                    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                        e.preventDefault();
                        $('#marketSearchInput').focus();
                    }
                    if (e.key === 'Escape' && $search.is(':focus')) {
                        $('#marketSearchInput').val('');
                        const $livewireSearch = $('input[wire\\:model\\.live\\.debounce\\.300ms="search"]');
                        if ($livewireSearch.length) {
                            $livewireSearch.val('').trigger('input');
                        }
                    }
                });
            })();

            // NOTIFICATIONS (Optimized)
            (function() {
                window.showNotification = function(message, type = 'info') {
                    const colors = {
                        success: {
                            bg: '#00C853',
                            color: '#fff'
                        },
                        danger: {
                            bg: '#FF4757',
                            color: '#fff'
                        },
                        info: {
                            bg: '#ffb11a',
                            color: '#000'
                        }
                    };
                    const style = colors[type] || colors.info;
                    const $notif = $('<div>').text(message).css({
                        position: 'fixed',
                        top: '100px',
                        right: '20px',
                        padding: '12px 20px',
                        background: style.bg,
                        color: style.color,
                        borderRadius: '6px',
                        fontWeight: '600',
                        zIndex: '1000',
                        boxShadow: '0 4px 12px rgba(0,0,0,0.2)',
                        animation: 'slideIn 0.3s ease'
                    });
                    $body.append($notif);
                    setTimeout(() => {
                        $notif.css('animation', 'slideOut 0.3s ease');
                        setTimeout(() => $notif.remove(), 300);
                    }, 2000);
                };
            })();

            // MARKET CARDS & ACTIONS (Optimized)
            (function() {
                $doc.on('click', '.yes-btn, .no-btn', function(e) {
                    e.stopPropagation();
                    const $btn = $(this);
                    $btn.css('transform', 'scale(0.95)');
                    setTimeout(() => $btn.css('transform', 'scale(1.05)'), 100);
                    showNotification(`Vote recorded: ${$btn.hasClass('yes-btn') ? 'YES' : 'NO'}`, $btn
                        .hasClass('yes-btn') ? 'success' : 'danger');
                });

                $doc.on('click', '.action-btn', function(e) {
                    e.stopPropagation();
                    const $icon = $(this).find('i');
                    if ($icon.hasClass('fa-bookmark')) {
                        const $btn = $(this);
                        const isActive = $btn.css('opacity') !== '0.5';
                        $btn.css({
                            opacity: isActive ? '0.5' : '1',
                            color: isActive ? '' : 'var(--accent)'
                        });
                        showNotification(isActive ? 'Bookmark removed' : 'Market bookmarked', isActive ?
                            'danger' : 'success');
                    }
                });
            })();

            // MARKET DETAIL PAGE (Optimized)
            (function() {
                    if (!$('#marketChart').length && !$('.outcome-row').length) return;

                    // Ensure trading panel is closed on page load
                    $('#tradingPanel').removeClass('active');
                    $('#mobilePanelOverlay').removeClass('active');
                    $body.css('overflow', '');

                    let currentShares = 0,
                        isBuy = true,
                        isYes = true,
                        isLimitOrder = false,
                        limitPrice = 0,
                        userBalance = window.userBalance || 0;

                const updateSummary = () => {
                    // Get market price (convert from cents to decimal 0-1)
                    let marketPrice;
                    if (isLimitOrder && limitPrice > 0) {
                        marketPrice = limitPrice / 100;
                    } else {
                        if (window.currentYesPrice !== undefined && window.currentNoPrice !== undefined) {
                            marketPrice = isYes ? window.currentYesPrice / 100 : window.currentNoPrice / 100;
                        } else {
                            marketPrice = isYes ? 0.5 : 0.5; // Default
                        }
                    }
                    
                    // Ensure price is valid (0.0001 to 0.9999)
                    marketPrice = Math.max(0.0001, Math.min(0.9999, marketPrice));
                    
                    // Investment amount (currentShares is the investment in USD)
                    const investment = parseFloat(currentShares) || 0;
                    
                    // Polymarket-style calculations
                    // Calculate shares: shares = investment ÷ market_price
                    const shares = marketPrice > 0 ? investment / marketPrice : 0;
                    
                    // Potential payout: payout = shares × 1 (per share payout is $1)
                    const payout = shares * 1;
                    
                    // Update Potential Payout display
                    $('#potentialPayout').text(`$${payout.toFixed(2)}`);
                    
                    // Validation: Check if investment exceeds portfolio balance
                    const cost = shares * marketPrice;
                    const portfolioBefore = userBalance || 0;
                    if (cost > portfolioBefore) {
                        $('#executeTrade').prop('disabled', true).addClass('disabled');
                    } else {
                        $('#executeTrade').prop('disabled', false).removeClass('disabled');
                    }
                };

                const updateShares = (amount) => {
                    currentShares = Math.max(0, currentShares + amount);
                    $('#sharesInput').val(currentShares);
                    updateSummary();
                };
                
                // Initialize summary on page load
                updateSummary();

                const updateOutcomePrice = () => {
                    if (window.currentYesPrice !== undefined && window.currentNoPrice !== undefined) {
                        const price = isYes ? window.currentYesPrice : window.currentNoPrice;
                        $('#limitPrice').val(price);
                        limitPrice = price;
                    }
                };

                let currentMarketId = null;

                const populateTradingPanel = ($row, isYesSelected, isMobile) => {
                    $('.outcome-row').removeClass('active selected');
                    $row.addClass('active selected');
                    // Get outcome name - try multiple selectors
                    let outcomeName = $row.find('.outcome-name').text().trim();
                    if (!outcomeName) {
                        outcomeName = $row.find('.outcome-text .outcome-name').text().trim();
                    }
                    if (!outcomeName) {
                        outcomeName = $row.data('outcome-name') || '';
                    }

                    let marketTitle = $('.market-title').text().trim() || $('#panelMarketTitle').text().trim();
                    const $yesBtn = $row.find('.btn-yes');
                    const $noBtn = $row.find('.btn-no');
                    const yesPrice = parseFloat($yesBtn.text().match(/([\d.]+)¢/)?.[1] || 0);
                    const noPrice = parseFloat($noBtn.text().match(/([\d.]+)¢/)?.[1] || 0);

                    // Get market ID from row or panel
                    currentMarketId = $row.data('market-id') || $('#tradingPanel').data('market-id');

                    // Update panel elements
                    if (marketTitle) {
                        $('#panelMarketTitle').text(marketTitle);
                    }
                    if (outcomeName) {
                        $('#panelOutcomeName').text(outcomeName);
                        // Also update panelOutcomeTitle if it exists (for backward compatibility)
                        if ($('#panelOutcomeTitle').length) {
                            $('#panelOutcomeTitle').text(outcomeName);
                        }
                    }
                    if (isMobile) {
                        $('#buyTab').addClass('active');
                        $('#sellTab').removeClass('active');
                        $('#actionTabs').addClass('buy-only');
                    }
                    // Update button text with prices
                    $('#yesBtn').html(`Yes ${yesPrice.toFixed(1)}¢`).attr('data-price', yesPrice);
                    $('#noBtn').html(`No ${noPrice.toFixed(1)}¢`).attr('data-price', noPrice);

                    if (isYesSelected) {
                        $('#yesBtn').addClass('active');
                        $('#noBtn').removeClass('active');
                        $('#limitPrice').val(yesPrice);
                        $('#avgPriceValue').text(yesPrice.toFixed(0) + '¢');
                        isYes = true;
                        $('#executeTrade').text('Buy Yes');
                    } else {
                        $('#noBtn').addClass('active');
                        $('#yesBtn').removeClass('active');
                        $('#limitPrice').val(noPrice);
                        $('#avgPriceValue').text(noPrice.toFixed(0) + '¢');
                        isYes = false;
                        $('#executeTrade').text('Buy No');
                    }
                    // Ensure prices are valid numbers
                    window.currentYesPrice = parseFloat(yesPrice) || 0;
                    window.currentNoPrice = parseFloat(noPrice) || 0;
                    limitPrice = isYesSelected ? (parseFloat(yesPrice) || 0) : (parseFloat(noPrice) || 0);
                    updateSummary();
                };

                $doc.on('click', '.tab-item', function() {
                    $('.tab-item').removeClass('active');
                    $(this).addClass('active');
                    $('.tab-content').removeClass('active');
                    $(`#${$(this).data('tab')}`).addClass('active');
                });

                $doc.on('click', '.holders-tab', function() {
                    $('.holders-tab').removeClass('active');
                    $(this).addClass('active');
                    $('#yes-holders, #no-holders').hide();
                    $(`#${$(this).data('holders')}-holders`).show();
                });

                $doc.on('click', '.activity-filter', function() {
                    $('.activity-filter').removeClass('active');
                    $(this).addClass('active');
                });

                $('#buyTab').on('click', () => {
                    $('.action-tab').removeClass('active');
                    $('#buyTab').addClass('active');
                    isBuy = true;
                    updateOutcomePrice();
                    updateSummary();
                }); $('#sellTab').on('click', () => {
                    $('.action-tab').removeClass('active');
                    $('#sellTab').addClass('active');
                    isBuy = false;
                    updateOutcomePrice();
                    updateSummary();
                }); $('#orderType').on('change', function() {
                    isLimitOrder = $(this).val() === 'limit';
                    $('#limitOrderFields').toggleClass('active', isLimitOrder);
                    updateSummary();
                }); $('#limitPrice').on('input', function() {
                    limitPrice = parseFloat($(this).val()) || 0;
                    updateSummary();
                }); $('#yesBtn').on('click', function() {
                    $('.outcome-btn-yes, .outcome-btn-no').removeClass('active');
                    $(this).addClass('active');
                    isYes = true;
                    $('#executeTrade').text('Buy Yes');
                    if (window.currentYesPrice !== undefined) $('#limitPrice').val(window.currentYesPrice);
                    updateOutcomePrice();
                    updateSummary();
                }); $('#noBtn').on('click', function() {
                    $('.outcome-btn-yes, .outcome-btn-no').removeClass('active');
                    $(this).addClass('active');
                    isYes = false;
                    $('#executeTrade').text('Buy No');
                    if (window.currentNoPrice !== undefined) $('#limitPrice').val(window.currentNoPrice);
                    updateOutcomePrice();
                    updateSummary();
                }); $('#decrease-100, #decrease-10, #increase-10, #increase-100').on('click', function() {
                    updateShares(parseInt($(this).data('amount') || $(this).attr('id').includes(
                        'decrease') ? -parseInt($(this).attr('id').match(/\d+/)[0]) : parseInt(
                        $(
                            this).attr('id').match(/\d+/)[0])));
                }); $('#sharesInput').on('input', function() {
                    // Allow decimal values for investment amount
                    const value = parseFloat($(this).val()) || 0;
                    currentShares = Math.max(0, value);
                    $(this).val(currentShares);
                    updateSummary();
                }); $doc.on('click', '.quick-btn', function() {
                    if (this.id === 'maxShares') currentShares = Math.floor(userBalance / 0.01);
                    else {
                        const percent = parseInt($(this).data('percent'));
                        currentShares = Math.floor((userBalance * percent) / 100 / 0.01);
                    }
                    $('#sharesInput').val(currentShares);
                    updateSummary();
                }); $doc.on('click', '.shares-price', function() {
                    updateShares(parseInt($(this).data('price')));
                });

                $('#executeTrade').on('click', function() {
                        if (currentShares <= 0) {
                            showWarning('Please enter a valid amount', 'Invalid Amount');
                            return;
                        }

                        if (!currentMarketId) {
                            showWarning('Market not selected', 'Selection Required');
                            return;
                        }

                        // Check if user is logged in
                        @auth
                        // Calculate required amount using Polymarket formula
                        let marketPrice = null;
                        if (isLimitOrder && limitPrice > 0) {
                            marketPrice = limitPrice / 100;
                        } else {
                            if (isYes && window.currentYesPrice !== undefined) {
                                marketPrice = window.currentYesPrice / 100;
                            } else if (!isYes && window.currentNoPrice !== undefined) {
                                marketPrice = window.currentNoPrice / 100;
                            } else {
                                marketPrice = 0.5; // Default
                            }
                        }
                        marketPrice = Math.max(0.0001, Math.min(0.9999, marketPrice));
                        
                        // Investment amount (currentShares is investment in USD)
                        const investment = parseFloat(currentShares) || 0;
                        
                        // Calculate shares and cost
                        const shares = marketPrice > 0 ? investment / marketPrice : 0;
                        const requiredAmount = shares * marketPrice; // Should equal investment

                        // Check balance
                        if (userBalance < requiredAmount) {
                            const shortfall = requiredAmount - userBalance;
                            Swal.fire({
                                icon: 'warning',
                                title: 'Insufficient Balance',
                                html: `You need $${requiredAmount.toFixed(2)} to place this trade.<br>Your current balance is $${userBalance.toFixed(2)}.<br><br>You need to deposit $${shortfall.toFixed(2)} more.`,
                                showCancelButton: true,
                                confirmButtonText: 'Deposit Now',
                                cancelButtonText: 'Cancel',
                                confirmButtonColor: getThemeColor('--accent'),
                                cancelButtonColor: getThemeColor('--secondary'),
                                background: getThemeColor('--card-bg'),
                                color: getThemeColor('--text-primary'),
                                customClass: {
                                    popup: 'swal2-theme',
                                    title: 'swal2-title-theme',
                                    content: 'swal2-content-theme'
                                },
                                didClose: () => {
                                    cleanupSwal();
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Open deposit modal
                                    if (typeof window.openDepositModal === 'function') {
                                        window.openDepositModal();
                                    } else {
                                        // Fallback: trigger deposit button click
                                        $('#depositBtn').trigger('click');
                                    }
                                }
                            });
                            return;
                        }

                        // Balance is sufficient, proceed with trade
                        executeTrade();
                    @else
                        Swal.fire({
                            icon: 'warning',
                            title: 'Login Required',
                            text: 'Please login to place a trade',
                            showCancelButton: true,
                            confirmButtonText: 'Go to Login',
                            confirmButtonColor: getThemeColor('--accent'),
                            cancelButtonColor: getThemeColor('--secondary'),
                            cancelButtonText: 'Cancel',
                            background: getThemeColor('--card-bg'),
                            color: getThemeColor('--text-primary'),
                            customClass: {
                                popup: 'swal2-theme',
                                title: 'swal2-title-theme',
                                content: 'swal2-content-theme'
                            },
                            didClose: () => {
                                cleanupSwal();
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '{{ route('login') }}';
                            }
                        });
                    @endauth
                });

            function executeTrade() {
                const $btn = $('#executeTrade');
                const originalText = $btn.text();
                $btn.prop('disabled', true).text('Processing...');

                // Calculate price (convert from cents to decimal, or use percentage)
                let price = null;
                if (isLimitOrder && limitPrice > 0) {
                    // Limit price is in cents, convert to decimal (0.01 to 0.99)
                    price = limitPrice / 100;
                } else {
                    // Use current market price (already in percentage, convert to decimal)
                    if (isYes && window.currentYesPrice !== undefined) {
                        price = window.currentYesPrice / 100;
                    } else if (!isYes && window.currentNoPrice !== undefined) {
                        price = window.currentNoPrice / 100;
                    } else {
                        // Fallback: use price from button text
                        const priceText = isYes ?
                            $('.btn-yes').text().match(/([\d.]+)\$/)?.[1] :
                            $('.btn-no').text().match(/([\d.]+)\$/)?.[1];
                        price = priceText ? (parseFloat(priceText) / 100) : 0.5;
                    }
                }

                // Ensure price is between 0.0001 and 0.9999
                price = Math.max(0.0001, Math.min(0.9999, price));

                // Prepare trade data
                const tradeData = {
                    option: isYes ? 'yes' : 'no',
                    amount: currentShares,
                    price: price
                };

                // Make API call
                fetch(`/trades/market/${currentMarketId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                'content') || '',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(tradeData)
                    })
                    .then(response => {
                        // Check if response is ok
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.message || 'Server error');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Show success message with backend message or default
                            const successMessage = data.message || 'Trade placed successfully!';
                            if (typeof showSuccess !== 'undefined') {
                                showSuccess(successMessage, 'Trade Executed');
                            } else {
                                alert('Success: ' + successMessage);
                            }

                            // Reset form
                            currentShares = 0;
                            $('#sharesInput').val(0);
                            updateSummary();

                            // Close panel on mobile
                            if (window.innerWidth <= 768) {
                                closeTradingPanel();
                            }

                            // Reload page after a short delay to show updated balance
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            // Show error message
                            const errorMessage = data.message || 'Failed to place trade';
                            if (typeof showError !== 'undefined') {
                                showError(errorMessage, 'Trade Failed');
                            } else {
                                alert('Error: ' + errorMessage);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Trade error:', error);
                        const errorMessage = error.message || 'An error occurred. Please try again.';
                        if (typeof showError !== 'undefined') {
                            showError(errorMessage, 'Error');
                        } else {
                            alert('Error: ' + errorMessage);
                        }
                    })
                    .finally(() => {
                        $btn.prop('disabled', false).text(originalText);
                    });
            }

            $doc.on('click', '.btn-yes, .btn-no', function(e) {
                e.stopPropagation();
                const $row = $(this).closest('.outcome-row');
                const isMobile = $win.width() <= 768;
                populateTradingPanel($row, $(this).hasClass('btn-yes'), isMobile);
                if (isMobile) {
                    $('#tradingPanel, #mobilePanelOverlay').addClass('active');
                    $body.css('overflow', 'hidden');
                    setTimeout(() => $('#tradingPanel').scrollTop(0), 100);
                } else {
                    const $panel = $('#tradingPanel');
                    if ($panel.length) $('html, body').animate({
                        scrollTop: $panel.offset().top - 100
                    }, 500);
                }
            });

            $doc.on('click', '.outcome-row', function(e) {
                if ($(e.target).closest('.btn-yes, .btn-no').length) return;
                const $row = $(this);
                const isMobile = $win.width() <= 768;
                populateTradingPanel($row, true, isMobile);
                if (isMobile) {
                    $('#tradingPanel, #mobilePanelOverlay').addClass('active');
                    $body.css('overflow', 'hidden');
                } else {
                    const $panel = $('#tradingPanel');
                    if ($panel.length) $('html, body').animate({
                        scrollTop: $panel.offset().top - 100
                    }, 500);
                }
            });

            // Close trading panel handlers
            function closeTradingPanel() {
                $('#tradingPanel, #mobilePanelOverlay').removeClass('active');
                $body.css('overflow', '');
            }

            $doc.on('click', '#panelCloseBtn, #mobilePanelOverlay', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeTradingPanel();
            });

            $doc.on('click', '.show-more-btn', function() {
                $(this).toggleClass('expanded');
            });

            $doc.on('click', '.reply-btn', function() {
                $(this).closest('.comment-section').find('.comment-reply-wrapper').slideToggle(200);
            });

            $doc.on('click', '.comment-reply-cancel-btn', function() {
                $(this).closest('.comment-reply-wrapper').slideUp(200).find('.comment-reply-input').val(
                    '');
            });

            $doc.on('click', '.comment-reply-submit-btn', function() {
                const replyText = $(this).siblings('.comment-reply-input').val().trim();
                if (replyText === '') {
                    showWarning('Please enter a reply', 'Reply Required');
                    return;
                }
                showSuccess('Reply posted: ' + replyText, 'Reply Posted');
                $(this).siblings('.comment-reply-input').val('');
                $(this).closest('.comment-reply-wrapper').slideUp(200);
            });
            $doc.on('keypress', '.comment-reply-input', function(e) {
                if (e.which === 13) $(this).siblings('.comment-reply-submit-btn').click();
            });

            $doc.on('click', '.chart-btn', function() {
                $('.chart-btn').removeClass('active');
                $(this).addClass('active');
                if (window.marketChart) {
                    window.marketChart.data.datasets[0].data = generateChartData(50, 100);
                    window.marketChart.data.datasets[1].data = generateChartData(48, 100);
                    window.marketChart.data.datasets[2].data = generateChartData(1.9, 100);
                    window.marketChart.data.datasets[3].data = generateChartData(0.5, 100);
                    window.marketChart.update();
                }
            });

            const generateChartData = (target, length) => {
                const data = [];
                let current = Math.random() * 20 + 10;
                for (let i = 0; i < length; i++) {
                    current = current + (Math.random() - 0.5) * 10 + (target - current) * 0.02;
                    data.push(Math.max(0, Math.min(100, current)));
                }
                return data;
            };

            const initMarketChart = () => {
                const ctx = $('#marketChart')[0];
                if (ctx && typeof Chart !== 'undefined') {
                    window.marketChart = new Chart(ctx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: Array.from({
                                length: 100
                            }, (_, i) => i),
                            datasets: [{
                                    label: 'No change',
                                    data: generateChartData(50, 100),
                                    borderColor: '#f97316',
                                    borderWidth: 2,
                                    tension: 0.1,
                                    pointRadius: 0
                                },
                                {
                                    label: '25 bps decrease',
                                    data: generateChartData(48, 100),
                                    borderColor: '#3b82f6',
                                    borderWidth: 2,
                                    tension: 0.1,
                                    pointRadius: 0
                                },
                                {
                                    label: '50+ bps decrease',
                                    data: generateChartData(1.9, 100),
                                    borderColor: '#06b6d4',
                                    borderWidth: 2,
                                    tension: 0.1,
                                    pointRadius: 0
                                },
                                {
                                    label: '25+ bps increase',
                                    data: generateChartData(0.5, 100),
                                    borderColor: '#eab308',
                                    borderWidth: 2,
                                    tension: 0.1,
                                    pointRadius: 0
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false
                                }
                            },
                            scales: {
                                y: {
                                    grid: {
                                        color: '#374151'
                                    },
                                    position: 'right',
                                    ticks: {
                                        callback: v => v + '%'
                                    }
                                }
                            },
                            interaction: {
                                mode: 'nearest',
                                axis: 'x',
                                intersect: false
                            }
                        }
                    });
                }
            };

            const checkChartJS = () => {
                if (typeof Chart !== 'undefined') initMarketChart();
                else setTimeout(checkChartJS, 50);
            };
            checkChartJS();
            updateOutcomePrice();
            updateSummary();
        })();

        // PROFILE PAGE 
        (function() {
            const handleHashNavigation = () => {
                const hash = window.location.hash.replace('#', '');
                const validHashes = ['profile', 'account', 'trading', 'notifications', 'builder', 'export'];
                if (hash && validHashes.includes(hash)) {
                    $('.settings-tab, .mobile-profile-dropdown-item, .profile-nav-dropdown-menu .dropdown-item')
                        .removeClass('active');
                    $(`.settings-tab[data-tab="${hash}"], .mobile-profile-dropdown-item[data-tab="${hash}"], .profile-nav-dropdown-menu .dropdown-item[href*="${hash}"]`)
                        .addClass('active');
                    $('.tab-content').removeClass('active');
                    $(`#${hash}-tab`).addClass('active');
                    setTimeout(() => $('html, body').animate({
                        scrollTop: $('.settings-container').offset().top - 20
                    }, 300), 100);
                }
            };

            $doc.on('click', '.content-tab', function() {
                const tab = $(this).data('tab');
                $('.content-tab').removeClass('active');
                $(this).addClass('active');
                $('.tab-content-wrapper').addClass('d-none');
                $(`#${tab}-tab`).removeClass('d-none');
            });

            $doc.on('click', '.subtab-btn', function() {
                const subtab = $(this).data('subtab');
                $('.subtab-btn').removeClass('active');
                $(this).addClass('active');
                $('.active-headers').toggleClass('d-none', subtab !== 'active');
                $('.closed-headers').toggleClass('d-none', subtab === 'active');
                $('#sortFilterBtn span').text(subtab === 'active' ? 'Value' : 'Profit/Loss');
            });

            $doc.on('click', '.time-filter-btn', function() {
                $('.time-filter-btn').removeClass('active');
                $(this).addClass('active');
                const time = $(this).data('time');
                const timeframes = {
                    'ALL': 'All-Time',
                    '1D': 'Past Day',
                    '1W': 'Past Week',
                    '1M': 'Past Month'
                };
                $('#profitLossTimeframe').text(timeframes[time] || 'All-Time');
            });

            $doc.on('click', '#sortFilterBtn, #amountFilterBtn', function(e) {
                e.stopPropagation();
                const $wrapper = $(this).closest('.filter-dropdown-wrapper');
                $('.filter-dropdown-wrapper').not($wrapper).removeClass('active');
                $wrapper.toggleClass('active');
            });

            $doc.on('click', '#sortFilterMenu .filter-dropdown-item, #amountFilterMenu .filter-dropdown-item',
                function(e) {
                    e.preventDefault();
                    const $this = $(this);
                    const text = $this.text();
                    $this.siblings().removeClass('active');
                    $this.addClass('active');
                    $this.closest('.filter-dropdown-wrapper').find('span').text(text);
                    $this.closest('.filter-dropdown-wrapper').removeClass('active');
                });

            $doc.on('input', '.search-input', function() {
                const term = $(this).val().toLowerCase().trim();
                $('.positions-table tbody tr').each(function() {
                    $(this).toggle($(this).text().toLowerCase().includes(term));
                });
            });

            $doc.on('click', '.settings-tab', function(e) {
                e.preventDefault();
                $('.settings-tab').removeClass('active');
                $(this).addClass('active');
                $('.tab-content').removeClass('active');
                const tabId = $(this).data('tab');
                $(`#${tabId}-tab`).addClass('active');
                $('.mobile-profile-dropdown-item').removeClass('active');
                $(`.mobile-profile-dropdown-item[data-tab="${tabId}"]`).addClass('active');
            });

            $('#mobileProfileDropdownBtn').on('click', function(e) {
                e.stopPropagation();
                $('.mobile-profile-dropdown').toggleClass('active');
            });

            $doc.on('click', '.mobile-profile-dropdown-item', function(e) {
                e.preventDefault();
                const tabId = $(this).data('tab');
                $('.mobile-profile-dropdown-item, .settings-tab').removeClass('active');
                $(this).addClass('active');
                $(`.settings-tab[data-tab="${tabId}"]`).addClass('active');
                $('.tab-content').removeClass('active');
                $(`#${tabId}-tab`).addClass('active');
                $('.mobile-profile-dropdown').removeClass('active');
                $('html, body').animate({
                    scrollTop: $('.settings-container').offset().top - 20
                }, 300);
            });

            $doc.on('click', e => {
                if (!$(e.target).closest('.mobile-profile-dropdown').length) {
                    $('.mobile-profile-dropdown').removeClass('active');
                }
            });

            handleHashNavigation();
            $win.on('hashchange', handleHashNavigation);
        })();

        // LAZY LOAD & ANIMATIONS 
        (function() {
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        $(entry.target).css('animation', 'fadeIn 0.5s ease');
                    }
                });
            });
            $('.market-card').each(function() {
                observer.observe(this);
            });
        })();

        })(jQuery);
    </script>
    <!-- Deposit Modal JS (Production Optimized) -->
    <script src="{{ asset('frontend/assets/js/deposit-modal.min.js') }}"></script>
    <!-- Notifications & Utilities JS (Production Optimized) -->
    <script src="{{ asset('frontend/assets/js/notifications.min.js') }}"></script>
    <!-- Deposit Modal Routes (Inline - Required for Blade variables) -->
    <script>
        $(function() {
            const $depositBtn = $("#depositBtn");
            const $depositModal = $("#depositModalPopup");
            const $depositOverlay = $("#depositModalOverlay");
            const $depositClose = $("#depositModalClose");
            const $depositAmount = $("#depositAmount");
            const $quickAmountBtns = $(".quick-amount-btn");
            const $methodBtns = $(".deposit-method-btn");
            const $depositSubmit = $("#depositSubmitBtn");

            function openDepositModal() {
                $depositModal.addClass("active");
                $depositOverlay.addClass("active");
                $("body").css("overflow", "hidden");
            }

            function closeDepositModal() {
                $depositModal.removeClass("active");
                $depositOverlay.removeClass("active");
                $("body").css("overflow", "");
            }

            // Make functions globally accessible
            window.openDepositModal = openDepositModal;
            window.closeDepositModal = closeDepositModal;

            // Prevent form default submit
            $('#depositForm').on('submit', function(e) {
                e.preventDefault();
                $('#depositSubmitBtn').trigger('click');
            });

            // Open modal
            $depositBtn.on("click", function(e) {
                e.preventDefault();
                openDepositModal();
            });

            // Close modal
            $depositClose.on("click", function(e) {
                e.preventDefault();
                closeDepositModal();
            });

            // Close modal on overlay click
            $depositOverlay.on("click", function(e) {
                if ($(e.target).is($depositOverlay)) {
                    closeDepositModal();
                }
            });

            // Quick amount buttons
            $quickAmountBtns.on("click", function() {
                const amount = $(this).data("amount");
                $depositAmount.val(amount);
                $quickAmountBtns.removeClass("active");
                $(this).addClass("active");
            });

            // Payment method selection
            $methodBtns.on("click", function(e) {
                e.preventDefault();
                $methodBtns.removeClass("active");
                $(this).addClass("active");

                const method = $(this).data("method");
                if (method === 'manual') {
                    $("#queryCodeGroup").slideDown(200);
                    $("#depositNoteText").text(
                        "Minimum deposit: $10. Enter your transaction code for manual verification.");
                } else {
                    $("#queryCodeGroup").slideUp(200);
                    $("#queryCode").val("");
                    $("#depositNoteText").text(
                        "Minimum deposit: $10. Your payment will be processed securely.");
                }
            });

            // Submit deposit
            $depositSubmit.on("click", function(e) {
                e.preventDefault();
                const amount = parseFloat($depositAmount.val());
                const method = $methodBtns.filter('.active').data('method') || 'binancepay';
                const currency = 'USDT';

                if (!amount || amount <= 0) {
                    if (typeof showWarning !== 'undefined') {
                        showWarning('Please enter a valid amount', 'Invalid Amount');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Invalid Amount',
                            text: 'Please enter a valid amount',
                            confirmButtonColor: '#ffb11a'
                        });
                    } else {
                        alert('Please enter a valid amount');
                    }
                    return;
                }

                if (amount < 10) {
                    if (typeof showWarning !== 'undefined') {
                        showWarning('Minimum deposit amount is $10', 'Minimum Amount Required');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Minimum Amount Required',
                            text: 'Minimum deposit amount is $10',
                            confirmButtonColor: '#ffb11a'
                        });
                    } else {
                        alert('Minimum deposit amount is $10');
                    }
                    return;
                }

                // Disable submit button
                const $btn = $(this);
                const originalText = $btn.html();
                $btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

                // Handle Binance Pay
                if (method === 'binancepay') {
                    $btn.prop("disabled", true).html(
                        '<i class="fas fa-spinner fa-spin"></i> Creating payment...');

                    $.ajax({
                        url: '{{ route('binance.create') }}',
                        method: 'POST',
                        data: {
                            amount: amount,
                            currency: currency,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success && response.checkoutUrl) {
                                closeDepositModal();
                                setTimeout(function() {
                                    window.location.href = response.checkoutUrl;
                                }, 100);
                            } else {
                                let errorMsg = response.message ||
                                    "Failed to create payment. Please try again.";

                                if (errorMsg.includes('IP') || errorMsg.includes('whitelist')) {
                                    errorMsg +=
                                        '\n\nPlease contact support to whitelist the server IP address.';
                                }

                                showError(errorMsg, 'Payment Error');
                                $btn.prop("disabled", false).html(originalText);
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = "Failed to create payment. Please try again.";

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;

                                if (errorMessage.includes('IP') || errorMessage.includes(
                                        'whitelist')) {
                                    errorMessage +=
                                        '\n\n💡 Tip: Server IP needs to be whitelisted in Binance Pay dashboard.';
                                } else if (errorMessage.includes('authentication') ||
                                    errorMessage.includes('API')) {
                                    errorMessage +=
                                        '\n\n💡 Tip: Please check API credentials in admin settings.';
                                } else if (errorMessage.includes('signature')) {
                                    errorMessage +=
                                        '\n\n💡 Tip: Please verify API secret key is correct.';
                                }
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = Object.values(xhr.responseJSON.errors).flat();
                                errorMessage = errors.join('\n');
                            } else if (xhr.status === 0) {
                                errorMessage =
                                    "Network error. Please check your internet connection and try again.";
                            } else if (xhr.status === 500) {
                                errorMessage =
                                    "Server error. Please try again later or contact support.";
                            }

                            showError(errorMessage, 'Payment Error');
                            $btn.prop("disabled", false).html(originalText);
                        }
                    });
                    return;
                }

                // Handle Manual Payment
                if (method === 'manual') {
                    const queryCode = $("#queryCode").val();
                    if (!queryCode) {
                        showWarning('Please enter your transaction/query code', 'Query Code Required');
                        $btn.prop("disabled", false).html(originalText);
                        return;
                    }

                    $.ajax({
                        url: '{{ route('binance.manual.verify') }}',
                        method: 'POST',
                        data: {
                            query_code: queryCode,
                            amount: amount,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                showSuccess(
                                    response.message ||
                                    `Payment verified! Your new balance is $${response.balance}`,
                                    'Payment Verified'
                                );

                                $('.wallet-value').each(function() {
                                    if ($(this).closest('.wallet-item').find(
                                            '.wallet-label').text().trim() === 'Cash') {
                                        $(this).text('$' + response.balance);
                                    }
                                });

                                $('.balance-value').text('$' + response.balance);
                                closeDepositModal();
                                $depositAmount.val("");
                                $("#queryCode").val("");

                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                showError(response.message ||
                                    "Verification failed. Please try again.",
                                    'Verification Failed');
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = "Verification failed. Please try again.";

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = Object.values(xhr.responseJSON.errors).flat();
                                errorMessage = errors.join('\n');
                            }

                            showError(errorMessage, 'Verification Failed');
                        },
                        complete: function() {
                            $btn.prop("disabled", false).html(originalText);
                        }
                    });
                    return;
                }

                // Handle MetaMask & Trust Wallet
                if (method === 'metamask' || method === 'trustwallet') {
                    handleWeb3Deposit(amount, currency, $btn, originalText, method);
                    return;
                }

                // Handle other methods - placeholder
                showWarning('This payment method is not yet implemented', 'Coming Soon');
                $btn.prop("disabled", false).html(originalText);
            });

            // Close on Escape key
            $(document).on("keydown", function(e) {
                if (e.key === "Escape" && $depositModal.hasClass("active")) {
                    closeDepositModal();
                }
            });

            // ==================== WEB3 WALLET INTEGRATION ====================

            // Detect and get the appropriate Web3 provider
            function getWeb3Provider(walletType) {
                // For Trust Wallet on mobile
                if (walletType === 'trustwallet') {
                    // Trust Wallet injects as window.ethereum on mobile
                    if (window.ethereum && window.ethereum.isTrust) {
                        return window.ethereum;
                    }
                    // Fallback to regular ethereum if Trust Wallet is not detected
                    if (window.ethereum) {
                        return window.ethereum;
                    }
                    return null;
                }

                // For MetaMask
                if (walletType === 'metamask') {
                    // Check for MetaMask specifically
                    if (window.ethereum && window.ethereum.isMetaMask) {
                        return window.ethereum;
                    }
                    // Fallback to general ethereum provider
                    if (window.ethereum) {
                        return window.ethereum;
                    }
                    return null;
                }

                return null;
            }

            // Main Web3 deposit handler (works for both MetaMask and Trust Wallet)
            function handleWeb3Deposit(amount, currency, $btn, originalText, walletType) {
                const walletName = walletType === 'trustwallet' ? 'Trust Wallet' : 'MetaMask';

                const provider = getWeb3Provider(walletType);

                if (!provider) {
                    const message = walletType === 'trustwallet' ?
                        'Trust Wallet is not detected. Please open this page in Trust Wallet browser or install Trust Wallet extension.' :
                        'MetaMask is not installed. Please install MetaMask extension to continue.';

                    if (typeof showError !== 'undefined') {
                        showError(message, walletName + ' Required');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: walletName + ' Required',
                            text: message,
                            confirmButtonColor: '#ef4444',
                        });
                    } else {
                        alert(message);
                    }
                    $btn.prop("disabled", false).html(originalText);
                    return;
                }

                console.log(walletName + ' detected, starting deposit process...');

                const network = 'ethereum'; // You can make this dynamic based on user selection
                const tokenAddress = null; // null for native token (ETH/BNB/MATIC)

                // Helper function to get user address
                function getUserAddress() {
                    return new Promise(function(resolve, reject) {
                        provider.request({
                                method: 'eth_accounts'
                            })
                            .then(function(accounts) {
                                if (accounts && accounts.length > 0) {
                                    console.log(walletName + ' already connected:', accounts[0]);
                                    resolve(accounts[0]);
                                } else {
                                    console.log('Requesting ' + walletName + ' accounts...');
                                    $btn.prop("disabled", true).html(
                                        '<i class="fas fa-spinner fa-spin"></i> Opening ' +
                                        walletName + '...');

                                    provider.request({
                                            method: 'eth_requestAccounts'
                                        })
                                        .then(function(requestedAccounts) {
                                            if (!requestedAccounts || requestedAccounts.length ===
                                                0) {
                                                reject(new Error(
                                                    'No accounts found. Please unlock ' +
                                                    walletName + '.'));
                                            } else {
                                                console.log(walletName + ' connected:',
                                                    requestedAccounts[0]);
                                                resolve(requestedAccounts[0]);
                                            }
                                        })
                                        .catch(reject);
                                }
                            })
                            .catch(reject);
                    });
                }

                // Step 1: Get user address
                $btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Connecting...');

                getUserAddress()
                    .then(function(userAddress) {
                        console.log('Got user address, creating deposit record...');

                        // Step 2: Create deposit record
                        $btn.prop("disabled", true).html(
                            '<i class="fas fa-spinner fa-spin"></i> Creating deposit...');

                        return $.ajax({
                            url: '{{ route('metamask.deposit.create') }}',
                            method: 'POST',
                            data: {
                                amount: amount,
                                currency: currency || 'USDT',
                                network: network,
                                token_address: tokenAddress,
                                wallet_type: walletType,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            }
                        }).then(function(createDepositResponse) {
                            if (!createDepositResponse.success) {
                                throw new Error(createDepositResponse.message ||
                                    "Failed to create deposit");
                            }
                            console.log('Deposit record created:', createDepositResponse);
                            return {
                                response: createDepositResponse,
                                userAddress: userAddress
                            };
                        });
                    })
                    .then(function(data) {
                        console.log('Preparing to send transaction...');
                        const response = data.response;
                        const userAddress = data.userAddress;
                        const merchantAddress = response.merchant_address;

                        // Convert amount to wei
                        const decimals = response.token_decimals || 18;
                        const amountFloat = parseFloat(amount);

                        // Use string multiplication to avoid precision issues
                        let amountInWei;
                        if (typeof BigInt !== 'undefined') {
                            const multiplier = BigInt(10) ** BigInt(decimals);
                            const amountBigInt = BigInt(Math.floor(amountFloat * Math.pow(10, decimals)));
                            amountInWei = amountBigInt;
                        } else {
                            amountInWei = Math.floor(amountFloat * Math.pow(10, decimals));
                        }

                        // Convert to hex
                        const amountHex = '0x' + amountInWei.toString(16);

                        // Step 3: Send transaction
                        $btn.prop("disabled", true).html(
                            '<i class="fas fa-spinner fa-spin"></i> Opening ' + walletName + '...');

                        console.log('Transaction details:', {
                            from: userAddress,
                            to: merchantAddress,
                            value: amountHex,
                            network: network
                        });

                        // Get gas price
                        return provider.request({
                                method: 'eth_gasPrice'
                            })
                            .then(function(gasPrice) {
                                console.log('Gas price:', gasPrice);

                                // Estimate gas limit
                                return provider.request({
                                    method: 'eth_estimateGas',
                                    params: [{
                                        from: userAddress,
                                        to: merchantAddress,
                                        value: amountHex
                                    }]
                                }).catch(function() {
                                    // If estimation fails, use default
                                    return '0x5208'; // 21000 in hex
                                }).then(function(gasLimit) {
                                    const transactionParameters = {
                                        from: userAddress,
                                        to: merchantAddress,
                                        value: amountHex,
                                        gas: gasLimit,
                                        gasPrice: gasPrice
                                    };

                                    console.log('Sending transaction with params:',
                                        transactionParameters);

                                    // Send transaction
                                    return provider.request({
                                        method: 'eth_sendTransaction',
                                        params: [transactionParameters]
                                    }).then(function(txHash) {
                                        return {
                                            txHash: txHash,
                                            depositId: response.deposit_id
                                        };
                                    });
                                });
                            });
                    })
                    .then(function(result) {
                        const txHash = result.txHash;
                        const depositId = result.depositId;

                        if (!txHash) {
                            throw new Error('Transaction failed. No transaction hash returned.');
                        }

                        console.log('Transaction hash:', txHash);

                        // Close deposit modal
                        closeDepositModal();

                        // Show success message
                        if (typeof showSuccess !== 'undefined') {
                            showSuccess(
                                `Transaction sent! Hash: ${txHash.substring(0, 10)}...${txHash.substring(txHash.length - 8)}. Please wait for confirmation...`,
                                'Transaction Sent'
                            );
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Transaction Sent!',
                                html: `
                                <div style="text-align: left; color: var(--text-primary, #333);">
                                    <p style="color: var(--text-primary, #333); margin-bottom: 10px;">
                                        <strong>Transaction Hash:</strong>
                                    </p>
                                    <div style="background: var(--secondary, #f5f5f5); color: var(--text-primary, #333); padding: 12px; border-radius: 8px; word-break: break-all; font-family: 'Courier New', monospace; margin: 10px 0; font-size: 13px; border: 1px solid var(--border, #ddd);">
                                        ${txHash}
                                    </div>
                                    <p style="margin-top: 15px; color: var(--text-primary, #333);">
                                        Your transaction has been sent. Please wait for confirmation...
                                    </p>
                                </div>
                            `,
                                icon: 'success',
                                showConfirmButton: true,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3085d6',
                                allowOutsideClick: false
                            });
                        }

                        // Verify transaction
                        verifyWeb3Transaction(txHash, depositId, network, $btn, originalText);
                    })
                    .catch(function(error) {
                        console.error(walletName + ' Error:', error);

                        let errorMessage = 'Failed to process payment. Please try again.';

                        if (error.code === 4001) {
                            errorMessage = 'Transaction was rejected. Please try again.';
                        } else if (error.code === -32002) {
                            errorMessage = 'A request is already pending in ' + walletName +
                                '. Please check your wallet.';
                        } else if (error.code === -32603) {
                            errorMessage = 'Internal error. Please try again.';
                        } else if (error.message) {
                            errorMessage = error.message;
                        } else if (error.responseJSON && error.responseJSON.message) {
                            errorMessage = error.responseJSON.message;
                        }

                        console.log('Showing error to user:', errorMessage);
                        showError(errorMessage, 'Payment Error');
                        $btn.prop("disabled", false).html(originalText);
                    });
            }

            // Verify Web3 Transaction
            function verifyWeb3Transaction(txHash, depositId, network, $btn, originalText) {
                $btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Verifying...');
                checkTransactionStatus(txHash, network, depositId, $btn, originalText, 0);
            }

            // Check transaction status with polling
            function checkTransactionStatus(txHash, network, depositId, $btn, originalText, attempt) {
                const maxAttempts = 60; // 60 attempts = ~5 minutes

                $.ajax({
                    url: '{{ route('metamask.transaction.status') }}',
                    method: 'POST',
                    data: {
                        tx_hash: txHash,
                        network: network,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success && response.status === 'success') {
                            verifyTransaction(txHash, depositId, network, $btn, originalText);
                        } else if (response.success && response.status === 'pending') {
                            if (attempt < maxAttempts) {
                                setTimeout(() => {
                                    checkTransactionStatus(txHash, network, depositId, $btn,
                                        originalText, attempt + 1);
                                }, 5000); // Check every 5 seconds
                            } else {
                                showError(
                                    'Transaction is taking too long to confirm. Please verify manually later.',
                                    'Timeout');
                                $btn.prop("disabled", false).html(originalText);
                            }
                        } else {
                            showError(response.message || 'Transaction verification failed', 'Error');
                            $btn.prop("disabled", false).html(originalText);
                        }
                    },
                    error: function() {
                        verifyTransaction(txHash, depositId, network, $btn, originalText);
                    }
                });
            }

            // Verify transaction on server
            function verifyTransaction(txHash, depositId, network, $btn, originalText) {
                $.ajax({
                    url: '{{ route('metamask.transaction.verify') }}',
                    method: 'POST',
                    data: {
                        tx_hash: txHash,
                        deposit_id: depositId,
                        network: network,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showSuccess(
                                response.message ||
                                `Payment verified! Your new balance is $${response.balance}`,
                                'Payment Verified'
                            );

                            $('.wallet-value').each(function() {
                                if ($(this).closest('.wallet-item').find('.wallet-label').text()
                                    .trim() === 'Cash') {
                                    $(this).text('$' + response.balance);
                                }
                            });

                            $('.balance-value').text('$' + response.balance);
                            closeDepositModal();
                            $depositAmount.val("");

                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showError(response.message || "Verification failed. Please try again.",
                                'Verification Failed');
                            $btn.prop("disabled", false).html(originalText);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = "Verification failed. Please try again.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showError(errorMessage, 'Verification Failed');
                        $btn.prop("disabled", false).html(originalText);
                    }
                });
            }
        });
    </script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('global/sweetalert/sweetalert2@11.js') }}"></script>
    <!-- Toastr JS -->
    <script src="{{ asset('global/toastr/toastr.min.js') }}"></script>
    
    <!-- Success/Error Message Functions -->
    <script>
        // Initialize Toastr
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
        }

        // Define showSuccess function
        window.showSuccess = function(message, title = 'Success') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: title,
                    text: message,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    confirmButtonColor: '#00C853',
                });
            } else if (typeof toastr !== 'undefined') {
                toastr.success(message, title);
            } else {
                alert(title + ': ' + message);
            }
        };

        // Define showError function
        window.showError = function(message, title = 'Error') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: title,
                    text: message,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    toast: true,
                    confirmButtonColor: '#FF4757',
                });
            } else if (typeof toastr !== 'undefined') {
                toastr.error(message, title);
            } else {
                alert(title + ': ' + message);
            }
        };

        // Define showWarning function
        window.showWarning = function(message, title = 'Warning') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: title,
                    text: message,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    confirmButtonColor: '#ffb11a',
                });
            } else if (typeof toastr !== 'undefined') {
                toastr.warning(message, title);
            } else {
                alert(title + ': ' + message);
            }
        };

        // Define showInfo function
        window.showInfo = function(message, title = 'Info') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: title,
                    text: message,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    confirmButtonColor: '#ffb11a',
                });
            } else if (typeof toastr !== 'undefined') {
                toastr.info(message, title);
            } else {
                alert(title + ': ' + message);
            }
        };
    </script>
    
    <!-- Flash Messages Handler -->
    <script>
        // Wait for DOM and showSuccess function to be ready
        document.addEventListener('DOMContentLoaded', function() {
            // Use setTimeout to ensure showSuccess is defined
            setTimeout(function() {
                @if (Session::has('success'))
                    const successMsg = @json(Session::get('success'));
                    if (typeof showSuccess !== 'undefined') {
                        showSuccess(successMsg);
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: successMsg,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true,
                            toast: true,
                            confirmButtonColor: '#00C853',
                        });
                    } else if (typeof toastr !== 'undefined') {
                        toastr.success(successMsg);
                    } else {
                        alert('Success: ' + successMsg);
                    }
                @endif
                @if (Session::has('error'))
                    const errorMsg = @json(Session::get('error'));
                    if (typeof showError !== 'undefined') {
                        showError(errorMsg);
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true,
                            toast: true,
                            confirmButtonColor: '#FF4757',
                        });
                    } else if (typeof toastr !== 'undefined') {
                        toastr.error(errorMsg);
                    } else {
                        alert('Error: ' + errorMsg);
                    }
                @endif
                @if (Session::has('warning'))
                    const warningMsg = @json(Session::get('warning'));
                    if (typeof showWarning !== 'undefined') {
                        showWarning(warningMsg);
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: warningMsg,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            toast: true,
                            confirmButtonColor: '#ffb11a',
                        });
                    } else if (typeof toastr !== 'undefined') {
                        toastr.warning(warningMsg);
                    } else {
                        alert('Warning: ' + warningMsg);
                    }
                @endif
                @if (Session::has('info'))
                    const infoMsg = @json(Session::get('info'));
                    if (typeof showInfo !== 'undefined') {
                        showInfo(infoMsg);
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Info',
                            text: infoMsg,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            toast: true,
                            confirmButtonColor: '#ffb11a',
                        });
                    } else if (typeof toastr !== 'undefined') {
                        toastr.info(infoMsg);
                    } else {
                        alert('Info: ' + infoMsg);
                    }
                @endif
            }, 500);
        });
    </script>

    @livewireScripts
    @stack('script')

    <!-- Google Analytics -->
    @if ($gaTrackingId)
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaTrackingId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '{{ $gaTrackingId }}');
        </script>
    @endif

    <!-- Facebook Pixel -->
    @if ($fbPixelId)
        <script>
            ! function(f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function() {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $fbPixelId }}');
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id={{ $fbPixelId }}&ev=PageView&noscript=1" />
        </noscript>
    @endif

    <!-- Tawk.to Chat Widget -->
    @if ($tawkWidgetCode)
        <script>
            {!! $tawkWidgetCode !!}
        </script>
    @endif

    <style>
        .market-card-action-btn.saved {
            color: #28a745 !important;
            border-color: #28a745 !important;
        }

        .market-card-action-btn.saved:hover {
            color: #218838 !important;
            border-color: #218838 !important;
        }

        .market-card-action-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .market-action-btn.saved {
            color: #28a745 !important;
        }

        .market-action-btn.saved:hover {
            color: #218838 !important;
        }

        .market-action-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>

</body>

</html>

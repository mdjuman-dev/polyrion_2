<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta_derails')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/responsive.css') }}">
    @livewireStyles
    @stack('style')
</head>

<body class="dark-theme has-bottom-nav">
    <div id="header">
        <!-- Header -->
        <header>
            <div class="container">
                <div class="header-content d-lg-flex d-none">
                    <a href="{{ route('home') }}" class="logo">
                        <div class="logo-icon"><i class="fas fa-chart-line"></i></div>
                        <span>Polyrion</span>
                    </a>
                    <div class="search-bar" style="position: relative;">
                        <livewire:header-search />
                    </div>
                    <div class="header-actions d-flex align-items-center justify-content-end">
                        @if (auth()->check())
                            @php
                                $wallet = auth()->user()->wallet;
                                $portfolio = $wallet->portfolio ?? 0;
                                $cash = $wallet->balance ?? 0;
                            @endphp

                            <div class="wallet-summary">
                                <div class="wallet-item">
                                    <span class="wallet-label">Portfolio</span>
                                    <span class="wallet-value">${{ number_format($portfolio, 2) }}</span>
                                </div>
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
                                    <img src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : asset('frontend/assets/images/default-avatar.png') }}"
                                        alt="{{ auth()->user()->username }}">
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
                                                <img src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : asset('frontend/assets/images/default-avatar.png') }}"
                                                    alt="{{ auth()->user()->username }}">
                                            </div>
                                            <div class="header-user-info">
                                                <a href="{{ route('profile.index') }}">
                                                    <div class="header-user-name">
                                                        {{ auth()->user()->username }}
                                                    </div>
                                                </a>
                                                @if (auth()->user()->email)
                                                    <div class="header-user-sub">
                                                        {{ auth()->user()->email }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="header-menu-divider">
                                        </div>
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
                                <div class="logo-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <span>Polyrion</span>
                            </a>
                        </div>
                        <div class="col-7">
                            <div class="header-actions d-flex align-items-center justify-content-end">

                                <div class="header-menu-wrapper">
                                    @if (auth()->check())
                                        <div class="header-user-avatar" id="moreMenuBtn" style="cursor: pointer;">
                                            <img src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : asset('frontend/assets/images/default-avatar.png') }}"
                                                alt="{{ auth()->user()->username }}">
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
                                                        <img src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : asset('frontend/assets/images/default-avatar.png') }}"
                                                            alt="{{ auth()->user()->username }}">
                                                    </div>
                                                    <div class="header-user-info">
                                                        <a href="{{ route('profile.index') }}">
                                                            <div class="header-user-name">
                                                                {{ auth()->user()->username }}
                                                            </div>
                                                        </a>
                                                        @if (auth()->user()->email)
                                                            <div class="header-user-sub">
                                                                {{ auth()->user()->email }}
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
        <a href="{{ route('home') }}    " class="mobile-nav-item active">
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
            <span>${{ auth()->user()->wallet->balance ?? '0.00' }}</span>
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
            <h3>Menu</h3>
            <button class="close-menu-btn" id="closeMenuBtn"><i class="fas fa-times"></i></button>
        </div>

        @if (auth()->check())
            <div class="header-user p-0">
                <div class="header-user-avatar">
                    <img src="{{ isset(auth()->user()->profile_image) ? asset('storage/' . auth()->user()->profile_image) : asset('frontend/assets/images/default-avatar.png') }}"
                        alt="{{ auth()->user()->username }}">
                </div>
                <div class="header-user-info">
                    <div class="header-user-name">
                        {{ auth()->user()->username }}
                    </div>
                    @if (auth()->user()->email)
                        <div class="header-user-sub">
                            {{ auth()->user()->email }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="more-menu-divider"></div>
        @endif

        <div class="more-menu-links">
            <a href="#"><i class="fas fa-trophy" style="color: #ffb11a;"></i> Leaderboard</a>
            <a href="#"><i class="fas fa-dollar-sign" style="color: #00c853;"></i> Rewards</a>
            <a href="#"><i class="fas fa-code" style="color: #ff4757;"></i> APIs</a>
        </div>
        <div class="more-menu-divider"></div>
        <div class="more-menu-links">
            <a href="#">Accuracy</a>
            <a href="#">Documentation</a>
            <a href="#">Terms of Use</a>
        </div>
        <div class="more-menu-social">
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-discord"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-tiktok"></i></a>
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
        <div class="deposit-modal-header">
            <h3>Deposit Funds</h3>
            <button type="button" class="deposit-modal-close" id="depositModalClose" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="deposit-modal-content">
            <div class="deposit-form-container">
                <div class="deposit-balance-info">
                    <div class="balance-item">
                        <span class="balance-label">Current Balance</span>
                        <span
                            class="balance-value">${{ number_format(auth()->user()->wallet->balance ?? 0, 2) }}</span>
                    </div>
                </div>

                <div class="deposit-input-group">
                    <label class="deposit-input-label">Amount</label>
                    <div class="deposit-input-wrapper">
                        <span class="deposit-currency">$</span>
                        <input type="number" class="deposit-input" id="depositAmount" placeholder="0.00"
                            min="0" step="0.01">
                    </div>
                </div>

                <div class="deposit-quick-amounts">
                    <button class="quick-amount-btn" data-amount="10">$10</button>
                    <button class="quick-amount-btn" data-amount="50">$50</button>
                    <button class="quick-amount-btn" data-amount="100">$100</button>
                    <button class="quick-amount-btn" data-amount="500">$500</button>
                </div>

                <div class="deposit-method-section">
                    <label class="deposit-method-label">Payment Method</label>
                    <div class="deposit-methods">
                        <button type="button" class="deposit-method-btn active" data-method="binancepay">
                            <i class="fas fa-coins"></i>
                            <span>Binance Pay</span>
                        </button>

                        <button type="button" class="deposit-method-btn" data-method="manual">
                            <i class="fas fa-keyboard"></i>
                            <span>Manual Payment</span>
                        </button>

                        <button type="button" class="deposit-method-btn" data-method="metamask">
                            <i class="fas fa-mask"></i>
                            <span>MetaMask</span>
                        </button>

                        <button type="button" class="deposit-method-btn" data-method="trustwallet">
                            <i class="fas fa-shield-alt"></i>
                            <span>Trust Wallet</span>
                        </button>
                    </div>
                </div>

                <div class="deposit-input-group" id="queryCodeGroup" style="display: none;">
                    <label class="deposit-input-label">Transaction/Query Code</label>
                    <div class="deposit-input-wrapper">
                        <span class="deposit-currency"><i class="fas fa-barcode"></i></span>
                        <input type="text" class="deposit-input" id="queryCode"
                            placeholder="Enter transaction or merchant trade number">
                    </div>
                    <small class="text-muted" style="display: block; margin-top: 5px; font-size: 12px;">
                        <i class="fas fa-info-circle"></i> Enter your Binance Pay transaction code or merchant trade
                        number
                    </small>
                </div>

                <button type="button" class="deposit-submit-btn" id="depositSubmitBtn">
                    <i class="fas fa-arrow-right"></i>
                    <span>Deposit</span>
                </button>

                <div class="deposit-footer">
                    <p class="deposit-note">
                        <i class="fas fa-info-circle"></i>
                        Deposits are processed securely. Minimum deposit: $10
                    </p>
                </div>
            </div>
        </div>
    </div>



    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-left">
                    <p class="footer-copyright">Adventure One QSS Inc. © 2025</p>
                    <div class="footer-links">
                        <a href="#" class="footer-link">Privacy</a>
                        <span class="footer-separator">•</span>
                        <a href="#" class="footer-link">Terms of Use</a>
                        <span class="footer-separator">•</span>
                        <a href="#" class="footer-link">Learn</a>
                        <span class="footer-separator">•</span>
                        <a href="#" class="footer-link">Careers</a>
                        <span class="footer-separator">•</span>
                        <a href="#" class="footer-link">Press</a>
                    </div>
                </div>
                <div class="footer-right">
                    <div class="footer-social">
                        <a href="#" class="footer-social-link" aria-label="Email">
                            <i class="fas fa-envelope"></i>
                        </a>
                        <a href="#" class="footer-social-link" aria-label="X (Twitter)">
                            <i class="fa-brands fa-x"></i>
                        </a>
                        <a href="#" class="footer-social-link" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="footer-social-link" aria-label="Discord">
                            <i class="fab fa-discord"></i>
                        </a>
                        <a href="#" class="footer-social-link" aria-label="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('frontend/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/bootstrap.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script>
        // ============================================
        // OPTIMIZED FRONTEND JAVASCRIPT
        // ============================================
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

            // ============================================
            // THEME TOGGLE (Optimized)
            // ============================================
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

            // ============================================
            // HEADER MENU (Optimized)
            // ============================================
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

            // ============================================
            // FILTERS & DROPDOWNS (Optimized with delegation)
            // ============================================
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

            // ============================================
            // NAVIGATION (Optimized with Overflow Detection)
            // ============================================
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

            // ============================================
            // MOBILE NAVIGATION (Optimized)
            // ============================================
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

            // ============================================
            // SEARCH KEYBOARD SHORTCUTS (Livewire handles search)
            // ============================================
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

            // ============================================
            // NOTIFICATIONS (Optimized)
            // ============================================
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

            // ============================================
            // MARKET CARDS & ACTIONS (Optimized)
            // ============================================
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

            // ============================================
            // MARKET DETAIL PAGE (Optimized)
            // ============================================
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
                    userBalance = 1000;

                const updateSummary = () => {
                    let price;
                    if (isLimitOrder && limitPrice > 0) {
                        price = limitPrice / 100;
                    } else {
                        if (window.currentYesPrice !== undefined && window.currentNoPrice !== undefined) {
                            price = isBuy ? (isYes ? window.currentYesPrice / 100 : window.currentNoPrice /
                                100) : (isYes ? window.currentNoPrice / 100 : window.currentYesPrice / 100);
                        } else {
                            price = isBuy ? (isYes ? 0.001 : 0.999) : (isYes ? 0.999 : 0.001);
                        }
                    }
                    const total = currentShares * price;
                    const toWin = isBuy ? currentShares * (1 - price) : currentShares * price;
                    $('#totalCost').text(`$${total.toFixed(2)}`);
                    $('#potentialWin').text(`$${toWin.toFixed(2)}`);
                };

                const updateShares = (amount) => {
                    currentShares = Math.max(0, currentShares + amount);
                    $('#sharesInput').val(currentShares);
                    updateSummary();
                };

                const updateOutcomePrice = () => {
                    if (window.currentYesPrice !== undefined && window.currentNoPrice !== undefined) {
                        const price = isYes ? window.currentYesPrice : window.currentNoPrice;
                        $('#limitPrice').val(price);
                        limitPrice = price;
                    }
                };

                const populateTradingPanel = ($row, isYesSelected, isMobile) => {
                    $('.outcome-row').removeClass('active selected');
                    $row.addClass('active selected');
                    const outcomeName = $row.find('.outcome-name').text();
                    const marketTitle = $('.market-title').text();
                    const $yesBtn = $row.find('.btn-yes');
                    const $noBtn = $row.find('.btn-no');
                    const yesPrice = parseFloat($yesBtn.text().match(/([\d.]+)¢/)?.[1] || 0);
                    const noPrice = parseFloat($noBtn.text().match(/([\d.]+)¢/)?.[1] || 0);
                    $('#panelMarketTitle').text(marketTitle);
                    $('#panelOutcomeTitle').text(outcomeName);
                    if (isMobile) {
                        $('#buyTab').addClass('active');
                        $('#sellTab').removeClass('active');
                        $('#actionTabs').addClass('buy-only');
                    }
                    if (isYesSelected) {
                        $('#yesBtn').addClass('active');
                        $('#noBtn').removeClass('active');
                        $('#limitPrice').val(yesPrice);
                    } else {
                        $('#noBtn').addClass('active');
                        $('#yesBtn').removeClass('active');
                        $('#limitPrice').val(noPrice);
                    }
                    window.currentYesPrice = yesPrice;
                    window.currentNoPrice = noPrice;
                    limitPrice = isYesSelected ? yesPrice : noPrice;
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
                });
                $('#sellTab').on('click', () => {
                    $('.action-tab').removeClass('active');
                    $('#sellTab').addClass('active');
                    isBuy = false;
                    updateOutcomePrice();
                    updateSummary();
                });
                $('#orderType').on('change', function() {
                    isLimitOrder = $(this).val() === 'limit';
                    $('#limitOrderFields').toggleClass('active', isLimitOrder);
                    updateSummary();
                });
                $('#limitPrice').on('input', function() {
                    limitPrice = parseFloat($(this).val()) || 0;
                    updateSummary();
                });
                $('#yesBtn').on('click', function() {
                    $('.outcome-btn-yes, .outcome-btn-no').removeClass('active');
                    $(this).addClass('active');
                    isYes = true;
                    if (window.currentYesPrice !== undefined) $('#limitPrice').val(window.currentYesPrice);
                    updateOutcomePrice();
                    updateSummary();
                });
                $('#noBtn').on('click', function() {
                    $('.outcome-btn-yes, .outcome-btn-no').removeClass('active');
                    $(this).addClass('active');
                    isYes = false;
                    if (window.currentNoPrice !== undefined) $('#limitPrice').val(window.currentNoPrice);
                    updateOutcomePrice();
                    updateSummary();
                });
                $('#decrease-100, #decrease-10, #increase-10, #increase-100').on('click', function() {
                    updateShares(parseInt($(this).data('amount') || $(this).attr('id').includes(
                        'decrease') ? -parseInt($(this).attr('id').match(/\d+/)[0]) : parseInt(
                        $(
                            this).attr('id').match(/\d+/)[0])));
                });
                $('#sharesInput').on('input', function() {
                    currentShares = parseInt($(this).val()) || 0;
                    updateSummary();
                });
                $doc.on('click', '.quick-btn', function() {
                    if (this.id === 'maxShares') currentShares = Math.floor(userBalance / 0.01);
                    else {
                        const percent = parseInt($(this).data('percent'));
                        currentShares = Math.floor((userBalance * percent) / 100 / 0.01);
                    }
                    $('#sharesInput').val(currentShares);
                    updateSummary();
                });
                $doc.on('click', '.shares-price', function() {
                    updateShares(parseInt($(this).data('price')));
                });

                $('#executeTrade').on('click', function() {
                    if (currentShares <= 0) return alert('Enter valid shares');
                    if (isLimitOrder && limitPrice <= 0) return alert('Enter valid limit price');
                    // Trade execution logic here
                });

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
                    if (replyText === '') return alert('Please enter a reply');
                    alert('Reply posted: ' + replyText);
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

            // ============================================
            // PROFILE PAGE (Optimized)
            // ============================================
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

            // ============================================
            // LAZY LOAD & ANIMATIONS (Optimized)
            // ============================================
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

        // ============================================
        // DEPOSIT MODAL (Already optimized)
        // ============================================
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

                // Show/hide query code field for manual payment
                const method = $(this).data("method");
                if (method === 'manual') {
                    $("#queryCodeGroup").slideDown(200);
                } else {
                    $("#queryCodeGroup").slideUp(200);
                    $("#queryCode").val("");
                }
            });

            // Submit deposit
            $depositSubmit.on("click", function(e) {
                e.preventDefault();
                const amount = parseFloat($depositAmount.val());
                const method = $methodBtns.filter(".active").data("method");

                if (!amount || amount <= 0) {
                    alert("Please enter a valid amount");
                    return;
                }

                if (amount < 10) {
                    alert("Minimum deposit amount is $10");
                    return;
                }

                if (!method) {
                    alert("Please select a payment method");
                    return;
                }

                // Disable submit button
                const $btn = $(this);
                const originalText = $btn.html();
                $btn.prop("disabled", true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Processing...');

                // Handle Binance Pay differently
                if (method === 'binancepay') {
                    // Create Binance Pay order
                    $.ajax({
                        url: '{{ route('binance.create') }}',
                        method: 'POST',
                        data: {
                            amount: amount,
                            currency: 'USDT',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success && response.checkoutUrl) {
                                // Redirect to Binance Pay checkout
                                window.location.href = response.checkoutUrl;
                            } else {
                                alert(response.message ||
                                    "Failed to create payment. Please try again.");
                                $btn.prop("disabled", false).html(originalText);
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = "Failed to create payment. Please try again.";

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = Object.values(xhr.responseJSON.errors).flat();
                                errorMessage = errors.join('\n');
                            }

                            alert(errorMessage);
                            $btn.prop("disabled", false).html(originalText);
                        }
                    });
                    return;
                }

                // Handle Manual Payment
                if (method === 'manual') {
                    const queryCode = $("#queryCode").val().trim();

                    if (!queryCode) {
                        alert("Please enter transaction/query code");
                        $btn.prop("disabled", false).html(originalText);
                        return;
                    }

                    // Verify and process manual payment
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
                                // Show success message
                                alert(
                                    `Payment verified successfully! Deposit of $${response.amount} processed. Your new balance is $${response.balance}`
                                );

                                // Update balance display if exists
                                $('.wallet-value').each(function() {
                                    if ($(this).closest('.wallet-item').find(
                                            '.wallet-label')
                                        .text().trim() === 'Cash') {
                                        $(this).text('$' + response.balance);
                                    }
                                });

                                // Update balance in modal
                                $('.balance-value').text('$' + response.balance);

                                // Close modal and reset form
                                closeDepositModal();
                                $depositAmount.val("");
                                $("#queryCode").val("");
                                $quickAmountBtns.removeClass("active");
                            } else {
                                alert(response.message ||
                                    "Payment verification failed. Please try again.");
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = "Payment verification failed. Please try again.";

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = Object.values(xhr.responseJSON.errors).flat();
                                errorMessage = errors.join('\n');
                            }

                            alert(errorMessage);
                        },
                        complete: function() {
                            // Re-enable submit button
                            $btn.prop("disabled", false).html(originalText);
                        }
                    });
                    return;
                }

                // Handle other payment methods (MetaMask, Trust Wallet, etc.)
                $.ajax({
                    url: '{{ route('wallet.deposit') }}',
                    method: 'POST',
                    data: {
                        amount: amount,
                        method: method,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            alert(
                                `Deposit of $${response.amount} successful! Your new balance is $${response.balance}`
                            );

                            // Update balance display if exists
                            $('.wallet-value').each(function() {
                                if ($(this).closest('.wallet-item').find(
                                        '.wallet-label')
                                    .text().trim() === 'Cash') {
                                    $(this).text('$' + response.balance);
                                }
                            });

                            // Update balance in modal
                            $('.balance-value').text('$' + response.balance);

                            // Close modal and reset form
                            closeDepositModal();
                            $depositAmount.val("");
                            $quickAmountBtns.removeClass("active");
                        } else {
                            alert(response.message || "Deposit failed. Please try again.");
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = "Deposit failed. Please try again.";

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMessage = errors.join('\n');
                        }

                        alert(errorMessage);
                    },
                    complete: function() {
                        // Re-enable submit button
                        $btn.prop("disabled", false).html(originalText);
                    }
                });
            });

            // Close on Escape key
            $(document).on("keydown", function(e) {
                if (e.key === "Escape" && $depositModal.hasClass("active")) {
                    closeDepositModal();
                }
            });
        });
    </script>
    @livewireScripts
    @stack('script')

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('event-saved', (data) => {
                // Notification removed as per user request
                // Event save/unsave happens silently with visual feedback only
            });
        });
    </script>

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

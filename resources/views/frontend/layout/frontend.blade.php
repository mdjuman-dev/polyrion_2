<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @yield('meta_derails')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/responsive.css') }}">
    @stack('style')
</head>

<body class="dark-theme has-bottom-nav">
    <div id="header">
        <!-- Header -->
        <header>
            <div class="d-lg-block d-none">
                <div class="header-content ">
                    <a href="{{ route('home') }}" class="logo">
                        <div class="logo-icon"><i class="fas fa-chart-line"></i></div>
                        <span>Polyrion</span>
                    </a>
                    <div class="search-bar">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" placeholder="Search polymarket">
                        <span class="search-shortcut">/</span>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('login') }}" class="btn-header">Log In</a>
                        <a href="{{ route('register') }}" class="btn-header btn-sign-up">Sign Up</a>
                        <button class="theme-toggle hide-mobile" id="themeToggle"><i class="fas fa-moon"></i></button>
                    </div>
                </div>
            </div>
            <div class="header-content d-lg-none d-block">
                <div class="row align-items-center">
                    <div class="col-6">
                        <a href="#" class="logo">
                            <div class="logo-icon"><i class="fas fa-chart-line"></i></div>
                            <span>Polymarket</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <div class="header-actions">
                            <a href="{{ route('login') }}" class="btn-header">Log In</a>
                            <a href="{{ route('register') }}" class="btn-header btn-sign-up">Sign Up</a>
                            <button class="theme-toggle hide-mobile" id="themeToggle">
                                <i class="fas fa-moon"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Navigation -->
        <nav>
            <div class="container">
                <div class="nav-content">
                    <a href="index.html" class="nav-item active"><i class="fas fa-arrow-trend-up"></i> Trending</a>
                    <a href="breaking.html" class="nav-item">Breaking</a>
                    <a href="index.html" class="nav-item">New</a>
                    <div class="nav-item-divider"></div>
                    <a href="index.html" class="nav-item">Politics</a>
                    <a href="index.html" class="nav-item">Sports</a>
                    <a href="index.html" class="nav-item">Finance</a>
                    <a href="index.html" class="nav-item">Crypto</a>
                    <a href="index.html" class="nav-item">Geopolitics</a>
                    <a href="index.html" class="nav-item">Earnings</a>
                    <a href="index.html" class="nav-item">Tech</a>
                    <a href="index.html" class="nav-item">Culture</a>
                    <a href="index.html" class="nav-item">World</a>
                    <a href="index.html" class="nav-item">Mentions</a>
                    <div class="nav-item-dropdown d-none d-lg-block">
                        <a href="#" class="nav-item" id="moreNavBtn"><i class="fas fa-chevron-down"></i> More</a>
                        <div class="more-dropdown-menu" id="moreDropdownMenu">
                            <a href="#" class="dropdown-item"><i class="fas fa-chart-line"></i> Activity</a>
                            <a href="#" class="dropdown-item"><i class="fas fa-trophy"></i> Leaderboard</a>
                            <a href="#" class="dropdown-item"><i class="fas fa-chart-bar"></i> Dashboards</a>
                            <a href="#" class="dropdown-item"><i class="fas fa-gift"></i> Rewards</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="mobile-bottom-nav">
        <a href="index.html" class="mobile-nav-item active">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="#" class="mobile-nav-item">
            <i class="fas fa-search"></i>
            <span>Search</span>
        </a>
        <a href="breaking.html" class="mobile-nav-item">
            <i class="fas fa-sync-alt"></i>
            <span>Breaking</span>
        </a>
        <a href="#" class="mobile-nav-item" id="moreMenuBtn">
            <i class="fas fa-bars"></i>
            <span>More</span>
        </a>
    </div>

    <!-- Mobile Search Popup -->
    <div class="mobile-search-overlay" id="mobileSearchOverlay"></div>
    <div class="mobile-search-popup" id="mobileSearchPopup">
        <div class="mobile-search-header">
            <div class="mobile-search-bar">
                <i class="fas fa-search search-icon"></i>
                <input type="text" placeholder="Search polymarket" id="mobileSearchInput" autocomplete="off">

                <button class="mobile-search-close" id="mobileSearchClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mobile-search-tabs">
                <button class="mobile-search-tab active" data-tab="markets">Markets</button>
                <button class="mobile-search-tab" data-tab="profiles">Profiles</button>
            </div>
        </div>
        <div class="mobile-search-content">
            <div class="mobile-search-tab-content active" id="marketsTab">
                <div class="mobile-search-results" id="mobileSearchResults">
                    <!-- Search results will appear here -->
                </div>
            </div>
            <div class="mobile-search-tab-content" id="profilesTab">
                <div class="mobile-search-profiles" id="mobileSearchProfiles">
                    <!-- Profile results will appear here -->
                </div>
            </div>
        </div>
    </div>

    <!-- More Menu Sidebar -->
    <div class="more-menu-overlay" id="moreMenuOverlay"></div>
    <div class="more-menu-sidebar" id="moreMenuSidebar">
        <div class="more-menu-header">
            <h3>Menu</h3>
            <button class="close-menu-btn" id="closeMenuBtn"><i class="fas fa-times"></i></button>
        </div>
        <div class="more-menu-search">
            <input type="text" placeholder="Search polymarket">
        </div>
        <div class="more-menu-section">
            <h4>BROWSE</h4>
            <div class="browse-grid">
                <a href="#" class="browse-item"><i class="fas fa-star"></i> New</a>
                <a href="#" class="browse-item"><i class="fas fa-chart-line"></i> Trending</a>
                <a href="#" class="browse-item"><i class="fas fa-fire"></i> Popular</a>
                <a href="#" class="browse-item"><i class="fas fa-tint"></i> Liquid</a>
                <a href="#" class="browse-item"><i class="fas fa-clock"></i> Ending Soon</a>
                <a href="#" class="browse-item"><i class="fas fa-trophy"></i> Competitive</a>
            </div>
        </div>
        <div class="more-menu-section">
            <h4>TOPICS</h4>
            <div class="topics-grid">
                <a href="#" class="topic-item">
                    <i class="fas fa-chart-line" style="color: #ff4757;"></i>
                    <span>Live Crypto</span>
                </a>
                <a href="#" class="topic-item">
                    <i class="fas fa-landmark"></i>
                    <span>Politics</span>
                </a>
                <a href="#" class="topic-item">
                    <i class="fas fa-globe"></i>
                    <span>Middle East</span>
                </a>
                <a href="#" class="topic-item">
                    <i class="fab fa-bitcoin"></i>
                    <span>Crypto</span>
                </a>
                <a href="#" class="topic-item">
                    <i class="fas fa-football-ball"></i>
                    <span>Sports</span>
                </a>
                <a href="#" class="topic-item">
                    <i class="fas fa-trophy"></i>
                    <span>Pop Culture</span>
                </a>
                <a href="#" class="topic-item">
                    <i class="fas fa-laptop"></i>
                    <span>Tech</span>
                </a>
                <a href="#" class="topic-item">
                    <i class="fas fa-robot"></i>
                    <span>AI</span>
                </a>
            </div>
        </div>
        <div class="more-menu-divider"></div>
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
            <button class="theme-toggle-mobile" id="themeToggleMobile"><i class="fas fa-sun"></i></button>
        </div>
        <div class="more-menu-actions">
            <button class="btn-login-menu">Log in</button>
            <button class="btn-signup-menu">Sign up</button>
        </div>
    </div>

    @yield('content')
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
    <script src="{{ asset('frontend/assets/js/app.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    @stack('script')
</body>

</html>

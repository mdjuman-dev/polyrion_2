<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $siteFavicon = \App\Models\GlobalSetting::getValue('favicon');
        $siteLogo = \App\Models\GlobalSetting::getValue('logo');
    @endphp
    <link rel="icon"
        href="{{ $siteFavicon ? asset('storage/' . $siteFavicon) : asset('backend/assets/images/favicon.ico') }}">

    <title>@yield('title') | Polymarkets</title>

    <!-- Vendors Style-->
    <link rel="stylesheet" href="{{ asset('backend/assets/css/vendors_css.css') }}">
    <!--amcharts -->
    <link href="https://www.amcharts.com/lib/3/plugins/export/export.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('global/toastr/toastr.min.css') }}">
    <!-- SweetAlert2 -->
    <script src="{{ asset('global/sweetalert/sweetalert2@11.js') }}"></script>
    <!-- Style-->
    <link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/skin_color.css') }}">
    
    <!-- Toastr Z-Index Override - Highest Priority -->
    <style>
        /* Toastr Container - Highest z-index to appear above all modals */
        #toast-container,
        #toast-container.toast-top-right,
        #toast-container.toast-top-left,
        #toast-container.toast-top-center,
        #toast-container.toast-bottom-right,
        #toast-container.toast-bottom-left,
        #toast-container.toast-bottom-center,
        .toast-container,
        .toast-top-right,
        .toast-top-left,
        .toast-top-center,
        .toast-bottom-right,
        .toast-bottom-left,
        .toast-bottom-center {
            z-index: 10000000 !important;
            position: fixed !important;
        }
        
        /* Individual Toast Messages */
        .toast,
        #toast-container .toast,
        .toast-container .toast {
            z-index: 10000001 !important;
            position: relative !important;
        }
        
        /* Make Toastr Messages Slimmer */
        #toast-container .toast,
        .toast-container .toast,
        .toast {
            padding: 8px 12px !important;
            min-height: auto !important;
            height: auto !important;
            margin-bottom: 8px !important;
        }
        
        #toast-container .toast-title,
        .toast-container .toast-title,
        .toast-title {
            font-size: 13px !important;
            font-weight: 600 !important;
            margin-bottom: 4px !important;
            line-height: 1.3 !important;
        }
        
        #toast-container .toast-message,
        .toast-container .toast-message,
        .toast-message {
            font-size: 12px !important;
            line-height: 1.4 !important;
            margin: 0 !important;
        }
        
        #toast-container .toast-close-button,
        .toast-container .toast-close-button,
        .toast-close-button {
            font-size: 14px !important;
            line-height: 1 !important;
            padding: 0 !important;
            margin-top: -2px !important;
            margin-right: -2px !important;
            width: 18px !important;
            height: 18px !important;
        }
        
        /* Ensure toastr appears above all modals and overlays */
        body > #toast-container {
            z-index: 10000000 !important;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('backend/assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/admin-theme.css') }}">
    <style>
        /* Fix Feather icon sizes globally */
        svg[data-feather] {
            width: 16px !important;
            height: 16px !important;
            max-width: 16px !important;
            max-height: 16px !important;
            stroke-width: 2 !important;
        }

        /* Main sidebar menu icons */
        .sidebar-menu svg[data-feather],
        .sidebar-menu i[data-feather] {
            width: 16px !important;
            height: 16px !important;
            max-width: 16px !important;
            max-height: 16px !important;
            min-width: 16px !important;
            min-height: 16px !important;
            stroke-width: 2 !important;
        }

        .sidebar-menu>li>a>svg[data-feather],
        .sidebar-menu>li>a>i[data-feather] {
            width: 16px !important;
            height: 16px !important;
            min-width: 16px !important;
            min-height: 16px !important;
            max-width: 16px !important;
            max-height: 16px !important;
            margin-right: 10px !important;
            vertical-align: middle !important;
            display: inline-block !important;
            stroke-width: 2 !important;
            color: rgba(255, 255, 255, 0.85) !important;
            stroke: rgba(255, 255, 255, 0.85) !important;
        }

        .sidebar-menu>li.active>a>svg[data-feather],
        .sidebar-menu>li.active>a>i[data-feather] {
            color: #ffffff !important;
            stroke: #ffffff !important;
        }

        .sidebar-menu>li:hover>a>svg[data-feather],
        .sidebar-menu>li:hover>a>i[data-feather] {
            color: #ffffff !important;
            stroke: #ffffff !important;
        }

        /* Treeview submenu icons - slightly smaller */
        .treeview-menu svg[data-feather],
        .treeview-menu i[data-feather] {
            width: 14px !important;
            height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            min-width: 14px !important;
            min-height: 14px !important;
            stroke-width: 2 !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }

        .treeview-menu>li>a>svg[data-feather],
        .treeview-menu>li>a>i[data-feather] {
            width: 14px !important;
            height: 14px !important;
            min-width: 14px !important;
            min-height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            margin-right: 8px !important;
            vertical-align: middle !important;
            display: inline-block !important;
            stroke-width: 2 !important;
            float: none !important;
            text-align: left !important;
            line-height: normal !important;
            color: rgba(255, 255, 255, 0.75) !important;
            stroke: rgba(255, 255, 255, 0.75) !important;
        }

        .treeview-menu>li.active>a>svg[data-feather],
        .treeview-menu>li.active>a>i[data-feather] {
            color: #60a5fa !important;
            stroke: #60a5fa !important;
        }

        .treeview-menu>li:hover>a>svg[data-feather],
        .treeview-menu>li:hover>a>i[data-feather] {
            color: #ffffff !important;
            stroke: #ffffff !important;
        }

        /* Override any large icon styles in treeview */
        .treeview-menu>li>a {
            display: flex !important;
            align-items: center !important;
            flex-direction: row !important;
        }

        .treeview-menu>li>a>svg[data-feather],
        .treeview-menu>li>a>i[data-feather] {
            flex-shrink: 0 !important;
            margin-right: 8px !important;
        }

        .treeview-menu>li>a>span {
            flex: 1 !important;
        }

        /* Prevent oversized icons */
        i[data-feather] {
            display: inline-block !important;
            width: 16px !important;
            height: 16px !important;
        }

        /* Force treeview menu icons to be small - override any conflicting styles */
        .treeview-menu>li>a>svg[data-feather] {
            width: 14px !important;
            height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            min-width: 14px !important;
            min-height: 14px !important;
            display: inline-block !important;
            vertical-align: middle !important;
            margin-right: 8px !important;
            float: none !important;
            text-align: left !important;
            line-height: normal !important;
            flex-shrink: 0 !important;
        }

        /* Ensure treeview menu items display properly */
        .treeview-menu>li>a {
            display: flex !important;
            align-items: center !important;
            flex-direction: row !important;
            padding: 2px 5px 2px 25px !important;
        }

        .treeview-menu>li>a>span {
            flex: 1 !important;
            display: inline-block !important;
        }

        /* Ensure treeview menu items display properly */
        .treeview-menu>li>a {
            display: flex !important;
            align-items: center !important;
            flex-direction: row !important;
        }

        /* Modern Sidebar & Header Styles - Moved to custom.css for better organization */

        /* Light skin menu open styles */
        .light-skin .sidebar-menu>li.menu-open>a {
            color: #172b4c !important;
        }

        .light-skin .sidebar-menu>li.menu-open>a svg {
            color: #172b4c !important;
        }

        .light-skin .sidebar-menu>li.menu-open>a i[data-feather] {
            color: #172b4c !important;
        }

        /* Light skin active menu item styles */
        .light-skin .sidebar-menu>li.active>a {
            padding: 10px !important;
        }
    </style>
    @stack('styles')
    @livewireStyles
</head>

<body class="light-skin sidebar-mini theme-primary fixed">

    <div class="wrapper">
        <div id="loader"
            style=" background: #fff url({{ asset('backend/assets/images/preloaders/1.gif') }}) no-repeat center center;">
        </div>

        <header class=" main-header">
            <div class="d-flex align-items-center logo-box justify-content-start">
                <!-- Logo -->
                <a href="{{ route('admin.backend.dashboard') }}" class="logo">
                    <!-- logo-->
                    <div class="logo-mini w-30">
                        <span class="light-logo"><img
                                src="{{ $siteLogo ? asset('storage/' . $siteLogo) : asset('backend/assets/images/logo-letter.png') }}"
                                alt="logo" style="max-height: 30px;"></span>
                        <span class="dark-logo"><img
                                src="{{ $siteLogo ? asset('storage/' . $siteLogo) : asset('backend/assets/images/logo-letter.png') }}"
                                alt="logo" style="max-height: 30px;"></span>
                    </div>
                    <div class="logo-lg">
                        <span class="light-logo"><img
                                src="{{ $siteLogo ? asset('storage/' . $siteLogo) : asset('backend/assets/images/logo-dark-text.png') }}"
                                alt="logo" style="max-height: 40px;"></span>
                        <span class="dark-logo"><img
                                src="{{ $siteLogo ? asset('storage/' . $siteLogo) : asset('backend/assets/images/logo-light-text.png') }}"
                                alt="logo" style="max-height: 40px;"></span>
                    </div>
                </a>
            </div>
            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <div class="app-menu">
                    <ul class="header-megamenu nav">
                        <li class="btn-group nav-item">
                            <a href="javascript:void(0)" class="waves-effect waves-light nav-link push-btn btn-primary-light sidebar-toggle-btn"
                                data-toggle="push-menu" role="button" title="Toggle Sidebar">
                                <i data-feather="align-left"></i>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="navbar-custom-menu r-side">
                    <ul class="nav navbar-nav">
                        <li class="btn-group d-lg-inline-flex d-none">
                            <div class="app-menu">
                                <div class="search-bx mx-5">
                                    <form action="{{ route('admin.search') }}" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <input type="search" name="q" class="form-control"
                                                placeholder="Search users or events..." aria-label="Search"
                                                aria-describedby="button-addon2" value="{{ old('q') }}" required>
                                            <div class="input-group-append">
                                                <button class="btn" type="submit" id="button-addon3"><i
                                                        data-feather="search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </li>
                        <li class="btn-group nav-item d-lg-inline-flex d-none">
                            <a href="index.html#" data-provide="fullscreen"
                                class="waves-effect waves-light nav-link full-screen btn-primary-light"
                                title="Full Screen">
                                <i data-feather="maximize"></i>
                            </a>
                        </li>
                        <!-- Cache Clear Button -->
                        <li class="btn-group nav-item">
                            <a href="javascript:void(0)" class="waves-effect waves-light nav-link btn-primary-light"
                                title="Clear Cache" id="clear-cache-btn">
                                <i data-feather="refresh-cw"></i>
                            </a>
                        </li>

                       
                            

                        <!-- User Account-->
                        <li class="dropdown user user-menu">
                            <a href="index.html#" class="waves-effect waves-light dropdown-toggle btn-primary-light"
                                data-bs-toggle="dropdown" title="User">
                                <i data-feather="user"></i>
                            </a>
                            <ul class="dropdown-menu animated flipInX">
                                <li class="user-body">
                                    <div class="p-20 text-center">
                                        <div class="user-img">
                                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                                                style="width: 80px; height: 80px; font-size: 32px; font-weight: bold;">
                                                {{ strtoupper(substr(auth()->guard('admin')->user()->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <h5 class="mb-0 mt-2">{{ auth()->guard('admin')->user()->name }}</h5>
                                        <p class="text-muted mb-0">{{ auth()->guard('admin')->user()->email }}</p>
                                    </div>
                                </li>
                                <li class="user-body">
                                    <a class="dropdown-item" href="{{ route('admin.profile.show') }}"><i
                                            class="ti-user text-muted me-2"></i>
                                        My Profile</a>
                                    <a class="dropdown-item" href="{{ route('admin.profile.edit') }}"><i
                                            class="ti-settings text-muted me-2"></i>
                                        Edit Profile</a>
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <a class="dropdown-item" href="javascript:void(0)"
                                            onclick="event.preventDefault(); this.closest('form').submit();"><i
                                                class="ti-lock text-muted me-2"></i> Logout</a>
                                    </form>
                                </li>
                            </ul>
                        </li>

                        <!-- Control Sidebar Toggle Button -->
                        <li>
                            <a href="{{ route('admin.setting') }}"
                                class="waves-effect waves-light btn-primary-light">
                                <i data-feather="settings"></i>
                            </a>
                        </li>

                    </ul>
                </div>
            </nav>
        </header>

        <aside class="main-sidebar">
            <!-- sidebar-->
            @include('backend.layouts.main_menu')
        </aside>
        @yield('content')
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <div class="pull-right d-none d-sm-inline-block">
                <ul class="nav nav-primary nav-dotted nav-dot-separated justify-content-center justify-content-md-end">
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0)">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="https://themeforest.net/item/crypto-admin-responsive-bootstrap-4-admin-html-templates/21604673"
                            target="_blank">Purchase Now</a>
                    </li>
                </ul>
            </div>
            &copy; 2025 <a href="https://www.multipurposethemes.com/">Multipurpose Themes</a>. All Rights Reserved.
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar">

            <div class="rpanel-title"><span class="pull-right btn btn-circle btn-danger"
                    data-toggle="control-sidebar"><i class="ion ion-close text-white"></i></span> </div>
            <!-- Create the tabs -->
            <ul class="nav nav-tabs control-sidebar-tabs">
                <li class="nav-item"><a href="index.html#control-sidebar-home-tab" data-bs-toggle="tab"><i
                            class="mdi mdi-message-text"></i></a></li>
                <li class="nav-item"><a href="index.html#control-sidebar-settings-tab" data-bs-toggle="tab"><i
                            class="mdi mdi-playlist-check"></i></a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <!-- Home tab content -->
                <div class="tab-pane" id="control-sidebar-home-tab">
                    <div class="flexbox">
                        <a href="javascript:void(0)" class="text-grey">
                            <i class="ti-more"></i>
                        </a>
                        <p>Users</p>
                        <a href="javascript:void(0)" class="text-end text-grey"><i class="ti-plus"></i></a>
                    </div>
                    <div class="lookup lookup-sm lookup-right d-none d-lg-block">
                        <input type="text" name="s" placeholder="Search" class="w-p100">
                    </div>
                    <div class="media-list media-list-hover mt-20">
                        <div class="media py-10 px-0">
                            <a class="avatar avatar-lg status-success" href="index.html#">
                                <img src="{{ asset('backend/assets/images/avatar/1.jpg') }}" alt="...">
                            </a>
                            <div class="media-body">
                                <p class="fs-16">
                                    <a class="hover-primary" href="index.html#"><strong>Tyler</strong></a>
                                </p>
                                <p>Praesent tristique diam...</p>
                                <span>Just now</span>
                            </div>
                        </div>

                        <div class="media py-10 px-0">
                            <a class="avatar avatar-lg status-danger" href="index.html#">
                                <img src="{{ asset('backend/assets/images/avatar/2.jpg') }}" alt="...">
                            </a>
                            <div class="media-body">
                                <p class="fs-16">
                                    <a class="hover-primary" href="index.html#"><strong>Luke</strong></a>
                                </p>
                                <p>Cras tempor diam ...</p>
                                <span>33 min ago</span>
                            </div>
                        </div>

                        <div class="media py-10 px-0">
                            <a class="avatar avatar-lg status-warning" href="index.html#">
                                <img src="{{ asset('backend/assets/images/avatar/3.jpg') }}" alt="...">
                            </a>
                            <div class="media-body">
                                <p class="fs-16">
                                    <a class="hover-primary" href="index.html#"><strong>Evan</strong></a>
                                </p>
                                <p>In posuere tortor vel...</p>
                                <span>42 min ago</span>
                            </div>
                        </div>

                        <div class="media py-10 px-0">
                            <a class="avatar avatar-lg status-primary" href="index.html#">
                                <img src="{{ asset('backend/assets/images/avatar/4.jpg') }}" alt="...">
                            </a>
                            <div class="media-body">
                                <p class="fs-16">
                                    <a class="hover-primary" href="index.html#"><strong>Evan</strong></a>
                                </p>
                                <p>In posuere tortor vel...</p>
                                <span>42 min ago</span>
                            </div>
                        </div>

                        <div class="media py-10 px-0">
                            <a class="avatar avatar-lg status-success" href="index.html#">
                                <img src="{{ asset('backend/assets/images/avatar/1.jpg') }}" alt="...">
                            </a>
                            <div class="media-body">
                                <p class="fs-16">
                                    <a class="hover-primary" href="index.html#"><strong>Tyler</strong></a>
                                </p>
                                <p>Praesent tristique diam...</p>
                                <span>Just now</span>
                            </div>
                        </div>

                        <div class="media py-10 px-0">
                            <a class="avatar avatar-lg status-danger" href="index.html#">
                                <img src="{{ asset('backend/assets/images/avatar/2.jpg') }}" alt="...">
                            </a>
                            <div class="media-body">
                                <p class="fs-16">
                                    <a class="hover-primary" href="index.html#"><strong>Luke</strong></a>
                                </p>
                                <p>Cras tempor diam ...</p>
                                <span>33 min ago</span>
                            </div>
                        </div>

                        <div class="media py-10 px-0">
                            <a class="avatar avatar-lg status-warning" href="index.html#">
                                <img src="{{ asset('backend/assets/images/avatar/3.jpg') }}" alt="...">
                            </a>
                            <div class="media-body">
                                <p class="fs-16">
                                    <a class="hover-primary" href="index.html#"><strong>Evan</strong></a>
                                </p>
                                <p>In posuere tortor vel...</p>
                                <span>42 min ago</span>
                            </div>
                        </div>

                    </div>

                </div>
                <!-- /.tab-pane -->
                <!-- Settings tab content -->
                <div class="tab-pane" id="control-sidebar-settings-tab">
                    <div class="flexbox">
                        <a href="javascript:void(0)" class="text-grey">
                            <i class="ti-more"></i>
                        </a>
                        <p>Todo List</p>
                        <a href="javascript:void(0)" class="text-end text-grey"><i class="ti-plus"></i></a>
                    </div>
                    <ul class="todo-list mt-20">
                        <li class="py-15 px-5 by-1">
                            <!-- checkbox -->
                            <input type="checkbox" id="basic_checkbox_1" class="filled-in">
                            <label for="basic_checkbox_1" class="mb-0 h-15"></label>
                            <!-- todo text -->
                            <span class="text-line">Nulla vitae purus</span>
                            <!-- Emphasis label -->
                            <small class="badge bg-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
                            <!-- General tools such as edit or delete-->
                            <div class="tools">
                                <i class="fa fa-edit"></i>
                                <i class="fa fa-trash-o"></i>
                            </div>
                        </li>
                        <li class="py-15 px-5">
                            <!-- checkbox -->
                            <input type="checkbox" id="basic_checkbox_2" class="filled-in">
                            <label for="basic_checkbox_2" class="mb-0 h-15"></label>
                            <span class="text-line">Phasellus interdum</span>
                            <small class="badge bg-info"><i class="fa fa-clock-o"></i> 4 hours</small>
                            <div class="tools">
                                <i class="fa fa-edit"></i>
                                <i class="fa fa-trash-o"></i>
                            </div>
                        </li>
                        <li class="py-15 px-5 by-1">
                            <!-- checkbox -->
                            <input type="checkbox" id="basic_checkbox_3" class="filled-in">
                            <label for="basic_checkbox_3" class="mb-0 h-15"></label>
                            <span class="text-line">Quisque sodales</span>
                            <small class="badge bg-warning"><i class="fa fa-clock-o"></i> 1 day</small>
                            <div class="tools">
                                <i class="fa fa-edit"></i>
                                <i class="fa fa-trash-o"></i>
                            </div>
                        </li>
                        <li class="py-15 px-5">
                            <!-- checkbox -->
                            <input type="checkbox" id="basic_checkbox_4" class="filled-in">
                            <label for="basic_checkbox_4" class="mb-0 h-15"></label>
                            <span class="text-line">Proin nec mi porta</span>
                            <small class="badge bg-success"><i class="fa fa-clock-o"></i> 3 days</small>
                            <div class="tools">
                                <i class="fa fa-edit"></i>
                                <i class="fa fa-trash-o"></i>
                            </div>
                        </li>
                        <li class="py-15 px-5 by-1">
                            <!-- checkbox -->
                            <input type="checkbox" id="basic_checkbox_5" class="filled-in">
                            <label for="basic_checkbox_5" class="mb-0 h-15"></label>
                            <span class="text-line">Maecenas scelerisque</span>
                            <small class="badge bg-primary"><i class="fa fa-clock-o"></i> 1 week</small>
                            <div class="tools">
                                <i class="fa fa-edit"></i>
                                <i class="fa fa-trash-o"></i>
                            </div>
                        </li>
                        <li class="py-15 px-5">
                            <!-- checkbox -->
                            <input type="checkbox" id="basic_checkbox_6" class="filled-in">
                            <label for="basic_checkbox_6" class="mb-0 h-15"></label>
                            <span class="text-line">Vivamus nec orci</span>
                            <small class="badge bg-info"><i class="fa fa-clock-o"></i> 1 month</small>
                            <div class="tools">
                                <i class="fa fa-edit"></i>
                                <i class="fa fa-trash-o"></i>
                            </div>
                        </li>
                        <li class="py-15 px-5 by-1">
                            <!-- checkbox -->
                            <input type="checkbox" id="basic_checkbox_7" class="filled-in">
                            <label for="basic_checkbox_7" class="mb-0 h-15"></label>
                            <!-- todo text -->
                            <span class="text-line">Nulla vitae purus</span>
                            <!-- Emphasis label -->
                            <small class="badge bg-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
                            <!-- General tools such as edit or delete-->
                            <div class="tools">
                                <i class="fa fa-edit"></i>
                                <i class="fa fa-trash-o"></i>
                            </div>
                        </li>
                        <li class="py-15 px-5">
                            <!-- checkbox -->
                            <input type="checkbox" id="basic_checkbox_8" class="filled-in">
                            <label for="basic_checkbox_8" class="mb-0 h-15"></label>
                            <span class="text-line">Phasellus interdum</span>
                            <small class="badge bg-info"><i class="fa fa-clock-o"></i> 4 hours</small>
                            <div class="tools">
                                <i class="fa fa-edit"></i>
                                <i class="fa fa-trash-o"></i>
                            </div>
                        </li>
                        <li class="py-15 px-5 by-1">
                            <!-- checkbox -->
                            <input type="checkbox" id="basic_checkbox_9" class="filled-in">
                            <label for="basic_checkbox_9" class="mb-0 h-15"></label>
                            <span class="text-line">Quisque sodales</span>
                            <small class="badge bg-warning"><i class="fa fa-clock-o"></i> 1 day</small>
                            <div class="tools">
                                <i class="fa fa-edit"></i>
                                <i class="fa fa-trash-o"></i>
                            </div>
                        </li>
                        <li class="py-15 px-5">
                            <!-- checkbox -->
                            <input type="checkbox" id="basic_checkbox_10" class="filled-in">
                            <label for="basic_checkbox_10" class="mb-0 h-15"></label>
                            <span class="text-line">Proin nec mi porta</span>
                            <small class="badge bg-success"><i class="fa fa-clock-o"></i> 3 days</small>
                            <div class="tools">
                                <i class="fa fa-edit"></i>
                                <i class="fa fa-trash-o"></i>
                            </div>
                        </li>
                    </ul>
                </div>
                <!-- /.tab-pane -->
            </div>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>

    </div>
    <!-- ./wrapper -->
    <div id="chat-box-body">
        <div id="chat-circle" data-toggle="control-sidebar" title=" Themes Setting"
            class="waves-effect waves-circle btn btn-circle btn-lg btn-primary l-h-70">
            <span class="icon-Group-chat fs-30">
                <i style="width: 30px; margin:auto;" data-feather="settings"></i>
            </span>
        </div>
    </div>

    <!-- Page Content overlay -->


    <!-- Vendor JS -->
    <script src="{{ asset('global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/vendors.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/pages/chat-popup.js') }}"></script>
    <script src="{{ asset('backend/assets/js/demo.js') }}"></script>
    <script src="{{ asset('backend/assets/icons/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor_plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor_components/Flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor_components/Flot/jquery.flot.resize.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor_components/Flot/jquery.flot.pie.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor_components/Flot/jquery.flot.categories.js') }}"></script>
    <script src="https://www.amcharts.com/lib/3/amcharts.js" type="text/javascript"></script>
    <script src="https://www.amcharts.com/lib/3/gauge.js" type="text/javascript"></script>
    <script src="https://www.amcharts.com/lib/3/serial.js" type="text/javascript"></script>
    <script src="https://www.amcharts.com/lib/3/amstock.js" type="text/javascript"></script>
    <script src="https://www.amcharts.com/lib/3/pie.js" type="text/javascript"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/dataloader/dataloader.min.js"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/animate/animate.min.js" type="text/javascript"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js" type="text/javascript"></script>
    <script src="https://www.amcharts.com/lib/3/themes/patterns.js" type="text/javascript"></script>
    <script src="https://www.amcharts.com/lib/3/themes/light.js" type="text/javascript"></script>
    <script src="{{ asset('backend/assets/vendor_components/Web-Ticker-master/jquery.webticker.min.js') }}"></script>

    <!-- Crypto Admin App -->
    <script src="{{ asset('backend/assets/js/template.js') }}"></script>
    <script src="{{ asset('backend/assets/js/pages/dashboard32.js') }}"></script>
    <script src="{{ asset('backend/assets/js/pages/dashboard32-chart.js') }}"></script>
    <script src="{{ asset('backend/assets/js/pages/widget-flot-charts.js') }}"></script>
    <script src="{{ asset('global/toastr/toastr.min.js') }}"></script>
    
    <!-- Iconify Icons CDN - Load after jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/@iconify/iconify@3.1.1/dist/iconify.min.js"></script>

    <script>
        // Configure Toastr
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
        
        // Ensure toastr container has highest z-index
        setTimeout(function() {
            const toastContainer = document.getElementById('toast-container');
            if (toastContainer) {
                toastContainer.style.zIndex = '10000000';
                toastContainer.style.position = 'fixed';
            }
        }, 100);

        // Custom Confirmation Function using SweetAlert2
        function confirmAction(message, callback) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: message || 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, proceed!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (typeof callback === 'function') {
                            callback();
                        } else {
                            return true;
                        }
                    }
                    return false;
                });
                return false;
            } else {
                // Fallback to default confirm if SweetAlert is not available
                return confirm(message || 'Are you sure?');
            }
        }

        // Handle delete confirmation for form submissions
        function handleDeleteConfirm(event, message) {
            event.preventDefault();
            const form = event.target;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: message || 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed && form) {
                        form.submit();
                    }
                });
                return false;
            } else {
                if (confirm(message || 'Are you sure?')) {
                    form.submit();
                    return true;
                }
                return false;
            }
        }

        // Delete confirmation with toastr notification
        function confirmDeleteWithToastr(event, commentId, message) {
            event.preventDefault();
            event.stopPropagation();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: message || 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show info toastr before deletion
                        toastr.info('Deleting...', 'Please wait', {
                            timeOut: 2000
                        });

                        // Find the Livewire component and call delete method
                        const button = event.target.closest('button');
                        if (button) {
                            // Find the parent Livewire component
                            const livewireComponent = button.closest('[wire\\:id]');
                            if (livewireComponent) {
                                const wireId = livewireComponent.getAttribute('wire:id');
                                // Wait for Livewire to be initialized if needed
                                if (typeof Livewire !== 'undefined' && Livewire.find) {
                                    const component = Livewire.find(wireId);
                                    if (component) {
                                        component.call('delete');
                                    }
                                } else {
                                    // Fallback: wait for Livewire to be ready
                                    document.addEventListener('livewire:initialized', () => {
                                        const component = Livewire.find(wireId);
                                        if (component) {
                                            component.call('delete');
                                        }
                                    }, {
                                        once: true
                                    });
                                }
                            }
                        }
                    }
                });
            } else {
                // Fallback to native confirm
                if (confirm(message || 'Are you sure?')) {
                    toastr.info('Deleting...', 'Please wait', {
                        timeOut: 2000
                    });
                    const button = event.target.closest('button');
                    if (button) {
                        const livewireComponent = button.closest('[wire\\:id]');
                        if (livewireComponent) {
                            const wireId = livewireComponent.getAttribute('wire:id');
                            if (typeof Livewire !== 'undefined' && Livewire.find) {
                                const component = Livewire.find(wireId);
                                if (component) {
                                    component.call('delete');
                                }
                            } else {
                                document.addEventListener('livewire:initialized', () => {
                                    const component = Livewire.find(wireId);
                                    if (component) {
                                        component.call('delete');
                                    }
                                }, {
                                    once: true
                                });
                            }
                        }
                    }
                }
            }
            return false;
        }

        // Enhanced form submission with confirmation
        document.addEventListener('DOMContentLoaded', function() {
            // Handle all forms with data-confirm attribute
            document.querySelectorAll('form[data-confirm]').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const message = form.getAttribute('data-confirm');

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: message || 'This action cannot be undone!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Yes, proceed!',
                            cancelButtonText: 'Cancel',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else {
                        if (confirm(message || 'Are you sure?')) {
                            form.submit();
                        }
                    }
                });
            });
        });
    </script>

    <script>
        // Ensure menu links work properly
        document.addEventListener('DOMContentLoaded', function() {
            // Fix menu links navigation
            const menuLinks = document.querySelectorAll('.sidebar-menu a[href]');
            menuLinks.forEach(function(link) {
                const href = link.getAttribute('href');
                // Only prevent if it's a hash link or empty
                if (href && href !== '#' && !href.includes('index.html#')) {
                    // Ensure link navigates properly
                    link.addEventListener('click', function(e) {
                        // Don't prevent default for valid links
                        if (href && href !== '#' && !href.includes('index.html#')) {
                            // Allow normal navigation
                            return true;
                        }
                    });
                }
            });
        });

        // Handle flash messages with toastr
        @if (Session::has('success'))
            toastr.success("{{ Session::get('success') }}");
        @endif

        @if (Session::has('error'))
            toastr.error("{{ Session::get('error') }}");
        @endif

        @if (Session::has('warning'))
            toastr.warning("{{ Session::get('warning') }}");
        @endif

        @if (Session::has('info'))
            toastr.info("{{ Session::get('info') }}");
        @endif

        // Also support old format with message and alert-type
        @if (Session::has('message'))
            var type = "{{ Session::get('alert-type', 'info') }}"
            switch (type) {
                case 'info':
                    toastr.info("{{ Session::get('message') }}");
                    break;
                case 'success':
                    toastr.success("{{ Session::get('message') }}");
                    break;
                case 'warning':
                    toastr.warning("{{ Session::get('message') }}");
                    break;
                case 'error':
                    toastr.error("{{ Session::get('message') }}");
                    break;
            }
        @endif

        // Listen for Livewire events to show toastr notifications
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastr', (data) => {
                const type = data[0].type || 'info';
                const message = data[0].message || 'Operation completed';

                switch (type) {
                    case 'success':
                        toastr.success(message);
                        break;
                    case 'error':
                        toastr.error(message);
                        break;
                    case 'warning':
                        toastr.warning(message);
                        break;
                    case 'info':
                    default:
                        toastr.info(message);
                        break;
                }
            });

            // Handle comment deletion - refresh the page
            Livewire.on('commentDeleted', (commentId) => {
                // Reload the page to refresh comments list
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            });
        });
    </script>
    @stack('scripts')
    @livewireScripts

    <script>
        // Initialize Iconify Icons for Sidebar
        (function() {
            function initializeIconifyIcons() {
                // Check if Iconify is loaded
                if (typeof Iconify !== 'undefined' && typeof Iconify.scan === 'function') {
                    console.log('Iconify is loaded, scanning for icons...');
                    
                    // Scan and render all iconify-icon elements
                    try {
                        Iconify.scan();
                    } catch (e) {
                        console.error('Error scanning icons:', e);
                    }
                    
                    // Ensure all icons are visible and styled
                    const icons = document.querySelectorAll('iconify-icon, iconify-icon.sidebar-icon');
                    console.log('Found icons:', icons.length);
                    
                    icons.forEach(function(icon) {
                        icon.style.display = 'inline-block';
                        icon.style.visibility = 'visible';
                        icon.style.opacity = '1';
                        icon.style.width = '18px';
                        icon.style.height = '18px';
                        icon.style.verticalAlign = 'middle';
                        icon.style.marginRight = '12px';
                        icon.style.color = 'rgba(255, 255, 255, 0.85)';
                    });
                    
                    console.log('Iconify icons initialized:', icons.length);
                    return true;
                }
                return false;
            }
            
            // Wait for Iconify to load
            function waitForIconify() {
                if (typeof Iconify !== 'undefined' && typeof Iconify.scan === 'function') {
                    initializeIconifyIcons();
                    return true;
                }
                return false;
            }
            
            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    let attempts = 0;
                    const checkIconify = setInterval(function() {
                        attempts++;
                        if (waitForIconify() || attempts > 100) {
                            clearInterval(checkIconify);
                            if (attempts > 100) {
                                console.warn('Iconify failed to load after 10 seconds');
                            }
                        }
                    }, 100);
                });
            } else {
                // DOM already loaded
                let attempts = 0;
                const checkIconify = setInterval(function() {
                    attempts++;
                    if (waitForIconify() || attempts > 100) {
                        clearInterval(checkIconify);
                        if (attempts > 100) {
                            console.warn('Iconify failed to load after 10 seconds');
                        }
                    }
                }, 100);
            }
            
            // Also try on window load
            window.addEventListener('load', function() {
                setTimeout(function() {
                    if (waitForIconify()) {
                        initializeIconifyIcons();
                    }
                }, 500);
            });
        })();

        // Function to initialize sidebar icons (for feather icons - legacy support)
        function initializeSidebarIcons() {
            // First, ensure all icons are replaced
            if (typeof feather !== 'undefined') {
                // Replace all icons in sidebar
                const sidebarIcons = document.querySelectorAll('.sidebar-menu i[data-feather], .treeview-menu i[data-feather]');
                if (sidebarIcons.length > 0) {
                    feather.replace({
                        width: 18,
                        height: 18,
                        'stroke-width': 2.5
                    });
                }
            }
            
            // Wait a bit for feather to replace icons, then style them
            setTimeout(function() {
                    // Main sidebar menu icons
                    document.querySelectorAll('.sidebar-menu > li > a > svg[data-feather], .sidebar-menu > li > a > i[data-feather]').forEach(
                        function(icon) {
                            // If it's still an <i> tag, feather hasn't replaced it yet
                            if (icon.tagName === 'I') {
                                return; // Skip, will be handled by feather.replace
                            }
                            
                            // It's an SVG, set properties
                            icon.setAttribute('width', '18');
                            icon.setAttribute('height', '18');
                            icon.setAttribute('stroke-width', '2.5');
                            icon.classList.add('sidebar-icon');
                            icon.style.width = '18px';
                            icon.style.height = '18px';
                            icon.style.maxWidth = '18px';
                            icon.style.maxHeight = '18px';
                            icon.style.minWidth = '18px';
                            icon.style.minHeight = '18px';
                            icon.style.verticalAlign = 'middle';
                            icon.style.marginRight = '12px';
                            icon.style.display = 'inline-block';
                            
                            // Set icon color based on parent state
                            const parentLi = icon.closest('li');
                            const parentLink = icon.closest('a');
                            
                            if (parentLi && parentLi.classList.contains('active')) {
                                icon.style.color = '#ffffff';
                                icon.style.stroke = '#ffffff';
                            } else {
                                icon.style.color = 'rgba(255, 255, 255, 0.85)';
                                icon.style.stroke = 'rgba(255, 255, 255, 0.85)';
                            }
                            
                            // Add hover listener
                            if (parentLink) {
                                parentLink.addEventListener('mouseenter', function() {
                                    icon.style.color = '#ffffff';
                                    icon.style.stroke = '#ffffff';
                                });
                                parentLink.addEventListener('mouseleave', function() {
                                    if (!parentLi || !parentLi.classList.contains('active')) {
                                        icon.style.color = 'rgba(255, 255, 255, 0.85)';
                                        icon.style.stroke = 'rgba(255, 255, 255, 0.85)';
                                    }
                                });
                            }
                        });

                    // Treeview menu icons (submenu) - ensure they're small and inline
                    document.querySelectorAll('.treeview-menu > li > a > svg[data-feather], .treeview-menu > li > a > i[data-feather]').forEach(
                        function(icon) {
                            // If it's still an <i> tag, feather hasn't replaced it yet
                            if (icon.tagName === 'I') {
                                return; // Skip, will be handled by feather.replace
                            }
                            
                            // It's an SVG, set properties
                            icon.setAttribute('width', '16');
                            icon.setAttribute('height', '16');
                            icon.setAttribute('stroke-width', '2');
                            icon.classList.add('sidebar-icon');
                            icon.style.width = '16px';
                            icon.style.height = '16px';
                            icon.style.maxWidth = '16px';
                            icon.style.maxHeight = '16px';
                            icon.style.minWidth = '16px';
                            icon.style.minHeight = '16px';
                            icon.style.verticalAlign = 'middle';
                            icon.style.marginRight = '10px';
                            icon.style.display = 'inline-block';
                            icon.style.float = 'none';
                            icon.style.textAlign = 'left';
                            icon.style.lineHeight = 'normal';
                            icon.style.flexShrink = '0';
                            
                            // Set icon color based on parent state
                            const parentLi = icon.closest('li');
                            const parentLink = icon.closest('a');
                            
                            if (parentLi && parentLi.classList.contains('active')) {
                                icon.style.color = '#60a5fa';
                                icon.style.stroke = '#60a5fa';
                            } else {
                                icon.style.color = 'rgba(255, 255, 255, 0.75)';
                                icon.style.stroke = 'rgba(255, 255, 255, 0.75)';
                            }
                            
                            // Add hover listener
                            if (parentLink) {
                                parentLink.addEventListener('mouseenter', function() {
                                    icon.style.color = '#ffffff';
                                    icon.style.stroke = '#ffffff';
                                });
                                parentLink.addEventListener('mouseleave', function() {
                                    if (parentLi && parentLi.classList.contains('active')) {
                                        icon.style.color = '#60a5fa';
                                        icon.style.stroke = '#60a5fa';
                                    } else {
                                        icon.style.color = 'rgba(255, 255, 255, 0.75)';
                                        icon.style.stroke = 'rgba(255, 255, 255, 0.75)';
                                    }
                                });
                            }
                        });

                    // Ensure treeview menu links use flexbox for proper alignment
                    document.querySelectorAll('.treeview-menu > li > a').forEach(function(link) {
                        if (!link.style.display || link.style.display !== 'flex') {
                            link.style.display = 'flex';
                            link.style.alignItems = 'center';
                            link.style.flexDirection = 'row';
                        }
                    });

                    // All other feather icons
                    document.querySelectorAll('svg[data-feather]').forEach(function(svg) {
                        if (!svg.closest('.sidebar-menu > li > a') && !svg.closest(
                                '.treeview-menu > li > a')) {
                            svg.setAttribute('width', '16');
                            svg.setAttribute('height', '16');
                            svg.setAttribute('stroke-width', '2');
                            svg.style.width = '16px';
                            svg.style.height = '16px';
                            svg.style.maxWidth = '16px';
                            svg.style.maxHeight = '16px';
                        }
                    });
            }, 100);
        }

        // Initialize Feather icons - wait for both DOM and feather library
        function initFeatherIcons() {
            // Check if feather is available
            if (typeof feather === 'undefined') {
                // Wait for feather to load
                let attempts = 0;
                const checkFeather = setInterval(function() {
                    attempts++;
                    if (typeof feather !== 'undefined') {
                        clearInterval(checkFeather);
                        initializeSidebarIcons();
                    } else if (attempts > 50) {
                        clearInterval(checkFeather);
                        console.warn('Feather icons library not loaded');
                    }
                }, 100);
                return;
            }

            // Feather is available, initialize
            initializeSidebarIcons();
            
            // Re-initialize after a delay to catch any missed icons
            setTimeout(function() {
                if (typeof feather !== 'undefined') {
                    feather.replace({
                        width: 18,
                        height: 18,
                        'stroke-width': 2.5
                    });
                    setTimeout(initializeSidebarIcons, 100);
                }
            }, 500);
        }

        // Run on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFeatherIcons);
        } else {
            // DOM already loaded
            initFeatherIcons();
        }

        // Also run on window load as fallback
        window.addEventListener('load', function() {
            setTimeout(initFeatherIcons, 200);
        });

        // Also use jQuery ready if available (after template.js runs)
        if (typeof jQuery !== 'undefined') {
            jQuery(document).ready(function($) {
                setTimeout(function() {
                    if (typeof feather !== 'undefined') {
                        feather.replace({
                            width: 18,
                            height: 18,
                            'stroke-width': 2.5
                        });
                        setTimeout(initializeSidebarIcons, 150);
                    }
                }, 300);
            });
        }
        
        // Re-initialize when sidebar state changes
        if (document.body) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        setTimeout(initializeSidebarIcons, 100);
                    }
                });
            });
            
            observer.observe(document.body, {
                attributes: true,
                attributeFilter: ['class']
            });
        }

        // Sidebar toggle functionality - working with existing pushMenu plugin
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for jQuery and pushMenu plugin to be ready
            if (typeof jQuery !== 'undefined') {
                jQuery(document).ready(function($) {
                    // Initialize sidebar state from localStorage
                    const sidebarState = localStorage.getItem('sidebarState');
                    if (sidebarState === 'collapsed' && !$('body').hasClass('sidebar-collapse')) {
                        $('body').addClass('sidebar-collapse');
                    }

                    // Listen for sidebar toggle events
                    $(document).on('expanded.pushMenu collapsed.pushMenu', function() {
                        // Save state to localStorage when sidebar state changes
                        if ($('body').hasClass('sidebar-collapse')) {
                            localStorage.setItem('sidebarState', 'collapsed');
                        } else {
                            localStorage.setItem('sidebarState', 'expanded');
                        }
                    });

                    // Handle responsive behavior
                    function handleResize() {
                        if (window.innerWidth < 768) {
                            // On mobile, ensure sidebar can be toggled
                            if (!$('body').hasClass('sidebar-collapse')) {
                                // Don't force collapse on mobile, let user control it
                            }
                        }
                    }

                    $(window).on('resize', handleResize);
                    handleResize(); // Check on load
                });
            } else {
                // Fallback if jQuery is not available
                const sidebarState = localStorage.getItem('sidebarState');
                if (sidebarState === 'collapsed') {
                    document.body.classList.add('sidebar-collapse');
                }

                const sidebarToggle = document.querySelector('[data-toggle="push-menu"]');
                if (sidebarToggle) {
                    sidebarToggle.addEventListener('click', function(e) {
                        setTimeout(function() {
                            if (document.body.classList.contains('sidebar-collapse')) {
                                localStorage.setItem('sidebarState', 'collapsed');
                            } else {
                                localStorage.setItem('sidebarState', 'expanded');
                            }
                        }, 100);
                    });
                }
            }

            // Cache Clear Button with SweetAlert2 Confirmation
            const clearCacheBtn = document.getElementById('clear-cache-btn');
            const clearCacheUrl = '{{ route('admin.clear-cache') }}';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (clearCacheBtn) {
                clearCacheBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Clear All Caches?',
                            text: 'This will clear application cache, config cache, route cache, view cache, and permission cache. Are you sure?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, Clear Cache',
                            cancelButtonText: 'Cancel',
                            reverseButtons: true,
                            showLoaderOnConfirm: true,
                            preConfirm: () => {
                                return fetch(clearCacheUrl, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': csrfToken,
                                            'Accept': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    })
                                    .then(response => {
                                        if (!response.ok) {
                                            return response.json().then(data => {
                                                throw new Error(data.message ||
                                                    'Failed to clear cache');
                                            });
                                        }
                                        return response.json();
                                    })
                                    .catch(error => {
                                        Swal.showValidationMessage(error.message ||
                                            'Request failed');
                                    });
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Show success toastr notification
                                toastr.success('All caches cleared successfully!',
                                    'Cache Cleared', {
                                        timeOut: 3000,
                                        progressBar: true
                                    });

                                // Optionally reload the page after a short delay
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            }
                        });
                    } else {
                        // Fallback to default confirm if SweetAlert is not available
                        if (confirm('Are you sure you want to clear all caches?')) {
                            // Create a form and submit
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = clearCacheUrl;
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken;
                            form.appendChild(csrfInput);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    }
                });
            }
        });
    </script>
</body>

</html>

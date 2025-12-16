<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('backend/assets/images/favicon.ico') }}">

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
    <link rel="stylesheet" href="{{ asset('backend/assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/custom.css') }}">
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

        /* Keep sidebar always open */
        .sidebar {
            position: relative !important;
            display: block !important;
        }

        body.sidebar-collapse .sidebar {
            display: block !important;
        }

        body.sidebar-mini:not(.sidebar-mini-expand-feature) .sidebar {
            display: block !important;
        }

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
                <a href="index.html" class="logo">
                    <!-- logo-->
                    <div class="logo-mini w-30">
                        <span class="light-logo"><img src="{{ asset('backend/assets/images/logo-letter.png') }}"
                                alt="logo"></span>
                        <span class="dark-logo"><img src="{{ asset('backend/assets/images/logo-letter.png') }}"
                                alt="logo"></span>
                    </div>
                    <div class="logo-lg">
                        <span class="light-logo"><img src="{{ asset('backend/assets/images/logo-dark-text.png') }}"
                                alt="logo"></span>
                        <span class="dark-logo"><img src="{{ asset('backend/assets/images/logo-light-text.png') }}"
                                alt="logo"></span>
                    </div>
                </a>
            </div>
            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <div class="app-menu">
                    <ul class="header-megamenu nav">
                        <li class="btn-group nav-item">
                            <a href="index.html#" class="waves-effect waves-light nav-link push-btn btn-primary-light"
                                data-toggle="push-menu" role="button">
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
                        <!-- Notifications -->
                        <li class="dropdown notifications-menu">
                            <a href="index.html#" class="waves-effect waves-light dropdown-toggle btn-primary-light"
                                data-bs-toggle="dropdown" title="Notifications">
                                <i data-feather="bell"></i>
                            </a>
                            <ul class="dropdown-menu animated bounceIn">

                                <li class="header">
                                    <div class="p-20">
                                        <div class="flexbox">
                                            <div>
                                                <h4 class="mb-0 mt-0">Notifications</h4>
                                            </div>
                                            <div>
                                                <a href="index.html#" class="text-danger">Clear All</a>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu sm-scrol">
                                        <li>
                                            <a href="index.html#">
                                                <i class="fa fa-users text-info"></i> Curabitur id eros quis nunc
                                                suscipit blandit.
                                            </a>
                                        </li>
                                        <li>
                                            <a href="index.html#">
                                                <i class="fa fa-warning text-warning"></i> Duis malesuada justo eu
                                                sapien elementum, in semper diam posuere.
                                            </a>
                                        </li>
                                        <li>
                                            <a href="index.html#">
                                                <i class="fa fa-users text-danger"></i> Donec at nisi sit amet tortor
                                                commodo porttitor pretium a erat.
                                            </a>
                                        </li>
                                        <li>
                                            <a href="index.html#">
                                                <i class="fa fa-shopping-cart text-success"></i> In gravida mauris et
                                                nisi
                                            </a>
                                        </li>
                                        <li>
                                            <a href="index.html#">
                                                <i class="fa fa-user text-danger"></i> Praesent eu lacus in libero
                                                dictum fermentum.
                                            </a>
                                        </li>
                                        <li>
                                            <a href="index.html#">
                                                <i class="fa fa-user text-primary"></i> Nunc fringilla lorem
                                            </a>
                                        </li>
                                        <li>
                                            <a href="index.html#">
                                                <i class="fa fa-user text-success"></i> Nullam euismod dolor ut quam
                                                interdum, at scelerisque ipsum imperdiet.
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="footer">
                                    <a href="index.html#">View all</a>
                                </li>
                            </ul>
                        </li>

                        <!-- User Account-->
                        <li class="dropdown user user-menu">
                            <a href="index.html#" class="waves-effect waves-light dropdown-toggle btn-primary-light"
                                data-bs-toggle="dropdown" title="User">
                                <i data-feather="user"></i>
                            </a>
                            <ul class="dropdown-menu animated flipInX">
                                <li class="user-body">
                                    <a class="dropdown-item" href="index.html#"><i
                                            class="ti-user text-muted me-2"></i>
                                        Profile</a>
                                    <a class="dropdown-item" href="index.html#"><i
                                            class="ti-wallet text-muted me-2"></i> My
                                        Wallet</a>
                                    <a class="dropdown-item" href="index.html#"><i
                                            class="ti-settings text-muted me-2"></i>
                                        Settings</a>
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
        // Fix Feather icon sizes after initialization
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                // Replace icons with size constraints
                feather.replace({
                    width: 16,
                    height: 16,
                    'stroke-width': 2
                });

                // Ensure all feather icons have proper size
                setTimeout(function() {
                    // Main sidebar menu icons
                    document.querySelectorAll('.sidebar-menu > li > a > svg[data-feather]').forEach(
                        function(svg) {
                            svg.setAttribute('width', '16');
                            svg.setAttribute('height', '16');
                            svg.setAttribute('stroke-width', '2');
                            svg.style.width = '16px';
                            svg.style.height = '16px';
                            svg.style.maxWidth = '16px';
                            svg.style.maxHeight = '16px';
                            svg.style.minWidth = '16px';
                            svg.style.minHeight = '16px';
                            svg.style.verticalAlign = 'middle';
                            svg.style.marginRight = '10px';
                            svg.style.display = 'inline-block';
                        });

                    // Treeview menu icons (submenu) - ensure they're small and inline
                    document.querySelectorAll('.treeview-menu > li > a > svg[data-feather]').forEach(
                        function(svg) {
                            svg.setAttribute('width', '14');
                            svg.setAttribute('height', '14');
                            svg.setAttribute('stroke-width', '2');
                            svg.style.width = '14px';
                            svg.style.height = '14px';
                            svg.style.maxWidth = '14px';
                            svg.style.maxHeight = '14px';
                            svg.style.minWidth = '14px';
                            svg.style.minHeight = '14px';
                            svg.style.verticalAlign = 'middle';
                            svg.style.marginRight = '8px';
                            svg.style.display = 'inline-block';
                            svg.style.float = 'none';
                            svg.style.textAlign = 'left';
                            svg.style.lineHeight = 'normal';
                            svg.style.flexShrink = '0';
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

                // Re-run after a short delay to catch any dynamically loaded icons
                setTimeout(function() {
                    feather.replace({
                        width: 16,
                        height: 16,
                        'stroke-width': 2
                    });
                }, 300);
            }
        });

        // Keep sidebar always open
        document.addEventListener('DOMContentLoaded', function() {
            // Remove sidebar-collapse class if it exists
            document.body.classList.remove('sidebar-collapse');

            // Prevent sidebar toggle from collapsing
            const sidebarToggle = document.querySelector('[data-toggle="push-menu"]');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    // Keep sidebar open
                    document.body.classList.remove('sidebar-collapse');
                    return false;
                });
            }

            // Ensure sidebar stays open on window resize
            window.addEventListener('resize', function() {
                document.body.classList.remove('sidebar-collapse');
            });
        });
    </script>
</body>

</html>

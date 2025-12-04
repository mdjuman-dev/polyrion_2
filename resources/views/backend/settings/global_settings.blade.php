@extends('backend.layouts.master')
@section('title', 'Settings')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <!-- Breadcrumb -->
                <div class="row mb-3">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.backend.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Setting</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-body p-0">
                                <form method="POST" action="{{ route('admin.setting.update') }}" id="settingsForm">
                                    @csrf

                                    <div class="row g-0">
                                        <!-- Left Sidebar Navigation -->
                                        <div class="col-md-3 settings-sidebar">
                                            <div class="settings-nav">
                                                <ul class="nav nav-pills flex-column" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" data-bs-toggle="pill" href="#general"
                                                            role="tab">
                                                            <div class="nav-icon">
                                                                <i data-feather="settings"></i>
                                                            </div>
                                                            <span>General Setting</span>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-bs-toggle="pill" href="#logo"
                                                            role="tab">
                                                            <div class="nav-icon">
                                                                <i data-feather="image"></i>
                                                            </div>
                                                            <span>Logo and Favicon</span>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-bs-toggle="pill" href="#recaptcha"
                                                            role="tab">
                                                            <div class="nav-icon">
                                                                <i data-feather="shield"></i>
                                                            </div>
                                                            <span>Google reCaptcha</span>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-bs-toggle="pill" href="#tawk"
                                                            role="tab">
                                                            <div class="nav-icon">
                                                                <i data-feather="message-circle"></i>
                                                            </div>
                                                            <span>Tawk Chat</span>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-bs-toggle="pill" href="#analytics"
                                                            role="tab">
                                                            <div class="nav-icon">
                                                                <i data-feather="bar-chart-2"></i>
                                                            </div>
                                                            <span>Google Analytic</span>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-bs-toggle="pill" href="#facebook"
                                                            role="tab">
                                                            <div class="nav-icon">
                                                                <i data-feather="facebook"></i>
                                                            </div>
                                                            <span>Facebook Pixel</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Right Content Area -->
                                        <div class="col-md-9 settings-content">
                                            <div class="tab-content p-4">
                                                <!-- General Settings Tab -->
                                                <div class="tab-pane fade show active" id="general" role="tabpanel">
                                                    <h5 class="mb-4">General Setting</h5>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">App Name <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" name="app_name" class="form-control"
                                                                    value="{{ old('app_name', $generalSettings['app_name'] ?? '') }}"
                                                                    placeholder="Enter application name" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Select Theme</label>
                                                                <select name="app_theme" class="form-control">
                                                                    <option value="all"
                                                                        {{ old('app_theme', $generalSettings['app_theme'] ?? '') == 'all' ? 'selected' : '' }}>
                                                                        All Theme</option>
                                                                    <option value="light"
                                                                        {{ old('app_theme', $generalSettings['app_theme'] ?? '') == 'light' ? 'selected' : '' }}>
                                                                        Light Theme</option>
                                                                    <option value="dark"
                                                                        {{ old('app_theme', $generalSettings['app_theme'] ?? '') == 'dark' ? 'selected' : '' }}>
                                                                        Dark Theme</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Commission Type</label>
                                                                <select name="commission_type" class="form-control">
                                                                    <option value="commission"
                                                                        {{ old('commission_type', $generalSettings['commission_type'] ?? '') == 'commission' ? 'selected' : '' }}>
                                                                        Commission</option>
                                                                    <option value="fixed"
                                                                        {{ old('commission_type', $generalSettings['commission_type'] ?? '') == 'fixed' ? 'selected' : '' }}>
                                                                        Fixed Fee</option>
                                                                    <option value="hybrid"
                                                                        {{ old('commission_type', $generalSettings['commission_type'] ?? '') == 'hybrid' ? 'selected' : '' }}>
                                                                        Hybrid</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Commission per sale (%)</label>
                                                                <input type="number" name="commission_percentage"
                                                                    class="form-control"
                                                                    value="{{ old('commission_percentage', $generalSettings['commission_percentage'] ?? '0') }}"
                                                                    placeholder="0">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Contact Message Receiver
                                                                    Email</label>
                                                                <input type="email" name="contact_email"
                                                                    class="form-control"
                                                                    value="{{ old('contact_email', $generalSettings['contact_email'] ?? '') }}"
                                                                    placeholder="freelancermarkets@gmail.com">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Timezone</label>
                                                                <select name="app_timezone" class="form-control">
                                                                    <option value="Asia/Dhaka"
                                                                        {{ old('app_timezone', $generalSettings['app_timezone'] ?? '') == 'Asia/Dhaka' ? 'selected' : '' }}>
                                                                        Asia/Dhaka</option>
                                                                    <option value="UTC"
                                                                        {{ old('app_timezone', $generalSettings['app_timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>
                                                                        UTC</option>
                                                                    <option value="America/New_York"
                                                                        {{ old('app_timezone', $generalSettings['app_timezone'] ?? '') == 'America/New_York' ? 'selected' : '' }}>
                                                                        America/New_York</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Logo and Favicon Tab -->
                                                <div class="tab-pane fade" id="logo" role="tabpanel">
                                                    <h5 class="mb-4">Logo and Favicon</h5>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Site Logo</label>
                                                                <input type="file" name="site_logo"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Favicon</label>
                                                                <input type="file" name="favicon"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Google reCaptcha Tab -->
                                                <div class="tab-pane fade" id="recaptcha" role="tabpanel">
                                                    <h5 class="mb-4">Google reCaptcha</h5>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">reCaptcha Site Key</label>
                                                                <input type="text" name="recaptcha_site_key"
                                                                    class="form-control"
                                                                    value="{{ old('recaptcha_site_key', $recaptchaSettings['site_key'] ?? '') }}"
                                                                    placeholder="Enter your site key">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">reCaptcha Secret Key</label>
                                                                <input type="password" name="recaptcha_secret_key"
                                                                    class="form-control"
                                                                    value="{{ old('recaptcha_secret_key', $recaptchaSettings['secret_key'] ?? '') }}"
                                                                    placeholder="Enter your secret key">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tawk Chat Tab -->
                                                <div class="tab-pane fade" id="tawk" role="tabpanel">
                                                    <h5 class="mb-4">Tawk Chat</h5>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Tawk.to Widget Code</label>
                                                                <textarea name="tawk_widget_code" class="form-control" rows="6"
                                                                    placeholder="Paste your Tawk.to widget code here">{{ old('tawk_widget_code', $tawkSettings['widget_code'] ?? '') }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Google Analytics Tab -->
                                                <div class="tab-pane fade" id="analytics" role="tabpanel">
                                                    <h5 class="mb-4">Google Analytics</h5>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Google Analytics Tracking
                                                                    ID</label>
                                                                <input type="text" name="ga_tracking_id"
                                                                    class="form-control"
                                                                    value="{{ old('ga_tracking_id', $analyticsSettings['tracking_id'] ?? '') }}"
                                                                    placeholder="UA-XXXXXXXXX-X or G-XXXXXXXXXX">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Facebook Pixel Tab -->
                                                <div class="tab-pane fade" id="facebook" role="tabpanel">
                                                    <h5 class="mb-4">Facebook Pixel</h5>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Facebook Pixel ID</label>
                                                                <input type="text" name="fb_pixel_id"
                                                                    class="form-control"
                                                                    value="{{ old('fb_pixel_id', $facebookSettings['pixel_id'] ?? '') }}"
                                                                    placeholder="Enter your Facebook Pixel ID">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Database Clear Tab -->
                                                <div class="tab-pane fade" id="database" role="tabpanel">
                                                    <h5 class="mb-4">Database Clear</h5>

                                                    <div class="alert alert-warning">
                                                        <strong>Warning!</strong> This action will clear all cache and
                                                        temporary data. This cannot be undone.
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <button type="button" class="btn btn-danger"
                                                                onclick="confirm('Are you sure you want to clear the database?') && this.form.submit()">
                                                                <i data-feather="trash-2"></i> Clear Database
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Form Actions -->
                                            <div class="settings-footer p-4 border-top">
                                                <button type="submit" class="btn btn-primary">
                                                    Update
                                                </button>
                                                <button type="reset" class="btn btn-secondary">
                                                    Reset
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .settings-sidebar {
            background-color: #f8f9fa;
            border-right: 1px solid #e5e7eb;
            min-height: 600px;
            padding: 20px 0;
        }

        .settings-sidebar i {
            width: 20px;
            height: 20px;
        }

        .settings-nav {
            padding: 0;
        }

        .settings-nav .nav-link {
            color: #4b5563;
            padding: 14px 24px;
            border-radius: 0;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            font-size: 15px;
        }

        .settings-nav .nav-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #e5e7eb;
            flex-shrink: 0;
        }

        .settings-nav .nav-link:hover {
            background-color: #e9ecef;
            color: #1f2937;
        }

        .settings-nav .nav-link.active {
            background-color: #10b981;
            color: #ffffff;
            border-left-color: #059669;
        }

        .settings-nav .nav-link.active .nav-icon {
            background: rgba(255, 255, 255, 0.2);
            border-color: transparent;
        }

        .settings-content {
            background-color: #ffffff;
        }

        .settings-content .tab-content {
            min-height: 500px;
        }

        .settings-content h5 {
            color: #1f2937;
            font-weight: 600;
            font-size: 20px;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .settings-footer {
            background-color: #fff;
        }

        .btn-primary {
            background: #1f2937;
            border-color: #1f2937;
            padding: 10px 28px;
        }

        .btn-primary:hover {
            background: #111827;
            border-color: #111827;
        }

        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .settings-sidebar {
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
            }

            .settings-nav .nav-link {
                border-left: none;
                border-bottom: 3px solid transparent;
            }

            .settings-nav .nav-link.active {
                border-left: none;
                border-bottom-color: #059669;
            }
        }

        .settings-sidebar svg {
            width: 20px;
            height: 20px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Feather Icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            // Handle form submission
            document.getElementById('settingsForm').addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i data-feather="loader"></i> Updating...';

                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            });

            // Handle tab switching - reinitialize feather icons
            document.querySelectorAll('[data-bs-toggle="pill"]').forEach(function(tab) {
                tab.addEventListener('shown.bs.tab', function() {
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                });
            });
        });
    </script>
@endpush

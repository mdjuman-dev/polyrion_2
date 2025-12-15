@extends('backend.layouts.master')
@section('title', 'Global Settings')
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
                                <form method="POST" action="{{ route('admin.setting.update') }}" id="settingsForm"
                                    enctype="multipart/form-data">
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
                                                                <input type="file" name="site_logo" id="site_logo"
                                                                    class="form-control" accept="image/*">
                                                                @if (isset($generalSettings['logo']) && $generalSettings['logo'])
                                                                    <div class="mt-3">
                                                                        <img src="{{ str_starts_with($generalSettings['logo'], 'http') ? $generalSettings['logo'] : asset('storage/' . $generalSettings['logo']) }}"
                                                                            alt="Current Logo" class="img-preview"
                                                                            style="max-width: 200px; max-height: 100px; border-radius: 8px; border: 2px solid #e5e7eb;"
                                                                            onerror="this.src='{{ asset('backend/assets/images/avatar.png') }}'">
                                                                    </div>
                                                                @endif
                                                                <div id="site_logo_preview" class="mt-3"
                                                                    style="display: none;">
                                                                    <img id="site_logo_preview_img" src=""
                                                                        alt="Logo Preview"
                                                                        style="max-width: 200px; max-height: 100px; border-radius: 8px; border: 2px solid #e5e7eb;">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Favicon</label>
                                                                <input type="file" name="favicon" id="favicon"
                                                                    class="form-control" accept="image/*">
                                                                @if (isset($generalSettings['favicon']) && $generalSettings['favicon'])
                                                                    <div class="mt-3">
                                                                        <img src="{{ str_starts_with($generalSettings['favicon'], 'http') ? $generalSettings['favicon'] : asset('storage/' . $generalSettings['favicon']) }}"
                                                                            alt="Current Favicon" class="img-preview"
                                                                            style="max-width: 64px; max-height: 64px; border-radius: 8px; border: 2px solid #e5e7eb;"
                                                                            onerror="this.src='{{ asset('backend/assets/images/avatar.png') }}'">
                                                                    </div>
                                                                @endif
                                                                <div id="favicon_preview" class="mt-3"
                                                                    style="display: none;">
                                                                    <img id="favicon_preview_img" src=""
                                                                        alt="Favicon Preview"
                                                                        style="max-width: 64px; max-height: 64px; border-radius: 8px; border: 2px solid #e5e7eb;">
                                                                </div>
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
                                                                    value="{{ old('fb_pixel_id', $facebookPixelSettings['pixel_id'] ?? '') }}"
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
        /* Page Background */
        .content-wrapper {
            background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 50%, #f0f2ff 100%);
            min-height: 100vh;
        }

        /* Settings Container */
        .box {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: none;
        }

        .box-body {
            padding: 0 !important;
        }

        /* Sidebar */
        .settings-sidebar {
            background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
            border-right: 2px solid #e5e7eb;
            min-height: 600px;
            padding: 30px 0;
        }

        .settings-nav {
            padding: 0;
        }

        .settings-nav .nav-link {
            color: #4b5563;
            padding: 16px 28px;
            border-radius: 0;
            border-left: 4px solid transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 14px;
            font-weight: 600;
            font-size: 15px;
            margin: 4px 0;
            position: relative;
        }

        .settings-nav .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
        }

        .settings-nav .nav-icon {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #ffffff;
            border: 2px solid #e5e7eb;
            flex-shrink: 0;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .settings-nav .nav-link:hover {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.05) 0%, transparent 100%);
            color: #667eea;
            transform: translateX(4px);
        }

        .settings-nav .nav-link:hover .nav-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
            color: white;
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .settings-nav .nav-link.active {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.1) 0%, transparent 100%);
            color: #667eea;
            border-left-color: #667eea;
            font-weight: 700;
        }

        .settings-nav .nav-link.active::before {
            width: 4px;
        }

        .settings-nav .nav-link.active .nav-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
            color: white;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        /* Content Area */
        .settings-content {
            background-color: #ffffff;
        }

        .settings-content .tab-content {
            min-height: 500px;
            padding: 40px;
        }

        .settings-content h5 {
            color: #1f2937;
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .settings-content h5::before {
            content: '';
            width: 4px;
            height: 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }

        /* Form Elements */
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 10px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-label .text-danger {
            color: #ef4444;
        }

        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #ffffff;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
            transform: translateY(-1px);
        }

        .form-control:hover {
            border-color: #cbd5e1;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        /* Image Preview */
        .img-preview {
            transition: all 0.3s ease;
        }

        .img-preview:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* Footer */
        .settings-footer {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-top: 2px solid #e5e7eb;
            padding: 30px 40px;
            display: flex;
            gap: 15px;
            justify-content: flex-end;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 32px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            padding: 12px 32px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        /* Breadcrumb */
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 20px;
        }

        .breadcrumb-item a {
            color: #667eea;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .breadcrumb-item a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        /* Alert */
        .alert-warning {
            background: linear-gradient(135deg, #fff5e6 0%, #ffe0b2 100%);
            border: 2px solid #ffb74d;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .settings-sidebar {
                border-right: none;
                border-bottom: 2px solid #e5e7eb;
                min-height: auto;
            }

            .settings-nav .nav-link {
                border-left: none;
                border-bottom: 3px solid transparent;
            }

            .settings-nav .nav-link.active {
                border-left: none;
                border-bottom-color: #667eea;
            }

            .settings-content .tab-content {
                padding: 25px 20px;
            }

            .settings-footer {
                flex-direction: column;
                padding: 20px;
            }

            .settings-footer .btn {
                width: 100%;
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

            // Image preview for site_logo
            const siteLogoInput = document.getElementById('site_logo');
            if (siteLogoInput) {
                siteLogoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const preview = document.getElementById('site_logo_preview');
                            const previewImg = document.getElementById('site_logo_preview_img');
                            if (preview && previewImg) {
                                previewImg.src = e.target.result;
                                preview.style.display = 'block';
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Image preview for favicon
            const faviconInput = document.getElementById('favicon');
            if (faviconInput) {
                faviconInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const preview = document.getElementById('favicon_preview');
                            const previewImg = document.getElementById('favicon_preview_img');
                            if (preview && previewImg) {
                                previewImg.src = e.target.result;
                                preview.style.display = 'block';
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
@endpush

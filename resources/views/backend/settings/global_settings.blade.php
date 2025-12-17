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
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-bs-toggle="pill" href="#smtp"
                                                            role="tab">
                                                            <div class="nav-icon">
                                                                <i data-feather="mail"></i>
                                                            </div>
                                                            <span>SMTP Settings</span>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-bs-toggle="pill" href="#email-templates"
                                                            role="tab">
                                                            <div class="nav-icon">
                                                                <i data-feather="edit-3"></i>
                                                            </div>
                                                            <span>Email Templates</span>
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
                                                        <!-- Site Logo -->
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-4">
                                                                <label class="form-label mb-3">
                                                                    <i class="fa fa-image me-2"></i> Site Logo
                                                                </label>



                                                                <!-- Modern Image Uploader -->
                                                                <div class="modern-image-uploader" id="logo_uploader">
                                                                    <input type="file" name="site_logo" id="site_logo"
                                                                        class="file-input-hidden" accept="image/*"
                                                                        onchange="handleLogoUpload(this)">
                                                                    <div class="uploader-container"
                                                                        id="logo_upload_container">
                                                                        <div class="uploader-dropzone" id="logo_dropzone">
                                                                            <div class="uploader-icon-wrapper">
                                                                                <i
                                                                                    class="fa fa-cloud-upload-alt uploader-icon"></i>
                                                                            </div>
                                                                            <div class="uploader-text-content">
                                                                                <p class="uploader-main-text">
                                                                                    <span class="uploader-click-text">Click
                                                                                        to upload</span>
                                                                                    <span class="uploader-or-text">or drag
                                                                                        and drop</span>
                                                                                </p>
                                                                                <p class="uploader-hint-text">PNG, JPG,
                                                                                    GIF, SVG (Max 2MB)</p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="uploader-preview"
                                                                            id="logo_preview_container"
                                                                            style="display: none;">
                                                                            <div class="preview-image-wrapper">
                                                                                <img id="logo_preview_img" src=""
                                                                                    alt="Preview" class="preview-image">
                                                                                <div class="preview-overlay">
                                                                                    <button type="button"
                                                                                        class="preview-remove-btn"
                                                                                        onclick="removeLogoPreview()">
                                                                                        <i class="fa fa-times"></i>
                                                                                    </button>
                                                                                    <button type="button"
                                                                                        class="preview-change-btn"
                                                                                        onclick="document.getElementById('site_logo').click()">
                                                                                        <i class="fa fa-edit"></i> Change
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                            <div class="preview-info">
                                                                                <span class="preview-filename"
                                                                                    id="logo_filename"></span>
                                                                                <span class="preview-filesize"
                                                                                    id="logo_filesize"></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="uploader-progress" id="logo_progress"
                                                                            style="display: none;">
                                                                            <div class="progress-bar-wrapper">
                                                                                <div class="progress-bar"
                                                                                    id="logo_progress_bar"></div>
                                                                            </div>
                                                                            <span class="progress-text"
                                                                                id="logo_progress_text">0%</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- Current Logo Display -->
                                                                @if (isset($generalSettings['logo']) && $generalSettings['logo'])
                                                                    <div class="current-image-container mb-3">
                                                                        <label
                                                                            class="text-muted small mb-2 d-block">Current
                                                                            Logo:</label>
                                                                        <div class="image-preview-wrapper">
                                                                            <img src="{{ str_starts_with($generalSettings['logo'], 'http') ? $generalSettings['logo'] : asset('storage/' . $generalSettings['logo']) }}"
                                                                                alt="Current Logo" id="current_logo_img"
                                                                                class="current-image"
                                                                                onerror="this.src='{{ asset('backend/assets/images/avatar.png') }}'; this.onerror=null;">
                                                                            <div class="image-overlay">
                                                                                <span class="image-label">Current
                                                                                    Logo</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                <!-- New Logo Preview -->
                                                                <div id="site_logo_preview" class="new-image-preview"
                                                                    style="display: none;">
                                                                    <label class="text-muted small mb-2 d-block">New Logo
                                                                        Preview:</label>
                                                                    <div class="image-preview-wrapper">
                                                                        <img id="site_logo_preview_img" src=""
                                                                            alt="Logo Preview" class="preview-image">
                                                                        <div class="image-overlay">
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-danger"
                                                                                onclick="removeLogoPreview()">
                                                                                <i class="fa fa-times"></i> Remove
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Favicon -->
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-4">
                                                                <label class="form-label mb-3">
                                                                    <i class="fa fa-star me-2"></i> Favicon
                                                                </label>

                                                                <!-- Current Favicon Display -->
                                                                @if (isset($generalSettings['favicon']) && $generalSettings['favicon'])
                                                                    <div class="current-image-container mb-3">
                                                                        <label
                                                                            class="text-muted small mb-2 d-block">Current
                                                                            Favicon:</label>
                                                                        <div class="favicon-preview-wrapper">
                                                                            <img src="{{ str_starts_with($generalSettings['favicon'], 'http') ? $generalSettings['favicon'] : asset('storage/' . $generalSettings['favicon']) }}"
                                                                                alt="Current Favicon"
                                                                                id="current_favicon_img"
                                                                                class="current-favicon"
                                                                                onerror="this.src='{{ asset('backend/assets/images/avatar.png') }}'; this.onerror=null;">
                                                                            <div class="image-overlay">
                                                                                <span class="image-label">Current
                                                                                    Favicon</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                <!-- Modern Image Uploader -->
                                                                <div class="modern-image-uploader favicon-uploader"
                                                                    id="favicon_uploader">
                                                                    <input type="file" name="favicon" id="favicon"
                                                                        class="file-input-hidden" accept="image/*"
                                                                        onchange="handleFaviconUpload(this)">
                                                                    <div class="uploader-container"
                                                                        id="favicon_upload_container">
                                                                        <div class="uploader-dropzone"
                                                                            id="favicon_dropzone">
                                                                            <div class="uploader-icon-wrapper">
                                                                                <i
                                                                                    class="fa fa-cloud-upload-alt uploader-icon"></i>
                                                                            </div>
                                                                            <div class="uploader-text-content">
                                                                                <p class="uploader-main-text">
                                                                                    <span class="uploader-click-text">Click
                                                                                        to upload</span>
                                                                                    <span class="uploader-or-text">or drag
                                                                                        and drop</span>
                                                                                </p>
                                                                                <p class="uploader-hint-text">ICO, PNG, JPG
                                                                                    (Max 1MB, Recommended: 32x32 or 64x64)
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="uploader-preview"
                                                                            id="favicon_preview_container"
                                                                            style="display: none;">
                                                                            <div
                                                                                class="preview-image-wrapper favicon-preview-wrapper">
                                                                                <img id="favicon_preview_img"
                                                                                    src="" alt="Preview"
                                                                                    class="preview-image">
                                                                                <div class="preview-overlay">
                                                                                    <button type="button"
                                                                                        class="preview-remove-btn"
                                                                                        onclick="removeFaviconPreview()">
                                                                                        <i class="fa fa-times"></i>
                                                                                    </button>
                                                                                    <button type="button"
                                                                                        class="preview-change-btn"
                                                                                        onclick="document.getElementById('favicon').click()">
                                                                                        <i class="fa fa-edit"></i> Change
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                            <div class="preview-info">
                                                                                <span class="preview-filename"
                                                                                    id="favicon_filename"></span>
                                                                                <span class="preview-filesize"
                                                                                    id="favicon_filesize"></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="uploader-progress"
                                                                            id="favicon_progress" style="display: none;">
                                                                            <div class="progress-bar-wrapper">
                                                                                <div class="progress-bar"
                                                                                    id="favicon_progress_bar"></div>
                                                                            </div>
                                                                            <span class="progress-text"
                                                                                id="favicon_progress_text">0%</span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- New Favicon Preview -->
                                                                <div id="favicon_preview" class="new-image-preview"
                                                                    style="display: none;">
                                                                    <label class="text-muted small mb-2 d-block">New
                                                                        Favicon Preview:</label>
                                                                    <div class="favicon-preview-wrapper">
                                                                        <img id="favicon_preview_img" src=""
                                                                            alt="Favicon Preview" class="preview-favicon">
                                                                        <div class="image-overlay">
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-danger"
                                                                                onclick="removeFaviconPreview()">
                                                                                <i class="fa fa-times"></i> Remove
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="alert alert-info mt-4">
                                                        <strong><i class="fa fa-info-circle"></i> Tips:</strong>
                                                        <ul class="mb-0 mt-2">
                                                            <li>Logo: Recommended size 200x60px or similar aspect ratio</li>
                                                            <li>Favicon: Recommended size 32x32px or 64x64px (square format)
                                                            </li>
                                                            <li>Supported formats: PNG, JPG, GIF, ICO</li>
                                                            <li>Maximum file size: 2MB for logo, 1MB for favicon</li>
                                                        </ul>
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

                                                <!-- SMTP Settings Tab -->
                                                <div class="tab-pane fade" id="smtp" role="tabpanel">
                                                    <h5 class="mb-4">SMTP Email Settings</h5>

                                                    <div class="alert alert-info">
                                                        <i class="fa fa-info-circle"></i> Configure your SMTP settings to
                                                        send emails from your application.
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Mail Driver <span
                                                                        class="text-danger">*</span></label>
                                                                <select name="mail_mailer" class="form-control" required>
                                                                    <option value="smtp"
                                                                        {{ old('mail_mailer', $mailSettings['mail_mailer'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>
                                                                        SMTP</option>
                                                                    <option value="sendmail"
                                                                        {{ old('mail_mailer', $mailSettings['mail_mailer'] ?? '') == 'sendmail' ? 'selected' : '' }}>
                                                                        Sendmail</option>
                                                                    <option value="log"
                                                                        {{ old('mail_mailer', $mailSettings['mail_mailer'] ?? '') == 'log' ? 'selected' : '' }}>
                                                                        Log (Testing)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">SMTP Host <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" name="mail_host"
                                                                    class="form-control"
                                                                    value="{{ old('mail_host', $mailSettings['mail_host'] ?? '') }}"
                                                                    placeholder="smtp.gmail.com" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">SMTP Port <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="number" name="mail_port"
                                                                    class="form-control"
                                                                    value="{{ old('mail_port', $mailSettings['mail_port'] ?? '587') }}"
                                                                    placeholder="587" required>
                                                                <small class="form-text text-muted">
                                                                    Common ports: 587 (TLS), 465 (SSL), 25 (Non-encrypted)
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Encryption</label>
                                                                <select name="mail_encryption" class="form-control">
                                                                    <option value="tls"
                                                                        {{ old('mail_encryption', $mailSettings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>
                                                                        TLS</option>
                                                                    <option value="ssl"
                                                                        {{ old('mail_encryption', $mailSettings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>
                                                                        SSL</option>
                                                                    <option value=""
                                                                        {{ old('mail_encryption', $mailSettings['mail_encryption'] ?? '') == '' ? 'selected' : '' }}>
                                                                        None</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">SMTP Username <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="email" name="mail_username"
                                                                    class="form-control"
                                                                    value="{{ old('mail_username', $mailSettings['mail_username'] ?? '') }}"
                                                                    placeholder="your-email@gmail.com" required>
                                                                <small class="form-text text-muted">
                                                                    Must be your full Gmail address (e.g.,
                                                                    yourname@gmail.com)
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">SMTP Password <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="password" name="mail_password"
                                                                    class="form-control"
                                                                    value="{{ old('mail_password', $mailSettings['mail_password'] ?? '') }}"
                                                                    placeholder="Gmail App Password (16 characters)"
                                                                    required>
                                                                <small class="form-text text-muted">
                                                                    <strong>For Gmail:</strong> You MUST use App Password
                                                                    (not regular password).
                                                                    <a href="https://support.google.com/accounts/answer/185833"
                                                                        target="_blank">How to create App Password</a>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">From Email Address <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="email" name="mail_from_address"
                                                                    class="form-control"
                                                                    value="{{ old('mail_from_address', $mailSettings['mail_from_address'] ?? '') }}"
                                                                    placeholder="noreply@yourdomain.com" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">From Name <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" name="mail_from_name"
                                                                    class="form-control"
                                                                    value="{{ old('mail_from_name', $mailSettings['mail_from_name'] ?? config('app.name')) }}"
                                                                    placeholder="Your App Name" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="alert alert-info mt-3">
                                                        <strong><i class="fa fa-info-circle"></i>
                                                            Gmail Setup Instructions:</strong>
                                                        <ol class="mb-0 mt-2">
                                                            <li><strong>Enable 2-Step Verification:</strong> Go to your
                                                                Google Account → Security → Enable 2-Step Verification</li>
                                                            <li><strong>Create App Password:</strong> After enabling 2-Step
                                                                Verification, go to Security → App Passwords → Generate a
                                                                new app password</li>
                                                            <li><strong>Use App Password:</strong> Copy the 16-character app
                                                                password and paste it in the SMTP Password field above (NOT
                                                                your regular Gmail password)</li>
                                                            <li><strong>Username:</strong> Use your full Gmail address
                                                                (e.g., yourname@gmail.com)</li>
                                                            <li><strong>Settings:</strong> Host: smtp.gmail.com, Port: 587,
                                                                Encryption: TLS</li>
                                                        </ol>
                                                        <p class="mb-0 mt-2"><strong>Note:</strong> Regular Gmail passwords
                                                            will NOT work. You MUST use App Password.</p>
                                                    </div>
                                                </div>

                                                <!-- Email Templates Tab -->
                                                <div class="tab-pane fade" id="email-templates" role="tabpanel">
                                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                                        <div>
                                                            <h5 class="mb-1">Email Templates</h5>
                                                            <p class="text-muted mb-0">Customize email templates that users
                                                                receive. Logo from settings will be used automatically.</p>
                                                        </div>
                                                    </div>

                                                    <!-- Template Selection -->
                                                    <div class="card mb-4">
                                                        <div class="card-header">
                                                            <h6 class="mb-0">Select Email Template</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="btn-group template-selector" role="group">
                                                                <button type="button"
                                                                    class="btn btn-primary active template-btn"
                                                                    data-template="reset-password">
                                                                    <i class="fa fa-key"></i> Reset Password
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-outline-primary template-btn"
                                                                    data-template="welcome">
                                                                    <i class="fa fa-envelope"></i> Welcome Email
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-outline-primary template-btn"
                                                                    data-template="verification">
                                                                    <i class="fa fa-check-circle"></i> Email Verification
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Reset Password Template -->
                                                    <div id="reset-password-template" class="template-section">
                                                        <h6 class="mb-3"><i class="fa fa-key"></i> Reset Password Email
                                                            Template</h6>
                                                        <p class="text-muted mb-4">Customize the reset password email that
                                                            users receive. You can use placeholders: <code>:name</code>
                                                            (user
                                                            name), <code>:count</code> (expiry minutes),
                                                            <code>:app_name</code>
                                                            (application name)
                                                        </p>

                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">Email Subject <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" name="reset_password_subject"
                                                                        class="form-control"
                                                                        value="{{ old('reset_password_subject', $emailTemplateSettings['reset_password_subject'] ?? 'Reset Password Notification') }}"
                                                                        placeholder="Reset Password Notification" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">Greeting <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" name="reset_password_greeting"
                                                                        class="form-control"
                                                                        value="{{ old('reset_password_greeting', $emailTemplateSettings['reset_password_greeting'] ?? 'Hello :name!') }}"
                                                                        placeholder="Hello :name!" required>
                                                                    <small class="form-text text-muted">Use :name
                                                                        placeholder
                                                                        for user's name</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">First Line (Main Message)
                                                                        <span class="text-danger">*</span></label>
                                                                    <textarea name="reset_password_line1" class="form-control" rows="3" required
                                                                        placeholder="You are receiving this email because we received a password reset request for your account.">{{ old('reset_password_line1', $emailTemplateSettings['reset_password_line1'] ?? 'You are receiving this email because we received a password reset request for your account.') }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">Button Text <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text"
                                                                        name="reset_password_action_text"
                                                                        class="form-control"
                                                                        value="{{ old('reset_password_action_text', $emailTemplateSettings['reset_password_action_text'] ?? 'Reset Password') }}"
                                                                        placeholder="Reset Password" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">Second Line (Expiry Message)
                                                                        <span class="text-danger">*</span></label>
                                                                    <textarea name="reset_password_line2" class="form-control" rows="2" required
                                                                        placeholder="This password reset link will expire in :count minutes.">{{ old('reset_password_line2', $emailTemplateSettings['reset_password_line2'] ?? 'This password reset link will expire in :count minutes.') }}</textarea>
                                                                    <small class="form-text text-muted">Use :count
                                                                        placeholder
                                                                        for expiry minutes</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">Third Line (Security Message)
                                                                        <span class="text-danger">*</span></label>
                                                                    <textarea name="reset_password_line3" class="form-control" rows="2" required
                                                                        placeholder="If you did not request a password reset, no further action is required.">{{ old('reset_password_line3', $emailTemplateSettings['reset_password_line3'] ?? 'If you did not request a password reset, no further action is required.') }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">Salutation (Closing) <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" name="reset_password_salutation"
                                                                        class="form-control"
                                                                        value="{{ old('reset_password_salutation', $emailTemplateSettings['reset_password_salutation'] ?? 'Regards,') }}"
                                                                        placeholder="Regards," required>
                                                                    <small class="form-text text-muted">This will be
                                                                        followed
                                                                        by your app name automatically</small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="alert alert-info mt-3">
                                                            <strong><i class="fa fa-info-circle"></i> Available
                                                                Placeholders:</strong>
                                                            <ul class="mb-0 mt-2">
                                                                <li><code>:name</code> - User's full name</li>
                                                                <li><code>:count</code> - Password reset link expiry time in
                                                                    minutes</li>
                                                                <li><code>:app_name</code> - Application name (automatically
                                                                    added at the end)</li>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <!-- Welcome Email Template -->
                                                    <div id="welcome-template" class="template-section"
                                                        style="display: none;">
                                                        <h6 class="mb-3"><i class="fa fa-envelope"></i> Welcome Email
                                                            Template</h6>
                                                        <p class="text-muted mb-4">Customize the welcome email sent to new
                                                            users. Placeholders: <code>:name</code>, <code>:app_name</code>
                                                        </p>

                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">Email Subject</label>
                                                                    <input type="text" name="welcome_email_subject"
                                                                        class="form-control"
                                                                        value="{{ old('welcome_email_subject', $emailTemplateSettings['welcome_email_subject'] ?? 'Welcome to :app_name!') }}"
                                                                        placeholder="Welcome to :app_name!">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">Greeting</label>
                                                                    <input type="text" name="welcome_email_greeting"
                                                                        class="form-control"
                                                                        value="{{ old('welcome_email_greeting', $emailTemplateSettings['welcome_email_greeting'] ?? 'Hello :name!') }}"
                                                                        placeholder="Hello :name!">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">Welcome Message</label>
                                                                    <textarea name="welcome_email_message" class="form-control" rows="4"
                                                                        placeholder="Thank you for joining :app_name!">{{ old('welcome_email_message', $emailTemplateSettings['welcome_email_message'] ?? 'Thank you for joining :app_name! We are excited to have you on board.') }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Email Verification Template -->
                                                    <div id="verification-template" class="template-section"
                                                        style="display: none;">
                                                        <h6 class="mb-3"><i class="fa fa-check-circle"></i> Email
                                                            Verification Template</h6>
                                                        <p class="text-muted mb-4">Customize the email verification email.
                                                            Placeholders: <code>:name</code>, <code>:app_name</code></p>

                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">Email Subject</label>
                                                                    <input type="text"
                                                                        name="verification_email_subject"
                                                                        class="form-control"
                                                                        value="{{ old('verification_email_subject', $emailTemplateSettings['verification_email_subject'] ?? 'Verify Your Email Address') }}"
                                                                        placeholder="Verify Your Email Address">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">Greeting</label>
                                                                    <input type="text"
                                                                        name="verification_email_greeting"
                                                                        class="form-control"
                                                                        value="{{ old('verification_email_greeting', $emailTemplateSettings['verification_email_greeting'] ?? 'Hello :name!') }}"
                                                                        placeholder="Hello :name!">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">Verification Message</label>
                                                                    <textarea name="verification_email_message" class="form-control" rows="4"
                                                                        placeholder="Please verify your email address.">{{ old('verification_email_message', $emailTemplateSettings['verification_email_message'] ?? 'Please click the button below to verify your email address.') }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">Button Text</label>
                                                                    <input type="text"
                                                                        name="verification_email_button_text"
                                                                        class="form-control"
                                                                        value="{{ old('verification_email_button_text', $emailTemplateSettings['verification_email_button_text'] ?? 'Verify Email Address') }}"
                                                                        placeholder="Verify Email Address">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="alert alert-success mt-4">
                                                        <strong><i class="fa fa-check-circle"></i> Logo
                                                            Information:</strong>
                                                        <p class="mb-0 mt-2">Email templates will automatically use the
                                                            logo from <strong>Logo and Favicon</strong> settings. If no logo
                                                            is set, the default logo will be used.</p>
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
                                                            <button type="button" class="btn btn-danger" id="clearDatabaseBtn">
                                                                <i data-feather="trash-2"></i> Clear Database
                                                            </button>
                                                            <script>
                                                                document.addEventListener('DOMContentLoaded', function() {
                                                                    const clearBtn = document.getElementById('clearDatabaseBtn');
                                                                    if (clearBtn) {
                                                                        clearBtn.addEventListener('click', function(e) {
                                                                            e.preventDefault();
                                                                            const form = this.closest('form');
                                                                            if (typeof Swal !== 'undefined') {
                                                                                Swal.fire({
                                                                                    title: 'Are you sure?',
                                                                                    text: 'Are you sure you want to clear the database? This action cannot be undone!',
                                                                                    icon: 'warning',
                                                                                    showCancelButton: true,
                                                                                    confirmButtonColor: '#d33',
                                                                                    cancelButtonColor: '#6c757d',
                                                                                    confirmButtonText: 'Yes, clear database',
                                                                                    cancelButtonText: 'Cancel'
                                                                                }).then((result) => {
                                                                                    if (result.isConfirmed && form) {
                                                                                        form.submit();
                                                                                    }
                                                                                });
                                                                            } else if (confirm('Are you sure you want to clear the database?') && form) {
                                                                                form.submit();
                                                                            }
                                                                        });
                                                                    }
                                                                });
                                                            </script>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Form Actions -->
                                            <div class="settings-footer p-4 border-top">
                                                <button type="submit" class="btn btn-primary">
                                                    Update
                                                </button>
                                                <button type="button" class="btn btn-secondary" onclick="resetForm()">
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

        /* Email Template Styles */
        .template-section {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .btn-group .btn {
            margin-right: 5px;
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

        /* Settings sidebar icons */
        .settings-sidebar svg,
        .settings-nav svg[data-feather],
        .settings-nav i[data-feather] {
            width: 18px !important;
            height: 18px !important;
            max-width: 18px !important;
            max-height: 18px !important;
            display: inline-block !important;
        }

        .nav-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            min-width: 24px;
        }

        .nav-icon svg,
        .nav-icon i[data-feather],
        .nav-icon svg[data-feather] {
            width: 18px !important;
            height: 18px !important;
            max-width: 18px !important;
            max-height: 18px !important;
            display: inline-block !important;
        }

        /* Ensure icons are visible */
        .settings-nav .nav-icon i[data-feather] {
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* Modern Image Uploader Styles */
        .modern-image-uploader {
            margin-bottom: 20px;
        }

        .file-input-hidden {
            display: none;
        }

        .uploader-container {
            position: relative;
        }

        .uploader-dropzone {
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            padding: 50px 30px;
            text-align: center;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .uploader-dropzone::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            transition: left 0.5s;
        }

        .uploader-dropzone:hover::before {
            left: 100%;
        }

        .uploader-dropzone:hover,
        .uploader-dropzone.drag-over {
            border-color: #667eea;
            background: linear-gradient(135deg, #e6edff 0%, #f0f4ff 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.15);
        }

        .uploader-icon-wrapper {
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .uploader-icon {
            font-size: 56px;
            color: #667eea;
            display: block;
        }

        .uploader-text-content {
            z-index: 1;
        }

        .uploader-main-text {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .uploader-click-text {
            color: #667eea;
        }

        .uploader-or-text {
            color: #718096;
            font-size: 14px;
            font-weight: 400;
        }

        .uploader-hint-text {
            font-size: 13px;
            color: #718096;
            margin: 0;
        }

        .uploader-preview {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            background: #ffffff;
            transition: all 0.3s ease;
        }

        .preview-image-wrapper {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
            background: #f8f9fa;
            min-height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .favicon-preview-wrapper {
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
        }

        .preview-image {
            max-width: 100%;
            max-height: 200px;
            object-fit: contain;
            display: block;
        }

        .favicon-preview-wrapper .preview-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .preview-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .preview-image-wrapper:hover .preview-overlay {
            opacity: 1;
        }

        .preview-remove-btn,
        .preview-change-btn {
            background: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px 15px;
            color: #2d3748;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .preview-remove-btn {
            background: #ef4444;
            color: #fff;
        }

        .preview-remove-btn:hover {
            background: #dc2626;
            transform: scale(1.05);
        }

        .preview-change-btn {
            background: #667eea;
            color: #fff;
        }

        .preview-change-btn:hover {
            background: #5568d3;
            transform: scale(1.05);
        }

        .preview-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
        }

        .preview-filename {
            font-size: 14px;
            font-weight: 500;
            color: #2d3748;
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .preview-filesize {
            font-size: 12px;
            color: #718096;
            margin-left: 10px;
        }

        .uploader-progress {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .progress-bar-wrapper {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            width: 0%;
            transition: width 0.3s ease;
            border-radius: 4px;
        }

        .progress-text {
            font-size: 12px;
            color: #667eea;
            font-weight: 600;
            text-align: center;
            display: block;
        }

        .current-image-container {
            margin-bottom: 20px;
        }

        .image-preview-wrapper,
        .favicon-preview-wrapper {
            position: relative;
            display: inline-block;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #e5e7eb;
            background: #ffffff;
            padding: 10px;
            transition: all 0.3s ease;
        }

        .image-preview-wrapper:hover,
        .favicon-preview-wrapper:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .current-image {
            max-width: 200px;
            max-height: 100px;
            height: auto;
            width: auto;
            display: block;
            border-radius: 4px;
        }

        .current-favicon {
            width: 64px;
            height: 64px;
            object-fit: contain;
            display: block;
            border-radius: 4px;
        }

        .preview-image {
            max-width: 200px;
            max-height: 100px;
            height: auto;
            width: auto;
            display: block;
            border-radius: 4px;
        }

        .preview-favicon {
            width: 64px;
            height: 64px;
            object-fit: contain;
            display: block;
            border-radius: 4px;
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 8px;
        }

        .image-preview-wrapper:hover .image-overlay,
        .favicon-preview-wrapper:hover .image-overlay {
            opacity: 1;
        }

        .image-label {
            color: #ffffff;
            font-size: 14px;
            font-weight: 500;
        }

        .new-image-preview {
            margin-top: 15px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .favicon-upload {
            padding: 30px 20px;
        }

        .favicon-upload .upload-icon {
            font-size: 36px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Function to initialize feather icons
        function initializeFeatherIcons() {
            if (typeof feather !== 'undefined') {
                // Replace all feather icons in settings page
                feather.replace({
                    width: 18,
                    height: 18
                });

                // Ensure all feather icons in settings sidebar have proper size
                setTimeout(function() {
                    // Fix icons that are still <i> tags (not yet replaced)
                    document.querySelectorAll('.settings-nav i[data-feather]').forEach(function(icon) {
                        // Check if icon has been replaced by feather
                        const svg = icon.nextElementSibling;
                        if (svg && svg.tagName === 'svg' && svg.hasAttribute('data-feather')) {
                            svg.setAttribute('width', '18');
                            svg.setAttribute('height', '18');
                            svg.style.width = '18px';
                            svg.style.height = '18px';
                            svg.style.maxWidth = '18px';
                            svg.style.maxHeight = '18px';
                        }
                    });

                    // Fix any SVG elements directly
                    document.querySelectorAll('.settings-nav svg[data-feather], .nav-icon svg[data-feather]')
                        .forEach(function(svg) {
                            svg.setAttribute('width', '18');
                            svg.setAttribute('height', '18');
                            svg.style.width = '18px';
                            svg.style.height = '18px';
                            svg.style.maxWidth = '18px';
                            svg.style.maxHeight = '18px';
                        });
                }, 150);
            }
        }

        // Initialize icons when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for feather library to be loaded
            if (typeof feather !== 'undefined') {
                initializeFeatherIcons();
            } else {
                // If feather is not loaded yet, wait and try again
                let attempts = 0;
                const checkFeather = setInterval(function() {
                    attempts++;
                    if (typeof feather !== 'undefined') {
                        clearInterval(checkFeather);
                        initializeFeatherIcons();
                    } else if (attempts > 20) {
                        clearInterval(checkFeather);
                        console.warn('Feather icons library not loaded after 2 seconds');
                    }
                }, 100);
            }
        });

        // Also initialize on window load (in case DOMContentLoaded fires too early)
        window.addEventListener('load', function() {
            if (typeof feather !== 'undefined') {
                initializeFeatherIcons();
            }
        });

        // Handle form submission
        const settingsForm = document.getElementById('settingsForm');
        // jQuery-based Form Submission Handler
        $(document).ready(function() {
            const $settingsForm = $('#settingsForm');

            if ($settingsForm.length) {
                $settingsForm.on('submit', function(e) {
                    const $logoInput = $('#site_logo');
                    const $faviconInput = $('#favicon');
                    const $submitBtn = $(this).find('button[type="submit"]');

                    // Verify files are present before submission
                    if ($logoInput[0] && $logoInput[0].files && $logoInput[0].files.length > 0) {
                        console.log('Logo file will be submitted:', $logoInput[0].files[0].name, $logoInput[
                            0].files[0].size);
                    } else {
                        console.log('No logo file selected');
                    }

                    if ($faviconInput[0] && $faviconInput[0].files && $faviconInput[0].files.length > 0) {
                        console.log('Favicon file will be submitted:', $faviconInput[0].files[0].name,
                            $faviconInput[0].files[0].size);
                    } else {
                        console.log('No favicon file selected');
                    }

                    // Verify form has enctype
                    if ($(this).attr('enctype') !== 'multipart/form-data') {
                        console.error('Form missing enctype="multipart/form-data"');
                        $(this).attr('enctype', 'multipart/form-data');
                    }

                    // Prevent default submission if files are selected but form doesn't have proper enctype
                    const formEnctype = $(this).attr('enctype');
                    if (($logoInput[0] && $logoInput[0].files && $logoInput[0].files.length > 0) ||
                        ($faviconInput[0] && $faviconInput[0].files && $faviconInput[0].files.length > 0)) {
                        if (!formEnctype || formEnctype !== 'multipart/form-data') {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'error',
                                title: 'Form Error',
                                text: 'Form configuration error. Please refresh the page and try again.',
                                confirmButtonColor: '#667eea'
                            });
                            return false;
                        }
                    }

                    // Disable submit button and show loading
                    if ($submitBtn.length) {
                        $submitBtn.prop('disabled', true);
                        const originalHtml = $submitBtn.html();
                        $submitBtn.html('<i data-feather="loader"></i> Updating...');
                        initializeFeatherIcons();

                        // Re-enable button after 10 seconds as fallback
                        setTimeout(function() {
                            $submitBtn.prop('disabled', false);
                            $submitBtn.html(originalHtml);
                        }, 10000);
                    }

                    // Let form submit normally - files should be included automatically
                });
            }
        });

        // Handle tab switching - reinitialize feather icons
        document.querySelectorAll('[data-bs-toggle="pill"]').forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function() {
            initializeFeatherIcons();
        });
        });

        });

        // jQuery-based Modern Logo Upload Handler
        function handleLogoUpload(input) {
            if (!input || !input.files || !input.files[0]) return;

            const file = input.files[0];
            const $input = $(input);
            const $dropzone = $('#logo_dropzone');
            const $previewContainer = $('#logo_preview_container');
            const $previewImg = $('#logo_preview_img');
            const $filename = $('#logo_filename');
            const $filesize = $('#logo_filesize');
            const $progress = $('#logo_progress');
            const $progressBar = $('#logo_progress_bar');
            const $progressText = $('#logo_progress_text');

            // Validate file size (2MB max)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Logo file size must be less than 2MB',
                    confirmButtonColor: '#667eea'
                });
                $input.val('');
                return;
            }

            // Validate file type
            if (!file.type.match('image.*')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please select a valid image file (PNG, JPG, GIF, SVG)',
                    confirmButtonColor: '#667eea'
                });
                $input.val('');
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                $previewImg.attr('src', e.target.result);
                $dropzone.hide();
                $previewContainer.show();

                $filename.text(file.name);
                $filesize.text(formatFileSize(file.size));

                // Hide current logo if exists
                $('#current_logo_img').closest('.current-image-container').css('opacity', '0.5');
            };
            reader.readAsDataURL(file);
        }

        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // jQuery-based Modern Favicon Upload Handler
        function handleFaviconUpload(input) {
            if (!input || !input.files || !input.files[0]) return;

            const file = input.files[0];
            const $input = $(input);
            const $dropzone = $('#favicon_dropzone');
            const $previewContainer = $('#favicon_preview_container');
            const $previewImg = $('#favicon_preview_img');
            const $filename = $('#favicon_filename');
            const $filesize = $('#favicon_filesize');
            const $progress = $('#favicon_progress');
            const $progressBar = $('#favicon_progress_bar');
            const $progressText = $('#favicon_progress_text');

            // Validate file size (1MB max)
            if (file.size > 1 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Favicon file size must be less than 1MB',
                    confirmButtonColor: '#667eea'
                });
                $input.val('');
                return;
            }

            // Validate file type
            if (!file.type.match('image.*')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please select a valid image file (ICO, PNG, JPG)',
                    confirmButtonColor: '#667eea'
                });
                $input.val('');
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                $previewImg.attr('src', e.target.result);
                $dropzone.hide();
                $previewContainer.show();

                $filename.text(file.name);
                $filesize.text(formatFileSize(file.size));

                // Hide current favicon if exists
                $('#current_favicon_img').closest('.current-image-container').css('opacity', '0.5');
            };
            reader.readAsDataURL(file);
        }

        // jQuery-based Remove Logo Preview
        function removeLogoPreview() {
            const $dropzone = $('#logo_dropzone');
            const $previewContainer = $('#logo_preview_container');
            const $input = $('#site_logo');

            $previewContainer.hide();
            $dropzone.show();
            $input.val('');

            // Show current logo again
            $('#current_logo_img').closest('.current-image-container').css('opacity', '1');
        }

        // jQuery-based Remove Favicon Preview
        function removeFaviconPreview() {
            const $dropzone = $('#favicon_dropzone');
            const $previewContainer = $('#favicon_preview_container');
            const $input = $('#favicon');

            $previewContainer.hide();
            $dropzone.show();
            $input.val('');

            // Show current favicon again
            $('#current_favicon_img').closest('.current-image-container').css('opacity', '1');
        }

        // jQuery-based Drag and Drop for Logo
        $(document).ready(function() {
            const $logoDropzone = $('#logo_dropzone');
            const $logoInput = $('#site_logo');

            if ($logoDropzone.length && $logoInput.length) {
                // Click to upload
                $logoDropzone.on('click', function() {
                    $logoInput.click();
                });

                // Prevent default drag behaviors
                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                // Drag and drop handlers
                $logoDropzone.on('dragenter dragover', function(e) {
                    preventDefaults(e);
                    $(this).addClass('drag-over');
                });

                $logoDropzone.on('dragleave drop', function(e) {
                    preventDefaults(e);
                    $(this).removeClass('drag-over');
                });

                $logoDropzone.on('drop', function(e) {
                    preventDefaults(e);
                    const files = e.originalEvent.dataTransfer.files;
                    if (files.length > 0) {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(files[0]);
                        $logoInput[0].files = dataTransfer.files;
                        handleLogoUpload($logoInput[0]);
                    }
                });
            }
        });

        // jQuery-based Drag and Drop for Favicon
        $(document).ready(function() {
            const $faviconDropzone = $('#favicon_dropzone');
            const $faviconInput = $('#favicon');

            if ($faviconDropzone.length && $faviconInput.length) {
                // Click to upload
                $faviconDropzone.on('click', function() {
                    $faviconInput.click();
                });

                // Prevent default drag behaviors
                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                // Drag and drop handlers
                $faviconDropzone.on('dragenter dragover', function(e) {
                    preventDefaults(e);
                    $(this).addClass('drag-over');
                });

                $faviconDropzone.on('dragleave drop', function(e) {
                    preventDefaults(e);
                    $(this).removeClass('drag-over');
                });

                $faviconDropzone.on('drop', function(e) {
                    preventDefaults(e);
                    const files = e.originalEvent.dataTransfer.files;
                    if (files.length > 0) {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(files[0]);
                        $faviconInput[0].files = dataTransfer.files;
                        handleFaviconUpload($faviconInput[0]);
                    }
                });
            }
        });
        // Reset form function (preserve file inputs)
        function resetForm() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Are you sure you want to reset all form fields? This will clear any unsaved changes.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, reset',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performReset();
                    }
                });
            } else if (confirm('Are you sure you want to reset all form fields? This will clear any unsaved changes.')) {
                performReset();
            }
            
            function performReset() {
                const form = document.getElementById('settingsForm');
                const logoInput = document.getElementById('site_logo');
                const faviconInput = document.getElementById('favicon');

                // Store file inputs
                const logoFile = logoInput ? logoInput.files[0] : null;
                const faviconFile = faviconInput ? faviconInput.files[0] : null;

                // Reset form
                form.reset();

                // Restore file inputs if they had files
                if (logoFile && logoInput) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(logoFile);
                    logoInput.files = dataTransfer.files;
                }

                if (faviconFile && faviconInput) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(faviconFile);
                    faviconInput.files = dataTransfer.files;
                }

                // Clear previews
                removeLogoPreview();
                removeFaviconPreview();
            }
            
            // Call confirmation first
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Are you sure you want to reset all form fields? This will clear any unsaved changes.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, reset',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performReset();
                    }
                });
            } else if (confirm('Are you sure you want to reset all form fields? This will clear any unsaved changes.')) {
                performReset();
            }
        }

        // Email Template Switching
        function showTemplate(templateName, buttonElement) {
            // Hide all templates
            document.querySelectorAll('.template-section').forEach(section => {
                section.style.display = 'none';
            });

            // Show selected template
            const selectedTemplate = document.getElementById(templateName + '-template');
            if (selectedTemplate) {
                selectedTemplate.style.display = 'block';
            }

            // Update button states
            document.querySelectorAll('.template-selector .template-btn').forEach(btn => {
                btn.classList.remove('active', 'btn-primary');
                btn.classList.add('btn-outline-primary');
            });

            // Activate clicked button
            if (buttonElement) {
                buttonElement.classList.add('active', 'btn-primary');
                buttonElement.classList.remove('btn-outline-primary');
            }
        }

        // Initialize template selector buttons
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners to template buttons
            document.querySelectorAll('.template-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const templateName = this.getAttribute('data-template');
                    if (templateName) {
                        showTemplate(templateName, this);
                    }
                });
            });

            // Show reset-password template by default
            showTemplate('reset-password', document.querySelector('.template-btn[data-template="reset-password"]'));
        });
    </script>
@endpush

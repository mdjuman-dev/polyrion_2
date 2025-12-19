@extends('backend.layouts.master')
@section('title', 'Google Login Settings')
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
                                <li class="breadcrumb-item active" aria-current="page">Google Login Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-body p-0">
                                <form method="POST" action="{{ route('admin.google-login.settings.update') }}"
                                    id="googleLoginSettingsForm">
                                    @csrf

                                    <div class="row g-0">
                                        <!-- Left Sidebar Navigation -->
                                        <div class="col-md-3 settings-sidebar">
                                            <div class="settings-nav">
                                                <ul class="nav nav-pills flex-column" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" data-bs-toggle="pill" href="#google-oauth"
                                                            role="tab">
                                                            <div class="nav-icon">
                                                                <i data-feather="log-in"></i>
                                                            </div>
                                                            <span>Google OAuth</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Right Content Area -->
                                        <div class="col-md-9 settings-content">
                                            <div class="tab-content p-4">

                                                <!-- Google OAuth Tab -->
                                                <div class="tab-pane fade show active" id="google-oauth" role="tabpanel">
                                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                                        <div>
                                                            <h5 class="mb-1">Google OAuth Login Settings</h5>
                                                            <p class="text-muted mb-0">Configure Google OAuth credentials for user login. These credentials will be stored in the database settings.</p>
                                                        </div>
                                                    </div>

                                                    <div class="alert alert-info mb-4">
                                                        <i class="fa fa-info-circle me-2"></i>
                                                        Get your OAuth credentials from <a href="https://console.cloud.google.com/apis/credentials"
                                                            target="_blank" class="alert-link">Google Cloud Console</a>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Google Client ID <span class="text-danger">*</span></label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fa fa-key"></i></span>
                                                                    <input type="text" name="google_client_id" id="google_client_id"
                                                                        class="form-control"
                                                                        value="{{ old('google_client_id', $googleSettings['google_client_id'] ?? '') }}"
                                                                        placeholder="Enter Google Client ID" required>
                                                                </div>
                                                                <small class="form-text text-muted mt-2">Your Google OAuth Client ID (e.g., 498087143603-xxxxx.apps.googleusercontent.com)</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Google Client Secret <span class="text-danger">*</span></label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                                                    <input type="password" name="google_client_secret" id="google_client_secret"
                                                                        class="form-control"
                                                                        value="{{ old('google_client_secret', $googleSettings['google_client_secret'] ?? '') }}"
                                                                        placeholder="Enter Google Client Secret" required>
                                                                    <button type="button" class="btn btn-outline-secondary"
                                                                        onclick="togglePassword('google_client_secret')">
                                                                        <i class="fa fa-eye" id="google_client_secret_icon"></i>
                                                                    </button>
                                                                </div>
                                                                <small class="form-text text-muted mt-2">Your Google OAuth Client Secret</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Google Redirect URL <span class="text-danger">*</span></label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fa fa-link"></i></span>
                                                                    <input type="url" name="google_redirect" id="google_redirect"
                                                                        class="form-control"
                                                                        value="{{ old('google_redirect', $googleSettings['google_redirect'] ?? env('APP_URL') . '/auth/google/callback') }}"
                                                                        placeholder="http://localhost:8000/auth/google/callback" required>
                                                                </div>
                                                                <small class="form-text text-muted mt-2">The callback URL where Google will redirect after authentication. Must match the URL configured in Google Cloud Console.</small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="alert alert-warning mt-4">
                                                        <strong><i class="fa fa-exclamation-triangle"></i> Important:</strong>
                                                        <ul class="mb-0 mt-2">
                                                            <li>Make sure the Redirect URL matches exactly with the one configured in Google Cloud Console</li>
                                                            <li>For local development, use: <code>http://localhost:8000/auth/google/callback</code></li>
                                                            <li>For production, use: <code>https://yourdomain.com/auth/google/callback</code></li>
                                                            <li>After updating these settings, clear the cache for changes to take effect</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Form Actions -->
                                            <div class="settings-footer p-4 border-top">
                                                <button type="submit" class="btn btn-primary">
                                                    <i data-feather="save"></i> Update Settings
                                                </button>
                                                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                                    <i data-feather="refresh-cw"></i> Reset
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
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
        .content-wrapper {
            background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 50%, #f0f2ff 100%);
            min-height: 100vh;
        }

        .settings-sidebar {
            background: #fff;
            border-right: 1px solid #e0e0e0;
        }

        .settings-nav .nav-link {
            padding: 15px 20px;
            color: #666;
            border-radius: 0;
            border-left: 3px solid transparent;
        }

        .settings-nav .nav-link:hover {
            background: #f8f9fa;
            color: #333;
        }

        .settings-nav .nav-link.active {
            background: #f8f9fa;
            color: #667eea;
            border-left-color: #667eea;
            font-weight: 600;
        }

        .settings-nav .nav-icon {
            display: inline-block;
            width: 20px;
            margin-right: 10px;
        }

        .settings-content {
            background: #fff;
        }

        .settings-footer {
            background: #f8f9fa;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Initialize Feather Icons
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });

        // Toggle Password Visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '_icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Reset Form
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
                        document.getElementById('googleLoginSettingsForm').reset();
                    }
                });
            } else if (confirm('Are you sure you want to reset all form fields?')) {
                document.getElementById('googleLoginSettingsForm').reset();
            }
        }

        // Handle form submission
        document.getElementById('googleLoginSettingsForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                const originalHtml = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i data-feather="loader"></i> Updating...';
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }

                // Re-enable button after 10 seconds as fallback
                setTimeout(function() {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                }, 10000);
            }
        });
    </script>
@endpush


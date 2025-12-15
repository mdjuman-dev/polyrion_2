@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Settings - {{ $appName }}</title>
    <meta name="description" content="Manage your account settings on {{ $appName }}.">
@endsection
@section('content')
    <div class="settings-container">

        <!-- Mobile Profile Dropdown -->
        <div class="mobile-profile-dropdown d-lg-none">
            <button type="button" class="mobile-profile-dropdown-btn" id="mobileProfileDropdownBtn">
                Profile <i class="fas fa-chevron-down"></i>
            </button>
            <div class="mobile-profile-dropdown-menu" id="mobileProfileDropdownMenu">
                <a href="#" class="mobile-profile-dropdown-item active" data-tab="profile">
                    <span class="profile-dropdown-dot"></span> Profile
                </a>
                <a href="#" class="mobile-profile-dropdown-item" data-tab="account">
                    <span class="profile-dropdown-dot"></span> Account</a>
                <a href="#" class="mobile-profile-dropdown-item" data-tab="trading">
                    <span class="profile-dropdown-dot"></span> Trading</a>
                <a href="#" class="mobile-profile-dropdown-item" data-tab="notifications">
                    <span class="profile-dropdown-dot"></span> Notifications</a>
                <a href="#" class="mobile-profile-dropdown-item" data-tab="builder">
                    <span class="profile-dropdown-dot"></span> Builder Codes</a>
                <a href="#" class="mobile-profile-dropdown-item" data-tab="export">
                    <span class="profile-dropdown-dot"></span>Export Private Key</a>
            </div>
        </div>
        <div class="settings-header">
            <div class="profile-header-dropdown ">

            </div>
        </div>

        <!-- Profile Tab -->
        <div class="tab-content active" id="profile-tab">
            <div class="settings-card">
                <h3 class="card-title">
                    Profile Settings
                </h3>

                <div class="profile-avatar-section mb-4">
                    <div class="avatar-upload">
                        <div class="profile-avatar-gradient">
                            <img src="{{ asset('storage/' . auth()->user()->name) }}" alt="{{ auth()->user()->name }}">
                        </div>
                    </div>
                    <button type="button" class="upload-btn">
                        <i class="fas fa-camera"></i> Upload
                    </button>
                </div>

                <div class="profile-form-fields">
                    <div class="form-field mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="Enter your email"
                            value="{{ auth()->user()->email }}">
                    </div>
                    <div class="form-field mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" placeholder="Enter your username"
                            value="{{ auth()->user()->username }}">
                    </div>
                    <div class="form-field mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control" id="bio" rows="3" placeholder="Bio">{{ auth()->user()->bio }}</textarea>
                    </div>
                </div>
            </div>
            <div class="profile-save-section mt-4">
                <button type="button" id="saveProfileBtn" class="btn-save-changes">Save changes</button>
            </div>
        </div>
    </div>
@endsection

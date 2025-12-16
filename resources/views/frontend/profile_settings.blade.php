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

                @php
                    $user = auth()->user();
                    $profileImage = $user->profile_image
                        ? asset('storage/' . $user->profile_image)
                        : asset('frontend/assets/images/default-avatar.png');
                @endphp

                <form id="profileSettingsForm" method="POST" action="{{ route('profile.update') }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="profile-avatar-section mb-4">
                        <div class="avatar-upload">
                            <div class="profile-avatar-gradient">
                                <img src="{{ $profileImage }}" alt="{{ $user->name }}" id="profileImagePreview"
                                    loading="lazy"
                                    onerror="this.src='{{ asset('frontend/assets/images/default-avatar.png') }}'">
                            </div>
                        </div>
                        <div>
                            <label for="profile_image" class="upload-btn" style="cursor: pointer;">
                                <i class="fas fa-camera"></i> Upload
                            </label>
                            <input type="file" id="profile_image" name="profile_image" accept="image/*"
                                style="display: none;" onchange="previewProfileImage(this)">
                            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.5rem;">PNG, JPG, GIF
                                up to 2MB</p>
                        </div>
                    </div>

                    <div class="profile-form-fields">
                        <div class="form-field mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Enter your full name" value="{{ $user->name }}" required>
                        </div>

                        <div class="form-field mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Enter your email" value="{{ $user->email }}" required>
                        </div>

                        <div class="form-field mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="Enter your username" value="{{ $user->username ?? '' }}">
                        </div>

                        <div class="form-field mb-3">
                            <label for="number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="number" name="number"
                                placeholder="Enter your phone number" value="{{ $user->number ?? '' }}">
                        </div>
                    </div>

                    <div class="profile-save-section mt-4">
                        <button type="submit" id="saveProfileBtn" class="btn-save-changes"
                            style="padding: 0.875rem 2rem; background: linear-gradient(135deg, #ffb11a 0%, #ff9500 100%); color: #000; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(255, 177, 26, 0.3);"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(255, 177, 26, 0.4)'; this.style.background='linear-gradient(135deg, #ff9500 0%, #ffb11a 100%)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(255, 177, 26, 0.3)'; this.style.background='linear-gradient(135deg, #ffb11a 0%, #ff9500 100%)'">
                            <i class="fas fa-spinner fa-spin d-none" id="saveSpinner"></i>
                            <span id="saveBtnText">Save changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            $(document).ready(function() {
                // Profile image preview
                window.previewProfileImage = function(input) {
                    if (input.files && input.files[0]) {
                        const file = input.files[0];

                        // Validate file size (2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            if (typeof showError !== 'undefined') {
                                showError('Image size must be less than 2MB', 'Error');
                            } else if (typeof toastr !== 'undefined') {
                                toastr.error('Image size must be less than 2MB', 'Error');
                            } else {
                                alert('Image size must be less than 2MB');
                            }
                            input.value = '';
                            return;
                        }

                        // Validate file type
                        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                        if (!validTypes.includes(file.type)) {
                            if (typeof showError !== 'undefined') {
                                showError('Please select a valid image file (JPG, PNG, GIF)', 'Error');
                            } else if (typeof toastr !== 'undefined') {
                                toastr.error('Please select a valid image file (JPG, PNG, GIF)', 'Error');
                            } else {
                                alert('Please select a valid image file (JPG, PNG, GIF)');
                            }
                            input.value = '';
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('profileImagePreview').src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                };

                // Profile settings form submission
                $('#profileSettingsForm').on('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const btn = $('#saveProfileBtn');
                    const spinner = $('#saveSpinner');
                    const btnText = $('#saveBtnText');
                    const originalText = btnText.text();

                    // Disable button and show loading
                    btn.prop('disabled', true);
                    spinner.removeClass('d-none');
                    btnText.text('Saving...');

                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            btn.prop('disabled', false);
                            spinner.addClass('d-none');
                            btnText.text(originalText);

                            if (typeof showSuccess !== 'undefined') {
                                showSuccess(response.message || 'Profile updated successfully!',
                                    'Success');
                            } else if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || 'Profile updated successfully!',
                                    'Success');
                            } else {
                                alert(response.message || 'Profile updated successfully!');
                            }

                            // Reload page after 1.5 seconds to show updated data
                            setTimeout(function() {
                                window.location.reload();
                            }, 1500);
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false);
                            spinner.addClass('d-none');
                            btnText.text(originalText);

                            let errorMsg = 'Failed to update profile. Please try again.';

                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                } else if (xhr.responseJSON.errors) {
                                    const errors = xhr.responseJSON.errors;
                                    const firstError = Object.values(errors)[0];
                                    errorMsg = Array.isArray(firstError) ? firstError[0] :
                                        firstError;
                                }
                            }

                            if (typeof showError !== 'undefined') {
                                showError(errorMsg, 'Error');
                            } else if (typeof toastr !== 'undefined') {
                                toastr.error(errorMsg, 'Error');
                            } else {
                                alert(errorMsg);
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection

@extends('backend.layouts.master')
@section('title', 'Referral Settings')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <!-- Breadcrumb -->
                <div class="row mb-3">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.backend.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.setting') }}">Settings</a></li>
                                <li class="breadcrumb-item active">Referral Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(40, 167, 69, 0.15);">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-check-circle fa-2x me-3"></i>
                            <div class="flex-grow-1">
                                <strong>Success!</strong> {{ session('success') }}
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(220, 53, 69, 0.15);">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-exclamation-circle fa-2x me-3"></i>
                            <div class="flex-grow-1">
                                <strong>Error!</strong> {{ session('error') }}
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(220, 53, 69, 0.15);">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-exclamation-triangle fa-2x me-3"></i>
                            <div class="flex-grow-1">
                                <strong>Validation Error!</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="box" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
                            <div class="box-header with-border primary-gradient" style="padding: 25px 30px; border: none;">
                                <h4 class="box-title mb-0" style="color: #fff; font-weight: 700; font-size: 24px;">
                                    <i class="fa fa-users me-2"></i> Referral Commission Settings
                                </h4>
                            </div>
                            <div class="box-body" style="padding: 30px;">
                                <form method="POST" action="{{ route('admin.referral-settings.update') }}" id="referralSettingsForm">
                                    @csrf

                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="alert alert-info" style="border-radius: 12px; border: none; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); box-shadow: 0 4px 12px rgba(33, 150, 243, 0.1);">
                                                <div class="d-flex align-items-start">
                                                    <i class="fa fa-info-circle fa-2x me-3 mt-1" style="color: #1976d2;"></i>
                                                    <div>
                                                        <strong style="color: #1565c0; font-size: 16px;">How it works:</strong>
                                                        <p class="mb-0 mt-2" style="color: #424242;">
                                                            <strong>Commissions are distributed on deposits only, not on trades.</strong> When a user makes a deposit, referral commissions are automatically distributed to their referral chain (up to 3 levels). 
                                                            Set the commission percentage for each level below.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Level 1 Settings -->
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="card premium-level-card" style="border: none; border-radius: 16px; padding: 25px; background: linear-gradient(135deg, #fff 0%, #f8f9ff 100%); box-shadow: 0 4px 20px rgba(102, 126, 234, 0.1); transition: all 0.3s ease;">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="level-icon-wrapper" style="width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; margin-right: 20px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                                                        <i class="fa fa-level-up-alt fa-2x text-white"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="mb-1" style="color: #1f2937; font-weight: 700; font-size: 20px;">
                                                            Level 1 (Direct Referrer)
                                                        </h5>
                                                        <p class="text-muted mb-0" style="font-size: 14px;">
                                                            The user who directly referred the depositor
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-3">
                                                            <label for="level_1_percent" class="form-label fw-bold" style="color: #374151; margin-bottom: 10px;">
                                                                Commission Percentage <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="input-group" style="box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-radius: 10px; overflow: hidden;">
                                                                <input type="number" 
                                                                    class="form-control" 
                                                                    id="level_1_percent" 
                                                                    name="level_1_percent" 
                                                                    value="{{ old('level_1_percent', $settings->where('level', 1)->first()->commission_percent ?? 10) }}" 
                                                                    min="0" 
                                                                    max="100" 
                                                                    step="0.01" 
                                                                    required
                                                                    style="border: 1px solid #e5e7eb; padding: 12px 15px; font-size: 15px; transition: all 0.3s ease;">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border: none; font-weight: 600; padding: 12px 20px;">%</span>
                                                                </div>
                                                            </div>
                                                            <small class="form-text text-muted mt-2 d-block">Enter percentage (0-100)</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-3">
                                                            <label class="form-label fw-bold d-block" style="color: #374151; margin-bottom: 15px;">Status</label>
                                                            <div class="form-check form-switch" style="padding-left: 3.5em;">
                                                                <input class="form-check-input" 
                                                                    type="checkbox" 
                                                                    id="level_1_active" 
                                                                    name="level_1_active" 
                                                                    value="1" 
                                                                    style="width: 3em; height: 1.5em; cursor: pointer;"
                                                                    {{ old('level_1_active', $settings->where('level', 1)->first()->is_active ?? true) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-semibold" for="level_1_active" style="color: #374151; cursor: pointer;">
                                                                    Active
                                                                </label>
                                                            </div>
                                                            <small class="form-text text-muted mt-2 d-block">Uncheck to disable Level 1 commissions</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Level 2 Settings -->
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="card premium-level-card" style="border: none; border-radius: 16px; padding: 25px; background: linear-gradient(135deg, #fff 0%, #f0fdf4 100%); box-shadow: 0 4px 20px rgba(34, 197, 94, 0.1); transition: all 0.3s ease;">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="level-icon-wrapper" style="width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); display: flex; align-items: center; justify-content: center; margin-right: 20px; box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);">
                                                        <i class="fa fa-level-up-alt fa-2x text-white"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="mb-1" style="color: #1f2937; font-weight: 700; font-size: 20px;">
                                                            Level 2 (Referrer's Referrer)
                                                        </h5>
                                                        <p class="text-muted mb-0" style="font-size: 14px;">
                                                            The user who referred the Level 1 referrer
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-3">
                                                            <label for="level_2_percent" class="form-label fw-bold" style="color: #374151; margin-bottom: 10px;">
                                                                Commission Percentage <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="input-group" style="box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-radius: 10px; overflow: hidden;">
                                                                <input type="number" 
                                                                    class="form-control" 
                                                                    id="level_2_percent" 
                                                                    name="level_2_percent" 
                                                                    value="{{ old('level_2_percent', $settings->where('level', 2)->first()->commission_percent ?? 5) }}" 
                                                                    min="0" 
                                                                    max="100" 
                                                                    step="0.01" 
                                                                    required
                                                                    style="border: 1px solid #e5e7eb; padding: 12px 15px; font-size: 15px; transition: all 0.3s ease;">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: #fff; border: none; font-weight: 600; padding: 12px 20px;">%</span>
                                                                </div>
                                                            </div>
                                                            <small class="form-text text-muted mt-2 d-block">Enter percentage (0-100)</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-3">
                                                            <label class="form-label fw-bold d-block" style="color: #374151; margin-bottom: 15px;">Status</label>
                                                            <div class="form-check form-switch" style="padding-left: 3.5em;">
                                                                <input class="form-check-input" 
                                                                    type="checkbox" 
                                                                    id="level_2_active" 
                                                                    name="level_2_active" 
                                                                    value="1" 
                                                                    style="width: 3em; height: 1.5em; cursor: pointer;"
                                                                    {{ old('level_2_active', $settings->where('level', 2)->first()->is_active ?? true) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-semibold" for="level_2_active" style="color: #374151; cursor: pointer;">
                                                                    Active
                                                                </label>
                                                            </div>
                                                            <small class="form-text text-muted mt-2 d-block">Uncheck to disable Level 2 commissions</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Level 3 Settings -->
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="card premium-level-card" style="border: none; border-radius: 16px; padding: 25px; background: linear-gradient(135deg, #fff 0%, #fffbeb 100%); box-shadow: 0 4px 20px rgba(251, 191, 36, 0.1); transition: all 0.3s ease;">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="level-icon-wrapper" style="width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); display: flex; align-items: center; justify-content: center; margin-right: 20px; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);">
                                                        <i class="fa fa-level-up-alt fa-2x text-white"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="mb-1" style="color: #1f2937; font-weight: 700; font-size: 20px;">
                                                            Level 3 (Referrer's Referrer's Referrer)
                                                        </h5>
                                                        <p class="text-muted mb-0" style="font-size: 14px;">
                                                            The user who referred the Level 2 referrer
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-3">
                                                            <label for="level_3_percent" class="form-label fw-bold" style="color: #374151; margin-bottom: 10px;">
                                                                Commission Percentage <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="input-group" style="box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-radius: 10px; overflow: hidden;">
                                                                <input type="number" 
                                                                    class="form-control" 
                                                                    id="level_3_percent" 
                                                                    name="level_3_percent" 
                                                                    value="{{ old('level_3_percent', $settings->where('level', 3)->first()->commission_percent ?? 2) }}" 
                                                                    min="0" 
                                                                    max="100" 
                                                                    step="0.01" 
                                                                    required
                                                                    style="border: 1px solid #e5e7eb; padding: 12px 15px; font-size: 15px; transition: all 0.3s ease;">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff; border: none; font-weight: 600; padding: 12px 20px;">%</span>
                                                                </div>
                                                            </div>
                                                            <small class="form-text text-muted mt-2 d-block">Enter percentage (0-100)</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-3">
                                                            <label class="form-label fw-bold d-block" style="color: #374151; margin-bottom: 15px;">Status</label>
                                                            <div class="form-check form-switch" style="padding-left: 3.5em;">
                                                                <input class="form-check-input" 
                                                                    type="checkbox" 
                                                                    id="level_3_active" 
                                                                    name="level_3_active" 
                                                                    value="1" 
                                                                    style="width: 3em; height: 1.5em; cursor: pointer;"
                                                                    {{ old('level_3_active', $settings->where('level', 3)->first()->is_active ?? true) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-semibold" for="level_3_active" style="color: #374151; cursor: pointer;">
                                                                    Active
                                                                </label>
                                                            </div>
                                                            <small class="form-text text-muted mt-2 d-block">Uncheck to disable Level 3 commissions</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Example Calculation -->
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="alert" style="border-radius: 16px; border: none; background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); box-shadow: 0 4px 20px rgba(251, 191, 36, 0.15); padding: 25px;">
                                                <div class="d-flex align-items-start mb-3">
                                                    <div style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); display: flex; align-items: center; justify-content: center; margin-right: 20px; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);">
                                                        <i class="fa fa-calculator fa-lg text-white"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="alert-heading mb-3" style="color: #92400e; font-weight: 700; font-size: 18px;">Example Calculation:</h6>
                                                        <p class="mb-3" style="color: #78350f; font-size: 15px;">
                                                            If a user deposits <strong style="color: #92400e; font-size: 18px;">$100</strong>:
                                                        </p>
                                                        <ul class="mb-0" style="list-style: none; padding: 0;">
                                                            <li class="mb-2" style="padding: 12px 15px; background: rgba(255,255,255,0.6); border-radius: 8px; border-left: 4px solid #667eea;">
                                                                <strong style="color: #667eea;">Level 1</strong> referrer gets: 
                                                                <strong style="color: #1f2937; font-size: 16px;">${{ number_format(100 * ($settings->where('level', 1)->first()->commission_percent ?? 10) / 100, 2) }}</strong> 
                                                                <span style="color: #6b7280;">({{ $settings->where('level', 1)->first()->commission_percent ?? 10 }}%)</span>
                                                            </li>
                                                            <li class="mb-2" style="padding: 12px 15px; background: rgba(255,255,255,0.6); border-radius: 8px; border-left: 4px solid #22c55e;">
                                                                <strong style="color: #22c55e;">Level 2</strong> referrer gets: 
                                                                <strong style="color: #1f2937; font-size: 16px;">${{ number_format(100 * ($settings->where('level', 2)->first()->commission_percent ?? 5) / 100, 2) }}</strong> 
                                                                <span style="color: #6b7280;">({{ $settings->where('level', 2)->first()->commission_percent ?? 5 }}%)</span>
                                                            </li>
                                                            <li class="mb-0" style="padding: 12px 15px; background: rgba(255,255,255,0.6); border-radius: 8px; border-left: 4px solid #f59e0b;">
                                                                <strong style="color: #f59e0b;">Level 3</strong> referrer gets: 
                                                                <strong style="color: #1f2937; font-size: 16px;">${{ number_format(100 * ($settings->where('level', 3)->first()->commission_percent ?? 2) / 100, 2) }}</strong> 
                                                                <span style="color: #6b7280;">({{ $settings->where('level', 3)->first()->commission_percent ?? 2 }}%)</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group d-flex gap-3">
                                                <button type="submit" class="btn btn-primary-gradient btn-lg" style="padding: 12px 30px; font-size: 16px; font-weight: 600; border-radius: 12px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); transition: all 0.3s ease;">
                                                    <i class="fa fa-save me-2"></i> Update Referral Settings
                                                </button>
                                                <a href="{{ route('admin.setting') }}" class="btn btn-secondary-theme btn-lg" style="padding: 12px 30px; font-size: 16px; font-weight: 600; border-radius: 12px; transition: all 0.3s ease;">
                                                    <i class="fa fa-arrow-left me-2"></i> Back to Settings
                                                </a>
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

@push('styles')
<style>
    /* Premium Level Card Hover Effect */
    .premium-level-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.12) !important;
    }
    
    /* Input Focus Effect */
    #level_1_percent:focus,
    #level_2_percent:focus,
    #level_3_percent:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
        outline: none;
    }
    
    /* Form Switch Custom Styling */
    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }
    
    .form-check-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }
    
    /* Button Hover Effects */
    .btn-primary-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4) !important;
    }
    
    .btn-secondary-theme:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Form validation
        $('#referralSettingsForm').on('submit', function(e) {
            var level1 = parseFloat($('#level_1_percent').val());
            var level2 = parseFloat($('#level_2_percent').val());
            var level3 = parseFloat($('#level_3_percent').val());

            if (isNaN(level1) || level1 < 0 || level1 > 100) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Level 1 percentage must be between 0 and 100',
                    confirmButtonColor: '#667eea'
                });
                e.preventDefault();
                return false;
            }

            if (isNaN(level2) || level2 < 0 || level2 > 100) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Level 2 percentage must be between 0 and 100',
                    confirmButtonColor: '#667eea'
                });
                e.preventDefault();
                return false;
            }

            if (isNaN(level3) || level3 < 0 || level3 > 100) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Level 3 percentage must be between 0 and 100',
                    confirmButtonColor: '#667eea'
                });
                e.preventDefault();
                return false;
            }

            // Show loading
            var submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Updating...');
        });
        
        // Real-time calculation update
        $('#level_1_percent, #level_2_percent, #level_3_percent').on('input', function() {
            updateExampleCalculation();
        });
        
        function updateExampleCalculation() {
            var level1 = parseFloat($('#level_1_percent').val()) || 0;
            var level2 = parseFloat($('#level_2_percent').val()) || 0;
            var level3 = parseFloat($('#level_3_percent').val()) || 0;
            var deposit = 100;
            
            // Update example calculation (if needed, can be enhanced with AJAX)
            // For now, the server-side calculation will handle it
        }
    });
</script>
@endpush
@endsection


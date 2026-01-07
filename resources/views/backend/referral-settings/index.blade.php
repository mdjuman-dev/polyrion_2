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
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Validation Error!</strong>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    <i class="fa fa-users"></i> Referral Commission Settings
                                </h4>
                            </div>
                            <div class="box-body">
                                <form method="POST" action="{{ route('admin.referral-settings.update') }}" id="referralSettingsForm">
                                    @csrf

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> 
                                                <strong>How it works:</strong> When a user makes a deposit, commissions are distributed to their referral chain (up to 3 levels). 
                                                Set the commission percentage for each level below.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Level 1 Settings -->
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="card" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 1.5rem;">
                                                <h5 class="mb-3" style="color: #333; font-weight: 600;">
                                                    <i class="fa fa-level-up-alt text-primary"></i> Level 1 (Direct Referrer)
                                                </h5>
                                                <p class="text-muted mb-3" style="font-size: 0.875rem;">
                                                    The user who directly referred the depositor
                                                </p>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="level_1_percent" class="form-label">
                                                                Commission Percentage <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" 
                                                                    class="form-control" 
                                                                    id="level_1_percent" 
                                                                    name="level_1_percent" 
                                                                    value="{{ old('level_1_percent', $settings->where('level', 1)->first()->commission_percent ?? 10) }}" 
                                                                    min="0" 
                                                                    max="100" 
                                                                    step="0.01" 
                                                                    required>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                            </div>
                                                            <small class="form-text text-muted">Enter percentage (0-100)</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Status</label>
                                                            <div class="checkbox">
                                                                <input type="checkbox" 
                                                                    id="level_1_active" 
                                                                    name="level_1_active" 
                                                                    value="1" 
                                                                    {{ old('level_1_active', $settings->where('level', 1)->first()->is_active ?? true) ? 'checked' : '' }}>
                                                                <label for="level_1_active">Active</label>
                                                            </div>
                                                            <small class="form-text text-muted">Uncheck to disable Level 1 commissions</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Level 2 Settings -->
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="card" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 1.5rem;">
                                                <h5 class="mb-3" style="color: #333; font-weight: 600;">
                                                    <i class="fa fa-level-up-alt text-success"></i> Level 2 (Referrer's Referrer)
                                                </h5>
                                                <p class="text-muted mb-3" style="font-size: 0.875rem;">
                                                    The user who referred the Level 1 referrer
                                                </p>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="level_2_percent" class="form-label">
                                                                Commission Percentage <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" 
                                                                    class="form-control" 
                                                                    id="level_2_percent" 
                                                                    name="level_2_percent" 
                                                                    value="{{ old('level_2_percent', $settings->where('level', 2)->first()->commission_percent ?? 5) }}" 
                                                                    min="0" 
                                                                    max="100" 
                                                                    step="0.01" 
                                                                    required>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                            </div>
                                                            <small class="form-text text-muted">Enter percentage (0-100)</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Status</label>
                                                            <div class="checkbox">
                                                                <input type="checkbox" 
                                                                    id="level_2_active" 
                                                                    name="level_2_active" 
                                                                    value="1" 
                                                                    {{ old('level_2_active', $settings->where('level', 2)->first()->is_active ?? true) ? 'checked' : '' }}>
                                                                <label for="level_2_active">Active</label>
                                                            </div>
                                                            <small class="form-text text-muted">Uncheck to disable Level 2 commissions</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Level 3 Settings -->
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="card" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 1.5rem;">
                                                <h5 class="mb-3" style="color: #333; font-weight: 600;">
                                                    <i class="fa fa-level-up-alt text-warning"></i> Level 3 (Referrer's Referrer's Referrer)
                                                </h5>
                                                <p class="text-muted mb-3" style="font-size: 0.875rem;">
                                                    The user who referred the Level 2 referrer
                                                </p>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="level_3_percent" class="form-label">
                                                                Commission Percentage <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" 
                                                                    class="form-control" 
                                                                    id="level_3_percent" 
                                                                    name="level_3_percent" 
                                                                    value="{{ old('level_3_percent', $settings->where('level', 3)->first()->commission_percent ?? 2) }}" 
                                                                    min="0" 
                                                                    max="100" 
                                                                    step="0.01" 
                                                                    required>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                            </div>
                                                            <small class="form-text text-muted">Enter percentage (0-100)</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Status</label>
                                                            <div class="checkbox">
                                                                <input type="checkbox" 
                                                                    id="level_3_active" 
                                                                    name="level_3_active" 
                                                                    value="1" 
                                                                    {{ old('level_3_active', $settings->where('level', 3)->first()->is_active ?? true) ? 'checked' : '' }}>
                                                                <label for="level_3_active">Active</label>
                                                            </div>
                                                            <small class="form-text text-muted">Uncheck to disable Level 3 commissions</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Example Calculation -->
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="alert alert-warning">
                                                <h6 class="alert-heading"><i class="fa fa-calculator"></i> Example Calculation:</h6>
                                                <p class="mb-0">
                                                    If a user deposits <strong>$100</strong>:
                                                    <ul class="mb-0">
                                                        <li>Level 1 referrer gets: <strong>${{ number_format(100 * ($settings->where('level', 1)->first()->commission_percent ?? 10) / 100, 2) }}</strong> ({{ $settings->where('level', 1)->first()->commission_percent ?? 10 }}%)</li>
                                                        <li>Level 2 referrer gets: <strong>${{ number_format(100 * ($settings->where('level', 2)->first()->commission_percent ?? 5) / 100, 2) }}</strong> ({{ $settings->where('level', 2)->first()->commission_percent ?? 5 }}%)</li>
                                                        <li>Level 3 referrer gets: <strong>${{ number_format(100 * ($settings->where('level', 3)->first()->commission_percent ?? 2) / 100, 2) }}</strong> ({{ $settings->where('level', 3)->first()->commission_percent ?? 2 }}%)</li>
                                                    </ul>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    <i class="fa fa-save"></i> Update Referral Settings
                                                </button>
                                                <a href="{{ route('admin.setting') }}" class="btn btn-secondary btn-lg">
                                                    <i class="fa fa-arrow-left"></i> Back to Settings
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
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Form validation
        $('#referralSettingsForm').on('submit', function(e) {
            var level1 = parseFloat($('#level_1_percent').val());
            var level2 = parseFloat($('#level_2_percent').val());
            var level3 = parseFloat($('#level_3_percent').val());

            if (level1 < 0 || level1 > 100) {
                alert('Level 1 percentage must be between 0 and 100');
                e.preventDefault();
                return false;
            }

            if (level2 < 0 || level2 > 100) {
                alert('Level 2 percentage must be between 0 and 100');
                e.preventDefault();
                return false;
            }

            if (level3 < 0 || level3 > 100) {
                alert('Level 3 percentage must be between 0 and 100');
                e.preventDefault();
                return false;
            }

            // Show loading
            var submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
        });
    });
</script>
@endsection


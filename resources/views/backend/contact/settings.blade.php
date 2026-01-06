@extends('backend.layouts.master')
@section('title', 'Support Settings')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row mb-3">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.backend.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.contact.index') }}">Contact Messages</a></li>
                                <li class="breadcrumb-item active">Support Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    <i class="fa fa-cog"></i> Support Information Settings
                                </h4>
                            </div>
                            <div class="box-body">
                                <form action="{{ route('admin.contact.settings.update') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">Support Email</label>
                                        <input type="email" name="support_email" class="form-control @error('support_email') is-invalid @enderror" 
                                            value="{{ old('support_email', $supportEmail) }}" placeholder="support@example.com">
                                        @error('support_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Support Phone</label>
                                        <input type="text" name="support_phone" class="form-control @error('support_phone') is-invalid @enderror" 
                                            value="{{ old('support_phone', $supportPhone) }}" placeholder="+1 (555) 123-4567">
                                        @error('support_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Support Description</label>
                                        <textarea name="support_description" class="form-control textarea @error('support_description') is-invalid @enderror" 
                                            rows="6" placeholder="Enter support information or help text...">{{ old('support_description', $supportDescription) }}</textarea>
                                        @error('support_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Save Settings
                                        </button>
                                        <a href="{{ route('admin.contact.index') }}" class="btn btn-secondary">
                                            <i class="fa fa-times"></i> Cancel
                                        </a>
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
<link rel="stylesheet" href="{{ asset('backend/assets/vendor_plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.css') }}">
@endpush

@push('scripts')
<script>
    $(function() {
        $('.textarea').wysihtml5({
            toolbar: {
                "font-styles": true,
                "emphasis": true,
                "lists": true,
                "html": false,
                "link": true,
                "image": false,
                "color": false,
                "size": "sm"
            }
        });
    });
</script>
@endpush


@extends('backend.layouts.master')
@section('title', 'Edit ' . ($page->page_key === 'privacy_policy' ? 'Privacy Policy' : 'Terms of Use'))
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row mb-3">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.backend.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.setting') }}">Settings</a></li>
                                <li class="breadcrumb-item active">{{ $page->page_key === 'privacy_policy' ? 'Privacy Policy' : 'Terms of Use' }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    <i class="fa fa-edit"></i> Edit {{ $page->page_key === 'privacy_policy' ? 'Privacy Policy' : 'Terms of Use' }}
                                </h4>
                            </div>
                            <div class="box-body">
                                <form id="pageEditForm" method="POST" action="{{ route('admin.pages.' . ($page->page_key === 'privacy_policy' ? 'privacy-policy' : 'terms-of-use') . '.update') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label required">Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $page->title) }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Content</label>
                                        <textarea name="content" id="pageContent" class="form-control textarea @error('content') is-invalid @enderror" rows="15" placeholder="Enter page content...">{{ old('content', $page->content) }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div id="pageMessage" style="display: none; padding: 0.75rem; margin-bottom: 1rem; border-radius: 6px;"></div>

                                    <div class="form-group">
                                        <button type="submit" id="savePageBtn" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Save Changes
                                        </button>
                                        <a href="{{ route('admin.setting') }}" class="btn btn-secondary">
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

        $('#pageEditForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
            const btn = $('#savePageBtn');
            const originalText = btn.html();
            const messageDiv = $('#pageMessage');

            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            messageDiv.hide().removeClass('alert-success alert-danger');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                success: function(response) {
                    btn.prop('disabled', false).html(originalText);
                    if (response.success) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'Page updated successfully!', 'Success');
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message || 'Page updated successfully!',
                                confirmButtonColor: '#ffb11a',
                                timer: 3000,
                                timerProgressBar: true
                            });
                        }
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    const response = xhr.responseJSON;
                    let errorMsg = 'Failed to update page. Please try again.';
                    
                    if (response && response.errors) {
                        const errors = [];
                        $.each(response.errors, function(key, value) {
                            errors.push(value[0]);
                        });
                        errorMsg = errors.join('<br>');
                    } else if (response && response.message) {
                        errorMsg = response.message;
                    }

                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMsg, 'Error');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg.replace(/<br>/g, ' '),
                            confirmButtonColor: '#ef4444'
                        });
                    } else {
                        messageDiv.removeClass('alert-success').addClass('alert-danger')
                            .css({
                                'background': '#f8d7da',
                                'color': '#721c24',
                                'border': '1px solid #f5c6cb'
                            })
                            .html(errorMsg)
                            .show();
                    }
                }
            });
        });
    });
</script>
@endpush


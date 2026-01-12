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
                        <div class="box" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
                            <div class="box-header with-border primary-gradient" style="padding: 25px 30px; border: none;">
                                <div class="d-flex align-items-center">
                                    <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; margin-right: 20px;">
                                        <i class="fa {{ $page->page_key === 'privacy_policy' ? 'fa-shield' : 'fa-file-text' }} fa-lg text-white"></i>
                                    </div>
                                    <div>
                                        <h4 class="box-title mb-0" style="color: #fff; font-weight: 700; font-size: 24px;">
                                            Edit {{ $page->page_key === 'privacy_policy' ? 'Privacy Policy' : 'Terms of Use' }}
                                        </h4>
                                        <p class="mb-0 mt-1" style="color: rgba(255,255,255,0.9); font-size: 14px;">
                                            Update the content and title for this page
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="box-body" style="padding: 30px;">
                                <form id="pageEditForm" method="POST" action="{{ route('admin.pages.' . ($page->page_key === 'privacy_policy' ? 'privacy-policy' : 'terms-of-use') . '.update') }}">
                                    @csrf
                                    
                                    <!-- Title Field -->
                                    <div class="form-group mb-4">
                                        <label class="form-label fw-bold mb-2" style="color: #374151; font-size: 15px;">
                                            Title <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                            name="title" 
                                            class="form-control @error('title') is-invalid @enderror" 
                                            value="{{ old('title', $page->title) }}" 
                                            required
                                            style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 15px; font-size: 15px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                        @error('title')
                                            <div class="invalid-feedback d-block mt-2" style="color: #ef4444; font-size: 14px;">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted mt-2 d-block">Enter the page title that will be displayed to users</small>
                                    </div>

                                    <!-- Content Field -->
                                    <div class="form-group mb-4">
                                        <label class="form-label fw-bold mb-2" style="color: #374151; font-size: 15px;">
                                            Content
                                        </label>
                                        <div style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                            <textarea name="content" 
                                                id="pageContent" 
                                                class="form-control textarea @error('content') is-invalid @enderror" 
                                                rows="20" 
                                                placeholder="Enter page content here... You can use the toolbar above to format your text."
                                                style="border: 1px solid #e5e7eb; border-radius: 12px; padding: 15px; font-size: 14px; line-height: 1.6; min-height: 400px; resize: vertical;">{{ old('content', $page->content) }}</textarea>
                                        </div>
                                        @error('content')
                                            <div class="invalid-feedback d-block mt-2" style="color: #ef4444; font-size: 14px;">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted mt-2 d-block">
                                            <i class="fa fa-info-circle me-1"></i> Use the formatting toolbar to style your content. HTML is supported.
                                        </small>
                                    </div>

                                    <!-- Message Display -->
                                    <div id="pageMessage" style="display: none; padding: 15px; margin-bottom: 20px; border-radius: 12px; font-size: 14px;"></div>

                                    <!-- Action Buttons -->
                                    <div class="form-group d-flex gap-3 pt-3" style="border-top: 1px solid #e5e7eb;">
                                        <button type="submit" id="savePageBtn" class="btn btn-primary-gradient" style="padding: 12px 30px; font-size: 16px; font-weight: 600; border-radius: 12px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); transition: all 0.3s ease;">
                                            <i class="fa fa-save me-2"></i> Save Changes
                                        </button>
                                        <a href="{{ route('admin.setting') }}" class="btn btn-secondary-theme" style="padding: 12px 30px; font-size: 16px; font-weight: 600; border-radius: 12px; transition: all 0.3s ease;">
                                            <i class="fa fa-times me-2"></i> Cancel
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
<style>
    /* Form Input Focus Effects */
    #pageEditForm input[type="text"]:focus,
    #pageEditForm textarea:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
        outline: none;
    }
    
    /* WYSIHTML5 Editor Styling */
    .wysihtml5-toolbar {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 10px 10px 0 0 !important;
        padding: 10px 15px !important;
        margin-bottom: 0 !important;
    }
    
    .wysihtml5-toolbar .btn {
        border-radius: 6px !important;
        margin: 2px !important;
        padding: 6px 12px !important;
        transition: all 0.3s ease !important;
    }
    
    .wysihtml5-toolbar .btn:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: #fff !important;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
    }
    
    /* Textarea Editor Area */
    .wysihtml5-sandbox {
        border: 1px solid #e5e7eb !important;
        border-top: none !important;
        border-radius: 0 0 12px 12px !important;
        padding: 15px !important;
        min-height: 400px !important;
        font-size: 14px !important;
        line-height: 1.6 !important;
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
    
    /* Form Validation Styles */
    .is-invalid {
        border-color: #ef4444 !important;
    }
    
    .is-invalid:focus {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
    }
</style>
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


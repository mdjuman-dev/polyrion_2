@extends('backend.layouts.master')
@section('title', 'Social Media Settings')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row mb-3">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.backend.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Social Media Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    <i class="fa fa-share-alt"></i> Social Media Links
                                </h4>
                            </div>
                            <div class="box-body">
                                <form id="socialMediaForm" method="POST" action="{{ route('admin.social-media.update') }}">
                                    @csrf
                                    <div class="row">
                                        @foreach($links as $link)
                                            <div class="col-md-6 mb-4">
                                                <div class="social-platform-card" style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 1.5rem;">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 style="margin: 0; font-weight: 600; text-transform: capitalize;">
                                                            <i class="fab fa-{{ $link->platform === 'twitter' ? 'x' : $link->platform }} me-2"></i>
                                                            {{ ucfirst($link->platform === 'twitter' ? 'X (Twitter)' : $link->platform) }}
                                                        </h5>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input status-toggle" type="checkbox" 
                                                                id="status_{{ $link->platform }}" 
                                                                name="links[{{ $link->platform }}][status]" 
                                                                value="active" 
                                                                {{ $link->status === 'active' ? 'checked' : '' }}
                                                                style="cursor: pointer;">
                                                            <label class="form-check-label" for="status_{{ $link->platform }}" style="cursor: pointer;">
                                                                <input type="hidden" name="links[{{ $link->platform }}][status]" value="inactive">
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-label">URL</label>
                                                        <input type="url" 
                                                            name="links[{{ $link->platform }}][url]" 
                                                            class="form-control" 
                                                            value="{{ old('links.' . $link->platform . '.url', $link->url) }}" 
                                                            placeholder="https://{{ $link->platform }}.com/yourpage">
                                                        <small class="text-muted">Enter the full URL including https://</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div id="socialMediaMessage" style="display: none; padding: 0.75rem; margin-bottom: 1rem; border-radius: 6px;"></div>

                                    <div class="form-group mt-4">
                                        <button type="submit" id="saveSocialBtn" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Save Changes
                                        </button>
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

@push('scripts')
<script>
    $(function() {
        $('.status-toggle').on('change', function() {
            const hiddenInput = $(this).siblings('label').find('input[type="hidden"]');
            if ($(this).is(':checked')) {
                hiddenInput.val('active');
            } else {
                hiddenInput.val('inactive');
            }
        });

        $('#socialMediaForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
            const btn = $('#saveSocialBtn');
            const originalText = btn.html();
            const messageDiv = $('#socialMediaMessage');

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
                            toastr.success(response.message || 'Social media settings updated successfully!', 'Success');
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message || 'Social media settings updated successfully!',
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
                    let errorMsg = 'Failed to update settings. Please try again.';
                    
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


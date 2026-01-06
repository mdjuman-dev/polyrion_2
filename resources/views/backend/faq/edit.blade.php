@extends('backend.layouts.master')
@section('title', 'Edit FAQ')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row mb-3">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.backend.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.faqs.index') }}">FAQs</a></li>
                                <li class="breadcrumb-item active">Edit</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    <i class="fa fa-edit"></i> Edit FAQ
                                </h4>
                            </div>
                            <div class="box-body">
                                <form action="{{ route('admin.faqs.update', $faq->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label class="form-label required">Question <span class="text-danger">*</span></label>
                                        <input type="text" name="question" class="form-control @error('question') is-invalid @enderror" 
                                            value="{{ old('question', $faq->question) }}" placeholder="Enter question" required maxlength="500">
                                        @error('question')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label required">Answer <span class="text-danger">*</span></label>
                                        <textarea name="answer" class="form-control textarea @error('answer') is-invalid @enderror" 
                                            rows="10" placeholder="Enter answer" required>{{ old('answer', $faq->answer) }}</textarea>
                                        @error('answer')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label required">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                            <option value="active" {{ old('status', $faq->status) === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $faq->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Update FAQ
                                        </button>
                                        <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">
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


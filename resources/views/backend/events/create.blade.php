@extends('backend.layouts.master')
@section('title', 'Create New Event')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    <i class="fa fa-plus-circle"></i> Create New Event
                                </h4>
                            </div>
                            <div class="box-body">
                                <form action="{{ route('admin.events.store') }}" method="POST"
                                    enctype="multipart/form-data" id="eventForm">
                                    @csrf

                                    <div class="row">
                                        <!-- Left Column -->
                                        <div class="col-md-8">
                                            <!-- Event Title -->
                                            <div class="form-group">
                                                <label class="form-label required">Event Title <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="title"
                                                    class="form-control @error('title') is-invalid @enderror"
                                                    value="{{ old('title') }}"
                                                    placeholder="e.g., Which CEOs will be gone in 2025?" required>
                                                @error('title')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Enter a clear, descriptive title for the
                                                    event</small>
                                            </div>

                                            <!-- Slug -->
                                            <div class="form-group">
                                                <label class="form-label">Slug</label>
                                                <input type="text" name="slug"
                                                    class="form-control @error('slug') is-invalid @enderror"
                                                    value="{{ old('slug') }}" placeholder="Auto-generated from title"
                                                    id="slugInput">
                                                @error('slug')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">URL-friendly identifier (auto-generated
                                                    if left empty)</small>
                                            </div>

                                            <!-- Description -->
                                            <div class="form-group">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5"
                                                    placeholder="Describe the event...">{{ old('description') }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Category -->
                                            <div class="form-group">
                                                <label class="form-label">Category</label>
                                                <select name="category"
                                                    class="form-control @error('category') is-invalid @enderror">
                                                    <option value="">Auto-detect from title</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category }}"
                                                            {{ old('category') == $category ? 'selected' : '' }}>
                                                            {{ ucfirst($category) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('category')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Leave empty to auto-detect from
                                                    title</small>
                                            </div>
                                        </div>

                                        <!-- Right Column -->
                                        <div class="col-md-4">
                                            <!-- Image URL -->
                                            <div class="form-group">
                                                <label class="form-label">Event Image URL</label>
                                                <input type="url" name="image"
                                                    class="form-control @error('image') is-invalid @enderror"
                                                    value="{{ old('image') }}"
                                                    placeholder="https://example.com/image.jpg">
                                                @error('image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="mt-2" id="imagePreview" style="display: none;">
                                                    <img src="" alt="Preview" class="img-thumbnail"
                                                        style="max-width: 100%; max-height: 200px;">
                                                </div>
                                            </div>

                                            <!-- Icon URL -->
                                            <div class="form-group">
                                                <label class="form-label">Event Icon URL</label>
                                                <input type="url" name="icon"
                                                    class="form-control @error('icon') is-invalid @enderror"
                                                    value="{{ old('icon') }}" placeholder="https://example.com/icon.jpg">
                                                @error('icon')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Dates -->
                                            <div class="form-group">
                                                <label class="form-label">Start Date</label>
                                                <input type="datetime-local" name="start_date"
                                                    class="form-control @error('start_date') is-invalid @enderror"
                                                    value="{{ old('start_date') }}">
                                                @error('start_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label">End Date</label>
                                                <input type="datetime-local" name="end_date"
                                                    class="form-control @error('end_date') is-invalid @enderror"
                                                    value="{{ old('end_date') }}">
                                                @error('end_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Status Toggles -->
                                            <div class="form-group">
                                                <div class="checkbox">
                                                    <input type="checkbox" name="active" id="active" value="1"
                                                        {{ old('active', true) ? 'checked' : '' }}>
                                                    <label for="active">Active</label>
                                                </div>
                                                <div class="checkbox">
                                                    <input type="checkbox" name="featured" id="featured" value="1"
                                                        {{ old('featured') ? 'checked' : '' }}>
                                                    <label for="featured">Featured</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="form-group mt-4">
                                        <button type="submit" name="add_markets" value="0"
                                            class="btn btn-primary">
                                            <i class="fa fa-save"></i> Create Event
                                        </button>
                                        <button type="submit" name="add_markets" value="1"
                                            class="btn btn-success">
                                            <i class="fa fa-save"></i> Create Event & Add Markets
                                        </button>
                                        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
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

    <script>
        // Auto-generate slug from title
        document.querySelector('input[name="title"]').addEventListener('input', function(e) {
            const slugInput = document.getElementById('slugInput');
            if (!slugInput.value || slugInput.dataset.autoGenerated === 'true') {
                const slug = e.target.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.value = slug;
                slugInput.dataset.autoGenerated = 'true';
            }
        });

        // Manual slug edit
        document.getElementById('slugInput').addEventListener('input', function() {
            this.dataset.autoGenerated = 'false';
        });

        // Image preview
        document.querySelector('input[name="image"]').addEventListener('input', function(e) {
            const preview = document.getElementById('imagePreview');
            if (e.target.value) {
                preview.style.display = 'block';
                preview.querySelector('img').src = e.target.value;
            } else {
                preview.style.display = 'none';
            }
        });
    </script>
@endsection


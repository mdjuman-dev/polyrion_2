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

                                            <!-- Slug -->
                                            <div class="form-group">
                                                <label class="form-label">Slug</label>
                                                <input type="text" name="slug"
                                                    class="form-control @error('slug') is-invalid @enderror"
                                                    value="{{ old('slug') }}"
                                                    placeholder="auto-generated-from-title">
                                                @error('slug')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Leave empty to auto-generate from title</small>
                                            </div>

                                            <!-- Image URL -->
                                            <div class="form-group">
                                                <label class="form-label">Image URL</label>
                                                <input type="url" name="image"
                                                    class="form-control @error('image') is-invalid @enderror"
                                                    value="{{ old('image') }}"
                                                    placeholder="https://example.com/image.jpg">
                                                @error('image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Icon URL -->
                                            <div class="form-group">
                                                <label class="form-label">Icon URL</label>
                                                <input type="url" name="icon"
                                                    class="form-control @error('icon') is-invalid @enderror"
                                                    value="{{ old('icon') }}"
                                                    placeholder="https://example.com/icon.jpg">
                                                @error('icon')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Start Date -->
                                            <div class="form-group">
                                                <label class="form-label">Start Date</label>
                                                <input type="datetime-local" name="start_date"
                                                    class="form-control @error('start_date') is-invalid @enderror"
                                                    value="{{ old('start_date') }}">
                                                @error('start_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- End Date -->
                                            <div class="form-group">
                                                <label class="form-label">End Date</label>
                                                <input type="datetime-local" name="end_date"
                                                    class="form-control @error('end_date') is-invalid @enderror"
                                                    value="{{ old('end_date') }}">
                                                @error('end_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Must be after start date</small>
                                            </div>
                                        </div>

                                        <!-- Right Column -->
                                        <div class="col-md-4">
                                            <!-- Options Card -->
                                            <div class="box" style="background: #f8f9fa; border: 1px solid #e9ecef;">
                                                <div class="box-body">
                                                    <h5 class="mb-3">
                                                        <i class="fa fa-cog"></i> Options
                                                    </h5>

                                                    <!-- Active Status -->
                                                    <div class="form-group">
                                                        <div class="checkbox">
                                                            <input type="checkbox" name="active" value="1" id="active"
                                                                {{ old('active', true) ? 'checked' : '' }}>
                                                            <label for="active">Active</label>
                                                        </div>
                                                        <small class="form-text text-muted">Event will be visible to users</small>
                                                    </div>

                                                    <!-- Featured Status -->
                                                    <div class="form-group">
                                                        <div class="checkbox">
                                                            <input type="checkbox" name="featured" value="1" id="featured"
                                                                {{ old('featured') ? 'checked' : '' }}>
                                                            <label for="featured">Featured</label>
                                                        </div>
                                                        <small class="form-text text-muted">Show as featured event</small>
                                                    </div>

                                                    <!-- Add Markets Option -->
                                                    <div class="form-group">
                                                        <div class="checkbox">
                                                            <input type="checkbox" name="add_markets" value="1" id="add_markets">
                                                            <label for="add_markets">Add Markets After Creation</label>
                                                        </div>
                                                        <small class="form-text text-muted">Redirect to add markets page after creating</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Help Card -->
                                            <div class="box" style="background: #e3f2fd; border: 1px solid #90caf9; margin-top: 20px;">
                                                <div class="box-body">
                                                    <h5 class="mb-3">
                                                        <i class="fa fa-info-circle"></i> Quick Tips
                                                    </h5>
                                                    <ul class="list-unstyled mb-0">
                                                        <li class="mb-2">
                                                            <i class="fa fa-check text-primary"></i>
                                                            Category will be auto-detected if left empty
                                                        </li>
                                                        <li class="mb-2">
                                                            <i class="fa fa-check text-primary"></i>
                                                            Slug will be auto-generated from title
                                                        </li>
                                                        <li class="mb-2">
                                                            <i class="fa fa-check text-primary"></i>
                                                            You can add markets after creating the event
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Create Event
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
@endsection


@extends('backend.layouts.master')
@section('title', 'Edit Event')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    <i class="fa fa-edit"></i> Edit Event
                                </h4>
                            </div>
                            <div class="box-body">
                                <form action="{{ route('admin.events.update', $event) }}" method="POST"
                                    enctype="multipart/form-data" id="eventForm">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <!-- Left Column -->
                                        <div class="col-md-8">
                                            <!-- Event Title -->
                                            <div class="form-group">
                                                <label class="form-label required">Event Title <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="title"
                                                    class="form-control @error('title') is-invalid @enderror"
                                                    value="{{ old('title', $event->title) }}"
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
                                                    placeholder="Describe the event...">{{ old('description', $event->description) }}</textarea>
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
                                                            {{ old('category', $event->category) == $category ? 'selected' : '' }}>
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
                                            <!-- Event Info Card -->
                                            <div class="box" style="background: #f8f9fa; border: 1px solid #e9ecef;">
                                                <div class="box-body">
                                                    <h5 class="mb-3">
                                                        <i class="fa fa-info-circle"></i> Event Information
                                                    </h5>
                                                    <div class="info-item mb-2">
                                                        <strong>Slug:</strong>
                                                        <code>{{ $event->slug }}</code>
                                                    </div>
                                                    @if ($event->image)
                                                        <div class="info-item mb-2">
                                                            <strong>Image:</strong>
                                                            <a href="{{ $event->image }}" target="_blank"
                                                                class="text-primary">
                                                                <i class="fa fa-external-link"></i> View
                                                            </a>
                                                        </div>
                                                    @endif
                                                    @if ($event->icon)
                                                        <div class="info-item mb-2">
                                                            <strong>Icon:</strong>
                                                            <a href="{{ $event->icon }}" target="_blank"
                                                                class="text-primary">
                                                                <i class="fa fa-external-link"></i> View
                                                            </a>
                                                        </div>
                                                    @endif
                                                    <div class="info-item mb-2">
                                                        <strong>Status:</strong>
                                                        @if ($event->active)
                                                            <span class="badge badge-success">Active</span>
                                                        @else
                                                            <span class="badge badge-secondary">Inactive</span>
                                                        @endif
                                                        @if ($event->featured)
                                                            <span class="badge badge-warning ml-1">Featured</span>
                                                        @endif
                                                    </div>
                                                    <div class="info-item mb-2">
                                                        <strong>Markets:</strong>
                                                        <span
                                                            class="badge badge-info">{{ $event->markets->count() }}</span>
                                                    </div>
                                                    @if ($event->start_date)
                                                        <div class="info-item mb-2">
                                                            <strong>Start Date:</strong>
                                                            <br>{{ format_date($event->start_date) }}
                                                        </div>
                                                    @endif
                                                    @if ($event->end_date)
                                                        <div class="info-item mb-2">
                                                            <strong>End Date:</strong>
                                                            <br>{{ format_date($event->end_date) }}
                                                        </div>
                                                    @endif
                                                    <div class="info-item">
                                                        <strong>Created:</strong>
                                                        <br>{{ format_date($event->created_at) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Update Event
                                        </button>
                                        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                                            <i class="fa fa-times"></i> Cancel
                                        </a>
                                        <a href="{{ route('admin.events.show', $event) }}" class="btn btn-info">
                                            <i class="fa fa-eye"></i> View Event
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
        // Auto-detect category on title change
        document.querySelector('input[name="title"]').addEventListener('input', function(e) {
            const categorySelect = document.querySelector('select[name="category"]');
            if (categorySelect.value === '') {
                // Category will be auto-detected on server side
            }
        });
    </script>

    <style>
        .info-item {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item code {
            background: #e9ecef;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
    </style>
@endsection


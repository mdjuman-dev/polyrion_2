@extends('backend.layouts.master')
@section('title', 'Edit Secondary Category')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <!-- Back Button -->
                        <div class="mb-3">
                            <a href="{{ route('admin.secondary-categories.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back to Categories
                            </a>
                        </div>

                        <!-- Edit Form -->
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    <i class="fa fa-edit"></i> Edit Secondary Category: {{ $secondaryCategory->name }}
                                </h4>
                            </div>
                            <div class="box-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <strong>Error!</strong>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ route('admin.secondary-categories.update', $secondaryCategory) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-md-8">
                                            <!-- Name -->
                                            <div class="form-group">
                                                <label class="form-label">
                                                    Category Name <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="name" 
                                                    class="form-control @error('name') is-invalid @enderror" 
                                                    value="{{ old('name', $secondaryCategory->name) }}" 
                                                    placeholder="e.g., Chattogram, Dhaka, International"
                                                    required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Main Category -->
                                            <div class="form-group">
                                                <label class="form-label">
                                                    Main Category <span class="text-danger">*</span>
                                                </label>
                                                <select name="main_category" 
                                                    class="form-select @error('main_category') is-invalid @enderror" 
                                                    required>
                                                    <option value="">Select Main Category</option>
                                                    @foreach($mainCategories as $mainCat)
                                                        <option value="{{ $mainCat }}" 
                                                            {{ old('main_category', $secondaryCategory->main_category) == $mainCat ? 'selected' : '' }}>
                                                            {{ ucfirst($mainCat) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('main_category')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    This category will appear under the selected main category
                                                </small>
                                            </div>

                                            <!-- Slug -->
                                            <div class="form-group">
                                                <label class="form-label">
                                                    Slug
                                                </label>
                                                <input type="text" name="slug" 
                                                    class="form-control @error('slug') is-invalid @enderror" 
                                                    value="{{ old('slug', $secondaryCategory->slug) }}" 
                                                    placeholder="Auto-generated from name">
                                                @error('slug')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    Leave empty to auto-generate from name
                                                </small>
                                            </div>

                                            <!-- Description -->
                                            <div class="form-group">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" 
                                                    class="form-control @error('description') is-invalid @enderror" 
                                                    rows="4" 
                                                    placeholder="Brief description about this category">{{ old('description', $secondaryCategory->description) }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <!-- Icon Upload -->
                                            <div class="form-group">
                                                <label class="form-label">Category Icon</label>
                                                
                                                <!-- Current Icon -->
                                                @if($secondaryCategory->icon)
                                                    <div class="mb-3">
                                                        <label class="form-text text-muted d-block mb-2">Current Icon:</label>
                                                        <img src="{{ asset('storage/' . $secondaryCategory->icon) }}" 
                                                            alt="{{ $secondaryCategory->name }}" 
                                                            style="max-width: 150px; max-height: 150px; border-radius: 8px; border: 2px solid #e9ecef;"
                                                            id="currentIcon">
                                                        
                                                        <div class="form-check mt-2">
                                                            <input type="checkbox" name="remove_icon" 
                                                                class="form-check-input" 
                                                                id="removeIconCheck" 
                                                                value="1"
                                                                onchange="toggleIconRemoval(this)">
                                                            <label class="form-check-label" for="removeIconCheck">
                                                                Remove current icon
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                <div class="icon-upload-wrapper">
                                                    <input type="file" name="icon" 
                                                        id="iconInput" 
                                                        class="form-control @error('icon') is-invalid @enderror" 
                                                        accept="image/*"
                                                        onchange="previewIcon(this)">
                                                    @error('icon')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">
                                                        Max size: 2MB. Formats: JPEG, PNG, GIF, SVG
                                                    </small>
                                                </div>
                                                
                                                <!-- Preview -->
                                                <div id="iconPreview" style="display: none; margin-top: 15px;">
                                                    <label class="form-text text-muted d-block mb-2">New Icon Preview:</label>
                                                    <img id="iconPreviewImage" 
                                                        src="" 
                                                        alt="Icon Preview" 
                                                        style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #e9ecef;">
                                                </div>
                                            </div>

                                            <!-- Display Order -->
                                            <div class="form-group">
                                                <label class="form-label">Display Order</label>
                                                <input type="number" name="display_order" 
                                                    class="form-control @error('display_order') is-invalid @enderror" 
                                                    value="{{ old('display_order', $secondaryCategory->display_order) }}" 
                                                    min="0">
                                                @error('display_order')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    Lower numbers appear first
                                                </small>
                                            </div>

                                            <!-- Active Status -->
                                            <div class="form-group">
                                                <label class="form-label">Status</label>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" name="active" 
                                                        class="form-check-input" 
                                                        id="activeSwitch" 
                                                        value="1" 
                                                        {{ old('active', $secondaryCategory->active) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="activeSwitch">
                                                        Active
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-save"></i> Update Category
                                        </button>
                                        <a href="{{ route('admin.secondary-categories.index') }}" class="btn btn-secondary">
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
        function previewIcon(input) {
            const preview = document.getElementById('iconPreview');
            const previewImage = document.getElementById('iconPreviewImage');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    preview.style.display = 'block';
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }

        function toggleIconRemoval(checkbox) {
            const currentIcon = document.getElementById('currentIcon');
            const iconInput = document.getElementById('iconInput');
            
            if (checkbox.checked) {
                currentIcon.style.opacity = '0.3';
                iconInput.disabled = true;
            } else {
                currentIcon.style.opacity = '1';
                iconInput.disabled = false;
            }
        }
    </script>
@endsection






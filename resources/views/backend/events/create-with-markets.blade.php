@extends('backend.layouts.master')
@section('title', 'Create New Event')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <!-- Progress Steps -->
                        <div class="progress-steps-container">
                            <div class="steps-indicator">
                                <div class="step active" data-step="1">
                                    <div class="step-icon-wrapper">
                                        <div class="step-number">1</div>
                                        <div class="step-checkmark" style="display: none;">
                                            <i class="fa fa-check"></i>
                                        </div>
                                    </div>
                                    <div class="step-label">Event Details</div>
                                    <div class="step-description">Fill event information</div>
                                </div>
                                <div class="step-connector">
                                    <div class="connector-line"></div>
                                </div>
                                <div class="step" data-step="2">
                                    <div class="step-icon-wrapper">
                                        <div class="step-number">2</div>
                                        <div class="step-checkmark" style="display: none;">
                                            <i class="fa fa-check"></i>
                                        </div>
                                    </div>
                                    <div class="step-label">Add Markets</div>
                                    <div class="step-description">Add trading markets</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-container">
                            <div class="form-header">
                                <div class="header-content">
                                    <h2 class="form-title">
                                        <i class="fa fa-plus-circle"></i>
                                        Create Event with Markets
                                    </h2>
                                    <p class="form-subtitle">Fill in the event details and add markets in one go</p>
                                </div>
                            </div>

                            <!-- Validation Errors -->
                            @if ($errors->any())
                                <div class="alert alert-danger validation-errors">
                                    <div class="alert-header">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <strong>Please fix the following errors:</strong>
                                    </div>
                                    <ul class="error-list">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('admin.events.store-with-markets') }}" method="POST"
                                enctype="multipart/form-data" id="eventWithMarketsForm">
                                @csrf

                                <!-- Step 1: Event Information Section -->
                                <div class="form-section event-section" id="eventSection">
                                    <div class="section-card">
                                        <div class="section-header-card">
                                            <div class="section-header-content">
                                                <div class="section-icon">
                                                    <i class="fa fa-calendar-alt"></i>
                                                </div>
                                                <div>
                                                    <h3 class="section-title">Event Information</h3>
                                                    <p class="section-subtitle">Provide details about your event</p>
                                                </div>
                                            </div>
                                            <span class="section-badge required-badge">Required</span>
                                        </div>

                                        <div class="section-body">
                                            <div class="row">
                                                <!-- Left Column -->
                                                <div class="col-md-8">
                                                    <!-- Event Title -->
                                                    <div class="form-group-modern">
                                                        <label class="form-label-modern">
                                                            <i class="fa fa-heading"></i>
                                                            Event Title
                                                            <span class="required-star">*</span>
                                                        </label>
                                                        <input type="text" name="title"
                                                            class="form-control-modern @error('title') is-invalid @enderror"
                                                            value="{{ old('title') }}"
                                                            placeholder="e.g., Which CEOs will be gone in 2025?" required>
                                                        @error('title')
                                                            <div class="error-message">
                                                                <i class="fa fa-exclamation-circle"></i> {{ $message }}
                                                            </div>
                                                        @enderror
                                                        <small class="form-hint">
                                                            <i class="fa fa-info-circle"></i>
                                                            Enter a clear, descriptive title for the event
                                                        </small>
                                                    </div>

                                                    <!-- Slug -->
                                                    <div class="form-group-modern">
                                                        <label class="form-label-modern">
                                                            <i class="fa fa-link"></i>
                                                            Slug
                                                        </label>
                                                        <input type="text" name="slug"
                                                            class="form-control-modern @error('slug') is-invalid @enderror"
                                                            value="{{ old('slug') }}"
                                                            placeholder="Auto-generated from title" id="slugInput">
                                                        @error('slug')
                                                            <div class="error-message">
                                                                <i class="fa fa-exclamation-circle"></i> {{ $message }}
                                                            </div>
                                                        @enderror
                                                        <small class="form-hint">
                                                            <i class="fa fa-info-circle"></i>
                                                            URL-friendly identifier (auto-generated if left empty)
                                                        </small>
                                                    </div>

                                                    <!-- Description -->
                                                    <div class="form-group-modern">
                                                        <label class="form-label-modern">
                                                            <i class="fa fa-align-left"></i>
                                                            Description
                                                        </label>
                                                        <textarea name="description" class="form-control-modern @error('description') is-invalid @enderror" rows="5"
                                                            placeholder="Describe the event...">{{ old('description') }}</textarea>
                                                        @error('description')
                                                            <div class="error-message">
                                                                <i class="fa fa-exclamation-circle"></i> {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>

                                                    <!-- Category -->
                                                    <div class="form-group-modern">
                                                        <label class="form-label-modern">
                                                            <i class="fa fa-tag"></i>
                                                            Category
                                                        </label>
                                                        <select name="category"
                                                            class="form-control-modern @error('category') is-invalid @enderror">
                                                            <option value="">Auto-detect from title</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category }}"
                                                                    {{ old('category') == $category ? 'selected' : '' }}>
                                                                    {{ ucfirst($category) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('category')
                                                            <div class="error-message">
                                                                <i class="fa fa-exclamation-circle"></i> {{ $message }}
                                                            </div>
                                                        @enderror
                                                        <small class="form-hint">
                                                            <i class="fa fa-info-circle"></i>
                                                            Leave empty to auto-detect from title
                                                        </small>
                                                    </div>
                                                </div>

                                                <!-- Right Column -->
                                                <div class="col-md-4">
                                                    <!-- Event Image Upload -->
                                                    <div class="form-group-modern">
                                                        <label class="form-label-modern">
                                                            <i class="fa fa-image"></i>
                                                            Event Image
                                                        </label>
                                                        <div class="upload-option-tabs">
                                                            <button type="button" class="upload-tab-btn active"
                                                                data-tab="image-upload"
                                                                onclick="switchImageTab('upload')">
                                                                <i class="fa fa-upload"></i> Upload
                                                            </button>
                                                            <button type="button" class="upload-tab-btn"
                                                                data-tab="image-url" onclick="switchImageTab('url')">
                                                                <i class="fa fa-link"></i> URL
                                                            </button>
                                                        </div>

                                                        <!-- File Upload -->
                                                        <div id="imageUploadTab" class="upload-tab-content">
                                                            <div class="file-upload-wrapper">
                                                                <input type="file" name="image_file"
                                                                    id="imageFileInput"
                                                                    class="file-input @error('image_file') is-invalid @enderror"
                                                                    accept="image/*"
                                                                    onchange="handleImageFileSelect(this)">
                                                                <label for="imageFileInput" class="file-upload-label">
                                                                    <i class="fa fa-cloud-upload-alt"></i>
                                                                    <span>Choose Image File</span>
                                                                </label>
                                                                @error('image_file')
                                                                    <div class="error-message">
                                                                        <i class="fa fa-exclamation-circle"></i>
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                            <div class="mt-3" id="imageFilePreview"
                                                                style="display: none;">
                                                                <div class="image-preview-wrapper">
                                                                    <img src="" alt="Preview"
                                                                        class="preview-image" id="imageFilePreviewImg">
                                                                    <button type="button" class="remove-preview"
                                                                        onclick="removeImageFilePreview()">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- URL Input -->
                                                        <div id="imageUrlTab" class="upload-tab-content"
                                                            style="display: none;">
                                                            <input type="url" name="image"
                                                                class="form-control-modern @error('image') is-invalid @enderror"
                                                                value="{{ old('image') }}"
                                                                placeholder="https://example.com/image.jpg"
                                                                oninput="handleImageUrlInput(this)">
                                                            @error('image')
                                                                <div class="error-message">
                                                                    <i class="fa fa-exclamation-circle"></i>
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                            <div class="mt-3" id="imageUrlPreview"
                                                                style="display: none;">
                                                                <div class="image-preview-wrapper">
                                                                    <img src="" alt="Preview"
                                                                        class="preview-image" id="imageUrlPreviewImg">
                                                                    <button type="button" class="remove-preview"
                                                                        onclick="removeImageUrlPreview()">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Event Icon Upload -->
                                                    <div class="form-group-modern">
                                                        <label class="form-label-modern">
                                                            <i class="fa fa-icons"></i>
                                                            Event Icon
                                                        </label>
                                                        <div class="upload-option-tabs">
                                                            <button type="button" class="upload-tab-btn active"
                                                                data-tab="icon-upload" onclick="switchIconTab('upload')">
                                                                <i class="fa fa-upload"></i> Upload
                                                            </button>
                                                            <button type="button" class="upload-tab-btn"
                                                                data-tab="icon-url" onclick="switchIconTab('url')">
                                                                <i class="fa fa-link"></i> URL
                                                            </button>
                                                        </div>

                                                        <!-- File Upload -->
                                                        <div id="iconUploadTab" class="upload-tab-content">
                                                            <div class="file-upload-wrapper">
                                                                <input type="file" name="icon_file" id="iconFileInput"
                                                                    class="file-input @error('icon_file') is-invalid @enderror"
                                                                    accept="image/*"
                                                                    onchange="handleIconFileSelect(this)">
                                                                <label for="iconFileInput" class="file-upload-label">
                                                                    <i class="fa fa-cloud-upload-alt"></i>
                                                                    <span>Choose Icon File</span>
                                                                </label>
                                                                @error('icon_file')
                                                                    <div class="error-message">
                                                                        <i class="fa fa-exclamation-circle"></i>
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                            <div class="mt-3" id="iconFilePreview"
                                                                style="display: none;">
                                                                <div class="image-preview-wrapper">
                                                                    <img src="" alt="Preview"
                                                                        class="preview-image" id="iconFilePreviewImg">
                                                                    <button type="button" class="remove-preview"
                                                                        onclick="removeIconFilePreview()">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- URL Input -->
                                                        <div id="iconUrlTab" class="upload-tab-content"
                                                            style="display: none;">
                                                            <input type="url" name="icon"
                                                                class="form-control-modern @error('icon') is-invalid @enderror"
                                                                value="{{ old('icon') }}"
                                                                placeholder="https://example.com/icon.jpg"
                                                                oninput="handleIconUrlInput(this)">
                                                            @error('icon')
                                                                <div class="error-message">
                                                                    <i class="fa fa-exclamation-circle"></i>
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                            <div class="mt-3" id="iconUrlPreview"
                                                                style="display: none;">
                                                                <div class="image-preview-wrapper">
                                                                    <img src="" alt="Preview"
                                                                        class="preview-image" id="iconUrlPreviewImg">
                                                                    <button type="button" class="remove-preview"
                                                                        onclick="removeIconUrlPreview()">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Dates -->
                                                    <div class="form-group-modern">
                                                        <label class="form-label-modern">
                                                            <i class="fa fa-calendar-check"></i>
                                                            Start Date
                                                        </label>
                                                        <input type="datetime-local" name="start_date"
                                                            class="form-control-modern @error('start_date') is-invalid @enderror"
                                                            value="{{ old('start_date') }}">
                                                        @error('start_date')
                                                            <div class="error-message">
                                                                <i class="fa fa-exclamation-circle"></i> {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group-modern">
                                                        <label class="form-label-modern">
                                                            <i class="fa fa-calendar-times"></i>
                                                            End Date
                                                        </label>
                                                        <input type="datetime-local" name="end_date"
                                                            class="form-control-modern @error('end_date') is-invalid @enderror"
                                                            value="{{ old('end_date') }}">
                                                        @error('end_date')
                                                            <div class="error-message">
                                                                <i class="fa fa-exclamation-circle"></i> {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>

                                                    <!-- Status Toggles -->
                                                    <div class="form-group-modern">
                                                        <label class="form-label-modern">
                                                            <i class="fa fa-toggle-on"></i>
                                                            Status
                                                        </label>
                                                        <div class="toggle-group">
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" name="active" id="active"
                                                                    value="1"
                                                                    {{ old('active', true) ? 'checked' : '' }}>
                                                                <span class="toggle-slider"></span>
                                                                <span class="toggle-label">Active</span>
                                                            </label>
                                                            <label class="toggle-switch">
                                                                <input type="checkbox" name="featured" id="featured"
                                                                    value="1" {{ old('featured') ? 'checked' : '' }}>
                                                                <span class="toggle-slider"></span>
                                                                <span class="toggle-label">Featured</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Divider -->
                                <div class="section-divider">
                                    <div class="divider-line"></div>
                                    <div class="divider-icon">
                                        <i class="fa fa-arrow-down"></i>
                                    </div>
                                    <div class="divider-line"></div>
                                </div>

                                <!-- Step 2: Markets Section -->
                                <div class="form-section markets-section" id="marketsSection">
                                    <div class="section-card">
                                        <div class="section-header-card">
                                            <div class="section-header-content">
                                                <div class="section-icon markets-icon">
                                                    <i class="fa fa-chart-line"></i>
                                                </div>
                                                <div>
                                                    <h3 class="section-title">Add Markets</h3>
                                                    <p class="section-subtitle">Add trading markets for this event</p>
                                                </div>
                                            </div>
                                            <div class="section-actions">
                                                <span class="section-badge info-badge">
                                                    <i class="fa fa-info-circle"></i>
                                                    At least 1 market required
                                                </span>
                                                <button type="button" class="btn-add-market" id="addMarketBtn">
                                                    <i class="fa fa-plus"></i>
                                                    Add Market
                                                </button>
                                            </div>
                                        </div>

                                        <div class="section-body">
                                            <div id="marketsContainer">
                                                <!-- Market Template (Hidden) -->
                                                <!-- Market Template (Hidden) - All inputs disabled to prevent validation -->
                                                <div class="market-item-template" style="display: none;">
                                                    <div class="market-card">
                                                        <div class="market-header">
                                                            <div class="market-header-left">
                                                                <div class="market-number-badge">
                                                                    <span class="market-number">1</span>
                                                                </div>
                                                                <div>
                                                                    <h4 class="market-title">Market <span
                                                                            class="market-number-text">1</span></h4>
                                                                    <p class="market-subtitle">Trading market details</p>
                                                                </div>
                                                            </div>
                                                            <button type="button"
                                                                class="btn-remove-market remove-market">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </div>
                                                        <div class="market-body">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <div class="form-group-modern">
                                                                        <label class="form-label-modern">
                                                                            <i class="fa fa-question-circle"></i>
                                                                            Market Question
                                                                            <span class="required-star">*</span>
                                                                        </label>
                                                                        <input type="text"
                                                                            name="markets[INDEX][question]"
                                                                            class="form-control-modern market-question"
                                                                            placeholder="e.g., Tim Cook out as Apple CEO in 2025?"
                                                                            disabled>
                                                                    </div>

                                                                    <div class="form-group-modern">
                                                                        <label class="form-label-modern">
                                                                            <i class="fa fa-align-left"></i>
                                                                            Description
                                                                        </label>
                                                                        <textarea name="markets[INDEX][description]" class="form-control-modern" rows="3"
                                                                            placeholder="Market description..." disabled></textarea>
                                                                    </div>

                                                                    <div class="form-group-modern">
                                                                        <label class="form-label-modern">
                                                                            <i class="fa fa-link"></i>
                                                                            Slug
                                                                        </label>
                                                                        <input type="text" name="markets[INDEX][slug]"
                                                                            class="form-control-modern market-slug"
                                                                            placeholder="Auto-generated from question"
                                                                            disabled>
                                                                        <small class="form-hint">
                                                                            <i class="fa fa-info-circle"></i>
                                                                            Auto-generated if left empty
                                                                        </small>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <div class="form-group-modern">
                                                                        <label class="form-label-modern">
                                                                            <i class="fa fa-image"></i>
                                                                            Market Image URL
                                                                        </label>
                                                                        <input type="url" name="markets[INDEX][image]"
                                                                            class="form-control-modern"
                                                                            placeholder="https://example.com/image.jpg"
                                                                            disabled>
                                                                    </div>

                                                                    <div class="form-group-modern">
                                                                        <label class="form-label-modern">
                                                                            <i class="fa fa-icons"></i>
                                                                            Market Icon URL
                                                                        </label>
                                                                        <input type="url" name="markets[INDEX][icon]"
                                                                            class="form-control-modern"
                                                                            placeholder="https://example.com/icon.jpg"
                                                                            disabled>
                                                                    </div>

                                                                    <div class="form-group-modern">
                                                                        <label class="form-label-modern">
                                                                            <i class="fa fa-calendar-check"></i>
                                                                            Start Date
                                                                        </label>
                                                                        <input type="datetime-local"
                                                                            name="markets[INDEX][start_date]"
                                                                            class="form-control-modern" disabled>
                                                                    </div>

                                                                    <div class="form-group-modern">
                                                                        <label class="form-label-modern">
                                                                            <i class="fa fa-calendar-times"></i>
                                                                            End Date
                                                                        </label>
                                                                        <input type="datetime-local"
                                                                            name="markets[INDEX][end_date]"
                                                                            class="form-control-modern" disabled>
                                                                    </div>

                                                                    <div class="form-group-modern">
                                                                        <label class="form-label-modern">
                                                                            <i class="fa fa-dollar-sign"></i>
                                                                            Initial Prices
                                                                        </label>
                                                                        <div class="price-input-group">
                                                                            <div class="price-input-wrapper">
                                                                                <label class="price-label">Yes</label>
                                                                                <input type="number"
                                                                                    name="markets[INDEX][yes_price]"
                                                                                    class="form-control-modern price-input"
                                                                                    step="0.001" min="0"
                                                                                    max="1" value="0.5"
                                                                                    placeholder="0.5" disabled>
                                                                            </div>
                                                                            <div class="price-input-wrapper">
                                                                                <label class="price-label">No</label>
                                                                                <input type="number"
                                                                                    name="markets[INDEX][no_price]"
                                                                                    class="form-control-modern price-input"
                                                                                    step="0.001" min="0"
                                                                                    max="1" value="0.5"
                                                                                    placeholder="0.5" disabled>
                                                                            </div>
                                                                        </div>
                                                                        <small class="form-hint">
                                                                            <i class="fa fa-info-circle"></i>
                                                                            Must sum to 1.0
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Initial Market Item -->
                                                <div class="market-item">
                                                    <div class="market-card">
                                                        <div class="market-header">
                                                            <div class="market-header-left">
                                                                <div class="market-number-badge">
                                                                    <span class="market-number">1</span>
                                                                </div>
                                                                <div>
                                                                    <h4 class="market-title">Market 1</h4>
                                                                    <p class="market-subtitle">Trading market details</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="market-body">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <div class="form-group-modern">
                                                                        <label class="form-label-modern">
                                                                            <i class="fa fa-question-circle"></i>
                                                                            Market Question
                                                                            <span class="required-star">*</span>
                                                                        </label>
                                                                        <input type="text" name="markets[0][question]"
                                                                            class="form-control-modern market-question @error('markets.0.question') is-invalid @enderror"
                                                                            placeholder="e.g., Tim Cook out as Apple CEO in 2025?"
                                                                            value="{{ old('markets.0.question') }}"
                                                                            required>
                                                                        @error('markets.0.question')
                                                                            <div class="error-message">
                                                                                <i class="fa fa-exclamation-circle"></i>
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>

                                                                    <div class="form-group-modern">
                                                                        <label class="form-label-modern">
                                                                            <i class="fa fa-align-left"></i>
                                                                            Description
                                                                        </label>
                                                                        <textarea name="markets[0][description]" class="form-control-modern" rows="3"
                                                                            placeholder="Market description..."></textarea>
                                                                    </div>

                                                                    <div class="form-group-modern">
                                                                        <label class="form-label-modern">
                                                                            <i class="fa fa-link"></i>
                                                                            Slug
                                                                        </label>
                                                                        <input type="text" name="markets[0][slug]"
                                                                            class="form-control-modern market-slug"
                                                                            placeholder="Auto-generated from question">
                                                                        <small class="form-hint">
                                                                            <i class="fa fa-info-circle"></i>
                                                                            Auto-generated if left empty
                                                                        </small>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <div class="form-group-modern">
                                                                        <label class="form-label-modern">
                                                                            <i class="fa fa-image"></i>
                                                                            Market Image URL
                                                                        </label>
                                                                        <input type="url" name="markets[0][image]"
                                                                            class="form-control-modern"
                                                                            placeholder="https://example.com/image.jpg">
                                                                    </div>

                                                                    <div class="form-group-modern">
                                                                        <label class="form-label-modern">
                                                                            <i class="fa fa-icons"></i>
                                                                            Market Icon URL
                                                                        </label>
                                                                        <input type="url" name="markets[0][icon]"
                                                                            class="form-control-modern"
                                                                            placeholder="https://example.com/icon.jpg">
                                                                    </div>

                                                                    <div class="form-group-modern">
                                                                        <label class="form-label-modern">
                                                                            <i class="fa fa-calendar-check"></i>
                                                                            Start Date
                                                                        </label>
                                                                        <input type="datetime-local"
                                                                            name="markets[0][start_date]"
                                                                            class="form-control-modern">
                                                                    </div>

                                                                    <div class="form-group-modern">
                                                                        <label class="form-label-modern">
                                                                            <i class="fa fa-calendar-times"></i>
                                                                            End Date
                                                                        </label>
                                                                        <input type="datetime-local"
                                                                            name="markets[0][end_date]"
                                                                            class="form-control-modern">
                                                                    </div>

                                                                    <div class="form-group-modern">
                                                                        <label class="form-label-modern">
                                                                            <i class="fa fa-dollar-sign"></i>
                                                                            Initial Prices
                                                                        </label>
                                                                        <div class="price-input-group">
                                                                            <div class="price-input-wrapper">
                                                                                <label class="price-label">Yes</label>
                                                                                <input type="number"
                                                                                    name="markets[0][yes_price]"
                                                                                    class="form-control-modern price-input"
                                                                                    step="0.001" min="0"
                                                                                    max="1" value="0.5"
                                                                                    placeholder="0.5">
                                                                            </div>
                                                                            <div class="price-input-wrapper">
                                                                                <label class="price-label">No</label>
                                                                                <input type="number"
                                                                                    name="markets[0][no_price]"
                                                                                    class="form-control-modern price-input"
                                                                                    step="0.001" min="0"
                                                                                    max="1" value="0.5"
                                                                                    placeholder="0.5">
                                                                            </div>
                                                                        </div>
                                                                        <small class="form-hint">
                                                                            <i class="fa fa-info-circle"></i>
                                                                            Must sum to 1.0
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="form-actions">
                                    <button type="submit" class="btn-submit">
                                        <i class="fa fa-save"></i>
                                        <span>Create Event with Markets</span>
                                    </button>
                                    <a href="{{ route('admin.events.index') }}" class="btn-cancel">
                                        <i class="fa fa-times"></i>
                                        <span>Cancel</span>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        let marketIndex = 1;

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

        // Image/Icon Tab Switching
        function switchImageTab(type) {
            const uploadTab = document.getElementById('imageUploadTab');
            const urlTab = document.getElementById('imageUrlTab');
            const uploadBtn = document.querySelector('[data-tab="image-upload"]');
            const urlBtn = document.querySelector('[data-tab="image-url"]');

            if (type === 'upload') {
                uploadTab.style.display = 'block';
                urlTab.style.display = 'none';
                uploadBtn.classList.add('active');
                urlBtn.classList.remove('active');
            } else {
                uploadTab.style.display = 'none';
                urlTab.style.display = 'block';
                uploadBtn.classList.remove('active');
                urlBtn.classList.add('active');
            }
        }

        function switchIconTab(type) {
            const uploadTab = document.getElementById('iconUploadTab');
            const urlTab = document.getElementById('iconUrlTab');
            const uploadBtn = document.querySelector('[data-tab="icon-upload"]');
            const urlBtn = document.querySelector('[data-tab="icon-url"]');

            if (type === 'upload') {
                uploadTab.style.display = 'block';
                urlTab.style.display = 'none';
                uploadBtn.classList.add('active');
                urlBtn.classList.remove('active');
            } else {
                uploadTab.style.display = 'none';
                urlTab.style.display = 'block';
                uploadBtn.classList.remove('active');
                urlBtn.classList.add('active');
            }
        }

        // Image File Upload Handler
        function handleImageFileSelect(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imageFilePreview');
                    const previewImg = document.getElementById('imageFilePreviewImg');
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeImageFilePreview() {
            document.getElementById('imageFilePreview').style.display = 'none';
            document.getElementById('imageFileInput').value = '';
        }

        // Image URL Handler
        function handleImageUrlInput(input) {
            const preview = document.getElementById('imageUrlPreview');
            const previewImg = document.getElementById('imageUrlPreviewImg');
            if (input.value) {
                previewImg.src = input.value;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }

        function removeImageUrlPreview() {
            document.getElementById('imageUrlPreview').style.display = 'none';
            document.querySelector('input[name="image"]').value = '';
        }

        // Icon File Upload Handler
        function handleIconFileSelect(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('iconFilePreview');
                    const previewImg = document.getElementById('iconFilePreviewImg');
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeIconFilePreview() {
            document.getElementById('iconFilePreview').style.display = 'none';
            document.getElementById('iconFileInput').value = '';
        }

        // Icon URL Handler
        function handleIconUrlInput(input) {
            const preview = document.getElementById('iconUrlPreview');
            const previewImg = document.getElementById('iconUrlPreviewImg');
            if (input.value) {
                previewImg.src = input.value;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }

        function removeIconUrlPreview() {
            document.getElementById('iconUrlPreview').style.display = 'none';
            document.querySelector('input[name="icon"]').value = '';
        }

        // Add new market
        document.getElementById('addMarketBtn').addEventListener('click', function() {
            const template = document.querySelector('.market-item-template');
            const newMarket = template.cloneNode(true);
            newMarket.style.display = 'block';
            newMarket.classList.remove('market-item-template');

            // Update all inputs with new index and enable them
            newMarket.querySelectorAll('input, textarea, select').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace('[INDEX]', `[${marketIndex}]`);
                    // Remove disabled attribute and add required to question field
                    input.removeAttribute('disabled');
                    if (input.name.includes('[question]')) {
                        input.setAttribute('required', 'required');
                    }
                }
            });

            // Clear any error classes from template
            newMarket.querySelectorAll('.error-message').forEach(error => {
                error.remove();
            });

            // Update market number
            const numberElement = newMarket.querySelector('.market-number');
            const numberText = newMarket.querySelector('.market-number-text');
            if (numberElement) numberElement.textContent = marketIndex + 1;
            if (numberText) numberText.textContent = marketIndex + 1;

            document.getElementById('marketsContainer').appendChild(newMarket);
            marketIndex++;

            // Update remove buttons visibility
            updateRemoveButtons();
            updateMarketNumbers();

            // Scroll to new market with animation
            setTimeout(() => {
                newMarket.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
                newMarket.style.animation = 'slideIn 0.5s ease-out';
            }, 100);
        });

        // Remove market
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-market')) {
                const marketItem = e.target.closest('.market-item');
                marketItem.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => {
                    marketItem.remove();
                    updateRemoveButtons();
                    updateMarketNumbers();
                }, 300);
            }
        });

        // Auto-generate slug from question
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('market-question')) {
                const slugInput = e.target.closest('.market-item').querySelector('.market-slug');
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
            }
        });

        // Manual slug edit
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('market-slug')) {
                e.target.dataset.autoGenerated = 'false';
            }
        });

        function updateRemoveButtons() {
            const markets = document.querySelectorAll('.market-item:not(.market-item-template)');
            markets.forEach((market, index) => {
                const removeBtn = market.querySelector('.remove-market');
                if (removeBtn) {
                    removeBtn.style.display = markets.length > 1 ? 'flex' : 'none';
                }
            });
        }

        function updateMarketNumbers() {
            const markets = document.querySelectorAll('.market-item:not(.market-item-template)');
            markets.forEach((market, index) => {
                const numberElement = market.querySelector('.market-number');
                const numberText = market.querySelector('.market-number-text');
                if (numberElement) numberElement.textContent = index + 1;
                if (numberText) numberText.textContent = index + 1;
            });
        }

        // Validate price sum
        document.getElementById('eventWithMarketsForm').addEventListener('submit', function(e) {
            const markets = document.querySelectorAll('.market-item:not(.market-item-template)');
            let isValid = true;

            markets.forEach(market => {
                const yesPriceInput = market.querySelector('input[name*="[yes_price]"]');
                const noPriceInput = market.querySelector('input[name*="[no_price]"]');

                if (yesPriceInput && noPriceInput) {
                    const yesPrice = parseFloat(yesPriceInput.value) || 0;
                    const noPrice = parseFloat(noPriceInput.value) || 0;
                    const sum = yesPrice + noPrice;

                    if (Math.abs(sum - 1.0) > 0.001) {
                        isValid = false;
                        alert('Yes and No prices must sum to 1.0 for all markets');
                        e.preventDefault();
                        return false;
                    }
                }
            });
        });

        // Scroll to first error on page load if validation errors exist
        document.addEventListener('DOMContentLoaded', function() {
            const firstError = document.querySelector('.validation-errors, .error-message, .is-invalid');
            if (firstError) {
                setTimeout(() => {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    // Highlight the error
                    firstError.style.animation = 'pulse 0.5s ease-in-out 3';
                }, 300);
            }
        });

        // Update step indicator on scroll
        let scrollTimeout;
        window.addEventListener('scroll', function() {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                const eventSection = document.getElementById('eventSection');
                const marketsSection = document.getElementById('marketsSection');
                const marketsRect = marketsSection.getBoundingClientRect();

                const step1 = document.querySelector('.step[data-step="1"]');
                const step2 = document.querySelector('.step[data-step="2"]');

                if (marketsRect.top < window.innerHeight / 2) {
                    step1.classList.remove('active');
                    step2.classList.add('active');
                } else {
                    step1.classList.add('active');
                    step2.classList.remove('active');
                }
            }, 100);
        });
    </script>

    <style>
        /* Progress Steps */
        .progress-steps-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 40px 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        }

        .steps-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            max-width: 800px;
            margin: 0 auto;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
            transition: all 0.3s ease;
        }

        .step-icon-wrapper {
            position: relative;
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
        }

        .step-number {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            transition: all 0.3s ease;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .step-checkmark {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: #4caf50;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            opacity: 0;
            transform: scale(0);
            transition: all 0.3s ease;
        }

        .step.active .step-number {
            background: white;
            color: #667eea;
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.4);
            border-color: white;
        }

        .step.completed .step-checkmark {
            opacity: 1;
            transform: scale(1);
        }

        .step-label {
            color: white;
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 5px;
            text-align: center;
        }

        .step-description {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            text-align: center;
        }

        .step-connector {
            flex: 1;
            max-width: 200px;
            height: 4px;
            position: relative;
            margin-top: -50px;
        }

        .connector-line {
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .step.active~.step-connector .connector-line,
        .step-connector:has(+ .step.active) .connector-line {
            background: rgba(255, 255, 255, 0.7);
        }

        /* Form Container */
        .form-container {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 30px 40px;
            border-bottom: 1px solid #e0e0e0;
        }

        .form-title {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .form-title i {
            color: #667eea;
        }

        .form-subtitle {
            color: #6c757d;
            margin: 0;
            font-size: 16px;
        }

        /* Form Sections */
        .form-section {
            padding: 40px;
        }

        .section-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e9ecef;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .section-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border-color: #667eea;
        }

        .section-header-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 25px 30px;
            border-bottom: 2px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-header-content {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .section-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .markets-icon {
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 5px 0;
        }

        .section-subtitle {
            color: #6c757d;
            margin: 0;
            font-size: 14px;
        }

        .section-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .required-badge {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
        }

        .info-badge {
            background: linear-gradient(135deg, #4dabf7 0%, #339af0 100%);
            color: white;
        }

        .section-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .section-body {
            padding: 30px;
        }

        /* Modern Form Controls */
        .form-group-modern {
            margin-bottom: 25px;
        }

        .form-label-modern {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .form-label-modern i {
            color: #667eea;
            font-size: 16px;
        }

        .required-star {
            color: #ff6b6b;
            margin-left: 3px;
        }

        .form-control-modern {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #ffffff;
        }

        .form-control-modern:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-hint {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #6c757d;
            font-size: 12px;
            margin-top: 6px;
        }

        .form-hint i {
            color: #667eea;
        }

        .error-message {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #ff6b6b;
            font-size: 13px;
            margin-top: 6px;
        }

        /* Upload Tabs */
        .upload-option-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
            border-bottom: 2px solid #e9ecef;
        }

        .upload-tab-btn {
            padding: 8px 16px;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            color: #6c757d;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .upload-tab-btn:hover {
            color: #667eea;
        }

        .upload-tab-btn.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }

        .upload-tab-content {
            margin-top: 0;
        }

        /* File Upload */
        .file-upload-wrapper {
            position: relative;
        }

        .file-input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
            border: 2px dashed #667eea;
            border-radius: 12px;
            background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .file-upload-label:hover {
            border-color: #764ba2;
            background: linear-gradient(135deg, #f0f2ff 0%, #f8f9ff 100%);
            transform: translateY(-2px);
        }

        .file-upload-label i {
            font-size: 32px;
            color: #667eea;
            margin-bottom: 10px;
        }

        .file-upload-label span {
            color: #2c3e50;
            font-weight: 600;
            font-size: 14px;
        }

        /* Image Preview */
        .image-preview-wrapper {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid #e9ecef;
        }

        .preview-image {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            display: block;
        }

        .remove-preview {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(255, 107, 107, 0.9);
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .remove-preview:hover {
            background: #ff6b6b;
            transform: scale(1.1);
        }

        /* Toggle Switches */
        .toggle-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .toggle-switch {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }

        .toggle-switch input {
            display: none;
        }

        .toggle-slider {
            width: 50px;
            height: 26px;
            background: #ccc;
            border-radius: 26px;
            position: relative;
            transition: all 0.3s ease;
        }

        .toggle-slider::before {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: white;
            top: 3px;
            left: 3px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .toggle-switch input:checked+.toggle-slider {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .toggle-switch input:checked+.toggle-slider::before {
            transform: translateX(24px);
        }

        .toggle-label {
            font-weight: 500;
            color: #2c3e50;
        }

        /* Section Divider */
        .section-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 50px 0;
            position: relative;
        }

        .divider-line {
            flex: 1;
            height: 3px;
            background: linear-gradient(to right, transparent, #667eea, transparent);
        }

        .divider-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        /* Market Cards */
        .market-card {
            background: #ffffff;
            border: 2px solid #e9ecef;
            border-radius: 16px;
            margin-bottom: 25px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .market-card:hover {
            border-color: #667eea;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
            transform: translateY(-2px);
        }

        .market-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .market-header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .market-number-badge {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .market-title {
            color: white;
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 5px 0;
        }

        .market-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 13px;
            margin: 0;
        }

        .btn-remove-market {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(255, 107, 107, 0.9);
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-remove-market:hover {
            background: #ff6b6b;
            transform: scale(1.1);
        }

        .market-body {
            padding: 25px;
        }

        /* Price Input Group */
        .price-input-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .price-input-wrapper {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .price-label {
            font-size: 13px;
            font-weight: 600;
            color: #495057;
        }

        .price-input {
            text-align: center;
            font-weight: 600;
        }

        /* Buttons */
        .btn-add-market {
            padding: 12px 24px;
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .btn-add-market:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }

        .form-actions {
            padding: 30px 40px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .btn-submit {
            padding: 15px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-cancel {
            padding: 15px 40px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        /* Validation Errors */
        .validation-errors {
            margin: 20px 40px;
            border-radius: 12px;
            border: 2px solid #ff6b6b;
            background: linear-gradient(135deg, #fff5f5 0%, #ffe0e0 100%);
            padding: 20px 25px;
            animation: slideIn 0.3s ease-out;
        }

        .alert-header {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            font-weight: 700;
            color: #c92a2a;
            margin-bottom: 15px;
        }

        .alert-header i {
            font-size: 20px;
        }

        .error-list {
            margin: 0;
            padding-left: 25px;
            color: #c92a2a;
        }

        .error-list li {
            margin-bottom: 8px;
            line-height: 1.6;
        }

        .error-list li:last-child {
            margin-bottom: 0;
        }

        .form-control-modern.is-invalid {
            border-color: #ff6b6b;
            background-color: #fff5f5;
        }

        .form-control-modern.is-invalid:focus {
            border-color: #ff6b6b;
            box-shadow: 0 0 0 4px rgba(255, 107, 107, 0.1);
        }

        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .steps-indicator {
                flex-direction: column;
                gap: 20px;
            }

            .step-connector {
                width: 4px;
                height: 50px;
                margin: 0;
                margin-left: 38px;
            }

            .form-section {
                padding: 20px;
            }

            .section-header-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .section-actions {
                width: 100%;
                flex-direction: column;
            }

            .price-input-group {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn-submit,
            .btn-cancel {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endsection

@extends('backend.layouts.master')
@section('title', 'Add Markets')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <!-- Event Info Card -->
                        <div class="box mb-3">
                            <div class="box-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-1">{{ $event->title }}</h4>
                                        <p class="text-muted mb-0">{{ $event->description ?? 'No description' }}</p>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.events.show', $event) }}" class="btn btn-secondary">
                                            <i class="fa fa-arrow-left"></i> Back to Event
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Markets Form -->
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    <i class="fa fa-plus-circle"></i> Add Markets to Event
                                </h4>
                            </div>
                            <div class="box-body">
                                <form action="{{ route('admin.events.store-markets', $event) }}" method="POST"
                                    enctype="multipart/form-data" id="marketsForm">
                                    @csrf

                                    <div id="marketsContainer">
                                        <!-- Market Template (Hidden) -->
                                        <div class="market-item-template" style="display: none;">
                                            <div class="box market-item mb-3">
                                                <div class="box-header with-border bg-primary">
                                                    <h5 class="box-title text-white">
                                                        <i class="fa fa-chart-line"></i> Market <span
                                                            class="market-number">1</span>
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger float-right remove-market"
                                                            style="display: none;">
                                                            <i class="fa fa-times"></i> Remove
                                                        </button>
                                                    </h5>
                                                </div>
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <label class="form-label required">Market Question <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" name="markets[INDEX][question]"
                                                                    class="form-control market-question"
                                                                    placeholder="e.g., Tim Cook out as Apple CEO in 2025?"
                                                                    required>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Description</label>
                                                                <textarea name="markets[INDEX][description]" class="form-control" rows="3" placeholder="Market description..."></textarea>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Slug</label>
                                                                <input type="text" name="markets[INDEX][slug]"
                                                                    class="form-control market-slug"
                                                                    placeholder="Auto-generated from question">
                                                                <small class="form-text text-muted">Auto-generated if left
                                                                    empty</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <!-- Market Image Upload -->
                                                            <div class="form-group">
                                                                <label class="form-label">Market Image</label>
                                                                <div class="upload-option-tabs">
                                                                    <button type="button" class="upload-tab-btn active"
                                                                        data-tab="market-image-upload-INDEX"
                                                                        onclick="switchMarketImageTab('upload', 'INDEX')">
                                                                        <i class="fa fa-upload"></i> Upload
                                                                    </button>
                                                                    <button type="button" class="upload-tab-btn"
                                                                        data-tab="market-image-url-INDEX"
                                                                        onclick="switchMarketImageTab('url', 'INDEX')">
                                                                        <i class="fa fa-link"></i> URL
                                                                    </button>
                                                                </div>

                                                                <!-- File Upload -->
                                                                <div class="upload-tab-content"
                                                                    id="marketImageUploadTab-INDEX">
                                                                    <div class="file-upload-wrapper">
                                                                        <input type="file"
                                                                            name="markets[INDEX][image_file]"
                                                                            id="marketImageFileInput-INDEX"
                                                                            class="file-input" accept="image/*"
                                                                            onchange="handleMarketImageFileSelect(this, 'INDEX')">
                                                                        <label for="marketImageFileInput-INDEX"
                                                                            class="file-upload-label">
                                                                            <i class="fa fa-cloud-upload-alt"></i>
                                                                            <span>Choose Image File</span>
                                                                        </label>
                                                                    </div>
                                                                    <div class="mt-3 market-image-file-preview-INDEX"
                                                                        style="display: none;">
                                                                        <div class="image-preview-wrapper">
                                                                            <img src="" alt="Preview"
                                                                                class="preview-image">
                                                                            <button type="button" class="remove-preview"
                                                                                onclick="removeMarketImageFilePreview('INDEX')">
                                                                                <i class="fa fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- URL Input -->
                                                                <div class="upload-tab-content" id="marketImageUrlTab-INDEX"
                                                                    style="display: none;">
                                                                    <input type="url" name="markets[INDEX][image]"
                                                                        class="form-control"
                                                                        placeholder="https://example.com/image.jpg"
                                                                        oninput="handleMarketImageUrlInput(this, 'INDEX')">
                                                                    <div class="mt-3 market-image-url-preview-INDEX"
                                                                        style="display: none;">
                                                                        <div class="image-preview-wrapper">
                                                                            <img src="" alt="Preview"
                                                                                class="preview-image">
                                                                            <button type="button" class="remove-preview"
                                                                                onclick="removeMarketImageUrlPreview('INDEX')">
                                                                                <i class="fa fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Market Icon Upload -->
                                                            <div class="form-group">
                                                                <label class="form-label">Market Icon</label>
                                                                <div class="upload-option-tabs">
                                                                    <button type="button" class="upload-tab-btn active"
                                                                        data-tab="market-icon-upload-INDEX"
                                                                        onclick="switchMarketIconTab('upload', 'INDEX')">
                                                                        <i class="fa fa-upload"></i> Upload
                                                                    </button>
                                                                    <button type="button" class="upload-tab-btn"
                                                                        data-tab="market-icon-url-INDEX"
                                                                        onclick="switchMarketIconTab('url', 'INDEX')">
                                                                        <i class="fa fa-link"></i> URL
                                                                    </button>
                                                                </div>

                                                                <!-- File Upload -->
                                                                <div class="upload-tab-content"
                                                                    id="marketIconUploadTab-INDEX">
                                                                    <div class="file-upload-wrapper">
                                                                        <input type="file"
                                                                            name="markets[INDEX][icon_file]"
                                                                            id="marketIconFileInput-INDEX"
                                                                            class="file-input" accept="image/*"
                                                                            onchange="handleMarketIconFileSelect(this, 'INDEX')">
                                                                        <label for="marketIconFileInput-INDEX"
                                                                            class="file-upload-label">
                                                                            <i class="fa fa-cloud-upload-alt"></i>
                                                                            <span>Choose Icon File</span>
                                                                        </label>
                                                                    </div>
                                                                    <div class="mt-3 market-icon-file-preview-INDEX"
                                                                        style="display: none;">
                                                                        <div class="image-preview-wrapper">
                                                                            <img src="" alt="Preview"
                                                                                class="preview-image">
                                                                            <button type="button" class="remove-preview"
                                                                                onclick="removeMarketIconFilePreview('INDEX')">
                                                                                <i class="fa fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- URL Input -->
                                                                <div class="upload-tab-content"
                                                                    id="marketIconUrlTab-INDEX" style="display: none;">
                                                                    <input type="url" name="markets[INDEX][icon]"
                                                                        class="form-control"
                                                                        placeholder="https://example.com/icon.jpg"
                                                                        oninput="handleMarketIconUrlInput(this, 'INDEX')">
                                                                    <div class="mt-3 market-icon-url-preview-INDEX"
                                                                        style="display: none;">
                                                                        <div class="image-preview-wrapper">
                                                                            <img src="" alt="Preview"
                                                                                class="preview-image">
                                                                            <button type="button" class="remove-preview"
                                                                                onclick="removeMarketIconUrlPreview('INDEX')">
                                                                                <i class="fa fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Start Date</label>
                                                                <input type="datetime-local"
                                                                    name="markets[INDEX][start_date]"
                                                                    class="form-control">
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">End Date</label>
                                                                <input type="datetime-local"
                                                                    name="markets[INDEX][end_date]" class="form-control">
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Initial Prices</label>
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <label class="small">Yes Price</label>
                                                                        <input type="number"
                                                                            name="markets[INDEX][yes_price]"
                                                                            class="form-control" step="0.001"
                                                                            min="0" max="1" value="0.5"
                                                                            placeholder="0.5">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label class="small">No Price</label>
                                                                        <input type="number"
                                                                            name="markets[INDEX][no_price]"
                                                                            class="form-control" step="0.001"
                                                                            min="0" max="1" value="0.5"
                                                                            placeholder="0.5">
                                                                    </div>
                                                                </div>
                                                                <small class="form-text text-muted">Must sum to 1.0</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Initial Market Item -->
                                        <div class="market-item mb-3">
                                            <div class="box">
                                                <div class="box-header with-border bg-primary">
                                                    <h5 class="box-title text-white">
                                                        <i class="fa fa-chart-line"></i> Market 1
                                                    </h5>
                                                </div>
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <label class="form-label required">Market Question <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" name="markets[0][question]"
                                                                    class="form-control market-question"
                                                                    placeholder="e.g., Tim Cook out as Apple CEO in 2025?"
                                                                    required>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Description</label>
                                                                <textarea name="markets[0][description]" class="form-control" rows="3" placeholder="Market description..."></textarea>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Slug</label>
                                                                <input type="text" name="markets[0][slug]"
                                                                    class="form-control market-slug"
                                                                    placeholder="Auto-generated from question">
                                                                <small class="form-text text-muted">Auto-generated if left
                                                                    empty</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <!-- Market Image Upload -->
                                                            <div class="form-group">
                                                                <label class="form-label">Market Image</label>
                                                                <div class="upload-option-tabs">
                                                                    <button type="button" class="upload-tab-btn active"
                                                                        data-tab="market-image-upload-0"
                                                                        onclick="switchMarketImageTab('upload', '0')">
                                                                        <i class="fa fa-upload"></i> Upload
                                                                    </button>
                                                                    <button type="button" class="upload-tab-btn"
                                                                        data-tab="market-image-url-0"
                                                                        onclick="switchMarketImageTab('url', '0')">
                                                                        <i class="fa fa-link"></i> URL
                                                                    </button>
                                                                </div>

                                                                <!-- File Upload -->
                                                                <div class="upload-tab-content"
                                                                    id="marketImageUploadTab-0">
                                                                    <div class="file-upload-wrapper">
                                                                        <input type="file"
                                                                            name="markets[0][image_file]"
                                                                            id="marketImageFileInput-0" class="file-input"
                                                                            accept="image/*"
                                                                            onchange="handleMarketImageFileSelect(this, '0')">
                                                                        <label for="marketImageFileInput-0"
                                                                            class="file-upload-label">
                                                                            <i class="fa fa-cloud-upload-alt"></i>
                                                                            <span>Choose Image File</span>
                                                                        </label>
                                                                    </div>
                                                                    <div class="mt-3 market-image-file-preview-0"
                                                                        style="display: none;">
                                                                        <div class="image-preview-wrapper">
                                                                            <img src="" alt="Preview"
                                                                                class="preview-image">
                                                                            <button type="button" class="remove-preview"
                                                                                onclick="removeMarketImageFilePreview('0')">
                                                                                <i class="fa fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- URL Input -->
                                                                <div class="upload-tab-content" id="marketImageUrlTab-0"
                                                                    style="display: none;">
                                                                    <input type="url" name="markets[0][image]"
                                                                        class="form-control"
                                                                        placeholder="https://example.com/image.jpg"
                                                                        oninput="handleMarketImageUrlInput(this, '0')">
                                                                    <div class="mt-3 market-image-url-preview-0"
                                                                        style="display: none;">
                                                                        <div class="image-preview-wrapper">
                                                                            <img src="" alt="Preview"
                                                                                class="preview-image">
                                                                            <button type="button" class="remove-preview"
                                                                                onclick="removeMarketImageUrlPreview('0')">
                                                                                <i class="fa fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Market Icon Upload -->
                                                            <div class="form-group">
                                                                <label class="form-label">Market Icon</label>
                                                                <div class="upload-option-tabs">
                                                                    <button type="button" class="upload-tab-btn active"
                                                                        data-tab="market-icon-upload-0"
                                                                        onclick="switchMarketIconTab('upload', '0')">
                                                                        <i class="fa fa-upload"></i> Upload
                                                                    </button>
                                                                    <button type="button" class="upload-tab-btn"
                                                                        data-tab="market-icon-url-0"
                                                                        onclick="switchMarketIconTab('url', '0')">
                                                                        <i class="fa fa-link"></i> URL
                                                                    </button>
                                                                </div>

                                                                <!-- File Upload -->
                                                                <div class="upload-tab-content"
                                                                    id="marketIconUploadTab-0">
                                                                    <div class="file-upload-wrapper">
                                                                        <input type="file" name="markets[0][icon_file]"
                                                                            id="marketIconFileInput-0" class="file-input"
                                                                            accept="image/*"
                                                                            onchange="handleMarketIconFileSelect(this, '0')">
                                                                        <label for="marketIconFileInput-0"
                                                                            class="file-upload-label">
                                                                            <i class="fa fa-cloud-upload-alt"></i>
                                                                            <span>Choose Icon File</span>
                                                                        </label>
                                                                    </div>
                                                                    <div class="mt-3 market-icon-file-preview-0"
                                                                        style="display: none;">
                                                                        <div class="image-preview-wrapper">
                                                                            <img src="" alt="Preview"
                                                                                class="preview-image">
                                                                            <button type="button" class="remove-preview"
                                                                                onclick="removeMarketIconFilePreview('0')">
                                                                                <i class="fa fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- URL Input -->
                                                                <div class="upload-tab-content" id="marketIconUrlTab-0"
                                                                    style="display: none;">
                                                                    <input type="url" name="markets[0][icon]"
                                                                        class="form-control"
                                                                        placeholder="https://example.com/icon.jpg"
                                                                        oninput="handleMarketIconUrlInput(this, '0')">
                                                                    <div class="mt-3 market-icon-url-preview-0"
                                                                        style="display: none;">
                                                                        <div class="image-preview-wrapper">
                                                                            <img src="" alt="Preview"
                                                                                class="preview-image">
                                                                            <button type="button" class="remove-preview"
                                                                                onclick="removeMarketIconUrlPreview('0')">
                                                                                <i class="fa fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Start Date</label>
                                                                <input type="datetime-local" name="markets[0][start_date]"
                                                                    class="form-control">
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">End Date</label>
                                                                <input type="datetime-local" name="markets[0][end_date]"
                                                                    class="form-control">
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Initial Prices</label>
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <label class="small">Yes Price</label>
                                                                        <input type="number" name="markets[0][yes_price]"
                                                                            class="form-control" step="0.001"
                                                                            min="0" max="1" value="0.5"
                                                                            placeholder="0.5">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label class="small">No Price</label>
                                                                        <input type="number" name="markets[0][no_price]"
                                                                            class="form-control" step="0.001"
                                                                            min="0" max="1" value="0.5"
                                                                            placeholder="0.5">
                                                                    </div>
                                                                </div>
                                                                <small class="form-text text-muted">Must sum to 1.0</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="form-group mt-4">
                                        <button type="button" class="btn btn-info" id="addMarketBtn">
                                            <i class="fa fa-plus"></i> Add Another Market
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Save All Markets
                                        </button>
                                        <a href="{{ route('admin.events.show', $event) }}" class="btn btn-secondary">
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
        let marketIndex = 1;

        // Add new market
        document.getElementById('addMarketBtn').addEventListener('click', function() {
            const template = document.querySelector('.market-item-template');
            const newMarket = template.cloneNode(true);
            newMarket.style.display = 'block';
            newMarket.classList.remove('market-item-template');

            // Update all inputs with new index
            newMarket.querySelectorAll('input, textarea, select, button, label, div').forEach(element => {
                // Update name attributes
                if (element.name) {
                    element.name = element.name.replace('[INDEX]', `[${marketIndex}]`);
                }
                // Update id attributes
                if (element.id) {
                    element.id = element.id.replace('INDEX', marketIndex);
                }
                // Update for attributes
                if (element.hasAttribute('for')) {
                    element.setAttribute('for', element.getAttribute('for').replace('INDEX', marketIndex));
                }
                // Update onclick attributes
                if (element.hasAttribute('onclick')) {
                    element.setAttribute('onclick', element.getAttribute('onclick').replace(/'INDEX'/g,
                        `'${marketIndex}'`));
                }
                // Update onchange attributes
                if (element.hasAttribute('onchange')) {
                    element.setAttribute('onchange', element.getAttribute('onchange').replace(/'INDEX'/g,
                        `'${marketIndex}'`));
                }
                // Update oninput attributes
                if (element.hasAttribute('oninput')) {
                    element.setAttribute('oninput', element.getAttribute('oninput').replace(/'INDEX'/g,
                        `'${marketIndex}'`));
                }
                // Update class names with INDEX
                if (element.className) {
                    element.className = element.className.replace(/INDEX/g, marketIndex);
                }
                // Update data-tab attributes
                if (element.hasAttribute('data-tab')) {
                    element.setAttribute('data-tab', element.getAttribute('data-tab').replace('INDEX',
                        marketIndex));
                }
            });

            // Update market number
            newMarket.querySelector('.market-number').textContent = marketIndex + 1;

            // Show remove button if more than one market
            if (marketIndex > 0) {
                newMarket.querySelector('.remove-market').style.display = 'block';
            }

            document.getElementById('marketsContainer').appendChild(newMarket);
            marketIndex++;

            // Update remove buttons visibility
            updateRemoveButtons();
        });

        // Remove market
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-market')) {
                const marketItem = e.target.closest('.market-item');
                marketItem.remove();
                updateRemoveButtons();
                updateMarketNumbers();
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
                    removeBtn.style.display = markets.length > 1 ? 'block' : 'none';
                }
            });
        }

        function updateMarketNumbers() {
            const markets = document.querySelectorAll('.market-item:not(.market-item-template)');
            markets.forEach((market, index) => {
                market.querySelector('.market-number').textContent = index + 1;
            });
        }

        // Validate price sum
        document.getElementById('marketsForm').addEventListener('submit', function(e) {
            const markets = document.querySelectorAll('.market-item:not(.market-item-template)');
            let isValid = true;

            markets.forEach(market => {
                const yesPrice = parseFloat(market.querySelector('input[name*="[yes_price]"]').value) || 0;
                const noPrice = parseFloat(market.querySelector('input[name*="[no_price]"]').value) || 0;
                const sum = yesPrice + noPrice;

                if (Math.abs(sum - 1.0) > 0.001) {
                    isValid = false;
                    alert('Yes and No prices must sum to 1.0 for all markets');
                    e.preventDefault();
                    return false;
                }
            });
        });

        // Market Image Tab Switching
        function switchMarketImageTab(type, index) {
            const uploadTab = document.getElementById(`marketImageUploadTab-${index}`);
            const urlTab = document.getElementById(`marketImageUrlTab-${index}`);
            const uploadBtn = document.querySelector(`[data-tab="market-image-upload-${index}"]`);
            const urlBtn = document.querySelector(`[data-tab="market-image-url-${index}"]`);

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

        // Market Icon Tab Switching
        function switchMarketIconTab(type, index) {
            const uploadTab = document.getElementById(`marketIconUploadTab-${index}`);
            const urlTab = document.getElementById(`marketIconUrlTab-${index}`);
            const uploadBtn = document.querySelector(`[data-tab="market-icon-upload-${index}"]`);
            const urlBtn = document.querySelector(`[data-tab="market-icon-url-${index}"]`);

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

        // Market Image File Upload Handler
        function handleMarketImageFileSelect(input, index) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector(`.market-image-file-preview-${index}`);
                    const previewImg = preview.querySelector('img');
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeMarketImageFilePreview(index) {
            const preview = document.querySelector(`.market-image-file-preview-${index}`);
            preview.style.display = 'none';
            const fileInput = document.getElementById(`marketImageFileInput-${index}`);
            if (fileInput) fileInput.value = '';
        }

        // Market Image URL Handler
        function handleMarketImageUrlInput(input, index) {
            const preview = document.querySelector(`.market-image-url-preview-${index}`);
            const previewImg = preview.querySelector('img');
            if (input.value) {
                previewImg.src = input.value;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }

        function removeMarketImageUrlPreview(index) {
            const preview = document.querySelector(`.market-image-url-preview-${index}`);
            preview.style.display = 'none';
            const urlInput = document.querySelector(`input[name="markets[${index}][image]"]`);
            if (urlInput) urlInput.value = '';
        }

        // Market Icon File Upload Handler
        function handleMarketIconFileSelect(input, index) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector(`.market-icon-file-preview-${index}`);
                    const previewImg = preview.querySelector('img');
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeMarketIconFilePreview(index) {
            const preview = document.querySelector(`.market-icon-file-preview-${index}`);
            preview.style.display = 'none';
            const fileInput = document.getElementById(`marketIconFileInput-${index}`);
            if (fileInput) fileInput.value = '';
        }

        // Market Icon URL Handler
        function handleMarketIconUrlInput(input, index) {
            const preview = document.querySelector(`.market-icon-url-preview-${index}`);
            const previewImg = preview.querySelector('img');
            if (input.value) {
                previewImg.src = input.value;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }

        function removeMarketIconUrlPreview(index) {
            const preview = document.querySelector(`.market-icon-url-preview-${index}`);
            preview.style.display = 'none';
            const urlInput = document.querySelector(`input[name="markets[${index}][icon]"]`);
            if (urlInput) urlInput.value = '';
        }
    </script>

    <style>
        .market-item {
            border: 1px solid #e0e0e0;
            border-radius: 4px;
        }

        .market-item .box-header {
            border-bottom: 2px solid #fff;
        }

        .form-label.required::after {
            content: '';
        }
    </style>
@endsection

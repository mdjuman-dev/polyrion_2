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
                                                                <label class="form-label">Initial Prices (Chance %)</label>
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <label class="small">Yes Chance (%)</label>
                                                                        <input type="number"
                                                                            name="markets[INDEX][yes_price_percent]"
                                                                            class="form-control yes-price-percent" step="0.1"
                                                                            min="0" max="100" value="50"
                                                                            placeholder="50.0"
                                                                            onchange="updateMarketPrices(this)">
                                                                        <input type="hidden" name="markets[INDEX][yes_price]" class="yes-price-decimal" value="0.5">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label class="small">No Chance (%)</label>
                                                                        <input type="number"
                                                                            name="markets[INDEX][no_price_percent]"
                                                                            class="form-control no-price-percent" step="0.1"
                                                                            min="0" max="100" value="50"
                                                                            placeholder="50.0"
                                                                            onchange="updateMarketPrices(this)">
                                                                        <input type="hidden" name="markets[INDEX][no_price]" class="no-price-decimal" value="0.5">
                                                                    </div>
                                                                </div>
                                                                <small class="form-text text-muted">Must sum to 100%</small>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Volume</label>
                                                                <input type="number" name="markets[INDEX][volume]"
                                                                    class="form-control" step="0.01"
                                                                    min="0" value="0"
                                                                    placeholder="0.00">
                                                                <small class="form-text text-muted">Trading volume for this market</small>
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
                                                                <label class="form-label">Initial Prices (Chance %)</label>
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <label class="small">Yes Chance (%)</label>
                                                                        <input type="number" name="markets[0][yes_price_percent]"
                                                                            class="form-control yes-price-percent" step="0.1"
                                                                            min="0" max="100" value="50"
                                                                            placeholder="50.0"
                                                                            onchange="updateMarketPrices(this)">
                                                                        <input type="hidden" name="markets[0][yes_price]" class="yes-price-decimal" value="0.5">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label class="small">No Chance (%)</label>
                                                                        <input type="number" name="markets[0][no_price_percent]"
                                                                            class="form-control no-price-percent" step="0.1"
                                                                            min="0" max="100" value="50"
                                                                            placeholder="50.0"
                                                                            onchange="updateMarketPrices(this)">
                                                                        <input type="hidden" name="markets[0][no_price]" class="no-price-decimal" value="0.5">
                                                                    </div>
                                                                </div>
                                                                <small class="form-text text-muted">Must sum to 100%</small>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Volume</label>
                                                                <input type="number" name="markets[0][volume]"
                                                                    class="form-control" step="0.01"
                                                                    min="0" value="0"
                                                                    placeholder="0.00">
                                                                <small class="form-text text-muted">Trading volume for this market</small>
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

        // Validate price sum and handle form submission with AJAX
        document.getElementById('marketsForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Always prevent default to handle via AJAX
            
            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            const markets = document.querySelectorAll('.market-item:not(.market-item-template)');
            let isValid = true;

            // Client-side validation first
            markets.forEach(market => {
                const yesPercentInput = market.querySelector('.yes-price-percent');
                const noPercentInput = market.querySelector('.no-price-percent');
                
                if (yesPercentInput && noPercentInput) {
                    const yesPercent = parseFloat(yesPercentInput.value) || 0;
                    const noPercent = parseFloat(noPercentInput.value) || 0;
                    const sum = yesPercent + noPercent;

                    if (Math.abs(sum - 100.0) > 0.1) {
                        isValid = false;
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Prices',
                                text: 'Yes and No chances must sum to 100% for all markets. Current sum: ' + sum.toFixed(1) + '%',
                                confirmButtonColor: '#ffb11a'
                            });
                        } else {
                            alert('Yes and No chances must sum to 100% for all markets. Current sum: ' + sum.toFixed(1) + '%');
                        }
                        return false;
                    }
                }
            });

            if (!isValid) return;

            // Show loading state
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving Markets...';

            // Clear previous errors
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            // Prepare FormData
            const formData = new FormData(form);

            // Submit via AJAX
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw data;
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Markets added successfully!',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            window.location.href = data.redirect || '{{ route("admin.events.show", $event) }}';
                        });
                    } else {
                        window.location.href = data.redirect || '{{ route("admin.events.show", $event) }}';
                    }
                } else {
                    throw data;
                }
            })
            .catch(error => {
                console.error('Validation errors:', error);
                
                // Restore button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;

                // Handle validation errors
                if (error.errors) {
                    let firstErrorElement = null;
                    
                    // Show each error on its field
                    Object.keys(error.errors).forEach(field => {
                        const errors = Array.isArray(error.errors[field]) ? error.errors[field] : [error.errors[field]];
                        
                        // Handle nested field names like markets.0.question
                        if (field.includes('.')) {
                            const parts = field.split('.');
                            if (parts[0] === 'markets' && parts.length >= 3) {
                                const marketIndex = parseInt(parts[1]);
                                const fieldName = parts[2];
                                const markets = document.querySelectorAll('.market-item:not(.market-item-template)');
                                
                                if (markets[marketIndex]) {
                                    // Try multiple selectors to find the input
                                    let input = markets[marketIndex].querySelector(`[name="markets[${marketIndex}][${fieldName}]"]`) ||
                                               markets[marketIndex].querySelector(`[name*="[${fieldName}]"]`) ||
                                               markets[marketIndex].querySelector(`.${fieldName}`);
                                    
                                    // Special handling for specific fields
                                    if (!input) {
                                        if (fieldName === 'question') {
                                            input = markets[marketIndex].querySelector('.market-question') ||
                                                   markets[marketIndex].querySelector('input[placeholder*="question"]');
                                        } else if (fieldName === 'yes_price' || fieldName === 'no_price') {
                                            input = markets[marketIndex].querySelector(`.${fieldName.replace('_', '-')}-percent`);
                                        } else if (fieldName === 'volume') {
                                            input = markets[marketIndex].querySelector('input[name*="volume"]');
                                        }
                                    }
                                    
                                    if (input) {
                                        // Add red border and styling
                                        input.classList.add('is-invalid');
                                        input.style.borderColor = '#ff6b6b';
                                        input.style.backgroundColor = '#fff5f5';
                                        
                                        // Remove existing error
                                        const existingError = input.parentElement.querySelector('.invalid-feedback');
                                        if (existingError) existingError.remove();
                                        
                                        // Add error message
                                        const errorDiv = document.createElement('div');
                                        errorDiv.className = 'invalid-feedback';
                                        errorDiv.style.display = 'block';
                                        errorDiv.style.color = '#ff6b6b';
                                        errorDiv.style.fontSize = '0.875rem';
                                        errorDiv.style.marginTop = '0.25rem';
                                        errorDiv.style.fontWeight = '500';
                                        errorDiv.innerHTML = `<i class="fa fa-exclamation-circle"></i> ${errors[0]}`;
                                        input.parentElement.appendChild(errorDiv);
                                        
                                        // Save first error element
                                        if (!firstErrorElement) {
                                            firstErrorElement = input;
                                        }
                                        
                                        // Highlight market box
                                        const marketBox = markets[marketIndex].closest('.market-item, .box');
                                        if (marketBox) {
                                            marketBox.style.borderColor = '#ff6b6b';
                                            marketBox.style.borderWidth = '2px';
                                            marketBox.style.boxShadow = '0 0 0 3px rgba(255, 107, 107, 0.1)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                    
                    // Scroll to first error
                    if (firstErrorElement) {
                        setTimeout(() => {
                            firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstErrorElement.focus();
                            firstErrorElement.style.animation = 'pulse 0.5s ease-in-out 3';
                        }, 100);
                    }
                }
                
                if (error.message && typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: error.message,
                        confirmButtonColor: '#ff6b6b'
                    });
                }
            });
        });

        // Update market prices - convert percentage to decimal
        function updateMarketPrices(input) {
            const marketItem = input.closest('.market-item, .box');
            const yesPercentInput = marketItem.querySelector('.yes-price-percent');
            const noPercentInput = marketItem.querySelector('.no-price-percent');
            const yesDecimalInput = marketItem.querySelector('.yes-price-decimal');
            const noDecimalInput = marketItem.querySelector('.no-price-decimal');

            if (yesPercentInput && noPercentInput && yesDecimalInput && noDecimalInput) {
                const yesPercent = parseFloat(yesPercentInput.value) || 0;
                const noPercent = parseFloat(noPercentInput.value) || 0;

                // Auto-adjust the other value if this one changes
                if (input.classList.contains('yes-price-percent')) {
                    noPercentInput.value = (100.0 - yesPercent).toFixed(1);
                } else if (input.classList.contains('no-price-percent')) {
                    yesPercentInput.value = (100.0 - noPercent).toFixed(1);
                }

                // Update hidden decimal fields
                const finalYesPercent = parseFloat(yesPercentInput.value) || 0;
                const finalNoPercent = parseFloat(noPercentInput.value) || 0;
                
                yesDecimalInput.value = (finalYesPercent / 100).toFixed(3);
                noDecimalInput.value = (finalNoPercent / 100).toFixed(3);
            }
        }

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



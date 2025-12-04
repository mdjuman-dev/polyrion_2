@extends('backend.layouts.master')
@section('title', 'Add Markets to Event')
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
                                <form action="{{ route('admin.events.store-markets', $event) }}" method="POST" id="marketsForm">
                                    @csrf
                                    
                                    <div id="marketsContainer">
                                        <!-- Market Template (Hidden) -->
                                        <div class="market-item-template" style="display: none;">
                                            <div class="box market-item mb-3">
                                                <div class="box-header with-border bg-primary">
                                                    <h5 class="box-title text-white">
                                                        <i class="fa fa-chart-line"></i> Market <span class="market-number">1</span>
                                                        <button type="button" class="btn btn-sm btn-danger float-right remove-market" style="display: none;">
                                                            <i class="fa fa-times"></i> Remove
                                                        </button>
                                                    </h5>
                                                </div>
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <label class="form-label required">Market Question <span class="text-danger">*</span></label>
                                                                <input type="text" 
                                                                       name="markets[INDEX][question]" 
                                                                       class="form-control market-question" 
                                                                       placeholder="e.g., Tim Cook out as Apple CEO in 2025?"
                                                                       required>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Description</label>
                                                                <textarea name="markets[INDEX][description]" 
                                                                          class="form-control" 
                                                                          rows="3" 
                                                                          placeholder="Market description..."></textarea>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Slug</label>
                                                                <input type="text" 
                                                                       name="markets[INDEX][slug]" 
                                                                       class="form-control market-slug" 
                                                                       placeholder="Auto-generated from question">
                                                                <small class="form-text text-muted">Auto-generated if left empty</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="form-label">Market Image URL</label>
                                                                <input type="url" 
                                                                       name="markets[INDEX][image]" 
                                                                       class="form-control" 
                                                                       placeholder="https://example.com/image.jpg">
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Market Icon URL</label>
                                                                <input type="url" 
                                                                       name="markets[INDEX][icon]" 
                                                                       class="form-control" 
                                                                       placeholder="https://example.com/icon.jpg">
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
                                                                       name="markets[INDEX][end_date]" 
                                                                       class="form-control">
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Initial Prices</label>
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <label class="small">Yes Price</label>
                                                                        <input type="number" 
                                                                               name="markets[INDEX][yes_price]" 
                                                                               class="form-control" 
                                                                               step="0.001" 
                                                                               min="0" 
                                                                               max="1" 
                                                                               value="0.5"
                                                                               placeholder="0.5">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label class="small">No Price</label>
                                                                        <input type="number" 
                                                                               name="markets[INDEX][no_price]" 
                                                                               class="form-control" 
                                                                               step="0.001" 
                                                                               min="0" 
                                                                               max="1" 
                                                                               value="0.5"
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
                                                                <label class="form-label required">Market Question <span class="text-danger">*</span></label>
                                                                <input type="text" 
                                                                       name="markets[0][question]" 
                                                                       class="form-control market-question" 
                                                                       placeholder="e.g., Tim Cook out as Apple CEO in 2025?"
                                                                       required>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Description</label>
                                                                <textarea name="markets[0][description]" 
                                                                          class="form-control" 
                                                                          rows="3" 
                                                                          placeholder="Market description..."></textarea>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Slug</label>
                                                                <input type="text" 
                                                                       name="markets[0][slug]" 
                                                                       class="form-control market-slug" 
                                                                       placeholder="Auto-generated from question">
                                                                <small class="form-text text-muted">Auto-generated if left empty</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="form-label">Market Image URL</label>
                                                                <input type="url" 
                                                                       name="markets[0][image]" 
                                                                       class="form-control" 
                                                                       placeholder="https://example.com/image.jpg">
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Market Icon URL</label>
                                                                <input type="url" 
                                                                       name="markets[0][icon]" 
                                                                       class="form-control" 
                                                                       placeholder="https://example.com/icon.jpg">
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Start Date</label>
                                                                <input type="datetime-local" 
                                                                       name="markets[0][start_date]" 
                                                                       class="form-control">
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">End Date</label>
                                                                <input type="datetime-local" 
                                                                       name="markets[0][end_date]" 
                                                                       class="form-control">
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="form-label">Initial Prices</label>
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <label class="small">Yes Price</label>
                                                                        <input type="number" 
                                                                               name="markets[0][yes_price]" 
                                                                               class="form-control" 
                                                                               step="0.001" 
                                                                               min="0" 
                                                                               max="1" 
                                                                               value="0.5"
                                                                               placeholder="0.5">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label class="small">No Price</label>
                                                                        <input type="number" 
                                                                               name="markets[0][no_price]" 
                                                                               class="form-control" 
                                                                               step="0.001" 
                                                                               min="0" 
                                                                               max="1" 
                                                                               value="0.5"
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
            newMarket.querySelectorAll('input, textarea, select').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace('[INDEX]', `[${marketIndex}]`);
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



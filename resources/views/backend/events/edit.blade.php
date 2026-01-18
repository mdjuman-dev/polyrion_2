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
                                    <i class="fa fa-edit"></i> Edit Event & Markets
                                </h4>
                            </div>
                            <div class="box-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <strong>Please fix the following errors:</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ route('admin.events.update', $event) }}" method="POST"
                                    enctype="multipart/form-data" id="eventForm">
                                    @csrf
                                    @method('PUT')

                                    <!-- Event Details Section -->
                                    <div class="card mb-4">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0">
                                                <i class="fa fa-calendar-alt"></i> Event Details
                                            </h5>
                                        </div>
                                        <div class="card-body">
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
                                                    </div>
                                                </div>

                                                <!-- Right Column - Event Info -->
                                                <div class="col-md-4">
                                                    <div class="box" style="background: #f8f9fa; border: 1px solid #e9ecef;">
                                                        <div class="box-body">
                                                            <h5 class="mb-3">
                                                                <i class="fa fa-info-circle"></i> Event Information
                                                            </h5>
                                                            <div class="info-item mb-2">
                                                                <strong>Slug:</strong>
                                                                <code>{{ $event->slug }}</code>
                                                            </div>
                                                            <div class="info-item mb-2">
                                                                <strong>Markets:</strong>
                                                                <span class="badge badge-info">{{ $event->markets->count() }}</span>
                                                            </div>
                                                            <div class="info-item">
                                                                <strong>Created:</strong>
                                                                <br><small>{{ $event->created_at->format('d M Y, H:i') }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Markets Section -->
                                    @if ($event->markets->count() > 0)
                                        <div class="card mb-4">
                                            <div class="card-header bg-success text-white">
                                                <h5 class="mb-0">
                                                    <i class="fa fa-chart-line"></i> Markets ({{ $event->markets->count() }})
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <div id="marketsContainer">
                                                    @foreach ($event->markets as $index => $market)
                                                        @php
                                                            $outcomePrices = is_array($market->outcome_prices) ? $market->outcome_prices : json_decode($market->outcome_prices, true);
                                                            $yesPrice = isset($outcomePrices[1]) ? floatval($outcomePrices[1]) * 100 : 50;
                                                            $noPrice = isset($outcomePrices[0]) ? floatval($outcomePrices[0]) * 100 : 50;
                                                        @endphp
                                                        <div class="market-card mb-3">
                                                            <div class="market-header">
                                                                <h6 class="mb-0">
                                                                    <i class="fa fa-chart-bar"></i> Market {{ $index + 1 }}
                                                                    @if (!$market->active)
                                                                        <span class="badge badge-secondary ml-2">Inactive</span>
                                                                    @endif
                                                                    @if ($market->closed)
                                                                        <span class="badge badge-danger ml-2">Closed</span>
                                                                    @endif
                                                                </h6>
                                                            </div>
                                                            <div class="market-body">
                                                                <input type="hidden" name="markets[{{ $index }}][id]"
                                                                    value="{{ $market->id }}">

                                                                <div class="row">
                                                                    <div class="col-md-8">
                                                                        <!-- Market Question -->
                                                                        <div class="form-group">
                                                                            <label class="form-label required">Market Question <span
                                                                                    class="text-danger">*</span></label>
                                                                            <input type="text"
                                                                                name="markets[{{ $index }}][question]"
                                                                                class="form-control @error('markets.' . $index . '.question') is-invalid @enderror"
                                                                                value="{{ old('markets.' . $index . '.question', $market->question) }}"
                                                                                required>
                                                                            @error('markets.' . $index . '.question')
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>

                                                                        <!-- Description -->
                                                                        <div class="form-group">
                                                                            <label class="form-label">Description</label>
                                                                            <textarea name="markets[{{ $index }}][description]" class="form-control" rows="2">{{ old('markets.' . $index . '.description', $market->description) }}</textarea>
                                                                        </div>

                                                                        <!-- Market Info -->
                                                                        <div class="form-group">
                                                                            <small class="text-muted">
                                                                                <strong>Slug:</strong> <code>{{ $market->slug }}</code>
                                                                            </small>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <!-- Volume -->
                                                                        <div class="form-group">
                                                                            <label class="form-label">Volume</label>
                                                                            <input type="number"
                                                                                name="markets[{{ $index }}][volume]"
                                                                                class="form-control"
                                                                                value="{{ old('markets.' . $index . '.volume', $market->volume ?? 0) }}"
                                                                                step="0.01" min="0">
                                                                            <small class="form-text text-muted">Trading volume</small>
                                                                        </div>

                                                                        <!-- Outcome Prices -->
                                                                        <div class="form-group">
                                                                            <label class="form-label">Outcome Prices</label>
                                                                            <div class="row">
                                                                                <div class="col-6">
                                                                                    <label class="small">Yes (%)</label>
                                                                                    <input type="number"
                                                                                        name="markets[{{ $index }}][yes_price_percent]"
                                                                                        class="form-control yes-price-percent"
                                                                                        value="{{ old('markets.' . $index . '.yes_price_percent', $yesPrice) }}"
                                                                                        step="0.1" min="0" max="100"
                                                                                        onchange="updateMarketPricesInline(this, {{ $index }})">
                                                                                    <input type="hidden"
                                                                                        name="markets[{{ $index }}][yes_price]"
                                                                                        class="yes-price-decimal"
                                                                                        value="{{ $yesPrice / 100 }}">
                                                                                </div>
                                                                                <div class="col-6">
                                                                                    <label class="small">No (%)</label>
                                                                                    <input type="number"
                                                                                        name="markets[{{ $index }}][no_price_percent]"
                                                                                        class="form-control no-price-percent"
                                                                                        value="{{ old('markets.' . $index . '.no_price_percent', $noPrice) }}"
                                                                                        step="0.1" min="0" max="100"
                                                                                        onchange="updateMarketPricesInline(this, {{ $index }})">
                                                                                    <input type="hidden"
                                                                                        name="markets[{{ $index }}][no_price]"
                                                                                        class="no-price-decimal"
                                                                                        value="{{ $noPrice / 100 }}">
                                                                                </div>
                                                                            </div>
                                                                            <small class="form-text text-muted">Must sum to 100%</small>
                                                                        </div>

                                                                        <!-- Status Checkboxes -->
                                                                        <div class="form-group">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="hidden"
                                                                                    name="markets[{{ $index }}][active]"
                                                                                    value="0">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input"
                                                                                    id="market_active_{{ $index }}"
                                                                                    name="markets[{{ $index }}][active]"
                                                                                    value="1"
                                                                                    {{ old('markets.' . $index . '.active', $market->active) ? 'checked' : '' }}>
                                                                                <label class="custom-control-label"
                                                                                    for="market_active_{{ $index }}">
                                                                                    Active
                                                                                </label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="hidden"
                                                                                    name="markets[{{ $index }}][closed]"
                                                                                    value="0">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input"
                                                                                    id="market_closed_{{ $index }}"
                                                                                    name="markets[{{ $index }}][closed]"
                                                                                    value="1"
                                                                                    {{ old('markets.' . $index . '.closed', $market->closed) ? 'checked' : '' }}>
                                                                                <label class="custom-control-label"
                                                                                    for="market_closed_{{ $index }}">
                                                                                    Closed
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Action Buttons -->
                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fa fa-save"></i> Update Event & Markets
                                        </button>
                                        <a href="{{ route('admin.events.show', $event) }}" class="btn btn-info btn-lg">
                                            <i class="fa fa-eye"></i> View Event
                                        </a>
                                        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary btn-lg">
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
        // Update market prices - convert percentage to decimal
        function updateMarketPricesInline(input, index) {
            const parentRow = input.closest('.row');
            const yesPercentInput = parentRow.querySelector('.yes-price-percent');
            const noPercentInput = parentRow.querySelector('.no-price-percent');
            const yesDecimalInput = parentRow.querySelector('.yes-price-decimal');
            const noDecimalInput = parentRow.querySelector('.no-price-decimal');

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

        // Form validation before submit
        document.getElementById('eventForm').addEventListener('submit', function(e) {
            const markets = document.querySelectorAll('.market-card');
            let isValid = true;

            markets.forEach((market, index) => {
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
                                text: `Market ${index + 1}: Yes and No chances must sum to 100%. Current sum: ${sum.toFixed(1)}%`,
                                confirmButtonColor: '#ffb11a'
                            });
                        } else {
                            alert(`Market ${index + 1}: Yes and No chances must sum to 100%. Current sum: ${sum.toFixed(1)}%`);
                        }
                        e.preventDefault();
                        return false;
                    }
                }
            });
        });
    </script>

    <style>
        .market-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .market-card:hover {
            border-color: #28a745;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .market-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 15px 20px;
            border-bottom: 2px solid #20c997;
        }

        .market-body {
            padding: 20px;
            background: #fff;
        }

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

        .card-header {
            border-radius: 8px 8px 0 0 !important;
        }

        .custom-control-label {
            cursor: pointer;
        }
    </style>
@endsection

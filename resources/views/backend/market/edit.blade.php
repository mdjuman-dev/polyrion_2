@extends('backend.layouts.master')
@section('title', 'Edit Market')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <!-- Back Button -->
                <div class="row mb-3">
                    <div class="col-12">
                        <a href="{{ route('admin.market.show', $market->id) }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Market
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    <i class="fa fa-edit"></i> Edit Market
                                </h4>
                            </div>
                            <div class="box-body">
                                <form action="{{ route('admin.market.update', $market->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="question">Question <span class="text-danger">*</span></label>
                                                <input type="text" 
                                                    class="form-control @error('question') is-invalid @enderror" 
                                                    id="question" 
                                                    name="question" 
                                                    value="{{ old('question', $market->question) }}" 
                                                    required>
                                                @error('question')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                                    id="description" 
                                                    name="description" 
                                                    rows="4">{{ old('description', $market->description) }}</textarea>
                                                @error('description')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="event_id">Event</label>
                                                <select class="form-control @error('event_id') is-invalid @enderror" 
                                                    id="event_id" 
                                                    name="event_id">
                                                    <option value="">Select Event</option>
                                                    @foreach($events as $event)
                                                        <option value="{{ $event->id }}" 
                                                            {{ old('event_id', $market->event_id) == $event->id ? 'selected' : '' }}>
                                                            {{ $event->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('event_id')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="volume">Volume</label>
                                                <input type="number" 
                                                    class="form-control @error('volume') is-invalid @enderror" 
                                                    id="volume" 
                                                    name="volume" 
                                                    value="{{ old('volume', $market->volume ?? 0) }}" 
                                                    step="0.01"
                                                    min="0"
                                                    placeholder="0.00">
                                                @error('volume')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">Trading volume for this market</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <h5 class="mt-3 mb-3">
                                                <i class="fa fa-percentage"></i> Outcome Prices (Chance/Probability)
                                            </h5>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="yes_price">Yes Chance (%)</label>
                                                <input type="number" 
                                                    class="form-control @error('yes_price') is-invalid @enderror" 
                                                    id="yes_price" 
                                                    name="yes_price" 
                                                    value="{{ old('yes_price', isset($outcomePrices[1]) ? ($outcomePrices[1] * 100) : 50) }}" 
                                                    step="0.1"
                                                    min="0"
                                                    max="100"
                                                    placeholder="50.0"
                                                    oninput="updateNoPrice()">
                                                @error('yes_price')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">Probability of Yes outcome (0 - 100%)</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="no_price">No Chance (%)</label>
                                                <input type="number" 
                                                    class="form-control @error('no_price') is-invalid @enderror" 
                                                    id="no_price" 
                                                    name="no_price" 
                                                    value="{{ old('no_price', isset($outcomePrices[0]) ? ($outcomePrices[0] * 100) : 50) }}" 
                                                    step="0.1"
                                                    min="0"
                                                    max="100"
                                                    placeholder="50.0"
                                                    oninput="updateYesPrice()">
                                                @error('no_price')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">Probability of No outcome (0 - 100%)</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> <strong>Note:</strong> Yes Chance + No Chance must equal 100%
                                                <br>
                                                <span id="priceSum"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="checkbox">
                                                    <input type="hidden" name="active" value="0">
                                                    <input type="checkbox" 
                                                        id="active" 
                                                        name="active" 
                                                        value="1" 
                                                        {{ old('active', $market->active) ? 'checked' : '' }}>
                                                    <label for="active">Active</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="checkbox">
                                                    <input type="hidden" name="closed" value="0">
                                                    <input type="checkbox" 
                                                        id="closed" 
                                                        name="closed" 
                                                        value="1" 
                                                        {{ old('closed', $market->closed) ? 'checked' : '' }}>
                                                    <label for="closed">Closed</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="checkbox">
                                                    <input type="hidden" name="featured" value="0">
                                                    <input type="checkbox" 
                                                        id="featured" 
                                                        name="featured" 
                                                        value="1" 
                                                        {{ old('featured', $market->featured) ? 'checked' : '' }}>
                                                    <label for="featured">Featured</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-save"></i> Update Market
                                            </button>
                                            <a href="{{ route('admin.market.show', $market->id) }}" class="btn btn-secondary">
                                                Cancel
                                            </a>
                                        </div>
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
        // Auto-calculate prices to ensure they sum to 100%
        function updateNoPrice() {
            const yesPrice = parseFloat(document.getElementById('yes_price').value) || 0;
            const noPrice = (100.0 - yesPrice).toFixed(1);
            document.getElementById('no_price').value = noPrice;
            updatePriceSum();
        }

        function updateYesPrice() {
            const noPrice = parseFloat(document.getElementById('no_price').value) || 0;
            const yesPrice = (100.0 - noPrice).toFixed(1);
            document.getElementById('yes_price').value = yesPrice;
            updatePriceSum();
        }

        function updatePriceSum() {
            const yesPrice = parseFloat(document.getElementById('yes_price').value) || 0;
            const noPrice = parseFloat(document.getElementById('no_price').value) || 0;
            const sum = (yesPrice + noPrice).toFixed(1);
            const sumDisplay = document.getElementById('priceSum');
            
            if (Math.abs(sum - 100.0) < 0.1) {
                sumDisplay.innerHTML = '<strong style="color: green;">✓ Total: ' + sum + '% (Valid)</strong>';
            } else {
                sumDisplay.innerHTML = '<strong style="color: red;">✗ Total: ' + sum + '% (Must be 100%)</strong>';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updatePriceSum();
        });

        // Form validation and conversion before submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const yesPrice = parseFloat(document.getElementById('yes_price').value) || 0;
            const noPrice = parseFloat(document.getElementById('no_price').value) || 0;
            const sum = yesPrice + noPrice;

            if (Math.abs(sum - 100.0) > 0.1) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Prices',
                        text: 'Yes and No chances must sum to 100%. Current sum: ' + sum.toFixed(1) + '%',
                        confirmButtonColor: '#ffb11a'
                    });
                } else {
                    alert('Yes and No chances must sum to 100%. Current sum: ' + sum.toFixed(1) + '%');
                }
                return false;
            }

            // Convert percentage to decimal before sending to backend
            // Create hidden inputs with decimal values
            const form = this;
            const yesPriceDecimal = (yesPrice / 100).toFixed(3);
            const noPriceDecimal = (noPrice / 100).toFixed(3);

            // Remove existing hidden inputs if any
            const existingYesHidden = form.querySelector('input[name="yes_price_decimal"]');
            const existingNoHidden = form.querySelector('input[name="no_price_decimal"]');
            if (existingYesHidden) existingYesHidden.remove();
            if (existingNoHidden) existingNoHidden.remove();

            // Change the name of percentage inputs temporarily
            const yesPriceInput = document.getElementById('yes_price');
            const noPriceInput = document.getElementById('no_price');
            
            yesPriceInput.name = 'yes_price_percent';
            noPriceInput.name = 'no_price_percent';

            // Create hidden inputs with decimal values
            const yesHidden = document.createElement('input');
            yesHidden.type = 'hidden';
            yesHidden.name = 'yes_price';
            yesHidden.value = yesPriceDecimal;
            form.appendChild(yesHidden);

            const noHidden = document.createElement('input');
            noHidden.type = 'hidden';
            noHidden.name = 'no_price';
            noHidden.value = noPriceDecimal;
            form.appendChild(noHidden);
        });
    </script>
@endsection


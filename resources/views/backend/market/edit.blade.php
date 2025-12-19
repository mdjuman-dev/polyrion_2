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
@endsection


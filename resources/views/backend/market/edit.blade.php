@extends('backend.layouts.master')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <!-- Header Section -->
                        <div class="box edit-header-box">
                            <div class="box-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h2 class="edit-title">
                                            <i class="fa fa-edit"></i> Edit Market
                                        </h2>
                                        <p class="edit-subtitle">Update market information and settings</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="{{ route('admin.market.index') }}" class="btn btn-light">
                                            <i class="fa fa-arrow-left"></i> Back to List
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ isset($data->id) ? route('admin.market.update', $data->id) : route('admin.market.store') }}" method="post" enctype="multipart/form-data"
                            id="marketEditForm">
                            @csrf
                            @if(isset($data->id))
                                @method('PUT')
                                <input type="hidden" name="event_id" value="{{ $data->id }}">
                            @endif

                            <!-- Main Market Information -->
                            <div class="box modern-card">
                                <div class="box-header with-border card-header-modern" data-toggle="collapse"
                                    data-target="#mainInfo">
                                    <h4 class="box-title">
                                        <i class="fa fa-info-circle"></i> Main Information
                                        <i class="fa fa-chevron-down float-right toggle-icon"></i>
                                    </h4>
                                </div>
                                <div class="box-body collapse show" id="mainInfo">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group modern-form-group">
                                                <label for="title" class="modern-label">
                                                    <i class="fa fa-heading"></i> Title
                                                </label>
                                                <input type="text" name="title" id="title"
                                                    class="form-control modern-input" value="{{ $data->title }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group modern-form-group">
                                                <label for="slug" class="modern-label">
                                                    <i class="fa fa-"></i> Slug
                                                </label>
                                                <input type="text" name="slug" id="slug"
                                                    class="form-control modern-input" value="{{ $data->slug }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group modern-form-group">
                                                <label for="description" class="modern-label">
                                                    <i class="fa fa-align-left"></i> Description
                                                </label>
                                                <textarea name="description" id="description" rows="4" class="form-control modern-input">{{ $data->description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group modern-form-group">
                                                <label for="image" class="modern-label">
                                                    <i class="fa fa-image"></i> Main Image
                                                </label>
                                                <div class="file-upload-wrapper">
                                                    <input type="file" name="image" id="image"
                                                        class="form-control modern-input file-input" accept="image/*">
                                                    @if (!empty($data->image))
                                                        <input type="hidden" name="existing_image"
                                                            value="{{ $data->image }}">
                                                    @endif
                                                    <div class="image-preview mt-2">
                                                        <img id="imagePreview" src="{{ $data->image ?? '' }}" alt="Preview"
                                                            class="preview-image"
                                                            style="{{ empty($data->image) ? 'display:none;' : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group modern-form-group">
                                                <label for="icon" class="modern-label">
                                                    <i class="fa fa-icons"></i> Icon
                                                </label>
                                                <div class="file-upload-wrapper">
                                                    <input type="file" name="icon" id="icon"
                                                        class="form-control modern-input file-input" accept="image/*">
                                                    @if (!empty($data->icon))
                                                        <input type="hidden" name="existing_icon"
                                                            value="{{ $data->icon }}">
                                                    @endif
                                                    <div class="image-preview mt-2">
                                                        <img id="iconPreview" src="{{ $data->icon ?? '' }}"
                                                            alt="Icon Preview" class="preview-image"
                                                            style="{{ empty($data->icon) ? 'display:none;' : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group modern-form-group">
                                                <label for="start_date" class="modern-label">
                                                    <i class="fa fa-calendar-check"></i> Start Date
                                                </label>
                                                <input type="date" name="start_date" id="start_date"
                                                    class="form-control modern-input"
                                                    value="{{ $data->start_date ?? ($data->startDate ?? null) ? \Carbon\Carbon::parse($data->start_date ?? $data->startDate)->format('Y-m-d') : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group modern-form-group">
                                                <label for="end_date" class="modern-label">
                                                    <i class="fa fa-calendar-times"></i> End Date
                                                </label>
                                                <input type="date" name="end_date" id="end_date"
                                                    class="form-control modern-input"
                                                    value="{{ $data->end_date ?? ($data->endDate ?? null) ? \Carbon\Carbon::parse($data->end_date ?? $data->endDate)->format('Y-m-d') : '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Metrics -->
                            <div class="box modern-card">
                                <div class="box-header with-border card-header-modern" data-toggle="collapse"
                                    data-target="#financialMetrics">
                                    <h4 class="box-title">
                                        <i class="fa fa-chart-line"></i> Financial Metrics
                                        <i class="fa fa-chevron-down float-right toggle-icon"></i>
                                    </h4>
                                </div>
                                <div class="box-body collapse show" id="financialMetrics">
                                    <div class="row">
                                        <div class="col-md-6 col-lg-3">
                                            <div class="form-group modern-form-group">
                                                <label for="liquidity" class="modern-label">
                                                    <i class="fa fa-dollar-sign"></i> Liquidity
                                                </label>
                                                <input type="number" step="0.01" name="liquidity" id="liquidity"
                                                    class="form-control modern-input"
                                                    value="{{ $data->liquidity ?? 0 }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="form-group modern-form-group">
                                                <label for="liquidity_clob" class="modern-label">
                                                    <i class="fa fa-dollar-sign"></i> Liquidity CLOB
                                                </label>
                                                <input type="number" step="0.01" name="liquidity_clob"
                                                    id="liquidity_clob" class="form-control modern-input"
                                                    value="{{ $data->liquidity_clob ?? ($data->liquidityClob ?? 0) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="form-group modern-form-group">
                                                <label for="volume" class="modern-label">
                                                    <i class="fa fa-chart-bar"></i> Volume
                                                </label>
                                                <input type="number" step="0.01" name="volume" id="volume"
                                                    class="form-control modern-input" value="{{ $data->volume ?? 0 }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="form-group modern-form-group">
                                                <label for="volume_24hr" class="modern-label">
                                                    <i class="fa fa-clock"></i> Volume 24hr
                                                </label>
                                                <input type="number" step="0.01" name="volume_24hr" id="volume_24hr"
                                                    class="form-control modern-input"
                                                    value="{{ $data->volume_24hr ?? ($data->volume24hr ?? 0) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="form-group modern-form-group">
                                                <label for="volume_1wk" class="modern-label">
                                                    <i class="fa fa-calendar-week"></i> Volume 1wk
                                                </label>
                                                <input type="number" step="0.01" name="volume_1wk" id="volume_1wk"
                                                    class="form-control modern-input"
                                                    value="{{ $data->volume_1wk ?? ($data->volume1wk ?? 0) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="form-group modern-form-group">
                                                <label for="volume_1mo" class="modern-label">
                                                    <i class="fa fa-calendar-alt"></i> Volume 1mo
                                                </label>
                                                <input type="number" step="0.01" name="volume_1mo" id="volume_1mo"
                                                    class="form-control modern-input"
                                                    value="{{ $data->volume_1mo ?? ($data->volume1mo ?? 0) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="form-group modern-form-group">
                                                <label for="volume_1yr" class="modern-label">
                                                    <i class="fa fa-calendar"></i> Volume 1yr
                                                </label>
                                                <input type="number" step="0.01" name="volume_1yr" id="volume_1yr"
                                                    class="form-control modern-input"
                                                    value="{{ $data->volume_1yr ?? ($data->volume1yr ?? 0) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="form-group modern-form-group">
                                                <label for="competitive" class="modern-label">
                                                    <i class="fa fa-trophy"></i> Competitive
                                                </label>
                                                <input type="number" step="0.00000001" name="competitive"
                                                    id="competitive" class="form-control modern-input"
                                                    value="{{ $data->competitive ?? 0 }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Settings -->
                            <div class="box modern-card">
                                <div class="box-header with-border card-header-modern" data-toggle="collapse"
                                    data-target="#statusSettings">
                                    <h4 class="box-title">
                                        <i class="fa fa-toggle-on"></i> Status Settings
                                        <i class="fa fa-chevron-down float-right toggle-icon"></i>
                                    </h4>
                                </div>
                                <div class="box-body collapse show" id="statusSettings">
                                    <div class="row">
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group modern-form-group">
                                                <label for="active" class="modern-label">
                                                    <i class="fa fa-power-off"></i> Active
                                                </label>
                                                <select name="active" id="active" class="form-control modern-select">
                                                    <option value="1"
                                                        {{ isset($data->active) && $data->active ? 'selected' : '' }}>
                                                        Active
                                                    </option>
                                                    <option value="0"
                                                        {{ !isset($data->active) || !$data->active ? 'selected' : '' }}>
                                                        Inactive
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group modern-form-group">
                                                <label for="closed" class="modern-label">
                                                    <i class="fa fa-lock"></i> Closed
                                                </label>
                                                <select name="closed" id="closed" class="form-control modern-select">
                                                    <option value="1"
                                                        {{ isset($data->closed) && $data->closed ? 'selected' : '' }}>
                                                        Closed
                                                    </option>
                                                    <option value="0"
                                                        {{ !isset($data->closed) || !$data->closed ? 'selected' : '' }}>
                                                        Open
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group modern-form-group">
                                                <label for="featured" class="modern-label">
                                                    <i class="fa fa-star"></i> Featured
                                                </label>
                                                <select name="featured" id="featured"
                                                    class="form-control modern-select">
                                                    <option value="1"
                                                        {{ isset($data->featured) && $data->featured ? 'selected' : '' }}>
                                                        Featured
                                                    </option>
                                                    <option value="0"
                                                        {{ !isset($data->featured) || !$data->featured ? 'selected' : '' }}>
                                                        Not Featured
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group modern-form-group">
                                                <label for="new" class="modern-label">
                                                    <i class="fa fa-certificate"></i> New
                                                </label>
                                                <select name="new" id="new" class="form-control modern-select">
                                                    <option value="1"
                                                        {{ isset($data->new) && $data->new ? 'selected' : '' }}>New
                                                    </option>
                                                    <option value="0"
                                                        {{ !isset($data->new) || !$data->new ? 'selected' : '' }}>Not New
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group modern-form-group">
                                                <label for="restricted" class="modern-label">
                                                    <i class="fa fa-ban"></i> Restricted
                                                </label>
                                                <select name="restricted" id="restricted"
                                                    class="form-control modern-select">
                                                    <option value="1"
                                                        {{ isset($data->restricted) && $data->restricted ? 'selected' : '' }}>
                                                        Restricted
                                                    </option>
                                                    <option value="0"
                                                        {{ !isset($data->restricted) || !$data->restricted ? 'selected' : '' }}>
                                                        Not Restricted
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        @if (isset($data->archived) && $data->archived)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group modern-form-group">
                                                    <label for="archived" class="modern-label">
                                                        <i class="fa fa-archive"></i> Archived
                                                    </label>
                                                    <select name="archived" id="archived"
                                                        class="form-control modern-select">
                                                        <option value="1"
                                                            {{ isset($data->archived) && $data->archived ? 'selected' : '' }}>
                                                            Archived
                                                        </option>
                                                        <option value="0"
                                                            {{ !isset($data->archived) || !$data->archived ? 'selected' : '' }}>
                                                            Not Archived
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group modern-form-group">
                                                <label for="show_all_outcomes" class="modern-label">
                                                    <i class="fa fa-list"></i> Show All Outcomes
                                                </label>
                                                <select name="show_all_outcomes" id="show_all_outcomes"
                                                    class="form-control modern-select">
                                                    <option value="1"
                                                        {{ (isset($data->show_all_outcomes) && $data->show_all_outcomes) || (isset($data->showAllOutcomes) && $data->showAllOutcomes) ? 'selected' : '' }}>
                                                        Show All</option>
                                                    <option value="0"
                                                        {{ (!isset($data->show_all_outcomes) || !$data->show_all_outcomes) && (!isset($data->showAllOutcomes) || !$data->showAllOutcomes) ? 'selected' : '' }}>
                                                        Hide Some</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group modern-form-group">
                                                <label for="enable_order_book" class="modern-label">
                                                    <i class="fa fa-book"></i> Enable Order Book
                                                </label>
                                                <select name="enable_order_book" id="enable_order_book"
                                                    class="form-control modern-select">
                                                    <option value="1"
                                                        {{ (isset($data->enable_order_book) && $data->enable_order_book) || (isset($data->enableOrderBook) && $data->enableOrderBook) ? 'selected' : '' }}>
                                                        Enabled</option>
                                                    <option value="0"
                                                        {{ (!isset($data->enable_order_book) || !$data->enable_order_book) && (!isset($data->enableOrderBook) || !$data->enableOrderBook) ? 'selected' : '' }}>
                                                        Disabled</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Market Outcomes -->
                            <div class="box modern-card">
                                <div class="box-header with-border card-header-modern" data-toggle="collapse"
                                    data-target="#marketOutcomes">
                                    <h4 class="box-title">
                                        <i class="fa fa-list"></i> Market Outcomes
                                        <span class="badge badge-primary ml-2">{{ count($data->markets ?? []) }}</span>
                                        <i class="fa fa-chevron-down float-right toggle-icon"></i>
                                    </h4>
                                </div>
                                <div class="box-body collapse show" id="marketOutcomes">
                                    <div class="markets-container" id="marketsContainer">
                                        @foreach ($data->markets ?? [] as $index => $market)
                                            <div class="market-outcome-card" data-market-index="{{ $index }}">
                                                <div class="market-card-header">
                                                    <h5 class="market-card-title">
                                                        <i class="fa fa-question-circle"></i>
                                                        {{ $market->question ?? 'Market Outcome' }}
                                                    </h5>
                                                    <button type="button" class="btn btn-sm btn-danger remove-market-btn"
                                                        data-market-id="{{ $market->id ?? $index }}">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </button>
                                                </div>
                                                <div class="market-card-body">
                                                    <input type="hidden" name="markets[{{ $index }}][id]"
                                                        value="{{ $market->id ?? '' }}">

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-question"></i> Question/Outcome
                                                                </label>
                                                                <input type="text"
                                                                    name="markets[{{ $index }}][question]"
                                                                    class="form-control modern-input"
                                                                    value="{{ $market->question ?? '' }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-slug"></i>Slug
                                                                </label>
                                                                <input type="text"
                                                                    name="markets[{{ $index }}][slug]"
                                                                    class="form-control modern-input"
                                                                    value="{{ $market->slug ?? '' }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-align-left"></i> Description
                                                                </label>
                                                                <textarea name="markets[{{ $index }}][description]" rows="3" class="form-control modern-input">{{ $market->description ?? '' }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-image"></i> Icon
                                                                </label>
                                                                <div class="file-upload-wrapper">
                                                                    <input type="file"
                                                                        name="markets[{{ $index }}][icon]"
                                                                        class="form-control modern-input file-input"
                                                                        accept="image/*"
                                                                        id="marketIcon{{ $index }}">
                                                                    @if (isset($market->icon) && $market->icon)
                                                                        <input type="hidden"
                                                                            name="markets[{{ $index }}][existing_icon]"
                                                                            value="{{ $market->icon }}">
                                                                    @endif
                                                                    <div class="image-preview mt-2">
                                                                        <img id="marketIconPreview{{ $index }}"
                                                                            src="{{ $market->icon ?? '' }}"
                                                                            alt="Icon" class="preview-image-small"
                                                                            style="{{ empty($market->icon) ? 'display:none;' : '' }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-calendar-check"></i> Start Date
                                                                </label>
                                                                <input type="date"
                                                                    name="markets[{{ $index }}][start_date]"
                                                                    class="form-control modern-input"
                                                                    value="{{ $market->start_date ?? ($market->startDate ?? null) ? \Carbon\Carbon::parse($market->start_date ?? $market->startDate)->format('Y-m-d') : '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-calendar-times"></i> End Date
                                                                </label>
                                                                <input type="date"
                                                                    name="markets[{{ $index }}][end_date]"
                                                                    class="form-control modern-input"
                                                                    value="{{ $market->end_date ?? ($market->endDate ?? null) ? \Carbon\Carbon::parse($market->end_date ?? $market->endDate)->format('Y-m-d') : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @php
                                                        $prices = null;
                                                        if (isset($market->outcome_prices)) {
                                                            $prices = is_string($market->outcome_prices)
                                                                ? json_decode($market->outcome_prices, true)
                                                                : $market->outcome_prices;
                                                        } elseif (isset($market->outcomePrices)) {
                                                            $prices = is_string($market->outcomePrices)
                                                                ? json_decode($market->outcomePrices, true)
                                                                : $market->outcomePrices;
                                                        }
                                                        $yesPercent = '0.00';
                                                        $noPercent = '0.00';
                                                        if ($prices && is_array($prices) && count($prices) >= 2) {
                                                            $yesPercent = format_number($prices[0] * 100, 2);
                                                            $noPercent = format_number($prices[1] * 100, 2);
                                                        }
                                                    @endphp
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-check-circle text-success"></i> Yes
                                                                    Percent (%)
                                                                </label>
                                                                <input type="text"
                                                                    name="markets[{{ $index }}][yesPercent]"
                                                                    class="form-control modern-input"
                                                                    value="{{ $yesPercent }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-times-circle text-danger"></i> No
                                                                    Percent (%)
                                                                </label>
                                                                <input type="text"
                                                                    name="markets[{{ $index }}][noPercent]"
                                                                    class="form-control modern-input"
                                                                    value="{{ $noPercent }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 col-lg-3">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-dollar-sign"></i> Liquidity
                                                                </label>
                                                                <input type="number" step="0.01"
                                                                    name="markets[{{ $index }}][liquidity]"
                                                                    class="form-control modern-input"
                                                                    value="{{ $market->liquidity ?? 0 }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-3">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-dollar-sign"></i> Liquidity CLOB
                                                                </label>
                                                                <input type="number" step="0.01"
                                                                    name="markets[{{ $index }}][liquidity_clob]"
                                                                    class="form-control modern-input"
                                                                    value="{{ $market->liquidity_clob ?? ($market->liquidityClob ?? 0) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-3">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-chart-bar"></i> Volume
                                                                </label>
                                                                <input type="number" step="0.01"
                                                                    name="markets[{{ $index }}][volume]"
                                                                    class="form-control modern-input"
                                                                    value="{{ $market->volume ?? 0 }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-3">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-clock"></i> Volume 24hr
                                                                </label>
                                                                <input type="number" step="0.01"
                                                                    name="markets[{{ $index }}][volume_24hr]"
                                                                    class="form-control modern-input"
                                                                    value="{{ $market->volume_24hr ?? ($market->volume24hr ?? 0) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-3">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-calendar-week"></i> Volume 1wk
                                                                </label>
                                                                <input type="number" step="0.01"
                                                                    name="markets[{{ $index }}][volume_1wk]"
                                                                    class="form-control modern-input"
                                                                    value="{{ $market->volume_1wk ?? ($market->volume1wk ?? 0) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-3">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-calendar-alt"></i> Volume 1mo
                                                                </label>
                                                                <input type="number" step="0.01"
                                                                    name="markets[{{ $index }}][volume_1mo]"
                                                                    class="form-control modern-input"
                                                                    value="{{ $market->volume_1mo ?? ($market->volume1mo ?? 0) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-3">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-calendar"></i> Volume 1yr
                                                                </label>
                                                                <input type="number" step="0.01"
                                                                    name="markets[{{ $index }}][volume_1yr]"
                                                                    class="form-control modern-input"
                                                                    value="{{ $market->volume_1yr ?? ($market->volume1yr ?? 0) }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 col-lg-3">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-power-off"></i> Active
                                                                </label>
                                                                <select name="markets[{{ $index }}][active]"
                                                                    class="form-control modern-select">
                                                                    <option value="1"
                                                                        {{ isset($market->active) && $market->active ? 'selected' : '' }}>
                                                                        Active</option>
                                                                    <option value="0"
                                                                        {{ !isset($market->active) || !$market->active ? 'selected' : '' }}>
                                                                        Inactive</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-3">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-lock"></i> Closed
                                                                </label>
                                                                <select name="markets[{{ $index }}][closed]"
                                                                    class="form-control modern-select">
                                                                    <option value="1"
                                                                        {{ isset($market->closed) && $market->closed ? 'selected' : '' }}>
                                                                        Closed</option>
                                                                    <option value="0"
                                                                        {{ !isset($market->closed) || !$market->closed ? 'selected' : '' }}>
                                                                        Open</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-3">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-archive"></i> Archived
                                                                </label>
                                                                <select name="markets[{{ $index }}][archived]"
                                                                    class="form-control modern-select">
                                                                    <option value="1"
                                                                        {{ isset($market->archived) && $market->archived ? 'selected' : '' }}>
                                                                        Archived</option>
                                                                    <option value="0"
                                                                        {{ !isset($market->archived) || !$market->archived ? 'selected' : '' }}>
                                                                        Not Archived</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-3">
                                                            <div class="form-group modern-form-group">
                                                                <label class="modern-label">
                                                                    <i class="fa fa-certificate"></i> New
                                                                </label>
                                                                <select name="markets[{{ $index }}][new]"
                                                                    class="form-control modern-select">
                                                                    <option value="1"
                                                                        {{ isset($market->new) && $market->new ? 'selected' : '' }}>
                                                                        New</option>
                                                                    <option value="0"
                                                                        {{ !isset($market->new) || !$market->new ? 'selected' : '' }}>
                                                                        Not New</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary btn-lg btn-modern">
                                    <i class="fa fa-save"></i> Update Market
                                </button>
                                <a href="{{ route('admin.market.list') }}" class="btn btn-secondary btn-lg">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @push('styles')
        <style>
            .edit-header-box {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                margin-bottom: 30px;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

            .edit-title {
                font-size: 28px;
                font-weight: 700;
                margin: 0;
                color: white;
            }

            .edit-title i {
                margin-right: 10px;
            }

            .edit-subtitle {
                margin: 5px 0 0 0;
                opacity: 0.9;
                font-size: 14px;
            }

            .modern-card {
                border-radius: 12px;
                margin-bottom: 25px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
                border: none;
                overflow: hidden;
                transition: all 0.3s ease;
            }

            .modern-card:hover {
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            }

            .card-header-modern {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                padding: 20px;
                cursor: pointer;
                border-bottom: 2px solid #e9ecef;
                transition: all 0.3s ease;
            }

            .card-header-modern:hover {
                background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            }

            .card-header-modern h4 {
                margin: 0;
                font-weight: 600;
                color: #2c3e50;
            }

            .card-header-modern h4 i {
                margin-right: 10px;
                color: #667eea;
            }

            .toggle-icon {
                transition: transform 0.3s ease;
                color: #667eea;
            }

            .card-header-modern[aria-expanded="true"] .toggle-icon {
                transform: rotate(180deg);
            }

            .modern-form-group {
                margin-bottom: 20px;
            }

            .modern-label {
                font-weight: 600;
                color: #495057;
                margin-bottom: 8px;
                display: block;
                font-size: 14px;
            }

            .modern-label i {
                margin-right: 8px;
                color: #667eea;
                width: 18px;
            }

            .modern-input,
            .modern-select {
                border-radius: 8px;
                border: 2px solid #e9ecef;
                padding: 12px 15px;
                transition: all 0.3s ease;
                font-size: 14px;
            }

            .modern-input:focus,
            .modern-select:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
                outline: none;
            }

            .file-upload-wrapper {
                position: relative;
            }

            .file-input {
                cursor: pointer;
            }

            .image-preview {
                margin-top: 10px;
            }

            .preview-image {
                max-width: 200px;
                max-height: 200px;
                border-radius: 8px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                border: 2px solid #e9ecef;
            }

            .preview-image-small {
                max-width: 100px;
                max-height: 100px;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                border: 2px solid #e9ecef;
            }

            .market-outcome-card {
                background: #f8f9fa;
                border-radius: 12px;
                padding: 25px;
                margin-bottom: 25px;
                border: 2px solid #e9ecef;
                transition: all 0.3s ease;
                position: relative;
            }

            .market-outcome-card:hover {
                border-color: #667eea;
                box-shadow: 0 5px 20px rgba(102, 126, 234, 0.15);
                transform: translateY(-2px);
            }

            .market-card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 2px solid #dee2e6;
            }

            .market-card-title {
                margin: 0;
                font-size: 18px;
                font-weight: 600;
                color: #2c3e50;
            }

            .market-card-title i {
                color: #667eea;
                margin-right: 8px;
            }

            .remove-market-btn {
                border-radius: 8px;
                padding: 8px 15px;
                transition: all 0.3s ease;
            }

            .remove-market-btn:hover {
                transform: scale(1.05);
                box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            }

            .market-card-body {
                padding-top: 15px;
            }

            .form-actions {
                padding: 30px 0;
                text-align: center;
                border-top: 2px solid #e9ecef;
                margin-top: 30px;
            }

            .btn-modern {
                border-radius: 10px;
                padding: 12px 30px;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            }

            .btn-modern:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            }

            .badge {
                padding: 5px 12px;
                border-radius: 20px;
                font-size: 12px;
            }

            @media (max-width: 768px) {
                .edit-title {
                    font-size: 22px;
                }

                .market-card-header {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 15px;
                }

                .remove-market-btn {
                    width: 100%;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Show toast notifications
            @if (session('message'))
                Swal.fire({
                    icon: '{{ session('alert-type') === 'error' ? 'error' : 'success' }}',
                    title: '{{ session('alert-type') === 'error' ? 'Error!' : 'Success!' }}',
                    text: '{{ session('message') }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            // Form validation and error handling
            document.getElementById('marketEditForm').addEventListener('submit', function(e) {
                const form = this;
                const submitBtn = form.querySelector('button[type="submit"]');

                // Disable submit button to prevent double submission
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

                // Re-enable after 5 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa fa-save"></i> Update Market';
                }, 5000);
            });

            // Handle form errors
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error!',
                    html: '<ul style="text-align: left;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                    confirmButtonText: 'OK'
                });
            @endif

            document.addEventListener('DOMContentLoaded', function() {
                // Image preview functionality
                const imageInput = document.getElementById('image');
                const iconInput = document.getElementById('icon');
                const imagePreview = document.getElementById('imagePreview');
                const iconPreview = document.getElementById('iconPreview');

                if (imageInput) {
                    imageInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                imagePreview.src = e.target.result;
                                imagePreview.style.display = 'block';
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }

                if (iconInput) {
                    iconInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                iconPreview.src = e.target.result;
                                iconPreview.style.display = 'block';
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }

                // Market icon preview functionality
                document.querySelectorAll('[id^="marketIcon"]').forEach(function(input) {
                    if (input.type === 'file') {
                        const index = input.id.replace('marketIcon', '');
                        const preview = document.getElementById('marketIconPreview' + index);

                        if (preview) {
                            input.addEventListener('change', function(e) {
                                const file = e.target.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = function(e) {
                                        preview.src = e.target.result;
                                        preview.style.display = 'block';
                                    };
                                    reader.readAsDataURL(file);
                                }
                            });
                        }
                    }
                });

                // Remove market card functionality
                document.querySelectorAll('.remove-market-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const marketCard = this.closest('.market-outcome-card');
                        const marketId = this.getAttribute('data-market-id');

                        if (confirm(
                                'Are you sure you want to remove this market? This action cannot be undone.'
                            )) {
                            // Add hidden input to mark for deletion
                            const deleteInput = document.createElement('input');
                            deleteInput.type = 'hidden';
                            deleteInput.name =
                                `markets[${marketCard.getAttribute('data-market-index')}][_delete]`;
                            deleteInput.value = '1';
                            marketCard.appendChild(deleteInput);

                            // Animate removal
                            marketCard.style.transition = 'all 0.3s ease';
                            marketCard.style.opacity = '0';
                            marketCard.style.transform = 'translateX(-100px)';

                            setTimeout(() => {
                                marketCard.remove();
                            }, 300);
                        }
                    });
                });

                // Collapsible sections toggle icon
                document.querySelectorAll('.card-header-modern').forEach(header => {
                    header.addEventListener('click', function() {
                        const icon = this.querySelector('.toggle-icon');
                        const isExpanded = this.getAttribute('aria-expanded') === 'true';
                        this.setAttribute('aria-expanded', !isExpanded);
                    });
                });
            });
        </script>
    @endpush
@endsection

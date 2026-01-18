@extends('backend.layouts.master')
@section('title', 'View Secondary Category')
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
                            <a href="{{ route('admin.secondary-categories.edit', $secondaryCategory) }}" class="btn btn-warning">
                                <i class="fa fa-edit"></i> Edit Category
                            </a>
                        </div>

                        <!-- Category Details -->
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    <i class="fa fa-folder"></i> {{ $secondaryCategory->name }}
                                </h4>
                                <div class="box-tools">
                                    @if($secondaryCategory->active)
                                        <span class="badge bg-success">
                                            <i class="fa fa-check-circle"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fa fa-times-circle"></i> Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <th style="width: 200px;">Name</th>
                                                    <td>{{ $secondaryCategory->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Main Category</th>
                                                    <td>
                                                        <span class="badge bg-primary">{{ ucfirst($secondaryCategory->main_category) }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Slug</th>
                                                    <td><code>{{ $secondaryCategory->slug }}</code></td>
                                                </tr>
                                                <tr>
                                                    <th>Description</th>
                                                    <td>{{ $secondaryCategory->description ?? 'No description provided' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Display Order</th>
                                                    <td>{{ $secondaryCategory->display_order }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Events</th>
                                                    <td>
                                                        <span class="badge bg-info">{{ $secondaryCategory->events()->count() }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Active Events</th>
                                                    <td>
                                                        <span class="badge bg-success">{{ $secondaryCategory->activeEvents()->count() }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Created At</th>
                                                    <td>{{ $secondaryCategory->created_at->format('M d, Y h:i A') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Updated At</th>
                                                    <td>{{ $secondaryCategory->updated_at->format('M d, Y h:i A') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        @if($secondaryCategory->icon)
                                            <div class="text-center">
                                                <label class="form-label">Category Icon</label>
                                                <div>
                                                    <img src="{{ asset('storage/' . $secondaryCategory->icon) }}" 
                                                        alt="{{ $secondaryCategory->name }}" 
                                                        style="max-width: 100%; max-height: 300px; border-radius: 12px; border: 2px solid #e9ecef;">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Events -->
                        @if($secondaryCategory->events()->count() > 0)
                            <div class="box">
                                <div class="box-header with-border">
                                    <h4 class="box-title">
                                        <i class="fa fa-calendar"></i> Recent Events (Latest 10)
                                    </h4>
                                </div>
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Category</th>
                                                    <th>Status</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($secondaryCategory->events()->latest()->take(10)->get() as $event)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $event->title }}</strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">{{ $event->category }}</span>
                                                        </td>
                                                        <td>
                                                            @if($event->active)
                                                                <span class="badge bg-success">Active</span>
                                                            @else
                                                                <span class="badge bg-danger">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $event->created_at->format('M d, Y') }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.events.show', $event) }}" 
                                                                class="btn btn-sm btn-info" 
                                                                title="View Event">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    @if($secondaryCategory->events()->count() > 10)
                                        <div class="text-center mt-3">
                                            <a href="{{ route('admin.events.index', ['secondary_category' => $secondaryCategory->id]) }}" 
                                                class="btn btn-primary">
                                                View All Events ({{ $secondaryCategory->events()->count() }})
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection






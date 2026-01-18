@extends('backend.layouts.master')
@section('title', 'Secondary Categories')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <!-- Success Message -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Error Messages -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-exclamation-triangle me-2"></i>
                                <strong>Error!</strong>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Action Header -->
                        <div class="box mb-3">
                            <div class="box-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">
                                        <i class="fa fa-folder-tree"></i> Secondary Categories
                                    </h4>
                                    <a href="{{ route('admin.secondary-categories.create') }}" class="btn btn-success">
                                        <i class="fa fa-plus-circle"></i> Create Secondary Category
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Search and Filter Section -->
                        <div class="box search-filter-box">
                            <div class="box-body">
                                <form method="GET" action="{{ route('admin.secondary-categories.index') }}">
                                    <div class="row align-items-end">
                                        <div class="col-md-5">
                                            <label class="form-label">
                                                <i class="fa fa-search"></i> Search Categories
                                            </label>
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Search by name..."
                                                value="{{ request('search') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">
                                                <i class="fa fa-filter"></i> Main Category
                                            </label>
                                            <select name="main_category" class="form-select">
                                                <option value="">All Main Categories</option>
                                                @foreach($mainCategories as $mainCat)
                                                    <option value="{{ $mainCat }}" 
                                                        {{ request('main_category') == $mainCat ? 'selected' : '' }}>
                                                        {{ ucfirst($mainCat) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-search"></i> Search
                                            </button>
                                            @if(request('search') || request('main_category'))
                                                <a href="{{ route('admin.secondary-categories.index') }}" class="btn btn-secondary">
                                                    <i class="fa fa-refresh"></i> Reset
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Categories Table -->
                        <div class="box">
                            <div class="box-body">
                                @if($categories->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th style="width: 50px">#</th>
                                                    <th style="width: 80px">Icon</th>
                                                    <th>Name</th>
                                                    <th>Main Category</th>
                                                    <th>Slug</th>
                                                    <th style="width: 100px">Events</th>
                                                    <th style="width: 100px">Order</th>
                                                    <th style="width: 100px">Status</th>
                                                    <th style="width: 150px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($categories as $category)
                                                    <tr>
                                                        <td>{{ $categories->firstItem() + $loop->index }}</td>
                                                        <td>
                                                            @if($category->icon)
                                                                <img src="{{ asset('storage/' . $category->icon) }}" 
                                                                    alt="{{ $category->name }}" 
                                                                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                                            @else
                                                                <div style="width: 50px; height: 50px; background: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                                    <i class="fa fa-folder" style="font-size: 24px; color: #6c757d;"></i>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <strong>{{ $category->name }}</strong>
                                                            @if($category->description)
                                                                <br>
                                                                <small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-primary">{{ ucfirst($category->main_category) }}</span>
                                                        </td>
                                                        <td>
                                                            <code>{{ $category->slug }}</code>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info">{{ $category->events()->count() }}</span>
                                                        </td>
                                                        <td class="text-center">{{ $category->display_order }}</td>
                                                        <td>
                                                            @if($category->active)
                                                                <span class="badge bg-success">
                                                                    <i class="fa fa-check-circle"></i> Active
                                                                </span>
                                                            @else
                                                                <span class="badge bg-danger">
                                                                    <i class="fa fa-times-circle"></i> Inactive
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ route('admin.secondary-categories.show', $category) }}" 
                                                                    class="btn btn-sm btn-info" title="View">
                                                                    <i class="fa fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('admin.secondary-categories.edit', $category) }}" 
                                                                    class="btn btn-sm btn-warning" title="Edit">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                                <form action="{{ route('admin.secondary-categories.destroy', $category) }}" 
                                                                    method="POST" 
                                                                    onsubmit="return confirm('Are you sure you want to delete this category?');"
                                                                    style="display: inline-block;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $categories->appends(request()->query())->links() }}
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fa fa-folder-open" style="font-size: 64px; color: #e9ecef;"></i>
                                        <h5 class="mt-3 text-muted">No secondary categories found</h5>
                                        <p class="text-muted">Create your first secondary category to organize events better.</p>
                                        <a href="{{ route('admin.secondary-categories.create') }}" class="btn btn-success mt-2">
                                            <i class="fa fa-plus-circle"></i> Create Secondary Category
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection






@extends('backend.layouts.master')
@section('title', 'Admin Users')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <!-- Breadcrumb -->
                <div class="row mb-3">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.backend.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Admin Users</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <!-- Action Header -->
                        <div class="box mb-3">
                            <div class="box-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">
                                        <i class="fa fa-user-shield"></i> Admin Users Management
                                    </h4>
                                    <div class="btn-group">
                                        @if(auth()->guard('admin')->user()->can('create roles'))
                                        <a href="{{ route('admin.admins.create') }}" class="btn btn-success">
                                            <i class="fa fa-plus-circle"></i> Create Admin
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search Form -->
                        <div class="box mb-3">
                            <div class="box-body">
                                <form method="GET" action="{{ route('admin.admins.index') }}">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <input type="text" name="search" class="form-control"
                                                    placeholder="Search by name or email..." value="{{ request('search') }}">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa fa-search"></i> Search
                                                </button>
                                                @if (request('search'))
                                                    <a href="{{ route('admin.admins.index') }}" class="btn btn-light">
                                                        <i class="fa fa-times"></i> Clear
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <span class="badge bg-primary fs-14">
                                                Total: {{ $admins->total() }} admin(s)
                                            </span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Admins Table -->
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">All Admin Users</h4>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th width="80">ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Roles</th>
                                                <th>Permissions</th>
                                                <th width="150">Created At</th>
                                                <th width="180">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($admins as $admin)
                                                @php
                                                    $allPermissions = $admin->getAllPermissions();
                                                @endphp
                                                <tr>
                                                    <td>#{{ $admin->id }}</td>
                                                    <td>
                                                        <strong>{{ $admin->name }}</strong>
                                                        @if($admin->id === auth()->guard('admin')->id())
                                                            <span class="badge bg-info">You</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $admin->email }}</td>
                                                    <td>
                                                        @if ($admin->roles->count() > 0)
                                                            <div class="d-flex flex-wrap gap-1 mb-1">
                                                                @foreach ($admin->roles as $role)
                                                                    <span class="badge bg-primary" title="{{ $role->permissions->count() }} permission(s)">
                                                                        <i class="fa fa-shield"></i> {{ ucfirst($role->name) }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                            <small class="text-muted">
                                                                {{ $admin->roles->count() }} role(s)
                                                            </small>
                                                        @else
                                                            <span class="text-muted">No roles assigned</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($allPermissions->count() > 0)
                                                            <div class="d-flex flex-wrap gap-1 mb-1">
                                                                @foreach ($allPermissions->take(3) as $permission)
                                                                    <span class="badge bg-info" style="font-size: 11px;">
                                                                        {{ ucwords(str_replace(['_', '-'], ' ', $permission->name)) }}
                                                                    </span>
                                                                @endforeach
                                                                @if ($allPermissions->count() > 3)
                                                                    <span class="badge bg-secondary" style="font-size: 11px;">
                                                                        +{{ $allPermissions->count() - 3 }} more
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <small class="text-muted">
                                                                Total: {{ $allPermissions->count() }} permission(s)
                                                            </small>
                                                        @else
                                                            <span class="text-muted">No permissions</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $admin->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('admin.admins.show', $admin->id) }}"
                                                                class="btn btn-sm btn-info" title="View Details">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            @if(auth()->guard('admin')->user()->can('edit roles'))
                                                            <a href="{{ route('admin.admins.edit', $admin->id) }}"
                                                                class="btn btn-sm btn-warning" title="Edit">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            @endif
                                                            @if(auth()->guard('admin')->user()->can('delete roles') && $admin->id !== auth()->guard('admin')->id())
                                                            <form action="{{ route('admin.admins.destroy', $admin->id) }}"
                                                                method="POST" class="d-inline"
                                                                onsubmit="return confirm('Are you sure you want to delete this admin?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">
                                                        <p class="text-muted py-3">No admin users found.</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                @if ($admins->hasPages())
                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $admins->links() }}
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


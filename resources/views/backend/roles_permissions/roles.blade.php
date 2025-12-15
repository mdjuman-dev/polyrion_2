@extends('backend.layouts.master')
@section('title', 'Roles Management')
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
                                <li class="breadcrumb-item active" aria-current="page">Roles</li>
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
                                        <i class="fa fa-shield"></i> Roles Management
                                    </h4>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.roles.create') }}" class="btn btn-success">
                                            <i class="fa fa-plus-circle"></i> Create Role
                                        </a>
                                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-info">
                                            <i class="fa fa-key"></i> Manage Permissions
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Roles Table -->
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">All Roles</h4>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th width="80">ID</th>
                                                <th>Name</th>
                                                <th>Permissions</th>
                                                <th width="150">Created At</th>
                                                <th width="120">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($roles as $role)
                                                <tr>
                                                    <td>#{{ $role->id }}</td>
                                                    <td>
                                                        <span
                                                            class="badge bg-primary fs-14">{{ ucfirst($role->name) }}</span>
                                                    </td>
                                                    <td>
                                                        @if ($role->permissions->count() > 0)
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach ($role->permissions->take(3) as $permission)
                                                                    <span
                                                                        class="badge bg-info">{{ ucwords(str_replace('_', ' ', $permission->name)) }}</span>
                                                                @endforeach
                                                                @if ($role->permissions->count() > 3)
                                                                    <span class="badge bg-secondary">
                                                                        +{{ $role->permissions->count() - 3 }} more
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <small class="text-muted d-block mt-1">
                                                                Total: {{ $role->permissions->count() }} permission(s)
                                                            </small>
                                                        @else
                                                            <span class="text-muted"><i class="fa fa-ban"></i> No
                                                                permissions</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $role->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('admin.roles.edit', $role->id) }}"
                                                                class="btn btn-sm btn-primary" title="Edit Role">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            @if ($role->name !== 'admin')
                                                                <form
                                                                    action="{{ route('admin.roles.destroy', $role->id) }}"
                                                                    method="POST" class="d-inline"
                                                                    onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone.');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                                        title="Delete Role">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <span class="btn btn-sm btn-secondary"
                                                                    title="Admin role cannot be deleted"
                                                                    style="cursor: not-allowed;">
                                                                    <i class="fa fa-lock"></i>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4">
                                                        <i class="fa fa-shield fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted mb-0">No roles found.</p>
                                                        <a href="{{ route('admin.roles.create') }}"
                                                            class="btn btn-primary btn-sm mt-2">
                                                            <i class="fa fa-plus"></i> Create First Role
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if ($roles->hasPages())
                                    <div class="mt-3">
                                        {{ $roles->links() }}
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

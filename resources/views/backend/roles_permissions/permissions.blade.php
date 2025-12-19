@extends('backend.layouts.master')
@section('title', 'Permissions Management')
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
                                <li class="breadcrumb-item active" aria-current="page">Permissions</li>
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
                                        <i class="fa fa-key"></i> Permissions Management
                                    </h4>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.permissions.create') }}" class="btn btn-success">
                                            <i class="fa fa-plus-circle"></i> Create Permission
                                        </a>
                                        <a href="{{ route('admin.roles.index') }}" class="btn btn-info">
                                            <i class="fa fa-shield"></i> Manage Roles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Permissions Table -->
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">All Permissions</h4>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th width="80">ID</th>
                                                <th>Permission Name</th>
                                                <th width="150">Created At</th>
                                                <th width="120">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($permissions as $permission)
                                                <tr>
                                                    <td>#{{ $permission->id }}</td>
                                                    <td>
                                                        <span class="badge bg-info fs-14">
                                                            <i class="fa fa-key"></i>
                                                            {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $permission->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('admin.permissions.edit', $permission->id) }}"
                                                                class="btn btn-sm btn-primary" title="Edit Permission">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            <form
                                                                action="{{ route('admin.permissions.destroy', $permission->id) }}"
                                                                method="POST" class="d-inline"
                                                                onsubmit="return handleDeleteConfirm(event, 'Are you sure you want to delete this permission? This action cannot be undone.');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger"
                                                                    title="Delete Permission">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-4">
                                                        <i class="fa fa-key fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted mb-0">No permissions found.</p>
                                                        <a href="{{ route('admin.permissions.create') }}"
                                                            class="btn btn-primary btn-sm mt-2">
                                                            <i class="fa fa-plus"></i> Create First Permission
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if ($permissions->hasPages())
                                    <div class="mt-3">
                                        {{ $permissions->links() }}
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

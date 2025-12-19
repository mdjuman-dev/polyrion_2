@extends('backend.layouts.master')
@section('title', 'Admin User Details')
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
                                <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Admin Users</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Admin Details</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">Admin Information</h4>
                            </div>
                            <div class="box-body text-center">
                                <div class="mb-3">
                                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                                        style="width: 100px; height: 100px; font-size: 40px; font-weight: bold;">
                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                    </div>
                                </div>
                                <h4>{{ $admin->name }}</h4>
                                <p class="text-muted">{{ $admin->email }}</p>
                                <p class="text-muted">
                                    <small>Created: {{ $admin->created_at->format('M d, Y') }}</small>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <!-- Roles Section -->
                        <div class="box mb-3">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    <i class="fa fa-shield"></i> Assigned Roles
                                </h4>
                            </div>
                            <div class="box-body">
                                @if ($admin->roles->count() > 0)
                                    <div class="row">
                                        @foreach ($admin->roles as $role)
                                            <div class="col-md-6 mb-3">
                                                <div class="card border-primary">
                                                    <div class="card-body">
                                                        <h5 class="card-title">
                                                            <span class="badge bg-primary fs-14">
                                                                <i class="fa fa-shield"></i> {{ ucfirst($role->name) }}
                                                            </span>
                                                        </h5>
                                                        <p class="text-muted mb-2">
                                                            <small>
                                                                <i class="fa fa-key"></i> 
                                                                {{ $role->permissions->count() }} permission(s) in this role
                                                            </small>
                                                        </p>
                                                        @if($role->permissions->count() > 0)
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach($role->permissions->take(5) as $permission)
                                                                    <span class="badge bg-info" style="font-size: 10px;">
                                                                        {{ ucwords(str_replace(['_', '-'], ' ', $permission->name)) }}
                                                                    </span>
                                                                @endforeach
                                                                @if($role->permissions->count() > 5)
                                                                    <span class="badge bg-secondary" style="font-size: 10px;">
                                                                        +{{ $role->permissions->count() - 5 }} more
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-3">
                                        <strong>Total Roles: {{ $admin->roles->count() }}</strong>
                                    </div>
                                @else
                                    <p class="text-muted">No roles assigned to this admin.</p>
                                @endif
                            </div>
                        </div>

                        <!-- All Permissions Section -->
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    <i class="fa fa-key"></i> All Permissions
                                    <span class="badge bg-success">{{ $allPermissions->count() }} total</span>
                                </h4>
                            </div>
                            <div class="box-body">
                                @if ($allPermissions->count() > 0)
                                    @php
                                        // Group permissions by category (first word)
                                        $groupedPermissions = $allPermissions->groupBy(function($permission) {
                                            $parts = explode(' ', $permission->name);
                                            return ucfirst($parts[0]);
                                        });
                                    @endphp
                                    
                                    @foreach($groupedPermissions as $category => $permissions)
                                        <div class="mb-4">
                                            <h5 class="mb-2">
                                                <i class="fa fa-folder"></i> {{ $category }}
                                                <span class="badge bg-primary">{{ $permissions->count() }}</span>
                                            </h5>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($permissions as $permission)
                                                    <span class="badge bg-info" style="font-size: 12px;">
                                                        {{ ucwords(str_replace(['_', '-'], ' ', $permission->name)) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">No permissions assigned to this admin.</p>
                                @endif
                            </div>
                        </div>

                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">Actions</h4>
                            </div>
                            <div class="box-body">
                                <div class="btn-group">
                                    @if(auth()->guard('admin')->user()->can('edit roles'))
                                    <a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn btn-warning">
                                        <i class="fa fa-edit"></i> Edit Admin
                                    </a>
                                    @endif
                                    <a href="{{ route('admin.admins.index') }}" class="btn btn-light">
                                        <i class="fa fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection


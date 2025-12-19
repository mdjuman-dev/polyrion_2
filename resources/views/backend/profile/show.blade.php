@extends('backend.layouts.master')
@section('title', 'My Profile')
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
                                <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">Profile Information</h4>
                            </div>
                            <div class="box-body text-center">
                                <div class="mb-3">
                                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                                        style="width: 120px; height: 120px; font-size: 48px; font-weight: bold;">
                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                    </div>
                                </div>
                                <h4>{{ $admin->name }}</h4>
                                <p class="text-muted">{{ $admin->email }}</p>
                                @if($admin->isSuperAdmin())
                                    <span class="badge bg-danger">Super Admin</span>
                                @endif
                                <p class="text-muted mt-2">
                                    <small>Member since: {{ $admin->created_at->format('M d, Y') }}</small>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">Account Details</h4>
                            </div>
                            <div class="box-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="200">Name</th>
                                        <td>{{ $admin->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $admin->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Account Type</th>
                                        <td>
                                            @if($admin->isSuperAdmin())
                                                <span class="badge bg-danger">Super Admin</span>
                                            @else
                                                <span class="badge bg-primary">Admin</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ $admin->created_at->format('F d, Y h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated</th>
                                        <td>{{ $admin->updated_at->format('F d, Y h:i A') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($admin->roles->count() > 0)
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">Roles & Permissions</h4>
                            </div>
                            <div class="box-body">
                                <div class="mb-3">
                                    <h5>Roles:</h5>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($admin->roles as $role)
                                            <span class="badge bg-primary fs-14">{{ ucfirst($role->name) }}</span>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <h5>Permissions:</h5>
                                    @php
                                        $allPermissions = $admin->getAllPermissions();
                                        $groupedPermissions = $allPermissions->groupBy(function($permission) {
                                            $parts = explode(' ', $permission->name);
                                            return ucfirst($parts[0]);
                                        });
                                    @endphp
                                    
                                    @foreach($groupedPermissions as $category => $permissions)
                                        <div class="mb-3">
                                            <h6 class="mb-2">
                                                <i class="fa fa-folder"></i> {{ $category }}
                                                <span class="badge bg-info">{{ $permissions->count() }}</span>
                                            </h6>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($permissions as $permission)
                                                    <span class="badge bg-info" style="font-size: 11px;">
                                                        {{ ucwords(str_replace(['_', '-'], ' ', $permission->name)) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                    <p class="text-muted mt-2">
                                        <small>Total: {{ $allPermissions->count() }} permission(s)</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">Actions</h4>
                            </div>
                            <div class="box-body">
                                <div class="btn-group">
                                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-warning">
                                        <i class="fa fa-edit"></i> Edit Profile
                                    </a>
                                    <a href="{{ route('admin.backend.dashboard') }}" class="btn btn-light">
                                        <i class="fa fa-arrow-left"></i> Back to Dashboard
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


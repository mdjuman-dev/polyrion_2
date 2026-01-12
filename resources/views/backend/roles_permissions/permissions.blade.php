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
                        <div class="box mb-3" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
                            <div class="box-header with-border primary-gradient" style="padding: 25px 30px; border: none;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="box-title mb-0" style="color: #fff; font-weight: 700; font-size: 24px;">
                                            <i class="fa fa-key me-2"></i> Permissions Management
                                        </h4>
                                        <p class="mb-0 mt-2" style="color: rgba(255,255,255,0.9); font-size: 14px;">
                                            View and manage system permissions. Permissions cannot be edited or deleted once created.
                                        </p>
                                    </div>
                                    <div class="btn-group gap-2">
                                        <a href="{{ route('admin.permissions.create') }}" class="btn btn-light" style="border-radius: 10px; padding: 10px 20px; font-weight: 600;">
                                            <i class="fa fa-plus-circle me-2"></i> Create Permission
                                        </a>
                                        <a href="{{ route('admin.roles.index') }}" class="btn btn-light" style="border-radius: 10px; padding: 10px 20px; font-weight: 600;">
                                            <i class="fa fa-shield me-2"></i> Manage Roles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info mb-3" style="border-radius: 12px; border: none; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); box-shadow: 0 4px 12px rgba(33, 150, 243, 0.1);">
                            <div class="d-flex align-items-start">
                                <i class="fa fa-info-circle fa-2x me-3 mt-1" style="color: #1976d2;"></i>
                                <div>
                                    <strong style="color: #1565c0; font-size: 16px;">Setup Permissions:</strong>
                                    <p class="mb-0 mt-2" style="color: #424242;">
                                        To setup all default permissions, run the seeder: <code style="background: rgba(255,255,255,0.6); padding: 4px 8px; border-radius: 4px;">php artisan db:seed --class=RolePermissionSeeder</code>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Permissions Table -->
                        <div class="box" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-header with-border" style="padding: 20px 30px; border-bottom: 2px solid #e5e7eb;">
                                <h4 class="box-title mb-0" style="font-weight: 700; font-size: 18px; color: #1f2937;">
                                    <i class="fa fa-list me-2"></i> All Permissions
                                </h4>
                            </div>
                            <div class="box-body" style="padding: 25px;">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" style="margin-bottom: 0;">
                                        <thead>
                                            <tr style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                                <th width="80" style="font-weight: 700; color: #374151; padding: 15px;">ID</th>
                                                <th style="font-weight: 700; color: #374151; padding: 15px;">Permission Name</th>
                                                <th width="150" style="font-weight: 700; color: #374151; padding: 15px;">Created At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($permissions as $permission)
                                                <tr style="transition: all 0.3s ease;">
                                                    <td style="padding: 15px; font-weight: 600; color: #6b7280;">#{{ $permission->id }}</td>
                                                    <td style="padding: 15px;">
                                                        <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 8px 15px; border-radius: 8px; font-size: 13px; font-weight: 600;">
                                                            <i class="fa fa-key me-2"></i>
                                                            {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                                        </span>
                                                    </td>
                                                    <td style="padding: 15px; color: #6b7280;">{{ $permission->created_at->format('M d, Y') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-5">
                                                        <div style="padding: 40px;">
                                                            <i class="fa fa-key fa-4x text-muted mb-3" style="opacity: 0.5;"></i>
                                                            <p class="text-muted mb-3" style="font-size: 16px;">No permissions found.</p>
                                                            <a href="{{ route('admin.permissions.create') }}"
                                                                class="btn btn-primary-gradient" style="border-radius: 10px; padding: 10px 25px; font-weight: 600;">
                                                                <i class="fa fa-plus me-2"></i> Create First Permission
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if ($permissions->hasPages())
                                    <div class="mt-4">
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

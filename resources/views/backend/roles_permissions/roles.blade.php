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
                        <div class="box mb-3" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
                            <div class="box-header with-border primary-gradient" style="padding: 25px 30px; border: none;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="box-title mb-0" style="color: #fff; font-weight: 700; font-size: 24px;">
                                            <i class="fa fa-shield me-2"></i> Roles Management
                                        </h4>
                                        <p class="mb-0 mt-2" style="color: rgba(255,255,255,0.9); font-size: 14px;">
                                            Manage roles and assign permissions to control access
                                        </p>
                                    </div>
                                    <div class="btn-group gap-2">
                                        <a href="{{ route('admin.roles.create') }}" class="btn btn-light" style="border-radius: 10px; padding: 10px 20px; font-weight: 600;">
                                            <i class="fa fa-plus-circle me-2"></i> Create Role
                                        </a>
                                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-light" style="border-radius: 10px; padding: 10px 20px; font-weight: 600;">
                                            <i class="fa fa-key me-2"></i> Manage Permissions
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Roles Table -->
                        <div class="box" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-header with-border" style="padding: 20px 30px; border-bottom: 2px solid #e5e7eb;">
                                <h4 class="box-title mb-0" style="font-weight: 700; font-size: 18px; color: #1f2937;">
                                    <i class="fa fa-list me-2"></i> All Roles
                                </h4>
                            </div>
                            <div class="box-body" style="padding: 25px;">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" style="margin-bottom: 0;">
                                        <thead>
                                            <tr style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                                <th width="80" style="font-weight: 700; color: #374151; padding: 15px;">ID</th>
                                                <th style="font-weight: 700; color: #374151; padding: 15px;">Name</th>
                                                <th style="font-weight: 700; color: #374151; padding: 15px;">Permissions</th>
                                                <th width="150" style="font-weight: 700; color: #374151; padding: 15px;">Created At</th>
                                                <th width="120" style="font-weight: 700; color: #374151; padding: 15px; text-align: center;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($roles as $role)
                                                <tr style="transition: all 0.3s ease;">
                                                    <td style="padding: 15px; font-weight: 600; color: #6b7280;">#{{ $role->id }}</td>
                                                    <td style="padding: 15px;">
                                                        <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 8px 15px; border-radius: 8px; font-size: 13px; font-weight: 600;">
                                                            {{ ucfirst($role->name) }}
                                                        </span>
                                                    </td>
                                                    <td style="padding: 15px;">
                                                        @if ($role->permissions->count() > 0)
                                                            <div class="d-flex flex-wrap gap-1 mb-2">
                                                                @foreach ($role->permissions->take(3) as $permission)
                                                                    <span class="badge" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #fff; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                                                        {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                                                    </span>
                                                                @endforeach
                                                                @if ($role->permissions->count() > 3)
                                                                    <span class="badge" style="background: #6b7280; color: #fff; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                                                        +{{ $role->permissions->count() - 3 }} more
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <small class="text-muted" style="font-size: 12px; font-weight: 600;">
                                                                Total: {{ $role->permissions->count() }} permission(s)
                                                            </small>
                                                        @else
                                                            <span class="text-muted" style="font-size: 14px;">
                                                                <i class="fa fa-ban me-1"></i> No permissions
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td style="padding: 15px; color: #6b7280;">{{ $role->created_at->format('M d, Y') }}</td>
                                                    <td style="padding: 15px; text-align: center;">
                                                        <div class="btn-group gap-2">
                                                            <a href="{{ route('admin.roles.edit', $role->id) }}"
                                                                class="btn btn-sm btn-primary-gradient" title="Edit Role" style="border-radius: 8px; padding: 6px 12px; font-weight: 600;">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            @if ($role->name !== 'admin')
                                                                <form
                                                                    action="{{ route('admin.roles.destroy', $role->id) }}"
                                                                    method="POST" class="d-inline"
                                                                    onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone.');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete Role" style="border-radius: 8px; padding: 6px 12px; font-weight: 600;">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <span class="btn btn-sm btn-secondary" title="Admin role cannot be deleted"
                                                                    style="cursor: not-allowed; border-radius: 8px; padding: 6px 12px; font-weight: 600; opacity: 0.6;">
                                                                    <i class="fa fa-lock"></i>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-5">
                                                        <div style="padding: 40px;">
                                                            <i class="fa fa-shield fa-4x text-muted mb-3" style="opacity: 0.5;"></i>
                                                            <p class="text-muted mb-3" style="font-size: 16px;">No roles found.</p>
                                                            <a href="{{ route('admin.roles.create') }}"
                                                                class="btn btn-primary-gradient" style="border-radius: 10px; padding: 10px 25px; font-weight: 600;">
                                                                <i class="fa fa-plus me-2"></i> Create First Role
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if ($roles->hasPages())
                                    <div class="mt-4">
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

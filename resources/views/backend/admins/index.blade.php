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
                        <div class="box mb-3" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
                            <div class="box-header with-border primary-gradient" style="padding: 25px 30px; border: none;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="box-title mb-0" style="color: #fff; font-weight: 700; font-size: 24px;">
                                            <i class="fa fa-user-shield me-2"></i> Admin Users Management
                                        </h4>
                                        <p class="mb-0 mt-2" style="color: rgba(255,255,255,0.9); font-size: 14px;">
                                            Manage admin users, roles, and permissions
                                        </p>
                                    </div>
                                    <div class="btn-group gap-2">
                                        @if(auth()->guard('admin')->user()->can('create roles'))
                                        <a href="{{ route('admin.admins.create') }}" class="btn btn-light" style="border-radius: 10px; padding: 10px 20px; font-weight: 600;">
                                            <i class="fa fa-plus-circle me-2"></i> Create Admin
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search Form -->
                        <div class="box mb-3" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-body" style="padding: 20px 30px;">
                                <form method="GET" action="{{ route('admin.admins.index') }}">
                                    <div class="row align-items-end">
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <input type="text" name="search" class="form-control"
                                                    placeholder="Search by name or email..." value="{{ request('search') }}"
                                                    style="border: 1px solid #e5e7eb; border-radius: 10px 0 0 10px; padding: 12px 15px; font-size: 15px;">
                                                <button type="submit" class="btn btn-primary-gradient" style="border-radius: 0 10px 10px 0; padding: 12px 20px; font-weight: 600;">
                                                    <i class="fa fa-search me-2"></i> Search
                                                </button>
                                                @if (request('search'))
                                                    <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary-theme ms-2" style="border-radius: 10px; padding: 12px 20px; font-weight: 600;">
                                                        <i class="fa fa-times me-2"></i> Clear
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: 600;">
                                                Total: {{ $admins->total() }} admin(s)
                                            </span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Admins Table -->
                        <div class="box" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <div class="box-header with-border" style="padding: 20px 30px; border-bottom: 2px solid #e5e7eb;">
                                <h4 class="box-title mb-0" style="font-weight: 700; font-size: 18px; color: #1f2937;">
                                    <i class="fa fa-list me-2"></i> All Admin Users
                                </h4>
                            </div>
                            <div class="box-body" style="padding: 25px;">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" style="margin-bottom: 0;">
                                        <thead>
                                            <tr style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                                <th width="80" style="font-weight: 700; color: #374151; padding: 15px;">ID</th>
                                                <th style="font-weight: 700; color: #374151; padding: 15px;">Name</th>
                                                <th style="font-weight: 700; color: #374151; padding: 15px;">Email</th>
                                                <th style="font-weight: 700; color: #374151; padding: 15px;">Roles</th>
                                                <th style="font-weight: 700; color: #374151; padding: 15px;">Permissions</th>
                                                <th width="150" style="font-weight: 700; color: #374151; padding: 15px;">Created At</th>
                                                <th width="180" style="font-weight: 700; color: #374151; padding: 15px; text-align: center;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($admins as $admin)
                                                @php
                                                    $allPermissions = $admin->getAllPermissions();
                                                @endphp
                                                <tr style="transition: all 0.3s ease;">
                                                    <td style="padding: 15px; font-weight: 600; color: #6b7280;">#{{ $admin->id }}</td>
                                                    <td style="padding: 15px;">
                                                        <strong style="color: #1f2937; font-size: 15px;">{{ $admin->name }}</strong>
                                                        @if($admin->id === auth()->guard('admin')->id())
                                                            <span class="badge" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #fff; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; margin-left: 8px;">You</span>
                                                        @endif
                                                    </td>
                                                    <td style="padding: 15px; color: #6b7280;">{{ $admin->email }}</td>
                                                    <td style="padding: 15px;">
                                                        @if ($admin->roles->count() > 0)
                                                            <div class="d-flex flex-wrap gap-1 mb-2">
                                                                @foreach ($admin->roles as $role)
                                                                    <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;" title="{{ $role->permissions->count() }} permission(s)">
                                                                        <i class="fa fa-shield me-1"></i> {{ ucfirst($role->name) }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                            <small class="text-muted" style="font-size: 12px; font-weight: 600;">
                                                                {{ $admin->roles->count() }} role(s)
                                                            </small>
                                                        @else
                                                            <span class="text-muted" style="font-size: 14px;">No roles assigned</span>
                                                        @endif
                                                    </td>
                                                    <td style="padding: 15px;">
                                                        @if ($allPermissions->count() > 0)
                                                            <div class="d-flex flex-wrap gap-1 mb-2">
                                                                @foreach ($allPermissions->take(3) as $permission)
                                                                    <span class="badge" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #fff; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                                                        {{ ucwords(str_replace(['_', '-'], ' ', $permission->name)) }}
                                                                    </span>
                                                                @endforeach
                                                                @if ($allPermissions->count() > 3)
                                                                    <span class="badge" style="background: #6b7280; color: #fff; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                                                        +{{ $allPermissions->count() - 3 }} more
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <small class="text-muted" style="font-size: 12px; font-weight: 600;">
                                                                Total: {{ $allPermissions->count() }} permission(s)
                                                            </small>
                                                        @else
                                                            <span class="text-muted" style="font-size: 14px;">No permissions</span>
                                                        @endif
                                                    </td>
                                                    <td style="padding: 15px; color: #6b7280;">{{ $admin->created_at->format('M d, Y') }}</td>
                                                    <td style="padding: 15px; text-align: center;">
                                                        <div class="btn-group gap-2">
                                                            <a href="{{ route('admin.admins.show', $admin->id) }}"
                                                                class="btn btn-sm btn-info" title="View Details" style="border-radius: 8px; padding: 6px 12px; font-weight: 600;">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            @if(auth()->guard('admin')->user()->can('edit roles'))
                                                            <a href="{{ route('admin.admins.edit', $admin->id) }}"
                                                                class="btn btn-sm btn-warning" title="Edit" style="border-radius: 8px; padding: 6px 12px; font-weight: 600;">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            @endif
                                                            @if(auth()->guard('admin')->user()->can('delete roles') && $admin->id !== auth()->guard('admin')->id())
                                                            <form action="{{ route('admin.admins.destroy', $admin->id) }}"
                                                                method="POST" class="d-inline"
                                                                onsubmit="return confirm('Are you sure you want to delete this admin?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" style="border-radius: 8px; padding: 6px 12px; font-weight: 600;">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center py-5">
                                                        <div style="padding: 40px;">
                                                            <i class="fa fa-user-shield fa-4x text-muted mb-3" style="opacity: 0.5;"></i>
                                                            <p class="text-muted mb-3" style="font-size: 16px;">No admin users found.</p>
                                                            @if(auth()->guard('admin')->user()->can('create roles'))
                                                            <a href="{{ route('admin.admins.create') }}"
                                                                class="btn btn-primary-gradient" style="border-radius: 10px; padding: 10px 25px; font-weight: 600;">
                                                                <i class="fa fa-plus me-2"></i> Create First Admin
                                                            </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                @if ($admins->hasPages())
                                    <div class="d-flex justify-content-center mt-4">
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


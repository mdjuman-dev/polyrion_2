@extends('backend.layouts.master')
@section('title', 'Create Role')
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
                                <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create Role</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">Create New Role</h4>
                            </div>
                            <div class="box-body">
                                <form method="POST" action="{{ route('admin.roles.store') }}">
                                    @csrf

                                    <div class="form-group mb-3">
                                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}" placeholder="e.g., manager, editor" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Permissions</label>
                                        <div class="permissions-container border rounded p-3"
                                            style="max-height: 500px; overflow-y: auto; background-color: #f8f9fa;">
                                            @foreach ($permissions as $group => $groupPermissions)
                                                <div class="permission-group mb-4 pb-3 border-bottom">
                                                    <h5 class="mb-3 text-primary">
                                                        <i class="fa fa-folder"></i> {{ $group }}
                                                        <small class="text-muted">({{ $groupPermissions->count() }}
                                                            permissions)</small>
                                                    </h5>
                                                    <div class="row">
                                                        @foreach ($groupPermissions as $permission)
                                                            <div class="col-md-4 col-lg-3 mb-2">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="permissions[]" value="{{ $permission->id }}"
                                                                        id="permission_{{ $permission->id }}"
                                                                        {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="permission_{{ $permission->id }}">
                                                                        {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-secondary" onclick="selectAll()">
                                                <i class="fa fa-check-square"></i> Select All
                                            </button>
                                            <button type="button" class="btn btn-sm btn-secondary" onclick="deselectAll()">
                                                <i class="fa fa-square"></i> Deselect All
                                            </button>
                                            <span class="text-muted ms-2">
                                                <span id="selectedCount">0</span> permission(s) selected
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Create Role
                                        </button>
                                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                                            <i class="fa fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @push('styles')
        <style>
            /* Fix Feather icon sizes */
            svg[data-feather] {
                width: 18px !important;
                height: 18px !important;
            }

            .sidebar-menu svg[data-feather] {
                width: 18px !important;
                height: 18px !important;
            }

            .treeview-menu svg[data-feather] {
                width: 16px !important;
                height: 16px !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function updateSelectedCount() {
                const checked = document.querySelectorAll('input[name="permissions[]"]:checked').length;
                document.getElementById('selectedCount').textContent = checked;
            }

            function selectAll() {
                document.querySelectorAll('input[name="permissions[]"]').forEach(function(checkbox) {
                    checkbox.checked = true;
                });
                updateSelectedCount();
            }

            function deselectAll() {
                document.querySelectorAll('input[name="permissions[]"]').forEach(function(checkbox) {
                    checkbox.checked = false;
                });
                updateSelectedCount();
            }

            // Update count on checkbox change
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('input[name="permissions[]"]').forEach(function(checkbox) {
                    checkbox.addEventListener('change', updateSelectedCount);
                });
                updateSelectedCount();

                // Reinitialize feather icons with proper size constraints
                if (typeof feather !== 'undefined') {
                    feather.replace({
                        width: 18,
                        height: 18
                    });
                }
            });
        </script>
    @endpush
@endsection

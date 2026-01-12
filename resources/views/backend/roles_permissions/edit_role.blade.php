@extends('backend.layouts.master')
@section('title', 'Edit Role')
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
                                <li class="breadcrumb-item active" aria-current="page">Edit Role</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="box" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
                            <div class="box-header with-border primary-gradient" style="padding: 25px 30px; border: none;">
                                <div class="d-flex align-items-center">
                                    <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; margin-right: 20px;">
                                        <i class="fa fa-shield fa-lg text-white"></i>
                                    </div>
                                    <div>
                                        <h4 class="box-title mb-0" style="color: #fff; font-weight: 700; font-size: 24px;">
                                            Edit Role: {{ $role->name }}
                                        </h4>
                                        <p class="mb-0 mt-1" style="color: rgba(255,255,255,0.9); font-size: 14px;">
                                            Update role details and permissions
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="box-body" style="padding: 30px;">
                                <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group mb-4">
                                        <label class="form-label fw-bold mb-2" style="color: #374151; font-size: 15px;">
                                            Role Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $role->name) }}" placeholder="e.g., manager, editor"
                                            required
                                            style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 15px; font-size: 15px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                        @error('name')
                                            <div class="invalid-feedback d-block mt-2" style="color: #ef4444; font-size: 14px;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="form-label fw-bold mb-2" style="color: #374151; font-size: 15px;">
                                            Permissions
                                        </label>
                                        <div class="permissions-container border rounded p-4"
                                            style="max-height: 500px; overflow-y: auto; background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border: 1px solid #e5e7eb !important; border-radius: 12px;">
                                            @foreach ($permissions as $group => $groupPermissions)
                                                <div class="permission-group mb-4 pb-3" style="border-bottom: 2px solid #e5e7eb;">
                                                    <h5 class="mb-3" style="color: #667eea; font-weight: 700; font-size: 16px;">
                                                        <i class="fa fa-folder me-2"></i> {{ $group }}
                                                        <small class="text-muted" style="font-weight: 500;">({{ $groupPermissions->count() }}
                                                            permissions)</small>
                                                    </h5>
                                                    <div class="row">
                                                        @foreach ($groupPermissions as $permission)
                                                            <div class="col-md-4 col-lg-3 mb-3">
                                                                <div class="form-check" style="background: #fff; padding: 10px; border-radius: 8px; border: 1px solid #e5e7eb; transition: all 0.3s ease;">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="permissions[]" value="{{ $permission->id }}"
                                                                        id="permission_{{ $permission->id }}"
                                                                        {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                                                        style="width: 18px; height: 18px; cursor: pointer; accent-color: #667eea;">
                                                                    <label class="form-check-label" for="permission_{{ $permission->id }}"
                                                                        style="cursor: pointer; font-size: 14px; color: #374151; margin-left: 8px;">
                                                                        {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="mt-3 d-flex align-items-center gap-3">
                                            <button type="button" class="btn btn-sm btn-primary-gradient" onclick="selectAll()" style="border-radius: 8px; padding: 8px 16px; font-weight: 600;">
                                                <i class="fa fa-check-square me-2"></i> Select All
                                            </button>
                                            <button type="button" class="btn btn-sm btn-secondary-theme" onclick="deselectAll()" style="border-radius: 8px; padding: 8px 16px; font-weight: 600;">
                                                <i class="fa fa-square me-2"></i> Deselect All
                                            </button>
                                            <span class="text-muted" style="font-weight: 600;">
                                                <span id="selectedCount" style="color: #667eea; font-weight: 700;">0</span> permission(s) selected
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group d-flex gap-3 pt-3" style="border-top: 1px solid #e5e7eb;">
                                        <button type="submit" class="btn btn-primary-gradient" style="padding: 12px 30px; font-size: 16px; font-weight: 600; border-radius: 12px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); transition: all 0.3s ease;">
                                            <i class="fa fa-save me-2"></i> Update Role
                                        </button>
                                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary-theme" style="padding: 12px 30px; font-size: 16px; font-weight: 600; border-radius: 12px; transition: all 0.3s ease;">
                                            <i class="fa fa-times me-2"></i> Cancel
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
            /* Permission checkbox hover effects */
            .form-check:hover {
                background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%) !important;
                border-color: #667eea !important;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
            }
            
            .form-check-input:checked {
                background-color: #667eea;
                border-color: #667eea;
            }
            
            .form-check-input:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            }
            
            /* Permissions container scrollbar */
            .permissions-container::-webkit-scrollbar {
                width: 8px;
            }
            
            .permissions-container::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }
            
            .permissions-container::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 10px;
            }
            
            .permissions-container::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(135deg, #5568d3 0%, #6a3d8f 100%);
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

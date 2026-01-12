@extends('backend.layouts.master')
@section('title', 'Create Permission')
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
                                <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">Permissions</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Create Permission</li>
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
                                        <i class="fa fa-key fa-lg text-white"></i>
                                    </div>
                                    <div>
                                        <h4 class="box-title mb-0" style="color: #fff; font-weight: 700; font-size: 24px;">
                                            Create New Permission
                                        </h4>
                                        <p class="mb-0 mt-1" style="color: rgba(255,255,255,0.9); font-size: 14px;">
                                            Add a new permission to the system
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="box-body" style="padding: 30px;">
                                <form method="POST" action="{{ route('admin.permissions.store') }}">
                                    @csrf

                                    <div class="form-group mb-4">
                                        <label class="form-label fw-bold mb-2" style="color: #374151; font-size: 15px;">
                                            Permission Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}" placeholder="e.g., manage users, view dashboard"
                                            required
                                            style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 15px; font-size: 15px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                        <small class="form-text text-muted mt-2 d-block">
                                            <i class="fa fa-info-circle me-1"></i>
                                            Use lowercase with spaces or underscores (e.g., "manage users" or "manage_users")
                                        </small>
                                        @error('name')
                                            <div class="invalid-feedback d-block mt-2" style="color: #ef4444; font-size: 14px;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group d-flex gap-3 pt-3" style="border-top: 1px solid #e5e7eb;">
                                        <button type="submit" class="btn btn-primary-gradient" style="padding: 12px 30px; font-size: 16px; font-weight: 600; border-radius: 12px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); transition: all 0.3s ease;">
                                            <i class="fa fa-save me-2"></i> Create Permission
                                        </button>
                                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary-theme" style="padding: 12px 30px; font-size: 16px; font-weight: 600; border-radius: 12px; transition: all 0.3s ease;">
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
@endsection

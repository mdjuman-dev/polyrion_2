@extends('backend.layouts.master')
@section('title', 'Edit Permission')
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
                                <li class="breadcrumb-item active" aria-current="page">Edit Permission</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">Edit Permission: {{ $permission->name }}</h4>
                            </div>
                            <div class="box-body">
                                <form method="POST" action="{{ route('admin.permissions.update', $permission->id) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group mb-3">
                                        <label class="form-label">Permission Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $permission->name) }}"
                                            placeholder="e.g., manage users, view dashboard" required>
                                        <small class="form-text text-muted">
                                            Use lowercase with spaces or underscores (e.g., "manage users" or
                                            "manage_users")
                                        </small>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Update Permission
                                        </button>
                                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
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
@endsection

@extends('backend.layouts.master')
@section('title', 'Edit Admin User')
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
                                <li class="breadcrumb-item active" aria-current="page">Edit Admin</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    <i class="fa fa-user-edit"></i> Edit Admin User: {{ $admin->name }}
                                </h4>
                            </div>
                            <div class="box-body">
                                <form action="{{ route('admin.admins.update', $admin->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" id="name" class="form-control"
                                                    value="{{ old('name', $admin->name) }}" required>
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email <span class="text-danger">*</span></label>
                                                <input type="email" name="email" id="email" class="form-control"
                                                    value="{{ old('email', $admin->email) }}" required>
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <input type="password" name="password" id="password" class="form-control">
                                                <small class="text-muted">Leave blank to keep current password. Minimum 8
                                                    characters if changing.</small>
                                                @error('password')
                                                    <span class="text-danger d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password_confirmation">Confirm Password</label>
                                                <input type="password" name="password_confirmation"
                                                    id="password_confirmation" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Roles</label>
                                        <div class="row">
                                            @foreach ($roles as $role)
                                                <div class="col-md-4 mb-2">
                                                    <div class="checkbox">
                                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                                            id="role_{{ $role->id }}"
                                                            {{ in_array($role->id, old('roles', $adminRoles)) ? 'checked' : '' }}>
                                                        <label for="role_{{ $role->id }}">
                                                            <strong>{{ ucfirst($role->name) }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                {{ $role->permissions->count() }} permission(s)
                                                            </small>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('roles')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-3">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-save"></i> Update Admin
                                        </button>
                                        <a href="{{ route('admin.admins.index') }}" class="btn btn-light">
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


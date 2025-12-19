<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function __construct()
    {
        // Permission checks are handled in routes
    }

    /**
     * Display a listing of admin users.
     */
    public function index(Request $request)
    {
        $query = Admin::query();

        // Exclude super admin (superadmin@admin.com)
        $query->where('email', '!=', 'superadmin@admin.com');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        $admins = $query->with(['roles.permissions'])->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('backend.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        $roles = Role::where('guard_name', 'admin')->get();
        return view('backend.admins.create', compact('roles'));
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Assign roles if provided
            if ($request->has('roles') && !empty($request->roles)) {
                $roles = Role::whereIn('id', $request->roles)
                    ->where('guard_name', 'admin')
                    ->get();
                $admin->syncRoles($roles);
            }

            return redirect()->route('admin.admins.index')
                ->with('success', 'Admin user created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create admin: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified admin.
     */
    public function show($id)
    {
        $admin = Admin::with(['roles.permissions'])->findOrFail($id);
        
        // Prevent viewing super admin
        if ($admin->email === 'superadmin@admin.com') {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Cannot view super admin details!');
        }
        
        // Get all permissions through roles
        $allPermissions = $admin->getAllPermissions();
        
        return view('backend.admins.show', compact('admin', 'allPermissions'));
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit($id)
    {
        $admin = Admin::with('roles')->findOrFail($id);
        
        // Prevent editing super admin
        if ($admin->email === 'superadmin@admin.com') {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Cannot edit super admin!');
        }
        
        $roles = Role::where('guard_name', 'admin')->get();
        $adminRoles = $admin->roles->pluck('id')->toArray();
        
        return view('backend.admins.edit', compact('admin', 'roles', 'adminRoles'));
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        
        // Prevent editing super admin
        if ($admin->email === 'superadmin@admin.com') {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Cannot edit super admin!');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $admin->name = $request->name;
            $admin->email = $request->email;
            
            if ($request->filled('password')) {
                $admin->password = Hash::make($request->password);
            }
            
            $admin->save();

            // Sync roles
            if ($request->has('roles')) {
                $roles = Role::whereIn('id', $request->roles)
                    ->where('guard_name', 'admin')
                    ->get();
                $admin->syncRoles($roles);
            } else {
                $admin->syncRoles([]);
            }

            return redirect()->route('admin.admins.index')
                ->with('success', 'Admin user updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update admin: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy($id)
    {
        try {
            $admin = Admin::findOrFail($id);
            
            // Prevent deleting super admin
            if ($admin->email === 'superadmin@admin.com') {
                return redirect()->route('admin.admins.index')
                    ->with('error', 'Cannot delete super admin!');
            }
            
            // Prevent deleting yourself
            if ($admin->id === auth()->guard('admin')->id()) {
                return redirect()->route('admin.admins.index')
                    ->with('error', 'You cannot delete your own account!');
            }

            // Prevent deleting if only one admin exists (excluding super admin)
            $adminCount = Admin::where('email', '!=', 'superadmin@admin.com')->count();
            if ($adminCount <= 1) {
                return redirect()->route('admin.admins.index')
                    ->with('error', 'Cannot delete the last admin user!');
            }

            $admin->delete();

            return redirect()->route('admin.admins.index')
                ->with('success', 'Admin user deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Failed to delete admin: ' . $e->getMessage());
        }
    }
}

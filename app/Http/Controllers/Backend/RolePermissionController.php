<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        // Permission checks are handled in routes
    }

    /**
     * Display a listing of roles.
     */
    public function roles()
    {
        $roles = Role::where('guard_name', 'admin')->with('permissions')->paginate(15);
        return view('backend.roles_permissions.roles', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function createRole()
    {
        $permissions = Permission::where('guard_name', 'admin')->get()->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);
            return ucfirst($parts[0]); // Group by first word (e.g., "manage", "view", "create")
        });
        return view('backend.roles_permissions.create_role', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function storeRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,NULL,id,guard_name,admin',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($request) {
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'admin',
            ]);

            if ($request->has('permissions') && !empty($request->permissions)) {
                // Convert permission IDs to Permission objects
                $permissions = Permission::whereIn('id', $request->permissions)
                    ->where('guard_name', 'admin')
                    ->get();
                $role->syncPermissions($permissions);
            }
        });

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully!');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function editRole($id)
    {
        $role = Role::where('guard_name', 'admin')->findOrFail($id);
        $permissions = Permission::where('guard_name', 'admin')->get()->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);
            return ucfirst($parts[0]);
        });
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('backend.roles_permissions.edit_role', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role.
     */
    public function updateRole(Request $request, $id)
    {
        $role = Role::where('guard_name', 'admin')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $id . ',id,guard_name,admin',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($request, $role) {
            $role->update([
                'name' => $request->name,
            ]);

            if ($request->has('permissions') && !empty($request->permissions)) {
                // Convert permission IDs to Permission objects
                $permissions = Permission::whereIn('id', $request->permissions)
                    ->where('guard_name', 'admin')
                    ->get();
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }
        });

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully!');
    }

    /**
     * Remove the specified role.
     */
    public function destroyRole($id)
    {
        $role = Role::where('guard_name', 'admin')->findOrFail($id);
        
        // Prevent deletion of admin role
        if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot delete the admin role!');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully!');
    }

    /**
     * Display a listing of permissions.
     */
    public function permissions()
    {
        $permissions = Permission::where('guard_name', 'admin')->paginate(15);
        return view('backend.roles_permissions.permissions', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function createPermission()
    {
        return view('backend.roles_permissions.create_permission');
    }

    /**
     * Store a newly created permission.
     */
    public function storePermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name,NULL,id,guard_name,admin',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Permission::create([
            'name' => $request->name,
            'guard_name' => 'admin',
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission created successfully!');
    }


    /**
     * Remove the specified permission.
     */
    public function destroyPermission($id)
    {
        $permission = Permission::where('guard_name', 'admin')->findOrFail($id);
        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully!');
    }
}

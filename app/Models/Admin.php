<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasRoles {
        HasRoles::hasPermissionTo as traitHasPermissionTo;
        HasRoles::hasRole as traitHasRole;
        HasRoles::hasAnyRole as traitHasAnyRole;
        HasRoles::hasAllRoles as traitHasAllRoles;
    }

    protected $fillable = ['name', 'email', 'password'];

    /**
     * The guard name for the model.
     *
     * @var string
     */
    protected $guard_name = 'admin';

    /**
     * Check if admin is super admin
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->email === 'superadmin@admin.com';
    }

    /**
     * Override can method to allow super admin access to everything
     *
     * @param string $permission
     * @param string|null $guardName
     * @return bool
     */
    public function can($permission, $guardName = null): bool
    {
        // Super admin can do everything
        if ($this->isSuperAdmin()) {
            return true;
        }

        // For permission strings, use hasPermissionTo
        if (is_string($permission)) {
            return $this->hasPermissionTo($permission, $guardName ?? $this->guard_name);
        }

        // Use parent can method for other cases (policies, etc.)
        return parent::can($permission, $guardName ?? $this->guard_name);
    }

    /**
     * Override hasPermissionTo to allow super admin access to everything
     *
     * @param string|\Spatie\Permission\Contracts\Permission $permission
     * @param string|null $guardName
     * @return bool
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        // Super admin can do everything
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Use the trait's hasPermissionTo method for other admins
        return $this->traitHasPermissionTo($permission, $guardName ?? $this->guard_name);
    }

    /**
     * Override hasRole to allow super admin to have all roles
     *
     * @param string|\Spatie\Permission\Contracts\Role $role
     * @param string|null $guardName
     * @return bool
     */
    public function hasRole($role, $guardName = null): bool
    {
        // Super admin has all roles
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Use the trait's hasRole method for other admins
        return $this->traitHasRole($role, $guardName ?? $this->guard_name);
    }

    /**
     * Override hasAnyRole to allow super admin
     *
     * @param array $roles
     * @param string|null $guardName
     * @return bool
     */
    public function hasAnyRole($roles, $guardName = null): bool
    {
        // Super admin has all roles
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Use the trait's hasAnyRole method for other admins
        return $this->traitHasAnyRole($roles, $guardName ?? $this->guard_name);
    }

    /**
     * Override hasAllRoles to allow super admin
     *
     * @param array $roles
     * @param string|null $guardName
     * @return bool
     */
    public function hasAllRoles($roles, $guardName = null): bool
    {
        // Super admin has all roles
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Use the trait's hasAllRoles method for other admins
        return $this->traitHasAllRoles($roles, $guardName ?? $this->guard_name);
    }
}

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The guard name for the model.
     *
     * @var string
     */
    protected $guard_name = 'admin';
    
    /**
     * Check if user has role (for compatibility with Spatie Permission)
     * Returns true by default if package not installed
     */
    public function hasRole($role): bool
    {
        // If Spatie Permission trait is available, check if it's being used
        if (trait_exists(\Spatie\Permission\Traits\HasRoles::class)) {
            $traits = class_uses_recursive(static::class);
            if (isset($traits[\Spatie\Permission\Traits\HasRoles::class])) {
                return parent::hasRole($role);
            }
        }
        return true; // Default: all admins have all roles if package not installed
    }
    
    /**
     * Assign role to admin (for compatibility with Spatie Permission)
     */
    public function assignRole($role)
    {
        // If Spatie Permission trait is available, check if it's being used
        if (trait_exists(\Spatie\Permission\Traits\HasRoles::class)) {
            $traits = class_uses_recursive(static::class);
            if (isset($traits[\Spatie\Permission\Traits\HasRoles::class])) {
                return parent::assignRole($role);
            }
        }
        return $this; // Do nothing if package not installed
    }
}

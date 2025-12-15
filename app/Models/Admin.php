<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasRoles;

    protected $fillable = ['name', 'email', 'password'];

    /**
     * The guard name for the model.
     *
     * @var string
     */
    protected $guard_name = 'admin';
}

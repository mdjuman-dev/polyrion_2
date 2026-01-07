<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralSetting extends Model
{
    protected $fillable = [
        'level',
        'commission_percent',
        'commission_type',
        'is_active',
    ];

    protected $casts = [
        'level' => 'integer',
        'commission_percent' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}

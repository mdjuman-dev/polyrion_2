<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    protected $fillable = [
        'user_id',
        'wallet_type',
        'wallet_address',
        'network',
        'wallet_name',
        'memo_tag',
        'signature_verification',
        'is_verified',
        'is_default',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


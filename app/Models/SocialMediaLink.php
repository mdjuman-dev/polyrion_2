<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform',
        'url',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->whereNotNull('url');
    }
}

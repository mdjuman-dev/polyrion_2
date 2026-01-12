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

    /**
     * Boot method to clear cache on save/update/delete
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('social_media_links:active');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('social_media_links:active');
        });
    }
}

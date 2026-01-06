<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_key',
        'title',
        'content',
    ];

    public static function getByKey($key)
    {
        return self::where('page_key', $key)->first();
    }
}

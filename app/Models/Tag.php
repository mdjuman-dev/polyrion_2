<?php

namespace App\Models;

use App\Models\Event;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $guarded = ['id'];
    
    public function events()
    {
        return $this->belongsToMany(Event::class);
    }
}

<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\Event;
use Illuminate\Database\Eloquent\Model;

class EventTag extends Model
{
    protected $guarded = ['id'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
<?php

namespace App\Models;

use App\Models\Market;
use App\Models\MarketComment;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'liquidity' => 'float',
        'volume' => 'float',
        'volume_24hr' => 'float',
        'volume_1wk' => 'float',
        'volume_1mo' => 'float',
        'volume_1yr' => 'float',
        'liquidity_clob' => 'float',
        'active' => 'boolean',
        'closed' => 'boolean',
        'archived' => 'boolean',
        'new' => 'boolean',
        'featured' => 'boolean',
        'restricted' => 'boolean',
        'show_all_outcomes' => 'boolean',
        'enable_order_book' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function markets()
    {
        return $this->hasMany(Market::class);
    }

    public function comments()
    {
        return $this->hasMany(MarketComment::class);
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_events', 'event_id', 'user_id')
            ->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'event_tags', 'event_id', 'tag_id')
            ->withTimestamps();
    }
}

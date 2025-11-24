<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\Event;
use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'liquidity' => 'float',
        'liquidityClob' => 'float',
        'volume' => 'float',
        'volume24hr' => 'float',
        'volume1wk' => 'float',
        'volume1mo' => 'float',
        'volume1yr' => 'float',
        'outcomePrices' => 'array',
        'active' => 'boolean',
        'closed' => 'boolean',
        'archived' => 'boolean',
        'new' => 'boolean',
        'restricted' => 'boolean',
        'startDate' => 'datetime',
        'endDate' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}

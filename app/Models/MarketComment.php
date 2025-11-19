<?php

namespace App\Models;

use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Eloquent\Model;

class MarketComment extends Model
{
    protected $fillable = ['market_id', 'user_id', 'comment_text', 'parent_comment_id'];

    public function replies()
    {
        return $this->hasMany(MarketComment::class, 'parent_comment_id');
    }

    public function parent()
    {
        return $this->belongsTo(MarketComment::class, 'parent_comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function market()
    {
        return $this->belongsTo(Event::class, 'market_id');
    }
}

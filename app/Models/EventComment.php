<?php

namespace App\Models;

use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Eloquent\Model;

class EventComment extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'comment_text',
        'parent_comment_id',
        'is_active',
        'polymarket_id',
        'user_address',
        'reply_address',
        'parent_entity_type',
        'parent_entity_id',
        'parent_comment_polymarket_id',
        'reactions',
        'reaction_count',
        'report_count',
        'profile_data'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'reactions' => 'array',
        'profile_data' => 'array',
    ];

    public function replies()
    {
        return $this->hasMany(EventComment::class, 'parent_comment_id');
    }

    public function parent()
    {
        return $this->belongsTo(EventComment::class, 'parent_comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'event_comment_likes', 'comment_id', 'user_id')
            ->withTimestamps();
    }
}

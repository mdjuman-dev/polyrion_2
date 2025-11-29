<?php

namespace App\Models;

use App\Models\Market;
use App\Models\MarketComment;
use App\Services\CategoryDetector;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

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

    /**
     * Boot the model and set up automatic category detection
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically detect category when creating or updating
        static::saving(function ($event) {
            if (empty($event->category) && !empty($event->title)) {
                $categoryDetector = App::make(CategoryDetector::class);
                $event->category = $categoryDetector->detect($event->title);
            }
        });
    }

    /**
     * Scope to filter events by category
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Manually detect and update category
     * 
     * @return self
     */
    public function detectCategory(): self
    {
        if (!empty($this->title)) {
            $categoryDetector = App::make(CategoryDetector::class);
            $this->category = $categoryDetector->detect($this->title);
        }

        return $this;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SecondaryCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'main_category',
        'description',
        'icon',
        'active',
        'display_order',
    ];

    protected $casts = [
        'active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug if not provided
        static::creating(function ($category) {
            if (empty($category->slug) && !empty($category->name)) {
                $category->slug = Str::slug($category->name);
                
                // Ensure unique slug
                $baseSlug = $category->slug;
                $counter = 1;
                while (self::where('slug', $category->slug)->exists()) {
                    $category->slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
            }
        });
    }

    /**
     * Get all events belonging to this secondary category
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'secondary_category_id');
    }

    /**
     * Get active events only
     */
    public function activeEvents(): HasMany
    {
        return $this->events()
            ->where('active', true)
            ->where('closed', false);
    }

    /**
     * Scope to filter active categories only
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope to filter by main category
     */
    public function scopeByMainCategory($query, string $mainCategory)
    {
        return $query->where('main_category', $mainCategory);
    }

    /**
     * Scope to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Get the count of active events
     */
    public function getActiveEventsCountAttribute(): int
    {
        return $this->activeEvents()->count();
    }
}






# Laravel Migrations & Models Analysis Report

## 🔴 CRITICAL ISSUES FOUND

### 1. **Naming Convention Violations (Snake Case)**
   - ❌ `showAllOutcomes` → ✅ `show_all_outcomes`
   - ❌ `enableOrderBook` → ✅ `enable_order_book`
   - ❌ `startDate` → ✅ `start_date`
   - ❌ `endDate` → ✅ `end_date`
   - ❌ `liquidityClob` → ✅ `liquidity_clob`
   - ❌ `volume24hr` → ✅ `volume_24hr`
   - ❌ `volume1wk` → ✅ `volume_1wk`
   - ❌ `volume1mo` → ✅ `volume_1mo`
   - ❌ `volume1yr` → ✅ `volume_1yr`

### 2. **WRONG Relationship in Event Model**
   - ❌ `Event::comments()` uses `hasMany(MarketComment::class)` 
   - ✅ Should be removed - MarketComment belongs to Market, not Event

### 3. **WRONG Relationship in MarketComment Model**
   - ❌ `belongsTo(Event::class, 'market_id')` 
   - ✅ Should be `belongsTo(Market::class, 'market_id')`

### 4. **Missing Pivot Table Specification**
   - Tag model uses `belongsToMany(Event::class)` but table is `event_tags`
   - Need to specify table name in relationship

### 5. **Missing Model Configuration**
   - Missing `$casts` for dates, booleans, JSON
   - Missing `$fillable` or proper `$guarded`
   - Missing date fields in `$dates` or `$casts`

### 6. **Missing Indexes**
   - Frequently queried fields like `slug`, `active`, `featured` should have indexes

---

## ✅ FIXED CODE

### Fixed: `database/migrations/2025_11_17_132137_create_events_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();

            // Liquidity / Volume
            $table->decimal('liquidity', 30, 8)->default(0);
            $table->decimal('volume', 30, 8)->default(0);
            $table->decimal('volume_24hr', 30, 8)->default(0);
            $table->decimal('volume_1wk', 30, 8)->default(0);
            $table->decimal('volume_1mo', 30, 8)->default(0);
            $table->decimal('volume_1yr', 30, 8)->default(0);
            $table->decimal('liquidity_clob', 30, 8)->default(0);

            // Status / Flags
            $table->boolean('active')->default(true);
            $table->boolean('closed')->default(false);
            $table->boolean('archived')->default(false);
            $table->boolean('new')->default(false);
            $table->boolean('featured')->default(false);
            $table->boolean('restricted')->default(false);
            $table->boolean('show_all_outcomes')->default(true);
            $table->boolean('enable_order_book')->default(true);

            // Dates
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index('active');
            $table->index('featured');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
```

### Fixed: `database/migrations/2025_11_17_132150_create_markets_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();

            // Basic info
            $table->string('slug')->unique();
            $table->string('question');
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();

            // Trading info
            $table->decimal('liquidity', 30, 8)->default(0);
            $table->decimal('liquidity_clob', 30, 8)->default(0);
            $table->decimal('volume', 30, 8)->default(0);
            $table->decimal('volume_24hr', 30, 8)->default(0);
            $table->decimal('volume_1wk', 30, 8)->default(0);
            $table->decimal('volume_1mo', 30, 8)->default(0);
            $table->decimal('volume_1yr', 30, 8)->default(0);

            $table->json('outcome_prices')->nullable();

            $table->boolean('active')->default(true);
            $table->boolean('closed')->default(false);
            $table->boolean('archived')->default(false);
            $table->boolean('featured')->default(false);
            $table->boolean('new')->default(false);
            $table->boolean('restricted')->default(false);
            $table->boolean('approved')->default(true);

            // Dates
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('event_id');
            $table->index('active');
            $table->index('featured');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('markets');
    }
};
```

### Fixed: `app/Models/Event.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean',
        'closed' => 'boolean',
        'archived' => 'boolean',
        'new' => 'boolean',
        'featured' => 'boolean',
        'restricted' => 'boolean',
        'show_all_outcomes' => 'boolean',
        'enable_order_book' => 'boolean',
        'liquidity' => 'decimal:8',
        'volume' => 'decimal:8',
        'volume_24hr' => 'decimal:8',
        'volume_1wk' => 'decimal:8',
        'volume_1mo' => 'decimal:8',
        'volume_1yr' => 'decimal:8',
        'liquidity_clob' => 'decimal:8',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function markets(): HasMany
    {
        return $this->hasMany(Market::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'event_tags')
            ->withTimestamps();
    }
}
```

### Fixed: `app/Models/Market.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Market extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean',
        'closed' => 'boolean',
        'archived' => 'boolean',
        'featured' => 'boolean',
        'new' => 'boolean',
        'restricted' => 'boolean',
        'approved' => 'boolean',
        'liquidity' => 'decimal:8',
        'liquidity_clob' => 'decimal:8',
        'volume' => 'decimal:8',
        'volume_24hr' => 'decimal:8',
        'volume_1wk' => 'decimal:8',
        'volume_1mo' => 'decimal:8',
        'volume_1yr' => 'decimal:8',
        'outcome_prices' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'market_tags')
            ->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(MarketComment::class);
    }
}
```

### Fixed: `app/Models/MarketComment.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketComment extends Model
{
    protected $fillable = [
        'market_id',
        'user_id',
        'comment_text',
        'parent_comment_id',
        'likes_count',
        'replies_count',
    ];

    protected $casts = [
        'likes_count' => 'integer',
        'replies_count' => 'integer',
    ];

    public function replies(): HasMany
    {
        return $this->hasMany(MarketComment::class, 'parent_comment_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MarketComment::class, 'parent_comment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class, 'market_id');
    }
}
```

### Fixed: `app/Models/Tag.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $guarded = ['id'];

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_tags')
            ->withTimestamps();
    }

    public function markets(): BelongsToMany
    {
        return $this->belongsToMany(Market::class, 'market_tags')
            ->withTimestamps();
    }
}
```

---

## 📝 EXPLANATION OF FIXES

### 1. **Naming Convention Fixes**
   - **Issue**: Laravel uses snake_case for database columns by convention
   - **Fix**: Changed all camelCase columns to snake_case
   - **Impact**: Prevents confusion and follows Laravel standards

### 2. **Event Model - Removed Wrong Relationship**
   - **Issue**: `Event::comments()` was pointing to `MarketComment` which belongs to `Market`, not `Event`
   - **Fix**: Removed the relationship (comments belong to markets, not events)
   - **Impact**: Prevents runtime errors when accessing `$event->comments`

### 3. **MarketComment Model - Fixed Market Relationship**
   - **Issue**: `belongsTo(Event::class, 'market_id')` was wrong - should be Market
   - **Fix**: Changed to `belongsTo(Market::class, 'market_id')`
   - **Impact**: Fixes relationship queries and eager loading

### 4. **Pivot Table Specification**
   - **Issue**: Laravel defaults to alphabetical pivot table names (`event_tag` vs `event_tags`)
   - **Fix**: Explicitly specified `'event_tags'` in relationship
   - **Impact**: Ensures correct pivot table is used

### 5. **Added Model Casts**
   - **Issue**: Missing type casting for dates, booleans, decimals, JSON
   - **Fix**: Added comprehensive `$casts` array
   - **Impact**: Automatic type conversion, better performance, prevents bugs

### 6. **Added Indexes**
   - **Issue**: Frequently queried columns lacked indexes
   - **Fix**: Added indexes on `active`, `featured`, `event_id`, `created_at`
   - **Impact**: Significantly improves query performance

### 7. **Added Missing Relationships**
   - **Issue**: Market model missing `comments()` relationship
   - **Fix**: Added `hasMany(MarketComment::class)`
   - **Impact**: Can now access `$market->comments`

### 8. **Added Market-Tag Relationship**
   - **Issue**: Tag model only had events relationship
   - **Fix**: Added markets relationship to Tag model
   - **Impact**: Can now tag markets as well as events

---

## ⚠️ IMPORTANT NOTES

1. **Database Migration Required**: You'll need to create a new migration to rename columns:
   ```php
   // Create migration: php artisan make:migration fix_column_names
   ```

2. **Update All Code References**: After renaming columns, update:
   - Controllers
   - Views (Blade templates)
   - Any queries using old column names

3. **Market-Tags Pivot Table**: If you want markets to have tags, create migration:
   ```php
   Schema::create('market_tags', function (Blueprint $table) {
       $table->id();
       $table->foreignId('market_id')->constrained()->cascadeOnDelete();
       $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
       $table->timestamps();
   });
   ```

4. **Backup Database**: Always backup before running migrations that rename columns!


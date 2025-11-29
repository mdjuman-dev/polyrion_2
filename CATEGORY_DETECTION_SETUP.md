# Category Detection System - Setup Guide

## Overview

This system automatically detects and assigns categories to events based on their titles using keyword matching, similar to Polymarket.

## Files Created

1. **Service Class**: `app/Services/CategoryDetector.php`
   - Core detection logic
   - Keyword mapping
   - Extensible category system

2. **Migration**: `database/migrations/2025_01_15_000000_add_category_to_events_table.php`
   - Adds `category` column to events table
   - Includes index for performance

3. **Model Update**: `app/Models/Event.php`
   - Automatic category detection on save
   - `byCategory()` scope for filtering
   - `detectCategory()` method for manual detection

4. **Controller**: `app/Http/Controllers/Backend/EventController.php`
   - Example implementation
   - CRUD operations with category detection
   - Bulk category re-detection endpoints

5. **Artisan Command**: `app/Console/Commands/DetectEventCategories.php`
   - Bulk category detection for existing events
   - CLI tool for maintenance

## Installation Steps

### 1. Run Migration

```bash
php artisan migrate
```

This will add the `category` column to your `events` table.

### 2. Update Existing Events (Optional)

If you have existing events, you can bulk-detect their categories:

```bash
# Detect categories for all events without category
php artisan events:detect-categories

# Force update all events (even those with existing categories)
php artisan events:detect-categories --force

# Only update events with specific category
php artisan events:detect-categories --category="Other"
```

### 3. Use in Your Code

The system works automatically! Just create or update events:

```php
// Automatic detection
Event::create([
    'title' => 'Bitcoin price prediction',
    // category will be automatically set to "Crypto"
]);
```

## Category Mappings

| Category | Keywords |
|----------|----------|
| **Politics** | election, vote, president, trump, biden, senate, congress, etc. |
| **Elections** | election, primary, ballot, voting, poll, etc. |
| **Crypto** | bitcoin, crypto, ethereum, btc, eth, blockchain, etc. |
| **Finance** | stock, share, market, nasdaq, trading, investment, etc. |
| **Earnings** | earnings, revenue, report, quarterly, q1, q2, etc. |
| **Tech** | ai, tech, apple, google, microsoft, amazon, etc. |
| **Sports** | football, cricket, nba, fifa, soccer, basketball, etc. |
| **Geopolitics** | war, russia, china, israel, conflict, ukraine, etc. |
| **World** | world, global, international, climate, etc. |
| **Culture** | culture, media, social, entertainment, movie, etc. |
| **Other** | Default when no keywords match |

## Extending Categories

To add more categories or keywords:

```php
use App\Services\CategoryDetector;

$detector = app(CategoryDetector::class);

// Add new category
$detector->addCategory('Gaming', [
    'gaming', 'video game', 'playstation', 'xbox', 'esports'
]);

// Add keywords to existing category
$detector->addCategory('Tech', [
    'quantum computing', 'quantum', 'vr', 'ar'
]);
```

**Note**: For permanent changes, edit `app/Services/CategoryDetector.php` directly.

## API Endpoints (Example)

If using the provided EventController:

- `GET /backend/events` - List events (filter by `?category=Crypto`)
- `POST /backend/events` - Create event (auto-detects category)
- `PUT /backend/events/{id}` - Update event (re-detects if title changes)
- `POST /backend/events/{id}/redetect-category` - Manually re-detect category
- `POST /backend/events/bulk-redetect` - Bulk re-detect all events

## Testing

Test the detection system:

```php
use App\Services\CategoryDetector;

$detector = app(CategoryDetector::class);

$tests = [
    'Bitcoin will hit $100k' => 'Crypto',
    'NBA Finals 2025' => 'Sports',
    'Apple Q1 earnings' => 'Earnings',
    'Election results' => 'Politics',
];

foreach ($tests as $title => $expected) {
    $result = $detector->detect($title);
    echo "{$title} => {$result} " . ($result === $expected ? '✓' : '✗') . "\n";
}
```

## Performance

- Category detection is fast (keyword matching)
- Database index on `category` column for fast queries
- Automatic detection only runs when title changes
- Bulk operations available via Artisan command

## Notes

- Detection is case-insensitive
- Uses word boundaries to prevent partial matches
- First matching category wins (order matters in keyword arrays)
- Can be overridden manually when creating events


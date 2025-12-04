# Category Detection System - Usage Examples

## Basic Usage

### Automatic Detection (Recommended)

The category is automatically detected when creating or updating events:

```php
use App\Models\Event;

// Category will be automatically detected from title
$event = Event::create([
    'title' => 'Bitcoin price prediction for 2025',
    'description' => 'Will Bitcoin reach $100k?',
    // category will be automatically set to "Crypto"
]);

// Update title - category will be re-detected
$event->update([
    'title' => 'Apple earnings report Q1 2025',
    // category will be automatically updated to "Earnings"
]);
```

### Manual Detection

```php
use App\Services\CategoryDetector;

$detector = app(CategoryDetector::class);
$category = $detector->detect('Bitcoin will hit $100k in 2025');
// Returns: "Crypto"

$category = $detector->detect('NBA Finals 2025 winner');
// Returns: "Sports"

$category = $detector->detect('Random topic without keywords');
// Returns: "Other"
```

### In Controller

```php
use App\Services\CategoryDetector;
use App\Models\Event;

class MyController extends Controller
{
    public function store(Request $request, CategoryDetector $detector)
    {
        $event = Event::create([
            'title' => $request->title,
            'category' => $detector->detect($request->title), // Explicit detection
        ]);
    }
}
```

### Query Events by Category

```php
use App\Models\Event;

// Get all Crypto events
$cryptoEvents = Event::byCategory('Crypto')->get();

// Get all Politics events
$politicsEvents = Event::byCategory('Politics')->get();

// Count events by category
$categoryCounts = Event::select('category', \DB::raw('count(*) as total'))
    ->groupBy('category')
    ->get();
```

### Re-detect Category for Existing Event

```php
$event = Event::find(1);

// Manually re-detect category
$event->detectCategory();
$event->save();
```

### Add Custom Categories

```php
use App\Services\CategoryDetector;

$detector = app(CategoryDetector::class);

// Add new category
$detector->addCategory('Gaming', [
    'gaming', 'video game', 'playstation', 'xbox', 'nintendo', 'esports'
]);

// Or update existing category
$detector->addCategory('Tech', [
    'quantum computing', 'quantum', 'quantum computer'
]);
```

## Available Categories

- **Politics**: election, vote, president, trump, biden, etc.
- **Elections**: election, primary, ballot, voting, etc.
- **Crypto**: bitcoin, crypto, ethereum, btc, eth, etc.
- **Finance**: stock, share, market, nasdaq, etc.
- **Earnings**: earnings, revenue, report, quarterly, etc.
- **Tech**: ai, tech, apple, google, microsoft, etc.
- **Sports**: football, cricket, nba, fifa, etc.
- **Geopolitics**: war, russia, china, israel, conflict, etc.
- **World**: world, global, international, etc.
- **Culture**: culture, media, social, entertainment, etc.
- **Other**: Default category when no keywords match

## Migration

Run the migration to add the category column:

```bash
php artisan migrate
```

## Testing

```php
// Test category detection
$detector = app(CategoryDetector::class);

$testCases = [
    'Bitcoin price prediction' => 'Crypto',
    'NBA Finals winner' => 'Sports',
    'Apple earnings report' => 'Earnings',
    'Election results 2025' => 'Politics',
    'Random topic' => 'Other',
];

foreach ($testCases as $title => $expected) {
    $detected = $detector->detect($title);
    echo "Title: {$title}\n";
    echo "Expected: {$expected}, Detected: {$detected}\n";
    echo ($expected === $detected ? "✓ PASS\n" : "✗ FAIL\n");
    echo "\n";
}
```


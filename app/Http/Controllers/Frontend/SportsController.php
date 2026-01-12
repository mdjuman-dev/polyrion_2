<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Market;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SportsController extends Controller
{
    /**
     * Display sports page with categories and events
     */
    public function index(Request $request)
    {
        // Base query for sports events - Exclude ended events (reused for counts)
        $baseQuery = Event::where('category', 'Sports')
            ->where('active', true)
            ->where('closed', false)
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
            });

        // Extract dynamic categories from event titles and market questions (cached)
        $cacheKey = 'sports_dynamic_categories';
        $dynamicCategories = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($baseQuery) {
            // Load only necessary data for category extraction
            $events = (clone $baseQuery)
                ->select(['id', 'title'])
                ->with(['markets' => function($q) {
                    $q->select(['id', 'event_id', 'question']);
                }])
            ->get();
            return $this->extractCategoriesFromEvents($events);
        });

        // Get popular categories (top 5 by event count)
        $popularCategories = collect($dynamicCategories)
            ->sortByDesc('count')
            ->take(5)
            ->values()
            ->all();

        // Get all categories sorted alphabetically
        $allCategories = collect($dynamicCategories)
            ->sortBy('name')
            ->values()
            ->all();

        // Get selected category and subcategory from query parameters
        $selectedCategory = $request->get('category', 'all');
        $selectedSubcategory = $request->get('subcategory', null);

        // Get events filtered by sports category - Exclude ended events
        $eventsQuery = (clone $baseQuery)
            ->with(['markets' => function ($query) {
                $query->select([
                    'id', 'event_id', 'question', 'slug', 'groupItem_title',
                    'outcome_prices', 'outcomes', 'active', 'closed',
                    'best_ask', 'best_bid', 'last_trade_price',
                    'close_time', 'end_date', 'volume24hr', 'final_result',
                    'outcome_result', 'final_outcome', 'created_at'
                ])
                ->where('active', true)
                ->where('closed', false)
                ->orderBy('created_at', 'desc')
                ->limit(10);
            }])
            ->orderBy('created_at', 'desc');

        // If specific category selected, filter by event title and market questions
        if ($selectedCategory !== 'all') {
            $categoryKeywords = $this->getCategoryKeywords($selectedCategory);
            $eventsQuery->where(function ($query) use ($categoryKeywords) {
                foreach ($categoryKeywords as $keyword) {
                    $query->orWhere('title', 'LIKE', '%' . $keyword . '%');
                }
            });

            // Also filter by market questions
            $eventsQuery->whereHas('markets', function ($query) use ($categoryKeywords) {
                foreach ($categoryKeywords as $keyword) {
                    $query->orWhere('question', 'LIKE', '%' . $keyword . '%');
                }
            });

            // If subcategory selected, filter further
            if ($selectedSubcategory) {
                $eventsQuery->where(function ($query) use ($selectedSubcategory) {
                    $query->orWhere('title', 'LIKE', '%' . $selectedSubcategory . '%');
                });
            }
        }

        $events = $eventsQuery->paginate(20);

        // Get subcategories for selected category (e.g., countries for Cricket) - cached
        $subcategoriesCacheKey = 'sports_subcategories_' . ($selectedCategory ?? 'all');
        $subcategories = \Illuminate\Support\Facades\Cache::remember($subcategoriesCacheKey, 300, function () use ($baseQuery, $selectedCategory) {
            // Load only necessary data for subcategory extraction
            $events = (clone $baseQuery)
                ->select(['id', 'title'])
                ->with(['markets' => function($q) {
                    $q->select(['id', 'event_id', 'question']);
                }])
                ->get();
            return $this->getSubcategories($selectedCategory, $events);
        });

        return view('frontend.sports', compact(
            'popularCategories',
            'allCategories',
            'selectedCategory',
            'selectedSubcategory',
            'events',
            'subcategories'
        ));
    }

    /**
     * Extract categories dynamically from event titles and market questions
     */
    private function extractCategoriesFromEvents($events)
    {
        $categories = [];
        $categoryPatterns = [
            'NFL' => ['nfl', 'national football league', 'super bowl'],
            'NBA' => ['nba', 'national basketball association'],
            'NCAA CBB' => ['ncaa', 'college basketball', 'ncaa cbb'],
            'NHL' => ['nhl', 'national hockey league', 'stanley cup'],
            'UFC' => ['ufc', 'ultimate fighting championship'],
            'Football' => ['football', 'soccer', 'fifa', 'world cup', 'premier league', 'champions league'],
            'Esports' => ['esports', 'e-sports', 'gaming', 'league of legends', 'dota', 'csgo'],
            'Cricket' => ['cricket', 'ipl', 'test match', 'odi', 't20', 'bcci'],
            'Tennis' => ['tennis', 'wimbledon', 'us open', 'french open', 'australian open', 'atp', 'wta'],
            'Hockey' => ['hockey', 'ice hockey', 'field hockey'],
            'Rugby' => ['rugby', 'six nations', 'rugby world cup'],
            'Basketball' => ['basketball', 'nba', 'wnba'],
            'American Football' => ['american football', 'nfl', 'college football'],
            'Baseball' => ['baseball', 'mlb', 'world series', 'major league baseball'],
            'Golf' => ['golf', 'pga', 'masters', 'us open golf', 'pga tour'],
            'Formula 1' => ['formula 1', 'f1', 'grand prix', 'formula one'],
            'Chess' => ['chess', 'fide', 'world chess'],
            'Boxing' => ['boxing', 'heavyweight', 'boxing match'],
            'Pickleball' => ['pickleball'],
        ];

        foreach ($events as $event) {
            $title = strtolower($event->title);
            $markets = $event->markets;

            foreach ($categoryPatterns as $categoryName => $keywords) {
                foreach ($keywords as $keyword) {
                    if (strpos($title, $keyword) !== false) {
                        if (!isset($categories[$categoryName])) {
                            $categories[$categoryName] = [
                                'name' => $categoryName,
                                'count' => 0,
                                'icon' => $this->getCategoryIcon($categoryName),
                            ];
                        }
                        $categories[$categoryName]['count']++;
                        break;
                    }
                }

                // Also check market questions
                foreach ($markets as $market) {
                    $question = strtolower($market->question ?? '');
                    foreach ($keywords as $keyword) {
                        if (strpos($question, $keyword) !== false) {
                            if (!isset($categories[$categoryName])) {
                                $categories[$categoryName] = [
                                    'name' => $categoryName,
                                    'count' => 0,
                                    'icon' => $this->getCategoryIcon($categoryName),
                                ];
                            }
                            if (!isset($categories[$categoryName]['counted_events'][$event->id])) {
                                $categories[$categoryName]['count']++;
                                $categories[$categoryName]['counted_events'][$event->id] = true;
                            }
                            break;
                        }
                    }
                }
            }
        }

        // Clean up counted_events from result
        foreach ($categories as &$category) {
            unset($category['counted_events']);
        }

        return $categories;
    }

    /**
     * Get category icon
     */
    private function getCategoryIcon($categoryName)
    {
        $icons = [
            'NFL' => 'fa-football',
            'NBA' => 'fa-basketball-ball',
            'NCAA CBB' => 'fa-basketball-ball',
            'NHL' => 'fa-hockey-puck',
            'UFC' => 'fa-fist-raised',
            'Football' => 'fa-futbol',
            'Esports' => 'fa-gamepad',
            'Cricket' => 'fa-baseball-ball',
            'Tennis' => 'fa-table-tennis',
            'Hockey' => 'fa-hockey-puck',
            'Rugby' => 'fa-football-ball',
            'Basketball' => 'fa-basketball-ball',
            'American Football' => 'fa-football-ball',
            'Baseball' => 'fa-baseball-ball',
            'Golf' => 'fa-golf-ball',
            'Formula 1' => 'fa-trophy',
            'Chess' => 'fa-chess',
            'Boxing' => 'fa-fist-raised',
            'Pickleball' => 'fa-table-tennis',
        ];

        return $icons[$categoryName] ?? 'fa-circle';
    }

    /**
     * Get subcategories for a category (e.g., countries for Cricket)
     */
    private function getSubcategories($category, $events)
    {
        if ($category === 'all' || !$category) {
            return [];
        }

        $subcategories = [];
        $countries = [
            'International', 'Australia', 'Bangladesh', 'England', 'India', 
            'New Zealand', 'Pakistan', 'South Africa', 'UAE', 'West Indies',
            'Sri Lanka', 'Afghanistan', 'Zimbabwe', 'Ireland'
        ];

        // For Cricket, return countries
        if (strtolower($category) === 'cricket') {
            foreach ($countries as $country) {
                $count = $events->filter(function ($event) use ($country) {
                    $title = strtolower($event->title);
                    $hasCountry = strpos($title, strtolower($country)) !== false;
                    
                    if (!$hasCountry) {
                        foreach ($event->markets as $market) {
                            $question = strtolower($market->question ?? '');
                            if (strpos($question, strtolower($country)) !== false) {
                                $hasCountry = true;
                                break;
                            }
                        }
                    }
                    
                    return $hasCountry;
                })->count();

                if ($count > 0) {
                    $subcategories[] = [
                        'name' => $country,
                        'count' => $count,
                    ];
                }
            }
        }

        return $subcategories;
    }

    /**
     * Get keywords for a sports category
     */
    private function getCategoryKeywords($category)
    {
        $keywords = [
            'nfl' => ['nfl', 'national football league', 'super bowl'],
            'nba' => ['nba', 'national basketball association'],
            'ncaa cbb' => ['ncaa', 'college basketball', 'ncaa cbb'],
            'nhl' => ['nhl', 'national hockey league', 'stanley cup'],
            'ufc' => ['ufc', 'ultimate fighting championship'],
            'football' => ['football', 'soccer', 'fifa', 'world cup'],
            'esports' => ['esports', 'e-sports', 'gaming'],
            'cricket' => ['cricket', 'ipl', 'test match', 'odi', 't20'],
            'tennis' => ['tennis', 'wimbledon', 'us open', 'french open'],
            'hockey' => ['hockey', 'ice hockey'],
            'rugby' => ['rugby', 'six nations'],
            'basketball' => ['basketball', 'nba', 'wnba'],
            'american football' => ['american football', 'nfl', 'college football'],
            'baseball' => ['baseball', 'mlb', 'world series'],
            'golf' => ['golf', 'pga', 'masters'],
            'formula 1' => ['formula 1', 'f1', 'grand prix'],
            'chess' => ['chess', 'fide'],
            'boxing' => ['boxing', 'heavyweight'],
            'pickleball' => ['pickleball'],
        ];

        return $keywords[strtolower($category)] ?? [strtolower($category)];
    }
}

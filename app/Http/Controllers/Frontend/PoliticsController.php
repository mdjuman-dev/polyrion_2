<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Market;
use Illuminate\Http\Request;

class PoliticsController extends Controller
{
    /**
     * Display politics page with categories and events
     */
    public function index(Request $request)
    {
        // Base query for politics events - Exclude ended events (reused for counts)
        $baseQuery = Event::whereIn('category', ['Politics', 'Geopolitics', 'Elections'])
            ->where('active', true)
            ->where('closed', false)
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
            });

        // Extract dynamic categories from event titles and market questions (cached)
        $cacheKey = 'politics_dynamic_categories';
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

        // Get popular categories (top 8 by event count)
        $popularCategories = collect($dynamicCategories)
            ->sortByDesc('count')
            ->take(8)
            ->values()
            ->all();

        // Get all categories sorted alphabetically
        $allCategories = collect($dynamicCategories)
            ->sortBy('name')
            ->values()
            ->all();

        // Get selected category and country from query parameters
        $selectedCategory = $request->get('category', 'all');
        $selectedCountry = $request->get('country', null);

        // Get events filtered by politics category - Exclude ended events
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
        }

        // If country selected, filter by country keywords
        if ($selectedCountry && $selectedCountry !== 'all') {
            $countryKeywords = $this->getCountryKeywords($selectedCountry);
            $eventsQuery->where(function ($query) use ($countryKeywords) {
                foreach ($countryKeywords as $keyword) {
                    $query->orWhere('title', 'LIKE', '%' . $keyword . '%');
                }
            });

            $eventsQuery->whereHas('markets', function ($query) use ($countryKeywords) {
                foreach ($countryKeywords as $keyword) {
                    $query->orWhere('question', 'LIKE', '%' . $keyword . '%');
                }
            });
        }

        $events = $eventsQuery->paginate(20);

        // Get countries/regions for category navigation (cached)
        $countriesCacheKey = 'politics_countries_counts';
        $countries = \Illuminate\Support\Facades\Cache::remember($countriesCacheKey, 300, function () use ($baseQuery) {
            // Load only necessary data for country extraction
            $events = (clone $baseQuery)
                ->select(['id', 'title'])
                ->with(['markets' => function($q) {
                    $q->select(['id', 'event_id', 'question']);
                }])
                ->get();
            return $this->getCountriesWithCounts($events);
        });

        return view('frontend.politics', compact(
            'popularCategories',
            'allCategories',
            'selectedCategory',
            'selectedCountry',
            'events',
            'countries'
        ));
    }

    /**
     * Get keywords for a country
     */
    private function getCountryKeywords($countrySlug)
    {
        $countryMap = [
            'us' => ['united states', 'usa', 'us election', 'american', 'america'],
            'uk' => ['united kingdom', 'britain', 'british', 'uk election', 'england', 'scotland', 'wales'],
            'india' => ['india', 'indian', 'indian election'],
            'brazil' => ['brazil', 'brazilian'],
            'france' => ['france', 'french', 'paris'],
            'germany' => ['germany', 'german', 'berlin'],
            'italy' => ['italy', 'italian', 'rome'],
            'spain' => ['spain', 'spanish', 'madrid'],
            'canada' => ['canada', 'canadian'],
            'australia' => ['australia', 'australian'],
            'japan' => ['japan', 'japanese', 'tokyo'],
            'south-korea' => ['south korea', 'korean', 'seoul'],
            'mexico' => ['mexico', 'mexican'],
            'argentina' => ['argentina', 'argentine'],
            'turkey' => ['turkey', 'turkish', 'istanbul'],
            'poland' => ['poland', 'polish', 'warsaw'],
            'ukraine' => ['ukraine', 'ukrainian', 'kyiv'],
            'russia' => ['russia', 'russian', 'moscow'],
            'china' => ['china', 'chinese', 'beijing'],
            'israel' => ['israel', 'israeli', 'tel aviv'],
            'palestine' => ['palestine', 'palestinian', 'gaza'],
            'iran' => ['iran', 'iranian', 'tehran'],
            'saudi-arabia' => ['saudi arabia', 'saudi'],
            'egypt' => ['egypt', 'egyptian', 'cairo'],
            'south-africa' => ['south africa', 'south african'],
            'nigeria' => ['nigeria', 'nigerian'],
            'kenya' => ['kenya', 'kenyan'],
        ];

        return $countryMap[$countrySlug] ?? [$countrySlug];
    }

    /**
     * Extract categories dynamically from event titles and market questions
     */
    private function extractCategoriesFromEvents($events)
    {
        $categories = [];
        $categoryPatterns = [
            'Trump' => ['trump', 'donald trump'],
            'Epstein' => ['epstein', 'jeffrey epstein'],
            'Venezuela' => ['venezuela', 'venezuelan'],
            'Midterms' => ['midterm', 'midterms', 'mid-term'],
            'Primaries' => ['primary', 'primaries', 'primary election'],
            'Minnesota Unrest' => ['minnesota', 'minneapolis'],
            'US Election' => ['us election', 'united states election', 'american election', 'presidential election'],
            'Trade War' => ['trade war', 'trade dispute', 'tariff'],
            'Congress' => ['congress', 'congressional', 'house of representatives', 'senate'],
            'Global Elections' => ['global election', 'international election', 'world election'],
            'Biden' => ['biden', 'joe biden'],
            'Ukraine' => ['ukraine', 'ukrainian', 'kyiv'],
            'Russia' => ['russia', 'russian', 'moscow'],
            'China' => ['china', 'chinese', 'beijing'],
            'Israel' => ['israel', 'israeli', 'tel aviv'],
            'Palestine' => ['palestine', 'palestinian', 'gaza'],
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
     * Get countries/regions with event counts for navigation
     */
    private function getCountriesWithCounts($events)
    {
        $countries = [
            'All' => 'all',
            'US' => ['united states', 'usa', 'us election', 'american', 'america'],
            'UK' => ['united kingdom', 'britain', 'british', 'uk election', 'england', 'scotland', 'wales'],
            'India' => ['india', 'indian', 'indian election'],
            'Brazil' => ['brazil', 'brazilian'],
            'France' => ['france', 'french', 'paris'],
            'Germany' => ['germany', 'german', 'berlin'],
            'Italy' => ['italy', 'italian', 'rome'],
            'Spain' => ['spain', 'spanish', 'madrid'],
            'Canada' => ['canada', 'canadian'],
            'Australia' => ['australia', 'australian'],
            'Japan' => ['japan', 'japanese', 'tokyo'],
            'South Korea' => ['south korea', 'korean', 'seoul'],
            'Mexico' => ['mexico', 'mexican'],
            'Argentina' => ['argentina', 'argentine'],
            'Turkey' => ['turkey', 'turkish', 'istanbul'],
            'Poland' => ['poland', 'polish', 'warsaw'],
            'Ukraine' => ['ukraine', 'ukrainian', 'kyiv'],
            'Russia' => ['russia', 'russian', 'moscow'],
            'China' => ['china', 'chinese', 'beijing'],
            'Israel' => ['israel', 'israeli', 'tel aviv'],
            'Palestine' => ['palestine', 'palestinian', 'gaza'],
            'Iran' => ['iran', 'iranian', 'tehran'],
            'Saudi Arabia' => ['saudi arabia', 'saudi'],
            'Egypt' => ['egypt', 'egyptian', 'cairo'],
            'South Africa' => ['south africa', 'south african'],
            'Nigeria' => ['nigeria', 'nigerian'],
            'Kenya' => ['kenya', 'kenyan'],
        ];

        $countryCounts = [];

        foreach ($countries as $countryName => $keywords) {
            if ($countryName === 'All') {
                $countryCounts[] = [
                    'name' => $countryName,
                    'slug' => 'all',
                    'count' => $events->count(),
                ];
                continue;
            }

            $keywordArray = is_array($keywords) ? $keywords : [$keywords];
            $count = $events->filter(function ($event) use ($keywordArray) {
                $title = strtolower($event->title);
                $hasKeyword = false;

                foreach ($keywordArray as $keyword) {
                    if (strpos($title, strtolower($keyword)) !== false) {
                        $hasKeyword = true;
                        break;
                    }
                }

                if (!$hasKeyword) {
                    foreach ($event->markets as $market) {
                        $question = strtolower($market->question ?? '');
                        foreach ($keywordArray as $keyword) {
                            if (strpos($question, strtolower($keyword)) !== false) {
                                $hasKeyword = true;
                                break 2;
                            }
                        }
                    }
                }

                return $hasKeyword;
            })->count();

            if ($count > 0 || $countryName === 'All') {
                $countryCounts[] = [
                    'name' => $countryName,
                    'slug' => strtolower(str_replace(' ', '-', $countryName)),
                    'count' => $count,
                ];
            }
        }

        return $countryCounts;
    }

    /**
     * Get keywords for a politics category
     */
    private function getCategoryKeywords($category)
    {
        $keywords = [
            'trump' => ['trump', 'donald trump'],
            'epstein' => ['epstein', 'jeffrey epstein'],
            'venezuela' => ['venezuela', 'venezuelan'],
            'midterms' => ['midterm', 'midterms', 'mid-term'],
            'primaries' => ['primary', 'primaries', 'primary election'],
            'minnesota unrest' => ['minnesota', 'minneapolis'],
            'us election' => ['us election', 'united states election', 'american election', 'presidential election'],
            'trade war' => ['trade war', 'trade dispute', 'tariff'],
            'congress' => ['congress', 'congressional', 'house of representatives', 'senate'],
            'global elections' => ['global election', 'international election', 'world election'],
            'biden' => ['biden', 'joe biden'],
            'ukraine' => ['ukraine', 'ukrainian', 'kyiv'],
            'russia' => ['russia', 'russian', 'moscow'],
            'china' => ['china', 'chinese', 'beijing'],
            'israel' => ['israel', 'israeli', 'tel aviv'],
            'palestine' => ['palestine', 'palestinian', 'gaza'],
        ];

        return $keywords[strtolower($category)] ?? [strtolower($category)];
    }
}

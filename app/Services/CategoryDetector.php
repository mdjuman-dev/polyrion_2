<?php

namespace App\Services;

class CategoryDetector
{
    /**
     * Category keyword mappings
     * Each category maps to an array of keywords (case-insensitive)
     * 
     * @var array<string, array<string>>
     */
    protected array $categoryKeywords = [

        // Politics & Elections
        'Politics' => [
            'election',
            'vote',
            'president',
            'trump',
            'biden',
            'senate',
            'congress',
            'democrat',
            'republican',
            'political',
            'politics',
            'candidate',
            'campaign',
            'governor',
            'mayor',
            'senator',
            'representative',
            'ballot',
            'polling'
        ],
        'Elections' => [
            'election',
            'primary',
            'ballot',
            'voting',
            'poll',
            'electoral',
            'referendum',
            'caucus',
            'nomination',
            'candidacy'
        ],

        // Crypto & Finance
        'Crypto' => [
            'bitcoin',
            'crypto',
            'ethereum',
            'btc',
            'eth',
            'blockchain',
            'defi',
            'nft',
            'altcoin',
            'cryptocurrency',
            'wallet',
            'exchange',
            'mining',
            'token',
            'coin',
            'dogecoin',
            'solana',
            'cardano',
            'polygon'
        ],
        'Finance' => [
            'stock',
            'share',
            'market',
            'nasdaq',
            'dow',
            's&p',
            'sp500',
            'trading',
            'investment',
            'portfolio',
            'dividend',
            'ipo',
            'merger',
            'acquisition',
            'bank',
            'banking',
            'financial',
            'economy',
            'recession',
            'inflation',
            'fed',
            'federal reserve',
            'interest rate'
        ],
        'Earnings' => [
            'earnings',
            'revenue',
            'report',
            'quarterly',
            'q1',
            'q2',
            'q3',
            'q4',
            'profit',
            'loss',
            'financial report',
            'earnings call',
            'guidance',
            'beat',
            'miss',
            'eps',
            'revenue growth'
        ],

        // Technology
        'Tech' => [
            'ai',
            'tech',
            'apple',
            'google',
            'microsoft',
            'amazon',
            'meta',
            'facebook',
            'tesla',
            'nvidia',
            'openai',
            'chatgpt',
            'artificial intelligence',
            'software',
            'hardware',
            'startup',
            'silicon valley',
            'innovation',
            'algorithm',
            'machine learning',
            'data science',
            'cloud',
            'aws'
        ],

        // Sports
        'Sports' => [
            'football',
            'cricket',
            'nba',
            'fifa',
            'soccer',
            'basketball',
            'baseball',
            'nfl',
            'mlb',
            'nhl',
            'tennis',
            'golf',
            'olympics',
            'championship',
            'tournament',
            'match',
            'game',
            'player',
            'team',
            'coach',
            'stadium'
        ],

        // Geopolitics & World
        'Geopolitics' => [
            'war',
            'russia',
            'china',
            'israel',
            'conflict',
            'ukraine',
            'gaza',
            'palestine',
            'nato',
            'sanctions',
            'diplomacy',
            'trade war',
            'tension',
            'military',
            'defense',
            'security',
            'international',
            'geopolitical',
            'middle east',
            'europe',
            'asia',
            'america'
        ],
        'World' => [
            'world',
            'global',
            'international',
            'united nations',
            'un',
            'climate',
            'environment',
            'pandemic',
            'covid',
            'health',
            'global economy',
            'worldwide',
            'international news'
        ],

        // Culture / Media / Entertainment
        'Culture' => [
            'culture',
            'media',
            'social',
            'entertainment',
            'movie',
            'film',
            'music',
            'celebrity',
            'tv',
            'television',
            'streaming',
            'netflix',
            'youtube',
            'tiktok',
            'instagram',
            'twitter',
            'x',
            'social media',
            'art',
            'literature',
            'book',
            'award',
            'oscar',
            'grammy'
        ],

        // Business & Economy
        'Business' => [
            'business',
            'company',
            'corporate',
            'entrepreneur',
            'industry',
            'founder',
            'ceo',
            'startup',
            'valuation',
            'company news',
            'hq move'
        ],
        'Economy' => [
            'gdp',
            'inflation',
            'economy',
            'unemployment',
            'jobs report',
            'economic outlook',
            'interest rates',
            'housing market'
        ],

        // Science / Health / Space (Polymarket এ দেখা যায়)
        'Science' => [
            'science',
            'research',
            'study',
            'experiment',
            'laboratory',
            'medicine',
            'vaccine',
            'disease',
            'medical',
            'healthcare',
            'biology',
            'genetics',
            'covid',
            'virus',
            'pandemic'
        ],
        'Space' => [
            'space',
            'nasa',
            'spacex',
            'rocket',
            'launch',
            'mars',
            'moon',
            'satellite',
            'astronaut',
            'orbit',
            'space mission'
        ],

        // Weather
        'Weather' => [
            'weather',
            'temperature',
            'climate',
            'storm',
            'hurricane',
            'rain',
            'flood',
            'heatwave',
            'natural disaster'
        ],

        // Trending
        'Trending' => [
            'trending',
            'viral',
            'hot',
            'breaking',
            'buzz',
            'trending topic'
        ],

    ];

    /**
     * Default category when no keywords match
     */
    protected string $defaultCategory = 'Other';

    /**
     * Detect category from event title
     * 
     * @param string $title The event title to analyze
     * @return string The detected category name
     */
    public function detect(string $title): string
    {
        if (empty(trim($title))) {
            return $this->defaultCategory;
        }

        // Normalize title: lowercase and remove extra spaces
        $normalizedTitle = strtolower(trim($title));

        // Check each category for keyword matches
        foreach ($this->categoryKeywords as $category => $keywords) {
            foreach ($keywords as $keyword) {
                // Check if keyword exists in title (word boundary aware)
                if ($this->containsKeyword($normalizedTitle, $keyword)) {
                    return $category;
                }
            }
        }

        return $this->defaultCategory;
    }

    /**
     * Check if title contains keyword (with word boundary awareness)
     * 
     * @param string $title Normalized title
     * @param string $keyword Keyword to search for
     * @return bool
     */
    protected function containsKeyword(string $title, string $keyword): bool
    {
        // Use word boundary regex for better matching
        // This prevents partial matches (e.g., "bit" matching "bitcoin")
        $pattern = '/\b' . preg_quote($keyword, '/') . '\b/i';

        return preg_match($pattern, $title) === 1;
    }

    /**
     * Get all available categories
     * 
     * @return array<string>
     */
    public function getAvailableCategories(): array
    {
        return array_keys($this->categoryKeywords);
    }

    /**
     * Add a new category or update keywords for existing category
     * 
     * @param string $category Category name
     * @param array<string> $keywords Array of keywords
     * @return self
     */
    public function addCategory(string $category, array $keywords): self
    {
        if (isset($this->categoryKeywords[$category])) {
            // Merge with existing keywords
            $this->categoryKeywords[$category] = array_unique(
                array_merge($this->categoryKeywords[$category], $keywords)
            );
        } else {
            // Add new category
            $this->categoryKeywords[$category] = $keywords;
        }

        return $this;
    }

    /**
     * Get keywords for a specific category
     * 
     * @param string $category Category name
     * @return array<string>|null
     */
    public function getCategoryKeywords(string $category): ?array
    {
        return $this->categoryKeywords[$category] ?? null;
    }
}
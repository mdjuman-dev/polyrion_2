<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Tag;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    function index()
    {
        return view('frontend.home');
    }

    function marketDetails($slug)
    {
        $event = Event::where('slug', $slug)->with('markets')->firstOrFail();
        return view('frontend.market_details', compact('event'));
    }

    function savedEvents()
    {
        return view('frontend.saved_events');
    }

    function eventsByTag($slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        return view('frontend.events_by_tag', compact('tag'));
    }

    function trending()
    {
        return view('frontend.trending');
    }

    function breaking()
    {
        return view('frontend.breaking');
    }

    function newEvents()
    {
        return view('frontend.new');
    }

    function eventsByCategory($category)
    {
        return view('frontend.events_by_category', compact('category'));
    }

    function getMarketPriceData($slug)
    {
        $event = Event::where('slug', $slug)->with('markets')->firstOrFail();
        
        $marketData = [];
        if ($event->markets && $event->markets->count() > 0) {
            $firstMarket = $event->markets->first();
            if ($firstMarket->outcome_prices) {
                $prices = json_decode($firstMarket->outcome_prices, true);
                if (is_array($prices) && isset($prices[0])) {
                    $marketData = [
                        'current_price' => $prices[0] * 100, // Convert to percentage
                        'market_id' => $firstMarket->id,
                        'question' => $firstMarket->question,
                        'volume' => $firstMarket->volume,
                        'created_at' => $firstMarket->created_at ? $firstMarket->created_at->toIso8601String() : null,
                        'updated_at' => $firstMarket->updated_at ? $firstMarket->updated_at->toIso8601String() : null,
                    ];
                }
            }
        }
        
        return response()->json($marketData);
    }
}
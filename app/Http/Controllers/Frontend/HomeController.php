<?php

namespace App\Http\Controllers\Frontend;

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
        $tag = \App\Models\Tag::where('slug', $slug)->firstOrFail();
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
}

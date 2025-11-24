<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    function index()
    {
        $events = Event::with('markets')->paginate(100);
        return view('frontend.home', compact('events'));
    }

    function marketDetails($slug)
    {

        return view('frontend.market_details',);
    }
}

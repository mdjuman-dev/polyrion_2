<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GlobalSettingsController extends Controller
{
    function setting() {
        return view('backend.settings.global_settings');
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    function profile()
    {
        $user = Auth::user();

        $user->load('wallet');

        $wallet = $user->wallet;
        $balance = $wallet ? $wallet->balance : 0;
        $portfolio = $wallet ? ($wallet->portfolio ?? 0) : 0;

        $profileImage = $user->profile_image
            ? asset('storage/' . $user->profile_image)
            : asset('frontend/assets/images/default-avatar.png');

        $stats = [
            'positions_value' => $portfolio,
            'biggest_win' => 0,
            'predictions' => 0,
        ];

        return view('frontend.profile', compact('user', 'wallet', 'balance', 'portfolio', 'profileImage', 'stats'));
    }

    function settings()
    {
        return view('frontend.profile_settings');
    }
}
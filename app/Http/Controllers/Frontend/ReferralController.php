<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    /**
     * Handle referral link - store referrer in session and redirect to register
     * 
     * @param string $username
     * @return \Illuminate\Http\RedirectResponse
     */
    public function referralLink(string $username)
    {
        // Find user by username
        $referrer = User::where('username', $username)->first();

        if ($referrer) {
            // Store referrer_id in session (valid for 24 hours)
            session()->put('referrer_id', $referrer->id);
            session()->put('referrer_username', $referrer->username);
            session()->put('referrer_name', $referrer->name);
        }

        // Redirect to registration page
        return redirect()->route('register')->with('referral', $referrer ? [
            'username' => $referrer->username,
            'name' => $referrer->name,
        ] : null);
    }
}


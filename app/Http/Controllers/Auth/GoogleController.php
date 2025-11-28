<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    public function googleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $email = strtolower($googleUser->getEmail());
            $googleId = $googleUser->getId();

            $user = User::where('google_id', $googleId)->first();

            if ($user) {
                Auth::login($user);
            } else {
                $existingUser = User::where('email', $email)->first();

                if ($existingUser) {
                    $existingUser->update(['google_id' => $googleId]);
                    Auth::login($existingUser);
                } else {
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $email,
                        'google_id' => $googleId,
                        'password' => bcrypt(Str::random(16)),
                        'email_verified_at' => now(),
                    ]);

                    Auth::login($user);
                }
            }

            return redirect()->intended(route('profile.index', absolute: false));
        } catch (\Exception $e) {
            Log::error('Google login failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Google login failed. Please try again.');
        }
    }
}
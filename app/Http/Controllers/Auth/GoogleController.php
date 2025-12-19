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

            // Validate required data
            if (!$googleUser || !$googleUser->getId()) {
                throw new \Exception('Invalid Google user data received');
            }

            $email = $googleUser->getEmail();
            $googleId = $googleUser->getId();
            $name = $googleUser->getName() ?? 'User';

            // Check if email is available (some Google accounts may not have email)
            if (!$email) {
                // Use Google ID as fallback identifier
                $email = 'google_' . $googleId . '@google.oauth';
            } else {
                $email = strtolower($email);
            }

            // Find user by Google ID first
            $user = User::where('google_id', $googleId)->first();

            if ($user) {
                Auth::login($user);
            } else {
                // Try to find existing user by email
                $existingUser = User::where('email', $email)->first();

                if ($existingUser) {
                    // Link Google account to existing user
                    $existingUser->update(['google_id' => $googleId]);
                    Auth::login($existingUser);
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $name,
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
            Log::error('Google login failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login')->with('error', 'Google login failed. Please try again.');
        }
    }
}
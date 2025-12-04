<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function facebookCallback(Request $request)
    {
        try {
            $fbUser = Socialite::driver('facebook')->user();

            $email = strtolower($fbUser->getEmail());
            $facebookId = $fbUser->getId();

            $user = User::where('facebook_id', $facebookId)->first();

            if ($user) {
                Auth::login($user);
            } else {
                $existingUser = User::where('email', $email)->first();

                if ($existingUser) {
                    $existingUser->update(['facebook_id' => $facebookId]);
                    Auth::login($existingUser);
                } else {
                    $user = User::create([
                        'name' => $fbUser->getName(),
                        'email' => $email,
                        'facebook_id' => $facebookId,
                        'password' => bcrypt(Str::random(16)),
                        'email_verified_at' => now(),
                    ]);

                    Auth::login($user);
                }
            }

            return redirect()->intended(route('profile.index', absolute: false));
        } catch (\Exception $e) {
            Log::error('Facebook login failed: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return redirect()->route('login')->with('error', 'Facebook login failed. Please try again.');
        }
    }
}

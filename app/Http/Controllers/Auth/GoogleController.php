<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\GlobalSetting;
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
        try {
            // Get Google OAuth credentials from GlobalSetting or config
            $clientId = GlobalSetting::getValue('google_client_id') ?? config('services.google.client_id');
            $clientSecret = GlobalSetting::getValue('google_client_secret') ?? config('services.google.client_secret');
            $redirect = GlobalSetting::getValue('google_redirect') ?? config('services.google.redirect');

            // Check if credentials are set
            if (empty($clientId) || empty($clientSecret)) {
                Log::error('Google OAuth credentials not configured');
                return redirect()->route('login')
                    ->with('error', 'Google login is not configured. Please contact administrator.');
            }

            // Temporarily override config for this request
            config([
                'services.google.client_id' => $clientId,
                'services.google.client_secret' => $clientSecret,
                'services.google.redirect' => $redirect,
            ]);

            return Socialite::driver('google')
                ->scopes(['openid', 'profile', 'email'])
                ->redirect();
        } catch (\Exception $e) {
            Log::error('Google redirect failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login')
                ->with('error', 'Failed to initiate Google login. Please try again.');
        }
    }
    public function googleCallback(Request $request)
    {
        try {
            // Get Google OAuth credentials
            $clientId = GlobalSetting::getValue('google_client_id') ?? config('services.google.client_id');
            $clientSecret = GlobalSetting::getValue('google_client_secret') ?? config('services.google.client_secret');
            $redirect = GlobalSetting::getValue('google_redirect') ?? config('services.google.redirect');

            // Check if credentials are set
            if (empty($clientId) || empty($clientSecret)) {
                Log::error('Google OAuth credentials not configured in callback');
                return redirect()->route('login')
                    ->with('error', 'Google login is not configured. Please contact administrator.');
            }

            // Temporarily override config for this request
            config([
                'services.google.client_id' => $clientId,
                'services.google.client_secret' => $clientSecret,
                'services.google.redirect' => $redirect,
            ]);

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
            try {
            $user = User::where('google_id', $googleId)->first();
            } catch (\Illuminate\Database\QueryException $e) {
                Log::error('Database connection failed in GoogleController: ' . $e->getMessage());
                return redirect()->route('login')
                    ->with('error', 'Unable to connect to database. Please try again later.');
            }

            if ($user) {
                Auth::login($user);
            } else {
                // Try to find existing user by email
                try {
                $existingUser = User::where('email', $email)->first();
                } catch (\Illuminate\Database\QueryException $e) {
                    Log::error('Database connection failed in GoogleController (email lookup): ' . $e->getMessage());
                    return redirect()->route('login')
                        ->with('error', 'Unable to connect to database. Please try again later.');
                }

                if ($existingUser) {
                    // Link Google account to existing user
                    $existingUser->update(['google_id' => $googleId]);
                    Auth::login($existingUser);
                } else {
                    // Create new user
                    try {
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'google_id' => $googleId,
                        'password' => bcrypt(Str::random(16)),
                        'email_verified_at' => now(),
                    ]);

                    Auth::login($user);
                    } catch (\Illuminate\Database\QueryException $e) {
                        Log::error('Database connection failed in GoogleController (user creation): ' . $e->getMessage());
                        return redirect()->route('login')
                            ->with('error', 'Unable to create user account. Please try again later.');
                    }
                }
            }

            // Redirect to intended page or profile
            $intended = session()->pull('url.intended', route('profile.index'));
            return redirect($intended);
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::error('Google OAuth state mismatch: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Google login session expired. Please try again.');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            Log::error('Google OAuth API error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Google login failed. Please check your credentials.');
        } catch (\Exception $e) {
            Log::error('Google login failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return redirect()->route('login')
                ->with('error', 'Google login failed: ' . $e->getMessage());
        }
    }
}
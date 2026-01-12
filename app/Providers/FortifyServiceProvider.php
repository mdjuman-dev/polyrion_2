<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::requestPasswordResetLinkView(fn() => view('livewire.auth.forgot-password'));
        Fortify::resetPasswordView(fn() => view('livewire.auth.reset-password'));

        // Custom authentication to allow login with email or phone number
        Fortify::authenticateUsing(function (Request $request) {
            try {
                // Verify Cloudflare reCAPTCHA if enabled
                if (\App\Services\CloudflareRecaptchaService::isEnabled()) {
                    $token = $request->input('cf_turnstile_response');
                    if (empty($token) || !\App\Services\CloudflareRecaptchaService::verify($token, $request->ip())) {
                        // Add error to session and return null
                        session()->flash('error', 'reCAPTCHA verification failed. Please complete the verification and try again.');
                        return null;
                    }
                }

                // Get login input (Fortify uses 'email' field by default)
                $login = trim($request->input('email') ?? '');
                $password = $request->input('password');

                // Early return for empty inputs
                if (empty($login) || empty($password)) {
                    return null;
                }

                // Validate and limit input length for security
                if (strlen($login) > 255 || strlen($password) > 255) {
                    return null;
                }

                $user = null;

                // Check if input is email format
                $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

                if ($isEmail) {
                    // Normalize email to lowercase for consistent matching
                    $normalizedEmail = strtolower($login);
                    // Search by email (most common case, check first)
                    $user = User::where('email', $normalizedEmail)->first();
                } else {
                    // Phone number authentication
                    // Try exact match first (fastest)
                    $user = User::where('number', $login)->first();

                    // If not found, try normalized phone number match
                    if (!$user) {
                        $normalizedLogin = preg_replace('/[\s\-\(\)\+]/', '', $login);

                        // Validate normalized phone number length
                        if (!empty($normalizedLogin) && strlen($normalizedLogin) >= 10 && strlen($normalizedLogin) <= 20) {
                            // Optimized database query for normalized phone number matching
                            // This is more efficient than loading all users
                            $user = User::whereNotNull('number')
                                ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(number, ' ', ''), '-', ''), '(', ''), ')', ''), '+', '') = ?", [$normalizedLogin])
                                ->first();
                        }
                    }
                }

                // Verify password if user found
                if ($user && Hash::check($password, $user->password)) {
                    return $user;
                }

                return null;
            } catch (\Exception $e) {
                // Log error for production debugging (silent failure for security)
                Log::error('Authentication error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn() => view('livewire.auth.login'));
        Fortify::verifyEmailView(fn() => view('livewire.auth.verify-email'));
        Fortify::twoFactorChallengeView(fn() => view('livewire.auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn() => view('livewire.auth.confirm-password'));
        Fortify::registerView(fn() => view('livewire.auth.register'));
        Fortify::resetPasswordView(fn() => view('livewire.auth.reset-password'));
        Fortify::requestPasswordResetLinkView(fn() => view('livewire.auth.forgot-password'));
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $login = $request->input('email') ?? '';
            $throttleKey = Str::transliterate(Str::lower($login) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}

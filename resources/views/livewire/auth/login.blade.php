<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <div class="auth-title-section">
            <h1>{{ __('Log in to your account') }}</h1>
            <p>{{ __('Enter your registered email or phone number and password below to log in') }}</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="auth-alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="auth-alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="auth-alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email or Phone Number -->
            <div class="auth-form-group">
                <label for="email">{{ __('Email or Phone Number') }}</label>
                <input type="text" name="email" id="email" value="{{ old('email') }}" required autofocus
                    autocomplete="username" placeholder="email or phone number" class="auth-input" />
                @error('email')
                    <span class="auth-error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="auth-form-group">
                <div class="auth-form-label-row">
                    <label for="password">{{ __('Password') }}</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="auth-forgot-link">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>
                <div class="auth-password-wrapper">
                    <input type="password" name="password" id="password" required autocomplete="current-password"
                        placeholder="{{ __('Password') }}" class="auth-input" />
                    <button type="button" class="auth-password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye" id="password-eye-icon"></i>
                    </button>
                </div>
                @error('password')
                    <span class="auth-error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="auth-form-group">
                <label class="auth-checkbox-label">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} />
                    <span>{{ __('Remember me') }}</span>
                </label>
            </div>

            <!-- Cloudflare Turnstile reCAPTCHA -->
            @php
                $recaptchaService = new \App\Services\CloudflareRecaptchaService();
                $siteKey = $recaptchaService::getSiteKey();
            @endphp
            @if(!empty($siteKey))
                <div class="auth-form-group">
                    <div id="cf-turnstile-widget" data-sitekey="{{ $siteKey }}" data-theme="dark"></div>
                    <input type="hidden" name="cf_turnstile_response" id="cf_turnstile_response">
                </div>
            @endif

            <button type="submit" class="auth-submit-btn" data-test="login-button">
                {{ __('Log in') }}
            </button>
        </form>

        <div class="auth-divider m-0">
            <span>{{ __('OR') }}</span>
        </div>

        <div class="auth-social-buttons">
            <a href="{{ route('google.redirect') }}" class="auth-social-btn">
                <i class="fab fa-google"></i>
                <span>{{ __('Continue with Google') }}</span>
            </a>
            {{-- <a href="{{ route('facebook.redirect') }}" class="auth-social-btn">
                <i class="fab fa-facebook-f"></i>
                <span>{{ __('Continue with Facebook') }}</span>
            </a> --}}
        </div>

        @if (Route::has('register'))
            <div class="auth-footer-link">
                <span>{{ __('Don\'t have an account?') }}</span>
                <a href="{{ route('register') }}">{{ __('Sign up') }}</a>
            </div>
        @endif
    </div>

    @php
        $recaptchaService = new \App\Services\CloudflareRecaptchaService();
        $siteKey = $recaptchaService::getSiteKey();
    @endphp
    @if(!empty($siteKey))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                // Wait for Turnstile to load
                if (typeof turnstile !== 'undefined') {
                    const widgetId = turnstile.render('#cf-turnstile-widget', {
                        sitekey: '{{ $siteKey }}',
                        theme: 'dark',
                        callback: function(token) {
                            document.getElementById('cf_turnstile_response').value = token;
                        },
                        'error-callback': function() {
                            document.getElementById('cf_turnstile_response').value = '';
                        }
                    });
                }
            });
        </script>
    @endif

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-eye-icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</x-layouts.auth>

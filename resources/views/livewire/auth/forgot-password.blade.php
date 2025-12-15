<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <div class="auth-title-section">
            <h1>{{ __('Forgot Password') }}</h1>
            <p>{{ __('Enter your email address and we will send you a password reset link') }}</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="auth-alert alert-success">
                {{ session('status') }}
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

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <div class="auth-form-group">
                <label for="email">{{ __('Email Address') }}</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    autocomplete="email" placeholder="email@example.com" class="auth-input" />
                @error('email')
                    <span class="auth-error-text">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="auth-submit-btn" data-test="email-password-reset-link-button">
                <i class="fas fa-paper-plane me-2"></i> {{ __('Send Password Reset Link') }}
            </button>
        </form>

        <div class="auth-footer-link">
            <span>{{ __('Remember your password?') }}</span>
            <a href="{{ route('login') }}">{{ __('Log in') }}</a>
        </div>
    </div>
</x-layouts.auth>

<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <div class="auth-title-section">
            <h1>{{ __('Reset Password') }}</h1>
            <p>{{ __('Please enter your new password below') }}</p>
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

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Token -->
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <!-- Email Address -->
            <div class="auth-form-group">
                <label for="email">{{ __('Email Address') }}</label>
                <input type="email" name="email" id="email" value="{{ old('email', request('email')) }}"
                    required autocomplete="email" placeholder="email@example.com" class="auth-input" />
                @error('email')
                    <span class="auth-error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="auth-form-group">
                <label for="password">{{ __('New Password') }}</label>
                <div class="auth-password-wrapper">
                    <input type="password" name="password" id="password" required autocomplete="new-password"
                        placeholder="{{ __('Password') }}" class="auth-input" />
                    <button type="button" class="auth-password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye" id="password-eye-icon"></i>
                    </button>
                </div>
                @error('password')
                    <span class="auth-error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="auth-form-group">
                <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                <div class="auth-password-wrapper">
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        autocomplete="new-password" placeholder="{{ __('Confirm Password') }}" class="auth-input" />
                    <button type="button" class="auth-password-toggle"
                        onclick="togglePassword('password_confirmation')">
                        <i class="fas fa-eye" id="password_confirmation-eye-icon"></i>
                    </button>
                </div>
                @error('password_confirmation')
                    <span class="auth-error-text">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="auth-submit-btn" data-test="reset-password-button">
                <i class="fas fa-key me-2"></i> {{ __('Reset Password') }}
            </button>
        </form>

        <div class="auth-footer-link">
            <span>{{ __('Remember your password?') }}</span>
            <a href="{{ route('login') }}">{{ __('Log in') }}</a>
        </div>
    </div>

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

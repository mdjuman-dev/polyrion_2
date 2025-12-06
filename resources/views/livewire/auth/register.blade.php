<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <div class="auth-title-section">
            <h1>{{ __('Create an account') }}</h1>
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

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Name -->
            <div class="auth-form-group">
                <label for="name">{{ __('Name') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                    autocomplete="name" placeholder="{{ __('Full name') }}" class="auth-input" />
                @error('name')
                    <span class="auth-error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email or Phone Number -->
            <div class="auth-form-group">
                <label for="email_or_number">{{ __('Email or Phone Number') }}</label>
                <input type="text" name="email_or_number" id="email_or_number" value="{{ old('email_or_number') }}"
                    required autocomplete="username" placeholder="email or phone number" class="auth-input" />
                @error('email_or_number')
                    <span class="auth-error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="auth-form-group">
                <label for="password">{{ __('Password') }}</label>
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
                <label for="password_confirmation">{{ __('Confirm password') }}</label>
                <div class="auth-password-wrapper">
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        autocomplete="new-password" placeholder="{{ __('Confirm password') }}" class="auth-input" />
                    <button type="button" class="auth-password-toggle"
                        onclick="togglePassword('password_confirmation')">
                        <i class="fas fa-eye" id="password_confirmation-eye-icon"></i>
                    </button>
                </div>
                @error('password_confirmation')
                    <span class="auth-error-text">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="auth-submit-btn" data-test="register-user-button">
                {{ __('Create account') }}
            </button>
        </form>

        <div class="auth-divider m-0">
            <span>{{ __('OR') }}</span>
        </div>

        <div class="auth-social-buttons  ">
            <a href="{{ route('google.redirect') }}" class="auth-social-btn">
                <i class="fab fa-google"></i>
            </a>
            <a href="{{ route('facebook.redirect') }}" class="auth-social-btn ">
                <i class="fab fa-facebook-f"></i>
            </a>
        </div>

        <div class="auth-footer-link">
            <span>{{ __('Already have an account?') }}</span>
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

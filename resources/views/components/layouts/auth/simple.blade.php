<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? config('app.name') }} - {{ config('app.name', 'Polyrion') }}</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/responsive.css') }}">
        @livewireStyles
        @fluxAppearance
        @stack('style')
    </head>
    <body class="dark-theme">
        <div class="auth-page-wrapper">
            <div class="auth-container">
                <div class="auth-header">
                    <a href="{{ route('home') }}" class="auth-logo">
                        <div class="logo-icon"><i class="fas fa-chart-line"></i></div>
                        <span>Polyrion</span>
                    </a>
                </div>
                <div class="auth-card">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
        @livewireScripts
        @stack('script')
    </body>
</html>

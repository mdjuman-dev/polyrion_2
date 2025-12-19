@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Payment Cancelled - {{ $appName }}</title>
    <meta name="description" content="Payment cancelled page for {{ $appName }}.">
@endsection
@section('content')
    <main>
        <div class="container">
            <div class="row justify-content-center mt-5">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body text-center p-5">
                            <div class="mb-4">
                                <i class="fas fa-times-circle text-warning" style="font-size: 64px;"></i>
                            </div>
                            <h3 class="text-warning mb-3">Payment Cancelled</h3>
                            <p class="text-muted mb-4">
                                Your payment has been cancelled. No charges were made to your account.
                            </p>
                            <p class="text-muted small mb-4">
                                If you want to complete the payment, please try again from your wallet.
                            </p>

                            <div class="mt-4">
                                @if (auth()->check())
                                    <a href="{{ route('profile.index') }}" class="btn btn-primary me-2">
                                        <i class="fas fa-wallet"></i> Go to Wallet
                                    </a>
                                @endif
                                <a href="{{ route('home') }}" class="btn btn-secondary">
                                    <i class="fas fa-home"></i> Go to Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

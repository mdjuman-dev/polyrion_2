@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Payment Return - {{ $appName }}</title>
    <meta name="description" content="Payment return page for {{ $appName }}.">
@endsection
@section('content')
    <main>
        <div class="container">
            <div class="row justify-content-center mt-5">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body text-center p-5">
                            @if ($processed && $status === 'SUCCESS')
                                <div class="mb-4">
                                    <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
                                </div>
                                <h3 class="text-success mb-3">Payment Successful!</h3>
                                <p class="text-muted mb-4">
                                    {{ $message ?? 'Your payment has been processed successfully and funds have been added to your account.' }}
                                </p>
                                @if (auth()->check())
                                    @php
                                        $wallet = auth()->user()->wallet;
                                    @endphp
                                    @if ($wallet)
                                        <div class="alert alert-info">
                                            <strong>New Balance:</strong> ${{ number_format($wallet->balance, 2) }}
                                        </div>
                                    @endif
                                @endif
                            @elseif($status === 'SUCCESS')
                                <div class="mb-4">
                                    <i class="fas fa-info-circle text-info" style="font-size: 64px;"></i>
                                </div>
                                <h3 class="text-info mb-3">Payment Verified</h3>
                                <p class="text-muted mb-4">{{ $message ?? 'Your payment has been verified.' }}</p>
                            @else
                                <div class="mb-4">
                                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 64px;"></i>
                                </div>
                                <h3 class="text-warning mb-3">Payment Status</h3>
                                <p class="text-muted mb-4">
                                    {{ $message ?? 'We are processing your payment. Please wait a moment or contact support if you have any concerns.' }}
                                </p>
                            @endif

                            @if ($merchantTradeNo)
                                <div class="mb-3">
                                    <small class="text-muted">Transaction ID: {{ $merchantTradeNo }}</small>
                                </div>
                            @endif

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

                            @if (!$processed && auth()->check())
                                <div class="mt-4">
                                    <p class="text-muted small">
                                        If your payment was successful but funds are not showing, please use the manual
                                        verification option in your wallet.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Auto-refresh wallet balance if user is logged in
        @if (auth()->check() && $processed)
            // Trigger a page refresh after 2 seconds to update wallet balance in navigation
            setTimeout(function() {
                if (typeof Livewire !== 'undefined') {
                    Livewire.emit('refreshWallet');
                }
            }, 2000);
        @endif
    </script>
@endsection

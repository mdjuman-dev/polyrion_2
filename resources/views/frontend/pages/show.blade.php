@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>{{ $title }} - {{ $appName }}</title>
    <meta name="description" content="{{ $title }} on {{ $appName }}.">
@endsection
@section('content')
    <main>
        <div class="container mt-5 mb-5">
            <div class="row">
                <div class="col-12">
                    <div class="page-content-card" style="background: var(--card-bg); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 2rem;">
                        <h1 class="page-title" style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                            {{ $title }}
                        </h1>
                        <div class="page-content" style="color: var(--text-primary); line-height: 1.8; font-size: 1rem;">
                            @if($content)
                                {!! $content !!}
                            @else
                                <p style="color: var(--text-secondary); font-style: italic;">Content coming soon...</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection


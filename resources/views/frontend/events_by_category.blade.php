@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>{{ ucfirst($category) }} Events - {{ $appName }}</title>
    <meta name="description" content="Explore {{ strtolower($category) }} prediction markets and events on {{ $appName }}.">
    <meta property="og:title" content="{{ ucfirst($category) }} Events - {{ $appName }}">
    <link rel="canonical" href="{{ $appUrl }}/category/{{ $category }}">
@endsection
@section('content')
    <!-- Main Content -->
    <main>
        <div class="container">
            <livewire:category-events-grid :category="$category" />
        </div>
    </main>
@endsection


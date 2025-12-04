@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>{{ ucfirst($category) }} Events - Polyrion</title>
@endsection
@section('content')
    <!-- Main Content -->
    <main>
        <div class="container">
            <livewire:category-events-grid :category="$category" />
        </div>
    </main>
@endsection


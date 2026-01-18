@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Election odds & predictions - {{ $appName }}</title>
    <meta name="description" content="Explore election prediction markets and odds on {{ $appName }}. Track presidential, parliamentary, and legislative elections worldwide.">
    <meta property="og:title" content="Election odds & predictions - {{ $appName }}">
    <link rel="canonical" href="{{ $appUrl }}/category/elections">
@endsection

@section('content')
<main class="elections-page">
    <div class="container">
        <div class="elections-content">
            <div class="page-header">
                <h1 class="page-title">Election odds & predictions</h1>
                <div class="polymarket-brand">
                    <i class="fas fa-chart-bar"></i>
                    <span>Polymarket</span>
                </div>
            </div>

            <!-- Election Cards Grid -->
            <div class="elections-grid">
                @forelse($events as $event)
                    <x-election-card :event="$event" :keyPrefix="'election'" />
                @empty
                    <div class="no-elections">
                        <i class="fas fa-vote-yea"></i>
                        <p>No upcoming elections found</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($events->hasPages())
                <div class="elections-pagination">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</main>

<style>
.elections-page {
    min-height: 100vh;
    padding: 20px 0;
}

/* Main Content Styles */
.elections-content {
    padding: 0;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.page-title {
    font-size: 32px;
    font-weight: 700;
    color: #fff;
    margin: 0;
}

.polymarket-brand {
    display: flex;
    align-items: center;
    gap: 8px;
    color: rgba(156, 163, 175, 0.7);
    font-size: 18px;
    font-weight: 600;
}

.polymarket-brand i {
    font-size: 20px;
}

/* Elections Grid */
.elections-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 40px;
}

.no-elections {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: rgba(156, 163, 175, 0.7);
}

.no-elections i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
}

.no-elections p {
    font-size: 18px;
    margin: 0;
}

/* Pagination */
.elections-pagination {
    display: flex;
    justify-content: center;
    padding: 20px 0;
}

/* Responsive */
@media (max-width: 1400px) {
    .elections-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 1200px) {
    .elections-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 991px) {
    .page-title {
        font-size: 28px;
    }
    
    .elections-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .page-title {
        font-size: 24px;
    }
    
    .polymarket-brand {
        font-size: 16px;
    }
    
    .elections-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
}

@media (max-width: 480px) {
    .elections-page {
        padding: 10px 0;
    }
    
    .page-title {
        font-size: 20px;
    }
}
</style>
@endsection


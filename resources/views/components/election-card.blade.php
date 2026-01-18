@props(['event', 'keyPrefix' => 'election'])

@php
    // Get active markets
    $activeMarkets = $event->markets->filter(function($market) {
        return $market->active && !$market->closed;
    })->sortByDesc(function($market) {
        // Sort by percentage (highest first)
        $prices = is_string($market->outcome_prices) 
            ? json_decode($market->outcome_prices, true) 
            : ($market->outcome_prices ?? [0.5, 0.5]);
        return isset($prices[1]) ? floatval($prices[1]) : 0.5;
    });
    
    $marketLink = $activeMarkets->count() === 1 
        ? route('market.single', $activeMarkets->first()->slug) 
        : route('market.details', $event->slug);
    
    // Parse event date
    $eventDate = $event->end_date ? \Carbon\Carbon::parse($event->end_date) : null;
@endphp

<a href="{{ $marketLink }}" class="election-card">
    <div class="election-card-date">
        @if($eventDate)
            <div class="date-day">{{ $eventDate->format('d') }}</div>
            <div class="date-month">{{ $eventDate->format('M') }}</div>
        @endif
    </div>
    
    <div class="election-card-content">
        <div class="election-header">
            <div class="election-info">
                <h3 class="election-title">{{ $event->title }}</h3>
                @if($event->category || $event->secondary_category_id)
                    @php
                        $categoryLabel = 'Presidential';
                        if ($event->secondary_category_id) {
                            $secCat = \App\Models\SecondaryCategory::find($event->secondary_category_id);
                            if ($secCat) {
                                $categoryLabel = $secCat->name;
                            }
                        }
                    @endphp
                    <span class="election-type">{{ $categoryLabel }}</span>
                @endif
            </div>
            @php
                // Check for event icon or image
                $flagImage = null;
                if (!empty($event->icon)) {
                    $flagImage = str_starts_with($event->icon, 'http') ? $event->icon : asset('storage/' . $event->icon);
                } elseif (!empty($event->image)) {
                    $flagImage = str_starts_with($event->image, 'http') ? $event->image : asset('storage/' . $event->image);
                }
            @endphp
            @if($flagImage)
                <div class="election-flag">
                    <img src="{{ $flagImage }}" 
                         alt="{{ $event->title }}"
                         onerror="this.parentElement.style.display='none'">
                </div>
            @endif
        </div>
        
        <div class="election-predictions">
            @foreach($activeMarkets->take(4) as $index => $market)
                @php
                    // Handle both string (JSON) and array formats
                    $prices = is_string($market->outcome_prices) 
                        ? json_decode($market->outcome_prices, true) 
                        : ($market->outcome_prices ?? [0.5, 0.5]);
                    
                    $yesPrice = isset($prices[1]) ? floatval($prices[1]) : 0.5;
                    
                    // Use best_ask if available
                    if ($market->best_ask !== null && $market->best_ask > 0) {
                        $yesPrice = floatval($market->best_ask);
                    }
                    
                    $yesPrice = max(0.001, min(0.999, $yesPrice));
                    $yesProb = round($yesPrice * 100);
                    
                    $outcomes = is_string($market->outcomes) 
                        ? json_decode($market->outcomes, true) 
                        : ($market->outcomes ?? []);
                    
                    if (empty($outcomes) || !is_array($outcomes)) {
                        $outcomes = ['Yes', 'No'];
                    }
                    
                    $candidateName = $market->groupItem_title ?: ($outcomes[0] ?? 'Yes');
                    
                    // Get market icon - check multiple sources
                    $candidateIcon = null;
                    if (!empty($market->icon)) {
                        $candidateIcon = str_starts_with($market->icon, 'http') 
                            ? $market->icon 
                            : asset('storage/' . $market->icon);
                    } elseif (!empty($market->image)) {
                        $candidateIcon = str_starts_with($market->image, 'http') 
                            ? $market->image 
                            : asset('storage/' . $market->image);
                    }
                    
                    // Highlight top candidate
                    $isTopCandidate = $index === 0;
                @endphp
                
                <div class="prediction-item {{ $isTopCandidate ? 'prediction-top' : 'prediction-normal' }}">
                    <div class="prediction-percentage">{{ $yesProb }}%</div>
                    <div class="prediction-info">
                        @if($candidateIcon)
                            <div class="prediction-avatar">
                                <img src="{{ $candidateIcon }}" 
                                     alt="{{ $candidateName }}" 
                                     onerror="this.parentElement.classList.add('prediction-avatar-placeholder'); this.parentElement.innerHTML='<i class=\'fas fa-user\'></i>'">
                            </div>
                        @else
                            <div class="prediction-avatar-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                        <div class="prediction-name">{{ \Illuminate\Support\Str::limit($candidateName, 35) }}</div>
                    </div>
                    <div class="prediction-bar" style="width: {{ $yesProb }}%"></div>
                </div>
            @endforeach
        </div>
    </div>
</a>

<style>
.election-card {
    display: flex;
    gap: 16px;
    padding: 20px;
    background: rgba(17, 24, 39, 0.8);
    border: 1px solid rgba(75, 85, 99, 0.3);
    border-radius: 12px;
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
    position: relative;
    overflow: hidden;
}

.election-card:hover {
    background: rgba(17, 24, 39, 0.95);
    border-color: rgba(56, 189, 248, 0.5);
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
}

.election-card-date {
    flex-shrink: 0;
    width: 70px;
    text-align: center;
    padding: 10px;
    background: rgba(31, 41, 55, 0.9);
    border-radius: 8px;
    border: 1px solid rgba(75, 85, 99, 0.4);
}

.date-day {
    font-size: 32px;
    font-weight: 800;
    color: #fff;
    line-height: 1;
}

.date-month {
    font-size: 13px;
    font-weight: 600;
    color: rgba(156, 163, 175, 0.9);
    margin-top: 4px;
    text-transform: uppercase;
}

.election-card-content {
    flex: 1;
    min-width: 0;
}

.election-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
    gap: 12px;
}

.election-info {
    flex: 1;
    min-width: 0;
}

.election-title {
    font-size: 17px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 6px 0;
    line-height: 1.3;
}

.election-type {
    display: inline-block;
    font-size: 12px;
    color: rgba(156, 163, 175, 0.8);
    font-weight: 500;
}

.election-flag {
    flex-shrink: 0;
    width: 72px;
    height: 54px;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid rgba(75, 85, 99, 0.4);
}

.election-flag img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.election-predictions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.prediction-item {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    background: rgba(31, 41, 55, 0.6);
    border-radius: 8px;
    border: 1px solid rgba(75, 85, 99, 0.3);
    transition: all 0.2s ease;
}

.prediction-item:hover {
    background: rgba(31, 41, 55, 0.8);
    border-color: rgba(75, 85, 99, 0.5);
}

.prediction-item.prediction-top {
    background: rgba(56, 189, 248, 0.15);
    border-color: rgba(56, 189, 248, 0.4);
    border-left: 3px solid rgba(56, 189, 248, 0.8);
}

.prediction-item.prediction-top .prediction-bar {
    background: linear-gradient(90deg, rgba(56, 189, 248, 0.4), rgba(56, 189, 248, 0.1));
}

.prediction-item.prediction-normal .prediction-bar {
    background: linear-gradient(90deg, rgba(75, 85, 99, 0.3), rgba(75, 85, 99, 0.1));
}

.prediction-percentage {
    flex-shrink: 0;
    width: 48px;
    font-size: 16px;
    font-weight: 700;
    color: #fff;
    text-align: center;
}

.prediction-info {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 0;
    position: relative;
    z-index: 1;
}

.prediction-avatar,
.prediction-avatar-placeholder {
    flex-shrink: 0;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    overflow: hidden;
    background: rgba(75, 85, 99, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(75, 85, 99, 0.5);
}

.prediction-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.prediction-avatar-placeholder i {
    font-size: 14px;
    color: rgba(156, 163, 175, 0.6);
}

.prediction-name {
    font-size: 14px;
    font-weight: 600;
    color: #fff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.prediction-bar {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    border-radius: 8px;
    transition: width 0.5s ease;
    z-index: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .election-card {
        flex-direction: column;
        gap: 12px;
        padding: 16px;
    }
    
    .election-card-date {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px;
    }
    
    .date-day {
        font-size: 28px;
    }
    
    .date-month {
        font-size: 12px;
        margin-top: 0;
    }
    
    .election-title {
        font-size: 16px;
    }
    
    .election-flag {
        width: 64px;
        height: 48px;
    }
    
    .prediction-percentage {
        width: 44px;
        font-size: 15px;
    }
    
    .prediction-name {
        font-size: 13px;
    }
    
    .prediction-avatar,
    .prediction-avatar-placeholder {
        width: 28px;
        height: 28px;
    }
}

@media (max-width: 480px) {
    .election-card {
        padding: 14px;
    }
    
    .prediction-item {
        padding: 9px 10px;
        gap: 8px;
    }
    
    .prediction-percentage {
        width: 40px;
        font-size: 14px;
    }
    
    .prediction-avatar,
    .prediction-avatar-placeholder {
        width: 26px;
        height: 26px;
    }
    
    .prediction-name {
        font-size: 12px;
    }
}
</style>


{{-- Low-Context Trade Card Component --}}
{{-- Shows minimal info by default, expands on hover/click --}}

@props(['trade'])

@php
    $status = strtoupper($trade->status ?? 'PENDING');
    $isPending = $status === 'PENDING';
    $isWon = in_array($status, ['WON', 'WIN']);
    $isLost = in_array($status, ['LOST', 'LOSS']);
    
    $statusConfig = [
        'PENDING' => [
            'color' => '#f59e0b',
            'bg' => '#fef3c7',
            'icon' => '⏳',
            'label' => 'Open',
            'text' => 'Pending'
        ],
        'WON' => [
            'color' => '#10b981',
            'bg' => '#d1fae5',
            'icon' => '✓',
            'label' => 'Won',
            'text' => 'Won'
        ],
        'LOST' => [
            'color' => '#ef4444',
            'bg' => '#fee2e2',
            'icon' => '✗',
            'label' => 'Lost',
            'text' => 'Lost'
        ],
    ];
    
    $config = $statusConfig[$status] ?? $statusConfig['PENDING'];
    $profit = ($trade->payout ?? 0) - ($trade->amount_invested ?? 0);
@endphp

<div class="trade-card" 
     data-trade-id="{{ $trade->id }}"
     data-status="{{ strtolower($status) }}"
     x-data="{ 
         showDetails: false,
         detailsLoaded: false,
         loading: false
     }"
     @mouseenter="if (!detailsLoaded) { loadDetails(); } showDetails = true"
     @mouseleave="showDetails = false"
     style="
         position: relative;
         padding: 0.75rem;
         border: 1px solid #e5e7eb;
         border-radius: 8px;
         background: white;
         cursor: pointer;
         transition: all 0.2s;
     "
     @mouseover="this.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)'"
     @mouseout="this.style.boxShadow = 'none'">
    
    {{-- Compact View (Always Visible) --}}
    <div class="trade-compact" style="display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
        {{-- Left: Outcome & Amount --}}
        <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1;">
            <span style="
                font-weight: 600;
                font-size: 0.875rem;
                padding: 0.25rem 0.5rem;
                border-radius: 4px;
                background: {{ $trade->outcome === 'YES' ? '#10b98120' : '#3b82f620' }};
                color: {{ $trade->outcome === 'YES' ? '#10b981' : '#3b82f6' }};
            ">
                {{ $trade->outcome }}
            </span>
            
            <span style="font-weight: 600; color: #111827;">
                ${{ number_format($trade->amount_invested ?? 0, 2) }}
            </span>
        </div>
        
        {{-- Right: Status Badge --}}
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            @if($isWon && $trade->payout)
                <span style="
                    font-size: 0.75rem;
                    color: #10b981;
                    font-weight: 600;
                ">
                    +${{ number_format($profit, 2) }}
                </span>
            @endif
            
            <span class="status-badge" style="
                display: inline-flex;
                align-items: center;
                gap: 0.25rem;
                padding: 0.25rem 0.5rem;
                border-radius: 6px;
                font-size: 0.75rem;
                font-weight: 600;
                background: {{ $config['bg'] }};
                color: {{ $config['color'] }};
                border: 1px solid {{ $config['color'] }}40;
            ">
                <span>{{ $config['icon'] }}</span>
                <span>{{ $config['label'] }}</span>
            </span>
        </div>
    </div>
    
    {{-- Expanded Details (On Hover) --}}
    <div x-show="showDetails" 
         x-transition
         style="
             margin-top: 0.75rem;
             padding-top: 0.75rem;
             border-top: 1px solid #e5e7eb;
             display: none;
         "
         x-cloak>
        
        <div x-show="loading" style="text-align: center; padding: 1rem; color: #6b7280;">
            Loading details...
        </div>
        
        <div x-show="!loading && detailsLoaded" class="trade-details" style="
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            font-size: 0.875rem;
        ">
            <div>
                <span style="color: #6b7280;">Market:</span>
                <span style="font-weight: 500; margin-left: 0.5rem;">
                    {{ Str::limit($trade->market->question ?? 'N/A', 40) }}
                </span>
            </div>
            
            <div>
                <span style="color: #6b7280;">Price at Buy:</span>
                <span style="font-weight: 500; margin-left: 0.5rem;">
                    {{ number_format(($trade->price_at_buy ?? 0) * 100, 2) }}¢
                </span>
            </div>
            
            <div>
                <span style="color: #6b7280;">Tokens:</span>
                <span style="font-weight: 500; margin-left: 0.5rem;">
                    {{ number_format($trade->token_amount ?? 0, 4) }}
                </span>
            </div>
            
            @if($isWon || $isLost)
                <div>
                    <span style="color: #6b7280;">Payout:</span>
                    <span style="font-weight: 500; margin-left: 0.5rem; color: {{ $isWon ? '#10b981' : '#6b7280' }};">
                        ${{ number_format($trade->payout ?? 0, 2) }}
                    </span>
                </div>
                
                <div>
                    <span style="color: #6b7280;">Profit/Loss:</span>
                    <span style="font-weight: 600; margin-left: 0.5rem; color: {{ $profit >= 0 ? '#10b981' : '#ef4444' }};">
                        {{ $profit >= 0 ? '+' : '' }}${{ number_format($profit, 2) }}
                    </span>
                </div>
                
                @if($trade->settled_at)
                    <div>
                        <span style="color: #6b7280;">Settled:</span>
                        <span style="font-weight: 500; margin-left: 0.5rem;">
                            {{ $trade->settled_at->format('M d, Y H:i') }}
                        </span>
                    </div>
                @endif
            @else
                <div>
                    <span style="color: #6b7280;">Potential Payout:</span>
                    <span style="font-weight: 500; margin-left: 0.5rem;">
                        ${{ number_format(($trade->token_amount ?? 0) * 1.00, 2) }}
                    </span>
                </div>
            @endif
            
            <div>
                <span style="color: #6b7280;">Placed:</span>
                <span style="font-weight: 500; margin-left: 0.5rem;">
                    {{ $trade->created_at->format('M d, Y H:i') }}
                </span>
            </div>
        </div>
    </div>
</div>

<script>
function loadTradeDetails(tradeId) {
    const card = document.querySelector(`[data-trade-id="${tradeId}"]`);
    if (!card) return;
    
    const alpine = Alpine.$data(card);
    if (alpine.detailsLoaded) return;
    
    alpine.loading = true;
    
    fetch(`/api/trades/${tradeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Details are already rendered in blade, just mark as loaded
                alpine.detailsLoaded = true;
                alpine.loading = false;
            }
        })
        .catch(error => {
            console.error('Failed to load trade details:', error);
            alpine.loading = false;
        });
}
</script>


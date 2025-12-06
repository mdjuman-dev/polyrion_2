@extends('frontend.layout.frontend')

@section('content')
<div class="container" style="padding: 2rem 1rem; max-width: 1200px; margin: 0 auto;">
    <div class="trades-history-page">
        <div class="page-header" style="margin-bottom: 2rem;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                <i class="fas fa-chart-line"></i> My Trades History
            </h1>
            <p style="color: var(--text-secondary);">Track all your trading activity</p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div class="stat-card" style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; text-align: center;">
                <div class="stat-icon" style="font-size: 2rem; color: var(--accent); margin-bottom: 0.5rem;">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="stat-value" style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">
                    {{ $totalTrades }}
                </div>
                <div class="stat-label" style="color: var(--text-secondary); font-size: 0.9rem;">
                    Total Trades
                </div>
            </div>

            <div class="stat-card" style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; text-align: center;">
                <div class="stat-icon" style="font-size: 2rem; color: #10b981; margin-bottom: 0.5rem;">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-value" style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">
                    ${{ number_format($totalAmount, 2) }}
                </div>
                <div class="stat-label" style="color: var(--text-secondary); font-size: 0.9rem;">
                    Total Amount
                </div>
            </div>

            <div class="stat-card" style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; text-align: center;">
                <div class="stat-icon" style="font-size: 2rem; color: #f59e0b; margin-bottom: 0.5rem;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value" style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">
                    {{ $pendingTrades }}
                </div>
                <div class="stat-label" style="color: var(--text-secondary); font-size: 0.9rem;">
                    Pending
                </div>
            </div>

            <div class="stat-card" style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; text-align: center;">
                <div class="stat-icon" style="font-size: 2rem; color: #10b981; margin-bottom: 0.5rem;">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-value" style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">
                    {{ $winTrades }}
                </div>
                <div class="stat-label" style="color: var(--text-secondary); font-size: 0.9rem;">
                    Wins
                </div>
            </div>

            <div class="stat-card" style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; text-align: center;">
                <div class="stat-icon" style="font-size: 2rem; color: #ef4444; margin-bottom: 0.5rem;">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-value" style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">
                    {{ $lossTrades }}
                </div>
                <div class="stat-label" style="color: var(--text-secondary); font-size: 0.9rem;">
                    Losses
                </div>
            </div>

            <div class="stat-card" style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; text-align: center;">
                <div class="stat-icon" style="font-size: 2rem; color: #10b981; margin-bottom: 0.5rem;">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-value" style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">
                    ${{ number_format($totalPayout, 2) }}
                </div>
                <div class="stat-label" style="color: var(--text-secondary); font-size: 0.9rem;">
                    Total Payout
                </div>
            </div>
        </div>

        <!-- Trade Dates Info -->
        @if($firstTrade || $lastTrade)
        <div class="trade-dates-info" style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 1rem 1.5rem; margin-bottom: 2rem; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            @if($firstTrade)
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calendar-plus" style="color: var(--accent);"></i>
                <span style="color: var(--text-secondary);">First Trade: </span>
                <strong style="color: var(--text-primary);">{{ $firstTrade->created_at->format('M d, Y h:i A') }}</strong>
            </div>
            @endif
            @if($lastTrade)
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calendar-check" style="color: var(--accent);"></i>
                <span style="color: var(--text-secondary);">Last Trade: </span>
                <strong style="color: var(--text-primary);">{{ $lastTrade->created_at->format('M d, Y h:i A') }}</strong>
            </div>
            @endif
        </div>
        @endif

        <!-- Trades Table -->
        <div class="trades-table-container" style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; overflow: hidden;">
            <div class="table-header" style="padding: 1.5rem; border-bottom: 1px solid var(--border);">
                <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin: 0;">
                    Recent Trades
                </h2>
            </div>

            @if($trades->count() > 0)
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="trades-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--bg-secondary); border-bottom: 1px solid var(--border);">
                            <th style="padding: 1rem; text-align: left; color: var(--text-secondary); font-weight: 600; font-size: 0.9rem;">Date & Time</th>
                            <th style="padding: 1rem; text-align: left; color: var(--text-secondary); font-weight: 600; font-size: 0.9rem;">Market</th>
                            <th style="padding: 1rem; text-align: left; color: var(--text-secondary); font-weight: 600; font-size: 0.9rem;">Option</th>
                            <th style="padding: 1rem; text-align: right; color: var(--text-secondary); font-weight: 600; font-size: 0.9rem;">Amount</th>
                            <th style="padding: 1rem; text-align: right; color: var(--text-secondary); font-weight: 600; font-size: 0.9rem;">Price</th>
                            <th style="padding: 1rem; text-align: center; color: var(--text-secondary); font-weight: 600; font-size: 0.9rem;">Status</th>
                            <th style="padding: 1rem; text-align: right; color: var(--text-secondary); font-weight: 600; font-size: 0.9rem;">Payout</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trades as $trade)
                        <tr style="border-bottom: 1px solid var(--border); transition: background 0.2s;">
                            <td style="padding: 1rem; color: var(--text-primary);">
                                <div style="font-weight: 500;">{{ $trade->created_at->format('M d, Y') }}</div>
                                <div style="font-size: 0.85rem; color: var(--text-secondary);">{{ $trade->created_at->format('h:i A') }}</div>
                            </td>
                            <td style="padding: 1rem; color: var(--text-primary); max-width: 300px;">
                                <div style="font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $trade->market->question ?? 'N/A' }}
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 6px; font-weight: 600; font-size: 0.85rem; 
                                    background: {{ $trade->option === 'yes' ? '#10b98120' : '#ef444420' }};
                                    color: {{ $trade->option === 'yes' ? '#10b981' : '#ef4444' }};
                                ">
                                    {{ strtoupper($trade->option) }}
                                </span>
                            </td>
                            <td style="padding: 1rem; text-align: right; color: var(--text-primary); font-weight: 600;">
                                ${{ number_format($trade->amount, 2) }}
                            </td>
                            <td style="padding: 1rem; text-align: right; color: var(--text-primary);">
                                {{ number_format($trade->price * 100, 2) }}¢
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                @if($trade->status === 'pending')
                                    <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 6px; font-weight: 600; font-size: 0.85rem; background: #f59e0b20; color: #f59e0b;">
                                        Pending
                                    </span>
                                @elseif($trade->status === 'win')
                                    <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 6px; font-weight: 600; font-size: 0.85rem; background: #10b98120; color: #10b981;">
                                        Win
                                    </span>
                                @elseif($trade->status === 'loss')
                                    <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 6px; font-weight: 600; font-size: 0.85rem; background: #ef444420; color: #ef4444;">
                                        Loss
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 1rem; text-align: right; color: var(--text-primary); font-weight: 600;">
                                @if($trade->payout_amount)
                                    ${{ number_format($trade->payout_amount, 2) }}
                                @else
                                    <span style="color: var(--text-secondary);">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-container" style="padding: 1.5rem; border-top: 1px solid var(--border);">
                {{ $trades->links() }}
            </div>
            @else
            <div style="padding: 3rem; text-align: center;">
                <i class="fas fa-inbox" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem; opacity: 0.5;"></i>
                <p style="color: var(--text-secondary); font-size: 1.1rem;">No trades yet</p>
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 0.5rem;">Start trading to see your history here</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .trades-table tbody tr:hover {
        background: var(--bg-secondary);
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        
        .trades-table {
            font-size: 0.85rem;
        }
        
        .trades-table th,
        .trades-table td {
            padding: 0.75rem 0.5rem !important;
        }
    }
</style>
@endsection


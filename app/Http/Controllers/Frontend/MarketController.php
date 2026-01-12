<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Market;
use App\Services\TradeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class MarketController extends Controller
{
    protected $tradeService;

    public function __construct(TradeService $tradeService)
    {
        $this->tradeService = $tradeService;
    }

    
    public function buy(Request $request, $marketId)
    {
        $request->validate([
            'outcome' => ['required', Rule::in(['YES', 'NO'])],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:100000'],
        ], [
            'amount.min' => 'Minimum trade amount is $0.01',
            'amount.max' => 'Maximum trade amount is $100,000',
        ]);

        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to trade',
                ], 401);
            }

            $market = Market::findOrFail($marketId);

            // Create trade
            $trade = $this->tradeService->createTrade(
                $user,
                $market,
                $request->outcome,
                $request->amount
            );

            // Get updated wallet balance
            $wallet = \App\Models\Wallet::where('user_id', $user->id)->first();

            return response()->json([
                'success' => true,
                'message' => 'Trade placed successfully',
                'trade' => [
                    'id' => $trade->id,
                    'outcome' => $trade->outcome,
                    'amount_invested' => $trade->amount_invested,
                    'token_amount' => $trade->token_amount,
                    'price_at_buy' => $trade->price_at_buy,
                    'status' => $trade->status,
                ],
                'wallet' => [
                    'balance' => $wallet ? $wallet->balance : 0,
                ],
            ]);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);

        } catch (\Exception $e) {
            Log::error('Trade placement failed', [
                'user_id' => Auth::id(),
                'market_id' => $marketId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to place trade: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get trade preview/estimate
     * API endpoint: GET /api/market/{marketId}/trade-preview
     */
    public function getTradePreview(Request $request, $marketId)
    {
        $request->validate([
            'outcome' => ['required', Rule::in(['YES', 'NO'])],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:100000'],
        ]);

        try {
            $market = Market::findOrFail($marketId);

            if (!$market->isOpenForTrading()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Market is closed for trading',
                ], 400);
            }

            $preview = $this->tradeService->getTradePreview(
                $market,
                $request->outcome,
                (float) $request->amount
            );

            return response()->json([
                'success' => true,
                'preview' => $preview,
            ]);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            Log::error('Trade preview failed', [
                'market_id' => $marketId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get trade preview: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current market prices
     * API endpoint: GET /api/market/{marketId}/prices
     */
    public function getMarketPrices($marketId)
    {
        try {
            $market = Market::findOrFail($marketId);

            $prices = $this->tradeService->getMarketPrices($market);

            return response()->json([
                'success' => true,
                'prices' => $prices,
                'market' => [
                    'id' => $market->id,
                    'question' => $market->question,
                    'slug' => $market->slug,
                    'is_open' => $market->isOpenForTrading(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Get market prices failed', [
                'market_id' => $marketId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get market prices: ' . $e->getMessage(),
            ], 500);
        }
    }

    
    public function settleMarket($marketId)
    {
        try {
            // Optimize: Eager load trades to avoid N+1 if trades have relationships
            $market = Market::with(['trades' => function($q) {
                $q->where('status', 'PENDING')
                  ->select(['id', 'user_id', 'market_id', 'outcome', 'amount_invested', 'amount', 'status', 'payout', 'payout_amount']);
            }])->findOrFail($marketId);

            if (!$market->final_outcome) {
                return response()->json([
                    'success' => false,
                    'message' => 'Market does not have a final outcome',
                ], 400);
            }

            $pendingTrades = $market->trades;

            if ($pendingTrades->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No pending trades to settle',
                    'settled_count' => 0,
                ]);
            }

            $settledCount = 0;
            foreach ($pendingTrades as $trade) {
                try {
                    $this->tradeService->settleTrade($trade);
                    $settledCount++;
                } catch (\Exception $e) {
                    Log::error('Failed to settle trade', [
                        'trade_id' => $trade->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Settled {$settledCount} trades",
                'settled_count' => $settledCount,
                'total_pending' => $pendingTrades->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Market settlement failed', [
                'market_id' => $marketId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to settle market: ' . $e->getMessage(),
            ], 500);
        }
    }
}




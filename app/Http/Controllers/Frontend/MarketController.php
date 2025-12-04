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

    /**
     * Buy YES or NO on a market
     * 
     * @param Request $request
     * @param int $marketId
     * @return \Illuminate\Http\JsonResponse
     */
    public function buy(Request $request, $marketId)
    {
        $request->validate([
            'outcome' => ['required', Rule::in(['YES', 'NO'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
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
     * Settle all trades for a market
     * 
     * @param int $marketId
     * @return \Illuminate\Http\JsonResponse
     */
    public function settleMarket($marketId)
    {
        try {
            $market = Market::findOrFail($marketId);

            if (!$market->final_outcome) {
                return response()->json([
                    'success' => false,
                    'message' => 'Market does not have a final outcome',
                ], 400);
            }

            $pendingTrades = $market->trades()->where('status', 'PENDING')->get();

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



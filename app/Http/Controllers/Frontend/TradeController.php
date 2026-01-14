<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Market;
use App\Models\Trade;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\TradeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class TradeController extends Controller
{
    protected $tradeService;

    public function __construct(TradeService $tradeService)
    {
        $this->tradeService = $tradeService;
    }
    /**
     * Place a trade (bet) on a market
     * Uses TradeService according to Polymarket-style trading system
     */
    public function placeTrade(Request $request, $marketId)
    {
        $request->validate([
            'option' => ['required', Rule::in(['yes', 'no'])],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:100000'],
            'price' => ['nullable', 'numeric', 'min:0.0001', 'max:0.9999'],
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

            // Get market
            $market = Market::findOrFail($marketId);

            // Convert option to outcome format (yes/no -> YES/NO)
            $outcome = strtoupper($request->option);
            $amount = (float) $request->amount;

            // Use TradeService to create trade
            $trade = $this->tradeService->createTrade($user, $market, $outcome, $amount);

            // Get updated main wallet balance
            $wallet = Wallet::where('user_id', $user->id)
                ->where('wallet_type', Wallet::TYPE_MAIN)
                ->first();

            // Calculate potential payout (for display purposes)
            $potentialPayout = $trade->token_amount * 1.00;

            Log::info('Trade placed successfully via TradeService', [
                'trade_id' => $trade->id,
                'user_id' => $user->id,
                'market_id' => $market->id,
                'outcome' => $outcome,
                'amount_invested' => $amount,
                'token_amount' => $trade->token_amount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Trade placed successfully',
                'trade' => [
                    'id' => $trade->id,
                    'outcome' => $trade->outcome,
                    'option' => $trade->option ?? strtolower($trade->outcome),
                    'amount_invested' => $trade->amount_invested,
                    'token_amount' => $trade->token_amount,
                    'price_at_buy' => $trade->price_at_buy,
                    'potential_payout' => $potentialPayout,
                    'status' => $trade->status,
                ],
                'wallet' => [
                    'balance' => $wallet ? $wallet->balance : 0,
                ],
            ]);

        } catch (\InvalidArgumentException $e) {
            Log::warning('Trade placement validation failed', [
                'user_id' => Auth::id(),
                'market_id' => $marketId,
                'error' => $e->getMessage(),
            ]);

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
     * Get user's trades with statistics
     */
    public function myTrades(Request $request)
    {
        $user = Auth::user();
        
        // Optimize: Select only necessary columns and eager load relationships with select
        $trades = Trade::select([
            'id', 'user_id', 'market_id', 'outcome', 'side', 'option',
            'amount_invested', 'amount', 'token_amount', 'shares', 'price_at_buy',
            'status', 'payout', 'payout_amount', 'settled_at', 'created_at'
        ])
        ->where('user_id', $user->id)
        ->with([
            'market' => function($q) {
                $q->select(['id', 'event_id', 'question', 'slug']);
            },
            'market.event' => function($q) {
                $q->select(['id', 'title', 'slug']);
            }
        ])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate statistics - optimize with base query
        $baseQuery = Trade::where('user_id', $user->id);
        $totalTrades = (clone $baseQuery)->count();
        $totalAmount = (clone $baseQuery)->sum(DB::raw('COALESCE(amount_invested, amount, 0)'));
        $pendingTrades = (clone $baseQuery)->whereRaw('UPPER(status) = ?', ['PENDING'])->count();
        $winTrades = (clone $baseQuery)->whereIn('status', ['win', 'WIN', 'WON', 'won'])->count();
        $lossTrades = (clone $baseQuery)->whereIn('status', ['loss', 'LOSS', 'LOST', 'lost'])->count();
        $totalPayout = (clone $baseQuery)->whereIn('status', ['win', 'WIN', 'WON', 'won'])
            ->sum(DB::raw('COALESCE(payout, payout_amount, 0)'));
        
        // Get first and last trade dates - optimize with single query
        $tradeDates = (clone $baseQuery)
            ->selectRaw('MIN(created_at) as first_trade, MAX(created_at) as last_trade')
            ->first();
        $firstTrade = $tradeDates ? (object)['created_at' => $tradeDates->first_trade] : null;
        $lastTrade = $tradeDates ? (object)['created_at' => $tradeDates->last_trade] : null;

        return response()->json([
            'success' => true,
            'trades' => $trades,
            'statistics' => [
                'total_trades' => $totalTrades,
                'total_amount' => number_format($totalAmount, 2),
                'pending_trades' => $pendingTrades,
                'win_trades' => $winTrades,
                'loss_trades' => $lossTrades,
                'total_payout' => number_format($totalPayout, 2),
                'first_trade_date' => $firstTrade ? $firstTrade->created_at->format('Y-m-d H:i:s') : null,
                'last_trade_date' => $lastTrade ? $lastTrade->created_at->format('Y-m-d H:i:s') : null,
            ],
        ]);
    }

    /**
     * Show user's trades history page
     */
    public function myTradesPage(Request $request)
    {
        $user = Auth::user();
        
        // Optimize: Select only necessary columns and eager load relationships with select
        $trades = Trade::select([
            'id', 'user_id', 'market_id', 'outcome', 'side', 'option',
            'amount_invested', 'amount', 'token_amount', 'shares', 'price_at_buy',
            'status', 'payout', 'payout_amount', 'settled_at', 'created_at'
        ])
        ->where('user_id', $user->id)
        ->with([
            'market' => function($q) {
                $q->select(['id', 'event_id', 'question', 'slug']);
            },
            'market.event' => function($q) {
                $q->select(['id', 'title', 'slug']);
            }
        ])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate statistics - optimize with base query
        $baseQuery = Trade::where('user_id', $user->id);
        $totalTrades = (clone $baseQuery)->count();
        $totalAmount = (clone $baseQuery)->sum(DB::raw('COALESCE(amount_invested, amount, 0)'));
        $pendingTrades = (clone $baseQuery)->whereRaw('UPPER(status) = ?', ['PENDING'])->count();
        $winTrades = (clone $baseQuery)->whereIn('status', ['win', 'WIN', 'WON', 'won'])->count();
        $lossTrades = (clone $baseQuery)->whereIn('status', ['loss', 'LOSS', 'LOST', 'lost'])->count();
        $totalPayout = (clone $baseQuery)->whereIn('status', ['win', 'WIN', 'WON', 'won'])
            ->sum(DB::raw('COALESCE(payout, payout_amount, 0)'));
        
        // Get first and last trade dates - optimize with single query
        $tradeDates = (clone $baseQuery)
            ->selectRaw('MIN(created_at) as first_trade, MAX(created_at) as last_trade')
            ->first();
        $firstTrade = $tradeDates ? (object)['created_at' => $tradeDates->first_trade] : null;
        $lastTrade = $tradeDates ? (object)['created_at' => $tradeDates->last_trade] : null;

        return view('frontend.trades_history', compact('trades', 'totalTrades', 'totalAmount', 'pendingTrades', 'winTrades', 'lossTrades', 'totalPayout', 'firstTrade', 'lastTrade'));
    }

    /**
     * Get trades for a specific market
     */
    public function marketTrades($marketId)
    {
        // Optimize: Select only necessary columns
        $market = Market::select(['id', 'question', 'slug'])->findOrFail($marketId);
        
        $trades = Trade::select([
            'id', 'user_id', 'market_id', 'outcome', 'side', 'option',
            'amount_invested', 'amount', 'token_amount', 'shares', 'price_at_buy',
            'status', 'payout', 'payout_amount', 'created_at'
        ])
        ->where('market_id', $marketId)
        ->with(['user' => function($q) {
            $q->select(['id', 'name', 'email']);
        }])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'trades' => $trades,
        ]);
    }

    /**
     * Get a specific trade by ID
     * API endpoint: GET /api/trades/{id}
     */
    public function getTrade($id)
    {
        $user = Auth::user();
        
        // Optimize: Select only necessary columns
        $trade = Trade::select([
            'id', 'user_id', 'market_id', 'outcome', 'side', 'option',
            'amount_invested', 'amount', 'token_amount', 'shares', 'price_at_buy',
            'status', 'payout', 'payout_amount', 'settled_at', 'created_at', 'updated_at'
        ])
        ->where('id', $id)
            ->where('user_id', $user->id) // Only allow users to see their own trades
        ->with([
            'market' => function($q) {
                $q->select(['id', 'event_id', 'question', 'slug']);
            },
            'market.event' => function($q) {
                $q->select(['id', 'title', 'slug']);
            },
            'user' => function($q) {
                $q->select(['id', 'name', 'email']);
            }
        ])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'trade' => [
                'id' => $trade->id,
                'market' => [
                    'id' => $trade->market->id,
                    'question' => $trade->market->question,
                    'slug' => $trade->market->slug,
                    'event' => $trade->market->event ? [
                        'id' => $trade->market->event->id,
                        'title' => $trade->market->event->title,
                        'slug' => $trade->market->event->slug,
                    ] : null,
                ],
                'outcome' => $trade->outcome,
                'amount_invested' => $trade->amount_invested,
                'token_amount' => $trade->token_amount,
                'shares' => $trade->shares ?? $trade->token_amount,
                'price_at_buy' => $trade->price_at_buy,
                'status' => $trade->status,
                'payout' => $trade->payout ?? 0,
                'settled_at' => $trade->settled_at,
                'created_at' => $trade->created_at,
                'updated_at' => $trade->updated_at,
                'is_pending' => $trade->isPending(),
                'is_win' => $trade->isWin(),
                'is_loss' => $trade->isLoss(),
            ],
        ]);
    }

}

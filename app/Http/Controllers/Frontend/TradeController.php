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
            'amount' => ['required', 'numeric', 'min:0.01'],
            'price' => ['nullable', 'numeric', 'min:0.0001', 'max:0.9999'],
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

            // Get updated wallet balance
            $wallet = Wallet::where('user_id', $user->id)->first();

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
        
        // Get all trades with market info
        $trades = Trade::where('user_id', $user->id)
            ->with('market')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate statistics
        $totalTrades = Trade::where('user_id', $user->id)->count();
        $totalAmount = Trade::where('user_id', $user->id)->sum('amount');
        $pendingTrades = Trade::where('user_id', $user->id)->where('status', 'pending')->count();
        $winTrades = Trade::where('user_id', $user->id)->where('status', 'win')->count();
        $lossTrades = Trade::where('user_id', $user->id)->where('status', 'loss')->count();
        $totalPayout = Trade::where('user_id', $user->id)->where('status', 'win')->sum('payout_amount');
        
        // Get first and last trade dates
        $firstTrade = Trade::where('user_id', $user->id)->orderBy('created_at', 'asc')->first();
        $lastTrade = Trade::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();

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
        
        // Get all trades with market info
        $trades = Trade::where('user_id', $user->id)
            ->with('market')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate statistics
        $totalTrades = Trade::where('user_id', $user->id)->count();
        $totalAmount = Trade::where('user_id', $user->id)->sum('amount');
        $pendingTrades = Trade::where('user_id', $user->id)->where('status', 'pending')->count();
        $winTrades = Trade::where('user_id', $user->id)->where('status', 'win')->count();
        $lossTrades = Trade::where('user_id', $user->id)->where('status', 'loss')->count();
        $totalPayout = Trade::where('user_id', $user->id)->where('status', 'win')->sum('payout_amount');
        
        // Get first and last trade dates
        $firstTrade = Trade::where('user_id', $user->id)->orderBy('created_at', 'asc')->first();
        $lastTrade = Trade::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();

        return view('frontend.trades_history', compact('trades', 'totalTrades', 'totalAmount', 'pendingTrades', 'winTrades', 'lossTrades', 'totalPayout', 'firstTrade', 'lastTrade'));
    }

    /**
     * Get trades for a specific market
     */
    public function marketTrades($marketId)
    {
        $market = Market::findOrFail($marketId);
        
        $trades = Trade::where('market_id', $marketId)
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'trades' => $trades,
        ]);
    }

}

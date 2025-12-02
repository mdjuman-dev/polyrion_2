<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Market;
use App\Models\Trade;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class TradeController extends Controller
{
    /**
     * Place a trade (bet) on a market
     */
    public function placeTrade(Request $request, $marketId)
    {
        $request->validate([
            'option' => ['required', Rule::in(['yes', 'no'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'price' => ['nullable', 'numeric', 'min:0.0001', 'max:0.9999'],
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to trade',
                ], 401);
            }

            // Get market
            $market = Market::findOrFail($marketId);

            // Check if market is open for trading
            if (!$market->isOpenForTrading()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This market is closed for trading',
                ], 400);
            }

            // Get or create wallet
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0, 'currency' => 'USDT', 'status' => 'active']
            );

            $amount = $request->amount;
            $option = $request->option;

            // Calculate price if not provided (use current market price)
            $price = $request->price;
            if (!$price) {
                $outcomePrices = json_decode($market->outcome_prices, true);
                if (is_array($outcomePrices)) {
                    if ($option === 'yes' && isset($outcomePrices[0])) {
                        $price = $outcomePrices[0];
                    } elseif ($option === 'no' && isset($outcomePrices[1])) {
                        $price = $outcomePrices[1];
                    } else {
                        // Default price if not available
                        $price = $option === 'yes' ? 0.5 : 0.5;
                    }
                } else {
                    $price = 0.5; // Default
                }
            }

            // Check if user has enough balance
            if ($wallet->balance < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance',
                    'balance' => $wallet->balance,
                    'required' => $amount,
                ], 400);
            }

            // Deduct amount from wallet
            $balanceBefore = $wallet->balance;
            $wallet->balance -= $amount;
            $wallet->save();

            // Create trade
            $trade = Trade::create([
                'user_id' => $user->id,
                'market_id' => $market->id,
                'option' => $option,
                'amount' => $amount,
                'price' => $price,
                'status' => 'pending',
            ]);

            // Create wallet transaction
            WalletTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'type' => 'trade',
                'amount' => -$amount, // Negative for deduction
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'reference_type' => Trade::class,
                'reference_id' => $trade->id,
                'description' => "Placed {$option} bet on market: {$market->question}",
                'metadata' => [
                    'trade_id' => $trade->id,
                    'market_id' => $market->id,
                    'option' => $option,
                    'price' => $price,
                ]
            ]);

            // Calculate potential payout
            $potentialPayout = $option === 'yes' 
                ? $amount + ($amount * (1 - $price))
                : $amount + ($amount * $price);

            DB::commit();

            Log::info('Trade placed successfully', [
                'trade_id' => $trade->id,
                'user_id' => $user->id,
                'market_id' => $market->id,
                'option' => $option,
                'amount' => $amount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Trade placed successfully',
                'trade' => [
                    'id' => $trade->id,
                    'option' => $trade->option,
                    'amount' => $trade->amount,
                    'price' => $trade->price,
                    'potential_payout' => $potentialPayout,
                    'status' => $trade->status,
                ],
                'wallet' => [
                    'balance' => $wallet->balance,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Trade placement failed', [
                'user_id' => Auth::id(),
                'market_id' => $marketId,
                'error' => $e->getMessage(),
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

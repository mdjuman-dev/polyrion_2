<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    function profile()
    {
        $user = Auth::user();

        $user->load('wallet', 'trades.market');

        $wallet = $user->wallet;
        $balance = $wallet ? $wallet->balance : 0;
        $portfolio = $wallet ? ($wallet->portfolio ?? 0) : 0;

        $profileImage = $user->profile_image
            ? asset('storage/' . $user->profile_image)
            : asset('frontend/assets/images/default-avatar.png');

        // Get user's trades with market info
        $trades = \App\Models\Trade::where('user_id', $user->id)
            ->with('market')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate stats
        $totalTrades = $trades->count();
        $pendingTrades = $trades->where('status', 'pending')->count();
        $winTrades = $trades->where('status', 'win')->count();
        $lossTrades = $trades->where('status', 'loss')->count();
        $totalPayout = $trades->where('status', 'win')->sum('payout_amount');
        $biggestWin = $trades->where('status', 'win')->max('payout_amount') ?? 0;

        $stats = [
            'positions_value' => $portfolio,
            'biggest_win' => $biggestWin,
            'predictions' => $totalTrades,
        ];

        // Get all positions with market info (for all trades, not just pending)
        $activePositions = $trades->map(function ($trade) {
            return [
                'trade' => $trade,
                'market' => $trade->market,
                'close_time' => $trade->market ? $trade->market->close_time : null,
                'result_set_at' => $trade->market ? $trade->market->result_set_at : null,
                'is_open' => $trade->market ? $trade->market->isOpenForTrading() : false,
                'is_closed' => $trade->market ? $trade->market->isClosed() : false,
                'has_result' => $trade->market ? $trade->market->hasResult() : false,
                'final_result' => $trade->market ? $trade->market->final_result : null,
            ];
        });

        return view('frontend.profile', compact('user', 'wallet', 'balance', 'portfolio', 'profileImage', 'stats', 'trades', 'activePositions'));
    }

    function settings()
    {
        return view('frontend.profile_settings');
    }
}

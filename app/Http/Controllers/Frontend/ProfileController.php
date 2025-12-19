<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    function profile()
    {
        $user = Auth::user();

        $user->load('wallet', 'trades.market');

        $wallet = $user->wallet;
        $balance = $wallet ? $wallet->balance : 0;

        $profileImage = $user->profile_image
            ? asset('storage/' . $user->profile_image)
            : asset('frontend/assets/images/default-avatar.png');

        // Get user's trades with market info - eager load market.event for better performance
        $trades = \App\Models\Trade::where('user_id', $user->id)
            ->with(['market.event'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate portfolio value from active/pending trades
        $portfolio = $this->calculatePortfolioValue($trades);

        // Calculate stats using database queries instead of collection filters (much faster)
        $totalTrades = \App\Models\Trade::where('user_id', $user->id)->count();
        $pendingTrades = \App\Models\Trade::where('user_id', $user->id)
            ->whereRaw('UPPER(status) = ?', ['PENDING'])
            ->count();
        $winTrades = \App\Models\Trade::where('user_id', $user->id)
            ->whereIn('status', ['WON', 'WIN', 'won', 'win'])
            ->count();
        $lossTrades = \App\Models\Trade::where('user_id', $user->id)
            ->whereIn('status', ['LOST', 'LOSS', 'lost', 'loss'])
            ->count();
        $closedTrades = \App\Models\Trade::where('user_id', $user->id)
            ->whereRaw('UPPER(status) = ?', ['CLOSED'])
            ->count();
        $totalPayout = \App\Models\Trade::where('user_id', $user->id)
            ->whereIn('status', ['WON', 'WIN', 'won', 'win'])
            ->sum(\DB::raw('COALESCE(payout, payout_amount, 0)'));
        $biggestWin = \App\Models\Trade::where('user_id', $user->id)
            ->whereIn('status', ['WON', 'WIN', 'won', 'win'])
            ->max(\DB::raw('COALESCE(payout, payout_amount, 0)')) ?? 0;

        $stats = [
            'positions_value' => $portfolio,
            'biggest_win' => $biggestWin,
            'predictions' => $totalTrades,
        ];

        // Get all positions with market info (for all trades, not just pending)
        $activePositions = $trades->map(function ($trade) {
            $tradeStatus = strtoupper($trade->status ?? 'PENDING');
            $isTradeClosed = false; // Close position feature disabled
            $isTradeSettled = in_array($tradeStatus, ['WON', 'WIN', 'LOST', 'LOSS']);
            
            return [
                'trade' => $trade,
                'market' => $trade->market,
                'close_time' => $trade->market ? $trade->market->close_time : null,
                'result_set_at' => $trade->market ? $trade->market->result_set_at : null,
                'is_open' => $trade->market ? $trade->market->isOpenForTrading() : false,
                'is_closed' => $trade->market ? $trade->market->isClosed() : false,
                'has_result' => $trade->market ? $trade->market->hasResult() : false,
                'final_result' => $trade->market ? $trade->market->final_result : null,
                'is_trade_closed' => $isTradeClosed,
                'is_trade_settled' => $isTradeSettled,
            ];
        });

        // Get user's withdrawals
        $withdrawals = \App\Models\Withdrawal::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get user's deposits
        $deposits = \App\Models\Deposit::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate profit/loss data for chart
        $profitLossData = $this->calculateProfitLossData($trades);

        // Combine trades and withdrawals for activity tab
        $allActivity = collect();
        foreach($trades as $trade) {
            $allActivity->push([
                'type' => 'trade',
                'data' => $trade,
                'date' => $trade->created_at,
            ]);
        }
        foreach($withdrawals as $withdrawal) {
            $allActivity->push([
                'type' => 'withdrawal',
                'data' => $withdrawal,
                'date' => $withdrawal->created_at,
            ]);
        }
        $allActivity = $allActivity->sortByDesc('date');

        return view('frontend.profile', compact('user', 'wallet', 'balance', 'portfolio', 'profileImage', 'stats', 'trades', 'activePositions', 'withdrawals', 'deposits', 'allActivity', 'profitLossData'));
    }

    function settings()
    {
        return view('frontend.profile_settings');
    }

    function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'username' => ['nullable', 'string', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'number' => ['nullable', 'string', 'max:20'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            try {
                $image = $request->file('profile_image');
                
                // Validate image
                if (!$image->isValid()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid image file. Please try again.'
                    ], 400);
                }

                // Ensure profiles directory exists
                $profilesPath = storage_path('app/public/profiles');
                if (!File::exists($profilesPath)) {
                    File::makeDirectory($profilesPath, 0755, true);
                }

                // Delete old image if exists
                if ($user->profile_image) {
                    $oldImagePath = 'profiles/' . basename($user->profile_image);
                    if (Storage::disk('public')->exists($oldImagePath)) {
                        Storage::disk('public')->delete($oldImagePath);
                    }
                    // Also check if stored with full path
                    if (Storage::disk('public')->exists($user->profile_image)) {
                        Storage::disk('public')->delete($user->profile_image);
                    }
                }

                // Generate unique filename
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                // Store image in storage/app/public/profiles directory
                $imagePath = $image->storeAs('profiles', $imageName, 'public');

                if (!$imagePath) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload image. Please try again.'
                    ], 500);
                }

                // Verify file was stored
                if (!Storage::disk('public')->exists($imagePath)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Image file was not saved properly. Please try again.'
                    ], 500);
                }

                // Save relative path (profiles/filename.jpg) to database
                $validated['profile_image'] = $imagePath;
                
            } catch (\Exception $e) {
                \Log::error('Profile image upload error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error uploading image: ' . $e->getMessage()
                ], 500);
            }
        }

        // Update email verification if email changed
        if ($user->email !== $validated['email']) {
            $user->email_verified_at = null;
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!'
        ]);
    }

    /**
     * Calculate profit/loss data grouped by date for chart
     */
    private function calculateProfitLossData($trades)
    {
        // Group trades by date
        $dailyData = [];
        $cumulativeProfit = 0;

        // Get all trades sorted by date
        $sortedTrades = $trades->sortBy('created_at');

        foreach ($sortedTrades as $trade) {
            $date = $trade->created_at->format('Y-m-d');
            
            // Calculate profit/loss for this trade
            $amount = $trade->amount ?? $trade->amount_invested ?? 0;
            $profitLoss = 0;

            if (strtoupper($trade->status) === 'WON' || $trade->status === 'win') {
                // Win: profit = payout - amount invested
                $payout = $trade->payout ?? $trade->payout_amount ?? 0;
                $profitLoss = $payout - $amount;
            } elseif (strtoupper($trade->status) === 'LOST' || $trade->status === 'loss') {
                // Loss: lost the full amount invested
                $profitLoss = -$amount;
            }
            // Pending trades don't contribute to profit/loss yet

            // Group by date (if multiple trades on same day, sum them)
            if (!isset($dailyData[$date])) {
                $dailyData[$date] = [
                    'date' => $date,
                    'profit_loss' => 0,
                ];
            }
            
            $dailyData[$date]['profit_loss'] += $profitLoss;
        }

        // Calculate cumulative profit/loss over time
        $result = [];
        $today = now();
        $startDate = $today->copy()->subDays(90);
        
        // Sort daily data by date
        ksort($dailyData);
        
        // Fill in dates and calculate cumulative values
        $lastCumulative = 0;
        for ($date = $startDate->copy(); $date <= $today; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            
            // If there are trades on this date, add their profit/loss
            if (isset($dailyData[$dateStr])) {
                $lastCumulative += $dailyData[$dateStr]['profit_loss'];
            }
            
            // Add data point (even if no trades, to show cumulative value)
            $result[] = [
                'date' => $dateStr,
                'label' => $date->format('M d'),
                'value' => $lastCumulative,
            ];
        }

        return $result;
    }

    /**
     * Calculate portfolio value from active/pending trades
     * Portfolio = sum of current values of all pending/active positions
     */
    private function calculatePortfolioValue($trades)
    {
        $portfolioValue = 0;

        foreach ($trades as $trade) {
            // Only calculate for pending/active trades (not won or lost)
            $tradeStatus = strtoupper($trade->status ?? 'PENDING');
            if (in_array($tradeStatus, ['WON', 'WIN', 'LOST', 'LOSS'])) {
                continue; // Skip settled trades
            }

            // Get market and current price
            if (!$trade->market) {
                continue;
            }

            $market = $trade->market;
            $outcomePrices = is_string($market->outcome_prices ?? $market->outcomePrices ?? null)
                ? json_decode($market->outcome_prices ?? $market->outcomePrices, true)
                : ($market->outcome_prices ?? $market->outcomePrices ?? [0.5, 0.5]);

            if (!is_array($outcomePrices) || count($outcomePrices) < 2) {
                continue;
            }

            // Get average price at buy
            $avgPrice = $trade->price_at_buy ?? $trade->price ?? 0.5;
            if ($avgPrice <= 0) {
                continue;
            }

            // Get outcome
            $outcome = strtoupper($trade->outcome ?? ($trade->option === 'yes' ? 'YES' : 'NO'));
            
            // Get current price for the outcome
            // outcome_prices[0] = NO price, outcome_prices[1] = YES price
            $currentPrice = ($outcome === 'YES' && isset($outcomePrices[1])) 
                ? $outcomePrices[1] 
                : (($outcome === 'NO' && isset($outcomePrices[0])) 
                    ? $outcomePrices[0] 
                    : $avgPrice);

            // Calculate shares (token_amount)
            $shares = $trade->token_amount ?? ($trade->shares ?? 0);
            if ($shares <= 0) {
                // Calculate from amount invested
                $amountInvested = $trade->amount_invested ?? $trade->amount ?? 0;
                $shares = $amountInvested / $avgPrice;
            }

            // Current value = shares * current_price
            $currentValue = $shares * $currentPrice;
            $portfolioValue += $currentValue;
        }

        return $portfolioValue;
    }
}

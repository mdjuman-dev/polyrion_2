<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\PasswordChangeOtp;
use App\Notifications\PasswordChangeOtpNotification;

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
      foreach ($trades as $trade) {
         $allActivity->push([
            'type' => 'trade',
            'data' => $trade,
            'date' => $trade->created_at,
         ]);
      }
      foreach ($withdrawals as $withdrawal) {
         $allActivity->push([
            'type' => 'withdrawal',
            'data' => $withdrawal,
            'date' => $withdrawal->created_at,
         ]);
      }
      $allActivity = $allActivity->sortByDesc('date');

      $hasWithdrawalPassword = !empty($user->withdrawal_password);
      $binanceWallet = $user->binance_wallet_address;
      $metamaskWallet = $user->metamask_wallet_address;

      return view('frontend.profile', compact('user', 'wallet', 'balance', 'portfolio', 'profileImage', 'stats', 'trades', 'activePositions', 'withdrawals', 'deposits', 'allActivity', 'profitLossData', 'hasWithdrawalPassword', 'binanceWallet', 'metamaskWallet'));
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

   function sendPasswordChangeOtp(Request $request)
   {
      try {
         $user = Auth::user();

         // Check if user logged in via Google or Facebook
         $isSocialLogin = !empty($user->google_id) || !empty($user->facebook_id);

         if (!$isSocialLogin) {
            return response()->json([
               'success' => false,
               'message' => 'OTP is only required for social login users.'
            ], 400);
         }

         // Create OTP
         $otpRecord = PasswordChangeOtp::createForUser($user->id);

         // Send OTP via email
         try {
            $user->notify(new PasswordChangeOtpNotification($otpRecord->otp));
         } catch (\Exception $e) {
            \Log::error('Failed to send OTP email: ' . $e->getMessage());
            return response()->json([
               'success' => false,
               'message' => 'Failed to send OTP email. Please try again later.'
            ], 500);
         }

         return response()->json([
            'success' => true,
            'message' => 'OTP has been sent to your email address. Please check your inbox.'
         ]);
      } catch (\Exception $e) {
         \Log::error('Failed to send OTP: ' . $e->getMessage());
         return response()->json([
            'success' => false,
            'message' => 'Failed to send OTP. Please try again later.'
         ], 500);
      }
   }

   function updatePassword(Request $request)
   {
      try {
         $user = Auth::user();

         if (!$user) {
            \Log::error('Password update failed: User not authenticated');
            return response()->json([
               'success' => false,
               'message' => 'User not authenticated. Please login again.'
            ], 401);
         }

         // Check if user logged in via Google or Facebook
         $isSocialLogin = !empty($user->google_id) || !empty($user->facebook_id);

         \Log::info('Password update request', [
            'user_id' => $user->id,
            'email' => $user->email,
            'is_social_login' => $isSocialLogin,
            'request_data' => $request->except(['password', 'password_confirmation', 'current_password', 'otp'])
         ]);

         // Validation rules
         $rules = [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
         ];

         if (!$isSocialLogin) {
            // Regular users must provide current password
            $rules['current_password'] = ['required', 'string'];
         } else {
            // Social login users must provide OTP
            $rules['otp'] = ['required', 'string', 'size:6'];
         }

         try {
            $validated = $request->validate($rules);
            \Log::info('Password update validation passed', [
               'user_id' => $user->id,
               'has_current_password' => !$isSocialLogin,
               'has_otp' => $isSocialLogin
            ]);
         } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Password update validation failed', [
               'user_id' => $user->id,
               'errors' => $e->errors()
            ]);
            throw $e;
         }

         if (!$isSocialLogin) {
            // Verify current password for regular users
            // Get password directly from database to avoid model casting issues
            $storedPassword = DB::table('users')->where('id', $user->id)->value('password');

            \Log::info('Current password verification', [
               'user_id' => $user->id,
               'has_stored_password' => !empty($storedPassword),
               'password_hash_preview' => $storedPassword ? substr($storedPassword, 0, 20) . '...' : null
            ]);

            if (!$storedPassword) {
               \Log::error('Password update failed: No stored password found', [
                  'user_id' => $user->id,
                  'email' => $user->email
               ]);

               return response()->json([
                  'success' => false,
                  'message' => 'No password found for your account. Please contact support.',
                  'errors' => ['current_password' => ['No password found for your account.']]
               ], 422);
            }

            $passwordMatch = Hash::check($validated['current_password'], $storedPassword);

            \Log::info('Password check result', [
               'user_id' => $user->id,
               'password_match' => $passwordMatch
            ]);

            if (!$passwordMatch) {
               \Log::warning('Password update failed: Current password incorrect', [
                  'user_id' => $user->id,
                  'email' => $user->email
               ]);

               return response()->json([
                  'success' => false,
                  'message' => 'Current password is incorrect.',
                  'errors' => ['current_password' => ['Current password is incorrect.']]
               ], 422);
            }
         } else {
            // Verify OTP for social login users
            $otpRecord = PasswordChangeOtp::verifyOtp($user->id, $validated['otp']);

            if (!$otpRecord) {
               return response()->json([
                  'success' => false,
                  'message' => 'Invalid or expired OTP. Please request a new OTP.',
                  'errors' => ['otp' => ['Invalid or expired OTP.']]
               ], 422);
            }

            // Mark OTP as used
            $otpRecord->markAsUsed();
         }

         // Update password - bypass model casting to avoid double hashing
         // Since User model has 'password' => 'hashed' casting, we need to update directly via DB
         // to prevent automatic hashing by the model
         try {
            \Log::info('Starting password update process', [
               'user_id' => $user->id,
               'email' => $user->email
            ]);

            // Hash the new password
            $hashedPassword = Hash::make($validated['password']);

            \Log::info('Password hashed successfully', [
               'user_id' => $user->id,
               'hash_preview' => substr($hashedPassword, 0, 20) . '...'
            ]);

            // Update password directly in database using raw query to bypass model casting
            $updated = DB::statement(
               'UPDATE users SET password = ?, updated_at = ? WHERE id = ?',
               [$hashedPassword, now(), $user->id]
            );

            // Alternative: Use DB::table with raw update
            if (!$updated) {
               $updated = DB::table('users')
                  ->where('id', $user->id)
                  ->update([
                     'password' => $hashedPassword,
                     'updated_at' => now()
                  ]);
            }

            \Log::info('Password update query executed', [
               'user_id' => $user->id,
               'update_result' => $updated
            ]);

            // Verify the update by checking if password was actually changed
            $newPasswordHash = DB::table('users')->where('id', $user->id)->value('password');

            if (!$newPasswordHash) {
               \Log::error('Password update verification failed: No password found after update', [
                  'user_id' => $user->id,
                  'email' => $user->email
               ]);

               return response()->json([
                  'success' => false,
                  'message' => 'Password update failed. Please try again.'
               ], 500);
            }

            // Verify that the new password can be checked against the stored hash
            // This ensures the password was saved correctly
            $passwordVerified = Hash::check($validated['password'], $newPasswordHash);

            \Log::info('Password verification result', [
               'user_id' => $user->id,
               'password_verified' => $passwordVerified,
               'stored_hash_preview' => substr($newPasswordHash, 0, 20) . '...'
            ]);

            if (!$passwordVerified) {
               \Log::error('Password update verification failed: New password does not match stored hash', [
                  'user_id' => $user->id,
                  'email' => $user->email
               ]);

               return response()->json([
                  'success' => false,
                  'message' => 'Password update failed verification. Please try again.'
               ], 500);
            }

            // Refresh user model to reflect changes
            $user->refresh();

            \Log::info('Password update successful', [
               'user_id' => $user->id,
               'email' => $user->email
            ]);

            return response()->json([
               'success' => true,
               'message' => $isSocialLogin ? 'Password set successfully! You can now login with email and password.' : 'Password updated successfully!'
            ]);
         } catch (\Exception $e) {
            \Log::error('Password update exception in try block: ' . $e->getMessage(), [
               'user_id' => $user->id ?? null,
               'email' => $user->email ?? null,
               'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
               'success' => false,
               'message' => 'An error occurred while updating password: ' . $e->getMessage()
            ], 500);
         }
      } catch (\Illuminate\Validation\ValidationException $e) {
         return response()->json([
            'success' => false,
            'message' => 'Validation failed. Please check your input.',
            'errors' => $e->errors()
         ], 422);
      } catch (\Exception $e) {
         \Log::error('Password update failed: ' . $e->getMessage(), [
            'user_id' => Auth::id(),
            'trace' => $e->getTraceAsString()
         ]);

         return response()->json([
            'success' => false,
            'message' => 'Failed to update password. Please try again later.'
         ], 500);
      }
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

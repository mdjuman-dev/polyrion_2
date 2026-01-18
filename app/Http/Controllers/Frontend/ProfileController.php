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
use App\Models\UserKycVerification;
use App\Notifications\PasswordChangeOtpNotification;
use App\Services\ReferralService;

class ProfileController extends Controller
{
   function profile(Request $request)
   {
      $user = Auth::user();
      
      // Check if this is an AJAX request for stats
      if ($request->ajax() && $request->has('stats_period')) {
         $period = $request->get('stats_period');
         $stats = $this->getTradeStatsForPeriod($user, $period);
         $chartData = $this->getProfitLossChartDataForPeriod($user, $period);
         return response()->json([
            'stats' => $stats,
            'chartData' => $chartData
         ]);
      }

      // Load both wallets
      $user->load(['mainWallet', 'earningWallet']);

      $mainWallet = $user->mainWallet;
      $earningWallet = $user->earningWallet;
      $mainBalance = $mainWallet ? $mainWallet->balance : 0;
      $earningBalance = $earningWallet ? $earningWallet->balance : 0;
      $totalBalance = $mainBalance + $earningBalance;
      
      // For backward compatibility, use main wallet as primary wallet
      $wallet = $mainWallet;
      $balance = $mainBalance;

      $profileImage = $user->profile_image
         ? asset('storage/' . $user->profile_image)
         : asset('frontend/assets/images/default-avatar.png');

      // Optimize: Get stats in single query using conditional aggregation
      $statsQuery = \App\Models\Trade::where('user_id', $user->id)
         ->selectRaw('
            COUNT(*) as total_trades,
            SUM(CASE WHEN UPPER(status) = "PENDING" THEN 1 ELSE 0 END) as pending_trades,
            SUM(CASE WHEN UPPER(status) IN ("WON", "WIN") THEN 1 ELSE 0 END) as win_trades,
            SUM(CASE WHEN UPPER(status) IN ("LOST", "LOSS") THEN 1 ELSE 0 END) as loss_trades,
            SUM(CASE WHEN UPPER(status) IN ("WON", "WIN") THEN COALESCE(payout, payout_amount, 0) ELSE 0 END) as total_payout,
            SUM(CASE WHEN UPPER(status) IN ("WON", "WIN") THEN COALESCE(amount_invested, amount, 0) ELSE 0 END) as total_invested_wins,
            SUM(CASE WHEN UPPER(status) IN ("LOST", "LOSS") THEN COALESCE(amount_invested, amount, 0) ELSE 0 END) as total_invested_losses,
            MAX(CASE WHEN UPPER(status) IN ("WON", "WIN") THEN COALESCE(payout, payout_amount, 0) ELSE 0 END) as biggest_win
         ')
         ->first();

      // Get stats for last 30 days
      $thirtyDaysAgo = now()->subDays(30);
      $stats30DaysQuery = \App\Models\Trade::where('user_id', $user->id)
         ->where('created_at', '>=', $thirtyDaysAgo)
         ->selectRaw('
            COUNT(*) as total_trades,
            SUM(CASE WHEN UPPER(status) = "PENDING" THEN 1 ELSE 0 END) as pending_trades,
            SUM(CASE WHEN UPPER(status) IN ("WON", "WIN") THEN 1 ELSE 0 END) as win_trades,
            SUM(CASE WHEN UPPER(status) IN ("LOST", "LOSS") THEN 1 ELSE 0 END) as loss_trades,
            SUM(CASE WHEN UPPER(status) IN ("WON", "WIN") THEN COALESCE(payout, payout_amount, 0) ELSE 0 END) as total_payout,
            SUM(CASE WHEN UPPER(status) IN ("WON", "WIN") THEN COALESCE(amount_invested, amount, 0) ELSE 0 END) as total_invested_wins,
            SUM(CASE WHEN UPPER(status) IN ("LOST", "LOSS") THEN COALESCE(amount_invested, amount, 0) ELSE 0 END) as total_invested_losses,
            SUM(CASE WHEN UPPER(status) IN ("WON", "WIN") THEN (COALESCE(payout, payout_amount, 0) - COALESCE(amount_invested, amount, 0)) ELSE 0 END) as total_profit,
            SUM(CASE WHEN UPPER(status) IN ("LOST", "LOSS") THEN COALESCE(amount_invested, amount, 0) ELSE 0 END) as total_loss
         ')
         ->first();

      $totalTrades = $statsQuery->total_trades ?? 0;
      $biggestWin = $statsQuery->biggest_win ?? 0;
      
      // Last 30 days stats
      $stats30Days = [
         'total_trades' => $stats30DaysQuery->total_trades ?? 0,
         'win_trades' => $stats30DaysQuery->win_trades ?? 0,
         'loss_trades' => $stats30DaysQuery->loss_trades ?? 0,
         'pending_trades' => $stats30DaysQuery->pending_trades ?? 0,
         'total_payout' => $stats30DaysQuery->total_payout ?? 0,
         'total_invested_wins' => $stats30DaysQuery->total_invested_wins ?? 0,
         'total_invested_losses' => $stats30DaysQuery->total_invested_losses ?? 0,
         'total_profit' => $stats30DaysQuery->total_profit ?? 0,
         'total_loss' => $stats30DaysQuery->total_loss ?? 0,
         'net_profit_loss' => ($stats30DaysQuery->total_profit ?? 0) - ($stats30DaysQuery->total_loss ?? 0),
         'win_rate' => ($stats30DaysQuery->total_trades ?? 0) > 0 
            ? round((($stats30DaysQuery->win_trades ?? 0) / ($stats30DaysQuery->total_trades ?? 1)) * 100, 2) 
            : 0,
      ];

      // Optimize: Get only recent trades (last 100) with limited columns for positions
      $trades = \App\Models\Trade::where('user_id', $user->id)
         ->select([
            'id', 'user_id', 'market_id', 'outcome', 'option',
            'amount_invested', 'amount', 'token_amount', 'shares',
            'price_at_buy', 'price', 'status', 'payout', 'payout_amount',
            'settled_at', 'created_at', 'updated_at'
         ])
         ->with([
            'market' => function($query) {
               $query->select([
                  'id', 'event_id', 'question', 'slug', 'image',
                  'outcome_prices',
                  'close_time', 'result_set_at', 'final_result',
                  'active', 'closed', 'archived'
               ]);
            },
            'market.event' => function($query) {
               $query->select(['id', 'title', 'slug', 'image', 'end_date']);
            }
         ])
         ->orderBy('created_at', 'desc')
         ->limit(100) // Limit to last 100 trades
         ->get();

      // Optimize: Calculate portfolio and profit/loss from limited trades
      $portfolio = $this->calculatePortfolioValue($trades);
      $totalProfitLoss = $this->calculateTotalProfitLoss($trades);

      $stats = [
         'positions_value' => $portfolio,
         'total_profit_loss' => $totalProfitLoss,
         'biggest_win' => $biggestWin,
         'predictions' => $totalTrades,
      ];

      // Optimize: Map positions with minimal data
      $activePositions = $trades->map(function ($trade) {
         $tradeStatus = strtoupper($trade->status ?? 'PENDING');
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
            'is_trade_closed' => false,
            'is_trade_settled' => $isTradeSettled,
         ];
      });

      // Optimize: Get only recent withdrawals (last 50)
      $withdrawals = \App\Models\Withdrawal::where('user_id', $user->id)
         ->select(['id', 'user_id', 'amount', 'status', 'created_at', 'updated_at'])
         ->orderBy('created_at', 'desc')
         ->limit(50)
         ->get();

      // Optimize: Get only recent deposits (last 50)
      $deposits = \App\Models\Deposit::where('user_id', $user->id)
         ->select(['id', 'user_id', 'amount', 'status', 'created_at', 'updated_at'])
         ->orderBy('created_at', 'desc')
         ->limit(50)
         ->get();

      // Optimize: Get only completed trades for chart (more efficient)
      $completedTradesForChart = \App\Models\Trade::where('user_id', $user->id)
         ->whereIn('status', ['WON', 'WIN', 'won', 'win', 'LOST', 'LOSS', 'lost', 'loss'])
         ->select([
            'id', 'status', 'amount_invested', 'amount',
            'payout', 'payout_amount', 'settled_at', 'created_at'
         ])
         ->orderBy('created_at', 'asc')
         ->get();

      // Calculate profit/loss data for chart (only completed trades)
      $profitLossData = $this->calculateProfitLossDataOptimized($completedTradesForChart);

      // Optimize: Combine only recent activities (already limited above)
      $allActivity = collect();
      foreach ($trades->take(30) as $trade) {
         $allActivity->push([
            'type' => 'trade',
            'data' => $trade,
            'date' => $trade->created_at,
         ]);
      }
      foreach ($withdrawals->take(20) as $withdrawal) {
         $allActivity->push([
            'type' => 'withdrawal',
            'data' => $withdrawal,
            'date' => $withdrawal->created_at,
         ]);
      }
      $allActivity = $allActivity->sortByDesc('date')->take(50); // Limit to 50 most recent

      // Optimize: Load KYC only if needed
      $hasWithdrawalPassword = !empty($user->withdrawal_password);
      $binanceWallet = $user->binance_wallet_address;
      $metamaskWallet = $user->metamask_wallet_address;
      $kycVerification = \App\Models\UserKycVerification::where('user_id', $user->id)
         ->first(['id', 'user_id', 'status']);

      // Get referral stats
      $referralService = new ReferralService();
      $referralStats = $referralService->getUserReferralStats($user);
      
      // Get referral commission history
      $referralCommissionService = new \App\Services\ReferralCommissionService();
      $referralCommissionHistory = $referralCommissionService->getReferrerCommissionHistory($user, 50);
      
      // Generate referral link
      $referralLink = route('referral.link', ['username' => $user->username]);

      // Get markets chart data (last 7 days) - similar to dashboard
      $marketsChartData = $this->getMarketsChartData();

      // Get initial chart data for 30 days
      $initialChartData30Days = $this->getProfitLossChartDataForPeriod($user, '30');

      // Check if user has transfer history
      $transferHistoryCount = \App\Models\WalletTransaction::where('user_id', $user->id)
         ->whereIn('type', ['transfer_in', 'transfer_out'])
         ->count();

      return view('frontend.profile', compact('user', 'wallet', 'mainWallet', 'earningWallet', 'balance', 'mainBalance', 'earningBalance', 'totalBalance', 'portfolio', 'profileImage', 'stats', 'stats30Days', 'trades', 'activePositions', 'withdrawals', 'deposits', 'allActivity', 'profitLossData', 'initialChartData30Days', 'hasWithdrawalPassword', 'binanceWallet', 'metamaskWallet', 'kycVerification', 'referralStats', 'referralCommissionHistory', 'referralLink', 'marketsChartData', 'transferHistoryCount'));
   }

   function settings()
   {
      return redirect()->route('profile.index');
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

   function submitIdVerification(Request $request)
   {
      $user = Auth::user();

      if ($user->kycVerification) {
         return response()->json([
            'success' => false,
            'message' => 'KYC verification already submitted. You can only submit once.'
         ], 400);
      }

      $idType = $request->id_verification_type;
      $idTypeMap = [
         'nid' => 'NID',
         'driving_license' => 'Driving License',
         'passport' => 'Passport'
      ];

      $rules = [
         'id_verification_type' => ['required', 'string', 'in:nid,driving_license,passport'],
      ];

      if ($idType === 'nid') {
         $rules['nid_front_photo'] = ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120'];
         $rules['nid_back_photo'] = ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120'];
      } elseif ($idType === 'driving_license') {
         $rules['id_license_number'] = ['required', 'string', 'max:255'];
         $rules['id_full_name'] = ['required', 'string', 'max:255'];
         $rules['id_date_of_birth'] = ['required', 'date'];
         $rules['dl_front_photo'] = ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120'];
      } elseif ($idType === 'passport') {
         $rules['id_passport_number'] = ['required', 'string', 'max:255'];
         $rules['id_full_name'] = ['required', 'string', 'max:255'];
         $rules['id_passport_expiry_date'] = [
            'required', 
            'date',
            'after:' . now()->addMonth()->format('Y-m-d')
         ];
         $rules['passport_biodata_photo'] = ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120'];
         $rules['passport_cover_photo'] = ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120'];
      }

      try {
         $validated = $request->validate($rules);
      } catch (\Illuminate\Validation\ValidationException $e) {
         return response()->json([
            'success' => false,
            'message' => 'Validation failed. Please check your input.',
            'errors' => $e->errors()
         ], 422);
      }

      try {
         $idVerificationPath = storage_path('app/public/id_verifications');
         if (!File::exists($idVerificationPath)) {
            File::makeDirectory($idVerificationPath, 0755, true);
         }

         $data = [
            'user_id' => $user->id,
            'id_type' => $idTypeMap[$idType],
            'status' => 'pending',
         ];

         if ($idType === 'nid') {
            if ($request->hasFile('nid_front_photo')) {
               $frontPhoto = $request->file('nid_front_photo');
               $frontName = time() . '_' . uniqid() . '_front.' . $frontPhoto->getClientOriginalExtension();
               $frontPath = $frontPhoto->storeAs('id_verifications', $frontName, 'public');
               $data['nid_front_photo'] = $frontPath;
            }
            if ($request->hasFile('nid_back_photo')) {
               $backPhoto = $request->file('nid_back_photo');
               $backName = time() . '_' . uniqid() . '_back.' . $backPhoto->getClientOriginalExtension();
               $backPath = $backPhoto->storeAs('id_verifications', $backName, 'public');
               $data['nid_back_photo'] = $backPath;
            }
         } elseif ($idType === 'driving_license') {
            $data['license_number'] = $validated['id_license_number'];
            $data['full_name'] = $validated['id_full_name'];
            $data['dob'] = $validated['id_date_of_birth'];
            if ($request->hasFile('dl_front_photo')) {
               $frontPhoto = $request->file('dl_front_photo');
               $frontName = time() . '_' . uniqid() . '_dl_front.' . $frontPhoto->getClientOriginalExtension();
               $frontPath = $frontPhoto->storeAs('id_verifications', $frontName, 'public');
               $data['license_front_photo'] = $frontPath;
            }
         } elseif ($idType === 'passport') {
            $data['passport_number'] = $validated['id_passport_number'];
            $data['full_name'] = $validated['id_full_name'];
            $data['passport_expiry_date'] = $validated['id_passport_expiry_date'];
            if ($request->hasFile('passport_biodata_photo')) {
               $biodataPhoto = $request->file('passport_biodata_photo');
               $biodataName = time() . '_' . uniqid() . '_passport_biodata.' . $biodataPhoto->getClientOriginalExtension();
               $biodataPath = $biodataPhoto->storeAs('id_verifications', $biodataName, 'public');
               $data['passport_biodata_photo'] = $biodataPath;
            }
            if ($request->hasFile('passport_cover_photo')) {
               $coverPhoto = $request->file('passport_cover_photo');
               $coverName = time() . '_' . uniqid() . '_passport_cover.' . $coverPhoto->getClientOriginalExtension();
               $coverPath = $coverPhoto->storeAs('id_verifications', $coverName, 'public');
               $data['passport_cover_photo'] = $coverPath;
            }
         }

         \App\Models\UserKycVerification::create($data);

         return response()->json([
            'success' => true,
            'message' => 'ID verification submitted successfully! It will be reviewed by admin.'
         ]);
      } catch (\Exception $e) {
         \Log::error('ID verification submission error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
         ]);

         return response()->json([
            'success' => false,
            'message' => 'Error submitting ID verification: ' . $e->getMessage()
         ], 500);
      }
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
    * Calculate profit/loss data grouped by date for chart (Optimized version)
    * Only includes completed trades (WON/LOST) - no pending trades for better performance
    */
   private function calculateProfitLossDataOptimized($trades)
   {
      // If no trades, return empty array
      if ($trades->isEmpty()) {
         return [];
      }

      // Optimize: Use database aggregation for daily profit/loss
      $dailyData = [];

      foreach ($trades as $trade) {
         // Use settled_at date if available, otherwise created_at
         $tradeDate = $trade->settled_at ?? $trade->created_at;
         $date = $tradeDate->format('Y-m-d');

         // Calculate profit/loss for this trade
         $amountInvested = $trade->amount_invested ?? $trade->amount ?? 0;
         if ($amountInvested <= 0) {
            continue;
         }

         $tradeStatus = strtoupper($trade->status ?? '');
         $profitLoss = 0;

         if ($tradeStatus === 'WON' || $tradeStatus === 'WIN') {
            // Win: profit = payout - cost
            $payout = $trade->payout ?? $trade->payout_amount ?? 0;
            $profitLoss = $payout - $amountInvested;
         } elseif ($tradeStatus === 'LOST' || $tradeStatus === 'LOSS') {
            // Loss: lost the full cost
            $profitLoss = -$amountInvested;
         }

         // Group by date
         if (!isset($dailyData[$date])) {
            $dailyData[$date] = [
               'date' => $date,
               'profit_loss' => 0,
            ];
         }

         $dailyData[$date]['profit_loss'] += $profitLoss;
      }

      // If no valid trades after filtering, return empty array
      if (empty($dailyData)) {
         return [];
      }

      // Calculate cumulative profit/loss over time
      $result = [];

      // Get date range from first trade to today (max 90 days back)
      $firstTradeDate = min(array_keys($dailyData));
      $startDate = \Carbon\Carbon::parse($firstTradeDate);
      $today = now();
      
      // Limit to last 90 days if first trade is older
      $maxStartDate = $today->copy()->subDays(90);
      if ($startDate < $maxStartDate) {
         $startDate = $maxStartDate;
      }

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

         // Add data point for all dates to show smooth chart
         $result[] = [
            'date' => $dateStr,
            'label' => $date->format('M d'),
            'value' => round($lastCumulative, 2), // Round to 2 decimal places
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

   /**
    * Calculate total profit/loss from all settled trades
    * Profit/Loss = sum of (payout - amount_invested) for all WON/LOST trades
    */
   private function calculateTotalProfitLoss($trades)
   {
      $totalProfitLoss = 0;

      foreach ($trades as $trade) {
         $tradeStatus = strtoupper($trade->status ?? 'PENDING');
         
         // Only calculate for settled trades
         if ($tradeStatus === 'WON' || $tradeStatus === 'WIN') {
            $amountInvested = $trade->amount_invested ?? $trade->amount ?? 0;
            $payout = $trade->payout ?? $trade->payout_amount ?? 0;
            $profitLoss = $payout - $amountInvested;
            $totalProfitLoss += $profitLoss;
         } elseif ($tradeStatus === 'LOST' || $tradeStatus === 'LOSS') {
            // Lost trades: lost the full amount invested
            $amountInvested = $trade->amount_invested ?? $trade->amount ?? 0;
            $totalProfitLoss -= $amountInvested;
         }
         // Pending trades don't contribute to profit/loss
      }

      return $totalProfitLoss;
   }

   /**
    * Get markets chart data for last 7 days (similar to dashboard)
    */
   private function getMarketsChartData()
   {
      $startDate = now()->subDays(7)->startOfDay();
      $endDate = now()->endOfDay();
      
      $labels = [];
      $marketsData = [];
      
      $currentDate = clone $startDate;
      while ($currentDate <= $endDate) {
         $periodStart = clone $currentDate;
         $periodEnd = (clone $currentDate)->endOfDay();
         
         $labels[] = $periodStart->format('M d');
         
         // Markets created in this period
         $marketsData[] = \App\Models\Market::whereBetween('created_at', [$periodStart, $periodEnd])->count();
         
         $currentDate->addDay();
      }
      
      return [
         'labels' => $labels,
         'markets' => $marketsData,
      ];
   }

   /**
    * Get trade stats for a specific time period
    */
   private function getTradeStatsForPeriod($user, $period)
   {
      $query = \App\Models\Trade::where('user_id', $user->id);
      
      if ($period === '1') {
         $query->where('created_at', '>=', now()->subDay());
      } elseif ($period === '7') {
         $query->where('created_at', '>=', now()->subDays(7));
      } elseif ($period === '30') {
         $query->where('created_at', '>=', now()->subDays(30));
      }
      // 'all' period doesn't need date filter
      
      $statsQuery = $query->selectRaw('
         COUNT(*) as total_trades,
         SUM(CASE WHEN UPPER(status) = "PENDING" THEN 1 ELSE 0 END) as pending_trades,
         SUM(CASE WHEN UPPER(status) IN ("WON", "WIN") THEN 1 ELSE 0 END) as win_trades,
         SUM(CASE WHEN UPPER(status) IN ("LOST", "LOSS") THEN 1 ELSE 0 END) as loss_trades,
         SUM(CASE WHEN UPPER(status) IN ("WON", "WIN") THEN COALESCE(payout, payout_amount, 0) ELSE 0 END) as total_payout,
         SUM(CASE WHEN UPPER(status) IN ("WON", "WIN") THEN COALESCE(amount_invested, amount, 0) ELSE 0 END) as total_invested_wins,
         SUM(CASE WHEN UPPER(status) IN ("LOST", "LOSS") THEN COALESCE(amount_invested, amount, 0) ELSE 0 END) as total_invested_losses,
         SUM(CASE WHEN UPPER(status) IN ("WON", "WIN") THEN (COALESCE(payout, payout_amount, 0) - COALESCE(amount_invested, amount, 0)) ELSE 0 END) as total_profit,
         SUM(CASE WHEN UPPER(status) IN ("LOST", "LOSS") THEN COALESCE(amount_invested, amount, 0) ELSE 0 END) as total_loss
      ')->first();
      
      $totalTrades = $statsQuery->total_trades ?? 0;
      $winRate = $totalTrades > 0 
         ? round((($statsQuery->win_trades ?? 0) / $totalTrades) * 100, 2) 
         : 0;
      
      return [
         'total_trades' => $totalTrades,
         'win_trades' => $statsQuery->win_trades ?? 0,
         'loss_trades' => $statsQuery->loss_trades ?? 0,
         'pending_trades' => $statsQuery->pending_trades ?? 0,
         'total_payout' => $statsQuery->total_payout ?? 0,
         'total_invested_wins' => $statsQuery->total_invested_wins ?? 0,
         'total_invested_losses' => $statsQuery->total_invested_losses ?? 0,
         'total_profit' => $statsQuery->total_profit ?? 0,
         'total_loss' => $statsQuery->total_loss ?? 0,
         'net_profit_loss' => ($statsQuery->total_profit ?? 0) - ($statsQuery->total_loss ?? 0),
         'win_rate' => $winRate,
      ];
   }

   /**
    * Get profit/loss chart data for a specific time period
    */
   private function getProfitLossChartDataForPeriod($user, $period)
   {
      $query = \App\Models\Trade::where('user_id', $user->id)
         ->whereIn('status', ['WON', 'WIN', 'won', 'win', 'LOST', 'LOSS', 'lost', 'loss']);
      
      // Determine date range
      $startDate = null;
      if ($period === '1') {
         $startDate = now()->subDay();
      } elseif ($period === '7') {
         $startDate = now()->subDays(7);
      } elseif ($period === '30') {
         $startDate = now()->subDays(30);
      } else {
         // 'all' - limit to last 90 days for performance
         $startDate = now()->subDays(90);
      }
      
      if ($startDate) {
         $query->where('created_at', '>=', $startDate);
      }
      
      $trades = $query->select([
         'id', 'status', 'amount_invested', 'amount',
         'payout', 'payout_amount', 'settled_at', 'created_at'
      ])
      ->orderBy('created_at', 'asc')
      ->get();
      
      if ($trades->isEmpty()) {
         return [];
      }
      
      // Calculate daily profit/loss
      $dailyData = [];
      foreach ($trades as $trade) {
         $tradeDate = $trade->settled_at ?? $trade->created_at;
         $date = $tradeDate->format('Y-m-d');
         
         $amountInvested = $trade->amount_invested ?? $trade->amount ?? 0;
         if ($amountInvested <= 0) {
            continue;
         }
         
         $tradeStatus = strtoupper($trade->status ?? '');
         $profitLoss = 0;
         
         if ($tradeStatus === 'WON' || $tradeStatus === 'WIN') {
            $payout = $trade->payout ?? $trade->payout_amount ?? 0;
            $profitLoss = $payout - $amountInvested;
         } elseif ($tradeStatus === 'LOST' || $tradeStatus === 'LOSS') {
            $profitLoss = -$amountInvested;
         }
         
         if (!isset($dailyData[$date])) {
            $dailyData[$date] = 0;
         }
         $dailyData[$date] += $profitLoss;
      }
      
      if (empty($dailyData)) {
         return [];
      }
      
      // Generate chart data points
      $chartData = [];
      $cumulative = 0;
      
      // Determine date range for chart
      $firstDate = \Carbon\Carbon::parse(min(array_keys($dailyData)));
      $lastDate = now();
      
      if ($period === '1') {
         // For 1D, show hourly data
         $current = $firstDate->copy()->startOfDay();
         while ($current <= $lastDate) {
            $dateStr = $current->format('Y-m-d');
            $hour = $current->format('H');
            
            // Add daily profit/loss if exists
            if (isset($dailyData[$dateStr])) {
               $cumulative += $dailyData[$dateStr];
            }
            
            $chartData[] = [
               'label' => $current->format('H:i'),
               'value' => round($cumulative, 2),
            ];
            
            $current->addHour();
            if ($current->diffInHours($lastDate) > 24) break;
         }
      } else {
         // For 7D, 30D, ALL - show daily data
         $current = $firstDate->copy();
         while ($current <= $lastDate) {
            $dateStr = $current->format('Y-m-d');
            
            if (isset($dailyData[$dateStr])) {
               $cumulative += $dailyData[$dateStr];
            }
            
            $chartData[] = [
               'label' => $current->format('M d'),
               'value' => round($cumulative, 2),
            ];
            
            $current->addDay();
         }
      }
      
      return $chartData;
   }
}

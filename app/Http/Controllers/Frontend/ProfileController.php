<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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

        // Get user's withdrawals
        $withdrawals = \App\Models\Withdrawal::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

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

        return view('frontend.profile', compact('user', 'wallet', 'balance', 'portfolio', 'profileImage', 'stats', 'trades', 'activePositions', 'withdrawals', 'allActivity'));
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
}

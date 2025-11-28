<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10|max:10000',
            'method' => 'required|string|in:card,bank,crypto',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();

            $wallet = Wallet::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0, 'status' => 'active']
            );

            $amount = $request->amount;
            $method = $request->method;

            $wallet->balance += $amount;
            $wallet->save();



            DB::commit();

            Log::info("Deposit successful", [
                'user_id' => $user->id,
                'amount' => $amount,
                'method' => $method,
                'new_balance' => $wallet->balance
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Deposit successful!',
                'balance' => number_format($wallet->balance, 2),
                'amount' => number_format($amount, 2)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Deposit failed", [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Deposit failed. Please try again.'
            ], 500);
        }
    }
}
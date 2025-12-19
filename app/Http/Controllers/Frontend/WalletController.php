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
    public function showPayment()
    {
        return view('frontend.payment');
    }

    public function deposit(Request $request)
    {
        // This endpoint should not be used in production
        // Deposits should be handled through proper payment gateways
        abort(404, 'Deposit endpoint not available');
    }
}

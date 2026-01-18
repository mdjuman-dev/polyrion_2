<?php

namespace App\Livewire;

use App\Models\Wallet;
use App\Models\Deposit;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Backend\BinancePayController;
use Livewire\Component;

class DepositRequest extends Component
{
    public $amount = '';
    public $payment_method = 'binancepay';
    public $query_code = '';
    public $wallet_balance = 0;
    public $currency = 'USDT';
    public $min_deposit = 10;
    
    // Binance Pay Manual Payment Details
    public $binance_wallet_address = '';
    public $binance_network = '';
    public $binance_instructions = '';

    protected $rules = [
        'amount' => 'required|numeric|min:10|max:1000000',
        'payment_method' => 'required|string|in:binancepay,manual,metamask,trustwallet',
    ];

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $wallet = Wallet::where('user_id', $user->id)
                ->where('wallet_type', Wallet::TYPE_MAIN)
                ->first();
                
            if ($wallet) {
                $this->wallet_balance = $wallet->balance;
                $this->currency = $wallet->currency;
            }
        }
        
        // Load Binance Pay manual payment details from settings
        $this->binance_wallet_address = \App\Models\GlobalSetting::getValue('binance_manual_wallet_address', '');
        $this->binance_network = \App\Models\GlobalSetting::getValue('binance_manual_network', 'BEP20');
        $this->binance_instructions = \App\Models\GlobalSetting::getValue('binance_manual_instructions', '');
    }

    public function setQuickAmount($amount)
    {
        $this->amount = $amount;
    }

    public function submit()
    {
        $this->validate();
        
        $user = Auth::user();
        if (!$user) {
            $this->addError('amount', 'You must be logged in to make a deposit.');
            return;
        }

        \Log::info('Deposit submit called', [
            'payment_method' => $this->payment_method,
            'amount' => $this->amount,
            'currency' => $this->currency
        ]);

        if ($this->payment_method === 'manual') {
            $this->validate(['query_code' => 'required|string']);
            $this->dispatch('deposit-manual', [
                'query_code' => $this->query_code,
                'amount' => $this->amount
            ]);
        } elseif ($this->payment_method === 'binancepay') {
            \Log::info('Processing Binance Pay deposit - Server side');
            
            // Create Binance payment directly on server
            try {
                $controller = new BinancePayController();
                $request = new \Illuminate\Http\Request();
                $request->merge([
                    'amount' => $this->amount,
                    'currency' => $this->currency
                ]);
                
                $response = $controller->createPayment($request);
                $data = $response->getData(true);
                
                \Log::info('Binance API response', $data);
                
                if (isset($data['success']) && $data['success'] && isset($data['checkoutUrl'])) {
                    // Redirect using Livewire 3's js() method
                    $this->js("window.location.href = '{$data['checkoutUrl']}'");
                } else {
                    $this->addError('amount', $data['message'] ?? 'Failed to create payment. Please try again.');
                }
            } catch (\Exception $e) {
                \Log::error('Binance payment error: ' . $e->getMessage());
                $this->addError('amount', 'Payment failed: ' . $e->getMessage());
            }
        } elseif ($this->payment_method === 'metamask') {
            $this->dispatch('deposit-metamask', [
                'amount' => $this->amount,
                'currency' => $this->currency
            ]);
        } elseif ($this->payment_method === 'trustwallet') {
            $this->dispatch('deposit-trustwallet', [
                'amount' => $this->amount,
                'currency' => $this->currency
            ]);
        } else {
            $this->addError('amount', 'Deposit failed. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.deposit-request');
    }
}


<?php

use App\Models\Wallet;
use App\Models\Deposit;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|numeric|min:10|max:1000000')]
    public $amount = '';

    #[Validate('required|string|in:binancepay,manual,metamask')]
    public $payment_method = 'binancepay';

    public $query_code = '';
    public $wallet_balance = 0;
    public $currency = 'USDT';
    public $min_deposit = 10;

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0, 'status' => 'active', 'currency' => 'USDT']
            );
            $this->wallet_balance = $wallet->balance;
            $this->currency = $wallet->currency;
        }
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

        if ($this->amount < $this->min_deposit) {
            $this->addError('amount', 'Minimum deposit amount is $' . $this->min_deposit);
            return;
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

            // Handle Manual Payment
            if ($this->payment_method === 'manual') {
                if (empty($this->query_code)) {
                    $this->addError('query_code', 'Transaction/Query code is required.');
                    \Illuminate\Support\Facades\DB::rollBack();
                    return;
                }

                $merchantTradeNo = 'MANUAL_' . $user->id . '_' . time() . '_' . rand(1000, 9999);

                $deposit = Deposit::create([
                    'user_id' => $user->id,
                    'merchant_trade_no' => $merchantTradeNo,
                    'amount' => $this->amount,
                    'currency' => $this->currency,
                    'status' => 'pending',
                    'payment_method' => 'manual',
                    'response_data' => [
                        'query_code' => $this->query_code,
                        'submitted_at' => now()->toDateTimeString(),
                    ],
                ]);

                \Illuminate\Support\Facades\DB::commit();

                // Livewire 3 dispatch with named arguments
                $this->dispatch('deposit-submitted', 
                    message: 'Manual payment request submitted! Your deposit is pending admin verification.',
                    balance: number_format($wallet->balance, 2)
                );

                $this->reset(['amount', 'query_code']);
                return;
            }

            // Handle Binance Pay
            if ($this->payment_method === 'binancepay') {
                \Illuminate\Support\Facades\DB::commit();
                $this->dispatch('deposit-binance', amount: $this->amount, currency: $this->currency);
                return;
            }

            // Handle MetaMask
            if ($this->payment_method === 'metamask') {
                \Illuminate\Support\Facades\DB::commit();
                $this->dispatch('deposit-metamask', amount: $this->amount, currency: $this->currency);
                return;
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Deposit Error: ' . $e->getMessage());
            $this->addError('amount', 'Deposit failed. Please try again.');
        }
    }
}; ?>

<div>
    <div class="deposit-modal-header">
        <h3>Deposit Funds</h3>
        <button type="button" class="deposit-modal-close" onclick="closeDepositModal()" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="deposit-modal-content">
        @auth
            <form wire:submit="submit" class="deposit-form-container">
                <div class="deposit-balance-info">
                    <div class="balance-item">
                        <span class="balance-label">Available Balance</span>
                        <span class="balance-value">${{ number_format($wallet_balance, 2) }} {{ $currency }}</span>
                    </div>
                </div>

                <div class="deposit-input-group">
                    <label class="deposit-input-label">Amount <span style="color: #ef4444;">*</span></label>
                    <div class="deposit-input-wrapper">
                        <span class="deposit-currency">$</span>
                        <input type="number" wire:model="amount" step="0.01" placeholder="0.00" class="deposit-input">
                    </div>
                    @error('amount') <p style="color: #ef4444; font-size: 0.85rem;">{{ $message }}</p> @enderror
                </div>

                <div class="deposit-quick-amounts">
                    @foreach([10, 50, 100, 500] as $qAmount)
                        <button type="button" class="quick-amount-btn {{ $amount == $qAmount ? 'active' : '' }}"
                            wire:click="setQuickAmount({{ $qAmount }})">${{ $qAmount }}</button>
                    @endforeach
                </div>

                <div class="deposit-method-section">
                    <label class="deposit-method-label">Payment Method</label>
                    <div class="deposit-methods">
                        <button type="button" class="deposit-method-btn {{ $payment_method === 'binancepay' ? 'active' : '' }}"
                            wire:click="$set('payment_method', 'binancepay')">
                            <i class="fas fa-coins"></i> <span>Binance Pay</span>
                        </button>
                        <button type="button" class="deposit-method-btn {{ $payment_method === 'manual' ? 'active' : '' }}"
                            wire:click="$set('payment_method', 'manual')">
                            <i class="fas fa-keyboard"></i> <span>Manual</span>
                        </button>
                        <button type="button" class="deposit-method-btn {{ $payment_method === 'metamask' ? 'active' : '' }}"
                            wire:click="$set('payment_method', 'metamask')">
                            <i class="fas fa-mask"></i> <span>MetaMask</span>
                        </button>
                    </div>
                </div>

                @if ($payment_method === 'manual')
                    <div class="deposit-input-group">
                        <label class="deposit-input-label">Transaction Code <span style="color: #ef4444;">*</span></label>
                        <div class="deposit-input-wrapper">
                            <input type="text" wire:model="query_code" class="deposit-input" placeholder="Enter transaction code">
                        </div>
                        @error('query_code') <p style="color: #ef4444; font-size: 0.85rem;">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="deposit-submit-section">
                    <button type="submit" wire:loading.attr="disabled" class="deposit-submit-btn">
                        <span wire:loading.remove>Deposit</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
            </form>
        @else
            <div class="text-center p-4">
                <h4>Login Required</h4>
                <a href="{{ route('login') }}" class="btn btn-primary">Log In</a>
            </div>
        @endauth
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        // Handle Deposit Success
        Livewire.on('deposit-submitted', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            
            if (typeof closeDepositModal === 'function') {
                closeDepositModal();
            }

            // Use showSuccess function if available, otherwise use Swal
            if (typeof showSuccess !== 'undefined') {
                showSuccess(
                    data.message || 'Deposit request submitted successfully! Your deposit is pending admin verification.',
                    'Deposit Submitted'
                );
            } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                    title: 'Deposit Submitted',
                    text: data.message || 'Deposit request submitted successfully! Your deposit is pending admin verification.',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    toast: true,
                confirmButtonColor: '#ffb11a',
                });
            } else if (typeof toastr !== 'undefined') {
                toastr.success(data.message || 'Deposit request submitted successfully!', 'Deposit Submitted');
            }

            // Reload after a delay to update balance
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        });

        // Handle MetaMask Deposit
        Livewire.on('deposit-metamask', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            
            if (typeof showInfo !== 'undefined') {
                showInfo(
                    `Please send ${data.amount} ${data.currency} to our MetaMask wallet. Your transaction will be verified automatically.`,
                    'MetaMask Deposit'
                );
            } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                    icon: 'info',
                    title: 'MetaMask Deposit',
                    text: `Please send ${data.amount} ${data.currency} to our MetaMask wallet. Your transaction will be verified automatically.`,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    toast: true,
                });
            } else if (typeof toastr !== 'undefined') {
                toastr.info(`Please send ${data.amount} ${data.currency} to our MetaMask wallet.`, 'MetaMask Deposit');
            }
        });
        
        // Handle Binance Pay Redirect
        Livewire.on('deposit-binance', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            if (data.checkoutUrl) {
                window.location.href = data.checkoutUrl;
            }
        });
    });
</script>
@endpush
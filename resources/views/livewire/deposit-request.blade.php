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
            $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0, 'status' => 'active', 'currency' => 'USDT']);
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

        $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'status' => 'active',
                'currency' => 'USDT',
            ]);
        }

        // Check minimum deposit
        if ($this->amount < $this->min_deposit) {
            $this->addError('amount', 'Minimum deposit amount is $' . $this->min_deposit);
            return;
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Handle Manual Payment
            if ($this->payment_method === 'manual') {
                if (empty($this->query_code)) {
                    $this->addError('query_code', 'Transaction/Query code is required for manual payment.');
                    \Illuminate\Support\Facades\DB::rollBack();
                    return;
                }

                // Generate unique merchant trade number
                $merchantTradeNo = 'MANUAL_' . $user->id . '_' . time() . '_' . rand(1000, 9999);

                // Create deposit record with pending status (requires admin verification)
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

                \Illuminate\Support\Facades\Log::info('Manual deposit request created', [
                    'user_id' => $user->id,
                    'deposit_id' => $deposit->id,
                    'amount' => $this->amount,
                    'query_code' => $this->query_code,
                    'merchant_trade_no' => $merchantTradeNo,
                ]);

                $this->dispatch('deposit-submitted', [
                    'message' => 'Manual payment request submitted successfully! Your deposit is pending admin verification. You will be notified once it\'s approved.',
                    'balance' => number_format($wallet->balance, 2),
                    'amount' => number_format($this->amount, 2),
                ]);

                // Reset form
                $this->reset(['amount', 'query_code']);
                $this->wallet_balance = $wallet->balance;
                return;
            }

            // Handle Binance Pay
            if ($this->payment_method === 'binancepay') {
                \Illuminate\Support\Facades\DB::commit();

                $this->dispatch('deposit-binance', [
                    'amount' => $this->amount,
                    'currency' => $this->currency,
                ]);
                return;
            }

            // Handle MetaMask
            if ($this->payment_method === 'metamask') {
                \Illuminate\Support\Facades\DB::commit();

                $this->dispatch('deposit-metamask', [
                    'amount' => $this->amount,
                    'currency' => $this->currency,
                ]);
                return;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
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
                <!-- Balance Info -->
                <div class="deposit-balance-info">
                    <div class="balance-item">
                        <span class="balance-label">Available Balance</span>
                        <span class="balance-value">${{ number_format($wallet_balance, 2) }} {{ $currency }}</span>
                    </div>
                    <div class="balance-item"
                        style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--border);">
                        <span class="balance-label">Minimum Deposit</span>
                        <span class="balance-label"
                            style="color: var(--text-primary); font-weight: 500;">${{ $min_deposit }}
                            {{ $currency }}</span>
                    </div>
                </div>

                <!-- Amount Input -->
                <div class="deposit-input-group">
                    <label class="deposit-input-label">Amount <span style="color: #ef4444;">*</span></label>
                    <div class="deposit-input-wrapper">
                        <span class="deposit-currency">$</span>
                        <input type="number" wire:model="amount" step="0.01" min="{{ $min_deposit }}"
                            placeholder="0.00" class="deposit-input" style="font-size: 16px;">
                    </div>
                    @error('amount')
                        <p style="color: #ef4444; font-size: 0.85rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quick Amount Buttons -->
                <div class="deposit-quick-amounts">
                    <button type="button" class="quick-amount-btn {{ $amount == 10 ? 'active' : '' }}"
                        wire:click="setQuickAmount(10)">$10</button>
                    <button type="button" class="quick-amount-btn {{ $amount == 50 ? 'active' : '' }}"
                        wire:click="setQuickAmount(50)">$50</button>
                    <button type="button" class="quick-amount-btn {{ $amount == 100 ? 'active' : '' }}"
                        wire:click="setQuickAmount(100)">$100</button>
                    <button type="button" class="quick-amount-btn {{ $amount == 500 ? 'active' : '' }}"
                        wire:click="setQuickAmount(500)">$500</button>
                </div>

                <!-- Payment Method -->
                <div class="deposit-method-section">
                    <label class="deposit-method-label">Payment Method</label>
                    <div class="deposit-methods">
                        <button type="button"
                            class="deposit-method-btn {{ $payment_method === 'binancepay' ? 'active' : '' }}"
                            wire:click="$set('payment_method', 'binancepay')">
                            <i class="fas fa-coins"></i>
                            <span>Binance Pay</span>
                        </button>
                        <button type="button"
                            class="deposit-method-btn {{ $payment_method === 'manual' ? 'active' : '' }}"
                            wire:click="$set('payment_method', 'manual')">
                            <i class="fas fa-keyboard"></i>
                            <span>Manual Payment</span>
                        </button>
                        <button type="button"
                            class="deposit-method-btn {{ $payment_method === 'metamask' ? 'active' : '' }}"
                            wire:click="$set('payment_method', 'metamask')">
                            <i class="fas fa-mask"></i>
                            <span>MetaMask</span>
                        </button>
                    </div>
                </div>

                <!-- Query Code Field - Shown for manual payment -->
                @if ($payment_method === 'manual')
                    <div class="deposit-input-group">
                        <label class="deposit-input-label">Transaction/Query Code <span
                                style="color: #ef4444;">*</span></label>
                        <div class="deposit-input-wrapper">
                            <span class="deposit-currency"><i class="fas fa-barcode"></i></span>
                            <input type="text" wire:model="query_code" class="deposit-input"
                                placeholder="Enter transaction or merchant trade number">
                        </div>
                        @error('query_code')
                            <p style="color: #ef4444; font-size: 0.85rem; margin-top: 0.5rem;">{{ $message }}</p>
                        @enderror
                        <small class="text-muted"
                            style="display: block; margin-top: 5px; font-size: 12px; color: var(--text-secondary);">
                            <i class="fas fa-info-circle"></i> Enter your Binance Pay transaction code or merchant trade
                            number
                        </small>
                    </div>
                @endif

                <!-- Submit Button -->
                <div class="deposit-submit-section">
                    <button type="submit" wire:loading.attr="disabled" class="deposit-submit-btn">
                        <span wire:loading.remove>
                            <i class="fas fa-arrow-right"></i> Deposit
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin"></i> Processing...
                        </span>
                    </button>
                </div>

                <!-- Footer Note -->
                <div class="deposit-footer">
                    <p class="deposit-note">
                        <i class="fas fa-info-circle"></i>
                        <span>
                            @if ($payment_method === 'manual')
                                Minimum deposit: ${{ $min_deposit }}. Enter your transaction code for manual verification.
                            @else
                                Minimum deposit: ${{ $min_deposit }}. Your payment will be processed securely.
                            @endif
                        </span>
                    </p>
                </div>
            </form>
        @else
            <div class="deposit-form-container" style="text-align: center; padding: 2rem;">
                <div style="margin-bottom: 1.5rem;">
                    <i class="fas fa-lock"
                        style="font-size: 3rem; color: var(--text-secondary, #666); margin-bottom: 1rem;"></i>
                </div>
                <h4 style="margin-bottom: 1rem; color: var(--text-primary);">Login Required</h4>
                <p style="color: var(--text-secondary, #666); margin-bottom: 2rem;">
                    You must be logged in to make a deposit.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <a href="{{ route('login') }}" class="deposit-submit-btn"
                        style="text-decoration: none; display: inline-block;">
                        <i class="fas fa-sign-in-alt"></i> Log In
                    </a>
                    <a href="{{ route('register') }}" class="deposit-submit-btn"
                        style="text-decoration: none; display: inline-block; background: var(--accent, #ffb11a);">
                        <i class="fas fa-user-plus"></i> Sign Up
                    </a>
                </div>
            </div>
        @endauth
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('deposit-submitted', (data) => {
                if (typeof closeDepositModal === 'function') {
                    closeDepositModal();
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deposit Successful!',
                        html: `<div style="text-align: left;">
                        <p style="margin-bottom: 10px;">${data.message || 'Deposit successful!'}</p>
                        ${data.balance ? `<p style="margin-top: 10px; font-size: 0.9rem; color: var(--text-secondary);"><strong>New Balance:</strong> $${data.balance}</p>` : ''}
                    </div>`,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ffb11a',
                        background: 'var(--card-bg)',
                        color: 'var(--text-primary)',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deposit Successful!',
                        text: data.message || 'Deposit successful!',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ffb11a'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    alert(data.message || 'Deposit successful!');
                    window.location.reload();
                }
            });

            Livewire.on('deposit-binance', (data) => {
                if (typeof closeDepositModal === 'function') {
                    closeDepositModal();
                }
                if (data.checkoutUrl) {
                    window.location.href = data.checkoutUrl;
                }
            });

            Livewire.on('deposit-metamask', (data) => {
                if (typeof closeDepositModal === 'function') {
                    closeDepositModal();
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'MetaMask Payment Instructions',
                        html: `<div style="text-align: left; color: var(--text-primary, #333);">
                        <p>Please send <strong>${data.amount} ${data.currency}</strong> to the following address:</p>
                        <p style="word-break: break-all; font-weight: bold; color: var(--accent);"><code>${data.merchant_address || 'Address will be provided'}</code></p>
                        <p>Network: <strong>${data.network || 'Ethereum'}</strong></p>
                        <p style="margin-top: 15px; font-size: 0.9em; color: var(--text-secondary);">
                            Once the transaction is confirmed on the blockchain, your balance will be updated.
                        </p>
                    </div>`,
                        icon: 'info',
                        confirmButtonText: 'I have sent the funds',
                        confirmButtonColor: '#ffb11a',
                        background: 'var(--card-bg)',
                        color: 'var(--text-primary)',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                }
            });
        });

        // Also listen for Livewire 3 events
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('deposit-submitted', (data) => {
                if (typeof closeDepositModal === 'function') {
                    closeDepositModal();
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deposit Successful!',
                        html: `<div style="text-align: left;">
                        <p style="margin-bottom: 10px;">${data.message || 'Deposit successful!'}</p>
                        ${data.balance ? `<p style="margin-top: 10px; font-size: 0.9rem; color: var(--text-secondary);"><strong>New Balance:</strong> $${data.balance}</p>` : ''}
                    </div>`,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ffb11a',
                        background: 'var(--card-bg)',
                        color: 'var(--text-primary)',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deposit Successful!',
                        text: data.message || 'Deposit successful!',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ffb11a'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    alert(data.message || 'Deposit successful!');
                    window.location.reload();
                }
            });

            Livewire.on('deposit-binance', (data) => {
                if (typeof closeDepositModal === 'function') {
                    closeDepositModal();
                }
                if (data.checkoutUrl) {
                    window.location.href = data.checkoutUrl;
                }
            });

            Livewire.on('deposit-metamask', (data) => {
                if (typeof closeDepositModal === 'function') {
                    closeDepositModal();
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'MetaMask Payment Instructions',
                        html: `<div style="text-align: left; color: var(--text-primary, #333);">
                        <p>Please send <strong>${data.amount} ${data.currency}</strong> to the following address:</p>
                        <p style="word-break: break-all; font-weight: bold; color: var(--accent);"><code>${data.merchant_address || 'Address will be provided'}</code></p>
                        <p>Network: <strong>${data.network || 'Ethereum'}</strong></p>
                        <p style="margin-top: 15px; font-size: 0.9em; color: var(--text-secondary);">
                            Once the transaction is confirmed on the blockchain, your balance will be updated.
                        </p>
                    </div>`,
                        icon: 'info',
                        confirmButtonText: 'I have sent the funds',
                        confirmButtonColor: '#ffb11a',
                        background: 'var(--card-bg)',
                        color: 'var(--text-primary)',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                }
            });
        });
    </script>
@endpush

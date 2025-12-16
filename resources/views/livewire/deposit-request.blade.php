<?php

use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|numeric|min:10|max:1000000')]
    public $amount = '';

    #[Validate('required|string|in:demo,binancepay,manual,metamask')]
    public $payment_method = 'demo';

    public $query_code = '';

    public $wallet_balance = 0;
    public $currency = 'USDT';
    public $min_deposit = 10;

    public function mount()
    {
        $user = Auth::user();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0, 'status' => 'active', 'currency' => 'USDT']);
        $this->wallet_balance = $wallet->balance;
        $this->currency = $wallet->currency;
    }

    public function setQuickAmount($amount)
    {
        $this->amount = $amount;
    }

    public function submit()
    {
        $this->validate();

        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

        // Check minimum deposit
        if ($this->amount < $this->min_deposit) {
            $this->addError('amount', 'Minimum deposit amount is $' . $this->min_deposit);
            return;
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Handle Demo Money (instant deposit)
            if ($this->payment_method === 'demo') {
                $balanceBefore = (float) $wallet->balance;
                $wallet->balance += $this->amount;
                $wallet->save();

                // Create wallet transaction
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'deposit',
                    'amount' => $this->amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $wallet->balance,
                    'description' => 'Demo money deposit - Test',
                    'metadata' => [
                        'payment_method' => 'demo',
                    ],
                ]);

                \Illuminate\Support\Facades\DB::commit();

                $this->dispatch('deposit-submitted', [
                    'message' => 'Demo money added successfully!',
                    'balance' => number_format($wallet->balance, 2),
                    'amount' => number_format($this->amount, 2),
                ]);

                // Reset form
                $this->reset(['amount', 'query_code']);
                $this->wallet_balance = $wallet->balance;
                return;
            }

            // Handle Manual Payment
            if ($this->payment_method === 'manual') {
                if (empty($this->query_code)) {
                    $this->addError('query_code', 'Transaction/Query code is required for manual payment.');
                    return;
                }

                // For now, just add the amount (in production, this would verify the query code first)
                $balanceBefore = (float) $wallet->balance;
                $wallet->balance += $this->amount;
                $wallet->save();

                // Create wallet transaction
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'deposit',
                    'amount' => $this->amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $wallet->balance,
                    'description' => 'Manual payment deposit - Query Code: ' . $this->query_code,
                    'metadata' => [
                        'payment_method' => 'manual',
                        'query_code' => $this->query_code,
                    ],
                ]);

                \Illuminate\Support\Facades\DB::commit();

                $this->dispatch('deposit-submitted', [
                    'message' => 'Manual payment processed successfully!',
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
    <div class="deposit-modal-popup">
        <div class="deposit-modal-header">
            <h3>Deposit Funds</h3>
            <button type="button" class="deposit-modal-close" onclick="closeDepositModal()" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="deposit-modal-content">
            <form wire:submit="submit" class="deposit-form-container">
                <!-- Balance Info -->
                <div class="deposit-balance-info">
                    <div class="balance-item">
                        <span class="balance-label">Current Balance</span>
                        <span class="balance-value">${{ number_format($wallet_balance, 2) }}</span>
                    </div>
                    <div class="balance-item" style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--border);">
                        <span class="balance-label">Minimum Deposit</span>
                        <span class="balance-label" style="color: var(--text-primary); font-weight: 500;">${{ $min_deposit }}</span>
                    </div>
                </div>

                <!-- Amount Input -->
                <div class="deposit-input-group">
                    <label class="deposit-input-label">Amount <span style="color: #ef4444;">*</span></label>
                    <div class="deposit-input-wrapper">
                        <span class="deposit-currency">$</span>
                        <input type="number" wire:model="amount" step="0.01" min="{{ $min_deposit }}"
                            placeholder="0.00" class="deposit-input">
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
                        <button type="button" class="deposit-method-btn {{ $payment_method === 'demo' ? 'active' : '' }}"
                            wire:click="$set('payment_method', 'demo')">
                            <i class="fas fa-flask"></i>
                            <span>Demo Money (Test)</span>
                        </button>
                        <button type="button" class="deposit-method-btn {{ $payment_method === 'binancepay' ? 'active' : '' }}"
                            wire:click="$set('payment_method', 'binancepay')">
                            <i class="fas fa-coins"></i>
                            <span>Binance Pay</span>
                        </button>
                        <button type="button" class="deposit-method-btn {{ $payment_method === 'manual' ? 'active' : '' }}"
                            wire:click="$set('payment_method', 'manual')">
                            <i class="fas fa-keyboard"></i>
                            <span>Manual Payment</span>
                        </button>
                        <button type="button" class="deposit-method-btn {{ $payment_method === 'metamask' ? 'active' : '' }}"
                            wire:click="$set('payment_method', 'metamask')">
                            <i class="fas fa-mask"></i>
                            <span>MetaMask</span>
                        </button>
                    </div>
                </div>

                <!-- Query Code Field - Shown for manual payment -->
                @if ($payment_method === 'manual')
                    <div class="deposit-input-group">
                        <label class="deposit-input-label">Transaction/Query Code <span style="color: #ef4444;">*</span></label>
                        <div class="deposit-input-wrapper">
                            <span class="deposit-currency"><i class="fas fa-barcode"></i></span>
                            <input type="text" wire:model="query_code" class="deposit-input"
                                placeholder="Enter transaction or merchant trade number">
                        </div>
                        @error('query_code')
                            <p style="color: #ef4444; font-size: 0.85rem; margin-top: 0.5rem;">{{ $message }}</p>
                        @enderror
                        <small class="text-muted" style="display: block; margin-top: 5px; font-size: 12px; color: var(--text-secondary);">
                            <i class="fas fa-info-circle"></i> Enter your Binance Pay transaction code or merchant trade number
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
                            @if($payment_method === 'demo')
                                Demo money for testing purposes. Amount will be added instantly to your wallet.
                            @elseif($payment_method === 'manual')
                                Minimum deposit: ${{ $min_deposit }}. Enter your transaction code for manual verification.
                            @else
                                Minimum deposit: ${{ $min_deposit }}. Your payment will be processed securely.
                            @endif
                        </span>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('deposit-submitted', (data) => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deposit Successful!',
                        html: `${data.message}<br><strong>Amount:</strong> $${data.amount}<br><strong>New Balance:</strong> $${data.balance}`,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ffb11a',
                        background: 'var(--card-bg)',
                        color: 'var(--text-primary)'
                    }).then(() => {
                        closeDepositModal();
                        window.location.reload();
                    });
                } else {
                    alert(data.message || 'Deposit successful!');
                    closeDepositModal();
                    window.location.reload();
                }
            });

            Livewire.on('deposit-binance', (data) => {
                // Handle Binance Pay redirect
                $.ajax({
                    url: '{{ route('binance.create') }}',
                    method: 'POST',
                    data: {
                        amount: data.amount,
                        currency: data.currency || 'USDT',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success && response.checkoutUrl) {
                            closeDepositModal();
                            window.location.href = response.checkoutUrl;
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Payment Error',
                                    text: response.message || 'Failed to create payment. Please try again.',
                                    confirmButtonColor: '#ffb11a'
                                });
                            } else {
                                alert(response.message || 'Failed to create payment. Please try again.');
                            }
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = "Failed to create payment. Please try again.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Payment Error',
                                text: errorMessage,
                                confirmButtonColor: '#ffb11a'
                            });
                        } else {
                            alert(errorMessage);
                        }
                    }
                });
            });

            Livewire.on('deposit-metamask', (data) => {
                // Handle MetaMask deposit
                // This would trigger the MetaMask payment flow
                if (typeof showInfo !== 'undefined') {
                    showInfo('MetaMask deposit functionality will be implemented soon.', 'Coming Soon');
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Coming Soon',
                        text: 'MetaMask deposit functionality will be implemented soon.',
                        confirmButtonColor: '#ffb11a'
                    });
                } else {
                    alert('MetaMask deposit functionality will be implemented soon.');
                }
            });
        });
    </script>
</div>


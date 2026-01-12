<?php

use App\Models\Wallet;
use App\Models\Deposit;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|numeric|min:10|max:1000000')]
    public $amount = '';

    #[Validate('required|string|in:binancepay,manual,metamask,trustwallet')]
    public $payment_method = 'binancepay';

    public $query_code = '';
    public $wallet_balance = 0;
    public $currency = 'USDT';
    public $min_deposit = 10;
    
    // Binance Pay Manual Payment Details
    public $binance_wallet_address = '';
    public $binance_network = '';
    public $binance_instructions = '';

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

            // Handle MetaMask or Trust Wallet
            if ($this->payment_method === 'metamask' || $this->payment_method === 'trustwallet') {
                \Illuminate\Support\Facades\DB::commit();
                $this->dispatch('deposit-web3', 
                    amount: $this->amount, 
                    currency: $this->currency,
                    walletType: $this->payment_method
                );
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
                    <div class="deposit-methods d-flex justify-content-between">
                        <button type="button" class="deposit-method-btn {{ $payment_method === 'binancepay' ? 'active' : '' }}"
                            wire:click="$set('payment_method', 'binancepay')">
                            <i class="fas fa-coins"></i> <span>Binance</span>
                        </button>
                        <button type="button" class="deposit-method-btn {{ $payment_method === 'metamask' ? 'active' : '' }}"
                            wire:click="$set('payment_method', 'metamask')">
                            <i class="fab fa-ethereum"></i> <span>MetaMask</span>
                        </button>
                        <button type="button" class="deposit-method-btn {{ $payment_method === 'trustwallet' ? 'active' : '' }}"
                            wire:click="$set('payment_method', 'trustwallet')">
                            <i class="fas fa-shield-alt"></i> <span>Trust Wallet</span>
                        </button>
                        <button type="button" class="deposit-method-btn {{ $payment_method === 'manual' ? 'active' : '' }}"
                        wire:click="$set('payment_method', 'manual')">
                        <i class="fas fa-keyboard"></i> <span>Manual</span>
                    </button>
                    </div>
                </div>

                @if ($payment_method === 'manual')
                    <!-- Binance Pay Details Section -->
                    @if(!empty($binance_wallet_address))
                    <div class="deposit-info-box" style="background: var(--card-bg); border: 1px solid var(--border); padding: 16px; border-radius: 8px; margin-top: 12px; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                            <i class="fas fa-coins" style="color: var(--accent); font-size: 18px;"></i>
                            <h4 style="margin: 0; color: var(--text-primary); font-size: 16px; font-weight: 600;">Binance Pay Payment Details</h4>
                        </div>
                        
                        <div style="margin-bottom: 12px;">
                            <label style="display: block; color: var(--text-secondary, rgba(255,255,255,0.6)); font-size: 12px; font-weight: 600; margin-bottom: 4px; text-transform: uppercase;">Wallet Address</label>
                            <div style="display: flex; align-items: center; gap: 8px; background: var(--secondary, rgba(255,255,255,0.05)); padding: 10px 12px; border-radius: 6px; border: 1px solid var(--border);">
                                <code style="flex: 1; color: var(--text-primary); font-size: 13px; word-break: break-all; font-family: 'Courier New', monospace;">{{ $binance_wallet_address }}</code>
                                <button type="button" id="copyWalletBtn" onclick="copyToClipboard('{{ $binance_wallet_address }}', this)" 
                                    style="background: var(--accent); color: #000; border: none; padding: 8px; width: 36px; height: 36px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; flex-shrink: 0;"
                                    title="Copy wallet address">
                                    <i class="fas fa-copy" id="copyWalletIcon"></i>
                                </button>
                            </div>
                        </div>
                        
                        @if(!empty($binance_network))
                        <div style="margin-bottom: 12px;">
                            <label style="display: block; color: var(--text-secondary, rgba(255,255,255,0.6)); font-size: 12px; font-weight: 600; margin-bottom: 4px; text-transform: uppercase;">Network</label>
                            <div style="background: var(--secondary, rgba(255,255,255,0.05)); padding: 10px 12px; border-radius: 6px; border: 1px solid var(--border);">
                                <span style="color: var(--text-primary); font-size: 14px; font-weight: 500;">{{ $binance_network }}</span>
                            </div>
                        </div>
                        @endif
                        
                        @if(!empty($binance_instructions))
                        <div style="margin-bottom: 12px;">
                            <label style="display: block; color: var(--text-secondary, rgba(255,255,255,0.6)); font-size: 12px; font-weight: 600; margin-bottom: 4px; text-transform: uppercase;">Instructions</label>
                            <div style="background: var(--secondary, rgba(255,255,255,0.05)); padding: 12px; border-radius: 6px; border: 1px solid var(--border);">
                                <p style="margin: 0; color: var(--text-primary); font-size: 13px; line-height: 1.6; white-space: pre-line;">{{ $binance_instructions }}</p>
                            </div>
                        </div>
                        @endif
                        
                        <div style="background: var(--hover, rgba(255,177,26,0.1)); border-left: 3px solid var(--accent); padding: 10px 12px; border-radius: 4px; margin-top: 12px;">
                            <p style="margin: 0; color: var(--text-primary); font-size: 12px; line-height: 1.5;">
                                <i class="fas fa-info-circle" style="margin-right: 6px; color: var(--accent);"></i>
                                <strong>Important:</strong> After sending payment, enter the transaction code below to verify your deposit.
                            </p>
                        </div>
                    </div>
                    @endif
                    
                    <div class="deposit-input-group">
                        <label class="deposit-input-label">Transaction Code <span style="color: #ef4444;">*</span></label>
                        <div class="deposit-input-wrapper">
                            <input type="text" wire:model="query_code" class="deposit-input" placeholder="Enter Binance Pay transaction code">
                        </div>
                        <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">
                            Enter the transaction code/hash from your Binance Pay payment
                        </small>
                        @error('query_code') <p style="color: #ef4444; font-size: 0.85rem;">{{ $message }}</p> @enderror
                    </div>
                @endif

                @if ($payment_method === 'metamask' || $payment_method === 'trustwallet')
                    <div class="deposit-info-box" style="background: #fef3c7; border: 1px solid #fbbf24; padding: 12px; border-radius: 8px; margin-top: 12px;">
                        <p style="margin: 0; font-size: 0.875rem; color: #92400e;">
                            <i class="fas fa-info-circle" style="margin-right: 6px;"></i>
                            <strong>{{ $payment_method === 'metamask' ? 'MetaMask' : 'Trust Wallet' }} Payment:</strong> 
                            You'll be prompted to connect your wallet and approve the transaction.
                        </p>
                    </div>
                @endif

                <div class="deposit-submit-section">
                    <button type="submit" wire:loading.attr="disabled" class="deposit-submit-btn">
                        <span wire:loading.remove>
                            @if($payment_method === 'metamask')
                                <i class="fab fa-ethereum"></i> Connect MetaMask
                            @elseif($payment_method === 'trustwallet')
                                <i class="fas fa-shield-alt"></i> Connect Trust Wallet
                            @else
                                Deposit
                            @endif
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin"></i> Processing...
                        </span>
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
    function copyToClipboard(text, button) {
        const icon = button.querySelector('i');
        const originalClass = icon.className;
        const originalTitle = button.getAttribute('title') || 'Copy wallet address';
        
        navigator.clipboard.writeText(text).then(function() {
            // Change icon to checkmark
            icon.className = 'fas fa-check';
            button.style.background = '#10b981';
            button.setAttribute('title', 'Copied!');
            
            // Reset after 2 seconds
            setTimeout(function() {
                icon.className = originalClass;
                button.style.background = 'var(--accent)';
                button.setAttribute('title', originalTitle);
            }, 2000);
        }).catch(function(err) {
            console.error('Failed to copy:', err);
        });
    }

    document.addEventListener('livewire:init', () => {
        // Handle Deposit Success
        Livewire.on('deposit-submitted', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            
            if (typeof closeDepositModal === 'function') {
                closeDepositModal();
            }

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

            setTimeout(() => {
                window.location.reload();
            }, 2000);
        });

        // Handle Web3 Wallet Deposits (MetaMask & Trust Wallet)
        Livewire.on('deposit-web3', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            const walletName = data.walletType === 'trustwallet' ? 'Trust Wallet' : 'MetaMask';
            
            // Check if the handleWeb3Deposit function exists
            if (typeof handleWeb3Deposit === 'function') {
                // Get the submit button
                const $btn = $('.deposit-submit-btn');
                const originalText = $btn.html();
                
                // Call the Web3 deposit handler
                handleWeb3Deposit(
                    data.amount, 
                    data.currency, 
                    $btn, 
                    originalText, 
                    data.walletType
                );
            } else {
                // Fallback if function is not loaded
                if (typeof showError !== 'undefined') {
                    showError(
                        `${walletName} integration is not loaded. Please refresh the page and try again.`,
                        'Integration Error'
                    );
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Integration Error',
                        text: `${walletName} integration is not loaded. Please refresh the page and try again.`,
                        confirmButtonColor: '#ef4444',
                    });
                } else {
                    alert(`${walletName} integration is not loaded. Please refresh the page and try again.`);
                }
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
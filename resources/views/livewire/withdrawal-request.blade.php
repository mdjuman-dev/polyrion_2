<?php

use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|numeric|min:1')]
    public $amount = '';

    #[Validate('required|string|in:bank,crypto,paypal')]
    public $payment_method = '';

    // Bank details
    public $bank_name = '';
    public $account_number = '';
    public $account_holder = '';
    public $swift_code = '';

    // Crypto details
    public $crypto_type = 'USDT';
    public $wallet_address = '';
    public $network = '';

    // PayPal details
    public $paypal_email = '';

    public $wallet_balance = 0;
    public $currency = 'USDT';
    public $min_withdrawal = 10;

    public function mount()
    {
        $user = Auth::user();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0, 'status' => 'active', 'currency' => 'USDT']);
        $this->wallet_balance = $wallet->balance;
        $this->currency = $wallet->currency;
    }

    public function submit()
    {
        $this->validate();

        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

        // Check balance
        if ($wallet->balance < $this->amount) {
            $this->addError('amount', 'Insufficient balance. Your current balance is ' . number_format($wallet->balance, 2) . ' ' . $this->currency);
            return;
        }

        // Check minimum withdrawal
        if ($this->amount < $this->min_withdrawal) {
            $this->addError('amount', 'Minimum withdrawal amount is ' . $this->min_withdrawal . ' ' . $this->currency);
            return;
        }

        // Collect payment details
        $payment_details = [];
        if ($this->payment_method === 'bank') {
            $payment_details = [
                'bank_name' => $this->bank_name,
                'account_number' => $this->account_number,
                'account_holder' => $this->account_holder,
                'swift_code' => $this->swift_code,
            ];
        } elseif ($this->payment_method === 'crypto') {
            $payment_details = [
                'crypto_type' => $this->crypto_type,
                'wallet_address' => $this->wallet_address,
                'network' => $this->network,
            ];
        } elseif ($this->payment_method === 'paypal') {
            $payment_details = [
                'paypal_email' => $this->paypal_email,
            ];
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Lock amount from wallet
            $balanceBefore = (float) $wallet->balance;
            $wallet->balance -= $this->amount;
            $wallet->save();

            // Create withdrawal request
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $this->amount,
                'currency' => $this->currency,
                'status' => 'pending',
                'payment_method' => $this->payment_method,
                'payment_details' => $payment_details,
            ]);

            // Create wallet transaction
            \App\Models\WalletTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'type' => 'withdraw',
                'amount' => -$this->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'reference_type' => Withdrawal::class,
                'reference_id' => $withdrawal->id,
                'description' => 'Withdrawal request - ' . ucfirst($this->payment_method),
                'metadata' => [
                    'payment_method' => $this->payment_method,
                    'payment_details' => $payment_details,
                ],
            ]);

            \Illuminate\Support\Facades\DB::commit();

            $this->dispatch('withdrawal-submitted', [
                'message' => 'Withdrawal request submitted successfully. It will be reviewed by admin.',
                'balance' => number_format($wallet->balance, 2),
            ]);

            // Reset form
            $this->reset(['amount', 'payment_method', 'bank_name', 'account_number', 'account_holder', 'swift_code', 'crypto_type', 'wallet_address', 'network', 'paypal_email']);
            $this->wallet_balance = $wallet->balance;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Withdrawal request failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'amount' => $this->amount,
                'payment_method' => $this->payment_method,
                'trace' => $e->getTraceAsString(),
            ]);
            $this->addError('amount', 'Withdrawal request failed: ' . $e->getMessage());
        }
    }
}; ?>

<div>
    <div class="withdrawal-modal-header">
        <h3>Request Withdrawal</h3>
        <button type="button" class="withdrawal-modal-close" onclick="closeWithdrawalModal()" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="withdrawal-modal-content">
        <form wire:submit="submit" class="withdrawal-form-container">
            <!-- Balance Info -->
            <div class="withdrawal-balance-info">
                <div class="balance-item">
                    <span class="balance-label">Available Balance</span>
                    <span class="balance-value">${{ number_format($wallet_balance, 2) }} {{ $currency }}</span>
                </div>
                <div class="balance-item"
                    style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--border);">
                    <span class="balance-label">Minimum Withdrawal</span>
                    <span class="balance-label"
                        style="color: var(--text-primary); font-weight: 500;">{{ $min_withdrawal }}
                        {{ $currency }}</span>
                </div>
            </div>

            <!-- Amount Input -->
            <div class="withdrawal-input-group">
                <label class="withdrawal-input-label">Amount <span style="color: #ef4444;">*</span></label>
                <div class="withdrawal-input-wrapper">
                    <span class="withdrawal-currency">$</span>
                    <input type="number" wire:model="amount" step="0.01" min="{{ $min_withdrawal }}"
                        max="{{ $wallet_balance }}" placeholder="0.00" class="withdrawal-input">
                </div>
                @error('amount')
                    <p style="color: #ef4444; font-size: 0.85rem; margin-top: 0.5rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Payment Method -->
            <div class="withdrawal-input-group">
                <label class="withdrawal-input-label">Payment Method <span style="color: #ef4444;">*</span></label>
                <select wire:model.live="payment_method" class="withdrawal-select">
                    <option value="">Select Payment Method</option>
                    <option value="bank">🏦 Bank Transfer</option>
                    <option value="crypto">₿ Cryptocurrency</option>
                    <option value="paypal">💳 PayPal</option>
                </select>
                @error('payment_method')
                    <p style="color: #ef4444; font-size: 0.85rem; margin-top: 0.5rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bank Details -->
            @if ($payment_method === 'bank')
                <div class="withdrawal-payment-details">
                    <h4 class="withdrawal-details-title">
                        <i class="fas fa-university"></i> Bank Account Details
                    </h4>
                    <div class="withdrawal-details-fields">
                        <div class="withdrawal-input-group">
                            <label class="withdrawal-input-label">Bank Name</label>
                            <input type="text" wire:model="bank_name" placeholder="Enter bank name"
                                class="withdrawal-input">
                        </div>
                        <div class="withdrawal-input-group">
                            <label class="withdrawal-input-label">Account Number</label>
                            <input type="text" wire:model="account_number" placeholder="Enter account number"
                                class="withdrawal-input">
                        </div>
                        <div class="withdrawal-input-group">
                            <label class="withdrawal-input-label">Account Holder Name</label>
                            <input type="text" wire:model="account_holder" placeholder="Enter account holder name"
                                class="withdrawal-input">
                        </div>
                        <div class="withdrawal-input-group">
                            <label class="withdrawal-input-label">SWIFT/IBAN Code</label>
                            <input type="text" wire:model="swift_code" placeholder="Enter SWIFT or IBAN code"
                                class="withdrawal-input">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Crypto Details -->
            @if ($payment_method === 'crypto')
                <div class="withdrawal-payment-details">
                    <h4 class="withdrawal-details-title">
                        <i class="fab fa-bitcoin"></i> Cryptocurrency Details
                    </h4>
                    <div class="withdrawal-details-fields">
                        <div class="withdrawal-input-group">
                            <label class="withdrawal-input-label">Cryptocurrency Type</label>
                            <select wire:model="crypto_type" class="withdrawal-select">
                                <option value="USDT">USDT</option>
                                <option value="BTC">Bitcoin (BTC)</option>
                                <option value="ETH">Ethereum (ETH)</option>
                                <option value="BNB">Binance Coin (BNB)</option>
                            </select>
                        </div>
                        <div class="withdrawal-input-group">
                            <label class="withdrawal-input-label">Wallet Address</label>
                            <input type="text" wire:model="wallet_address" placeholder="Enter wallet address"
                                class="withdrawal-input">
                        </div>
                        <div class="withdrawal-input-group">
                            <label class="withdrawal-input-label">Network</label>
                            <select wire:model="network" class="withdrawal-select">
                                <option value="">Select Network</option>
                                <option value="TRC20">TRC20</option>
                                <option value="ERC20">ERC20</option>
                                <option value="BEP20">BEP20</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            <!-- PayPal Details -->
            @if ($payment_method === 'paypal')
                <div class="withdrawal-payment-details">
                    <h4 class="withdrawal-details-title">
                        <i class="fab fa-paypal"></i> PayPal Details
                    </h4>
                    <div class="withdrawal-details-fields">
                        <div class="withdrawal-input-group">
                            <label class="withdrawal-input-label">PayPal Email</label>
                            <input type="email" wire:model="paypal_email" placeholder="Enter PayPal email"
                                class="withdrawal-input">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Submit Button -->
            <div class="withdrawal-submit-section">
                <button type="submit" wire:loading.attr="disabled" class="withdrawal-submit-btn">
                    <span wire:loading.remove>
                        <i class="fas fa-paper-plane"></i> Submit Withdrawal Request
                    </span>
                    <span wire:loading>
                        <i class="fas fa-spinner fa-spin"></i> Processing...
                    </span>
                </button>
            </div>

            <!-- Footer Note -->
            <div class="withdrawal-footer">
                <p class="withdrawal-note">
                    <i class="fas fa-info-circle"></i>
                    Your withdrawal request will be reviewed by admin and processed within 24-48 hours.
                </p>
            </div>
        </form>
    </div>

    <style>
        /* Withdrawal Modal Styles */
        .withdrawal-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
        }

        .withdrawal-modal-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .withdrawal-modal-close {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .withdrawal-modal-close:hover {
            background: var(--hover);
            color: var(--text-primary);
        }

        .withdrawal-modal-content {
            padding: 24px;
            overflow-y: auto;
            max-height: calc(90vh - 80px);
        }

        .withdrawal-form-container {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .withdrawal-balance-info {
            padding: 16px;
            background: var(--secondary);
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .balance-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .balance-label {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .balance-value {
            font-size: 20px;
            font-weight: 600;
            color: var(--accent);
        }

        .withdrawal-input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .withdrawal-input-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .withdrawal-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .withdrawal-currency {
            position: absolute;
            left: 16px;
            font-size: 18px;
            font-weight: 600;
            color: var(--text-secondary);
            z-index: 1;
        }

        .withdrawal-input,
        .withdrawal-select {
            width: 100%;
            padding: 14px 16px 14px 36px;
            background: var(--secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .withdrawal-select {
            padding: 14px 16px;
        }

        .withdrawal-input:focus,
        .withdrawal-select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(255, 177, 26, 0.1);
        }

        .withdrawal-payment-details {
            padding: 16px;
            background: var(--secondary);
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .withdrawal-details-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .withdrawal-details-fields {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .withdrawal-details-fields .withdrawal-input-group {
            gap: 8px;
        }

        .withdrawal-details-fields .withdrawal-input-label {
            font-size: 13px;
        }

        .withdrawal-details-fields .withdrawal-input,
        .withdrawal-details-fields .withdrawal-select {
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 500;
        }

        .withdrawal-details-fields .withdrawal-input-wrapper .withdrawal-input {
            padding-left: 16px;
        }

        .withdrawal-submit-section {
            margin-top: 8px;
        }

        .withdrawal-submit-btn {
            width: 100%;
            padding: 14px 24px;
            background: var(--accent);
            color: #000;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .withdrawal-submit-btn:hover:not(:disabled) {
            background: #ffa000;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 177, 26, 0.3);
        }

        .withdrawal-submit-btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .withdrawal-submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .withdrawal-footer {
            text-align: center;
        }

        .withdrawal-note {
            font-size: 12px;
            color: var(--text-secondary);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .withdrawal-note i {
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .withdrawal-modal-popup {
                width: 95%;
                max-width: none;
                border-radius: 20px 20px 0 0;
                top: auto;
                bottom: 0;
                transform: translate(-50%, 100%);
                max-height: 85vh;
            }

            .withdrawal-modal-popup.active {
                transform: translate(-50%, 0);
            }

            .withdrawal-modal-header {
                padding: 16px 20px;
            }

            .withdrawal-modal-content {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .withdrawal-modal-popup {
                width: 100%;
                border-radius: 20px 20px 0 0;
            }

            .withdrawal-modal-header h3 {
                font-size: 1.1rem;
            }

            .withdrawal-input {
                font-size: 16px;
            }
        }
    </style>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('withdrawal-submitted', (data) => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || 'Withdrawal request submitted successfully.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ffb11a',
                        background: 'var(--card-bg)',
                        color: 'var(--text-primary)'
                    }).then(() => {
                        closeWithdrawalModal();
                        window.location.reload();
                    });
                } else {
                    alert(data.message || 'Withdrawal request submitted successfully.');
                    closeWithdrawalModal();
                    window.location.reload();
                }
            });
        });
    </script>
</div>

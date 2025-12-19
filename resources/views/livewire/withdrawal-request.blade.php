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
    public $confirm_submit = false;

    public function mount()
    {
        $user = Auth::user();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0, 'status' => 'active', 'currency' => 'USDT']);
        $this->wallet_balance = $wallet->balance;
        $this->currency = $wallet->currency;
    }

    public function submit()
    {
        if (!$this->confirm_submit) {
            $this->addError('confirm_submit', 'Please confirm to submit withdrawal request.');
            return;
        }

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
            $this->reset(['amount', 'payment_method', 'bank_name', 'account_number', 'account_holder', 'swift_code', 'crypto_type', 'wallet_address', 'network', 'paypal_email', 'confirm_submit']);
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
        <div class="withdrawal-header-left">
            <h3>Request Withdrawal</h3>
            <button type="button" class="withdrawal-modal-close" onclick="closeWithdrawalModal()" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div class="withdrawal-modal-content">
        <form wire:submit="submit" class="withdrawal-form-container">
            <!-- Balance Info -->
            <div class="withdrawal-balance-info">
                <div class="balance-item">
                    <span class="balance-label">Available Balance</span>
                    <span class="balance-value">${{ number_format($wallet_balance, 2) }} {{ $currency }}</span>
                </div>
                <div class="balance-item balance-item-min">
                    <span class="balance-label">Minimum Withdrawal</span>
                    <span class="balance-min-value">{{ $min_withdrawal }} {{ $currency }}</span>
                </div>
            </div>

            <!-- Amount Input -->
            <div class="withdrawal-input-group">
                <label class="withdrawal-input-label">Amount <span style="color: #ef4444;">*</span></label>
                <div class="withdrawal-input-wrapper">
                    <span class="withdrawal-currency">$</span>
                    <input type="number" wire:model="amount" step="0.01" min="{{ $min_withdrawal }}"
                        max="{{ $wallet_balance }}" placeholder="0.00" class="withdrawal-input"
                        style="font-size: 16px;">
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

            <!-- Submit Confirmation -->
            <div class="withdrawal-submit-section">
                <div class="withdrawal-submit-row">
                    <label class="withdrawal-checkbox-label">
                        <input type="checkbox" wire:model="confirm_submit" class="withdrawal-checkbox">
                        <span class="withdrawal-checkbox-text">Submit Withdrawal Request</span>
                    </label>
                    <button type="submit" wire:loading.attr="disabled" class="withdrawal-submit-btn"
                        @if (!$confirm_submit) disabled @endif>
                        <span wire:loading.remove>
                            Submit Withdrawal Request
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin"></i> Processing...
                        </span>
                    </button>
                </div>
                @error('confirm_submit')
                    <p style="color: #ef4444; font-size: 0.85rem; margin-top: 0.5rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Footer Note -->
            <div class="withdrawal-footer">
                <p class="withdrawal-note">
                    <span class="withdrawal-note-icon">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    Your withdrawal request will be reviewed by admin and processed within 24-48 hours.
                </p>
            </div>
        </form>
    </div>
</div>

@push('styles')
    <style>
        /* Withdrawal Modal Styles - Matching Image Design */
        .withdrawal-modal-header {
            padding: 0;
            border-bottom: none;
            background: transparent;
        }

        .withdrawal-header-left {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 24px 24px 20px 24px;
        }

        .withdrawal-modal-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .withdrawal-modal-close {
            width: 28px;
            height: 28px;
            border-radius: 4px;
            border: none;
            background: #000000;
            color: #ffffff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .withdrawal-modal-close:hover {
            background: #1a1a1a;
            color: #ffffff;
        }

        .withdrawal-modal-content {
            padding: 0 24px 24px 24px;
            overflow-y: auto;
            max-height: calc(90vh - 100px);
        }

        .withdrawal-form-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .withdrawal-balance-info {
            padding: 0;
            background: transparent;
            border-radius: 0;
            border: none;
            backdrop-filter: none;
        }

        .balance-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .balance-item-min {
            margin-top: 0;
            padding-top: 0;
            border-top: none;
        }

        .balance-label {
            font-size: 14px;
            color: #ffffff;
            font-weight: 500;
        }

        .balance-value {
            font-size: 24px;
            font-weight: 700;
            color: #ffb11a;
            letter-spacing: -0.3px;
        }

        .balance-min-value {
            font-size: 14px;
            color: #ffffff;
            font-weight: 500;
        }

        .withdrawal-input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .withdrawal-input-label {
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 2px;
        }

        .withdrawal-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .withdrawal-currency {
            position: absolute;
            left: 14px;
            font-size: 16px;
            font-weight: 600;
            color: #ffffff;
            z-index: 1;
        }

        .withdrawal-input,
        .withdrawal-select {
            width: 100%;
            padding: 14px 16px 14px 40px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #ffffff;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .withdrawal-input {
            padding-left: 36px;
        }

        .withdrawal-select {
            padding: 14px 40px 14px 16px;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23ffb11a' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-color: rgba(255, 255, 255, 0.05);
        }

        .withdrawal-select option {
            color: #ffffff;
            background: #2a2a2a;
        }

        .withdrawal-select option[value=""] {
            color: rgba(255, 255, 255, 0.5);
        }

        .withdrawal-select:invalid {
            color: rgba(255, 255, 255, 0.5);
        }

        .withdrawal-select:valid {
            color: #ffffff;
        }

        .withdrawal-input:focus,
        .withdrawal-select:focus {
            outline: none;
            border-color: #ffb11a;
            box-shadow: 0 0 0 3px rgba(255, 177, 26, 0.1);
            background: rgba(255, 255, 255, 0.08);
        }

        .withdrawal-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
            opacity: 1;
        }

        .withdrawal-payment-details {
            padding: 20px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .withdrawal-details-title {
            font-size: 1rem;
            font-weight: 600;
            color: #ffffff;
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
            margin-top: 4px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .withdrawal-submit-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .withdrawal-checkbox-label {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            user-select: none;
            flex: 1;
        }

        .withdrawal-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #ffb11a;
            flex-shrink: 0;
        }

        .withdrawal-checkbox-text {
            font-size: 14px;
            font-weight: 500;
            color: #ffffff;
        }

        .withdrawal-submit-btn {
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: not-allowed;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .withdrawal-submit-btn:hover:not(:disabled) {
            background: linear-gradient(135deg, #ffb11a 0%, #ff9500 100%);
            color: #ffffff;
            border-color: #ffb11a;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 177, 26, 0.4);
        }

        .withdrawal-submit-btn:active:not(:disabled) {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(255, 177, 26, 0.3);
        }

        .withdrawal-submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .withdrawal-submit-btn:not(:disabled) {
            background: linear-gradient(135deg, #ffb11a 0%, #ff9500 100%);
            color: #ffffff;
            border-color: #ffb11a;
            cursor: pointer;
            opacity: 1;
        }

        .withdrawal-footer {
            text-align: left;
            margin-top: 16px;
        }

        .withdrawal-note {
            font-size: 13px;
            color: #ffffff;
            margin: 0;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 0;
            background: transparent;
            border: none;
        }

        .withdrawal-note-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #000000;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 12px;
            margin-top: 2px;
        }

        .withdrawal-note-icon i {
            color: #ffffff;
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
@endpush

@push('scripts')
    <script>
        // Update select styling when value changes
        function updateSelectStyle() {
            const select = document.querySelector('.withdrawal-select');
            if (select) {
                if (select.value) {
                    select.style.color = 'var(--text-primary)';
                } else {
                    select.style.color = 'var(--text-secondary)';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const select = document.querySelector('.withdrawal-select');
            if (select) {
                select.addEventListener('change', updateSelectStyle);
                updateSelectStyle(); // Check initial value
            }
        });

        document.addEventListener('livewire:init', () => {
            // Update select style after Livewire updates
            Livewire.hook('morph.updated', () => {
                setTimeout(updateSelectStyle, 50);
            });
            Livewire.on('withdrawal-submitted', (data) => {
                // Close modal first
                if (typeof closeWithdrawalModal === 'function') {
                    closeWithdrawalModal();
                }

                // Show success message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        html: `<div style="text-align: left;">
                            <p style="margin-bottom: 10px;">${data.message || 'Withdrawal request submitted successfully.'}</p>
                            ${data.balance ? `<p style="margin-top: 10px; font-size: 0.9rem; color: var(--text-secondary);"><strong>New Balance:</strong> $${data.balance}</p>` : ''}
                        </div>`,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ffb11a',
                        background: 'var(--card-bg)',
                        color: 'var(--text-primary)',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        // Reload page to show updated withdrawal list
                        window.location.reload();
                    });
                } else if (typeof toastr !== 'undefined') {
                    toastr.success(data.message || 'Withdrawal request submitted successfully.');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Withdrawal Request Submitted',
                        text: data.message || 'Withdrawal request submitted successfully.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ffb11a'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    alert(data.message || 'Withdrawal request submitted successfully.');
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            });
        });

        // Also listen for Livewire 3 events
        document.addEventListener('livewire:initialized', () => {
            // Update select style after Livewire updates
            Livewire.hook('morph.updated', () => {
                setTimeout(updateSelectStyle, 50);
            });

            Livewire.on('withdrawal-submitted', (data) => {
                // Close modal first
                if (typeof closeWithdrawalModal === 'function') {
                    closeWithdrawalModal();
                }

                // Show success message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        html: `<div style="text-align: left;">
                            <p style="margin-bottom: 10px;">${data.message || 'Withdrawal request submitted successfully.'}</p>
                            ${data.balance ? `<p style="margin-top: 10px; font-size: 0.9rem; color: var(--text-secondary);"><strong>New Balance:</strong> $${data.balance}</p>` : ''}
                        </div>`,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ffb11a',
                        background: 'var(--card-bg)',
                        color: 'var(--text-primary)',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        // Reload page to show updated withdrawal list
                        window.location.reload();
                    });
                } else if (typeof toastr !== 'undefined') {
                    toastr.success(data.message || 'Withdrawal request submitted successfully.');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Withdrawal Request Submitted',
                        text: data.message || 'Withdrawal request submitted successfully.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ffb11a'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    alert(data.message || 'Withdrawal request submitted successfully.');
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            });
        });
    </script>
@endpush

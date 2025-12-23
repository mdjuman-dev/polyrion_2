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

      if ($wallet->balance < $this->amount) {
         $this->addError('amount', 'Insufficient balance. Your current balance is ' . number_format($wallet->balance, 2) . ' ' . $this->currency);
         return;
      }

      if ($this->amount < $this->min_withdrawal) {
         $this->addError('amount', 'Minimum withdrawal amount is ' . $this->min_withdrawal . ' ' . $this->currency);
         return;
      }

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

         $balanceBefore = (float) $wallet->balance;
         $wallet->balance -= $this->amount;
         $wallet->save();

         $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => 'pending',
            'payment_method' => $this->payment_method,
            'payment_details' => $payment_details,
         ]);

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

<div class="withdrawal-modal-wrapper">
   <!-- Header -->
   <div class="withdrawal-header">
      <h3 class="withdrawal-title">Request Withdrawal</h3>
      <button type="button" class="withdrawal-close-btn" onclick="closeWithdrawalModal()">
         <i class="fas fa-times"></i>
      </button>
   </div>

   <!-- Content -->
   <div class="withdrawal-content">
      <form wire:submit="submit" class="withdrawal-form">
         <!-- Balance Section -->
         <div class="balance-section">
            <div class="balance-row">
               <span class="balance-label">Available Balance</span>
               <span class="balance-amount">${{ number_format($wallet_balance, 2) }} {{ $currency }}</span>
            </div>
            <div class="balance-row">
               <span class="balance-label">Minimum Withdrawal</span>
               <span class="balance-min">{{ $min_withdrawal }} {{ $currency }}</span>
            </div>
         </div>

         <!-- Amount Input -->
         <div class="form-group">
            <label class="form-label">
               $ Amount <span class="required">*</span>
            </label>
            <div class="input-group">
               <span class="input-prefix">$</span>
               <input type="number" wire:model="amount" step="0.01" min="{{ $min_withdrawal }}"
                  max="{{ $wallet_balance }}" placeholder="0.00" class="form-input">
            </div>
            @error('amount')
            <span class="error-text">{{ $message }}</span>
            @enderror
         </div>

         <!-- Payment Method -->
         <div class="form-group">
            <label class="form-label">
               Payment Method <span class="required">*</span>
            </label>
            <div class="payment-options">
               <label class="payment-option {{ $payment_method === 'bank' ? 'active' : '' }}"
                  wire:click="$set('payment_method', 'bank')">
                  <input type="radio" wire:model="payment_method" value="bank" style="display: none;">
                  <i class="fas fa-university"></i>
                  <span>Bank Transfer</span>
               </label>
               <label class="payment-option {{ $payment_method === 'crypto' ? 'active' : '' }}"
                  wire:click="$set('payment_method', 'crypto')">
                  <input type="radio" wire:model="payment_method" value="crypto" style="display: none;">
                  <i class="fab fa-bitcoin"></i>
                  <span>Cryptocurrency</span>
               </label>
               <label class="payment-option {{ $payment_method === 'paypal' ? 'active' : '' }}"
                  wire:click="$set('payment_method', 'paypal')">
                  <input type="radio" wire:model="payment_method" value="paypal" style="display: none;">
                  <i class="fab fa-paypal"></i>
                  <span>PayPal</span>
               </label>
            </div>
            @error('payment_method')
            <span class="error-text">{{ $message }}</span>
            @enderror
         </div>

         <!-- Bank Details -->
         @if ($payment_method === 'bank')
         <div class="payment-details">
            <h4 class="details-title">
               <i class="fas fa-university"></i> Bank Account Details
            </h4>
            <div class="details-fields">
               <div class="form-group">
                  <label class="form-label">
                     <i class="fas fa-university"></i> Bank Name
                  </label>
                  <input type="text" wire:model="bank_name" placeholder="Enter bank name"
                     class="form-input">
               </div>
               <div class="form-group">
                  <label class="form-label">
                     <i class="fas fa-hashtag"></i> Account Number
                  </label>
                  <input type="text" wire:model="account_number" placeholder="Enter account number"
                     class="form-input">
               </div>
               <div class="form-group">
                  <label class="form-label">
                     <i class="fas fa-user"></i> Account Holder Name
                  </label>
                  <input type="text" wire:model="account_holder" placeholder="Enter account holder name"
                     class="form-input">
               </div>
               <div class="form-group">
                  <label class="form-label">
                     <i class="fas fa-code"></i> SWIFT/IBAN Code
                  </label>
                  <input type="text" wire:model="swift_code" placeholder="Enter SWIFT or IBAN code"
                     class="form-input">
               </div>
            </div>
         </div>
         @endif

         <!-- Crypto Details -->
         @if ($payment_method === 'crypto')
         <div class="payment-details">
            <h4 class="details-title">
               <i class="fab fa-bitcoin"></i> Cryptocurrency Details
            </h4>
            <div class="details-fields">
               <div class="form-group">
                  <label class="form-label">
                     <i class="fab fa-bitcoin"></i> Cryptocurrency Type
                  </label>
                  <select wire:model="crypto_type" class="form-input form-select">
                     <option value="USDT">USDT</option>
                     <option value="BTC">Bitcoin (BTC)</option>
                     <option value="ETH">Ethereum (ETH)</option>
                     <option value="BNB">Binance Coin (BNB)</option>
                  </select>
               </div>
               <div class="form-group">
                  <label class="form-label">
                     <i class="fas fa-wallet"></i> Wallet Address
                  </label>
                  <input type="text" wire:model="wallet_address" placeholder="Enter wallet address"
                     class="form-input">
               </div>
               <div class="form-group">
                  <label class="form-label">
                     <i class="fas fa-network-wired"></i> Network
                  </label>
                  <select wire:model="network" class="form-input form-select">
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
         <div class="payment-details">
            <h4 class="details-title">
               <i class="fab fa-paypal"></i> PayPal Details
            </h4>
            <div class="details-fields">
               <div class="form-group">
                  <label class="form-label">
                     <i class="fab fa-paypal"></i> PayPal Email
                  </label>
                  <input type="email" wire:model="paypal_email" placeholder="Enter PayPal email"
                     class="form-input">
               </div>
            </div>
         </div>
         @endif

         <!-- Submit Section -->
         <div class="submit-section">
            <div class="submit-row">
               <label class="checkbox-label">
                  <input type="checkbox" wire:model="confirm_submit" class="checkbox-input">
                  <span class="checkbox-text">Submit Withdrawal Request</span>
               </label>
               <button type="submit" wire:loading.attr="disabled" class="submit-btn"
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
            <span class="error-text">{{ $message }}</span>
            @enderror
         </div>

         <!-- Footer Note -->
         <div class="footer-note">
            <p class="note-text">
               <span class="note-icon">i</span>
               Your withdrawal request will be reviewed by admin and processed within 24-48 hours.
            </p>
         </div>
      </form>
   </div>
</div>

@push('style')
<style>
   .withdrawal-modal-wrapper {
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
   }

   /* Header */
   .withdrawal-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 20px 24px;
      border-bottom: 1px solid rgba(255, 177, 26, 0.2);
      background: rgba(0, 0, 0, 0.2);
      flex-shrink: 0;
   }

   .withdrawal-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #ffffff;
      margin: 0;
      letter-spacing: -0.5px;
   }

   .withdrawal-close-btn {
      width: 32px;
      height: 32px;
      border-radius: 4px;
      border: none;
      background: #000000;
      color: #ffffff;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
      font-size: 16px;
      font-weight: 600;
   }

   .withdrawal-close-btn:hover {
      background: #1a1a1a;
      transform: scale(1.05);
   }

   /* Content */
   .withdrawal-content {
      padding: 24px;
      overflow-y: auto;
      flex: 1;
      min-height: 0;
   }

   .withdrawal-form {
      display: flex;
      flex-direction: column;
      gap: 20px;
   }

   /* Balance Section */
   .balance-section {
      display: flex;
      flex-direction: column;
      gap: 12px;
      padding: 0;
      background: transparent;
      border: none;
      margin-bottom: 20px;
   }

   .balance-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
   }

   .balance-label {
      font-size: 14px;
      color: #ffffff;
      font-weight: 500;
   }

   .balance-amount {
      font-size: 18px;
      font-weight: 700;
      color: #ffb11a;
      letter-spacing: -0.3px;
   }

   .balance-min {
      font-size: 14px;
      color: #ffffff;
      font-weight: 500;
   }

   /* Form Groups */
   .form-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
   }

   .form-label {
      font-size: 14px;
      font-weight: 600;
      color: #ffffff;
      display: flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 8px;
   }

   .form-label i {
      font-size: 13px;
      color: #ffb11a;
   }

   .required {
      color: #ef4444;
   }

   .input-group {
      position: relative;
      display: flex;
      align-items: center;
   }

   .input-prefix {
      position: absolute;
      left: 14px;
      font-size: 16px;
      font-weight: 600;
      color: #ffffff;
      z-index: 1;
   }

   .form-input {
      width: 100%;
      padding: 12px 16px 12px 36px;
      background: rgba(0, 0, 0, 0.4);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 8px;
      color: #ffffff;
      font-size: 16px;
      font-weight: 500;
      transition: all 0.3s ease;
   }

   .form-input:focus {
      outline: none;
      border-color: #ffb11a;
      box-shadow: 0 0 0 2px rgba(255, 177, 26, 0.2);
      background: rgba(0, 0, 0, 0.5);
   }

   .form-input::placeholder {
      color: rgba(255, 255, 255, 0.5);
   }

   .form-input:hover:not(:focus) {
      border-color: rgba(255, 255, 255, 0.3);
      background: rgba(0, 0, 0, 0.45);
   }

   .form-select {
      padding: 12px 40px 12px 16px;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23ffb11a' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 16px center;
      cursor: pointer;
      padding-left: 16px;
   }

   .form-select option {
      background: #2a2a2a;
      color: #ffffff;
   }

   .error-text {
      color: #ef4444;
      font-size: 0.85rem;
      margin-top: 4px;
   }

   /* Payment Options */
   .payment-options {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin-top: 8px;
   }

   .payment-option {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 16px 12px;
      background: rgba(0, 0, 0, 0.4);
      border: 2px solid rgba(255, 255, 255, 0.2);
      border-radius: 10px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-align: center;
      min-height: 100px;
      position: relative;
   }

   .payment-option:hover {
      background: rgba(0, 0, 0, 0.5);
      border-color: rgba(255, 177, 26, 0.4);
      transform: translateY(-2px);
   }

   .payment-option.active {
      background: rgba(255, 177, 26, 0.12);
      border-color: #ffb11a;
      box-shadow: 0 0 0 1px rgba(255, 177, 26, 0.3);
   }

   .payment-option i {
      font-size: 24px;
      color: #ffffff;
   }

   .payment-option.active i {
      color: #ffb11a;
   }

   .payment-option span {
      font-size: 13px;
      font-weight: 500;
      color: #ffffff;
   }

   /* Payment Details */
   .payment-details {
      padding: 20px;
      background: rgba(0, 0, 0, 0.3);
      border-radius: 10px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      animation: slideDown 0.3s ease;
      margin-top: 12px;
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

   .details-title {
      font-size: 1rem;
      font-weight: 600;
      color: #ffffff;
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 8px;
   }

   .details-title i {
      color: #ffb11a;
      font-size: 18px;
   }

   .details-fields {
      display: flex;
      flex-direction: column;
      gap: 16px;
   }

   .details-fields .form-input {
      padding-left: 16px;
   }

   /* Submit Section */
   .submit-section {
      margin-top: 4px;
   }

   .submit-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
   }

   .checkbox-label {
      display: flex;
      align-items: center;
      gap: 12px;
      cursor: pointer;
      user-select: none;
      flex: 1;
   }

   .checkbox-input {
      width: 20px;
      height: 20px;
      cursor: pointer;
      accent-color: #ffb11a;
   }

   .checkbox-text {
      font-size: 14px;
      font-weight: 500;
      color: #ffffff;
   }

   .submit-btn {
      padding: 12px 24px;
      background: rgba(255, 255, 255, 0.1);
      color: rgba(255, 255, 255, 0.5);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: not-allowed;
      transition: all 0.3s ease;
      white-space: nowrap;
   }

   .submit-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      background: rgba(255, 255, 255, 0.08);
      color: rgba(255, 255, 255, 0.4);
   }

   .submit-btn:not(:disabled) {
      background: linear-gradient(135deg, #ffb11a 0%, #ff9500 100%);
      color: #000000;
      border-color: #ffb11a;
      cursor: pointer;
      opacity: 1;
      font-weight: 700;
   }

   .submit-btn:not(:disabled):hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(255, 177, 26, 0.4);
      background: linear-gradient(135deg, #ff9500 0%, #ffb11a 100%);
   }

   /* Footer Note */
   .footer-note {
      margin-top: 20px;
      padding-top: 16px;
   }

   .note-text {
      font-size: 13px;
      color: #ffffff;
      margin: 0;
      display: flex;
      align-items: flex-start;
      gap: 10px;
   }

   .note-icon {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 700;
      flex-shrink: 0;
      margin-top: 2px;
      border: 1px solid rgba(255, 255, 255, 0.2);
   }

   /* Responsive */
   @media (max-width: 768px) {
      .withdrawal-header {
         padding: 16px 20px;
      }

      .withdrawal-title {
         font-size: 1.3rem;
      }

      .withdrawal-content {
         padding: 20px;
      }

      .payment-options {
         grid-template-columns: 1fr;
         gap: 10px;
      }

      .payment-option {
         min-height: 90px;
         padding: 14px 10px;
      }

      .balance-amount {
         font-size: 24px;
      }

      .submit-row {
         flex-direction: column;
         align-items: stretch;
      }

      .submit-btn {
         width: 100%;
      }
   }

   @media (max-width: 480px) {
      .withdrawal-title {
         font-size: 1.1rem;
      }

      .withdrawal-header {
         padding: 14px 16px;
      }

      .withdrawal-content {
         padding: 16px;
      }

      .balance-section {
         padding: 12px;
      }

      .balance-amount {
         font-size: 22px;
      }
   }
</style>
@endpush

@push('scripts')
<script>
   document.addEventListener('livewire:init', () => {
      Livewire.on('withdrawal-submitted', (data) => {
         if (typeof closeWithdrawalModal === 'function') {
            closeWithdrawalModal();
         }

         if (typeof Swal !== 'undefined') {
            Swal.fire({
               icon: 'success',
               title: 'Success!',
               html: `<div style="text-align: left;">
                        <p style="margin-bottom: 10px;">${data.message || 'Withdrawal request submitted successfully.'}</p>
                        ${data.balance ? `<p style="margin-top: 10px; font-size: 0.9rem; color: rgba(255,255,255,0.7);"><strong>New Balance:</strong> $${data.balance}</p>` : ''}
                    </div>`,
               confirmButtonText: 'OK',
               confirmButtonColor: '#ffb11a',
               background: '#2a2a2a',
               color: '#ffffff'
            }).then(() => {
               window.location.reload();
            });
         } else {
            alert(data.message || 'Withdrawal request submitted successfully.');
            setTimeout(() => window.location.reload(), 500);
         }
      });
   });

   document.addEventListener('livewire:initialized', () => {
      Livewire.on('withdrawal-submitted', (data) => {
         if (typeof closeWithdrawalModal === 'function') {
            closeWithdrawalModal();
         }

         if (typeof Swal !== 'undefined') {
            Swal.fire({
               icon: 'success',
               title: 'Success!',
               html: `<div style="text-align: left;">
                        <p style="margin-bottom: 10px;">${data.message || 'Withdrawal request submitted successfully.'}</p>
                        ${data.balance ? `<p style="margin-top: 10px; font-size: 0.9rem; color: rgba(255,255,255,0.7);"><strong>New Balance:</strong> $${data.balance}</p>` : ''}
                    </div>`,
               confirmButtonText: 'OK',
               confirmButtonColor: '#ffb11a',
               background: '#2a2a2a',
               color: '#ffffff'
            }).then(() => {
               window.location.reload();
            });
         } else {
            alert(data.message || 'Withdrawal request submitted successfully.');
            setTimeout(() => window.location.reload(), 500);
         }
      });
   });
</script>
@endpush
<?php

use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\UserWallet;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;

new class extends Component {
   #[Validate('required|numeric|min:1')]
   public $amount = '';

   #[Validate('required|string|in:crypto')]
   public $payment_method = 'crypto';

   // Crypto details
   public $crypto_type = 'USDT';

   public $wallet_balance = 0;
   public $currency = 'USDT';
   public $min_withdrawal = 10;
   public $confirm_submit = false;
   public $withdrawal_password = '';
   public $has_withdrawal_password = false;
   public $user_wallets = [];
   public $selected_wallet_id = '';

   public function mount($has_withdrawal_password = null)
   {
      $user = Auth::user();
      $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0, 'status' => 'active', 'currency' => 'USDT']);
      $this->wallet_balance = $wallet->balance;
      $this->currency = $wallet->currency;
      $this->min_withdrawal = 10;
      $this->has_withdrawal_password = $has_withdrawal_password !== null ? $has_withdrawal_password : !empty($user->withdrawal_password);
      $this->refreshWallets();
   }

   public function setQuickAmount($amount)
   {
      $this->amount = $amount;
   }

   public function refreshWallets()
   {
      $user = Auth::user();
      $this->user_wallets = $user->userWallets()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get()->toArray();
      if (count($this->user_wallets) > 0 && !$this->selected_wallet_id) {
         $this->selected_wallet_id = $this->user_wallets[0]['id'];
      }
   }

   #[On('refresh-withdrawal-wallets')]
   public function handleWalletRefresh()
   {
      $this->refreshWallets();
   }

   public function submit()
   {
      if (!$this->confirm_submit) {
         $this->addError('confirm_submit', 'Please confirm to submit withdrawal request.');
         return;
      }

      $this->validate([
         'amount' => 'required|numeric|min:1',
         'withdrawal_password' => 'required|string',
         'selected_wallet_id' => 'required|integer|exists:user_wallets,id',
      ], [], [
         'selected_wallet_id' => 'wallet',
      ]);

      $user = Auth::user();
      
      // Check withdrawal password
      if (!$user->withdrawal_password) {
         $this->addError('withdrawal_password', 'Withdrawal password not set. Please set your withdrawal password first.');
         return;
      }

      if (!\Illuminate\Support\Facades\Hash::check($this->withdrawal_password, $user->withdrawal_password)) {
         $this->addError('withdrawal_password', 'Incorrect withdrawal password.');
         return;
      }

      $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

      if ($wallet->balance < $this->amount) {
         $this->addError('amount', 'Insufficient balance. Your current balance is ' . number_format($wallet->balance, 2) . ' ' . $this->currency);
         return;
      }

      if ($this->amount < $this->min_withdrawal) {
         $this->addError('amount', 'Minimum withdrawal amount is ' . $this->min_withdrawal . ' ' . $this->currency);
         return;
      }

      $selectedWallet = UserWallet::where('id', $this->selected_wallet_id)
         ->where('user_id', $user->id)
         ->first();

      if (!$selectedWallet) {
         $this->addError('selected_wallet_id', 'Selected wallet not found.');
         return;
      }

      $payment_details = [
         'crypto_type' => $this->crypto_type,
         'wallet_address' => $selectedWallet->wallet_address,
         'network' => $selectedWallet->network,
         'wallet_type' => $selectedWallet->wallet_type,
         'wallet_name' => $selectedWallet->wallet_name,
         'memo_tag' => $selectedWallet->memo_tag,
         'payment_method' => 'crypto',
      ];

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
            'payment_method' => 'crypto',
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

         $this->reset(['amount', 'crypto_type', 'confirm_submit', 'withdrawal_password']);
         $this->wallet_balance = $wallet->balance;
         $this->refreshWallets();

         $this->dispatch('withdrawal-submitted', 
            message: 'Withdrawal request submitted successfully. It will be reviewed by admin.',
            balance: number_format($wallet->balance, 2)
         );
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
   <div class="deposit-modal-header">
      <h3>Request Withdrawal</h3>
      <button type="button" class="deposit-modal-close" onclick="closeWithdrawalModal()" aria-label="Close">
         <i class="fas fa-times"></i>
      </button>
   </div>

   <div class="deposit-modal-content">
      <form wire:submit="submit" class="deposit-form-container">
         <!-- Balance Section -->
         <div class="deposit-balance-info">
            <div class="balance-item">
               <h3 style="font-size: 20px; font-weight: 600; color: var(--text-primary);">Available Balance</h3>
               <span class="balance-value">${{ number_format($wallet_balance, 2) }} {{ $currency }}</span>
            </div>
         </div>

         <!-- Amount Input -->
         <div class="deposit-input-group">
            <label class="deposit-input-label">Amount <span style="color: #ef4444;">*</span></label>
            <div class="deposit-input-wrapper">
               <span class="deposit-currency">$</span>
               <input type="number" wire:model="amount" step="0.01" min="{{ $min_withdrawal }}"
                  max="{{ $wallet_balance }}" placeholder="Minimum Withdrawal is 10.00" class="deposit-input">
            </div>
            @error('amount')
            <p style="color: #ef4444; font-size: 0.85rem; margin-top: 4px;">{{ $message }}</p>
            @enderror
         </div>

         <div class="deposit-quick-amounts">
            @foreach([10, 50, 100, 500] as $qAmount)
               <button type="button" class="quick-amount-btn {{ $amount == $qAmount ? 'active' : '' }}"
                  wire:click="setQuickAmount({{ $qAmount }})">${{ $qAmount }}</button>
            @endforeach
         </div>

         <!-- Wallet Selection -->
         <div class="deposit-input-group">
            <label class="deposit-input-label">Select Wallet <span style="color: #ef4444;">*</span></label>
            @if(empty($user_wallets))
               <div style="padding: 12px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px; margin-bottom: 12px;">
                  <p style="font-size: 0.85rem; color: #fca5a5; margin: 0; display: flex; align-items: center; gap: 6px;">
                     <i class="fas fa-exclamation-triangle"></i>
                     No wallet addresses configured. Please add a wallet first.
                  </p>
               </div>
            @else
               <div class="deposit-input-wrapper">
                  <select wire:model="selected_wallet_id" class="deposit-input" style="padding-left: 16px; appearance: none; background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'12\' viewBox=\'0 0 12 12\'%3E%3Cpath fill=\'%23ffb11a\' d=\'M6 9L1 4h10z\'/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 16px center; cursor: pointer;">
                     <option value="">Select a wallet</option>
                     @foreach($user_wallets as $wallet)
                     <option value="{{ $wallet['id'] }}">
                        {{ $wallet['wallet_name'] ?: ucfirst($wallet['wallet_type']) }} - {{ substr($wallet['wallet_address'], 0, 8) }}...{{ substr($wallet['wallet_address'], -6) }} ({{ $wallet['network'] }})
                     </option>
                     @endforeach
                  </select>
               </div>
               @error('selected_wallet_id')
               <p style="color: #ef4444; font-size: 0.85rem; margin-top: 4px;">{{ $message }}</p>
               @enderror
            @endif
            <button type="button" onclick="openAddWalletModal()" 
               style="margin-top: 12px; padding: 10px 16px; background: var(--secondary); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;"
               onmouseover="this.style.background='var(--hover)'; this.style.borderColor='var(--accent)'"
               onmouseout="this.style.background='var(--secondary)'; this.style.borderColor='var(--border)'">
               <i class="fas fa-plus"></i> Add New Wallet
            </button>
         </div>

         <!-- Cryptocurrency Type -->
         <div class="deposit-input-group">
            <label class="deposit-input-label">Cryptocurrency Type</label>
            <div class="deposit-input-wrapper">
               <select wire:model="crypto_type" class="deposit-input" style="padding-left: 16px; appearance: none; background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'12\' viewBox=\'0 0 12 12\'%3E%3Cpath fill=\'%23ffb11a\' d=\'M6 9L1 4h10z\'/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 16px center; cursor: pointer;">
                  <option value="USDT">USDT</option>
                  <option value="BTC">Bitcoin (BTC)</option>
                  <option value="ETH">Ethereum (ETH)</option>
                  <option value="BNB">Binance Coin (BNB)</option>
               </select>
            </div>
         </div>

         <!-- Withdrawal Password -->
         <div class="deposit-input-group">
            <label class="deposit-input-label">Withdrawal Password <span style="color: #ef4444;">*</span></label>
            <div class="deposit-input-wrapper">
               <input type="password" wire:model="withdrawal_password" placeholder="Enter your withdrawal password"
                  class="deposit-input" style="padding-left: 16px;">
            </div>
            @error('withdrawal_password')
            <p style="color: #ef4444; font-size: 0.85rem; margin-top: 4px;">{{ $message }}</p>
            @enderror
         </div>

         <!-- Submit Section -->
         <div class="deposit-submit-section">
            <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; cursor: pointer; user-select: none;">
               <input type="checkbox" wire:model.live="confirm_submit" style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--accent);">
               <span style="font-size: 14px; color: var(--text-primary);">Confirm withdrawal request</span>
            </label>
            @error('confirm_submit')
            <p style="color: #ef4444; font-size: 0.85rem; margin-bottom: 12px;">{{ $message }}</p>
            @enderror
            <button type="submit" wire:loading.attr="disabled" class="deposit-submit-btn" 
               @if(!$confirm_submit) disabled @endif
               style="@if(!$confirm_submit) opacity: 0.6; cursor: not-allowed; @endif">
               <span wire:loading.remove>
                  Submit Withdrawal Request
               </span>
               <span wire:loading>
                  <i class="fas fa-spinner fa-spin"></i> Processing...
               </span>
            </button>
            <p style="font-size: 12px; color: var(--text-secondary); text-align: center; margin-top: 12px; margin-bottom: 0; display: flex; align-items: center; justify-content: center; gap: 6px;">
               <i class="fas fa-info-circle"></i>
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
</style>
@endpush

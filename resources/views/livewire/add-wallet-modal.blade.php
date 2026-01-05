<?php

use App\Models\UserWallet;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|string|in:metamask,binance,other')]
    public $wallet_type = '';

    #[Validate('required|string|max:255')]
    public $wallet_address = '';

    #[Validate('required|string|max:50')]
    public $network = '';

    public $wallet_name = '';
    public $memo_tag = '';
    public $wallet_password = '';

    public function mount()
    {
        // Initialize component
    }

    public function addWallet()
    {
        $rules = [
            'wallet_type' => 'required|string|in:metamask,binance,other',
            'wallet_address' => 'required|string|max:255',
            'network' => 'required|string|max:50',
            'wallet_name' => 'nullable|string|max:100',
            'memo_tag' => 'nullable|string|max:100',
            'wallet_password' => 'required|string',
        ];

        if ($this->wallet_type === 'metamask') {
            $rules['wallet_address'] = 'required|string|max:255|starts_with:0x';
        }

        $this->validate($rules, [
            'wallet_address.starts_with' => 'MetaMask/Ethereum wallet address must start with 0x',
        ]);

        $user = Auth::user();

        if (!$user->withdrawal_password) {
            $this->addError('wallet_password', 'Withdrawal password not set.');
            return;
        }

        if (!\Illuminate\Support\Facades\Hash::check($this->wallet_password, $user->withdrawal_password)) {
            $this->addError('wallet_password', 'Incorrect withdrawal password.');
            return;
        }

        $isFirstWallet = $user->userWallets()->count() === 0;

        UserWallet::create([
            'user_id' => $user->id,
            'wallet_type' => $this->wallet_type,
            'wallet_address' => $this->wallet_address,
            'network' => $this->network,
            'wallet_name' => $this->wallet_name,
            'memo_tag' => $this->memo_tag,
            'is_default' => $isFirstWallet,
        ]);

        $this->reset(['wallet_type', 'wallet_address', 'network', 'wallet_name', 'memo_tag', 'wallet_password']);
        $this->dispatch('wallet-added');
        $this->dispatch('refresh-withdrawal-wallets');
        $this->js('setTimeout(() => { if (typeof closeAddWalletModal === "function") closeAddWalletModal(); }, 300);');
    }
}; ?>

<div class="withdrawal-modal-wrapper" style="background: transparent; border: none; box-shadow: none;">
    <div class="deposit-modal-header">
        <h3>Add New Wallet</h3>
        <button type="button" class="deposit-modal-close" onclick="closeAddWalletModal()" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="deposit-modal-content">
        <form wire:submit="addWallet" class="deposit-form-container">
            <div class="deposit-input-group">
                <label class="deposit-input-label">Wallet Type <span style="color: #ef4444;">*</span></label>
                <div class="deposit-input-wrapper">
                    <select wire:model="wallet_type" class="deposit-input" style="padding-left: 16px; appearance: none; background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'12\' viewBox=\'0 0 12 12\'%3E%3Cpath fill=\'%23ffb11a\' d=\'M6 9L1 4h10z\'/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 16px center; cursor: pointer;" wire:change="$set('network', '')">
                        <option value="">Select wallet type</option>
                        <option value="metamask">MetaMask / Ethereum</option>
                        <option value="binance">Binance</option>
                        <option value="other">Other Crypto</option>
                    </select>
                </div>
                @error('wallet_type')
                <p style="color: #ef4444; font-size: 0.85rem; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <div class="deposit-input-group">
                <label class="deposit-input-label">Wallet Address <span style="color: #ef4444;">*</span></label>
                <div class="deposit-input-wrapper">
                    <input type="text" wire:model="wallet_address" 
                        placeholder="@if($wallet_type === 'metamask') Enter Ethereum address (starts with 0x) @else Enter wallet address @endif"
                        class="deposit-input" style="padding-left: 16px;">
                </div>
                @error('wallet_address')
                <p style="color: #ef4444; font-size: 0.85rem; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <div class="deposit-input-group">
                <label class="deposit-input-label">Network / Blockchain <span style="color: #ef4444;">*</span></label>
                <div class="deposit-input-wrapper">
                    <select wire:model="network" class="deposit-input" style="padding-left: 16px; appearance: none; background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'12\' viewBox=\'0 0 12 12\'%3E%3Cpath fill=\'%23ffb11a\' d=\'M6 9L1 4h10z\'/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 16px center; cursor: pointer;">
                        <option value="">Select network</option>
                        @if($wallet_type === 'metamask')
                            <option value="Ethereum">Ethereum (ERC20)</option>
                            <option value="BSC">Binance Smart Chain (BEP20)</option>
                            <option value="Polygon">Polygon</option>
                            <option value="Arbitrum">Arbitrum</option>
                            <option value="Optimism">Optimism</option>
                        @elseif($wallet_type === 'binance')
                            <option value="BEP20">BEP20</option>
                            <option value="ERC20">ERC20</option>
                            <option value="TRC20">TRC20</option>
                            <option value="BTC">Bitcoin (BTC)</option>
                            <option value="LTC">Litecoin (LTC)</option>
                        @else
                            <option value="ERC20">ERC20</option>
                            <option value="BEP20">BEP20</option>
                            <option value="TRC20">TRC20</option>
                            <option value="BTC">Bitcoin (BTC)</option>
                            <option value="LTC">Litecoin (LTC)</option>
                            <option value="Ethereum">Ethereum</option>
                            <option value="BSC">Binance Smart Chain</option>
                            <option value="Polygon">Polygon</option>
                        @endif
                    </select>
                </div>
                @error('network')
                <p style="color: #ef4444; font-size: 0.85rem; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <div class="deposit-input-group">
                <label class="deposit-input-label">Wallet Name / Label (Optional)</label>
                <div class="deposit-input-wrapper">
                    <input type="text" wire:model="wallet_name" placeholder="e.g., My Main Wallet"
                        class="deposit-input" style="padding-left: 16px;">
                </div>
                @error('wallet_name')
                <p style="color: #ef4444; font-size: 0.85rem; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            @if(in_array($network, ['BEP20', 'BSC']) || $wallet_type === 'binance')
            <div class="deposit-input-group">
                <label class="deposit-input-label">MEMO / TAG</label>
                <div class="deposit-input-wrapper">
                    <input type="text" wire:model="memo_tag" placeholder="Required for BNB, XRP, XLM, etc."
                        class="deposit-input" style="padding-left: 16px;">
                </div>
                <small style="font-size: 12px; color: var(--text-secondary); margin-top: 4px; display: block;">Required for certain coins like BNB, XRP, XLM</small>
                @error('memo_tag')
                <p style="color: #ef4444; font-size: 0.85rem; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>
            @endif

            <div class="deposit-input-group">
                <label class="deposit-input-label">Withdrawal Password <span style="color: #ef4444;">*</span></label>
                <div class="deposit-input-wrapper">
                    <input type="password" wire:model="wallet_password" placeholder="Enter withdrawal password"
                        class="deposit-input" style="padding-left: 16px;">
                </div>
                @error('wallet_password')
                <p style="color: #ef4444; font-size: 0.85rem; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <div class="deposit-submit-section">
                <button type="submit" wire:loading.attr="disabled" class="deposit-submit-btn">
                    <span wire:loading.remove>
                        <i class="fas fa-check"></i> Add Wallet
                    </span>
                    <span wire:loading>
                        <i class="fas fa-spinner fa-spin"></i> Adding...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>


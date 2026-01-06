<?php

use App\Models\UserWallet;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component {
    public $user_wallets = [];

    public function mount()
    {
        $this->loadWallets();
    }

    public function loadWallets()
    {
        $user = Auth::user();
        $this->user_wallets = $user->userWallets()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    #[On('wallet-added')]
    #[On('refresh-withdrawal-wallets')]
    public function handleWalletRefresh()
    {
        $this->loadWallets();
    }
}; ?>

<div class="withdrawal-modal-wrapper" style="background: transparent; border: none; box-shadow: none;">
    <div class="deposit-modal-header">
        <h3>My Wallets</h3>
        <button type="button" class="deposit-modal-close" onclick="closeWalletListModal()" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="deposit-modal-content">
        <div class="deposit-form-container">
            @if(empty($user_wallets))
                <div style="padding: 2rem; text-align: center;">
                    <i class="fas fa-wallet" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p style="color: var(--text-secondary); font-size: 1rem; margin-bottom: 1rem;">No wallets configured yet</p>
                    <p style="color: var(--text-secondary); font-size: 0.85rem;">Add a wallet from the withdrawal form to get started</p>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($user_wallets as $wallet)
                        <div style="padding: 1rem; background: var(--secondary); border: 1px solid var(--border); border-radius: 8px; display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; flex-shrink: 0;">
                                @if($wallet['wallet_type'] === 'metamask')
                                    <i class="fab fa-ethereum"></i>
                                @elseif($wallet['wallet_type'] === 'binance')
                                    <i class="fab fa-bitcoin"></i>
                                @else
                                    <i class="fas fa-wallet"></i>
                                @endif
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                    <span style="font-weight: 600; color: var(--text-primary); font-size: 0.95rem;">
                                        {{ $wallet['wallet_name'] ?: ucfirst($wallet['wallet_type']) }}
                                    </span>
                                    @if($wallet['is_default'])
                                        <span style="padding: 0.2rem 0.5rem; background: rgba(16, 185, 129, 0.2); color: #10b981; border-radius: 4px; font-size: 0.7rem; font-weight: 500;">Default</span>
                                    @endif
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-secondary); font-family: monospace; word-break: break-all;">
                                    {{ substr($wallet['wallet_address'], 0, 10) }}...{{ substr($wallet['wallet_address'], -8) }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">
                                    {{ $wallet['network'] }}
                                    @if($wallet['memo_tag'])
                                        • MEMO: {{ $wallet['memo_tag'] }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>


<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Component;

new class extends Component {
    public string $binance_wallet_address = '';
    public string $metamask_wallet_address = '';
    public string $withdrawal_password = '';
    public string $withdrawal_password_confirmation = '';
    public bool $has_withdrawal_password = false;

    public function mount(): void
    {
        $user = Auth::user();
        $this->binance_wallet_address = $user->binance_wallet_address ?? '';
        $this->metamask_wallet_address = $user->metamask_wallet_address ?? '';
        $this->has_withdrawal_password = !empty($user->withdrawal_password);
    }

    public function updateWalletAddresses(): void
    {
        $validated = $this->validate([
            'binance_wallet_address' => ['nullable', 'string', 'max:255'],
            'metamask_wallet_address' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        $user->update($validated);

        $this->dispatch('wallet-addresses-updated');
        session()->flash('status', 'wallet-addresses-updated');
    }

    public function deleteBinanceWallet(): void
    {
        $user = Auth::user();
        $user->update(['binance_wallet_address' => null]);
        $this->binance_wallet_address = '';
        session()->flash('status', 'binance-wallet-deleted');
    }

    public function deleteMetamaskWallet(): void
    {
        $user = Auth::user();
        $user->update(['metamask_wallet_address' => null]);
        $this->metamask_wallet_address = '';
        session()->flash('status', 'metamask-wallet-deleted');
    }

    public function setWithdrawalPassword(): void
    {
        $user = Auth::user();

        if ($user->withdrawal_password) {
            $this->addError('withdrawal_password', 'Withdrawal password can only be set once.');
            return;
        }

        $validated = $this->validate([
            'withdrawal_password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user->update([
            'withdrawal_password' => $validated['withdrawal_password'],
        ]);

        $this->has_withdrawal_password = true;
        $this->withdrawal_password = '';
        $this->withdrawal_password_confirmation = '';

        $this->dispatch('withdrawal-password-set');
        session()->flash('status', 'withdrawal-password-set');
        
        $this->redirect(route('profile'), navigate: true);
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Withdrawal Settings')" :subheading="__('Manage your withdrawal wallet addresses and password')">
    <div class="mt-6 space-y-6">
        <!-- Wallet Addresses Section -->
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Withdrawal Wallet Addresses
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Set your wallet addresses for withdrawals. These will be used when processing withdrawal requests.
                </p>

                <form wire:submit="updateWalletAddresses" class="mt-6 space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="binance_wallet_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Binance Wallet Address
                            </label>
                            @if($binance_wallet_address)
                                <button
                                    type="button"
                                    wire:click="deleteBinanceWallet"
                                    class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                    onclick="return confirm('Are you sure you want to delete this wallet address?')"
                                >
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            @endif
                        </div>
                        <input
                            type="text"
                            id="binance_wallet_address"
                            wire:model="binance_wallet_address"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                            placeholder="Enter Binance wallet address"
                        />
                        @if($binance_wallet_address)
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 font-mono">
                                {{ substr($binance_wallet_address, 0, 12) }}...{{ substr($binance_wallet_address, -8) }}
                            </p>
                        @endif
                        @error('binance_wallet_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="metamask_wallet_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                MetaMask (ERC20) Wallet Address
                            </label>
                            @if($metamask_wallet_address)
                                <button
                                    type="button"
                                    wire:click="deleteMetamaskWallet"
                                    class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                    onclick="return confirm('Are you sure you want to delete this wallet address?')"
                                >
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            @endif
                        </div>
                        <input
                            type="text"
                            id="metamask_wallet_address"
                            wire:model="metamask_wallet_address"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                            placeholder="Enter MetaMask ERC20 wallet address"
                        />
                        @if($metamask_wallet_address)
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 font-mono">
                                {{ substr($metamask_wallet_address, 0, 12) }}...{{ substr($metamask_wallet_address, -8) }}
                            </p>
                        @endif
                        @error('metamask_wallet_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                        >
                            Save Wallet Addresses
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Withdrawal Password Section -->
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Withdrawal Password
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    @if($has_withdrawal_password)
                        Your withdrawal password has been set. This password is required for all withdrawal requests and cannot be changed once set.
                    @else
                        Set a withdrawal password. This password will be required for all withdrawal requests and can only be set once.
                    @endif
                </p>

                @if(!$has_withdrawal_password)
                    <form wire:submit="setWithdrawalPassword" class="mt-6 space-y-4">
                        <div>
                            <label for="withdrawal_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Withdrawal Password
                            </label>
                            <input
                                type="password"
                                id="withdrawal_password"
                                wire:model="withdrawal_password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                                placeholder="Enter withdrawal password (min 6 characters)"
                            />
                            @error('withdrawal_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="withdrawal_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Confirm Withdrawal Password
                            </label>
                            <input
                                type="password"
                                id="withdrawal_password_confirmation"
                                wire:model="withdrawal_password_confirmation"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                                placeholder="Confirm withdrawal password"
                            />
                            @error('withdrawal_password_confirmation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                Set Withdrawal Password
                            </button>
                        </div>
                    </form>
                @else
                    <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
                        <p class="text-sm text-green-800 dark:text-green-200">
                            <i class="fas fa-check-circle mr-2"></i>
                            Withdrawal password is set and active.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </x-settings.layout>
</section>


<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public string $withdrawal_password = '';
    public string $withdrawal_password_confirmation = '';

    public function setPassword(): void
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

        $this->withdrawal_password = '';
        $this->withdrawal_password_confirmation = '';

        $this->dispatch('withdrawal-password-set');
        session()->flash('withdrawal_password_set', true);
    }
}; ?>

<div class="withdrawal-modal-wrapper" style="background: transparent; border: none; box-shadow: none;">
    <style>
        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
            z-index: 10;
        }

        .password-toggle-btn:hover {
            color: rgba(255, 255, 255, 0.9);
        }

        .password-toggle-btn:focus {
            outline: none;
        }

        .password-toggle-btn i {
            font-size: 16px;
        }
    </style>

    <div class="withdrawal-header" style="border-bottom: 1px solid rgba(255, 177, 26, 0.2); margin-bottom: 1.5rem;">
        <h3 class="withdrawal-title">Set Withdrawal Password</h3>
        <button type="button" class="withdrawal-close-btn" onclick="closePasswordSetModal()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="withdrawal-content">
        <form wire:submit="setPassword" class="withdrawal-form">
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-lock"></i> Withdrawal Password <span class="required">*</span>
                </label>
                <div style="position: relative;">
                    <input type="password" id="withdrawal_password_input" wire:model="withdrawal_password" placeholder="Enter withdrawal password (min 6 characters)"
                        class="form-input" style="padding-right: 45px;">
                    <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('withdrawal_password_input', 'withdrawal_password_eye')">
                        <i class="fas fa-eye" id="withdrawal_password_eye"></i>
                    </button>
                </div>
                @error('withdrawal_password')
                <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-lock"></i> Confirm Password <span class="required">*</span>
                </label>
                <div style="position: relative;">
                    <input type="password" id="withdrawal_password_confirmation_input" wire:model="withdrawal_password_confirmation" placeholder="Confirm withdrawal password"
                        class="form-input" style="padding-right: 45px;">
                    <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('withdrawal_password_confirmation_input', 'withdrawal_password_confirmation_eye')">
                        <i class="fas fa-eye" id="withdrawal_password_confirmation_eye"></i>
                    </button>
                </div>
                @error('withdrawal_password_confirmation')
                <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="p-3 mb-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
                <p class="text-sm text-blue-800 dark:text-blue-200">
                    <i class="fas fa-info-circle mr-2"></i>
                    This password will be required for all withdrawal requests and can only be set once.
                </p>
            </div>

            <div class="submit-section">
                <button type="submit" wire:loading.attr="disabled" class="submit-btn">
                    <span wire:loading.remove>
                        <i class="fas fa-check"></i> Set Password
                    </span>
                    <span wire:loading>
                        <i class="fas fa-spinner fa-spin"></i> Saving...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('withdrawal-password-set', () => {
                if (typeof closePasswordSetModal === 'function') {
                    closePasswordSetModal();
                }
                setTimeout(() => {
                    if (typeof openWithdrawalModal === 'function') {
                        openWithdrawalModal();
                    }
                }, 300);
            });
        });
    </script>
</div>



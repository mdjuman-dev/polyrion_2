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
    <style>
        .withdrawal-modal-wrapper {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .withdrawal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 177, 26, 0.2);
        }

        .withdrawal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .withdrawal-close-btn {
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

        .withdrawal-close-btn:hover {
            background: var(--hover);
            color: var(--text-primary);
        }

        .withdrawal-content {
            padding: 1.5rem;
            overflow-y: auto;
            flex: 1;
        }

        .withdrawal-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-label i {
            color: #ffb11a;
        }

        .form-label .required {
            color: #ef4444;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--secondary);
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-input::placeholder {
            color: var(--text-secondary);
            opacity: 0.7;
        }

        .form-input:focus {
            outline: none;
            border-color: #ffb11a;
            box-shadow: 0 0 0 3px rgba(255, 177, 26, 0.1);
        }

        .error-text {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .submit-section {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1rem;
        }

        .submit-btn {
            width: 100%;
            padding: 0.875rem 1.5rem;
            background: linear-gradient(135deg, #ffb11a 0%, #ff9500 100%);
            color: #000;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(255, 177, 26, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 177, 26, 0.4);
            background: linear-gradient(135deg, #ff9500 0%, #ffb11a 100%);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

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



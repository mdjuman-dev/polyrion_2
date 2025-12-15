@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Withdraw Funds - {{ $appName }}</title>
    <meta name="description" content="Withdraw your funds from {{ $appName }}. Secure and fast withdrawal process.">
@endsection
@section('content')
    <main style="padding-bottom: 120px;">
        <div class="container mt-5 mb-5">
            <div class="row d-flex justify-content-between m-auto">
                <!-- Left Column - Withdrawal Form -->
                <div class="col-lg-7 col-md-12 mb-4">
                    <div class="profile-card">
                        <div class="profile-header">
                            <div class="profile-info">
                                <div class="profile-id-wrapper d-flex justify-content-between align-items-center">
                                    <span class="profile-id">
                                        <i class="fas fa-money-bill-wave me-2"></i>Request Withdrawal
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Wallet Balance Display -->
                        <div class="withdrawal-balance-card">
                            <div class="balance-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="balance-label">Available Balance</div>
                            <div class="balance-value">{{ number_format($wallet->balance, 2) }} {{ $wallet->currency }}
                            </div>
                            <div class="balance-subtitle">Minimum withdrawal: 10 {{ $wallet->currency }}</div>
                        </div>

                        <form id="withdrawalForm" class="withdrawal-form">
                            @csrf
                            <div class="form-group-custom">
                                <label for="amount" class="form-label-custom">
                                    <i class="fas fa-dollar-sign"></i> Amount <span class="text-danger">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <input type="number" class="form-input-custom" id="amount" name="amount"
                                        step="0.01" min="10" max="{{ $wallet->balance }}"
                                        placeholder="Enter withdrawal amount" required>
                                    <span class="input-currency">{{ $wallet->currency }}</span>
                                </div>
                                <small class="form-hint">Minimum: 10 {{ $wallet->currency }}</small>
                            </div>

                            <div class="form-group-custom">
                                <label for="payment_method" class="form-label-custom">
                                    <i class="fas fa-credit-card"></i> Payment Method <span class="text-danger">*</span>
                                </label>
                                <select class="form-input-custom" id="payment_method" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="bank">🏦 Bank Transfer</option>
                                    <option value="crypto">₿ Cryptocurrency</option>
                                    <option value="paypal">💳 PayPal</option>
                                </select>
                            </div>

                            <!-- Payment Details Fields (Dynamic) -->
                            <div id="paymentDetailsContainer">
                                <!-- Bank Details -->
                                <div id="bankDetails" class="payment-details-section" style="display: none;">
                                    <div class="payment-details-header">
                                        <i class="fas fa-university"></i> Bank Account Details
                                    </div>
                                    <div class="form-group-custom">
                                        <label for="bank_name" class="form-label-custom">Bank Name</label>
                                        <input type="text" class="form-input-custom" id="bank_name" name="bank_name"
                                            placeholder="Enter bank name">
                                    </div>
                                    <div class="form-group-custom">
                                        <label for="account_number" class="form-label-custom">Account Number</label>
                                        <input type="text" class="form-input-custom" id="account_number"
                                            name="account_number" placeholder="Enter account number">
                                    </div>
                                    <div class="form-group-custom">
                                        <label for="account_holder" class="form-label-custom">Account Holder Name</label>
                                        <input type="text" class="form-input-custom" id="account_holder"
                                            name="account_holder" placeholder="Enter account holder name">
                                    </div>
                                    <div class="form-group-custom">
                                        <label for="swift_code" class="form-label-custom">SWIFT/IBAN Code</label>
                                        <input type="text" class="form-input-custom" id="swift_code" name="swift_code"
                                            placeholder="Enter SWIFT or IBAN code">
                                    </div>
                                </div>

                                <!-- Crypto Details -->
                                <div id="cryptoDetails" class="payment-details-section" style="display: none;">
                                    <div class="payment-details-header">
                                        <i class="fab fa-bitcoin"></i> Cryptocurrency Details
                                    </div>
                                    <div class="form-group-custom">
                                        <label for="crypto_type" class="form-label-custom">Cryptocurrency Type</label>
                                        <select class="form-input-custom" id="crypto_type" name="crypto_type">
                                            <option value="USDT">USDT</option>
                                            <option value="BTC">Bitcoin (BTC)</option>
                                            <option value="ETH">Ethereum (ETH)</option>
                                            <option value="BNB">Binance Coin (BNB)</option>
                                        </select>
                                    </div>
                                    <div class="form-group-custom">
                                        <label for="wallet_address" class="form-label-custom">Wallet Address</label>
                                        <input type="text" class="form-input-custom" id="wallet_address"
                                            name="wallet_address" placeholder="Enter your wallet address">
                                    </div>
                                    <div class="form-group-custom">
                                        <label for="network" class="form-label-custom">Network</label>
                                        <select class="form-input-custom" id="network" name="network">
                                            <option value="TRC20">TRC20 (Tron)</option>
                                            <option value="ERC20">ERC20 (Ethereum)</option>
                                            <option value="BEP20">BEP20 (Binance Smart Chain)</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- PayPal Details -->
                                <div id="paypalDetails" class="payment-details-section" style="display: none;">
                                    <div class="payment-details-header">
                                        <i class="fab fa-paypal"></i> PayPal Details
                                    </div>
                                    <div class="form-group-custom">
                                        <label for="paypal_email" class="form-label-custom">PayPal Email</label>
                                        <input type="email" class="form-input-custom" id="paypal_email"
                                            name="paypal_email" placeholder="your.email@example.com">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn-withdrawal-submit">
                                <i class="fas fa-paper-plane"></i> Submit Withdrawal Request
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right Column - Withdrawal History -->
                <div class="col-lg-5 col-md-12 mb-4">
                    <div class="positions-activity-card">
                        <div class="content-tabs">
                            <button type="button" class="content-tab active" data-tab="history">
                                <i class="fas fa-history"></i> History
                            </button>
                        </div>

                        <div class="tab-content-wrapper" id="history-tab">
                            @if ($withdrawals->count() > 0)
                                <div class="withdrawal-history-list">
                                    @foreach ($withdrawals as $withdrawal)
                                        <div class="withdrawal-history-item">
                                            <div class="withdrawal-item-header">
                                                <div class="withdrawal-item-amount">
                                                    {{ number_format($withdrawal->amount, 2) }} {{ $withdrawal->currency }}
                                                </div>
                                                <div class="withdrawal-item-status">
                                                    @if ($withdrawal->status == 'pending')
                                                        <span class="status-badge status-pending">
                                                            <i class="fas fa-clock"></i> Pending
                                                        </span>
                                                    @elseif($withdrawal->status == 'processing')
                                                        <span class="status-badge status-processing">
                                                            <i class="fas fa-spinner fa-spin"></i> Processing
                                                        </span>
                                                    @elseif($withdrawal->status == 'completed')
                                                        <span class="status-badge status-completed">
                                                            <i class="fas fa-check-circle"></i> Completed
                                                        </span>
                                                    @elseif($withdrawal->status == 'rejected')
                                                        <span class="status-badge status-rejected">
                                                            <i class="fas fa-times-circle"></i> Rejected
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="withdrawal-item-details">
                                                <div class="withdrawal-item-method">
                                                    <i
                                                        class="fas fa-{{ $withdrawal->payment_method == 'bank' ? 'university' : ($withdrawal->payment_method == 'crypto' ? 'coins' : 'paypal') }}"></i>
                                                    {{ ucfirst($withdrawal->payment_method) }}
                                                </div>
                                                <div class="withdrawal-item-date">
                                                    {{ $withdrawal->created_at->format('M d, Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="pagination-wrapper mt-3">
                                    {{ $withdrawals->links() }}
                                </div>
                            @else
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>No withdrawal requests yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('style')
        <style>
            /* Main Container - Fix Footer Overlap */
            main {
                padding-bottom: 120px !important;
                min-height: calc(100vh - 200px);
            }

            @media (max-width: 768px) {
                main {
                    padding-bottom: 100px !important;
                }
            }

            /* Withdrawal Balance Card - Premium Design */
            .withdrawal-balance-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 16px;
                padding: 32px 24px;
                margin-bottom: 32px;
                text-align: center;
                color: #ffffff;
                position: relative;
                overflow: hidden;
                box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
            }

            .withdrawal-balance-card::before {
                content: '';
                position: absolute;
                top: -50%;
                right: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
                animation: pulse 3s ease-in-out infinite;
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: 0.3;
                }

                50% {
                    opacity: 0.6;
                }
            }

            .balance-icon {
                font-size: 48px;
                margin-bottom: 16px;
                opacity: 0.9;
            }

            .balance-label {
                font-size: 13px;
                opacity: 0.9;
                margin-bottom: 8px;
                text-transform: uppercase;
                letter-spacing: 1px;
                font-weight: 600;
            }

            .balance-value {
                font-size: 36px;
                font-weight: 700;
                margin-bottom: 8px;
                text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            }

            .balance-subtitle {
                font-size: 12px;
                opacity: 0.8;
                font-weight: 500;
            }

            /* Form Styles - Premium */
            .withdrawal-form {
                padding: 0;
            }

            .form-group-custom {
                margin-bottom: 24px;
            }

            .form-label-custom {
                display: block;
                font-size: 14px;
                font-weight: 600;
                color: #ffffff;
                margin-bottom: 10px;
                letter-spacing: 0.3px;
            }

            .form-label-custom i {
                margin-right: 8px;
                color: #ffb11a;
            }

            .input-wrapper {
                position: relative;
            }

            .form-input-custom {
                width: 100%;
                padding: 14px 16px;
                border: 2px solid rgba(255, 255, 255, 0.1);
                border-radius: 12px;
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(10px);
                color: #ffffff;
                font-size: 15px;
                transition: all 0.3s ease;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            .form-input-custom::placeholder {
                color: rgba(255, 255, 255, 0.5);
            }

            .form-input-custom:focus {
                outline: none;
                border-color: #ffb11a;
                background: rgba(255, 255, 255, 0.08);
                box-shadow: 0 0 0 4px rgba(255, 177, 26, 0.15), 0 4px 16px rgba(0, 0, 0, 0.2);
                transform: translateY(-2px);
            }

            .input-currency {
                position: absolute;
                right: 16px;
                top: 50%;
                transform: translateY(-50%);
                color: rgba(255, 255, 255, 0.7);
                font-weight: 600;
                font-size: 14px;
                pointer-events: none;
            }

            .form-hint {
                display: block;
                font-size: 12px;
                color: rgba(255, 255, 255, 0.6);
                margin-top: 6px;
                font-weight: 500;
            }

            /* Payment Details Section - Premium */
            .payment-details-section {
                background: rgba(255, 255, 255, 0.03);
                border-radius: 16px;
                padding: 24px;
                margin-top: 24px;
                border: 2px solid rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
            }

            .payment-details-header {
                font-size: 16px;
                font-weight: 700;
                color: #ffffff;
                margin-bottom: 20px;
                padding-bottom: 16px;
                border-bottom: 2px solid rgba(255, 255, 255, 0.1);
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .payment-details-header i {
                color: #ffb11a;
                font-size: 18px;
            }

            /* Submit Button - Premium */
            .btn-withdrawal-submit {
                width: 100%;
                padding: 18px;
                background: linear-gradient(135deg, #ffb11a 0%, #ff9500 100%);
                color: #1a1a1a;
                border: none;
                border-radius: 12px;
                font-size: 16px;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.3s ease;
                margin-top: 32px;
                box-shadow: 0 4px 16px rgba(255, 177, 26, 0.3);
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .btn-withdrawal-submit:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 24px rgba(255, 177, 26, 0.4);
                background: linear-gradient(135deg, #ff9500 0%, #ffb11a 100%);
            }

            .btn-withdrawal-submit:active {
                transform: translateY(-1px);
            }

            .btn-withdrawal-submit:disabled {
                opacity: 0.6;
                cursor: not-allowed;
                transform: none;
            }

            .btn-withdrawal-submit i {
                margin-right: 10px;
            }

            /* Withdrawal History - Premium */
            .withdrawal-history-list {
                max-height: 600px;
                overflow-y: auto;
                padding-right: 8px;
            }

            .withdrawal-history-list::-webkit-scrollbar {
                width: 6px;
            }

            .withdrawal-history-list::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.05);
                border-radius: 10px;
            }

            .withdrawal-history-list::-webkit-scrollbar-thumb {
                background: rgba(255, 177, 26, 0.5);
                border-radius: 10px;
            }

            .withdrawal-history-item {
                background: rgba(255, 255, 255, 0.03);
                border-radius: 12px;
                padding: 18px;
                margin-bottom: 12px;
                border: 1px solid rgba(255, 255, 255, 0.1);
                transition: all 0.3s ease;
                backdrop-filter: blur(10px);
            }

            .withdrawal-history-item:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
                border-color: rgba(255, 177, 26, 0.3);
                background: rgba(255, 255, 255, 0.05);
            }

            .withdrawal-item-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 14px;
            }

            .withdrawal-item-amount {
                font-size: 20px;
                font-weight: 700;
                color: #ffffff;
            }

            .withdrawal-item-details {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 13px;
                color: rgba(255, 255, 255, 0.7);
            }

            .withdrawal-item-method {
                display: flex;
                align-items: center;
                gap: 8px;
                font-weight: 600;
            }

            .withdrawal-item-method i {
                color: #ffb11a;
                font-size: 14px;
            }

            /* Status Badges - Premium */
            .status-badge {
                padding: 6px 14px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .status-badge i {
                font-size: 10px;
            }

            .status-pending {
                background: rgba(255, 193, 7, 0.2);
                color: #ffc107;
                border: 1px solid rgba(255, 193, 7, 0.3);
            }

            .status-processing {
                background: rgba(23, 162, 184, 0.2);
                color: #17a2b8;
                border: 1px solid rgba(23, 162, 184, 0.3);
            }

            .status-completed {
                background: rgba(40, 167, 69, 0.2);
                color: #28a745;
                border: 1px solid rgba(40, 167, 69, 0.3);
            }

            .status-rejected {
                background: rgba(220, 53, 69, 0.2);
                color: #dc3545;
                border: 1px solid rgba(220, 53, 69, 0.3);
            }

            /* Empty State - Premium */
            .empty-state {
                text-align: center;
                padding: 80px 20px;
                color: rgba(255, 255, 255, 0.6);
            }

            .empty-state i {
                font-size: 64px;
                margin-bottom: 20px;
                opacity: 0.4;
                color: rgba(255, 255, 255, 0.3);
            }

            .empty-state p {
                font-size: 15px;
                margin: 0;
                font-weight: 500;
            }

            /* Select Dropdown Styling */
            select.form-input-custom {
                appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23ffb11a' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 16px center;
                padding-right: 40px;
                cursor: pointer;
            }

            /* Select Option Styling - Fix Background and Text Color */
            select.form-input-custom option {
                background: #16171a !important;
                color: #ffffff !important;
                padding: 12px 16px;
                border: none;
                font-size: 15px;
            }

            select.form-input-custom option:hover,
            select.form-input-custom option:focus {
                background: #25282c !important;
                color: #ffb11a !important;
            }

            select.form-input-custom option:checked,
            select.form-input-custom option[selected] {
                background: #ffb11a !important;
                color: #1a1a1a !important;
                font-weight: 600;
            }

            /* For better browser compatibility - ensure select has proper background */
            select.form-input-custom {
                background-color: rgba(255, 255, 255, 0.05) !important;
                color: #ffffff !important;
            }

            select.form-input-custom:focus {
                background-color: rgba(255, 255, 255, 0.08) !important;
                color: #ffffff !important;
            }

            /* Ensure selected option is visible */
            select.form-input-custom:not([multiple]) {
                color: #ffffff !important;
            }

            /* Responsive - Premium */
            @media (max-width: 768px) {
                main {
                    padding-bottom: 100px !important;
                }

                .withdrawal-balance-card {
                    padding: 24px 20px;
                }

                .balance-value {
                    font-size: 28px;
                }

                .balance-icon {
                    font-size: 40px;
                }

                .withdrawal-history-list {
                    max-height: 400px;
                }

                .form-input-custom {
                    padding: 12px 14px;
                }
            }

            /* Pagination Styling */
            .pagination-wrapper .pagination {
                justify-content: center;
            }

            .pagination-wrapper .page-link {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                color: #ffffff;
            }

            .pagination-wrapper .page-item.active .page-link {
                background: #ffb11a;
                border-color: #ffb11a;
                color: #1a1a1a;
            }
        </style>
    @endpush

    @push('script')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                // Show/hide payment details based on method
                $('#payment_method').on('change', function() {
                    $('.payment-details-section').hide();
                    const method = $(this).val();

                    if (method === 'bank') {
                        $('#bankDetails').show();
                    } else if (method === 'crypto') {
                        $('#cryptoDetails').show();
                    } else if (method === 'paypal') {
                        $('#paypalDetails').show();
                    }
                });

                // Form submission
                $('#withdrawalForm').on('submit', function(e) {
                    e.preventDefault();

                    const formData = {
                        amount: $('#amount').val(),
                        payment_method: $('#payment_method').val(),
                        payment_details: {}
                    };

                    // Collect payment details based on method
                    if (formData.payment_method === 'bank') {
                        formData.payment_details = {
                            bank_name: $('#bank_name').val(),
                            account_number: $('#account_number').val(),
                            account_holder: $('#account_holder').val(),
                            swift_code: $('#swift_code').val()
                        };
                    } else if (formData.payment_method === 'crypto') {
                        formData.payment_details = {
                            crypto_type: $('#crypto_type').val(),
                            wallet_address: $('#wallet_address').val(),
                            network: $('#network').val()
                        };
                    } else if (formData.payment_method === 'paypal') {
                        formData.payment_details = {
                            paypal_email: $('#paypal_email').val()
                        };
                    }

                    // Disable submit button
                    const submitBtn = $(this).find('button[type="submit"]');
                    submitBtn.prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin"></i> Processing...');

                    $.ajax({
                        url: '{{ route('withdrawal.store') }}',
                        method: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message,
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#ffb11a',
                                    background: '#1a1a1a',
                                    color: '#ffffff'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message,
                                    confirmButtonColor: '#ffb11a',
                                    background: '#1a1a1a',
                                    color: '#ffffff'
                                });
                                submitBtn.prop('disabled', false).html(
                                    '<i class="fas fa-paper-plane"></i> Submit Withdrawal Request'
                                    );
                            }
                        },
                        error: function(xhr) {
                            let errorMsg = 'An error occurred. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;
                                errorMsg = Object.values(errors).flat().join('<br>');
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                html: errorMsg,
                                confirmButtonColor: '#ffb11a',
                                background: '#1a1a1a',
                                color: '#ffffff'
                            });
                            submitBtn.prop('disabled', false).html(
                                '<i class="fas fa-paper-plane"></i> Submit Withdrawal Request');
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection

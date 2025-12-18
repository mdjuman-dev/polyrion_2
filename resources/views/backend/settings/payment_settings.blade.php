@extends('backend.layouts.master')
@section('title', 'Payment Settings')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <!-- Breadcrumb -->
                <div class="row mb-3">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.backend.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Payment Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-body p-0">
                                <form method="POST" action="{{ route('admin.payment.settings.update') }}"
                                    id="paymentSettingsForm">
                                    @csrf

                                    <div class="row g-0">
                                        <!-- Left Sidebar Navigation -->
                                        <div class="col-md-3 settings-sidebar">
                                            <div class="settings-nav">
                                                <ul class="nav nav-pills flex-column" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" data-bs-toggle="pill" href="#binance-pay"
                                                            role="tab">
                                                            <div class="nav-icon">
                                                                <i data-feather="dollar-sign"></i>
                                                            </div>
                                                            <span>Binance Pay</span>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-bs-toggle="pill" href="#metamask"
                                                            role="tab">
                                                            <div class="nav-icon">
                                                                <i data-feather="hexagon"></i>
                                                            </div>
                                                            <span>MetaMask</span>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-bs-toggle="pill" href="#trustwallet"
                                                            role="tab">
                                                            <div class="nav-icon">
                                                                <i data-feather="shield"></i>
                                                            </div>
                                                            <span>Trust Wallet</span>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-bs-toggle="pill" href="#crypto-link"
                                                            role="tab">
                                                            <div class="nav-icon">
                                                                <i data-feather="link"></i>
                                                            </div>
                                                            <span>Crypto Link (USDT/USDC)</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Right Content Area -->
                                        <div class="col-md-9 settings-content">
                                            <div class="tab-content p-4">

                                                <!-- Binance Pay Tab -->
                                                <div class="tab-pane fade show active" id="binance-pay" role="tabpanel">
                                                    <h5 class="mb-4">Binance Pay</h5>

                                                    <div class="alert alert-info mb-4">
                                                        <i class="fa fa-info-circle me-2"></i>
                                                        Get your API credentials from <a href="https://merchant.binance.com"
                                                            target="_blank" class="alert-link">Binance Merchant Portal</a>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">API Key</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i
                                                                            class="fa fa-key"></i></span>
                                                                    <input type="password" name="binance_api_key"
                                                                        id="binance_api_key" class="form-control"
                                                                        value="{{ old('binance_api_key', $binanceSettings['binance_api_key']) }}"
                                                                        placeholder="Enter Binance API Key">
                                                                    <button type="button" class="btn btn-outline-secondary"
                                                                        onclick="togglePassword('binance_api_key')">
                                                                        <i class="fa fa-eye" id="binance_api_key_icon"></i>
                                                                    </button>
                                                                </div>
                                                                <small class="text-muted">Your Binance Pay merchant API
                                                                    key</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Secret Key</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i
                                                                            class="fa fa-lock"></i></span>
                                                                    <input type="password" name="binance_secret_key"
                                                                        id="binance_secret_key" class="form-control"
                                                                        value="{{ old('binance_secret_key', $binanceSettings['binance_secret_key']) }}"
                                                                        placeholder="Enter Binance Secret Key">
                                                                    <button type="button"
                                                                        class="btn btn-outline-secondary"
                                                                        onclick="togglePassword('binance_secret_key')">
                                                                        <i class="fa fa-eye"
                                                                            id="binance_secret_key_icon"></i>
                                                                    </button>
                                                                </div>
                                                                <small class="text-muted">Your Binance Pay merchant secret
                                                                    key</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Base URL</label>
                                                                <input type="url" name="binance_base_url"
                                                                    class="form-control"
                                                                    value="{{ old('binance_base_url', $binanceSettings['binance_base_url']) }}"
                                                                    placeholder="https://bpay.binanceapi.com">
                                                                <small class="text-muted">Default:
                                                                    https://bpay.binanceapi.com</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- MetaMask Tab -->
                                                <div class="tab-pane fade" id="metamask" role="tabpanel">
                                                    <h5 class="mb-4">MetaMask</h5>

                                                    <h6 class="text-primary mb-3"><i
                                                            class="fa fa-wallet me-2"></i>Merchant Wallet Addresses</h6>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Ethereum Wallet Address
                                                                    (ERC-20)</label>
                                                                <input type="text" name="merchant_address"
                                                                    class="form-control"
                                                                    value="{{ old('merchant_address', $metamaskSettings['merchant_address']) }}"
                                                                    placeholder="0x...">
                                                                <small class="text-muted">Your wallet address for receiving
                                                                    ETH/ERC-20 tokens</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">BSC Wallet Address
                                                                    (BEP-20)</label>
                                                                <input type="text" name="merchant_address_bsc"
                                                                    class="form-control"
                                                                    value="{{ old('merchant_address_bsc', $metamaskSettings['merchant_address_bsc']) }}"
                                                                    placeholder="0x...">
                                                                <small class="text-muted">Leave empty to use Ethereum
                                                                    address</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Polygon Wallet Address</label>
                                                                <input type="text" name="merchant_address_polygon"
                                                                    class="form-control"
                                                                    value="{{ old('merchant_address_polygon', $metamaskSettings['merchant_address_polygon']) }}"
                                                                    placeholder="0x...">
                                                                <small class="text-muted">Leave empty to use Ethereum
                                                                    address</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Default Chain Network</label>
                                                                <select name="chain_network" class="form-control">
                                                                    <option value="ethereum"
                                                                        {{ ($metamaskSettings['chain_network'] ?? 'ethereum') == 'ethereum' ? 'selected' : '' }}>
                                                                        Ethereum</option>
                                                                    <option value="bsc"
                                                                        {{ ($metamaskSettings['chain_network'] ?? '') == 'bsc' ? 'selected' : '' }}>
                                                                        Binance Smart Chain (BSC)</option>
                                                                    <option value="polygon"
                                                                        {{ ($metamaskSettings['chain_network'] ?? '') == 'polygon' ? 'selected' : '' }}>
                                                                        Polygon</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <hr class="my-4">

                                                    <h6 class="text-primary mb-3"><i class="fa fa-key me-2"></i>Blockchain
                                                        Explorer API Keys</h6>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Etherscan API Key</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i
                                                                            class="fa fa-key"></i></span>
                                                                    <input type="password" name="etherscan_api_key"
                                                                        id="etherscan_api_key" class="form-control"
                                                                        value="{{ old('etherscan_api_key', $metamaskSettings['etherscan_api_key']) }}"
                                                                        placeholder="Enter Etherscan API Key">
                                                                    <button type="button"
                                                                        class="btn btn-outline-secondary"
                                                                        onclick="togglePassword('etherscan_api_key')">
                                                                        <i class="fa fa-eye"
                                                                            id="etherscan_api_key_icon"></i>
                                                                    </button>
                                                                </div>
                                                                <small class="text-muted">Get from <a
                                                                        href="https://etherscan.io/apis"
                                                                        target="_blank">etherscan.io/apis</a></small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">BSCScan API Key</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i
                                                                            class="fa fa-key"></i></span>
                                                                    <input type="password" name="bscscan_api_key"
                                                                        id="bscscan_api_key" class="form-control"
                                                                        value="{{ old('bscscan_api_key', $metamaskSettings['bscscan_api_key']) }}"
                                                                        placeholder="Enter BSCScan API Key">
                                                                    <button type="button"
                                                                        class="btn btn-outline-secondary"
                                                                        onclick="togglePassword('bscscan_api_key')">
                                                                        <i class="fa fa-eye"
                                                                            id="bscscan_api_key_icon"></i>
                                                                    </button>
                                                                </div>
                                                                <small class="text-muted">Get from <a
                                                                        href="https://bscscan.com/apis"
                                                                        target="_blank">bscscan.com/apis</a></small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">PolygonScan API Key</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i
                                                                            class="fa fa-key"></i></span>
                                                                    <input type="password" name="polygonscan_api_key"
                                                                        id="polygonscan_api_key" class="form-control"
                                                                        value="{{ old('polygonscan_api_key', $metamaskSettings['polygonscan_api_key']) }}"
                                                                        placeholder="Enter PolygonScan API Key">
                                                                    <button type="button"
                                                                        class="btn btn-outline-secondary"
                                                                        onclick="togglePassword('polygonscan_api_key')">
                                                                        <i class="fa fa-eye"
                                                                            id="polygonscan_api_key_icon"></i>
                                                                    </button>
                                                                </div>
                                                                <small class="text-muted">Get from <a
                                                                        href="https://polygonscan.com/apis"
                                                                        target="_blank">polygonscan.com/apis</a></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Trust Wallet Tab -->
                                                <div class="tab-pane fade" id="trustwallet" role="tabpanel">
                                                    <h5 class="mb-4">Trust Wallet</h5>

                                                    <div class="alert alert-info mb-4">
                                                        <i class="fa fa-info-circle me-2"></i>
                                                        Trust Wallet uses the same wallet addresses as MetaMask. Configure
                                                        your wallet addresses in the MetaMask tab.
                                                    </div>

                                                    <h6 class="text-primary mb-3"><i class="fa fa-wallet me-2"></i>Trust
                                                        Wallet Settings</h6>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Trust Wallet Deep Link</label>
                                                                <input type="text" name="trustwallet_deeplink"
                                                                    class="form-control"
                                                                    value="{{ old('trustwallet_deeplink', $trustwalletSettings['trustwallet_deeplink'] ?? '') }}"
                                                                    placeholder="trust://...">
                                                                <small class="text-muted">Optional: Custom deep link for
                                                                    Trust Wallet payments</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Enable Trust Wallet</label>
                                                                <select name="trustwallet_enabled" class="form-control">
                                                                    <option value="1"
                                                                        {{ ($trustwalletSettings['trustwallet_enabled'] ?? '1') == '1' ? 'selected' : '' }}>
                                                                        Enabled</option>
                                                                    <option value="0"
                                                                        {{ ($trustwalletSettings['trustwallet_enabled'] ?? '1') == '0' ? 'selected' : '' }}>
                                                                        Disabled</option>
                                                                </select>
                                                                <small class="text-muted">Enable or disable Trust Wallet
                                                                    payments</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Crypto Link Tab -->
                                                <div class="tab-pane fade" id="crypto-link" role="tabpanel">
                                                    <h5 class="mb-4">Crypto Link (USDT & USDC)</h5>

                                                    <div class="alert alert-info mb-4">
                                                        <i class="fa fa-info-circle me-2"></i>
                                                        Configure direct crypto payment links for USDT and USDC tokens
                                                    </div>

                                                    <h6 class="text-primary mb-3"><i class="fa fa-coins me-2"></i>USDT
                                                        Settings</h6>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">USDT Wallet Address
                                                                    (TRC-20)</label>
                                                                <input type="text" name="usdt_address_trc20"
                                                                    class="form-control"
                                                                    value="{{ old('usdt_address_trc20', $cryptoLinkSettings['usdt_address_trc20'] ?? '') }}"
                                                                    placeholder="T...">
                                                                <small class="text-muted">TRON network (TRC-20) wallet
                                                                    address for USDT</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">USDT Wallet Address
                                                                    (ERC-20)</label>
                                                                <input type="text" name="usdt_address_erc20"
                                                                    class="form-control"
                                                                    value="{{ old('usdt_address_erc20', $cryptoLinkSettings['usdt_address_erc20'] ?? '') }}"
                                                                    placeholder="0x...">
                                                                <small class="text-muted">Ethereum network (ERC-20) wallet
                                                                    address for USDT</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">USDT Wallet Address
                                                                    (BEP-20)</label>
                                                                <input type="text" name="usdt_address_bep20"
                                                                    class="form-control"
                                                                    value="{{ old('usdt_address_bep20', $cryptoLinkSettings['usdt_address_bep20'] ?? '') }}"
                                                                    placeholder="0x...">
                                                                <small class="text-muted">BSC network (BEP-20) wallet
                                                                    address for USDT</small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <hr class="my-4">

                                                    <h6 class="text-primary mb-3"><i class="fa fa-coins me-2"></i>USDC
                                                        Settings</h6>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">USDC Wallet Address
                                                                    (ERC-20)</label>
                                                                <input type="text" name="usdc_address_erc20"
                                                                    class="form-control"
                                                                    value="{{ old('usdc_address_erc20', $cryptoLinkSettings['usdc_address_erc20'] ?? '') }}"
                                                                    placeholder="0x...">
                                                                <small class="text-muted">Ethereum network (ERC-20) wallet
                                                                    address for USDC</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">USDC Wallet Address
                                                                    (BEP-20)</label>
                                                                <input type="text" name="usdc_address_bep20"
                                                                    class="form-control"
                                                                    value="{{ old('usdc_address_bep20', $cryptoLinkSettings['usdc_address_bep20'] ?? '') }}"
                                                                    placeholder="0x...">
                                                                <small class="text-muted">BSC network (BEP-20) wallet
                                                                    address for USDC</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">USDC Wallet Address
                                                                    (Polygon)</label>
                                                                <input type="text" name="usdc_address_polygon"
                                                                    class="form-control"
                                                                    value="{{ old('usdc_address_polygon', $cryptoLinkSettings['usdc_address_polygon'] ?? '') }}"
                                                                    placeholder="0x...">
                                                                <small class="text-muted">Polygon network wallet address
                                                                    for USDC</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <!-- Form Actions -->
                                            <div class="settings-footer p-4 border-top">
                                                <button type="submit" class="btn btn-primary">
                                                    Update
                                                </button>
                                                <button type="reset" class="btn btn-secondary">
                                                    Reset
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Page Background */
        .content-wrapper {
            background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 50%, #f0f2ff 100%);
            min-height: 100vh;
        }

        /* Settings Container */
        .box {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: none;
        }

        .box-body {
            padding: 0 !important;
        }

        /* Sidebar */
        .settings-sidebar {
            background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
            border-right: 2px solid #e5e7eb;
            min-height: 600px;
            padding: 30px 0;
        }

        .settings-nav {
            padding: 0;
        }

        .settings-nav .nav-link {
            color: #4b5563;
            padding: 16px 28px;
            border-radius: 0;
            border-left: 4px solid transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 14px;
            font-weight: 600;
            font-size: 15px;
            margin: 4px 0;
            position: relative;
        }

        .settings-nav .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
        }

        .settings-nav .nav-icon {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #ffffff;
            border: 2px solid #e5e7eb;
            flex-shrink: 0;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .settings-nav .nav-link:hover {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.05) 0%, transparent 100%);
            color: #667eea;
            transform: translateX(4px);
        }

        .settings-nav .nav-link:hover .nav-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
            color: white;
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .settings-nav .nav-link.active {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.1) 0%, transparent 100%);
            color: #667eea;
            border-left-color: #667eea;
            font-weight: 700;
        }

        .settings-nav .nav-link.active::before {
            width: 4px;
        }

        .settings-nav .nav-link.active .nav-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
            color: white;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        /* Content Area */
        .settings-content {
            background-color: #ffffff;
        }

        .settings-content .tab-content {
            min-height: 500px;
            padding: 40px;
        }

        .settings-content h5 {
            color: #1f2937;
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .settings-content h5::before {
            content: '';
            width: 4px;
            height: 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }

        /* Form Elements */
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            padding: 12px 16px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .input-group-text {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border: 2px solid #e5e7eb;
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: #6b7280;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0;
        }

        .input-group .btn {
            border: 2px solid #e5e7eb;
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        /* Footer */
        .settings-footer {
            background: linear-gradient(90deg, #f8f9fa 0%, #ffffff 100%);
            display: flex;
            gap: 15px;
        }

        .settings-footer .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .settings-footer .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.4);
        }

        .settings-footer .btn-secondary {
            background: #f3f4f6;
            border: 2px solid #e5e7eb;
            color: #4b5563;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .settings-sidebar {
                border-right: none;
                border-bottom: 2px solid #e5e7eb;
                min-height: auto;
            }

            .settings-nav .nav-link {
                border-left: none;
                border-bottom: 3px solid transparent;
            }

            .settings-nav .nav-link.active {
                border-left: none;
                border-bottom-color: #667eea;
            }

            .settings-content .tab-content {
                padding: 25px 20px;
            }

            .settings-footer {
                flex-direction: column;
            }

            .settings-footer .btn {
                width: 100%;
            }
        }

        /* Settings sidebar icons */
        .settings-sidebar svg,
        .settings-nav svg[data-feather],
        .settings-nav i[data-feather],
        .settings-nav svg,
        .nav-icon svg {
            width: 18px !important;
            height: 18px !important;
            max-width: 18px !important;
            max-height: 18px !important;
            min-width: 18px !important;
            min-height: 18px !important;
            display: inline-block !important;
            vertical-align: middle !important;
            stroke: #6b7280 !important;
            stroke-width: 2 !important;
        }

        .settings-nav .nav-link:hover .nav-icon svg,
        .settings-nav .nav-link.active .nav-icon svg {
            stroke: #ffffff !important;
        }

        .settings-nav .nav-icon i[data-feather],
        .settings-nav .nav-icon svg {
            opacity: 1 !important;
            visibility: visible !important;
        }

        .nav-icon {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '_icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Initialize Feather icons
        $(document).ready(function() {
            if (typeof feather !== 'undefined') {
                feather.replace();

                // Fix icon sizes in nav
                setTimeout(function() {
                    $('.settings-nav svg').each(function() {
                        $(this).attr('width', '18');
                        $(this).attr('height', '18');
                        $(this).css({
                            'width': '18px',
                            'height': '18px'
                        });
                    });
                }, 100);
            }
        });

        // Re-initialize icons when tab changes
        $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
@endpush

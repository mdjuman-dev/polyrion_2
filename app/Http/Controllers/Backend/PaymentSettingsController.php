<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PaymentSettingsController extends Controller
{
    public function index()
    {
        $settings = GlobalSetting::getAllSettings();

        // Binance Pay Settings
        $binanceSettings = [
            'binance_api_key' => $settings['binance_api_key'] ?? '',
            'binance_secret_key' => $settings['binance_secret_key'] ?? '',
            'binance_base_url' => $settings['binance_base_url'] ?? 'https://bpay.binanceapi.com',
        ];

        // MetaMask/Crypto Settings
        $metamaskSettings = [
            'merchant_address' => $settings['merchant_address'] ?? '',
            'merchant_address_bsc' => $settings['merchant_address_bsc'] ?? '',
            'merchant_address_polygon' => $settings['merchant_address_polygon'] ?? '',
            'chain_network' => $settings['chain_network'] ?? 'ethereum',
            'etherscan_api_key' => $settings['etherscan_api_key'] ?? '',
            'bscscan_api_key' => $settings['bscscan_api_key'] ?? '',
            'polygonscan_api_key' => $settings['polygonscan_api_key'] ?? '',
        ];

        // Trust Wallet Settings
        $trustwalletSettings = [
            'trustwallet_deeplink' => $settings['trustwallet_deeplink'] ?? '',
            'trustwallet_enabled' => $settings['trustwallet_enabled'] ?? '1',
        ];

        // Crypto Link Settings (USDT & USDC)
        $cryptoLinkSettings = [
            'usdt_address_trc20' => $settings['usdt_address_trc20'] ?? '',
            'usdt_address_erc20' => $settings['usdt_address_erc20'] ?? '',
            'usdt_address_bep20' => $settings['usdt_address_bep20'] ?? '',
            'usdc_address_erc20' => $settings['usdc_address_erc20'] ?? '',
            'usdc_address_bep20' => $settings['usdc_address_bep20'] ?? '',
            'usdc_address_polygon' => $settings['usdc_address_polygon'] ?? '',
        ];

        return view('backend.settings.payment_settings', compact(
            'binanceSettings',
            'metamaskSettings',
            'trustwalletSettings',
            'cryptoLinkSettings'
        ));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Binance Pay
            'binance_api_key' => 'nullable|string|max:255',
            'binance_secret_key' => 'nullable|string|max:255',
            'binance_base_url' => 'nullable|url|max:255',
            // MetaMask/Crypto
            'merchant_address' => 'nullable|string|max:255',
            'merchant_address_bsc' => 'nullable|string|max:255',
            'merchant_address_polygon' => 'nullable|string|max:255',
            'chain_network' => 'nullable|string|in:ethereum,bsc,polygon',
            'etherscan_api_key' => 'nullable|string|max:255',
            'bscscan_api_key' => 'nullable|string|max:255',
            'polygonscan_api_key' => 'nullable|string|max:255',
            // Trust Wallet
            'trustwallet_deeplink' => 'nullable|string|max:500',
            'trustwallet_enabled' => 'nullable|string|in:0,1',
            // Crypto Link (USDT & USDC)
            'usdt_address_trc20' => 'nullable|string|max:255',
            'usdt_address_erc20' => 'nullable|string|max:255',
            'usdt_address_bep20' => 'nullable|string|max:255',
            'usdc_address_erc20' => 'nullable|string|max:255',
            'usdc_address_bep20' => 'nullable|string|max:255',
            'usdc_address_polygon' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $settingsToUpdate = [
            // Binance Pay
            'binance_api_key',
            'binance_secret_key',
            'binance_base_url',
            // MetaMask/Crypto
            'merchant_address',
            'merchant_address_bsc',
            'merchant_address_polygon',
            'chain_network',
            'etherscan_api_key',
            'bscscan_api_key',
            'polygonscan_api_key',
            // Trust Wallet
            'trustwallet_deeplink',
            'trustwallet_enabled',
            // Crypto Link (USDT & USDC)
            'usdt_address_trc20',
            'usdt_address_erc20',
            'usdt_address_bep20',
            'usdc_address_erc20',
            'usdc_address_bep20',
            'usdc_address_polygon',
        ];

        foreach ($settingsToUpdate as $key) {
            if ($request->has($key)) {
                $value = $request->input($key);
                // Convert empty strings to null
                if ($value === null || (is_string($value) && trim($value) === '')) {
                    $value = null;
                }
                GlobalSetting::setValue($key, $value);
            }
        }

        Log::info('Payment settings updated by admin', [
            'admin_id' => auth()->guard('admin')->id(),
        ]);

        return redirect()->route('admin.payment.settings')
            ->with('success', 'Payment settings updated successfully!');
    }
}


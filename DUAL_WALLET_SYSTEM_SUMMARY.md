# Dual Wallet System Implementation Summary

## Overview
Implemented a dual wallet system where users have two separate wallets:
1. **Main Wallet** - For trading and deposits
2. **Earning Wallet** - For trade winnings, referral commissions, and other earnings

## Changes Made

### 1. Database Changes
- **Migration**: Added `wallet_type` column to `wallets` table
- **Migration**: Created migration to update existing wallets to `main` type
- **Migration**: Added `transfer_in` and `transfer_out` transaction types

### 2. Model Updates
- **Wallet Model**: Added `TYPE_MAIN` and `TYPE_EARNING` constants
- **User Model**: Added relationships:
  - `mainWallet()` - Returns main wallet
  - `earningWallet()` - Returns earning wallet
  - `wallets()` - Returns all wallets
  - Updated `wallet()` to return main wallet for backward compatibility

### 3. Service Updates

#### TradeService
- Trade creation: Deducts from **main wallet**
- Trade settlement (WON): Adds payout to **earning wallet**
- Trade settlement (LOST): No payout

#### SettlementService
- Market settlement: Adds payouts to **earning wallet**

#### ReferralService
- Referral commissions: Added to **earning wallet**
- Trade referral commissions: Added to **earning wallet**

### 4. Controller Updates

#### Deposit Controllers
- **DepositController**: Deposits go to **main wallet**
- **BinancePayController**: Deposits go to **main wallet**
- **MetaMaskController**: Deposits go to **main wallet**
- **UserController** (test deposit): Goes to **main wallet**

#### Withdrawal Controllers
- **WithdrawalController**: Withdrawals from **main wallet**
- Withdrawal rejection: Refunds to **main wallet**

#### Profile Controller
- Loads both wallets
- Passes both balances to view

### 5. Frontend Updates

#### Profile Page
- Shows **Main Wallet** card with:
  - Balance + Portfolio value
  - Deposit and Withdraw buttons
  - Description: "For trading & deposits"
- Shows **Earning Wallet** card with:
  - Earning balance
  - Description: "Trade wins, referrals & earnings"
  - Transfer to Main Wallet button

#### Transfer Functionality
- Added `WalletController::transferEarningToMain()` method
- Added route: `POST /wallet/transfer-earning-to-main`
- JavaScript function with SweetAlert confirmation dialog
- Transfers balance from earning to main wallet

#### Header/Layout
- Updated to show main wallet balance
- Mobile navigation shows main wallet balance

### 6. Livewire Components
- **deposit-request**: Uses main wallet
- **withdrawal-request**: Uses main wallet

### 7. View Composer
- **AppServiceProvider**: Loads main wallet for authenticated users
- Available as `$authUserMainWallet` in all views

## Wallet Flow

### Main Wallet
- ✅ User deposits → Added to main wallet
- ✅ User places trade → Deducted from main wallet
- ✅ User withdraws → Deducted from main wallet
- ✅ Withdrawal rejected → Refunded to main wallet

### Earning Wallet
- ✅ Trade wins → Added to earning wallet
- ✅ Referral commissions → Added to earning wallet
- ✅ Trade referral commissions → Added to earning wallet
- ✅ User can transfer → From earning to main wallet

## Migration Steps

1. Run migration to add `wallet_type` column:
   ```bash
   php artisan migrate
   ```

2. Existing wallets will be automatically set to `main` type

3. New wallets will be created with appropriate type:
   - Main wallet: `wallet_type = 'main'`
   - Earning wallet: `wallet_type = 'earning'`

## API Endpoints

### Transfer Earning to Main
```
POST /wallet/transfer-earning-to-main
Body: { "amount": 100.00 }
Response: {
    "success": true,
    "message": "Balance transferred successfully!",
    "earning_balance": "50.00",
    "main_balance": "150.00"
}
```

## UI Features

1. **Main Wallet Card** (Blue theme)
   - Shows main balance
   - Shows balance + portfolio value
   - Deposit and Withdraw buttons

2. **Earning Wallet Card** (Green theme)
   - Shows earning balance
   - Transfer button with confirmation dialog
   - Shows amount input with max validation

## Backward Compatibility

- `User::wallet` relationship still works (returns main wallet)
- Existing code using `$user->wallet` will continue to work
- All existing wallets migrated to `main` type automatically

## Files Modified

1. `database/migrations/2026_01_12_124711_add_wallet_type_to_wallets_table.php`
2. `database/migrations/2026_01_13_000000_migrate_existing_wallets_to_main_type.php`
3. `database/migrations/2026_01_13_000001_add_transfer_types_to_wallet_transactions_enum.php`
4. `app/Models/Wallet.php`
5. `app/Models/User.php`
6. `app/Services/TradeService.php`
7. `app/Services/SettlementService.php`
8. `app/Services/ReferralService.php`
9. `app/Jobs/ProcessTradeCommission.php`
10. `app/Http/Controllers/Backend/DepositController.php`
11. `app/Http/Controllers/Backend/WithdrawalController.php`
12. `app/Http/Controllers/Backend/UserController.php`
13. `app/Http/Controllers/Backend/BinancePayController.php`
14. `app/Http/Controllers/Backend/MetaMaskController.php`
15. `app/Http/Controllers/Frontend/ProfileController.php`
16. `app/Http/Controllers/Frontend/WithdrawalController.php`
17. `app/Http/Controllers/Frontend/TradeController.php`
18. `app/Http/Controllers/Frontend/MarketController.php`
19. `app/Http/Controllers/Frontend/WalletController.php` (new)
20. `app/Providers/AppServiceProvider.php`
21. `resources/views/frontend/profile.blade.php`
22. `resources/views/frontend/layout/frontend.blade.php`
23. `resources/views/livewire/deposit-request.blade.php`
24. `resources/views/livewire/withdrawal-request.blade.php`
25. `routes/frontend.php`
26. `app/Console/Commands/TestTradeSettlement.php`

## Testing Checklist

- [ ] Run migrations
- [ ] Test deposit → Should go to main wallet
- [ ] Test trade placement → Should deduct from main wallet
- [ ] Test trade win → Should add to earning wallet
- [ ] Test referral commission → Should add to earning wallet
- [ ] Test transfer earning to main → Should work correctly
- [ ] Test withdrawal → Should deduct from main wallet
- [ ] Test withdrawal rejection → Should refund to main wallet
- [ ] Verify UI shows both wallets correctly
- [ ] Verify header shows main wallet balance

## Notes

- Users can transfer balance from earning wallet to main wallet anytime
- Transfer requires confirmation dialog
- Transfer creates transaction records for both wallets
- All wallet operations are atomic (use transactions)
- Wallet types are enforced at database level (index on user_id + wallet_type)


# Test Deposit Feature

## Overview
Test deposit feature allows admins to quickly add test funds to any user's wallet for testing purposes.

## Location
- **Admin Panel**: User Details Page (`/admin/users/{id}`)
- **Button**: "Add Test Deposit" button in Financial Summary section

## How to Use

### Step 1: Navigate to User Details
1. Go to Admin Panel → Users
2. Click on any user to view their details
3. Scroll to "Financial Summary" section

### Step 2: Add Test Deposit
1. Click the **"Add Test Deposit"** button (yellow button with flask icon)
2. Enter the amount (minimum: $0.01, maximum: $100,000)
3. Optionally add a note
4. Click **"Add Deposit"**

### Step 3: Confirmation
- Success message will show:
  - Amount added
  - Previous balance
  - New balance
- Page will automatically refresh to show updated balance

## Features

### ✅ What It Does:
- Adds funds directly to user's wallet
- Creates a deposit record (marked as "test")
- Creates a wallet transaction record
- Updates wallet balance immediately
- Logs the action with admin details

### 🔒 Security:
- Only admins can access
- Requires authentication
- Validates amount (min/max limits)
- Uses database transactions (rollback on error)
- Logs all actions

### 📊 What Gets Created:
1. **Deposit Record**:
   - Status: `completed`
   - Payment Method: `test`
   - Contains admin info in `response_data`

2. **Wallet Transaction**:
   - Type: `deposit`
   - Records balance before/after
   - Includes metadata (admin info, note)

3. **Wallet Balance**:
   - Updated immediately
   - Balance increases by deposit amount

## API Endpoint

**Route**: `POST /admin/users/{id}/test-deposit`

**Request Body**:
```json
{
  "amount": 100.00,
  "note": "Test deposit for trading"
}
```

**Response**:
```json
{
  "success": true,
  "message": "Test deposit added successfully.",
  "data": {
    "amount": "100.00",
    "balance_before": "50.00",
    "balance_after": "150.00",
    "deposit_id": 123
  }
}
```

## Validation Rules

- **Amount**: 
  - Required
  - Numeric
  - Minimum: $0.01
  - Maximum: $100,000

- **Note**: 
  - Optional
  - String
  - Maximum: 500 characters

## Testing Scenarios

### Test 1: Basic Deposit
1. Add $100 test deposit
2. Verify wallet balance increases
3. Check deposit record created
4. Check transaction record created

### Test 2: Multiple Deposits
1. Add $50 deposit
2. Add $25 deposit
3. Verify total balance = $75

### Test 3: Large Amount
1. Add $10,000 deposit
2. Verify balance updates correctly

### Test 4: Small Amount
1. Add $0.01 deposit
2. Verify minimum amount works

### Test 5: With Note
1. Add deposit with note "Testing trading system"
2. Verify note saved in deposit record

## Logs

All test deposits are logged with:
- User ID and email
- Admin ID and name
- Amount
- Balance before/after
- Note (if provided)
- Timestamp

**Log Location**: `storage/logs/laravel.log`

**Log Entry Example**:
```
[2025-12-19 18:00:00] local.INFO: Test deposit added {"user_id":1,"user_email":"user@example.com","admin_id":1,"admin_name":"Admin User","amount":100,"balance_before":50,"balance_after":150,"note":"Test deposit"}
```

## Notes

- ⚠️ **For Testing Only**: This feature is designed for testing purposes
- 🔍 **Trackable**: All test deposits are marked and can be identified
- 📝 **Audit Trail**: Complete audit trail with admin info
- 💰 **Real Funds**: Adds real funds to wallet (use carefully in production)

## Troubleshooting

### Issue: Button not showing
- **Solution**: Make sure you're logged in as admin
- **Check**: User details page loads correctly

### Issue: Deposit fails
- **Check**: Amount is within limits (0.01 - 100000)
- **Check**: User exists
- **Check**: Database connection
- **Check**: Laravel logs for error details

### Issue: Balance not updating
- **Check**: Page refresh after deposit
- **Check**: Database transaction completed
- **Check**: Wallet record exists for user

## Related Files

- **Controller**: `app/Http/Controllers/Backend/UserController.php`
- **Route**: `routes/backend.php`
- **View**: `resources/views/backend/users/show.blade.php`
- **Model**: `app/Models/Deposit.php`
- **Model**: `app/Models/Wallet.php`
- **Model**: `app/Models/WalletTransaction.php`

---

**Ready to use!** 🎉

Go to Admin Panel → Users → Select User → Click "Add Test Deposit"


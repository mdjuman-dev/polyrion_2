# Binance Pay সম্পূর্ণ Setup Guide

## প্রয়োজনীয় Credentials

Binance Pay integration এর জন্য আপনার `.env` file এ নিম্নলিখিত credentials configure করতে হবে:

### 1. Binance Pay API Credentials

Binance Pay merchant account থেকে API credentials সংগ্রহ করতে হবে:

#### **API Key এবং Secret Key পাওয়ার জন্য:**

1. **Binance Pay Merchant Dashboard** এ login করুন
   - URL: https://merchant.binance.com/
   - যদি account না থাকে, তাহলে Binance Pay merchant account তৈরি করুন

2. **API Management** section এ যান
   - Dashboard → Settings → API Management

3. **API Key তৈরি করুন**
   - "Create API Key" button click করুন
   - API Key এবং Secret Key copy করুন
   - **⚠️ IMPORTANT:** Secret Key শুধুমাত্র একবার দেখানো হবে, তাই safe জায়গায় save করুন

4. **IP Whitelist Setup**
   - Settings → API Management → IP Whitelist
   - আপনার server এর outgoing IP address add করুন
   - IP detect করতে: `/binance/diagnostic` route visit করুন (login করতে হবে)

### 2. .env File Configuration

`.env` file এ নিম্নলিখিত variables add করুন:

```env
# Binance Pay Configuration
BINANCE_API_KEY=your_api_key_here
BINANCE_SECRET_KEY=your_secret_key_here
BINANCE_BASE_URL=https://bpay.binanceapi.com

# Optional: If your server has a specific outgoing IP
SERVER_OUTGOING_IP=your_server_ip_here
```

### 3. Database Settings (Alternative)

`.env` file এর পরিবর্তে Admin Panel থেকে settings করতে পারেন:

1. Admin Panel → Settings → Payment Settings
2. Binance Pay section এ:
   - API Key
   - Secret Key
   - Base URL (default: https://bpay.binanceapi.com)

## Webhook Configuration

### Webhook URL Setup

Binance Pay merchant dashboard এ webhook URL configure করতে হবে:

1. **Binance Pay Merchant Dashboard** → Settings → Webhook Configuration
2. **Webhook URL** add করুন:
   ```
   https://yourdomain.com/binance/webhook
   ```
3. **Webhook Events** select করুন:
   - ✅ PAY (Payment status updates)

### Webhook Testing

Webhook test করার জন্য:

1. Test payment করুন
2. Laravel logs check করুন: `storage/logs/laravel.log`
3. Webhook received হলে log এ দেখাবে:
   ```
   Binance Pay webhook received
   ```

## Server IP Whitelist

### IP Address Detect করা

আপনার server এর outgoing IP address জানতে:

1. Browser এ login করুন
2. Visit করুন: `https://yourdomain.com/binance/diagnostic`
3. Response এ দেখাবে:
   - `detected_outgoing_ip`: আপনার server IP
   - `binance_reported_ip`: Binance যা IP দেখছে (যদি test request করা হয়)

### IP Whitelist এ Add করা

1. Binance Pay Merchant Dashboard → Settings → API Management → IP Whitelist
2. Detect করা IP address add করুন
3. **Note:** IP whitelist changes 5-10 minutes সময় নিতে পারে

## Complete Setup Steps

### Step 1: Binance Pay Merchant Account তৈরি করুন
- https://merchant.binance.com/ এ account তৈরি করুন
- Account verification complete করুন

### Step 2: API Credentials সংগ্রহ করুন
- Dashboard → Settings → API Management
- API Key এবং Secret Key তৈরি করুন
- Credentials copy করুন

### Step 3: .env File Configure করুন
```env
BINANCE_API_KEY=your_api_key_here
BINANCE_SECRET_KEY=your_secret_key_here
BINANCE_BASE_URL=https://bpay.binanceapi.com
```

### Step 4: Server IP Whitelist করুন
- `/binance/diagnostic` visit করে IP detect করুন
- Binance Pay dashboard এ IP whitelist করুন

### Step 5: Webhook URL Setup করুন
- Binance Pay dashboard → Webhook Configuration
- Webhook URL: `https://yourdomain.com/binance/webhook`
- Event: PAY

### Step 6: Test Payment করুন
- Frontend থেকে deposit করুন
- Binance Pay checkout page open হবে
- Payment complete করুন
- Return page এ balance update দেখবেন

## Testing

### Test Payment Process

1. **Frontend Deposit:**
   - Login করুন
   - Wallet → Deposit
   - Binance Pay select করুন
   - Amount enter করুন (minimum $10)
   - Pay button click করুন

2. **Binance Pay Checkout:**
   - Binance Pay checkout page open হবে
   - Payment method select করুন
   - Payment complete করুন

3. **Return Page:**
   - Payment successful হলে return page এ redirect হবে
   - Balance automatically update হবে
   - Wallet transaction create হবে

### Manual Verification (Backup)

যদি automatic processing না হয়:

1. Wallet → Deposit History
2. Pending deposit select করুন
3. "Verify Payment" button click করুন
4. Query code (merchantTradeNo/prepayId) enter করুন
5. Amount enter করুন
6. Verify করুন

## Troubleshooting

### Error: "API authentication failed"

**সমাধান:**
1. API Key এবং Secret Key সঠিক আছে কিনা check করুন
2. Server IP whitelist করা আছে কিনা verify করুন
3. `/binance/diagnostic` visit করে IP check করুন
4. Binance Pay dashboard এ IP add করুন
5. 5-10 minutes wait করুন (IP whitelist update হতে সময় লাগে)

### Error: "Request blocked by CloudFront"

**সমাধান:**
1. Base URL সঠিক আছে কিনা check করুন: `https://bpay.binanceapi.com`
2. API credentials verify করুন
3. Server IP whitelist করুন

### Error: "Invalid signature"

**সমাধান:**
1. Secret Key সঠিক আছে কিনা verify করুন
2. .env file এ extra spaces/characters নেই কিনা check করুন
3. Laravel cache clear করুন:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

### Webhook Not Working

**সমাধান:**
1. Webhook URL accessible আছে কিনা check করুন:
   - `https://yourdomain.com/binance/webhook` visit করুন
   - 405 Method Not Allowed error দেখাবে (এটা normal, POST request expected)
2. Binance Pay dashboard এ webhook URL সঠিক আছে কিনা verify করুন
3. Laravel logs check করুন: `storage/logs/laravel.log`
4. Webhook signature verification check করুন

### Payment Not Processing Automatically

**সমাধান:**
1. Return page visit করুন (payment complete হওয়ার পর)
2. Manual verification ব্যবহার করুন
3. Laravel logs check করুন errors এর জন্য
4. Deposit status check করুন (Admin Panel → Deposits)

## Payment Flow

### Complete Payment Flow:

1. **User initiates deposit:**
   - Frontend → Deposit → Binance Pay → Amount → Pay

2. **Backend creates order:**
   - Deposit record create হয় (status: pending)
   - Binance Pay API call হয়
   - Checkout URL generate হয়

3. **User redirected to Binance Pay:**
   - Checkout page open হয়
   - User payment complete করে

4. **Payment processing:**
   - **Option A:** Webhook automatically process করে (recommended)
   - **Option B:** Return page automatically process করে
   - **Option C:** Manual verification (backup)

5. **Balance update:**
   - Wallet balance update হয়
   - Wallet transaction create হয়
   - Deposit status: completed

## Security Notes

⚠️ **IMPORTANT Security Tips:**

1. **Never commit .env file** to version control
2. **Keep API credentials secure** - don't share
3. **Use HTTPS** for webhook URL (required by Binance Pay)
4. **IP Whitelist** - only whitelist your server IPs
5. **Webhook Signature Verification** - always enabled (automatic)
6. **Regular Security Updates** - keep Laravel updated

## Support & Resources

### Binance Pay Documentation
- Official Docs: https://developers.binance.com/docs/binance-pay
- Merchant Dashboard: https://merchant.binance.com/

### Laravel Logs
- Location: `storage/logs/laravel.log`
- Search for: "Binance Pay" to see all related logs

### Diagnostic Endpoint
- URL: `/binance/diagnostic` (requires login)
- Shows: IP addresses, configuration status

## Cost Information

- **Setup:** FREE
- **API Access:** FREE
- **Transaction Fees:** Binance Pay fees apply (check Binance Pay pricing)
- **No Monthly Fees:** Pay per transaction only

## Features

✅ **Automatic Payment Processing**
- Webhook-based automatic processing
- Return page fallback processing
- Manual verification backup

✅ **Secure Payment Flow**
- Signature verification
- IP whitelist protection
- HTTPS required

✅ **User-Friendly**
- Seamless checkout experience
- Automatic balance update
- Transaction history

✅ **Admin Features**
- Deposit management
- Payment status tracking
- Manual processing option

## Next Steps

1. ✅ Complete Binance Pay merchant account setup
2. ✅ Configure API credentials in .env
3. ✅ Whitelist server IP address
4. ✅ Setup webhook URL
5. ✅ Test with small amount ($10 minimum)
6. ✅ Verify automatic processing works
7. ✅ Monitor logs for any issues

---

**Note:** যদি কোনো সমস্যা হয়, Laravel logs (`storage/logs/laravel.log`) check করুন এবং error messages দেখুন। Most common issue হল IP whitelist না করা।


# Google Login Fix

## 🔍 Common Issues & Solutions

### Issue 1: Missing OAuth Credentials

**Error:** "Google login is not configured"

**Solution:**
1. Go to Google Cloud Console: https://console.cloud.google.com/
2. Create OAuth 2.0 credentials
3. Add credentials to `.env`:
   ```env
   GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=your-client-secret
   GOOGLE_CLIENT_REDIRECT=https://polyrion.com/auth/google/callback
   ```
4. Or add via Admin Panel → Settings → Google Login Settings

### Issue 2: Redirect URI Mismatch

**Error:** "redirect_uri_mismatch"

**Solution:**
1. In Google Cloud Console → OAuth 2.0 Client IDs
2. Add Authorized redirect URIs:
   - `https://polyrion.com/auth/google/callback`
   - `http://localhost:8000/auth/google/callback` (for local)
3. Make sure it matches exactly (including http/https)

### Issue 3: Missing Scopes

**Error:** "insufficient permissions"

**Solution:**
The controller now requests required scopes:
- `openid`
- `profile`
- `email`

### Issue 4: State Mismatch

**Error:** "InvalidStateException"

**Solution:**
- Clear browser cookies
- Try again
- Make sure session is working

## 🚀 Setup Instructions

### Step 1: Create Google OAuth Credentials

1. Go to: https://console.cloud.google.com/
2. Create a new project or select existing
3. Enable Google+ API
4. Go to: APIs & Services → Credentials
5. Create OAuth 2.0 Client ID
6. Application type: Web application
7. Authorized redirect URIs:
   - `https://polyrion.com/auth/google/callback`
   - `http://localhost:8000/auth/google/callback` (for local)

### Step 2: Add Credentials

**Option A: Via .env**
```env
GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_CLIENT_REDIRECT=https://polyrion.com/auth/google/callback
```

**Option B: Via Admin Panel**
1. Login as admin
2. Go to: Settings → Google Login Settings
3. Enter Client ID, Client Secret, and Redirect URI
4. Save

### Step 3: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### Step 4: Test

1. Go to login page
2. Click "Continue with Google"
3. Should redirect to Google login
4. After login, should redirect back to your site

## 🔧 Code Changes Made

### GoogleController.php

**Improvements:**
1. ✅ Uses GlobalSetting for credentials (database) or config (fallback)
2. ✅ Better error handling with specific exception types
3. ✅ Validates credentials before redirect
4. ✅ Added required OAuth scopes
5. ✅ Better logging for debugging

**Key Changes:**
- Checks if credentials exist before redirect
- Uses GlobalSetting::getValue() for database-stored credentials
- Handles InvalidStateException separately
- Handles API errors separately
- Better error messages for users

## 📋 Verification Checklist

- [ ] Google OAuth credentials created
- [ ] Client ID added to .env or Admin Panel
- [ ] Client Secret added to .env or Admin Panel
- [ ] Redirect URI matches exactly
- [ ] Redirect URI added to Google Console
- [ ] Config cache cleared
- [ ] Test login works

## 🐛 Debugging

### Check Logs

```bash
tail -f storage/logs/laravel.log | grep -i google
```

### Test Credentials

```bash
php artisan tinker
>>> config('services.google');
```

### Check Routes

```bash
php artisan route:list | grep google
```

Should show:
- `GET auth/google` → `google.redirect`
- `GET auth/google/callback` → `google.callback`

## ✅ Expected Behavior

1. User clicks "Continue with Google"
2. Redirects to Google login page
3. User authorizes
4. Google redirects back to `/auth/google/callback`
5. User is logged in and redirected to profile

---

**Last Updated:** 2025-12-20


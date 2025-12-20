# Livewire 404 Error Fix

## 🔍 Error

**Error:**

```
GET https://polyrion.com/livewire/livewire.js?id=f084fdfb
net::ERR_ABORTED 404 (Not Found)
```

## ✅ Solutions

### Solution 1: Publish Livewire Assets (Most Common)

**On Server:**

```bash
php artisan livewire:publish --assets
```

This will copy Livewire assets to `public/vendor/livewire/`

### Solution 2: Clear Route Cache

**On Server:**

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Solution 3: Check File Permissions

**On Server:**

```bash
# Make sure public directory is accessible
chmod -R 755 public
chmod -R 755 public/vendor
chmod -R 755 public/vendor/livewire
```

### Solution 4: Verify Assets Exist

**On Server:**

```bash
# Check if assets exist
ls -la public/vendor/livewire/

# Should see:
# - livewire.js
# - livewire.min.js
# - livewire.js.map
```

### Solution 5: Use CDN (Alternative)

If assets still don't work, you can use Livewire CDN:

**In `resources/views/frontend/layout/frontend.blade.php`:**

Replace:

```blade
@livewireScripts
```

With:

```blade
@livewireScripts
<script src="https://cdn.jsdelivr.net/gh/livewire/livewire@v3.x.x/dist/livewire.min.js"></script>
```

Or use the latest version:

```blade
<script src="https://unpkg.com/livewire@3/dist/livewire.min.js"></script>
```

### Solution 6: Check APP_URL

**In `.env` on server:**

```env
APP_URL=https://polyrion.com
```

Then clear config:

```bash
php artisan config:clear
```

### Solution 7: Force Asset Publishing

**On Server:**

```bash
# Remove old assets
rm -rf public/vendor/livewire

# Publish again
php artisan livewire:publish --assets --force
```

## 🚀 Quick Fix Steps

### Step 1: SSH to Server

```bash
ssh username@your-server
```

### Step 2: Navigate to Project

```bash
cd /path/to/your/project
```

### Step 3: Publish Assets

```bash
php artisan livewire:publish --assets
```

### Step 4: Clear All Caches

```bash
php artisan optimize:clear
```

### Step 5: Set Permissions

```bash
chmod -R 755 public/vendor/livewire
```

### Step 6: Verify

```bash
ls -la public/vendor/livewire/
```

## 🔍 Verification

### Check Assets Exist:

```bash
ls -la public/vendor/livewire/
```

### Test in Browser:

1. Open browser DevTools (F12)
2. Go to Network tab
3. Refresh page
4. Check if `/livewire/livewire.js` loads (should be 200, not 404)

### Check Route:

```bash
php artisan route:list | grep livewire
```

Should show:

```
livewire/livewire.js
livewire/livewire.min.js.map
livewire/update
```

## 🐛 Common Issues

### Issue 1: Assets Not Published

**Symptom:** 404 error for `/livewire/livewire.js`

**Fix:**

```bash
php artisan livewire:publish --assets
```

### Issue 2: Route Cache

**Symptom:** Assets exist but route returns 404

**Fix:**

```bash
php artisan route:clear
```

### Issue 3: File Permissions

**Symptom:** 403 Forbidden instead of 404

**Fix:**

```bash
chmod -R 755 public/vendor/livewire
```

### Issue 4: Wrong APP_URL

**Symptom:** Assets load from wrong domain

**Fix:**

```env
APP_URL=https://polyrion.com
```

Then:

```bash
php artisan config:clear
```

## 📋 Server Checklist

-   [ ] Livewire assets published (`php artisan livewire:publish --assets`)
-   [ ] Assets exist in `public/vendor/livewire/`
-   [ ] File permissions set (755)
-   [ ] Route cache cleared
-   [ ] Config cache cleared
-   [ ] APP_URL set correctly
-   [ ] Web server can access `public/vendor/` directory

## 🔧 Alternative: Use CDN

If server assets don't work, use CDN:

**Replace in layout files:**

```blade
@livewireScripts
```

**With:**

```blade
<script src="https://unpkg.com/livewire@3/dist/livewire.min.js"></script>
```

**Note:** This bypasses local assets and uses CDN instead.

---

**Last Updated:** 2025-12-20

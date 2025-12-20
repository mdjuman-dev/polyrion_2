# Database Connection Refused - Quick Fix

## 🔍 Error

```
SQLSTATE[HY000] [2002] Connection refused
Connection: mysql, SQL: select * from `tags` order by `label` asc
```

## ✅ Server Fix (90% Cases)

### Step 1: Update .env on Server

```env
# Change this:
DB_HOST=127.0.0.1

# To this:
DB_HOST=localhost
```

### Step 2: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### Step 3: Test

```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

## 🔧 Why This Works

- `127.0.0.1` = TCP/IP connection (may be blocked)
- `localhost` = Unix socket (faster, more reliable on Linux)

## ✅ Code Changes

**Error Handling Added:**
- `TagFilters` component - graceful fallback
- `TaggedEventsGrid` component - error handling
- `HomeController@eventsByTag` - error handling

**Result:** Page won't crash if database connection fails.

---

**Quick Fix:** Server `.env` এ `DB_HOST=localhost` করুন


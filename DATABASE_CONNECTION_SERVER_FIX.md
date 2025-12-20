# Database Connection Error Fix - Server

## 🔍 Error

**Error:**

```
SQLSTATE[HY000] [2002] Connection refused
Connection: mysql, SQL: select * from `tags` order by `label` asc
```

## ✅ Quick Fix (Server)

### Step 1: Update .env on Server

**Change:**

```env
DB_HOST=127.0.0.1
```

**To:**

```env
DB_HOST=localhost
```

### Step 2: Clear Config Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### Step 3: Test Connection

```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

## 🔧 Why This Happens

**On Linux servers:**

-   `127.0.0.1` uses TCP/IP connection
-   `localhost` uses Unix socket (faster, more reliable)
-   Some servers don't accept TCP connections on 127.0.0.1

## 🐛 Alternative Solutions

### Solution 1: Check MySQL Service

```bash
sudo systemctl status mysql
sudo systemctl start mysql  # If stopped
```

### Solution 2: Check MySQL Socket

```bash
mysql_config --socket
# or
find /var -name "*.sock" 2>/dev/null | grep mysql
```

Then in `.env`:

```env
DB_HOST=localhost
DB_SOCKET=/var/run/mysqld/mysqld.sock
```

### Solution 3: Check Firewall

```bash
sudo netstat -tuln | grep 3306
```

## ✅ Code Changes Made

### Error Handling Added:

1. **`app/Livewire/TagFilters.php`**

    - Added try-catch for database queries
    - Returns empty collection on error
    - Prevents page crash

2. **`app/Livewire/TaggedEventsGrid.php`**
    - Added error handling for tag queries
    - Graceful fallback on connection failure

## 📋 Server Checklist

-   [ ] DB_HOST changed to `localhost` in `.env`
-   [ ] Config cache cleared
-   [ ] MySQL service running
-   [ ] Database credentials correct
-   [ ] Test connection works

---

**Most Common Fix:** Change `DB_HOST=127.0.0.1` to `DB_HOST=localhost` on server

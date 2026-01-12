# Production Deployment Checklist

## ✅ Pre-Deployment Checks

### 1. Environment Configuration (.env)
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Security
APP_KEY=base64:... (must be set)
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict

# Database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_secure_password

# Cache
CACHE_STORE=redis (recommended) or database
CACHE_DRIVER=redis (recommended)

# Queue
QUEUE_CONNECTION=database (or redis)

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Logging
LOG_CHANNEL=daily
LOG_LEVEL=error (production) or info (for debugging)
```

### 2. Security Settings ✅
- [x] APP_DEBUG=false
- [x] CSRF Protection enabled
- [x] Rate limiting configured
- [x] Cloudflare reCAPTCHA integrated
- [x] Password hashing (bcrypt)
- [x] Session security configured

### 3. Database
```bash
# Run migrations
php artisan migrate --force

# Seed if needed (only first time)
# php artisan db:seed --force
```

### 4. Optimization Commands
```bash
# Clear and cache config
php artisan config:clear
php artisan config:cache

# Clear and cache routes
php artisan route:clear
php artisan route:cache

# Clear and cache views
php artisan view:clear
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Cache application
php artisan optimize
```

### 5. Queue Workers (Required for Trade Commissions)
```bash
# Start queue worker
php artisan queue:work --tries=3 --timeout=90

# Or use Supervisor (recommended for production)
# See supervisor configuration below
```

### 6. Cron Jobs (Required for Auto-Settlement)
```bash
# Add to crontab (crontab -e)
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 7. File Permissions
```bash
# Storage and cache directories
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# If using nginx
chown -R nginx:nginx storage bootstrap/cache
```

### 8. SSL/HTTPS
- [ ] SSL certificate installed
- [ ] Force HTTPS redirect
- [ ] Update APP_URL to https://

### 9. Server Requirements
- PHP >= 8.1
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Node.js & NPM (for assets)
- Supervisor (for queue workers)

### 10. Supervisor Configuration (for Queue Workers)
Create `/etc/supervisor/conf.d/polyrion-worker.conf`:
```ini
[program:polyrion-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path-to-project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path-to-project/storage/logs/worker.log
stopwaitsecs=3600
```

Then run:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start polyrion-worker:*
```

## ✅ Critical Features Verified

### Trading System
- [x] Trade placement validation (market closed/resolving check)
- [x] Auto-settlement scheduler (runs every minute)
- [x] Automatic payout on win
- [x] Portfolio calculation
- [x] Profit/Loss calculation

### Security
- [x] Cloudflare reCAPTCHA on login/register
- [x] CSRF protection
- [x] Rate limiting
- [x] Password hashing

### Payment Integration
- [x] Binance Pay configured
- [x] MetaMask integration
- [x] Wallet system

## 🚀 Deployment Steps

1. **Backup Current Database**
```bash
mysqldump -u username -p database_name > backup.sql
```

2. **Upload Files to Server**
```bash
# Use git, FTP, or deployment tool
git pull origin main
```

3. **Install Dependencies**
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

4. **Run Migrations**
```bash
php artisan migrate --force
```

5. **Optimize Application**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

6. **Set Permissions**
```bash
chmod -R 775 storage bootstrap/cache
```

7. **Configure Cron**
```bash
crontab -e
# Add: * * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1
```

8. **Start Queue Worker**
```bash
# Using Supervisor (recommended)
sudo supervisorctl start polyrion-worker:*

# Or manually
php artisan queue:work
```

9. **Test Critical Features**
- [ ] User registration
- [ ] User login
- [ ] Trade placement
- [ ] Market settlement
- [ ] Payout processing
- [ ] Deposit/Withdrawal

## ⚠️ Important Notes

1. **Environment Variables**: Make sure all sensitive data is in .env file, NOT in code
2. **Database Backup**: Set up automatic daily backups
3. **Monitoring**: Monitor logs for errors
4. **Queue Workers**: Must run continuously for trade commissions
5. **Scheduler**: Must run every minute for auto-settlement
6. **SSL**: Required for production (HTTPS)
7. **Error Logging**: Check `storage/logs/laravel.log` regularly

## 🔍 Post-Deployment Verification

1. Check application is accessible
2. Test user registration
3. Test user login
4. Test trade placement
5. Verify auto-settlement is working
6. Check queue workers are processing jobs
7. Monitor error logs
8. Verify SSL certificate

## 📝 Maintenance Commands

```bash
# Clear all caches
php artisan optimize:clear

# View scheduled tasks
php artisan schedule:list

# Check queue status
php artisan queue:work --once

# View logs
tail -f storage/logs/laravel.log
```


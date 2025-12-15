# cPanel Cron Job Setup Guide

## Quick Setup for cPanel/Hosting Environment

Based on your hosting environment (polyrion.com), here's how to set up the Laravel scheduler cron job.

## Step-by-Step Instructions

### Step 1: Access cPanel Cron Jobs

1. Log in to your cPanel
2. Navigate to **Cron Jobs** (under Advanced section)
3. Click on **"Cron Jobs"** tab

### Step 2: Create New Cron Job

1. Click **"Add New Cron Job"** or **"Standard (cPanel v92)"** tab
2. Select **Template**: "Every minute" (or "Every Minute (* * * * *)")

### Step 3: Configure the Cron Job

#### Option A: Using Template (Recommended)

1. **Template**: Select "Every minute" from dropdown
2. **Command**: Enter the following command:

```bash
/usr/bin/php8.2 /home/polyrion/htdocs/polyrion.com/artisan schedule:run >> /dev/null 2>&1
```

**OR** if PHP 8.2 is not available, try:

```bash
/usr/bin/php /home/polyrion/htdocs/polyrion.com/artisan schedule:run >> /dev/null 2>&1
```

#### Option B: Manual Configuration

If you prefer to set it up manually:

- **Minute**: `*`
- **Hour**: `*`
- **Day**: `*`
- **Month**: `*`
- **Weekday**: `*`
- **Command**: 
```bash
/usr/bin/php8.2 /home/polyrion/htdocs/polyrion.com/artisan schedule:run >> /dev/null 2>&1
```

### Step 4: Add Cron Job

Click **"Add Cron Job"** button

## Finding Your Correct Paths

### Find PHP Path

You can find your PHP path by creating a temporary PHP file:

1. Create a file `phpinfo.php` in your public directory:
```php
<?php phpinfo(); ?>
```

2. Visit `https://polyrion.com/phpinfo.php` in your browser
3. Look for "System" → "Server API" and "Configuration File (php.ini) Path"
4. The PHP executable path is usually: `/usr/bin/php` or `/usr/local/bin/php` or `/opt/cpanel/ea-php82/root/usr/bin/php`

### Find Project Path

Your project path should be:
```
/home/polyrion/htdocs/polyrion.com
```

Or check in cPanel File Manager:
- Navigate to File Manager
- Your project root should be visible there
- The full path will be shown at the top

### Alternative: Using which php

If you have SSH access, you can run:
```bash
which php
```

Or check PHP version:
```bash
php -v
```

## Recommended Command (With Logging)

For better debugging, use this command with logging:

```bash
/usr/bin/php8.2 /home/polyrion/htdocs/polyrion.com/artisan schedule:run >> /home/polyrion/htdocs/polyrion.com/storage/logs/scheduler.log 2>&1
```

This will log all scheduler output to `storage/logs/scheduler.log`

## Verify Setup

### Method 1: Check Cron Logs in cPanel

1. Go to **Cron Jobs** in cPanel
2. Look for your cron job in the list
3. Check the "Last Run" timestamp

### Method 2: Check Laravel Logs

Check if the scheduler is running by viewing logs:
```bash
tail -f /home/polyrion/htdocs/polyrion.com/storage/logs/laravel.log
```

### Method 3: Test Manually (via SSH)

If you have SSH access, test the scheduler:
```bash
cd /home/polyrion/htdocs/polyrion.com
php artisan schedule:run
```

### Method 4: List Scheduled Tasks

```bash
cd /home/polyrion/htdocs/polyrion.com
php artisan schedule:list
```

## Current Scheduled Tasks

Your application has these scheduled tasks:

1. **events:store** - Runs every minute
   - Fetches events from Polymarket API
   
2. **events:detect-categories** - Runs daily at 2:00 AM
   - Detects and updates event categories
   
3. **settle-markets** - Runs every minute
   - Automatically settles closed markets

## Troubleshooting

### Cron Job Not Running

1. **Check PHP Path**: Make sure the PHP path is correct
   - Try: `/usr/bin/php`
   - Or: `/usr/local/bin/php`
   - Or: `/opt/cpanel/ea-php82/root/usr/bin/php` (for PHP 8.2)

2. **Check Project Path**: Verify the project path is correct
   - Should be: `/home/polyrion/htdocs/polyrion.com`
   - Make sure `artisan` file exists in this directory

3. **Check Permissions**: Ensure the cron user can execute PHP and access the project
   ```bash
   chmod +x /home/polyrion/htdocs/polyrion.com/artisan
   ```

4. **Check Laravel Logs**: 
   ```bash
   tail -f /home/polyrion/htdocs/polyrion.com/storage/logs/laravel.log
   ```

### Common Issues

**Issue**: "artisan: command not found"
- **Solution**: Use full path to artisan: `/home/polyrion/htdocs/polyrion.com/artisan`

**Issue**: "PHP not found"
- **Solution**: Use full path to PHP: `/usr/bin/php` or check with `which php`

**Issue**: "Permission denied"
- **Solution**: Check file permissions:
  ```bash
  chmod 755 /home/polyrion/htdocs/polyrion.com/artisan
  chmod -R 775 /home/polyrion/htdocs/polyrion.com/storage
  ```

## Alternative: Direct Command Execution

If you want to run specific commands directly via cron (not recommended, but possible):

### Run events:store every minute:
```bash
* * * * * /usr/bin/php8.2 /home/polyrion/htdocs/polyrion.com/artisan events:store >> /dev/null 2>&1
```

### Run events:detect-categories daily at 2 AM:
```bash
0 2 * * * /usr/bin/php8.2 /home/polyrion/htdocs/polyrion.com/artisan events:detect-categories >> /dev/null 2>&1
```

**Note**: Using `schedule:run` is recommended as it manages all scheduled tasks through Laravel's scheduler.

## Security Note

Make sure your `storage/logs` directory is not publicly accessible. Check your `.htaccess` or server configuration to prevent direct access to log files.

## Need Help?

If you encounter issues:
1. Check cPanel error logs
2. Check Laravel logs: `storage/logs/laravel.log`
3. Test commands manually via SSH
4. Verify PHP and project paths are correct


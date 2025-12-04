# Cron Job Setup Guide

## Laravel Scheduler Cron Job

Laravel's task scheduler needs a single cron entry that runs every minute. The scheduler will then determine which tasks need to be run.

## Linux/Unix Cron Setup

### Step 1: Edit Crontab

```bash
crontab -e
```

### Step 2: Add This Line

```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

**Replace `/path/to/your/project` with your actual project path.**

For example, if your project is at `/var/www/polyrion`:

```bash
* * * * * cd /var/www/polyrion && php artisan schedule:run >> /dev/null 2>&1
```

### Step 3: Save and Exit

- Press `Ctrl + X`
- Press `Y` to confirm
- Press `Enter` to save

### Step 4: Verify Cron Job

```bash
crontab -l
```

You should see your cron job listed.

## Windows Task Scheduler Setup

### Step 1: Open Task Scheduler

1. Press `Win + R`
2. Type `taskschd.msc` and press Enter

### Step 2: Create Basic Task

1. Click "Create Basic Task" in the right panel
2. Name: `Laravel Scheduler`
3. Description: `Runs Laravel scheduled tasks every minute`
4. Click Next

### Step 3: Set Trigger

1. Select "Daily"
2. Click Next
3. Set start date to today
4. Set time to current time
5. Recur every: `1 days`
6. Click Next

### Step 4: Set Action

1. Select "Start a program"
2. Click Next
3. Program/script: `C:\path\to\php.exe` (or `php` if in PATH)
4. Add arguments: `artisan schedule:run`
5. Start in: `E:\Client Projects\Backend\Polyrion` (your project path)
6. Click Next

### Step 5: Finish

1. Check "Open the Properties dialog for this task"
2. Click Finish

### Step 6: Configure Advanced Settings

1. In Properties, go to "Triggers" tab
2. Select the trigger and click "Edit"
3. Check "Repeat task every:" and set to `1 minute`
4. Set "for a duration of:" to `Indefinitely`
5. Click OK

### Step 7: Set Conditions

1. Go to "Conditions" tab
2. Uncheck "Start the task only if the computer is on AC power" (if needed)
3. Click OK

## Current Scheduled Commands

Your Laravel application has the following scheduled commands:

1. **events:store** - Runs every minute
   - Fetches events from Polymarket API
   - Command: `php artisan events:store`

2. **events:detect-categories** - Runs daily at 2:00 AM
   - Detects and updates event categories
   - Command: `php artisan events:detect-categories`

## Development: Using schedule:work (Recommended for Local)

For local development, you can use Laravel's `schedule:work` command which runs the scheduler continuously:

```bash
php artisan schedule:work
```

This will run the scheduler in the foreground and execute tasks as they become due. Press `Ctrl+C` to stop.

## Testing the Cron Job

### Test Scheduler Manually

```bash
php artisan schedule:run
```

### List Scheduled Commands

```bash
php artisan schedule:list
```

### Test Individual Commands

```bash
php artisan events:store
php artisan events:detect-categories
```

## Logging

Scheduled command outputs are logged to:
- `storage/logs/laravel.log` (default Laravel log)
- `storage/logs/category-detection.log` (category detection command)

## Troubleshooting

### Cron Job Not Running

1. Check if cron service is running:
   ```bash
   sudo service cron status  # Linux
   ```

2. Check cron logs:
   ```bash
   sudo tail -f /var/log/cron  # Linux
   ```

3. Verify PHP path:
   ```bash
   which php
   ```

4. Test manually:
   ```bash
   cd /path/to/project && php artisan schedule:run
   ```

### Permission Issues

Make sure the cron user has permission to:
- Read/write to the project directory
- Execute PHP
- Write to storage/logs directory

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## Production Server Setup

For production, you may want to:

1. **Add output logging:**
   ```bash
   * * * * * cd /path/to/project && php artisan schedule:run >> /path/to/project/storage/logs/scheduler.log 2>&1
   ```

2. **Use absolute paths:**
   ```bash
   * * * * * /usr/bin/php /path/to/project/artisan schedule:run >> /dev/null 2>&1
   ```

3. **Set proper user:**
   ```bash
   * * * * * www-data cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
   ```

## Alternative: Using Supervisor (Recommended for Production)

For more control, you can use Supervisor to run the scheduler:

1. Install Supervisor:
   ```bash
   sudo apt-get install supervisor
   ```

2. Create config file `/etc/supervisor/conf.d/laravel-scheduler.conf`:
   ```ini
   [program:laravel-scheduler]
   process_name=%(program_name)s
   command=/usr/bin/php /path/to/project/artisan schedule:work
   autostart=true
   autorestart=true
   user=www-data
   redirect_stderr=true
   stdout_logfile=/path/to/project/storage/logs/scheduler.log
   ```

3. Start Supervisor:
   ```bash
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start laravel-scheduler
   ```

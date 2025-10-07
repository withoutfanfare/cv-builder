# Deployment & Maintenance

<cite>
**Referenced Files in This Document**   
- [README.md](file://README.md)
- [config/database.php](file://config/database.php)
- [config/queue.php](file://config/queue.php)
- [config/filesystems.php](file://config/filesystems.php)
- [app/Jobs/ProcessCvReview.php](file://app/Jobs/ProcessCvReview.php)
- [app/Services/CvReviewService.php](file://app/Services/CvReviewService.php)
- [app/Console/Commands/CheckReviewBudget.php](file://app/Console/Commands/CheckReviewBudget.php)
</cite>

## Table of Contents
1. [Introduction](#introduction)
2. [Deployment Environments](#deployment-environments)
3. [Configuration Management](#configuration-management)
4. [Database Setup](#database-setup)
5. [Queue Workers](#queue-workers)
6. [Backup Strategies](#backup-strategies)
7. [Performance Optimization](#performance-optimization)
8. [Monitoring Recommendations](#monitoring-recommendations)
9. [Security Hardening](#security-hardening)
10. [Maintenance Routines](#maintenance-routines)

## Introduction
This document provides comprehensive guidance for deploying and maintaining the cv-builder application in production environments. The application is built with Laravel 12 and Filament 4, designed to help users manage job applications, tailor CVs, and optimize their job search workflow. This guide covers deployment across various hosting environments, configuration requirements, database setup, queue processing for AI-powered CV reviews, backup strategies, performance optimization, monitoring, security measures, and routine maintenance procedures to ensure reliable operation.

## Deployment Environments

### Shared Hosting
Deploying cv-builder on shared hosting requires careful consideration of resource limitations and environment constraints:

1. **Prerequisites**: Ensure PHP 8.2+ with required extensions (PDO, OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo, GD, cURL)
2. **Upload Files**: Transfer all application files via FTP/SFTP to the web root directory
3. **Set Permissions**: Configure storage and bootstrap/cache directories with write permissions
4. **Database Setup**: Create MySQL database and user through hosting control panel
5. **Environment Configuration**: Modify .env file with database credentials and set APP_ENV=production
6. **Run Migrations**: Execute migrations through SSH or hosting provider's database tool
7. **Asset Compilation**: Pre-compile assets locally using `npm run build` before upload

**Section sources**
- [README.md](file://README.md#L25-L45)
- [config/database.php](file://config/database.php#L10-L25)

### VPS (Virtual Private Server)
VPS deployment offers greater control and performance for the cv-builder application:

1. **Server Provisioning**: Install Ubuntu/Debian or CentOS with minimum 2GB RAM and 2 CPU cores
2. **LAMP/LEMP Stack**: Install Apache/Nginx, MySQL/MariaDB, and PHP 8.2+ with required extensions
3. **Application Deployment**: Clone repository to /var/www/cv-builder
4. **Composer Installation**: Run `composer install --optimize-autoloader --no-dev`
5. **Node.js Setup**: Install Node.js 18+ and run `npm install --production`
6. **Asset Compilation**: Execute `npm run build` to compile CSS/JS assets
7. **Web Server Configuration**: Set document root to /var/www/cv-builder/public
8. **Process Management**: Configure Supervisor for queue workers (detailed in Queue Workers section)

**Section sources**
- [README.md](file://README.md#L25-L45)
- [config/database.php](file://config/database.php#L10-L25)

### Cloud Platforms
Cloud deployment on platforms like AWS, Google Cloud, or Azure provides scalability and high availability:

#### AWS Elastic Beanstalk
1. **Environment Creation**: Create PHP platform environment with RDS database
2. **Configuration**: Set environment variables for database connection and services
3. **Deployment**: Package application as ZIP and deploy via EB CLI or console
4. **Scaling**: Configure auto-scaling based on CPU utilization or request count
5. **Storage**: Use S3 for file storage (configured in filesystems.php)

#### Platform as a Service (Heroku, Laravel Forge, Vapor)
1. **Repository Connection**: Connect GitHub/GitLab repository to deployment platform
2. **Build Configuration**: Define build steps in platform-specific configuration
3. **Environment Variables**: Set all required environment variables through platform interface
4. **Database Provisioning**: Use platform-managed database services
5. **Queue Processing**: Configure worker dynos or separate worker servers

**Section sources**
- [README.md](file://README.md#L25-L45)
- [config/database.php](file://config/database.php#L10-L25)
- [config/filesystems.php](file://config/filesystems.php#L30-L45)

## Configuration Management
Proper configuration is essential for production operation of the cv-builder application.

### Environment Variables
The application uses environment variables for configuration, defined in the .env file:

```env
APP_NAME=CV Builder
APP_ENV=production
APP_KEY=your-generated-app-key
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cv_builder
DB_USERNAME=your-db-username
DB_PASSWORD=your-db-password

# Queue Configuration
QUEUE_CONNECTION=database

# File Storage
FILESYSTEM_DISK=local

# OpenAI Integration
OPENAI_API_KEY=your-openai-api-key
OPENAI_MODEL=gpt-4-turbo
SERVICES_OPENAI_MONTHLY_BUDGET_CENTS=5000
```

**Section sources**
- [config/database.php](file://config/database.php#L10-L25)
- [config/queue.php](file://config/queue.php#L10-L15)
- [config/filesystems.php](file://config/filesystems.php#L10-L15)

### Configuration Files
Key configuration files and their production settings:

#### Database Configuration
The database.php configuration supports multiple database types with environment-driven settings:

```php
'default' => env('DB_CONNECTION', 'sqlite'),
'connections' => [
    'mysql' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'laravel'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => env('DB_CHARSET', 'utf8mb4'),
        'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
    ],
]
```

#### Queue Configuration
The queue.php configuration determines how background jobs are processed:

```php
'default' => env('QUEUE_CONNECTION', 'database'),
'connections' => [
    'database' => [
        'driver' => 'database',
        'table' => env('DB_QUEUE_TABLE', 'jobs'),
        'queue' => env('DB_QUEUE', 'default'),
        'retry_after' => (int) env('DB_QUEUE_RETRY_AFTER', 90),
    ],
]
```

#### Filesystem Configuration
The filesystems.php configuration manages file storage locations:

```php
'default' => env('FILESYSTEM_DISK', 'local'),
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app/private'),
    ],
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
]
```

**Section sources**
- [config/database.php](file://config/database.php#L10-L180)
- [config/queue.php](file://config/queue.php#L10-L110)
- [config/filesystems.php](file://config/filesystems.php#L10-L75)

## Database Setup
Proper database configuration is critical for application performance and data integrity.

### Database Selection
The application supports multiple database engines:

- **MySQL/MariaDB**: Recommended for production environments
- **SQLite**: Suitable for development and small-scale deployments
- **PostgreSQL**: Supported alternative for enterprise deployments

### Migration Process
After initial deployment, run database migrations to create required tables:

```bash
php artisan migrate --force
```

For new installations with sample data:
```bash
php artisan migrate --seed --force
```

### Database Optimization
Configure database settings for optimal performance:

1. **Indexing**: Ensure proper indexes on frequently queried columns
2. **Connection Pooling**: Configure appropriate connection limits
3. **Query Optimization**: Monitor slow queries and optimize as needed
4. **Regular Maintenance**: Schedule periodic optimization tasks

**Section sources**
- [config/database.php](file://config/database.php#L10-L180)
- [README.md](file://README.md#L35-L45)

## Queue Workers
The cv-builder application uses queue workers to process background tasks, particularly AI-powered CV reviews.

### Queue Configuration
The application is configured to use the database queue driver by default, which stores jobs in the jobs table:

```php
'default' => env('QUEUE_CONNECTION', 'database'),
```

### ProcessCvReview Job
The ProcessCvReview job handles AI analysis of CVs against job applications:

```php
class ProcessCvReview implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(public JobApplication $jobApplication)
    {
        //
    }

    public function handle(CvReviewService $service): void
    {
        try {
            $reviewData = $service->analyzeForJob(
                $this->jobApplication->cv,
                $this->jobApplication
            );

            $estimatedTokens = $reviewData['analysis_metadata']['tokens_used'] ?? 0;
            $costCents = $service->estimateCostCents($estimatedTokens);

            $this->jobApplication->update([
                'ai_review_data' => $reviewData,
                'ai_review_completed_at' => now(),
                'ai_review_cost_cents' => $costCents,
            ]);

            Log::info('CV review completed successfully', [
                'job_application_id' => $this->jobApplication->id,
                'match_score' => $reviewData['match_score'],
                'cost_cents' => $costCents,
            ]);
        } catch (\Exception $e) {
            Log::error('CV review failed', [
                'job_application_id' => $this->jobApplication->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
```

### Worker Process Management
Configure Supervisor to manage queue worker processes:

```ini
[program:cv-builder-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/cv-builder/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/cv-builder/storage/logs/worker.log
```

Start the worker processes:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start cv-builder-worker:*
```

### Alternative Queue Drivers
For high-volume deployments, consider switching to Redis:

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

**Section sources**
- [config/queue.php](file://config/queue.php#L10-L110)
- [app/Jobs/ProcessCvReview.php](file://app/Jobs/ProcessCvReview.php#L1-L60)
- [app/Services/CvReviewService.php](file://app/Services/CvReviewService.php#L1-L225)

## Backup Strategies
Implement comprehensive backup strategies to protect application data.

### Database Backups
Regular database backups are essential for data recovery:

#### Automated MySQL Backups
Create a backup script:
```bash
#!/bin/bash
DATE=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/backups/db"
MYSQL_USER="your-user"
MYSQL_PASSWORD="your-password"
DATABASE="cv_builder"

mysqldump -u $MYSQL_USER -p$MYSQL_PASSWORD $DATABASE | gzip > $BACKUP_DIR/db_$DATE.sql.gz
```

Schedule with cron:
```bash
# Daily backup at 2 AM
0 2 * * * /path/to/backup-script.sh

# Keep only last 7 days of backups
0 3 * * * find /backups/db -name "db_*.sql.gz" -mtime +7 -delete
```

#### SQLite Backups
For SQLite deployments:
```bash
#!/bin/bash
DATE=$(date +"%Y%m%d_%H%M%S")
cp /var/www/cv-builder/database/database.sqlite /backups/db/database_$DATE.sqlite
```

### File Backups
Backup uploaded files and application assets:

#### Storage Directory
Backup the storage directory which contains user-uploaded files:
```bash
#!/bin/bash
DATE=$(date +"%Y%m%d_%H%M%S")
tar -czf /backups/files/storage_$DATE.tar.gz /var/www/cv-builder/storage/app/public
```

#### Configuration Files
Backup critical configuration files:
```bash
#!/bin/bash
DATE=$(date +"%Y%m%d_%H%M%S")
tar -czf /backups/config/config_$DATE.tar.gz /var/www/cv-builder/.env /var/www/cv-builder/config
```

### Cloud Storage Backups
When using S3 or similar services, enable versioning and configure cross-region replication for added redundancy.

**Section sources**
- [config/database.php](file://config/database.php#L10-L180)
- [config/filesystems.php](file://config/filesystems.php#L10-L75)

## Performance Optimization
Implement various optimization techniques to ensure responsive application performance.

### Caching Strategies
Leverage Laravel's caching system to reduce database load:

#### Configuration Cache
Reduce configuration loading time:
```bash
php artisan config:cache
```

#### Route Cache
Speed up route registration:
```bash
php artisan route:cache
```

#### View Cache
Cache compiled Blade templates:
```bash
php artisan view:cache
```

### Queue Processing Optimization
Optimize queue worker performance:

1. **Multiple Workers**: Run multiple queue workers to process jobs in parallel
2. **Max Execution Time**: Set appropriate --max-time to prevent memory leaks
3. **Sleep Interval**: Adjust --sleep value based on job frequency
4. **Batch Processing**: Consider using queue batching for high-volume jobs

### Asset Optimization
Optimize frontend assets for faster loading:

1. **Minification**: Ensure CSS and JavaScript are minified
2. **Compression**: Enable Gzip/Brotli compression on web server
3. **CDN**: Serve static assets through a Content Delivery Network
4. **Caching Headers**: Set appropriate cache headers for static assets

### Database Optimization
Implement database performance improvements:

1. **Indexing**: Add indexes to frequently queried columns
2. **Query Optimization**: Use Laravel Debugbar to identify slow queries
3. **Eager Loading**: Implement eager loading to prevent N+1 query problems
4. **Database Cleanup**: Regularly clean up old records and optimize tables

**Section sources**
- [config/cache.php](file://config/cache.php)
- [config/queue.php](file://config/queue.php#L10-L110)
- [vite.config.js](file://vite.config.js)

## Monitoring Recommendations
Implement comprehensive monitoring to maintain application health.

### Application Health Monitoring
Monitor key application metrics:

#### Uptime Monitoring
Use external services to monitor application availability:
- Pingdom
- UptimeRobot
- Healthchecks.io

#### Performance Monitoring
Track application response times and error rates:
- Laravel Telescope (development only)
- New Relic
- Datadog
- Prometheus with Grafana

### Error Tracking
Implement robust error tracking:

#### Laravel Logging
Configure proper logging in config/logging.php:
```php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'slack'],
        'ignore_exceptions' => false,
    ],
    'single' => [
        'driver' => 'single',
        'path' => storage_path('logs/laravel.log'),
        'level' => 'debug',
    ],
]
```

#### External Error Tracking
Integrate with error tracking services:
- Sentry
- Rollbar
- Bugsnag

### API Usage Monitoring
Track usage of external APIs, particularly OpenAI:

#### Budget Monitoring
The CheckReviewBudget command monitors AI review costs:
```php
class CheckReviewBudget extends Command
{
    protected $signature = 'review:check-budget';
    protected $description = 'Check monthly AI review budget and alert if threshold exceeded';

    public function handle(): int
    {
        $budgetCents = config('services.openai.monthly_budget_cents', 5000);
        $threshold = 0.8;

        $totalCostCents = JobApplication::query()
            ->whereNotNull('ai_review_completed_at')
            ->whereYear('ai_review_completed_at', now()->year)
            ->whereMonth('ai_review_completed_at', now()->month)
            ->sum('ai_review_cost_cents');

        $percentageUsed = $budgetCents > 0 ? ($totalCostCents / $budgetCents) * 100 : 0;

        if ($percentageUsed >= ($threshold * 100)) {
            Log::warning('AI Review budget threshold exceeded', [
                'budget_cents' => $budgetCents,
                'used_cents' => $totalCostCents,
                'percentage' => $percentageUsed,
            ]);
        }

        return self::SUCCESS;
    }
}
```

Schedule the budget check:
```bash
# Run daily at 3 AM
0 3 * * * php /var/www/cv-builder/artisan review:check-budget
```

**Section sources**
- [app/Console/Commands/CheckReviewBudget.php](file://app/Console/Commands/CheckReviewBudget.php#L1-L65)
- [config/logging.php](file://config/logging.php)

## Security Hardening
Implement security measures to protect the application and user data.

### SSL Configuration
Enforce HTTPS across the application:

#### Web Server Configuration
Nginx configuration for SSL:
```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.com;

    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    
    root /var/www/cv-builder/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

#### Laravel Configuration
Force HTTPS in .env:
```env
APP_URL=https://your-domain.com
SESSION_SECURE_COOKIE=true
```

### Regular Updates
Maintain security through regular updates:

#### Dependency Updates
Regularly update PHP, Laravel, and npm packages:
```bash
# Update Composer dependencies
composer update

# Update npm packages
npm update

# Run Laravel-specific updates
php artisan view:clear
php artisan config:clear
```

#### Security Patches
Monitor for security vulnerabilities:
- Subscribe to Laravel security announcements
- Use tools like `composer audit` to identify vulnerable packages
- Regularly check for PHP and server OS security updates

### Security Headers
Implement security headers to protect against common web vulnerabilities:

```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header X-Content-Type-Options "nosniff" always;
add_header Referrer-Policy "no-referrer-when-downgrade" always;
add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self';" always;
```

**Section sources**
- [config/app.php](file://config/app.php)
- [config/session.php](file://config/session.php)

## Maintenance Routines
Implement regular maintenance tasks to ensure application stability.

### Log Rotation
Configure log rotation to prevent disk space issues:

#### Laravel Log Rotation
Use Laravel's built-in log rotation by configuring Monolog in config/logging.php:
```php
'single' => [
    'driver' => 'daily',
    'path' => storage_path('logs/laravel.log'),
    'level' => 'debug',
    'days' => 14,
],
```

#### System Log Rotation
Configure logrotate for system logs:
```bash
/var/www/cv-builder/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    sharedscripts
    postrotate
        php /var/www/cv-builder/artisan log:clear > /dev/null 2>&1 || true
    endscript
}
```

### Database Optimization
Schedule regular database maintenance:

```bash
# Weekly database optimization
0 2 * * 0 mysqlcheck -o --all-databases -u root -pYourPassword
```

### Dependency Updates
Implement a regular update schedule:

#### Weekly Updates
```bash
# Update Composer dependencies
composer update --dry-run  # First check what will be updated
composer update

# Update npm packages
npm outdated
npm update

# Clear caches after updates
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### Monthly Security Review
1. Review security advisories for Laravel and dependencies
2. Check for PHP and server OS updates
3. Audit user accounts and permissions
4. Review backup integrity

**Section sources**
- [config/logging.php](file://config/logging.php)
- [app/Console/Commands/CheckReviewBudget.php](file://app/Console/Commands/CheckReviewBudget.php#L1-L65)
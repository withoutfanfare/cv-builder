# Deployment & Maintenance

<cite>
**Referenced Files in This Document**   
- [composer.json](file://composer.json)
- [package.json](file://package.json)
- [vite.config.js](file://vite.config.js)
- [config/app.php](file://config/app.php)
- [config/database.php](file://config/database.php)
- [config/mail.php](file://config/mail.php)
- [config/filesystems.php](file://config/filesystems.php)
- [config/cache.php](file://config/cache.php)
- [config/session.php](file://config/session.php)
- [config/queue.php](file://config/queue.php)
- [config/services.php](file://config/services.php)
- [database/migrations/0001_01_01_000000_create_users_table.php](file://database/migrations/0001_01_01_000000_create_users_table.php)
- [database/migrations/0001_01_01_000001_create_cache_table.php](file://database/migrations/0001_01_01_000001_create_cache_table.php)
- [database/migrations/2025_10_04_002642_create_pdf_snapshots_table.php](file://database/migrations/2025_10_04_002642_create_pdf_snapshots_table.php)
- [app/Services/PdfSnapshotService.php](file://app/Services/PdfSnapshotService.php)
- [app/Http/Controllers/CvPdfController.php](file://app/Http/Controllers/CvPdfController.php)
- [app/Filament/Providers/AdminPanelProvider.php](file://app/Filament/Providers/AdminPanelProvider.php)
- [.env.example](file://.env.example) - *Updated in recent commit*
- [README.md](file://README.md) - *Updated in recent commit*
- [MYSQL_MIGRATION_ROADMAP.md](file://MYSQL_MIGRATION_ROADMAP.md) - *Added in recent commit*
</cite>

## Update Summary
**Changes Made**   
- Updated database configuration sections to reflect migration from SQLite to MySQL 9.2
- Added MySQL-specific deployment instructions and environment configuration
- Enhanced security considerations with file size limits and sanitization
- Updated section sources to include newly referenced files
- Added references to migration roadmap documentation

## Table of Contents
1. [Deployment Instructions](#deployment-instructions)
2. [Environment Configuration](#environment-configuration)
3. [Asset Compilation with Vite](#asset-compilation-with-vite)
4. [Database Migrations and Seeding](#database-migrations-and-seeding)
5. [Maintenance Tasks](#maintenance-tasks)
6. [Security Considerations](#security-considerations)
7. [Performance Optimization](#performance-optimization)
8. [Disaster Recovery and Rollback](#disaster-recovery-and-rollback)

## Deployment Instructions

### Shared Hosting
For shared hosting environments, ensure PHP 8.2+ is available. Upload the entire project directory via FTP or SFTP. Run the following commands via SSH or hosting control panel terminal:
```bash
composer install --optimize-autoloader --no-dev
cp .env.example .env
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### VPS (Ubuntu/Debian)
On a VPS, use the following deployment sequence:
```bash
# Install dependencies
sudo apt update && sudo apt upgrade -y
sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mbstring php8.2-xml php8.2-mysql php8.2-zip php8.2-curl php8.2-bcmath

# Install Node.js and npm
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Deploy application
git clone https://your-repo-url.git /var/www/cv-builder
cd /var/www/cv-builder
composer install --optimize-autoloader --no-dev
npm install
npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan storage:link
```

### Cloud Platforms (AWS, GCP, Azure)
For cloud deployments, use containerization:
```dockerfile
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    nodejs \
    npm \
    mysql-client

WORKDIR /var/www/html

COPY . .
RUN composer install --optimize-autoloader --no-dev
RUN npm install && npm run build
RUN php artisan key:generate
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

EXPOSE 80
CMD ["php", "artisan", "serve", "--host=0.0.0.0"]
```

**Section sources**
- [composer.json](file://composer.json)
- [package.json](file://package.json)
- [config/app.php](file://config/app.php)
- [.env.example](file://.env.example) - *Updated in recent commit*

## Environment Configuration

Configure environment-specific settings in the `.env` file:

### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cv_builder_local
DB_USERNAME=root
DB_PASSWORD=
```

The application has been migrated from SQLite to MySQL 9.2. The `.env.example` file has been updated to reflect MySQL as the default database connection. For testing purposes, SQLite is still used with an in-memory database as configured in `phpunit.xml`.

### Mail Configuration
```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@cvbuilder.com
MAIL_FROM_NAME="CV Builder"
# For production:
# MAIL_MAILER=smtp
# MAIL_HOST=mailhog
# MAIL_PORT=1025
# MAIL_USERNAME=null
# MAIL_PASSWORD=null
```

### Storage Configuration
```env
FILESYSTEM_DISK=public
# For S3:
# FILESYSTEM_DISK=s3
# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=us-east-1
# AWS_BUCKET=
```

### Cache and Session
```env
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

**Section sources**
- [config/database.php](file://config/database.php#L0-L182)
- [config/mail.php](file://config/mail.php#L0-L117)
- [config/filesystems.php](file://config/filesystems.php#L0-L47)
- [config/cache.php](file://config/cache.php#L0-L107)
- [config/session.php](file://config/session.php#L0-L216)
- [config/queue.php](file://config/queue.php#L0-L111)
- [phpunit.xml](file://phpunit.xml) - *Confirms SQLite usage for testing*

## Asset Compilation with Vite

The application uses Vite for asset management. In production:

1. Install Node.js dependencies:
```bash
npm install
```

2. Build assets for production:
```bash
npm run build
```

3. The build process compiles resources from `resources/css/app.css` and `resources/js/app.js` into the `public/build` directory.

4. Ensure the Vite manifest is generated:
```bash
# Output: public/build/manifest.json
```

5. For development, use:
```bash
npm run dev
```

The Vite configuration includes Laravel and Tailwind CSS plugins:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

**Section sources**
- [vite.config.js](file://vite.config.js)
- [package.json](file://package.json)
- [resources/css/app.css](file://resources/css/app.css)
- [resources/js/app.js](file://resources/js/app.js)

## Database Migrations and Seeding

Run database migrations and seed initial data:

```bash
# Run all migrations
php artisan migrate --force

# Seed with example data
php artisan db:seed --class=BaseCVSeeder

# Refresh database (use in development only)
php artisan migrate:fresh --seed
```

The application includes migrations for:
- User authentication tables
- CV and job application management
- PDF snapshot storage
- Cache and session tables

Key migration files:
- `0001_01_01_000000_create_users_table.php` - User authentication
- `2025_10_04_002642_create_pdf_snapshots_table.php` - PDF storage
- `0001_01_01_000001_create_cache_table.php` - Cache storage

The migration from SQLite to MySQL 9.2 has been completed successfully. All 27 application tables have been created in MySQL, and all tests continue to pass using SQLite for testing. The migration process is documented in `MYSQL_MIGRATION_ROADMAP.md`.

**Section sources**
- [database/migrations/0001_01_01_000000_create_users_table.php](file://database/migrations/0001_01_01_000000_create_users_table.php)
- [database/migrations/2025_10_04_002642_create_pdf_snapshots_table.php](file://database/migrations/2025_10_04_002642_create_pdf_snapshots_table.php)
- [database/migrations/0001_01_01_000001_create_cache_table.php](file://database/migrations/0001_01_01_000001_create_cache_table.php)
- [database/seeders/BaseCVSeeder.php](file://database/seeders/BaseCVSeeder.php)
- [MYSQL_MIGRATION_ROADMAP.md](file://MYSQL_MIGRATION_ROADMAP.md) - *Added in recent commit*

## Maintenance Tasks

### Backup Procedures
Regularly backup:
1. Database: `php artisan backup:run`
2. Storage files: `storage/app/public/pdf-snapshots/`
3. Environment file: `.env`

### Log Monitoring
Application logs are stored in `storage/logs/laravel.log`. Configure log rotation:
```bash
# In config/logging.php
'channels' => [
    'daily' => [
        'path' => storage_path('logs/laravel.log'),
        'level' => 'debug',
        'days' => 14,
    ],
]
```

### Queue Management
The application uses database queues for background processing:
```bash
# Start queue worker
php artisan queue:work --tries=3

# Monitor failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Cache Management
Clear and rebuild caches:
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Section sources**
- [config/logging.php](file://config/logging.php#L38-L73)
- [config/queue.php](file://config/queue.php#L0-L111)
- [app/Services/PdfSnapshotService.php](file://app/Services/PdfSnapshotService.php)

## Security Considerations

### Admin Route Protection
The Filament admin panel is secured by default:
- Requires authentication
- Uses Laravel's built-in session management
- Access controlled through user roles and permissions

```php
// app/Filament/Providers/AdminPanelProvider.php
protected function authMiddleware(): array
{
    return ['auth'];
}
```

### PDF Storage Security
PDF snapshots are stored in `storage/app/private` with restricted access:
- Not directly accessible via web
- Served through authenticated controllers
- Files have random generated names
- Maximum file size limited to 10MB to prevent storage exhaustion

```php
// app/Services/PdfSnapshotService.php
// Add file size limits
if (strlen($pdfContent) > 10 * 1024 * 1024) { // 10MB
    throw new \Exception('PDF exceeds maximum size of 10MB');
}

// Sanitize and validate
$sanitizedId = (int) $jobApplication->id;
$filePath = sprintf('pdf-snapshots/%d_%s.pdf', $sanitizedId, $hash);
```

### Dependency Security
Keep dependencies updated:
```bash
# Check for vulnerabilities
composer audit
npm audit

# Update dependencies
composer update --with-dependencies
npm update
```

### Environment Security
- Never commit `.env` to version control
- Set `APP_DEBUG=false` in production
- Use HTTPS for all production traffic
- Set secure session cookies:
```env
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

**Section sources**
- [app/Filament/Providers/AdminPanelProvider.php](file://app/Filament/Providers/AdminPanelProvider.php)
- [app/Http/Controllers/CvPdfController.php](file://app/Http/Controllers/CvPdfController.php)
- [config/filesystems.php](file://config/filesystems.php#L0-L47)
- [config/session.php](file://config/session.php#L158-L216)
- [app/Services/PdfSnapshotService.php](file://app/Services/PdfSnapshotService.php#L49-L71) - *Updated with security fixes*

## Performance Optimization

### Caching Strategies
Implement multiple caching layers:
```bash
# Route caching
php artisan route:cache

# Configuration caching
php artisan config:cache

# View caching
php artisan view:cache

# Clear caches when needed
php artisan cache:clear
```

### PDF Generation Optimization
The application uses Spatie Laravel PDF for PDF generation:
```php
// config/services.php
'pdf' => [
    'driver' => 'snappy',
    'binary' => env('PDF_BINARY', '/usr/local/bin/wkhtmltopdf'),
    'timeout' => 60,
]
```

Optimize by:
- Using queue workers for PDF generation
- Caching frequently generated PDFs
- Limiting concurrent PDF generation jobs

### Database Optimization
- Ensure proper indexing on frequently queried columns
- Use database connection pooling in production
- Monitor slow queries

### Asset Optimization
- Vite automatically minifies CSS and JavaScript
- Images are stored in optimized format
- Use CDN for static assets in production

**Section sources**
- [config/cache.php](file://config/cache.php#L0-L107)
- [config/services.php](file://config/services.php#L0-L37)
- [app/Services/PdfSnapshotService.php](file://app/Services/PdfSnapshotService.php)
- [vite.config.js](file://vite.config.js)

## Disaster Recovery and Rollback

### Backup Strategy
Implement regular backups:
1. Daily database backups
2. Weekly full system backups
3. Off-site backup storage

```bash
# Backup script example
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u root cv_builder_local > /backups/cv_builder_$DATE.sql
tar -czf /backups/cv_builder_files_$DATE.tar.gz storage/app/
```

### Rollback Procedure
To rollback to a previous version:
```bash
# 1. Revert code
git checkout <previous-commit>

# 2. Revert database
php artisan migrate:rollback --step=5

# 3. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. Rebuild assets
npm run build
```

### Monitoring and Alerts
Set up monitoring for:
- Application uptime
- Database performance
- Disk space usage
- Failed jobs

Use Laravel's built-in monitoring or third-party services like Laravel Horizon, New Relic, or Datadog.

### Emergency Access
Maintain emergency access procedures:
- Keep a backup of the `.env` file in secure location
- Maintain list of administrator accounts
- Document server access credentials securely

**Section sources**
- [database/migrations](file://database/migrations)
- [config/database.php](file://config/database.php#L0-L182)
- [config/queue.php](file://config/queue.php#L0-L111)
- [app/Services/PdfSnapshotService.php](file://app/Services/PdfSnapshotService.php)

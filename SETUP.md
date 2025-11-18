# LIMS Setup Guide (Laravel 12 - Pure PHP)

## Quick Start

### 1. System Requirements
```bash
# Laravel 12 requires PHP 8.2+ (Critical)
php --version

# Must show PHP 8.2.0 or higher
# If not, update PHP before proceeding

# Required PHP extensions for Laravel 12
php -m | grep -E "(pdo|pdo_mysql|mbstring|openssl|tokenizer|xml|ctype|json|bcmath|fileinfo|curl|zip)"
```

### 2. Pure PHP Installation (NO Node.js Required)
```bash
# 1. Install PHP dependencies only
composer install --optimize-autoloader

# 2. Laravel 12 optimization
composer dump-autoload --optimize

# ❌ NOT NEEDED - Zero Node.js dependencies
# npm install
# npm run build
# npm run dev

# ✅ ONLY NEEDED - Pure PHP approach
php artisan serve
```

### 3. Database Setup (Laravel 12 Compatible)
```bash
# Create database with proper charset for Laravel 12
mysql -u root -p -e "CREATE DATABASE lims_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Option 1: Import production-ready SQL (Recommended)
mysql -u root -p lims_db < sql/install.sql

# Option 2: Use Laravel 12 migrations
php artisan migrate:fresh --seed --force
```

### 4. Environment Configuration (Laravel 12)
```bash
# Copy environment file
copy .env.example .env

# Generate application key (Laravel 12 compatible)
php artisan key:generate --force

# Create storage link
php artisan storage:link --force

# Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lims_db
DB_USERNAME=root
DB_PASSWORD=your_password

# Laravel 12 specific settings
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### 5. Laravel 12 Performance Optimization
```bash
# Clear all caches (Laravel 12 method)
php artisan optimize:clear

# Cache for production performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### 6. Fix Permissions (Linux/Mac)
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 7. Start Development Server
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### 8. Access System
- **Public**: http://127.0.0.1:8000
- **Login**: http://127.0.0.1:8000/login
- **Default User**: devel / 123456

## Laravel 12 Troubleshooting

### Critical Laravel 12 Issues & Solutions

#### 1. PHP Version Compatibility
```bash
# Laravel 12 REQUIRES PHP 8.2+
php --version

# If PHP < 8.2, update immediately:
# Ubuntu/Debian
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-fileinfo php8.2-tokenizer

# CentOS/RHEL  
sudo dnf install php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-mbstring php8.2-xml
```

#### 2. Laravel 12 Migration Issues
```bash
# If migrations fail, reset completely:
php artisan migrate:reset
php artisan migrate:fresh --seed --force

# Check migration status
php artisan migrate:status

# Run specific migration
php artisan migrate --path=/database/migrations/2024_12_10_000013_add_lims_fields_to_existing_tables.php
```

#### 3. Laravel 12 Cache Problems
```bash
# Complete cache reset for Laravel 12
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear  
php artisan route:clear
php artisan view:clear
php artisan event:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

#### 4. Composer Issues with Laravel 12
```bash
# Update Composer first
composer self-update

# Clear Composer cache
composer clear-cache

# Remove vendor and reinstall
rm -rf vendor composer.lock
composer install --no-dev --optimize-autoloader

# Force platform requirements (if needed)
composer install --ignore-platform-reqs
```

#### 5. Laravel 12 Permission Issues
```bash
# Set correct permissions for Laravel 12
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Create missing directories
mkdir -p storage/logs
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p bootstrap/cache
```

### Laravel 12 Features Implemented ✅
- ✅ **New Application Bootstrap** - Uses `Application::configure()` method
- ✅ **Enhanced Middleware System** - Improved registration and performance
- ✅ **Built-in Health Checks** - `/up` endpoint for monitoring
- ✅ **Improved Caching** - Better cache performance and management
- ✅ **Updated Security** - Enhanced middleware and validation
- ✅ **PHP 8.2+ Features** - Modern PHP syntax and performance
- ✅ **Better Error Handling** - Improved debugging and logging

## Production Deployment

### Web Server Configuration

#### Apache Virtual Host
```apache
<VirtualHost *:80>
    ServerName lims.example.com
    DocumentRoot /path/to/lims/public
    
    <Directory /path/to/lims/public>
        AllowOverride All
        Require all granted
        
        # Laravel 12 optimization
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteRule ^(.*)$ index.php [QSA,L]
        </IfModule>
    </Directory>
    
    # Security headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
</VirtualHost>
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name lims.example.com;
    root /path/to/lims/public;
    index index.php;

    # Laravel 12 optimization
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Security
        fastcgi_hide_header X-Powered-By;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
}
```

### Production Security Checklist
- [ ] Change default passwords (devel/123456)
- [ ] Configure SSL/HTTPS certificates
- [ ] Set proper file permissions (755/644)
- [ ] Enable firewall (UFW/iptables)
- [ ] Configure regular database backups
- [ ] Monitor system logs and audit trails
- [ ] Update PHP and Laravel regularly
- [ ] Configure session security settings
- [ ] Enable OPcache for PHP performance
- [ ] Set up log rotation

### Performance Optimization
```bash
# Production optimization
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Enable OPcache (add to php.ini)
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

### Pure PHP Asset Management (No Build Tools)
```html
<!-- Bootstrap 5.3.2 - Laravel 12 Compatible -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Font Awesome 6.5.0 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<!-- jQuery 3.7.1 -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Alpine.js 3.x -->
<script defer src="https://unpkg.com/alpinejs@3.13.3/dist/cdn.min.js"></script>

<!-- Chart.js 4.4.0 -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
```

### Laravel 12 Health Check
```bash
# Built-in Laravel 12 health check
curl http://127.0.0.1:8000/up

# Should return: {"status": "ok"}

# LIMS custom health check
php artisan lims:health-check

# Test database connectivity
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit
```

### Production Deployment (Laravel 12)
```bash
# 1. Install production dependencies
composer install --no-dev --optimize-autoloader

# 2. Set proper environment
cp .env.example .env
# Edit .env for production settings

# 3. Generate key and optimize
php artisan key:generate --force
php artisan storage:link --force

# 4. Cache everything for performance
php artisan config:cache
php artisan route:cache  
php artisan view:cache
php artisan event:cache

# 5. Set production permissions
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Laravel 12 Development Workflow
```bash
# Daily development routine:
php artisan optimize:clear  # Clear all caches
php artisan serve          # Start development server

# When adding new routes/controllers:
php artisan route:clear
php artisan route:cache

# When modifying config:
php artisan config:clear
php artisan config:cache

# When updating views:
php artisan view:clear
```

### Zero Node.js Dependencies Benefits
- ✅ **Faster Setup** - No npm install required
- ✅ **Simpler Deployment** - Just upload PHP files  
- ✅ **Better Compatibility** - Works on any PHP hosting
- ✅ **Reduced Complexity** - No build process to manage
- ✅ **Instant Changes** - No compilation step needed
- ✅ **Lower Resource Usage** - No Node.js memory overhead
- ✅ **Easier Debugging** - Direct file editing and testing

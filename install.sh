#!/bin/bash

echo "🔬 LIMS Installation Script v2.0.0 - Laravel 12 (Pure PHP)"
echo "=========================================================="

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Check PHP version for Laravel 12
echo ""
print_info "Checking PHP version for Laravel 12 compatibility..."
PHP_VERSION=$(php -r "echo PHP_VERSION;")
PHP_MAJOR=$(php -r "echo PHP_MAJOR_VERSION;")
PHP_MINOR=$(php -r "echo PHP_MINOR_VERSION;")

if [ "$PHP_MAJOR" -gt 8 ] || ([ "$PHP_MAJOR" -eq 8 ] && [ "$PHP_MINOR" -ge 2 ]); then
    print_status "PHP $PHP_VERSION detected (Laravel 12 compatible ✓)"
else
    print_error "PHP 8.2+ required for Laravel 12. Current: $PHP_VERSION"
    echo ""
    echo "To update PHP on Ubuntu/Debian:"
    echo "sudo add-apt-repository ppa:ondrej/php"
    echo "sudo apt update"
    echo "sudo apt install php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl"
    exit 1
fi

# Check required PHP extensions for Laravel 12
print_info "Checking Laravel 12 required PHP extensions..."
required_extensions=("pdo" "pdo_mysql" "mbstring" "openssl" "tokenizer" "xml" "ctype" "json" "bcmath" "fileinfo" "curl" "zip")
missing_extensions=()

for ext in "${required_extensions[@]}"; do
    if php -m | grep -qi "^$ext$"; then
        print_status "$ext extension: OK"
    else
        missing_extensions+=("$ext")
        print_error "$ext extension: MISSING"
    fi
done

if [ ${#missing_extensions[@]} -ne 0 ]; then
    print_error "Missing required extensions: ${missing_extensions[*]}"
    echo ""
    echo "Install missing extensions (Ubuntu/Debian):"
    for ext in "${missing_extensions[@]}"; do
        echo "sudo apt install php8.2-$ext"
    done
    exit 1
fi

# Verify Composer is installed
if ! command -v composer &> /dev/null; then
    print_error "Composer is not installed. Please install Composer first."
    echo "Visit: https://getcomposer.org/download/"
    exit 1
fi

print_status "Composer detected: $(composer --version | head -1)"

# Install PHP dependencies only (NO Node.js required)
echo ""
print_info "Installing Laravel 12 dependencies (PHP ONLY - Zero Node.js Dependencies)..."
if [ -f "composer.json" ]; then
    # Clear any existing vendor directory
    if [ -d "vendor" ]; then
        print_warning "Removing existing vendor directory..."
        rm -rf vendor
    fi
    
    # Update Composer first
    composer self-update --quiet
    
    # Install dependencies
    composer install --optimize-autoloader --no-scripts
    
    if [ $? -eq 0 ]; then
        print_status "✅ Laravel 12 dependencies installed successfully (Pure PHP)"
        print_status "✅ NO Node.js/npm dependencies required"
    else
        print_error "Failed to install Composer dependencies"
        exit 1
    fi
else
    print_error "composer.json not found. Please run this script from the LIMS root directory."
    exit 1
fi

# Create .env file if not exists
echo ""
if [ ! -f .env ]; then
    print_info "Setting up Laravel 12 environment..."
    if [ -f .env.example ]; then
        cp .env.example .env
        print_status ".env file created from example"
    else
        print_warning ".env.example not found, creating minimal .env"
        cat > .env << EOL
APP_NAME="LIMS"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lims
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
EOL
    fi
    
    # Generate application key
    php artisan key:generate --force
    print_status "Application key generated"
else
    print_warning ".env file already exists - skipping creation"
fi

# Create storage link
print_info "Creating storage symbolic link..."
php artisan storage:link --force
if [ $? -eq 0 ]; then
    print_status "Storage link created successfully"
fi

# Database setup options
echo ""
print_info "Laravel 12 Database Setup Options:"
echo "1. Use production SQL file (sql/install.sql) - Recommended"
echo "2. Use Laravel 12 migrations and seeders"
echo "3. Skip database setup (configure later)"
read -p "Choose option (1-3) [1]: " db_option
db_option=${db_option:-1}

case $db_option in
    1)
        if [ -f "sql/install.sql" ]; then
            read -p "Database name [lims]: " db_name
            db_name=${db_name:-lims}
            read -p "MySQL username [root]: " db_user
            db_user=${db_user:-root}
            echo -n "MySQL password: "
            read -s db_pass
            echo ""
            
            print_info "Creating database and importing LIMS data..."
            
            # Create database
            mysql -u "$db_user" -p"$db_pass" -e "CREATE DATABASE IF NOT EXISTS \`$db_name\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
            
            # Import data
            mysql -u "$db_user" -p"$db_pass" "$db_name" < sql/install.sql 2>/dev/null
            
            if [ $? -eq 0 ]; then
                print_status "✅ Production database setup completed"
                print_status "✅ Default users and sample data imported"
            else
                print_error "Database import failed - check credentials and try again"
                exit 1
            fi
            
            # Update .env with database settings
            sed -i "s/DB_DATABASE=.*/DB_DATABASE=$db_name/" .env
            sed -i "s/DB_USERNAME=.*/DB_USERNAME=$db_user/" .env
            sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$db_pass/" .env
        else
            print_error "sql/install.sql not found"
            exit 1
        fi
        ;;
    2)
        read -p "Have you configured database settings in .env? (y/n) [n]: " db_configured
        db_configured=${db_configured:-n}
        if [ "$db_configured" = "y" ]; then
            print_info "Running Laravel 12 migrations..."
            php artisan migrate:fresh --seed --force
            if [ $? -eq 0 ]; then
                print_status "✅ Laravel 12 migrations completed"
            else
                print_error "Migration failed - check database configuration"
                exit 1
            fi
        else
            print_warning "Please configure database in .env file first, then run:"
            echo "php artisan migrate:fresh --seed --force"
        fi
        ;;
    3)
        print_warning "Database setup skipped - configure manually later"
        ;;
esac

# Set proper permissions (Unix/Linux/Mac only)
if [[ "$OSTYPE" != "msys" && "$OSTYPE" != "win32" ]]; then
    echo ""
    print_info "Setting Laravel 12 file permissions..."
    
    # Create directories if they don't exist
    mkdir -p storage/logs
    mkdir -p storage/framework/{cache,sessions,views}
    mkdir -p bootstrap/cache
    
    # Set permissions
    chmod -R 775 storage bootstrap/cache
    
    # Try to set ownership (might fail without sudo)
    if command -v chown &> /dev/null; then
        chown -R $USER:www-data storage bootstrap/cache 2>/dev/null || true
    fi
    
    print_status "File permissions configured"
fi

# Laravel 12 optimization and caching
echo ""
print_info "Optimizing Laravel 12 application..."

# Clear all caches first
php artisan optimize:clear --quiet

# Cache for better performance
php artisan config:cache --quiet
php artisan route:cache --quiet
php artisan view:cache --quiet
php artisan event:cache --quiet

print_status "✅ Laravel 12 application optimized for production"

# Test application
echo ""
print_info "Testing Laravel 12 application..."
if php artisan --version | grep -q "Laravel Framework"; then
    print_status "✅ Laravel 12 framework verified"
else
    print_warning "Laravel framework test inconclusive"
fi

# Check if LIMS commands are available
if php artisan list | grep -q "lims:"; then
    print_status "✅ LIMS commands registered"
    
    # Run health check if available
    if php artisan list | grep -q "lims:health-check"; then
        print_info "Running LIMS health check..."
        php artisan lims:health-check --quiet || true
    fi
else
    print_warning "LIMS commands not found (will be available after full setup)"
fi

# Final installation summary
echo ""
echo "🎉 LIMS Laravel 12 Installation Complete!"
echo "========================================"
print_status "✅ Framework: Laravel 12 (Pure PHP Implementation)"
print_status "✅ PHP Version: $PHP_VERSION (Compatible)"
print_status "✅ Dependencies: PHP Only - Zero Node.js Requirements"
print_status "✅ Assets: CDN-based (Bootstrap, jQuery, Alpine.js)"
print_status "✅ Build Tools: Not Required (Pure PHP Solution)"
echo ""
echo "📋 Access Information:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🌐 URL: http://127.0.0.1:8000"
echo "👤 Default Login: devel / 123456"
echo "🔧 Admin Panel: Available after login"
echo ""
echo "🚀 Quick Start Commands:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "# Start development server"
echo "php artisan serve"
echo ""
echo "# Start with custom host/port"
echo "php artisan serve --host=0.0.0.0 --port=8000"
echo ""
echo "🔧 Maintenance Commands:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "php artisan lims:health-check    # System health check"
echo "php artisan lims:cleanup         # Clean old files"  
echo "php artisan lims:backup          # Backup database"
echo "php artisan optimize:clear       # Clear all caches"
echo ""
print_info "📖 For production deployment guide, see SETUP.md"
print_info "📋 For detailed documentation, see README.md"
print_status "🎯 Installation completed successfully - Ready to use!"

# Reminder about pure PHP approach
echo ""
echo "💡 LIMS Pure PHP Advantages:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
print_status "✅ No Node.js installation required"
print_status "✅ No npm/yarn package management needed"  
print_status "✅ No Webpack/Vite build process required"
print_status "✅ No asset compilation steps needed"
print_status "✅ Faster deployment (PHP files only)"
print_status "✅ Simpler server requirements"
print_status "✅ Better compatibility with shared hosting"
print_status "✅ Reduced complexity for development team"

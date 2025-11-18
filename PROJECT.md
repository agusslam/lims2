# LIMS - Laboratory Integration Management System

## Overview
LIMS is a comprehensive Laboratory Information Management System designed for ISO/IEC 17025:2017 compliant laboratories. This system provides complete sample lifecycle management from intake to certificate generation.

## Features

## Terms to Agent
- Pedoman utama project hanya pada PROJECT.md
- Konsistensi terhadap tampilan tema agar tiap tampilan halaman selaras dan fokus pada kenyamanan member dalam tampilan
- Jangan pernah membuat duplikasi fungsi jika fungsi tersebut telah ada pada bagian lain (eficieny resource)
- Hindari pengulangan jika tidak benar-benar diperlukan untuk controller, views, routes, middleware dan kernel
- Permudah pengguna untuk melakukan preview dan printing jika diperlukan
- Lakukan perubahan pada migrations dan seeders jika benar-benar diperlukan serta konsistensi terhadap perubahan baru untuk menghindari terjadinya error pada bagian lain
- Lakukan perubahan secara bertahap meskipun semua files perlu dirubah secara teliti dan hindari kesalahan penulisan
- Buat project ini se-effisien mungkin terhadap resource dan data

### Code Public and Internal
- **Code Tracking Order (Public)** - UNEJ202510XXXXXX = 4 FIRSTCODE 4 YEAR 2 MM 6digit nomor urut (unique berurutan)
- **Code Sampel (Internal)** - 20251010XXXXXX = 4 FIRSTCODE 4 YEAR 2 MM 2 DD 6digit nomor urut (unique berurutan)
- **Code re-testing for review and validasi reject (internal)** - terduplicate dan code sampel akan ditambahkan 20251010XXXXXX-REVX serta history lama (bagian terduplicate) akan otomatis masuk kedalam archive sebagai not qualified tanpa terhapus namun hilang dari workflow lain (hanya bisa dilihat dari archive). lalu kembali ke bagian analyst

### Core Public
1. **Landing Page** - landing page untuk memudahkan pelanggan dalam memahami alur, informasi terbaru maupun navigasi
2. **Permohonan Pengujian Dan Kaji Ulang** - Form permohonan pengujian oleh publik yang yang terdisi dari :
- tab 1 mengharuskan pengisian nomor whatsapp dengan benar (tervalidasi) dan detail  Contact Person, Nama Perusahaan/Sekolah, Kota/Kabupaten Asal, Alamat Lengkap terintegrasi autocomplate google maps dan Email.
- tab 2 Jumlah Jenis Pengujian yang akan di ujikan (jika lebih dari satu maka kolom jenis sampel, parameter, dan lainnya akan ditambahkan), Jenis Sampel (jika pemilihan jenis sampel tidak adalah "lainnya" maka dapat ditulis secara manual), Jumlah Sampel (quantity), Parameter Uji (tabel atau dapat memilih beberapa parameter sekaligus untuk setiap Jumlah sampel dengan klasifikasi kategori parameter dan opsi parameter tambahan dengan penambahan input), Persyaratan permintaan pelanggan
- tab 3 Konfirmasi & Verifikasi Ringkasan Permintaan, Kode Captcha, and terms accept.
- after submit generate barcode or code tracking and move to tracking system for public dan buat laporan untuk arsip yang nantinya dapat di print oleh supervisor berdasarkan tampilan sama persis dengan paper acuan `data-acuan/F.2.7.1.0.01.jpg`
3. **Tracking Order** - form scan barcode atau input manual code tracking (UNEJ202510XXXXXX = 4 FIRSTCODE 4 YEAR 2 MM 6digit nomor urut)
4. **Feedback** - setelah semua proses selesai maka pelanggan diwajibkan mengisi Kuisioner Kepuasan Pelanggan untuk 1 kali saja `data-acuan\contoh-hasil-paper\Sertifikat Hasil Analisis K Pupuk Organik Padat 2024_18.jpg` lalu akan ditampilkan link mendownload hasil dari pengujian dan sertifikat (jika ada)

### Core Modules
1. **Daftar Sampel Baru** - Validasi sampel oleh supervisor lalu generate Code Sampel juga buat laporan untuk arsip yang nantinya dapat di print oleh supervisor berdasarkan tampilan sama persis dengan paper acuan `data-acuan/F.2.7.1.0.01.jpg` dan Pelaporan dan menunggu response dari admin untuk dilakukan pengecekkan serta kodefikasi barang uji
2. **Kodifikasi Barang Uji** -  pengecekan dan buat laporan untuk arsip kodifikasi barang uji `data-acuan/contoh-hasil-paper/Sertifikat Hasil Analisis K Pupuk Organik Padat 2024_07.jpg` jika telah memenuhi atau sesuai maka akan diteruskan ke Penugasan Analis
3. **Penugasan Analis** - Menerima Response kodifikasi dari admin untuk dilakukan pengecekan pemenuhan kebutuhan buat laporan untuk arsip formulir Verifikasi Permintaan Pengujian Dan Kaji Ulang dengan checklist rekomendasi penyelia `data-acuan/F.2.7.1.0.02.jpg` atau hasil jadi `data-acuan/contoh-hasil-paper/Sertifikat Hasil Analisis K Pupuk Organik Padat 2024_06.jpg` dan Distribusi penugasan sampel kepada analis dengan load balancing for analyst spesific or analyst team based from type and parameter spesialis primer and backup team lalu formulir kodefikasi barang uji untuk arsip serta analyst mempelajari yang nantinya dapat di print oleh analyst dan supervisor berdasarkan tampilan sama persis dengan paper acuan `data-acuan/contoh-hasil-paper/Sertifikat Hasil Analisis K Pupuk Organik Padat 2024_08.jpg`
4. **Pencatatan Hasil** - Input hasil pengujian dengan upload file instrumen dan validasi data dengan petimbangan tinggi (minimalkan kesalahan apapun atau sederhanakan tampilan untuk menunjang adaptasi serta ketelitian analyst) dengan input menyesuaikan data untuk disajikan ke pelanggan dan buat laporan untuk arsip formulir Pengambilan Data Pengujian `data-acuan\contoh-hasil-paper\Sertifikat Hasil Analisis K Pupuk Organik Padat 2024_14.jpg`, `data-acuan\contoh-hasil-paper\Sertifikat Hasil Analisis K Pupuk Organik Padat 2024_15.jpg`,`data-acuan\contoh-hasil-paper\Sertifikat Hasil Analisis K Pupuk Organik Padat 2024_16.jpg`, dan `data-acuan\contoh-hasil-paper\Sertifikat Hasil Analisis K Pupuk Organik Padat 2024_17.jpg`
5. **Review & Validasi** - Review teknis dan mutu oleh auditor dengan approval workflow atau ditolak untuk kembali ke analis untuk di lakukan pengujian ulang dan ketika disetujui maka akan dilanjutkan untuk pembuatan sertifikat atau tanpa sertifikat.
6. **Penerbitan Sertifikat** - Pembuatan dan penerbitan sertifikat hasil uji otomatis jika auditor memilih untuk penerbitan sertifikat.
7. **Pelanggan** - Manajemen pembayaran terintegrasi (Invoice) dengan tracking sistem dan Feedback kepuasan pelanggan 
8. **Manajemen Data** - Arsip, pencarian, dan export data dengan filtering advanced
9. **Compliance ISO 17025** - Dokumentasi dan audit trail lengkap
10. **Manajemen Parameter** - Konfigurasi parameter uji dan metode
11. **Manajemen User** - Role-based access control dengan permission granular
12. **Advanced Management** - Tools admin tingkat lanjut


### Core Laboratory Operations
- ✅ **Sample Intake**: Comprehensive sample registration and management system
- ✅ **Sample Verification**: Quality control and verification workflow
- ✅ **Sample Assignment**: Analyst assignment and workload management
- ✅ **Testing Management**: Complete testing workflow and result entry
- ✅ **Review & Validation**: Result review and validation process
- ✅ **Certificate Generation**: Automated certificate creation and management

### Administration & Management
- ✅ **User Management**: Role-based access control system
- ✅ **Customer Management**: Complete customer database
- ✅ **System Settings**: Comprehensive configuration management
- ✅ **Archive & Reports**: Data archiving and comprehensive reporting

### Quality & Compliance
- ✅ **ISO 17025 Compliance**: Full compliance with international standards
- ✅ **Audit Trail**: Complete activity logging and tracking
- ✅ **Data Integrity**: Secure data management and validation
- ✅ **Quality Assurance**: Built-in quality control measures

### Technical Features
- ✅ **Responsive Design**: Works on all devices and screen sizes
- ✅ **Real-time Dashboard**: Live statistics and monitoring
- ✅ **Processing Flow Visualization**: Visual sample status tracking
- ✅ **Interactive Forms**: User-friendly sample registration interface
- ✅ **Clean URL Routing**: SEO-friendly and user-friendly URLs
- ✅ **Document Print Preview**: Professional documentation preview system
- ✅ **Public Sample Tracking**: Customer sample tracking interface
- ✅ **API Integration**: RESTful APIs for future integrations
- ✅ **Security**: Advanced authentication and authorization
- ✅ **Performance**: Optimized for high-volume laboratory operations

### Security Features
- ✅ Role-based access control
- ✅ Session management dengan timeout otomatis
- ✅ Brute force protection dengan lockout system
- ✅ CSRF token protection untuk semua form
- ✅ Complete audit trail dengan timestamping
- ✅ File upload validation dengan virus scanning
- ✅ SQL injection protection dengan prepared statements
- ✅ XSS protection dengan input sanitization
- ✅ Security headers untuk web protection

### User Roles
| Role | Code | Description | Permissions |
|------|------|-------------|-------------|
| Kepala Laboratorium | SUPERVISOR | Full access | all |
| Koordinator Teknis | TECH_AUDITOR | Technical review | 5 |
| Koordinator Mutu | QUALITY_AUDITOR | Quality review | 5 |
| Penyelia | SUPERVISOR_ANALYST | Supervision | 1,3,5,8,9 |
| Teknisi Laboratorium | ANALYST | Testing | 4 |
| Costumer Service | ADMIN | Administration | 1,2,6,7,8,9 |
| Developer | DEVEL | Developer | all |


### Role Hierarchy
1. **ADMIN** - Administrasi (Costumer Service)
2. **SUPERVISOR** - Laboratory Supervisor (Management Access)
3. **SUPERVISOR_ANALYST** - Senior Analyst (Supervision + Testing)
4. **ANALYST** - Laboratory Technician (Testing Only)
5. **TECH_AUDITOR** - Technical Coordinator (QC Access)
6. **QUALITY_AUDITOR** - Quality Coordinator (QA Access)
7. **DEVEL** - Developer Full Access (Reports & Tracking)

### Permission Matrix
| Module | ADMIN | SUPERVISOR | ANALYST |  DEVEL |
|--------|-------|------------|---------|------------|
| List New Sampel | ✅ | ✅ | ❌ | ✅ |
| Codification |✅|❌|❌|✅|
| Assignment | ❌ | ✅ | ❌ | ✅ |
| Testing | ❌ | ❌ | ✅ | ✅ |
| Review | ❌ | ✅ | ❌ | ✅ |
| Certificates | ✅ | ✅ | ❌ | ✅ |
| Invoice | ✅ | ✅ | ❌ | ✅|
| Parameter | ❌ |✅ | ❌ | ✅ |
| User Management | ❌ | ✅ | ❌ | ✅ |
| System Settings | ❌ | ❌ | ❌ | ✅ |

## Installation & Requirements

### System Requirements (Laravel 12)
- **PHP 8.2+ (Critical)** - Laravel 12 minimum requirement
- **Laravel Framework 12.x** - Latest version with performance improvements
- **MySQL 8.0+ / MariaDB 10.4+** - Database with full UTF8MB4 support
- **Composer 2.x** - Dependency manager for PHP
- **Web Server** - Apache 2.4+ / Nginx 1.18+ / PHP Built-in Server
- **❌ Node.js NOT Required** - Pure PHP implementation

### Quick Installation Guide
```bash
# 1. Verify PHP version (Must be 8.2+)
php --version

# 2. Clone project
git clone [repository] lims && cd lims

# 3. Install dependencies (PHP only)
composer install --optimize-autoloader

# 4. Setup environment  
cp .env.example .env
php artisan key:generate --force

# 5. Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=lims
DB_USERNAME=root  
DB_PASSWORD=your_password

# 6. Create and populate database
mysql -u root -p -e "CREATE DATABASE lims CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p lims < sql/install.sql

# 7. Optimize Laravel 12
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Start development server
php artisan serve
```

### Access Information
- **Application URL**: `http://127.0.0.1:8000`
- **Default Login**: `devel/123456` (Developer access)
- **Health Check**: `http://127.0.0.1:8000/up` (Laravel 12 built-in)

### Laravel 12 Specific Features
- ✅ **Enhanced Performance** - Improved caching and optimization
- ✅ **New Bootstrap Structure** - Modern application initialization
- ✅ **Built-in Health Checks** - `/up` endpoint for monitoring
- ✅ **Improved Middleware** - Better request processing
- ✅ **Enhanced Security** - Updated validation and protection
- ✅ **PHP 8.2+ Optimizations** - Modern PHP feature utilization

## Pure PHP Architecture Benefits

### No Build Tools Required
This LIMS implementation deliberately avoids JavaScript build tools for several advantages:

#### Development Benefits:
- ✅ **Instant Changes** - Edit files, refresh browser immediately
- ✅ **Simpler Workflow** - No compilation or build steps
- ✅ **Easier Debugging** - Direct source file inspection
- ✅ **Faster Setup** - No npm install or node_modules

#### Deployment Benefits:
- ✅ **Smaller Footprint** - Only PHP files needed
- ✅ **Shared Hosting Compatible** - Works on basic PHP hosting
- ✅ **No Build Server** - Deploy directly from repository
- ✅ **Reduced Complexity** - No Node.js or npm on production

#### Maintenance Benefits:
- ✅ **Direct Editing** - Modify CSS/JS files directly
- ✅ **No Version Conflicts** - No npm dependency hell
- ✅ **Clearer Dependencies** - Only Composer packages
- ✅ **Simpler Updates** - Standard PHP update process

### Asset Loading Strategy
```html
<!-- Bootstrap 5.3.2 via CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Alpine.js for interactivity -->
<script defer src="https://unpkg.com/alpinejs@3.13.3/dist/cdn.min.js"></script>

<!-- jQuery for DOM manipulation -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
```

## Troubleshooting Guide

### Laravel 12 Common Issues

#### 1. PHP Version Incompatibility
```bash
# Problem: Laravel 12 requires PHP 8.2+
# Check version:
php --version

# If PHP < 8.2, upgrade:
# Ubuntu/Debian:
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-common

# CentOS/RHEL:
sudo dnf module enable php:8.2
sudo dnf install php php-cli php-common
```

#### 2. Missing PHP Extensions
```bash
# Check required extensions:
php -m | grep -E "(pdo|mysql|mbstring|openssl|tokenizer|xml|ctype|json|bcmath|fileinfo|curl|zip)"

# Install missing extensions (Ubuntu/Debian):
sudo apt install php8.2-pdo php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip
```

#### 3. Database Connection Issues
```bash
# Test database connection:
php artisan tinker
>>> DB::connection()->getPdo();
>>> DB::select('SHOW TABLES');
>>> exit

# Check MySQL service:
sudo systemctl status mysql

# Verify database exists:
mysql -u root -p -e "SHOW DATABASES LIKE 'lims';"
```

#### 4. Migration Problems
```bash
# Reset migrations completely:
php artisan migrate:reset
php artisan migrate:fresh --seed --force

# Check migration status:
php artisan migrate:status

# Run specific migration:
php artisan migrate --path=/database/migrations/specific_migration.php
```

#### 5. Cache and Permission Issues
```bash
# Clear all Laravel 12 caches:
php artisan optimize:clear

# Fix file permissions:
chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache

# Create missing directories:
mkdir -p storage/logs storage/framework/{cache,sessions,views}
```

#### 6. Composer Problems
```bash
# Update Composer:
composer self-update

# Clear Composer cache:
composer clear-cache

# Reinstall dependencies:
rm -rf vendor composer.lock
composer install --optimize-autoloader
```

### Production Deployment Issues

#### 1. Performance Optimization
```bash
# Optimize for production:
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Enable OPcache in php.ini:
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
```

#### 2. Security Configuration
```bash
# Set secure file permissions:
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache

# Secure sensitive files:
chmod 600 .env
chmod 644 composer.json composer.lock
```

#### 3. Web Server Configuration
```apache
# Apache .htaccess for Laravel 12
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

```nginx
# Nginx configuration for Laravel 12
server {
    listen 80;
    server_name lims.example.com;
    root /path/to/lims/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Maintenance Commands

#### Laravel 12 Specific Commands
```bash
# Health check
php artisan lims:health-check

# Database maintenance
php artisan lims:backup-database
php artisan lims:optimize-database
php artisan lims:cleanup --days=30

# Cache management
php artisan optimize:clear     # Clear all caches
php artisan optimize          # Cache for production

# Queue management (if using queues)
php artisan queue:work --timeout=300
php artisan queue:retry all
```

#### System Monitoring
```bash
# Check Laravel version
php artisan --version

# View routes
php artisan route:list

# Check configuration
php artisan config:show

# Database status
php artisan migrate:status

# Storage status
du -sh storage/
ls -la storage/
```

---

**LIMS Development Team** - Laboratory Integration Management System v2.0.0  
*Laravel 12 Compatible - Pure PHP Solution - December 2024*

**🚀 READY FOR PRODUCTION - NO BUILD TOOLS REQUIRED**

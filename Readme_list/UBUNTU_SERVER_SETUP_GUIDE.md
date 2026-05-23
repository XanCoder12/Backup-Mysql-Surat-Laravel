# 🐧 Ubuntu Server Setup Guide - Persuratan BP Suml
## Panduan Lengkap dari Scratch hingga Production Ready

**Target:** Deploy aplikasi Laravel di Ubuntu Server (dari bare metal)  
**Estimated Time:** 2-3 jam  
**Difficulty:** Intermediate  
**Last Updated:** May 2026

---

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Initial Server Setup](#initial-server-setup)
3. [Install Required Software](#install-required-software)
4. [Configure PHP & Web Server](#configure-php--web-server)
5. [Database Setup](#database-setup)
6. [Deploy Application](#deploy-application)
7. [SSL/HTTPS Setup](#ssltls-setup)
8. [Firewall & Security](#firewall--security)
9. [Monitoring & Maintenance](#monitoring--maintenance)
10. [Troubleshooting](#troubleshooting)

---

## Prerequisites

### Hardware Requirements (Minimum)
```
- CPU: 2 cores
- RAM: 4GB (2GB minimum)
- Storage: 50GB (SSD recommended)
- Network: Stable internet connection
```

### What You Need
- [ ] Ubuntu Server 22.04 LTS (or 24.04)
- [ ] SSH access to server
- [ ] Domain name (e.g., persuratan.bp.suml.com)
- [ ] Database backup or fresh setup
- [ ] Application source code (Git repository)

---

## Initial Server Setup

### Step 1: Connect to Server
```bash
ssh root@your_server_ip
# Or dengan ssh key
ssh -i /path/to/key.pem ubuntu@your_server_ip
```

### Step 2: Update System
```bash
# Update package list
sudo apt update

# Upgrade installed packages
sudo apt upgrade -y

# Install essential tools
sudo apt install -y curl wget git vim htop ufw fail2ban
```

### Step 3: Create Application User
```bash
# Create user untuk aplikasi (bukan root)
sudo useradd -m -s /bin/bash -G sudo persuratan

# Set password
sudo passwd persuratan

# Login dengan user baru
su - persuratan
```

### Step 4: Setup SSH Keys (Optional but Recommended)
```bash
# On your local machine, generate key pair
ssh-keygen -t rsa -b 4096 -f ~/.ssh/persuratan

# Copy public key to server
ssh-copy-id -i ~/.ssh/persuratan.pub persuratan@your_server_ip

# Test login
ssh -i ~/.ssh/persuratan persuratan@your_server_ip
```

---

## Install Required Software

### Step 1: Install PHP & Extensions

```bash
# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.2+ dengan extensions
sudo apt install -y \
    php8.3 \
    php8.3-fpm \
    php8.3-mysql \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-curl \
    php8.3-gd \
    php8.3-zip \
    php8.3-intl \
    php8.3-bcmath \
    php8.3-redis \
    php8.3-opcache

# Verify PHP installation
php -v
php -m
```

### Step 2: Install MySQL/MariaDB

```bash
# Install MySQL Server 8.0
sudo apt install -y mysql-server

# Secure installation
sudo mysql_secure_installation

# Create database & user
sudo mysql -u root -p

# Di dalam MySQL prompt:
CREATE DATABASE db_persuratan_bpsuml CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'persuratan'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON db_persuratan_bpsuml.* TO 'persuratan'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Install Nginx

```bash
# Install Nginx
sudo apt install -y nginx

# Start dan enable service
sudo systemctl start nginx
sudo systemctl enable nginx

# Check status
sudo systemctl status nginx
```

### Step 4: Install Composer

```bash
# Download Composer installer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

# Install Composer
php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Verify
composer --version

# Cleanup
rm composer-setup.php
```

### Step 5: Install Node.js & NPM

```bash
# Install Node.js 20 LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Verify
node --version
npm --version

# Install Vite
npm install -g vite
```

### Step 6: Install Redis (Optional but Recommended)

```bash
# Install Redis
sudo apt install -y redis-server

# Start service
sudo systemctl start redis-server
sudo systemctl enable redis-server

# Test Redis
redis-cli ping
# Output: PONG
```

---

## Configure PHP & Web Server

### Step 1: Configure PHP-FPM

```bash
# Edit PHP-FPM configuration
sudo nano /etc/php/8.3/fpm/php.ini

# Update these settings:
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
memory_limit = 512M
```

```bash
# Edit PHP-FPM pool configuration
sudo nano /etc/php/8.3/fpm/pool.d/www.conf

# Update these settings (cari dan uncomment):
listen = /run/php/php8.3-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660
user = www-data
group = www-data
```

```bash
# Restart PHP-FPM
sudo systemctl restart php8.3-fpm
```

### Step 2: Configure Nginx

```bash
# Remove default config
sudo rm /etc/nginx/sites-enabled/default

# Create config untuk aplikasi
sudo nano /etc/nginx/sites-available/persuratan.bp.suml.com

# Paste konfigurasi di bawah ini:
```

```nginx
# /etc/nginx/sites-available/persuratan.bp.suml.com

server {
    listen 80;
    listen [::]:80;
    
    server_name persuratan.bp.suml.com www.persuratan.bp.suml.com;
    root /var/www/persuratan/public;
    
    index index.php index.html index.htm;
    
    # Logs
    access_log /var/log/nginx/persuratan_access.log;
    error_log /var/log/nginx/persuratan_error.log;
    
    # SSL redirect (nanti setelah setup SSL)
    # Uncomment setelah setup SSL:
    # return 301 https://$server_name$request_uri;
    
    # Static files cache
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP-FPM
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Laravel-specific headers
        fastcgi_buffer_size 32k;
        fastcgi_buffers 32 32k;
    }
    
    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
    
    # Deny access to storage
    location ~ /storage/ {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/persuratan.bp.suml.com \
           /etc/nginx/sites-enabled/persuratan.bp.suml.com

# Test Nginx config
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

### Step 3: Configure File Permissions

```bash
# Setup application directory
sudo mkdir -p /var/www/persuratan
sudo chown -R www-data:www-data /var/www/persuratan
sudo chmod -R 775 /var/www/persuratan

# Setup storage permissions
sudo chmod -R 775 /var/www/persuratan/storage
sudo chmod -R 775 /var/www/persuratan/bootstrap/cache
```

---

## Database Setup

### Step 1: Configure Database Connection

```bash
# SSH ke server dan masuk ke directory aplikasi
ssh persuratan@your_server_ip
cd /var/www/persuratan

# Copy .env file
cp .env.example .env

# Edit .env
nano .env

# Update database settings:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_persuratan_bpsuml
DB_USERNAME=persuratan
DB_PASSWORD=strong_password_here

# Update aplikasi settings:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://persuratan.bp.suml.com

# Update session & cache
SESSION_DRIVER=database
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Step 2: Generate App Key

```bash
php artisan key:generate
```

### Step 3: Import Database (jika ada backup) atau Fresh Migration

```bash
# Option A: Fresh migration
php artisan migrate --force

# Option B: Restore dari backup
mysql -u persuratan -p db_persuratan_bpsuml < backup.sql

# Seed data (optional)
php artisan db:seed --force
```

---

## Deploy Application

### Step 1: Clone Repository

```bash
# Jika belum clone
cd /var/www
sudo rm -rf persuratan
sudo git clone https://github.com/yourorg/persuratan.git persuratan
sudo chown -R www-data:www-data persuratan
```

### Step 2: Install Dependencies

```bash
cd /var/www/persuratan

# PHP dependencies
composer install --no-dev --optimize-autoloader

# NPM dependencies
npm install
```

### Step 3: Build Frontend Assets

```bash
# Build production assets
npm run build
```

### Step 4: Cache Configuration

```bash
# Cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Step 5: Set File Permissions (Security)

```bash
# Set proper ownership
sudo chown -R www-data:www-data /var/www/persuratan

# Set proper permissions
sudo find /var/www/persuratan -type f -exec chmod 644 {} \;
sudo find /var/www/persuratan -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/persuratan/storage
sudo chmod -R 775 /var/www/persuratan/bootstrap/cache
```

---

## SSL/TLS Setup

### Step 1: Install Certbot

```bash
sudo apt install -y certbot python3-certbot-nginx
```

### Step 2: Generate SSL Certificate (Let's Encrypt - Free)

```bash
# Generate certificate
sudo certbot certonly --nginx -d persuratan.bp.suml.com -d www.persuratan.bp.suml.com

# Follow prompts:
# - Enter email
# - Agree to terms
# - Share email (optional)
```

### Step 3: Update Nginx Configuration

```bash
# Edit Nginx config
sudo nano /etc/nginx/sites-available/persuratan.bp.suml.com

# Replace dengan config di bawah:
```

```nginx
# /etc/nginx/sites-available/persuratan.bp.suml.com

# Redirect HTTP ke HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name persuratan.bp.suml.com www.persuratan.bp.suml.com;
    return 301 https://$server_name$request_uri;
}

# HTTPS Server Block
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    
    server_name persuratan.bp.suml.com www.persuratan.bp.suml.com;
    root /var/www/persuratan/public;
    
    # SSL Certificates
    ssl_certificate /etc/letsencrypt/live/persuratan.bp.suml.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/persuratan.bp.suml.com/privkey.pem;
    
    # SSL Security Headers
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    
    # HSTS (HTTP Strict Transport Security)
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    
    index index.php index.html index.htm;
    
    # Logs
    access_log /var/log/nginx/persuratan_access.log;
    error_log /var/log/nginx/persuratan_error.log;
    
    # Static files cache
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP-FPM
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_buffer_size 32k;
        fastcgi_buffers 32 32k;
    }
    
    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
    
    # Deny access to storage
    location ~ /storage/ {
        deny all;
    }
}
```

```bash
# Test & restart Nginx
sudo nginx -t
sudo systemctl restart nginx
```

### Step 4: Auto-Renew SSL Certificate

```bash
# Test auto-renewal
sudo certbot renew --dry-run

# Enable auto-renewal
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer
```

---

## Firewall & Security

### Step 1: Configure UFW Firewall

```bash
# Enable UFW
sudo ufw enable

# Default rules
sudo ufw default deny incoming
sudo ufw default allow outgoing

# Allow SSH
sudo ufw allow 22/tcp

# Allow HTTP
sudo ufw allow 80/tcp

# Allow HTTPS
sudo ufw allow 443/tcp

# Allow MySQL (only from localhost)
sudo ufw allow from 127.0.0.1 to any port 3306

# Check status
sudo ufw status
```

### Step 2: Configure Fail2Ban (Brute Force Protection)

```bash
# Fail2Ban sudah installed, konfigurasi
sudo nano /etc/fail2ban/jail.local

# Paste konfigurasi:
```

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true
port = 22
logpath = /var/log/auth.log

[sshd-ddos]
enabled = true
port = 22
logpath = /var/log/auth.log

[nginx-http-auth]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log

[nginx-noscript]
enabled = true
port = http,https
logpath = /var/log/nginx/access.log

[nginx-badbots]
enabled = true
port = http,https
logpath = /var/log/nginx/access.log

[nginx-noproxy]
enabled = true
port = http,https
logpath = /var/log/nginx/access.log

[nginx-limit-req]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log
```

```bash
# Restart Fail2Ban
sudo systemctl restart fail2ban

# Check status
sudo fail2ban-client status
```

### Step 3: Security Hardening

```bash
# Disable root login via SSH
sudo nano /etc/ssh/sshd_config

# Find dan ubah:
PermitRootLogin no
PasswordAuthentication no
PubkeyAuthentication yes
Protocol 2
X11Forwarding no

# Restart SSH
sudo systemctl restart ssh

# Update sudoers untuk user aplikasi (optional)
sudo visudo
# Add at the end:
# persuratan ALL=(ALL) NOPASSWD: /usr/bin/systemctl restart nginx
# persuratan ALL=(ALL) NOPASSWD: /usr/bin/systemctl restart php8.3-fpm
```

---

## Monitoring & Maintenance

### Step 1: Setup Log Monitoring

```bash
# Check PHP-FPM logs
tail -f /var/log/php8.3-fpm.log

# Check Nginx logs
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log

# Check application logs
tail -f /var/www/persuratan/storage/logs/laravel.log
```

### Step 2: Setup System Monitoring

```bash
# Monitor real-time
htop

# Check disk usage
df -h

# Check memory
free -h

# Check CPU load
uptime
```

### Step 3: Automated Backups

```bash
# Create backup script
sudo nano /usr/local/bin/backup-persuratan.sh

# Paste script:
```

```bash
#!/bin/bash

BACKUP_DIR="/var/backups/persuratan"
DB_NAME="db_persuratan_bpsuml"
DB_USER="persuratan"
APP_DIR="/var/www/persuratan"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p$DB_PASSWORD $DB_NAME > $BACKUP_DIR/db_$DATE.sql
gzip $BACKUP_DIR/db_$DATE.sql

# Backup application files (optional)
# tar -czf $BACKUP_DIR/app_$DATE.tar.gz $APP_DIR/storage

# Remove old backups (keep last 7 days)
find $BACKUP_DIR -name "db_*.sql.gz" -mtime +7 -delete

# Send to remote storage (optional)
# aws s3 cp $BACKUP_DIR/db_$DATE.sql.gz s3://your-bucket/backups/

echo "Backup completed: $DATE"
```

```bash
# Make executable
sudo chmod +x /usr/local/bin/backup-persuratan.sh

# Add to crontab (backup setiap hari jam 2 pagi)
sudo crontab -e

# Add this line:
0 2 * * * /usr/local/bin/backup-persuratan.sh >> /var/log/persuratan-backup.log 2>&1
```

### Step 4: Laravel Queue (Background Jobs)

```bash
# Setup Supervisor untuk queue worker
sudo apt install -y supervisor

# Create config
sudo nano /etc/supervisor/conf.d/persuratan-worker.conf

# Paste:
```

```ini
[program:persuratan-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/persuratan/artisan queue:work redis --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/persuratan-worker.log
user=www-data
stopasgroup=true
stopwaitsecs=60
```

```bash
# Restart Supervisor
sudo systemctl restart supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start persuratan-worker:*

# Check status
sudo supervisorctl status
```

---

## Troubleshooting

### Issue: "502 Bad Gateway"

```bash
# Check PHP-FPM status
sudo systemctl status php8.3-fpm

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm

# Check error logs
tail -f /var/log/php8.3-fpm.log
tail -f /var/log/nginx/error.log
```

### Issue: "Permission Denied" pada Storage

```bash
# Fix permissions
sudo chown -R www-data:www-data /var/www/persuratan/storage
sudo chmod -R 775 /var/www/persuratan/storage

# Fix bootstrap/cache
sudo chown -R www-data:www-data /var/www/persuratan/bootstrap/cache
sudo chmod -R 775 /var/www/persuratan/bootstrap/cache
```

### Issue: Database Connection Error

```bash
# Test MySQL connection
mysql -u persuratan -p -h 127.0.0.1 db_persuratan_bpsuml

# Check .env file
cat /var/www/persuratan/.env | grep DB_

# Check MySQL is running
sudo systemctl status mysql
```

### Issue: SSL Certificate Error

```bash
# Verify certificate
sudo certbot certificates

# Renew manually
sudo certbot renew --force-renewal

# Check Nginx config
sudo nginx -t

# View certificate details
openssl x509 -in /etc/letsencrypt/live/persuratan.bp.suml.com/fullchain.pem -text -noout
```

### Issue: High Memory/CPU Usage

```bash
# Check processes
top -b -n 1 | head -n 20

# Kill hung process
kill -9 <pid>

# Check database queries
# Di MySQL:
SHOW FULL PROCESSLIST;

# Check slow queries
tail -f /var/log/mysql/slow.log
```

---

## Final Verification Checklist

- [ ] Server accessible via HTTPS
- [ ] SSL certificate valid (no warnings)
- [ ] Database connection working
- [ ] Application loads without errors
- [ ] File uploads working
- [ ] Notifications popup showing
- [ ] Admin dashboard accessible
- [ ] Database backups scheduled
- [ ] Logs being recorded
- [ ] Firewall configured correctly
- [ ] SSH secured (key-based only)
- [ ] SSL auto-renewal configured
- [ ] Monitoring/alerts setup

---

## Quick Reference Commands

```bash
# Check service status
sudo systemctl status nginx
sudo systemctl status php8.3-fpm
sudo systemctl status mysql
sudo systemctl status redis-server

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm
sudo systemctl restart mysql
sudo systemctl restart redis-server

# View logs
tail -f /var/log/nginx/error.log
tail -f /var/log/php8.3-fpm.log
tail -f /var/www/persuratan/storage/logs/laravel.log

# Clear application cache
cd /var/www/persuratan
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Database maintenance
php artisan tinker
php artisan migrate
php artisan db:seed

# Update application
cd /var/www/persuratan
git pull origin main
composer install --no-dev
npm install && npm run build
php artisan migrate --force
php artisan cache:clear
```

---

## Support & Further Reading

- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Nginx Best Practices](https://nginx.org/en/docs/)
- [MySQL Security](https://dev.mysql.com/doc/mysql-security/)
- [Ubuntu Server Guide](https://ubuntu.com/server/docs)
- [Let's Encrypt Documentation](https://letsencrypt.org/docs/)

---

**Selamat! 🎉 Server Anda sekarang siap untuk production.**

Jika ada pertanyaan atau masalah, baca section "Troubleshooting" di atas atau hubungi administrator sistem Anda.


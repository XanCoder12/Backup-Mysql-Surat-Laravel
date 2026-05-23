# ⚡ Ubuntu Server Setup - Quick Checklists & Scripts

**Gunakan file ini untuk quick reference selama deployment**

---

## 🚀 QUICK START - 30 Minute Setup

Untuk setup cepat tanpa banyak detail. Lakukan step-by-step:

```bash
# ============================================
# 1. INITIAL SETUP (5 menit)
# ============================================
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl wget git vim htop ufw fail2ban

# Create app user
sudo useradd -m -s /bin/bash -G sudo persuratan
sudo passwd persuratan

# ============================================
# 2. INSTALL STACK (10 menit)
# ============================================

# PHP
sudo add-apt-repository ppa:ondrej/php -y && sudo apt update
sudo apt install -y php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl php8.3-gd php8.3-zip php8.3-redis php8.3-bcmath

# MySQL
sudo apt install -y mysql-server

# Nginx
sudo apt install -y nginx

# Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer && rm composer-setup.php

# ============================================
# 3. DATABASE SETUP (5 menit)
# ============================================

# Login MySQL
sudo mysql -u root

# Run di MySQL:
# CREATE DATABASE db_persuratan_bpsuml CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
# CREATE USER 'persuratan'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD';
# GRANT ALL PRIVILEGES ON db_persuratan_bpsuml.* TO 'persuratan'@'localhost';
# FLUSH PRIVILEGES;
# EXIT;

# ============================================
# 4. DEPLOY APPLICATION (10 menit)
# ============================================

cd /var/www
sudo mkdir -p persuratan
sudo chown -R www-data:www-data persuratan
cd persuratan

# Clone dari Git
sudo -u www-data git clone https://github.com/yourrepo/persuratan.git .

# Install dependencies
sudo -u www-data composer install --no-dev
sudo -u www-data npm install

# Setup env
sudo -u www-data cp .env.example .env
# Edit .env dengan database credentials

# Generate key & migrate
sudo -u www-data php artisan key:generate
sudo -u www-data php artisan migrate --force

# Build assets
sudo -u www-data npm run build

# Cache
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache

# Permissions
sudo chmod -R 775 storage bootstrap/cache

# ============================================
# 5. NGINX SETUP (5 menit)
# ============================================

# Copy Nginx config dari guide dan enable
# Atau gunakan script di bawah
```

---

## 📋 STEP-BY-STEP SCRIPTS

### Script 1: Auto Setup (Run as root)

```bash
#!/bin/bash
# save as: ~/setup-persuratan.sh
# run: sudo bash ~/setup-persuratan.sh

set -e

echo "🚀 Starting Persuratan Server Setup..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# ============================================
# 1. UPDATE SYSTEM
# ============================================
echo -e "${YELLOW}[1/6] Updating system...${NC}"
apt update && apt upgrade -y
apt install -y curl wget git vim htop ufw fail2ban

# ============================================
# 2. CREATE APP USER
# ============================================
echo -e "${YELLOW}[2/6] Creating application user...${NC}"
useradd -m -s /bin/bash -G sudo persuratan || echo "User already exists"
echo "persuratan:persuratan123" | chpasswd
sed -i '/%sudo.*ALL=(ALL)/a persuratan ALL=(ALL) NOPASSWD: /usr/bin/systemctl restart' /etc/sudoers

# ============================================
# 3. INSTALL STACK
# ============================================
echo -e "${YELLOW}[3/6] Installing PHP, MySQL, Nginx, Node.js...${NC}"

# PHP
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl php8.3-gd php8.3-zip php8.3-redis php8.3-bcmath php8.3-intl
systemctl enable php8.3-fpm
systemctl start php8.3-fpm

# MySQL
DEBIAN_FRONTEND=noninteractive apt install -y mysql-server
systemctl enable mysql
systemctl start mysql

# Nginx
apt install -y nginx
systemctl enable nginx
systemctl start nginx

# Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs
npm install -g vite

# Redis
apt install -y redis-server
systemctl enable redis-server
systemctl start redis-server

# Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php

echo -e "${GREEN}✓ Stack installed${NC}"

# ============================================
# 4. SETUP APPLICATION DIRECTORY
# ============================================
echo -e "${YELLOW}[4/6] Setting up application directory...${NC}"
mkdir -p /var/www/persuratan
chown -R www-data:www-data /var/www/persuratan
chmod -R 755 /var/www/persuratan

echo -e "${GREEN}✓ Directory ready at /var/www/persuratan${NC}"
echo -e "${YELLOW}   Next: Clone your repository here${NC}"

# ============================================
# 5. SETUP NGINX CONFIG
# ============================================
echo -e "${YELLOW}[5/6] Configuring Nginx...${NC}"

cat > /etc/nginx/sites-available/persuratan.bp.suml.com << 'EOF'
server {
    listen 80;
    listen [::]:80;
    server_name persuratan.bp.suml.com www.persuratan.bp.suml.com;
    root /var/www/persuratan/public;
    
    index index.php index.html index.htm;
    access_log /var/log/nginx/persuratan_access.log;
    error_log /var/log/nginx/persuratan_error.log;
    
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\. {
        deny all;
    }
}
EOF

rm -f /etc/nginx/sites-enabled/default
ln -s /etc/nginx/sites-available/persuratan.bp.suml.com /etc/nginx/sites-enabled/
nginx -t && systemctl restart nginx

echo -e "${GREEN}✓ Nginx configured${NC}"

# ============================================
# 6. SETUP FIREWALL
# ============================================
echo -e "${YELLOW}[6/6] Configuring Firewall...${NC}"
ufw --force enable
ufw default deny incoming
ufw default allow outgoing
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp

echo -e "${GREEN}✓ Firewall configured${NC}"

# ============================================
# SUMMARY
# ============================================
echo ""
echo -e "${GREEN}================================${NC}"
echo -e "${GREEN}✓ Setup Complete!${NC}"
echo -e "${GREEN}================================${NC}"
echo ""
echo "Next steps:"
echo "1. Clone repository: cd /var/www/persuratan && git clone ..."
echo "2. Setup .env file with database credentials"
echo "3. Run migrations: php artisan migrate --force"
echo "4. Build assets: npm run build"
echo "5. Setup SSL: sudo certbot certonly --nginx -d persuratan.bp.suml.com"
echo ""
echo "Check status:"
echo "  sudo systemctl status nginx"
echo "  sudo systemctl status php8.3-fpm"
echo "  sudo systemctl status mysql"
echo ""
```

---

### Script 2: Database Setup

```bash
#!/bin/bash
# save as: ~/setup-database.sh
# run: sudo bash ~/setup-database.sh

DB_NAME="db_persuratan_bpsuml"
DB_USER="persuratan"
DB_PASSWORD=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 16 | head -n 1)

echo "Creating database and user..."
echo "Username: $DB_USER"
echo "Password: $DB_PASSWORD"
echo "Database: $DB_NAME"

mysql -u root << EOF
CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
EXIT;
EOF

echo ""
echo "✓ Database created successfully!"
echo ""
echo "Save these credentials in .env file:"
echo "DB_CONNECTION=mysql"
echo "DB_HOST=127.0.0.1"
echo "DB_DATABASE=$DB_NAME"
echo "DB_USERNAME=$DB_USER"
echo "DB_PASSWORD=$DB_PASSWORD"
```

---

### Script 3: Deploy Application

```bash
#!/bin/bash
# save as: ~/deploy-persuratan.sh
# run: sudo bash ~/deploy-persuratan.sh

APP_DIR="/var/www/persuratan"
REPO_URL="https://github.com/yourorg/persuratan.git"

echo "🚀 Deploying Persuratan Application..."

cd $APP_DIR

echo "Pulling latest code..."
sudo -u www-data git pull origin main

echo "Installing PHP dependencies..."
sudo -u www-data composer install --no-dev --optimize-autoloader

echo "Installing NPM dependencies..."
sudo -u www-data npm install

echo "Building frontend assets..."
sudo -u www-data npm run build

echo "Caching configuration..."
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

echo "Running migrations..."
sudo -u www-data php artisan migrate --force

echo "Clearing caches..."
sudo -u www-data php artisan cache:clear

echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache

echo ""
echo "✓ Deployment complete!"
systemctl restart php8.3-fpm
echo "✓ PHP-FPM restarted"
```

---

### Script 4: SSL Setup

```bash
#!/bin/bash
# save as: ~/setup-ssl.sh
# run: sudo bash ~/setup-ssl.sh

DOMAIN="persuratan.bp.suml.com"

echo "Setting up SSL for $DOMAIN..."

# Install Certbot
apt install -y certbot python3-certbot-nginx

# Generate certificate
certbot certonly --nginx -d $DOMAIN -d www.$DOMAIN

# Update Nginx config
cat > /etc/nginx/sites-available/$DOMAIN << EOF
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN www.$DOMAIN;
    return 301 https://\$server_name\$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name $DOMAIN www.$DOMAIN;
    root /var/www/persuratan/public;
    
    ssl_certificate /etc/letsencrypt/live/$DOMAIN/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/$DOMAIN/privkey.pem;
    
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    
    index index.php;
    access_log /var/log/nginx/persuratan_access.log;
    error_log /var/log/nginx/persuratan_error.log;
    
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    location ~ \.php$ {
        try_files \$uri =404;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF

# Test and restart
nginx -t && systemctl restart nginx

# Auto-renewal
systemctl enable certbot.timer
systemctl start certbot.timer

echo "✓ SSL setup complete!"
echo "Certificate: /etc/letsencrypt/live/$DOMAIN/"
echo "Auto-renewal: Enabled"
```

---

### Script 5: Backup Automation

```bash
#!/bin/bash
# save as: /usr/local/bin/backup-persuratan.sh
# make executable: sudo chmod +x /usr/local/bin/backup-persuratan.sh

BACKUP_DIR="/var/backups/persuratan"
DB_NAME="db_persuratan_bpsuml"
DB_USER="persuratan"
DB_PASSWORD="your_password"  # Change this!
APP_DIR="/var/www/persuratan"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

echo "[$(date)] Starting backup..."

# Backup database
mysqldump -u $DB_USER -p$DB_PASSWORD $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup storage (optional)
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz -C $APP_DIR storage/

# Remove old backups (keep 7 days)
find $BACKUP_DIR -type f -mtime +7 -delete

# Check backup size
SIZE=$(du -sh $BACKUP_DIR | cut -f1)

echo "[$(date)] Backup completed. Total size: $SIZE"
echo "[$(date)] Files in backup: $(ls -1 $BACKUP_DIR | wc -l)"

# Optional: Send to cloud storage
# aws s3 sync $BACKUP_DIR s3://your-bucket/persuratan/ --delete

# Optional: Send notification
# Send email atau Slack notification
```

Add to crontab:
```bash
# sudo crontab -e
# Add this line (backup every day at 2 AM):
0 2 * * * /usr/local/bin/backup-persuratan.sh >> /var/log/persuratan-backup.log 2>&1
```

---

## 🔍 TROUBLESHOOTING QUICK FIXES

### 502 Bad Gateway
```bash
sudo systemctl restart php8.3-fpm
sudo nginx -t && sudo systemctl restart nginx
tail -f /var/log/nginx/error.log
```

### Permission Denied
```bash
sudo chown -R www-data:www-data /var/www/persuratan
sudo chmod -R 755 /var/www/persuratan
sudo chmod -R 775 /var/www/persuratan/storage
sudo chmod -R 775 /var/www/persuratan/bootstrap/cache
```

### Database Connection Error
```bash
# Check .env
cat /var/www/persuratan/.env | grep DB_

# Test MySQL
mysql -u persuratan -p db_persuratan_bpsuml -e "SELECT 1"

# Restart MySQL
sudo systemctl restart mysql
```

### High Memory/CPU
```bash
top -b -n 1 | head -20
sudo systemctl restart php8.3-fpm
sudo systemctl restart mysql
```

### View Logs
```bash
tail -f /var/log/nginx/error.log
tail -f /var/log/php8.3-fpm.log
tail -f /var/www/persuratan/storage/logs/laravel.log
journalctl -u nginx -f
```

---

## 📊 MONITORING COMMANDS

```bash
# System status
free -h                              # Memory
df -h                                # Disk
top                                  # Processes
uptime                               # Load average

# Service status
sudo systemctl status nginx
sudo systemctl status php8.3-fpm
sudo systemctl status mysql
sudo systemctl status redis-server

# Nginx stats
sudo tail -100 /var/log/nginx/access.log | awk '{print $1}' | sort | uniq -c | sort -rn

# MySQL connections
mysql -u persuratan -p -e "SHOW PROCESSLIST;"

# Redis check
redis-cli ping
redis-cli info stats
```

---

## 🔐 SECURITY CHECKLIST

- [ ] SSH key-based authentication enabled
- [ ] Password authentication disabled in SSH
- [ ] Firewall (UFW) enabled and configured
- [ ] Fail2Ban running and configured
- [ ] SSL certificate installed and auto-renewal enabled
- [ ] .env file not readable by others
- [ ] storage/ directory not publicly accessible
- [ ] database backups scheduled
- [ ] logs monitored regularly
- [ ] sudo password required for privilege escalation

---

## 📞 COMMON ISSUES & SOLUTIONS

| Issue | Solution |
|-------|----------|
| Connection refused | Check firewall: `ufw status` |
| Site not loading | Check Nginx: `sudo nginx -t` |
| Database errors | Check MySQL: `sudo systemctl status mysql` |
| File permissions | Run: `sudo chmod -R 775 storage bootstrap/cache` |
| SSL errors | Check certificate: `sudo certbot certificates` |
| High RAM usage | Restart PHP: `sudo systemctl restart php8.3-fpm` |
| Slow website | Check: `top`, `df -h`, slow query logs |

---

**Last Updated:** May 2026  
**For detailed guide, see:** UBUNTU_SERVER_SETUP_GUIDE.md


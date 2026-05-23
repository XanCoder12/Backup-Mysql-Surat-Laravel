# 🎯 Ubuntu Server Deployment - START HERE

**Panduan lengkap untuk deploy aplikasi Persuratan ke Ubuntu Server dari nol (from scratch)**

---

## 📚 Ada 2 Dokumen untuk Referensi:

### 1. **UBUNTU_SERVER_SETUP_GUIDE.md** (Detailed - Baca ini dulu!)
   - Penjelasan lengkap setiap step
   - Best practices & security hardening
   - Monitoring & maintenance
   - Troubleshooting guide
   
   **Gunakan untuk:** Pertama kali setup, saat ada problem, maintenance

### 2. **QUICK_UBUNTU_SCRIPTS.md** (Scripts & Quick Reference)
   - Ready-to-use bash scripts
   - Quick checklists
   - Common issues & fixes
   - Copy-paste commands
   
   **Gunakan untuk:** Fast deployment, quick reference, automation

---

## ⚡ Quick Start (30 Minutes)

Jika sudah pernah setup server, ikuti ini:

### Step 1: Login ke Server
```bash
ssh root@your_server_ip
# atau jika sudah punya user:
ssh ubuntu@your_server_ip
```

### Step 2: Run Auto Setup Script
```bash
# Download & run setup script
curl -O https://raw.githubusercontent.com/yourrepo/persuratan/main/setup-persuratan.sh
sudo bash setup-persuratan.sh

# Atau copy script dari QUICK_UBUNTU_SCRIPTS.md dan jalankan
```

### Step 3: Configure & Deploy
```bash
cd /var/www/persuratan

# Clone repository
git clone https://github.com/yourrepo/persuratan.git .

# Setup environment
cp .env.example .env
nano .env  # Update database & URL

# Install & build
composer install --no-dev
npm install && npm run build

# Run migrations
php artisan migrate --force
```

### Step 4: Setup SSL
```bash
sudo certbot certonly --nginx -d persuratan.bp.suml.com
# Update Nginx config dengan SSL
# Lihat QUICK_UBUNTU_SCRIPTS.md -> Script 4
```

✅ **Done! Server siap production**

---

## 📋 CHECKLIST - Sebelum Mulai

- [ ] Sudah dapat Ubuntu Server dengan akses root/sudo
- [ ] Sudah punya domain name (persuratan.bp.suml.com)
- [ ] Sudah punya Git repository dengan code
- [ ] Network connectivity OK
- [ ] Sudah backup data penting (jika ada server lama)

---

## 🔧 DETAILED SETUP (Jika script tidak work)

Follow step-by-step di **UBUNTU_SERVER_SETUP_GUIDE.md**:

1. Initial Server Setup (5 min)
   - Connect ke server
   - Update sistem
   - Create app user
   - Setup SSH keys

2. Install Required Software (10 min)
   - PHP 8.3 + extensions
   - MySQL/MariaDB
   - Nginx
   - Composer & Node.js
   - Redis (optional)

3. Configure PHP & Nginx (10 min)
   - Setup PHP-FPM
   - Configure Nginx virtual host
   - Set file permissions

4. Database Setup (5 min)
   - Create database
   - Create user
   - Grant permissions

5. Deploy Application (10 min)
   - Clone repository
   - Install dependencies
   - Setup .env
   - Run migrations
   - Build assets

6. SSL/HTTPS Setup (5 min)
   - Install Certbot
   - Generate certificate
   - Setup auto-renewal

7. Security & Monitoring (Optional)
   - Configure firewall
   - Setup backups
   - Enable monitoring

---

## 🌐 Expected Output

Setelah selesai, Anda akan memiliki:

```
✅ Ubuntu Server 22.04+ LTS
✅ PHP 8.3 + PHP-FPM
✅ MySQL 8.0 Database
✅ Nginx Web Server
✅ Node.js 20 LTS
✅ Redis Cache (optional)
✅ Let's Encrypt SSL Certificate (auto-renew)
✅ Firewalled with UFW
✅ Automated backups
✅ Monitoring & logging
✅ Laravel 12 Application
```

---

## 📊 Hardware Recommendations

| Size | CPU | RAM | Storage | Use Case |
|------|-----|-----|---------|----------|
| **Small** | 2 cores | 2-4GB | 30GB | Testing, dev |
| **Medium** | 4 cores | 8GB | 50GB | Production (100-500 users) |
| **Large** | 8+ cores | 16GB+ | 100GB+ | High traffic |

---

## 🚀 Implementation Timeline

```
Day 1:
  - [ ] Setup hardware & networking
  - [ ] Install Ubuntu Server
  - [ ] Run initial setup script (30 min)

Day 2:
  - [ ] Deploy application (30 min)
  - [ ] Setup SSL certificate (10 min)
  - [ ] Test all features
  - [ ] Configure backups

Day 3:
  - [ ] Performance testing
  - [ ] Security audit
  - [ ] Go live!
```

---

## 🔐 Security Reminders

- [ ] Change default passwords immediately
- [ ] Use SSH key-based authentication (not password)
- [ ] Configure firewall to restrict access
- [ ] Enable automatic security updates
- [ ] Setup regular backups (daily minimum)
- [ ] Monitor logs for suspicious activity
- [ ] Keep SSL certificates updated
- [ ] Use strong database passwords

---

## 📞 Need Help?

### Common Issues:
1. **"502 Bad Gateway"** → Restart PHP: `sudo systemctl restart php8.3-fpm`
2. **"Permission Denied"** → Fix permissions: `sudo chmod -R 775 storage`
3. **"Database error"** → Check .env file credentials
4. **"SSL not working"** → Run certbot again: `sudo certbot certonly --nginx`

**Lihat QUICK_UBUNTU_SCRIPTS.md → TROUBLESHOOTING QUICK FIXES**

---

## 📖 Recommended Reading Order

1. **Ini (START_HERE.md)** - Overview & checklist
2. **QUICK_UBUNTU_SCRIPTS.md** - Ambil script & jalankan
3. **UBUNTU_SERVER_SETUP_GUIDE.md** - Baca untuk understand detail
4. **QUICK_UBUNTU_SCRIPTS.md → Troubleshooting** - Jika ada problem

---

## ✅ Post-Deployment Checklist

Setelah deploy, pastikan:

```bash
# 1. Test application
curl https://persuratan.bp.suml.com
# Should return HTML (not error)

# 2. Check services running
sudo systemctl status nginx
sudo systemctl status php8.3-fpm
sudo systemctl status mysql

# 3. Test database
mysql -u persuratan -p db_persuratan_bpsuml -e "SELECT 1"

# 4. Check SSL
openssl s_client -connect persuratan.bp.suml.com:443

# 5. Monitor logs
tail -f /var/log/nginx/error.log
tail -f /var/www/persuratan/storage/logs/laravel.log

# 6. Check disk usage
df -h
du -sh /var/www/persuratan

# 7. Check memory
free -h

# 8. Check CPU
top -b -n 1
```

---

## 🎉 Selamat!

Jika semua checklist selesai, server Anda sudah **PRODUCTION READY**.

Kirim ke:
- IT Admin untuk monitoring
- Network team untuk DNS setup (jika belum)
- User untuk testing

---

## 📞 Support

**Pertanyaan sering:**

**Q: Berapa lama setup?**  
A: 1-2 jam (cepat) atau 3-4 jam (detail + testing)

**Q: Bisakah di-automate?**  
A: Ya, gunakan script di QUICK_UBUNTU_SCRIPTS.md

**Q: Apa kalo script error?**  
A: Baca UBUNTU_SERVER_SETUP_GUIDE.md untuk understand flow, lalu fix manual

**Q: Gimana backup data?**  
A: Ada script di QUICK_UBUNTU_SCRIPTS.md -> Script 5

**Q: Monitoring & alerts?**  
A: Setup di UBUNTU_SERVER_SETUP_GUIDE.md -> Monitoring & Maintenance

---

## 🔗 Quick Links

- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Nginx Docs](https://nginx.org/en/docs/)
- [Ubuntu Server Guide](https://ubuntu.com/server/docs)
- [Let's Encrypt](https://letsencrypt.org/)
- [MySQL Best Practices](https://dev.mysql.com/doc/mysql-security/)

---

**Version:** 1.0  
**Last Updated:** May 2026  
**Status:** Production Ready ✅


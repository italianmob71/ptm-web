# PTM Laravel — Staging Deployment Guide

**Target:** `staging.projecttruthministries.org`
**Branch:** `main` (clean base, not paul's working tree)
**Strategy:** `git clone` → `composer install` → `php artisan migrate --seed` → Apache config → SSL

---

## Prerequisites on Staging Server

- Ubuntu 22.04+ / 24.04+
- PHP 8.3+ (8.5 recommended) with extensions: `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, `curl`, `gd`, `zip`
- Composer 2.x
- MySQL 8.0+ / MariaDB 10.6+
- Apache 2.4+ with `mod_rewrite`, `mod_headers`, `mod_ssl`
- Node.js 20+ / npm (optional — for Vite build; currently using Tailwind CDN)
- Git

---

## 1. Clone the Repository

```bash
cd /var/www
git clone https://github.com/italianmob71/ptm-web.git staging.projecttruthministries.org
cd staging.projecttruthministries.org
```

> **Note:** The repo root is `ptm-web/` — Laravel `public/` is the document root.

---

## 2. PHP / Composer Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

---

## 3. Environment Configuration

Copy `.env.example` and configure:

```bash
cp .env.example .env
```

Edit `.env` with **staging values**:

```env
APP_NAME="Project Truth Ministries"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://staging.projecttruthministries.org
APP_KEY=base64:YOUR_GENERATED_KEY_HERE

# Generate with: php artisan key:generate --show

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ptmweb_staging
DB_USERNAME=ptmweb_staging
DB_PASSWORD=STRONG_RANDOM_PASSWORD

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

CACHE_STORE=file
QUEUE_CONNECTION=sync

MAIL_MAILER=log
# Or configure SMTP for production emails

FILES_TEMP_DIR=/tmp/laravel-temp
```

**Generate APP_KEY:**
```bash
php artisan key:generate
```

---

## 4. Database Setup

Create database and user:

```sql
CREATE DATABASE ptmweb_staging CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ptmweb_staging'@'localhost' IDENTIFIED BY 'STRONG_RANDOM_PASSWORD';
GRANT ALL PRIVILEGES ON ptmweb_staging.* TO 'ptmweb_staging'@'localhost';
FLUSH PRIVILEGES;
```

Run migrations + seeders (includes all 5 admin users + full content):

```bash
php artisan migrate --force --seed
```

**This creates:**
- 5 users (1 super-admin `justin.leoni@gmail.com` level 9, 4 admins level 5)
- 7 authors (Janice, Bryan, Jonathan, Victor, Justin, Wendy, Stephen)
- 6 books, 28 blog posts, 2 articles, 38 images, 6 PDFs, 17 videos, 4 travel notes, 3 Cochin books + 2 chapters, 8 events
- All passwords: `P@$$w0rd123!` with `force_update=1` (users must change on first login)

---

## 5. Storage / Upload Directories

```bash
# Ensure writable dirs (owned by www-data)
mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
mkdir -p public/images/uploads
chown -R www-data:www-data storage bootstrap/cache public/images/uploads
chmod -R 775 storage bootstrap/cache public/images/uploads
```

**Note:** Images/PDFs/videos are stored in `public/images/uploads/`, `public/pdfs/`, `public/videos/` — these are tracked in git (except uploads). No `storage:link` needed.

---

## 6. Apache Virtual Host

Create `/etc/apache2/sites-available/staging.projecttruthministries.org.conf`:

```apache
<VirtualHost *:80>
    ServerName staging.projecttruthministries.org
    DocumentRoot /var/www/staging.projecttruthministries.org/public

    # Redirect all HTTP to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>

<VirtualHost *:443>
    ServerName staging.projecttruthministries.org
    DocumentRoot /var/www/staging.projecttruthministries.org/public

    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/staging.projecttruthministries.org/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/staging.projecttruthministries.org/privkey.pem

    # Security headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options SAMEORIGIN
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy strict-origin-when-cross-origin

    <Directory /var/www/staging.projecttruthministries.org/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        DirectoryIndex index.php

        # Laravel front controller
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteBase /
            RewriteRule ^index\.php$ - [L]
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule . /index.php [L]
        </IfModule>
    </Directory>

    # PHP-FPM (if using) or mod_php
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php/php8.3-fpm.sock|fcgi://localhost"
    </FilesMatch>

    # Large file uploads (CKEditor PDFs)
    LimitRequestBody 52428800

    ErrorLog ${APACHE_LOG_DIR}/staging_ptm_error.log
    CustomLog ${APACHE_LOG_DIR}/staging_ptm_access.log combined
</VirtualHost>
```

Enable and restart:

```bash
a2ensite staging.projecttruthministries.org
a2enmod rewrite headers ssl
systemctl reload apache2
```

---

## 7. SSL Certificate (Let's Encrypt)

```bash
apt-get install -y certbot python3-certbot-apache
certbot --apache -d staging.projecttruthministries.org
# Choose "Redirect" when asked
```

---

## 8. Cache & Optimize

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## 9. File Permissions (Final)

```bash
chown -R www-data:www-data /var/www/staging.projecttruthministries.org
find /var/www/staging.projecttruthministries.org -type f -exec chmod 644 {} \;
find /var/www/staging.projecttruthministries.org -type d -exec chmod 755 {} \;
chmod -R 775 /var/www/staging.projecttruthministries.org/storage /var/www/staging.projecttruthministries.org/bootstrap/cache /var/www/staging.projecttruthministries.org/public/images/uploads
```

---

## 10. Verify Deployment

Test each route works:

```bash
# Health check
curl -I https://staging.projecttruthministries.org/

# Public pages
curl -s https://staging.projecttruthministries.org/ | grep -i "Project Truth"
curl -s https://staging.projecttruthministries.org/about
curl -s https://staging.projecttruthministries.org/team
curl -s https://staging.projecttruthministries.org/blog
curl -s https://staging.projecttruthministries.org/articles
curl -s https://staging.projecttruthministries.org/books
curl -s https://staging.projecttruthministries.org/resources
curl -s https://staging.projecttruthministries.org/studies/cochin/cochin-hebrew-matthew-Cambridge-Interlinear

# Admin login page
curl -s https://staging.projecttruthministries.org/login
```

**Admin login test:**
1. Visit `https://staging.projecttruthministries.org/login`
2. Login as `justin.leoni@gmail.com` / `P@$$w0rd123!`
3. Should force password change → redirect to `/admin/books` (super-admin dashboard)
4. Verify admin nav dropdown appears (Books, Authors, Articles, Blog, Images, PDFs, Videos, Travel Notes, Cochin Books)

---

## 11. Post-Deploy Verification Checklist

- [ ] Homepage loads with dark theme + theme toggle works (localStorage persists)
- [ ] All public routes return 200 (no 500 errors)
- [ ] Images load: `https://staging.projecttruthministries.org/images/uploads/...`
- [ ] PDF downloads work: `/pdfs/{slug}`, `/studies/cochin/{slug}/downloads`
- [ ] Video embeds work (YouTube/Rumble iframes)
- [ ] Admin login → force password change → dashboard accessible
- [ ] All 9 admin CRUD dashboards load (Books, Authors, Blog, Articles, Images, PDFs, Videos, Travel Notes, Cochin Books)
- [ ] Create/Edit/Delete works in each admin section
- [ ] CKEditor loads with custom buttons (Scripture Quote, Table Header Toggle)
- [ ] Theme switcher toggles dark/light and persists
- [ ] Search works (MySQL FULLTEXT on articles, blog, lexicon)

---

## 12. Rollback / Re-seed

If something breaks:

```bash
cd /var/www/staging.projecttruthministries.org
php artisan migrate:fresh --seed --force
php artisan config:clear && php artisan view:clear && php artisan route:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

---

## Troubleshooting

| Issue | Fix |
|-------|-----|
| 500 error on public pages | Check `storage/logs/laravel.log`; run `php artisan config:clear` |
| Images not loading | Verify `public/images/uploads/` exists and is writable by www-data |
| CKEditor not loading | Check browser console; `public/css/ckeditor5.css` and `public/js/ptm-editor.js` must be present |
| Migration fails | Ensure MySQL user has `CREATE`, `ALTER`, `INDEX` privileges |
| Theme not switching | Check `resources/views/components/nav-links.blade.php` has the Alpine.js theme toggle |
| Admin nav missing | Verify user has `security_group >= 5` (admin) or `9` (super-admin) |
| PDF upload fails | Increase `LimitRequestBody` in Apache vhost; check `php.ini` `upload_max_filesize` / `post_max_size` |

---

## Key Files Reference

| File | Purpose |
|------|---------|
| `database/seeders/*.php` | 13 seeders — full data replica of paul |
| `database/migrations/` | 24 migrations — complete schema |
| `app/Http/Middleware/RequireSecurityLevel.php` | Level-based auth middleware |
| `bootstrap/app.php` | Middleware alias `level` registered |
| `resources/views/layouts/app.blade.php` | Base layout with header/footer |
| `resources/views/components/nav-links.blade.php` | Shared nav (header + footer) |
| `public/css/theme.css` | Tailwind CDN + custom CSS variables |
| `public/.htaccess` | Laravel front controller + 50M upload |

---

## Version / Build Info

- **Laravel:** 13.24.0
- **PHP:** 8.5 (minimum 8.3)
- **DB:** MySQL 8.0+ utf8mb4_unicode_ci
- **Node:** 22 (optional, Vite not yet wired)
- **Tailwind:** 4 via CDN (not Vite-built)
- **Auth:** Hand-rolled (no Breeze/Jetstream)
- **Admin:** Level-based (0–9), middleware `level:N`

---

## Notes for Future Production Deploy

1. **Vite build:** When Tailwind moves off CDN, add `npm ci && npm run build` to deploy steps
2. **Queue worker:** Add `php artisan queue:work` via supervisor for async jobs
3. **Scheduler:** Add cron `* * * * * www-data php /var/www/production/artisan schedule:run`
4. **Redis:** Swap `CACHE_STORE=redis` and `SESSION_DRIVER=redis` for scale
5. **Backups:** Automate `mysqldump` + file snapshots before each deploy

---

**Generated:** 2026-08-17
**Seeders verified on paul (.198):** `php artisan migrate:fresh --seed` ✅
**All counts match original DB:** ✅
**5 users, 7 authors, 6 books, 28 blog posts, 2 articles, 38 images, 6 PDFs, 17 videos, 4 travel notes, 3 Cochin books, 2 chapters, 8 events** ✅
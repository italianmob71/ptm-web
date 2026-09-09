# ptm-web
PTM JSL integration

### Local Docker Setup

Make a .env in the `./ptm-web/` directory similar to this:

```bash
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

SESSION_DRIVER=file
SESSION_LIFETIME=120

LOG_CHANNEL=stderr
LOG_STACK=single
LOG_LEVEL=error

MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=YOUR_SENDGRID_API_KEY
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@livingscroll.org
MAIL_FROM_NAME="Living Scroll"

FILESYSTEM_DISK=local
CACHE_STORE=file
QUEUE_CONNECTION=sync

FILES_TEMP_DIR=storage/temp

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel

VITE_APP_NAME="${APP_NAME}"
```

#### shell commands

```bash
# change to the laravel app directory and run docker compose
cd ptm-web
docker compose up -d

# after mariadb and php build import a dump of the prod database
mariadb -h 127.0.0.1 -P 3307 -u root -p laravel < livingsc_web_prod.sql 

# then take the containers down and then do the composer install
docker compose down
docker compose run --rm app composer install

# ensure the necessary folders are there
mkdir -p storage/temp/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# fix local folder permissions
sudo chown -R 33:33 ptm-web/storage ptm-web/bootstrap/cache

sudo find ptm-web/storage ptm-web/bootstrap/cache \
    -type d -exec chmod 775 {} \;

sudo find ptm-web/storage ptm-web/bootstrap/cache \
    -type f -exec chmod 664 {} \;

# clear the artisan cache
docker compose up -d
docker compose exec app php artisan optimize:clear

# pull down images and pdfs from prod

rsync -avh --ignore-existing \
  livingsc@livingscroll.org:/home/livingsc/ptm-web/ptm-web/public/pdfs/ \
  ./public/pdfs/

rsync -avh --ignore-existing \
  livingsc@livingscroll.org:/home/livingsc/ptm-web/ptm-web/public/images/ \
  ./public/images/

# install npm packages and build vite assets
npm install && npn run build

```
Now visiting http://localhost:8080 in the browser should pull up the site.
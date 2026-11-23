# Telkom Invoice Automation System (TIAS) - Production Deployment Guide

Enterprise Production Deployment Manual for **PT. Telkom Indonesia (Persero) Tbk Witel Lampung**.

---

## System Requirements

| Specification | Minimum Requirement | Recommended |
|---|---|---|
| **OS** | Ubuntu 22.04 LTS / Debian 12 | Ubuntu 24.04 LTS |
| **PHP Version** | PHP 8.4+ | PHP 8.4 |
| **Database** | MySQL 8.0+ / MariaDB 10.6+ | MySQL 8.0 Enterprise |
| **Web Server** | Nginx 1.24+ | Nginx with HTTP/2 & SSL |
| **Node.js** | Node 20 LTS | Node 22 LTS |
| **Queue Driver** | Redis 7.0+ / Database | Redis 7.2 |

---

## 1. Nginx Server Configuration

Create Nginx site configuration file at `/etc/nginx/sites-available/tias.conf`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name invoice.telkom-lampung.co.id;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name invoice.telkom-lampung.co.id;
    root /var/www/Invoice_Generator/public;

    index index.php;

    ssl_certificate /etc/letsencrypt/live/invoice.telkom-lampung.co.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/invoice.telkom-lampung.co.id/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    location ~* \.(jpg|jpeg|gif|png|css|js|ico|webp|tiff|ttf|woff|woff2)$ {
        expires 365d;
        add_header Cache-Control "public, no-transform";
    }
}
```

Enable site & test Nginx syntax:
```bash
sudo ln -s /etc/nginx/sites-available/tias.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 2. Supervisor Queue Worker Setup

Create Supervisor configuration at `/etc/supervisor/conf.d/tias-worker.conf`:

```ini
[program:tias-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/Invoice_Generator/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/Invoice_Generator/storage/logs/worker.log
stopwaitsecs=3600
```

Start Supervisor worker processes:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start tias-worker:*
```

---

## 3. Cron Task Scheduler

Configure system cron job for Laravel Scheduler:

```bash
sudo crontab -u www-data -e
```

Add entry:
```cron
* * * * * cd /var/www/Invoice_Generator && php artisan schedule:run >> /dev/null 2>&1
```

---

## 4. Deployment Pipeline & Artisan Optimization Commands

Execute the deployment script when releasing new versions:

```bash
#!/usr/bin/env bash
set -e

echo "Deploying Telkom TIAS Release..."

# 1. Pull latest code & install dependencies
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# 2. Database migrations
php artisan migrate --force

# 3. Cache & Route Optimization
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# 4. Permissions Hardening
sudo chown -R www-data:www-data /var/www/Invoice_Generator
sudo chmod -R 775 /var/www/Invoice_Generator/storage /var/www/Invoice_Generator/bootstrap/cache

# 5. Restart Workers
php artisan queue:restart

echo "Deployment Completed Successfully!"
```

---

## 5. Security & Environment Check

Verify `.env` configuration in production:

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://invoice.telkom-lampung.co.id

LOG_CHANNEL=daily
LOG_LEVEL=info

QUEUE_CONNECTION=database
CACHE_STORE=file
```

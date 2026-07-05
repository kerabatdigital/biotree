# BioTree — Deployment Guide

BioTree deploys to your Coolify VPS via Docker, with automated deployments through GitHub Actions.

---

## Quick Deploy Checklist

- [ ] Configure GitHub Secrets (see below)
- [ ] Create Coolify app pointing to GitHub repo
- [ ] Set environment variables in Coolify
- [ ] Run initial migration: `php artisan migrate --force`
- [ ] Test the deployment

---

## 1. GitHub Actions CI/CD

### Setup Secrets

Go to **https://github.com/kerabatdigital/biotree/settings/secrets/actions**

Add these repository secrets:

| Secret Name | Value |
|-------------|-------|
| `DEPLOY_HOST` | `217.15.167.23` |
| `DEPLOY_USER` | `root` |
| `DEPLOY_PASSWORD` | `3genius8!Q!Q` |
| `DEPLOY_PORT` | `22` |

### How It Works

The workflow (`.github/workflows/deploy.yml`) triggers on push to `main` and:

1. Checks out the code
2. Sets up PHP 8.3 with required extensions
3. Installs Composer dependencies
4. Sets up Node.js and builds assets
5. SSH to the VPS and runs release commands

### Manual Actions

You can also trigger manual actions via GitHub Actions:

1. Go to **Actions** tab in GitHub
2. Select **Deploy to Coolify** workflow
3. Click **Run workflow**
4. Choose action: `deploy`, `migrate`, `logs`, or `restart`

---

## 2. Coolify App Setup

### Create New App

1. Log in to Coolify at `217.15.167.23`
2. Create a new application
3. Set build pack to **Dockerfile** or **Nixpacks**
4. Point to GitHub repo: `https://github.com/kerabatdigital/biotree`
5. Set branch to `main`

### Required Environment Variables

Set these in Coolify's environment settings:

```
APP_NAME=BioTree
APP_ENV=production
APP_DEBUG=false
APP_URL=https://biotree.my
APP_KEY=           # Run: php artisan key:generate --show

DB_CONNECTION=mysql
DB_HOST=           # Your Coolify MySQL host
DB_PORT=3306
DB_DATABASE=biotree
DB_USERNAME=       # Your Coolify MySQL user
DB_PASSWORD=       # Your Coolify MySQL password

REDIS_HOST=        # Your Coolify Redis host
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=https://biotree.my/auth/google/callback
```

### PHP Requirements

- PHP 8.3+
- Extensions: `pdo_mysql`, `redis`, `gd`, `mbstring`, `intl`, `zip`
- Optional: `librsvg2-bin` (for regenerating PWA icons/OG images)

### Docker Compose Override

Create a `docker-compose.yml` in Coolify or add to your Dockerfile:

```yaml
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    environment:
      - PHP_CLI_SERVER_WORKERS=4
    volumes:
      - .:/var/www/html
    depends_on:
      - mysql
      - redis
    networks:
      - biotree

  mysql:
    image: mysql:8.4
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_DATABASE: ${DB_DATABASE}
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - biotree

  redis:
    image: redis:alpine
    networks:
      - biotree

networks:
  biotree:
    driver: bridge

volumes:
  mysql_data:
```

---

## 3. Release Steps (Manual)

If deploying without GitHub Actions:

```bash
# SSH to server
ssh root@217.15.167.23

# Navigate to app directory
cd /path/to/biotree

# Pull latest code
git pull origin main

# Install/update dependencies
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# Run migrations
php artisan migrate --force

# Create storage symlink
php artisan storage:link

# Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart workers
php artisan queue:restart
```

---

## 4. Cloudflare Setup

1. Add `biotree.my` domain to Cloudflare
2. Point DNS to your VPS IP: `217.15.167.23`
3. Enable proxy (orange cloud)
4. Set SSL mode to "Full" or "Strict"

### Cache Headers

Public profile pages send:
```
Cache-Control: public, s-maxage=300, stale-while-revalidate
```

This allows Cloudflare to edge-cache HTML for 5 minutes while still counting page views via beacon.

### Cache Busting

When a profile or links are updated:
- Model observers can trigger Cloudflare API cache purge
- Or rely on the 5-minute TTL

---

## 5. Performance Optimizations

### Laravel Octane + FrankenPHP (Recommended)

For production, use FrankenPHP in worker mode:

```dockerfile
FROM bhklab/php:8.3-fpm

# Install FrankenPHP
RUN apt-get update && apt-get install -y frankenphp

# Or use Laravel Sail's Octane image
```

### Alternative: PHP-FPM + OPcache

```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0  ; 1 in dev
opcache.jit_buffer_size=100M
opcache.jit=tracing
```

### Redis Configuration

```
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

---

## 6. PWA Icons & OG Image Regeneration

The PNG files are committed. To rebuild after brand changes:

```bash
# Install rsvg-convert
apt-get install librsvg2-bin

# Regenerate icons
rsvg-convert -w 192 -h 192 resources/pwa/icon.svg -o public/icons/icon-192.png
rsvg-convert -w 512 -h 512 resources/pwa/icon.svg -o public/icons/icon-512.png
rsvg-convert -w 512 -h 512 resources/pwa/icon-maskable.svg -o public/icons/icon-maskable-512.png
rsvg-convert -w 180 -h 180 resources/pwa/icon.svg -o public/icons/apple-touch-icon.png
rsvg-convert -w 1200 -h 630 resources/pwa/og.svg -o public/og-default.png
```

---

## 7. Troubleshooting

### Check Logs

```bash
# Via GitHub Actions (manual trigger)
# Or SSH:
ssh root@217.15.167.23
docker logs --tail 100 biotree-app
```

### Common Issues

| Issue | Solution |
|-------|----------|
| Migration failed | Check DB credentials in Coolify env |
| 500 error | Check `APP_DEBUG=true` for error details |
| Assets not loading | Run `npm run build` and `php artisan storage:link` |
| Redis connection | Verify `REDIS_HOST` in environment |

### Reset Deployment

```bash
php artisan migrate:fresh --seed
php artisan cache:clear
```

---

## 8. Pre-Launch Checklist

- [ ] Set `APP_DEBUG=false`
- [ ] Configure Google OAuth consent screen (publish from Testing)
- [ ] Add `https://biotree.my/auth/google/callback` to OAuth client
- [ ] Set up privacy policy page
- [ ] Set up terms of service page
- [ ] Configure error monitoring (Sentry optional)
- [ ] Test on mobile devices
- [ ] Verify Cloudflare SSL certificate
- [ ] Check analytics tracking works
- [ ] Test link click tracking

---

## 9. Support

For deployment issues, check:
1. GitHub Actions logs
2. Coolify deployment logs
3. Laravel logs: `storage/logs/laravel.log`
4. Docker container logs

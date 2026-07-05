# BioTree — Coolify Setup Guide

Complete step-by-step instructions for deploying BioTree on your VPS using Coolify.

---

## Prerequisites

- VPS accessible at `217.15.167.23`
- Coolify installed on the VPS
- GitHub repository: `https://github.com/kerabatdigital/biotree`

---

## Step 1: Access Coolify

1. Open your browser and go to Coolify:
   ```
   http://217.15.167.23:3000
   ```
   (Port 3000 is the default Coolify UI port)

2. Log in with your Coolify credentials

---

## Step 2: Create a New Project

1. Click **"New Project"**
2. Name it: `BioTree`
3. Click **Create**

---

## Step 3: Add a New Resource (Application)

### Option A: Database & Redis (Recommended)

Since Coolify manages these, let's set them up first:

1. Go to your **Project**
2. Click **Add New Resource**
3. Select **Database**
4. Choose **MySQL 8.4**
5. Configure:
   - **Name**: `biotree-mysql`
   - **Database Name**: `biotree`
   - **Username**: `biotree`
   - **Password**: `biotree_secure_password_123` (use a strong password!)
6. Click **Deploy**

7. Repeat for Redis:
   - **Name**: `biotree-redis`
   - Select **Redis** instead of MySQL
8. Click **Deploy**

### Option B: Database (Existing)

If you already have MySQL/Redis on your VPS, note down the connection details.

---

## Step 4: Add the BioTree Application

1. Go to your **Project**
2. Click **Add New Resource**
3. Select **Application (GitHub)**

### Configure GitHub Integration

1. **GitHub Repository**: Select `kerabatdigital/biotree`
2. **Branch**: `main`
3. **Build Pack**: Select **Dockerfile** (our Dockerfile will be used)

### Configure Application

1. **Name**: `biotree`
2. **Port**: `80`
3. **Domain** (optional): Set to `biotree.my` later after DNS

### Environment Variables

Click **Add Variable** and add these:

```
APP_NAME=BioTree
APP_ENV=production
APP_DEBUG=false
APP_URL=http://217.15.167.23
APP_KEY=
```

> **Important**: Generate APP_KEY by running this locally:
> ```bash
> php artisan key:generate --show
> ```
> Copy the output and paste as APP_KEY value.

Continue adding:
```
DB_CONNECTION=mysql
DB_HOST=<your-mysql-host-from-step3>
DB_PORT=3306
DB_DATABASE=biotree
DB_USERNAME=biotree
DB_PASSWORD=biotree_secure_password_123

REDIS_HOST=<your-redis-host-from-step3>
REDIS_PASSWORD=
REDIS_PORT=6379

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

GOOGLE_CLIENT_ID=<from-google-cloud-console>
GOOGLE_CLIENT_SECRET=<from-google-cloud-console>
GOOGLE_REDIRECT_URI=http://217.15.167.23/auth/google/callback
```

### Deployment Settings

1. **Health Check**: `/up`
2. **Start Command**: Leave empty (Dockerfile CMD will be used)

---

## Step 5: Deploy

1. Click **Deploy**
2. Watch the logs - deployment takes 3-5 minutes on first run

---

## Step 6: Initial Setup Commands

After deployment, run these commands via Coolify's terminal:

1. **Generate App Key** (if not set):
   ```
   php artisan key:generate
   ```

2. **Run Migrations**:
   ```
   php artisan migrate --force
   ```

3. **Create Storage Link**:
   ```
   php artisan storage:link
   ```

4. **Clear Cache**:
   ```
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## Step 7: Test the Application

1. Open `http://217.15.167.23` in your browser
2. You should see the BioTree landing page

---

## Step 8: Set Up Admin User

### Option A: Via Coolify Terminal

1. Open Coolify's terminal for the BioTree container
2. Run:
   ```bash
   php artisan tinker
   ```
3. Then:
   ```php
   App\Models\User::where('email', 'your-google-email@gmail.com')->update(['role' => 'admin']);
   ```
4. Exit tinker: `exit`

### Option B: Via SSH

```bash
ssh root@217.15.167.23
docker exec -it biotree-app php artisan tinker
```

---

## Troubleshooting

### Container Not Starting

Check logs:
```bash
docker logs biotree-app
```

### Database Connection Failed

1. Verify MySQL is running in Coolify
2. Check `DB_HOST` matches the Coolify MySQL internal hostname
3. Ensure database `biotree` exists

### Permission Denied Errors

```bash
docker exec -it biotree-app chown -R www-data:www-data /var/www/html/storage
```

### SSL/HTTPS Issues

For production with HTTPS, configure Cloudflare or set up nginx reverse proxy.

---

## Production Checklist

- [ ] Set `APP_DEBUG=false`
- [ ] Configure custom domain (biotree.my)
- [ ] Set up Cloudflare
- [ ] Update Google OAuth redirect URI
- [ ] Test Google login
- [ ] Create admin user
- [ ] Test admin panel at `/admin`

---

## Updating the Application

### Automatic (GitHub Actions)

Push to `main` branch - GitHub Actions will trigger Coolify deployment.

### Manual

1. Go to Coolify
2. Click **Redeploy** on the BioTree app

---

## Useful Commands

```bash
# View logs
docker logs -f biotree-app

# Enter container
docker exec -it biotree-app bash

# Run artisan commands
docker exec -it biotree-app php artisan <command>

# Restart
docker restart biotree-app

# Check status
docker ps
```

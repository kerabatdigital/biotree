# BioTree Deployment Guide

## Live URL
**https://biotree.my**

## Server Info
- **VPS IP:** 217.15.167.23
- **Coolify:** http://217.15.167.23:8000

## Docker Containers

| Container | Image | Network |
|----------|-------|---------|
| biotree-app | biotree:latest | coolify |
| biotree-mysql | mysql:8.0 | coolify |
| biotree-redis | redis:7-alpine | coolify |

## Container IPs
- **App:** Dynamic (coolify network DNS)
- **MySQL:** 10.0.1.13
- **Redis:** 10.0.1.12

## MySQL Credentials
- Database: `biotree`
- User: `biotree`
- Password: `biotree_db_pass_2024`

## Redis Credentials
- Password: `biotree_redis_pass_2024`

## Rebuild & Deploy

```bash
# On server, rebuild image from GitHub
docker build -t biotree-app https://github.com/kerabatdigital/biotree.git#main

# Run container
docker run -d \
  --name biotree-app \
  --network coolify \
  --restart unless-stopped \
  --label coolify.managed=true \
  biotree-app
```

## Quick Deploy Steps

```bash
# SSH to server
ssh root@217.15.167.23

# Stop old container
docker stop biotree-app && docker rm biotree-app

# Pull/build new image
docker build -t biotree-app https://github.com/kerabatdigital/biotree.git#main

# Run with env
docker run -d --name biotree-app --network coolify --restart unless-stopped \
  --label coolify.managed=true biotree-app

# Setup env inside container
docker exec biotree-app sh -c '
  cp .env.example .env
  php artisan key:generate --force
  # Configure .env with MySQL/Redis credentials
'
```

## Coolify Integration

Access Coolify at http://217.15.167.23:8000

The app is configured with:
- Docker network: `coolify`
- Labels for Traefik routing to `biotree.my`

## SSL/TLS

Let's Encrypt handles SSL automatically via Traefik.

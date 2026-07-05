# BioTree — Deployment (Coolify)

BioTree is containerised, so it deploys to your Coolify VPS as a Docker app. This
is the production checklist; local dev stays on Laravel Sail (see `PLAN.md`).

## 1. App service
- Deploy from this git repo. Use a **Dockerfile** (recommended: FrankenPHP/Octane
  for worker-mode speed) or Nixpacks.
- PHP 8.3+ with extensions: `pdo_mysql`, `redis`, `gd`, `mbstring`, `intl`, `zip`,
  plus `librsvg2-bin` (only needed if you regenerate icons/OG — see below).

## 2. Environment
Set in Coolify (never commit real secrets):
```
APP_NAME=BioTree
APP_ENV=production
APP_DEBUG=false
APP_URL=https://biotree.my
APP_KEY=            # php artisan key:generate --show

DB_CONNECTION=mysql        # point at your Coolify MySQL service
REDIS_HOST=...             # Coolify Redis service
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=https://biotree.my/auth/google/callback
```
Add `https://biotree.my/auth/google/callback` to the Google OAuth client, and
publish the OAuth consent screen (move out of "Testing") before public launch.

## 3. Release steps
```
php artisan migrate --force
php artisan storage:link
php artisan config:cache && php artisan route:cache && php artisan view:cache
npm ci && npm run build
```

## 4. Cloudflare (superfast + GEO)
- Put biotree.my behind Cloudflare (KL/SG edge → low latency for Malaysia).
- Public pages send `Cache-Control: public, s-maxage=300, stale-while-revalidate`,
  so Cloudflare edge-caches them. Page views are still counted (client beacon),
  and link clicks go through `/out/{ulid}` (never cached).
- Purge the edge cache for `biotree.my/{username}` when a profile/links change
  (Cloudflare API), or rely on the 5-minute TTL.

## 5. Performance
- **Laravel Octane + FrankenPHP** for worker mode (biggest win).
- OPcache + JIT on.
- Redis for cache/session/queue; run **Horizon** as a worker for queued jobs.
  (Tracking currently uses `dispatchAfterResponse` and needs no worker; move to
  real queues + Horizon when you add heavier jobs like OG generation or rollups.)

## 6. Regenerating PWA icons / OG image
The PNGs in `public/icons` and `public/og-default.png` are committed. To rebuild
from the SVG sources after a brand tweak:
```
rsvg-convert -w 192 -h 192 resources/pwa/icon.svg -o public/icons/icon-192.png
rsvg-convert -w 512 -h 512 resources/pwa/icon.svg -o public/icons/icon-512.png
rsvg-convert -w 512 -h 512 resources/pwa/icon-maskable.svg -o public/icons/icon-maskable-512.png
rsvg-convert -w 180 -h 180 resources/pwa/icon.svg -o public/icons/apple-touch-icon.png
rsvg-convert -w 1200 -h 630 resources/pwa/og.svg -o public/og-default.png
```

## 7. PDPA / compliance (before public launch)
- Publish a privacy policy + terms (analytics store hashed IDs, no raw IPs).
- Add account data export + deletion (Phase 8).

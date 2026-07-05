# BioTree

A superfast, SEO-friendly bio-link SaaS platform for the Malaysian market. Create your personalized link-in-bio page and track your audience analytics.

**Domain:** [biotree.my](https://biotree.my)

---

## Features

### User Features
- **Google OAuth Login** - Quick sign-in with Google
- **Custom Username** - Personalize your profile URL
- **Link Management** - Add, edit, reorder links with drag-and-drop
- **Theme Customization** - 6 preset themes + custom colors, fonts, and styles
- **Link Analytics** - Track page views and click-through rates
- **PWA Support** - Install as an app on mobile devices
- **SEO Optimized** - Open Graph tags, JSON-LD structured data, sitemap

### Admin Features
- **Dashboard Overview** - Platform KPIs, user growth, top profiles
- **User Management** - Search, filter, suspend/restore users
- **Content Moderation** - Reports queue with action tools
- **Audit Logging** - Track all admin actions
- **Feature Flags** - Toggle platform features from admin panel

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| Framework | Laravel 13.8 (PHP 8.3+) |
| Frontend | Tailwind CSS v3, Vite, Alpine.js |
| Interactivity | Livewire 3.6 + Volt 1.7 |
| Auth | Laravel Breeze + Socialite (Google) |
| Database | MySQL 8 |
| Cache/Queue | Redis |
| CDN | Cloudflare |
| Deployment | Coolify (Docker) + GitHub Actions |

---

## Requirements

- PHP 8.3+
- MySQL 8 or SQLite
- Redis
- Node.js 20+
- Composer 2+

---

## Local Development

### Using Laravel Sail (Docker)

```bash
# Clone the repository
git clone https://github.com/kerabatdigital/biotree.git
cd biotree

# Install dependencies
composer install
npm install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Start Docker containers
./vendor/bin/sail up

# Run migrations
php artisan migrate

# Build assets
npm run dev
```

Visit `http://localhost` (or port 8080 based on your `.env` settings).

### Without Docker

1. Set up MySQL and Redis locally
2. Update `.env` with your database credentials
3. Run `php artisan migrate`
4. Run `npm run dev`

---

## Deployment

See [DEPLOY.md](./DEPLOY.md) for complete deployment instructions.

### Quick Deploy to Coolify

1. Add GitHub Secrets for deployment:
   - `DEPLOY_HOST`
   - `DEPLOY_USER`
   - `DEPLOY_PASSWORD`
   - `DEPLOY_PORT`

2. Create a new app in Coolify pointing to this GitHub repo

3. Set environment variables in Coolify

4. Push to `main` branch - deployment is automatic!

---

## Admin Panel

Access at `/admin` (requires admin role).

To make a user admin:

```bash
php artisan tinker
```

```php
App\Models\User::where('email', 'your-email@example.com')->update(['role' => 'admin']);
```

---

## Build Roadmap

| Phase | Status | Description |
|-------|--------|-------------|
| 0 | ✅ | Setup, Docker, base structure |
| 1 | ✅ | Google OAuth, username onboarding |
| 2 | ✅ | Link editor with drag-drop |
| 3 | ✅ | Public bio page + tracking |
| 4 | ✅ | Theme customizer |
| 5 | ✅ | Analytics dashboard |
| 6 | ✅ | Performance, SEO, PWA |
| 7 | ✅ | Admin panel |
| 8 | 🔲 | i18n (Malay), PDPA compliance |
| 9 | 🔲 | Billing (Pro plans) |

---

## Database Schema

### Core Tables

- **users** - Authentication, role (user/admin), status, plan
- **profiles** - Bio pages with username, theme, SEO data
- **links** - User links with ULID, click tracking
- **link_clicks** - Click events (country, device, referrer)
- **page_views** - Page view events

### Admin Tables

- **reports** - Content moderation queue
- **audit_logs** - Admin action history

---

## Environment Variables

```env
APP_NAME=BioTree
APP_ENV=local
APP_KEY=
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=biotree
DB_USERNAME=sail
DB_PASSWORD=password

REDIS_HOST=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
```

---

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'Add amazing feature'`
4. Push to branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

---

## License

This project is proprietary software. All rights reserved.

---

## Project Status

**Current Phase:** 7 - Admin Panel (Complete)

**Next:** Phase 8 - i18n (Malay) + PDPA Compliance

---

## Support

- Documentation: [PLAN.md](./PLAN.md)
- Deployment: [DEPLOY.md](./DEPLOY.md)
- Changelog: [CHANGELOG.md](./CHANGELOG.md)

# BioTree - Bio Link SaaS Platform

A modern bio link platform built with Laravel + Livewire.

## Features

- Beautiful bio pages with customizable themes
- Multiple theme presets (Midnight, Ocean, Sunset, Neon, Cyberpunk, Aurora, Lava, and more)
- Animated backgrounds (Gradient Shift, Floating Orbs, Stars, Aurora, Waves, Bubbles, Particles)
- QR code generation for each link
- Click/view analytics
- Admin dashboard
- Verified badge system
- Google OAuth login

## Tech Stack

- **Backend:** Laravel 13, PHP 8.3
- **Frontend:** Livewire, Alpine.js, Tailwind CSS
- **Database:** MySQL 8.0
- **Cache/Sessions:** Redis
- **Deployment:** Docker, Coolify

## Quick Links

- **Live App:** https://biotree.my
- **Admin:** https://biotree.my/admin
- **Login:** https://biotree.my/login

## Admin Credentials

- Email: `admin@biotree.my`
- Password: `Biotree@2024!`

## Local Development

```bash
# Install dependencies
composer install
npm install

# Copy environment
cp .env.example .env

# Generate key and run migrations
php artisan key:generate
php artisan migrate

# Start dev server
php artisan serve
```

## Production Deployment

See [DEPLOY.md](DEPLOY.md) for detailed deployment instructions.

## License

MIT

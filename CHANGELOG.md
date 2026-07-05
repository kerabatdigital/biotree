# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added
- Nothing yet

### Changed
- Nothing yet

---

## [1.0.0-beta.7] - 2026-07-05

### Added
- **Admin Panel** - Complete custom Livewire admin dashboard

#### Admin Components
- `Admin/Overview` - Platform KPIs, user growth chart (7/30/90 days), recent signups, top profiles by clicks
- `Admin/Users` - User search by name/email/username, filter by status/plan/role, suspend/restore, promote/demote admin
- `Admin/UserDetail` - Full user view with profile, links, activity history, bulk actions
- `Admin/Reports` - Moderation queue with status tabs, dismiss/action/review actions, morphTo reporting
- `Admin/Settings` - Site config, feature flags (maintenance, signups, Google login, analytics), reserved usernames CRUD

#### Admin Infrastructure
- `EnsureAdmin` middleware - Route protection for admin routes
- `Report` model - MorphTo relationship for Profile/Link/User reporting
- `AuditLog` model - Tracks admin actions with before/after values, admin ID, IP, user agent
- Admin layout with sidebar navigation
- Admin nav link in user navigation

#### Database
- `reports` table migration - `reportable_type`, `reportable_id`, `reporter_email`, `reason`, `description`, `status`, `admin_notes`, `handled_by`, `handled_at`
- `audit_logs` table migration - `admin_id`, `action`, `target_type`, `target_id`, `old_values`, `new_values`, `ip_address`, `user_agent`

#### CI/CD
- GitHub Actions workflow (`.github/workflows/deploy.yml`)
- Automated deployment on push to `main`
- Manual triggers: deploy, migrate, logs, restart
- SSH deployment to Coolify VPS

### Changed
- Updated `PLAN.md` - Marked Phase 7 as complete
- Updated `DEPLOY.md` - Added GitHub Actions CI/CD documentation
- Updated `README.md` - Complete BioTree project documentation
- New `CHANGELOG.md` - Version history

---

## [1.0.0-beta.6] - 2026-07-05

### Added
- **Performance Optimizations**
  - Cloudflare edge caching with 5-minute TTL
  - Cache-Control headers for public pages
  - Model observers for cache busting

- **SEO Enhancements**
  - Dynamic Open Graph images per profile
  - JSON-LD structured data (Person/Organization)
  - `sitemap.xml` generation for published profiles
  - `robots.txt` configuration
  - Per-profile meta title and description

- **PWA Support**
  - Service worker (`sw.js`) with Workbox
  - Web app manifest (`manifest.json`)
  - Offline fallback page
  - Installable on mobile devices
  - Maskable icons for Android

- **Asset Optimization**
  - Vite build configuration
  - SVG-based PWA icons (regeneratable)
  - Default OG image (`og-default.png`)

---

## [1.0.0-beta.5] - 2026-07-05

### Added
- **Analytics Dashboard**
  - Page views tracking
  - Unique visitors (hashed, PDPA-friendly)
  - Link click tracking via ULID
  - Geographic data (country via Cloudflare)
  - Device/browser detection
  - Time-series visualization
  - Top links by clicks
  - CTR (Click-through rate) per link

- **Data Models**
  - `PageView` model - Track profile views
  - `LinkClick` model - Track outbound clicks
  - Visitor hashing for privacy compliance

- **Tracking Pipeline**
  - `POST /track/view` - Page view beacon
  - `GET /out/{ulid}` - Click tracking + redirect
  - Async job dispatch for analytics

---

## [1.0.0-beta.4] - 2026-07-05

### Added
- **Appearance Editor**
  - 6 preset themes (midnight, ocean, sunset, grape, forest, paper)
  - Custom background colors
  - Text color customization
  - Accent color picker
  - Button styles (soft, solid, outline, shadow)
  - Button radius options (none, sm, md, lg, full)
  - Font selection (Figtree, Inter, Poppins, Manrope, etc.)
  - Avatar shape (circle, rounded, square)
  - Live preview panel

- **Theme System**
  - `ThemeBuilder` service class
  - JSON theme storage in profile
  - CSS custom properties for dynamic theming
  - Theme presets in `config/biotree.php`

---

## [1.0.0-beta.3] - 2026-07-05

### Added
- **Public Bio Page** (`GET /{username}`)
  - Mobile-first responsive design
  - SSR Blade template (no JS required)
  - Skeleton loading states
  - Theme applied from profile JSON
  - Link types: link, header, social, embed
  - Social icons row
  - Open Graph meta tags
  - JSON-LD structured data

- **Link Click Tracking**
  - ULID-based tracking URLs
  - `GET /out/{ulid}` redirect endpoint
  - Click event recording
  - Denormalized `clicks_count` on links

- **Page View Tracking**
  - Beacon-based tracking (CSRF-exempt)
  - `POST /track/view` endpoint
  - Visitor hash for unique counting
  - Geo/device extraction

---

## [1.0.0-beta.2] - 2026-07-05

### Added
- **Link Editor**
  - CRUD operations for links
  - Drag-and-drop reordering (Alpine.js)
  - Link types: link, header, social, embed
  - Phosphor icon picker
  - Inline editing
  - Toggle active/inactive
  - Optional thumbnail images
  - Scheduling (start/end dates placeholder)

- **Icon System**
  - `blade-phosphor-icons` package
  - Dynamic icon components
  - Icon picker modal

- **Live Preview**
  - Real-time mobile preview
  - Theme-aware styling
  - Real device simulation

---

## [1.0.0-beta.1] - 2026-07-05

### Added
- **Authentication**
  - Laravel Breeze (Volt) setup
  - Google OAuth via Socialite
  - Google login button on auth pages
  - User avatar from Google

- **Onboarding Flow**
  - Username claim step after Google login
  - `NotReservedUsername` validation rule
  - Username availability check
  - Reserved usernames guard
  - Auto-suggest from email

- **User Model**
  - Google ID tracking
  - Avatar field
  - Role (user/admin)
  - Status (active/suspended)
  - Plan (free/pro)
  - Locale (en/ms)
  - Last login timestamp

- **Profile Model**
  - Username (unique, indexed)
  - Display name, tagline, bio
  - Avatar path
  - Published status
  - Theme JSON storage
  - SEO fields

---

## [1.0.0-alpha] - 2026-05-26

### Added
- Initial Laravel 13 scaffold via Laravel Sail
- Docker Compose setup (MySQL, Redis)
- Tailwind CSS v3 configuration
- Vite asset bundling
- Base layouts (guest, app)
- Database migrations structure
- `.env.example` configuration
- `PLAN.md` architecture document
- `DEPLOY.md` deployment guide

---

## Upcoming (Phase 8+)

### Phase 8 - i18n & PDPA
- [ ] Malay (BM) language support
- [ ] Privacy policy page
- [ ] Terms of service page
- [ ] Data export feature (JSON)
- [ ] Account deletion feature
- [ ] Cookie consent banner
- [ ] PDPA compliance documentation

### Phase 9 - Billing
- [ ] Free/Pro plan feature split
- [ ] Stripe or FPX integration
- [ ] SST (8%) calculation
- [ ] e-Invoice (LHDN MyInvois) preparation
- [ ] Custom domains (Pro)
- [ ] Advanced analytics (Pro)
- [ ] Link scheduling (Pro)

---

[Unreleased]: https://github.com/kerabatdigital/biotree/compare/v1.0.0-beta.7...HEAD
[1.0.0-beta.7]: https://github.com/kerabatdigital/biotree/compare/v1.0.0-beta.6...v1.0.0-beta.7
[1.0.0-beta.6]: https://github.com/kerabatdigital/biotree/compare/v1.0.0-beta.5...v1.0.0-beta.6
[1.0.0-beta.5]: https://github.com/kerabatdigital/biotree/compare/v1.0.0-beta.4...v1.0.0-beta.5
[1.0.0-beta.4]: https://github.com/kerabatdigital/biotree/compare/v1.0.0-beta.3...v1.0.0-beta.4
[1.0.0-beta.3]: https://github.com/kerabatdigital/biotree/compare/v1.0.0-beta.2...v1.0.0-beta.3
[1.0.0-beta.2]: https://github.com/kerabatdigital/biotree/compare/v1.0.0-beta.1...v1.0.0-beta.2
[1.0.0-beta.1]: https://github.com/kerabatdigital/biotree/compare/v1.0.0-alpha...v1.0.0-beta.1
[1.0.0-alpha]: https://github.com/kerabatdigital/biotree/releases/tag/v1.0.0-alpha

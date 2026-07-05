# BioTree — Technical Plan & Architecture

> A superfast, SEO/GEO-friendly, PWA bio-link SaaS (Linktree-style) built for the Malaysian market.
> Domain: **biotree.my** · Public pages: **biotree.my/{username}**

**Locked decisions**
- Start with a **detailed plan** (this document) before writing code.
- **Billing: free at launch**, payment layer added later (schema designed to slot it in).
- **URL structure: path-based** — `biotree.my/{username}`.
- **Admin: custom Livewire panel** (not Filament) — full UI control.

---

## 1. Tech Stack (final)

| Layer | Choice | Notes |
|---|---|---|
| Framework | Laravel 13 (PHP 8.5) | Scaffolded via Laravel Sail (Docker) |
| Interactivity | Livewire 3 + Volt + Alpine.js | |
| Styling | Tailwind CSS v3 | Breeze default; modern theme + skeleton loaders |
| Icons | `codeat3/blade-phosphor-icons` | Phosphor as `<x-phosphor-* />` Blade components |
| Auth | Laravel Breeze (Livewire) + Fortify | Email + password |
| Social login | Laravel Socialite | **Google** login |
| Admin | Custom Livewire components | Users, moderation, platform analytics |
| Cache / Queue / Session | Redis + Laravel Horizon | Async analytics, email, rate limiting |
| App server | Laravel Octane (FrankenPHP) | Worker mode; fallback Nginx + PHP-FPM + OPcache |
| Database | MariaDB / MySQL 8 | |
| Assets | Vite | + `vite-plugin-pwa` (Workbox) for **PWA** |
| Media | `spatie/laravel-medialibrary` | Avatars/backgrounds → WebP/AVIF |
| Edge/CDN | Cloudflare (free tier) | KL edge, free SSL, WAF, DDoS, HTML edge-cache |
| SEO helpers | `spatie/laravel-sitemap`, `spatie/laravel-responsecache` | |
| Charts | ApexCharts (via Alpine) | Analytics dashboards |

---

## 2. System Architecture

```
                    ┌──────────────┐
  Visitor ─────────▶│  Cloudflare  │  (KL edge: SSL, WAF, cache /{username} HTML)
                    └──────┬───────┘
                           │ miss / dynamic
                    ┌──────▼───────────────┐
                    │  FrankenPHP (Octane) │  Laravel 12 workers
                    └──────┬───────────────┘
             ┌─────────────┼────────────────────┐
         ┌───▼───┐    ┌────▼────┐          ┌─────▼─────┐
         │ MySQL │    │  Redis  │          │  Horizon  │  queued jobs:
         └───────┘    │ cache/  │          │  workers  │  - analytics writes
                      │ queue/  │          └───────────┘  - OG image gen
                      │ session │                         - rollups, email
                      └─────────┘
```

**Request paths**
- **Public bio page** `GET /{username}` → SSR Blade, edge-cached HTML. Page views tracked via a tiny JS **beacon** (`navigator.sendBeacon → POST /api/track/view`) so the HTML stays fully cacheable.
- **Link click** `GET /out/{link_ulid}` → logs click to queue, `302` to target. Reliable without JS; not cached.
- **App/Admin** → authenticated, dynamic, not cached.

---

## 3. Deployment Topology (your VPS)

- **Region:** Singapore or Kuala Lumpur (lowest latency to MY; Cloudflare handles the rest).
- **Services:** FrankenPHP (Octane), MySQL, Redis, Horizon (supervisor), scheduler (cron `php artisan schedule:run`).
- **TLS:** Cloudflare origin cert or FrankenPHP auto-HTTPS.
- **Deploy:** Git-based (Deployer/GitHub Actions → SSH), zero-downtime with Octane reload.
- **Backups:** nightly `mysqldump` + media to object storage; `spatie/laravel-backup`.
- **Monitoring:** Laravel Pulse + uptime check; log to file/Sentry.

---

## 4. Data Model

### `users`
`id, name, email, email_verified_at, password (nullable), google_id (nullable, unique), avatar, role (enum: user|admin), status (enum: active|suspended), plan (enum: free|pro, default free), locale (en|ms), last_login_at, timestamps`

### `profiles` (1:1 user → page; extensible to many for Pro)
`id, user_id, username (unique, indexed), display_name, tagline, bio, avatar_path, is_published (bool), is_verified (bool), theme (JSON), custom_css (nullable, Pro), seo_title, seo_description, og_image_path, timestamps`

**`theme` JSON shape**
```json
{
  "preset": "midnight",
  "background": { "type": "color|gradient|image", "value": "#0f172a" },
  "button":     { "style": "fill|outline|soft|shadow", "radius": "none|sm|md|lg|full", "color": "#fff", "text_color": "#000" },
  "font": "inter|poppins|manrope",
  "text_color": "#ffffff",
  "avatar_shape": "circle|rounded|square"
}
```

### `links`
`id, ulid (unique, public id for /out), profile_id, type (enum: link|header|social|embed), title, url, icon (phosphor name), thumbnail_path (nullable), sort_order, is_active, clicks_count (denormalized), start_at (nullable), end_at (nullable), timestamps`
- Social icon row = rows with `type = social`. Scheduling via start/end (Pro).

### `page_views` (raw events)
`id, profile_id, visitor_hash, country, city (nullable), referrer_host, device, browser, os, created_at` · index `(profile_id, created_at)`

### `link_clicks` (raw events)
`id, link_id, profile_id, country, referrer_host, device, created_at` · index `(link_id, created_at)`

### `profile_daily_stats` / `link_daily_stats` (rollups)
`profile_id|link_id, date, views, unique_visitors, clicks` — populated by scheduled job; dashboards read these for speed.

### `reports` (abuse moderation — ✅ IMPLEMENTED)
`id, reportable_type, reportable_id, reporter_email (nullable), reason, description (nullable), status (enum: open|reviewed|actioned|dismissed), admin_notes, handled_by (nullable FK), handled_at, timestamps`
- MorphTo relationship to Profile, Link, or User
- Auto-actions: unpublish profile / disable link / suspend user when actioned

### `audit_logs` (admin action tracking — ✅ IMPLEMENTED)
`id, admin_id (FK), action (string), target_type, target_id, old_values (JSON), new_values (JSON), ip_address, user_agent, created_at`

### `plans` *(placeholder for later billing)*
`id, name, price_cents, currency (MYR), interval, features (JSON), is_active` — Cashier/subscriptions added in the billing phase.

**Reserved usernames** (block on claim, so they don't collide with the `/{username}` catch-all): `admin, dashboard, api, login, register, auth, settings, links, appearance, analytics, out, l, about, pricing, terms, privacy, help, support, www, blog, app`.

---

## 5. Application Structure

```
app/
  Http/
    Controllers/
      Auth/
        GoogleController.php
      OutboundClickController.php
      PublicProfileController.php
      SitemapController.php
      TrackController.php
    Middleware/
      EnsureAdmin.php          # Admin route protection
      EnsureOnboarded.php      # Onboarding flow protection
  Jobs/
    RecordLinkClick.php
    RecordLinkClick.php
  Livewire/
    App/
      Analytics.php            # User analytics dashboard
      AppearanceEditor.php     # Theme customizer
      LinkEditor.php           # Link CRUD with drag-reorder
    Admin/                     # ✅ Phase 7 - Admin Panel
      Overview.php             # Platform KPIs & growth
      Users.php                # User management
      UserDetail.php           # Single user view
      Reports.php              # Moderation queue
      Settings.php             # Feature flags & config
    Auth/                      # (Breeze Volt components)
    Forms/
    Onboarding/
      ClaimUsername.php        # Username claim flow
    Actions/
      Logout.php
  Models/
    User.php
    Profile.php
    Link.php
    LinkClick.php
    PageView.php
    Report.php                 # ✅ Abuse moderation
    AuditLog.php               # ✅ Admin action tracking
  Providers/
  Rules/
    NotReservedUsername.php
  Support/
    ThemeBuilder.php
    Visitor.php
  View/
bootstrap/app.php
config/
  app.php
  biotree.php                  # BioTree-specific config
  database.php
  services.php
database/
  migrations/
  factories/
  seeders/
resources/
  css/
    app.css
  js/
    app.js
  pwa/
    icon.svg
    og.svg
  views/
    layouts/
      admin.blade.php          # ✅ Admin layout with sidebar
      app.blade.php
      guest.blade.php
    livewire/
      admin/                   # ✅ Admin views
        overview.blade.php
        users.blade.php
        user-detail.blade.php
        reports.blade.php
        settings.blade.php
      app/                    # User dashboard views
      layout/
        navigation.blade.php   # Updated with admin link
    public/
    partials/
routes/
  web.php                      # Includes admin routes
  auth.php
  console.php
compose.yaml                   # Laravel Sail (local dev)
.github/
  workflows/
    deploy.yml                 # ✅ GitHub Actions CI/CD
.env                            # Local dev secrets
DEPLOY.md                       # Production deployment guide
PLAN.md                         # This file
CHANGELOG.md                    # Version history
```

---

## 6. Routing Map

| Method | Path | Purpose | Cache |
|---|---|---|---|
| GET | `/` | Marketing landing | edge |
| GET | `/pricing`, `/terms`, `/privacy` | Static pages | edge |
| GET | `/login`, `/register` | Auth (Breeze) | no |
| GET | `/auth/google`, `/auth/google/callback` | Google OAuth | no |
| GET | `/dashboard` | User home | no |
| GET | `/links` | Link editor (drag & drop) | no |
| GET | `/appearance` | Theme customizer | no |
| GET | `/analytics` | User analytics | no |
| GET | `/settings` | Account, data export/delete | no |
| GET | `/admin/*` | Custom Livewire admin (admin only) | no |
| POST | `/api/track/view` | Page-view beacon | no |
| GET | `/out/{ulid}` | Click tracking → 302 redirect | no |
| GET | `/sitemap.xml` | Published profiles | edge |
| GET | `/{username}` | **Public bio page** (catch-all, last) | edge |

---

## 7. Feature Specs

**Auth & onboarding** — Email/password (Breeze) + Google (Socialite). First login → claim username (validated against reserved list + uniqueness), pick a starter theme, land on dashboard.

**Link editor** — Livewire CRUD with drag-and-drop reordering (SortableJS/Alpine), inline edit, toggle active, Phosphor icon picker, link types (link / section header / social / embed), optional thumbnail. Live mobile preview panel beside the editor.

**Appearance** — Preset themes (seeded) + custom colors, button style/radius, font, background (color/gradient/image), avatar shape. Live preview via Livewire. Writes to `profiles.theme` JSON; observer busts the response cache.

**Public bio page** — Mobile-first SSR Blade, skeleton on first paint, lazy images (WebP/AVIF), fully edge-cacheable. Per-profile meta + OG + Twitter cards + JSON-LD `Person`/`Organization`. View tracking by beacon; clicks via `/out/{ulid}`.

**Analytics** — Async capture (queued), hashed visitor id (no cookies), GeoIP country/city (MaxMind GeoLite2 or Cloudflare `CF-IPCountry`), device/browser/referrer. Daily rollups. Dashboard: views, unique visitors, clicks, **CTR per link**, top links, top countries, referrers, device split, time-series (ApexCharts). Date-range filter.

**PWA** — Manifest + Workbox service worker (installable dashboard, offline shell, cached assets), maskable icons, install prompt.

**Custom Livewire admin** — ✅ **IMPLEMENTED** in Phase 7
  - Overview: platform KPIs, user growth charts, recent signups, top profiles by clicks
  - Users: search/filter (name, email, username), filter by status/plan/role, suspend/restore, promote/demote admin
  - UserDetail: full user view with links, activity history, bulk actions (suspend, restore, publish, admin)
  - Reports: moderation queue with dismiss/action/review, morphTo for Profile/Link/User reporting
  - Settings: site config, feature flags (maintenance, signups, Google login, analytics), reserved usernames CRUD
  - AuditLog: tracks all admin actions with before/after values, admin ID, IP, user agent
  - Guarded by `EnsureAdmin` middleware + `role = admin` check

**Settings & PDPA** — Profile/account, change email/password, connected accounts, **data export** (JSON) and **account+data deletion**, cookie consent, privacy policy.

---

## 8. Performance Strategy

- FrankenPHP/Octane worker mode; OPcache + JIT.
- `spatie/laravel-responsecache` on public pages, keyed by username + theme version; **model observers bust cache** on profile/link edits.
- Cloudflare edge-caches `/{username}` HTML; dynamic tracking kept off the cached path (beacon + redirect).
- Redis for cache/session/queue; heavy writes deferred to Horizon.
- Images → WebP/AVIF, responsive `srcset`, lazy loading; preload fonts; critical CSS via Tailwind.
- Denormalized `clicks_count`; dashboards read rollups, not raw events.

---

## 9. SEO / GEO Strategy

- SSR HTML (crawlable without JS), unique `<title>`/meta/OG per profile, dynamic OG images (queued + cached).
- JSON-LD structured data; `sitemap.xml` (published profiles only); `robots.txt`.
- `hreflang` for `en` + `ms`; canonical URLs.
- Geo: hosting/CDN near MY, `CF-IPCountry` for analytics, local schema where relevant.

---

## 10. Security & PDPA (Malaysia)

- Hashed visitor identifiers, **no tracking cookies** (PDPA-friendly), DNT respected.
- Data export + delete; documented retention; consent on signup.
- Link safety: optional Google Safe Browsing check on save to fight phishing/scam links; report + takedown flow.
- Rate limiting, CSRF, honeypots on public forms, signed URLs where needed.
- 2024 PDPA amendments in mind: breach-notification readiness, DPO contact in privacy policy.

---

## 11. Internationalization

- `lang/en` + `lang/ms`, `SetLocale` middleware (user pref → session → `Accept-Language`), `hreflang`, MYR formatting. English-first UI, BM added during i18n phase.

---

## 12. Malaysian Readiness (billing added later)

- Currency **MYR**; pricing fields tax-aware for **8% SST** on digital services.
- Billing phase: **FPX** via Chip/Billplz (local trust) and/or **Stripe** (cards + Cashier recurring).
- **e-Invoice (LHDN MyInvois)**: billing layer designed to emit e-invoices as the mandate expands.
- Region SG/KL; MYR + BM for local feel.

---

## 13. Build Roadmap

| Phase | Status | Deliverable |
|-------|--------|-------------|
| **0 — Setup** | ✅ Complete | Laravel 13 + Livewire 3 + Tailwind v3 + Phosphor + Breeze + Socialite + Redis + base layouts, `.env`, deploy skeleton |
| **1 — Auth** | ✅ Complete | Email + Google login, username claim onboarding, reserved-word guard |
| **2 — Links** | ✅ Complete | Link editor: CRUD, drag-reorder, types, icon picker, live preview |
| **3 — Public page** | ✅ Complete | SSR bio page, responsive + skeleton, base themes, click/view tracking pipeline |
| **4 — Appearance** | ✅ Complete | Theme customizer (colors, buttons, fonts, backgrounds) + cache busting |
| **5 — Analytics** | ✅ Complete | Event capture → rollups → dashboard charts |
| **6 — Perf/SEO/PWA** | ✅ Complete | Octane, responsecache, Cloudflare, OG images, sitemap, PWA |
| **7 — Admin** | ✅ Complete | Custom Livewire admin: users, reports/moderation, platform analytics, settings, audit logs |
| **8 — i18n & PDPA** | 🔲 Pending | en/ms, privacy policy, terms, data export/delete, cookie consent |
| **9 — Billing (later)** | 🔲 Future | Free→Pro plans, FPX/Stripe, SST, e-invoice, custom domains |

---

## 14. Open Questions (for later phases)

- Free vs Pro **feature split** (custom domains, advanced analytics, remove branding, scheduling).
- Multiple pages per user (Pro) — schema already supports it.
- Chosen payment gateway when billing begins (Chip/Billplz/Stripe).
- Email provider (Resend/Postmark/SES) for transactional mail.

<p align="center"><img src="images/logo.png" alt="Svea Hemfix" width="260"></p>

# Svea Hemfix

Fast, SEO-focused website with a built-in admin panel for a Stockholm cleaning company: service pages, RUT pricing, quote requests, a lead inbox, an auto-published blog and a media gallery.

**Live:** https://sveahemfix.se

---

## About

Svea Hemfix is a cleaning company in Stockholm offering home cleaning, deep cleaning, move-out cleaning, window cleaning, office cleaning, construction cleaning and stairwell cleaning, with the Swedish **RUT deduction** for private customers.

This repository is the source of the client's website. **Anas Alnaasan (via [MIZA Group](https://mizagroup.se))** created the logo and visual design and developed the site, including the admin panel and the blog automation.

The site is written in plain PHP with no framework and no build step. Content that rarely changes, such as services, prices, FAQ and areas, lives in a single PHP data file. Anything that changes at runtime is stored in SQLite: leads, blog posts, settings and logs.

## Features

**Public website (Swedish)**
- Home page with a full-screen video hero, before/after photos, a video reel, reviews, an FAQ and a CTA.
- Service overview (`/tjanster`) and one generated page per service (`/tjanst/<slug>`) covering what's included, add-ons, duration and a service-specific FAQ. The services are home, deep, move-out, window, office and construction cleaning.
- A separate landing page for stairwell cleaning aimed at property owners (`/trapphusstadning`).
- A price list (`/priser`) with prices before and after RUT, plus an interactive RUT calculator.
- A gallery (`/galleri`) with category filters (before/after, video, kitchen, bathroom, office…) and a lightbox for photos and videos.
- A blog (`/blogg`, `/blogg/<slug>`) with BlogPosting and FAQ JSON-LD, reading time and related CTAs.
- A contact/quote form (`/kontakt`) with a honeypot, GDPR consent, AJAX submit and a reference number.
- SEO: LocalBusiness and FAQPage JSON-LD, canonical URLs, a dynamic `sitemap.xml` that includes blog posts, `robots.txt`, a web manifest, clean URLs and 301 redirects for old service URLs.

**Form handling (`sendmail.php`)**
- Validates and sanitises the input, emails the company inbox (with an optional CC) and sends the customer a confirmation email.
- Saves every request as a lead in SQLite, even when sending the email fails. A local log file is the last-resort fallback if the database is unavailable.

**Admin panel (`/admin`)**
- First-run setup wizard that creates the admin account. It only works while no admin exists.
- Dashboard with KPIs (new leads, leads this week, bookings this month, blog posts), recent leads, leads per service over 90 days and the last webhook call.
- Lead inbox with status pipeline (`Ny → Kontaktad → Offert skickad → Bokad / Förlorad / Spam`), search, pagination, internal notes, deletion and CSV export.
- Blog management: list, filter, edit, publish/unpublish, delete, and manual "sync from API".
- Gallery manager: upload photos (auto-converted to WebP + thumbnail via GD, EXIF rotation) and MP4 videos with posters; replace, hide or delete items. It writes `media/manifest.full.json` (all items) and `media/manifest.json` (visible items).
- Settings: notification email and CC, BabyLoveGrowth API key, auto-publish toggle, webhook token rotation, login email and password change, and system info.

**Automated blog publishing (BabyLoveGrowth)**
- `POST /api/blg-webhook.php` receives finished articles from BabyLoveGrowth. Requests are authenticated with a Bearer token (or an `X-API-Key` header) and compared using `hash_equals`.
- Posts are upserted by external article ID. Slugs are generated and de-duplicated, and SEO metadata, keywords, hero image and JSON-LD are stored with the post.
- Every call is logged to `webhook_log`, which keeps the last 200 entries. A pull-based sync through the BabyLoveGrowth API is also available.

## Tech stack

| Layer | Technology |
|---|---|
| Language | PHP 8.1+ (strict types), no framework |
| Database | SQLite via PDO (WAL mode) |
| Frontend | Hand-written CSS (`assets/site.css`) and vanilla JS (`assets/site.js`) |
| Media | GD (WebP conversion), MP4 video |
| Email | PHP `mail()` |
| Server | Apache with `.htaccess` (mod_rewrite), deployed on shared hosting |
| Integrations | BabyLoveGrowth (webhook + REST API) |

## Project structure

```
.
├── index.php, tjanster.php, tjanst.php, priser.php, trapphusstadning.php,
│   galleri.php, blogg.php, blogg-post.php, kontakt.php,
│   integritetspolicy.php, 404.php        # Public pages
├── sendmail.php          # Contact/quote form handler (email + lead storage)
├── sitemap.php           # Dynamic sitemap.xml (static pages, services, blog posts)
├── api/
│   └── blg-webhook.php   # BabyLoveGrowth webhook receiver
├── admin/                # Admin panel: setup, login, dashboard, leads, posts, gallery, settings
├── lib/
│   ├── app.php           # Bootstrap: SQLite connection + schema migration, settings, helpers, leads, blog upsert, BLG API client
│   ├── auth.php          # Sessions, CSRF, login throttling, admin guard
│   └── media.php         # Gallery uploads, WebP conversion, manifest handling
├── data/
│   └── site.php          # Editable site content: services, prices, areas, reviews, FAQ, company details
├── partials/             # Shared header, footer, media strip
├── assets/               # site.css + site.js (what the live site loads)
├── build/assets/         # Older compiled Tailwind v4 / JS bundle (see note below)
├── images/               # Logo, service photos, before/after images, avatars
├── media/
│   ├── hero.mp4, hero.webp
│   ├── p/                # Gallery photos (pNN.webp + pNN-t.webp thumbnails)
│   ├── manifest.json     # Visible gallery items (read by the site)
│   └── manifest.full.json# All gallery items incl. hidden (read/written by admin)
├── .htaccess             # HTTPS/non-www redirect, clean URLs, access rules, caching
└── manifest.json, robots.txt
```

## Getting started

### Requirements
- PHP **8.1+** with these extensions: `pdo_sqlite`, `gd` (with WebP support), `mbstring`, `curl`, `fileinfo`
- Apache with `mod_rewrite` and `AllowOverride All`, needed for clean URLs and the access rules in `.htaccess`
- A working `mail()` setup if the contact form should send email

### Run locally

```bash
git clone <repo-url> svea-hemfix
cd svea-hemfix

# Quick preview with PHP's built-in server.
# .htaccess rewrites are NOT applied here, so use the .php URLs,
# e.g. /tjanster.php, /tjanst.php?s=hemstadning, /blogg-post.php?s=<slug>
php -S localhost:8000
```

For full behaviour (clean URLs, redirects, protected folders), serve the folder through Apache, for example with XAMPP, MAMP or a Docker `php:8.x-apache` image with `mod_rewrite` enabled.

> **Note:** `.htaccess` forces HTTPS and redirects every host to `https://sveahemfix.se`. Comment out that rule block when running under Apache locally.

### Database: created automatically on first run
No SQL file or migration command is needed. The first request that touches the database (for example `/admin`, `/blogg` or a form submit) does the following:

1. `lib/app.php` → `db()` opens `data/app.sqlite`. PDO creates the file if it doesn't exist, and on creation it is `chmod 0600`.
2. SQLite is switched to WAL mode with foreign keys on.
3. `migrate()` creates the tables `settings`, `leads`, `lead_notes`, `posts`, `webhook_log` and `login_attempts` (schema version 1, tracked in `settings`).

`data/` has to be writable by the web server. `.htaccess` blocks web access to `data/`, `lib/` and `partials/`, and to `*.sqlite`/`*.log` files.

### Admin setup
1. Open `/admin/`. With an empty database you're redirected to `/admin/setup.php`.
2. Create the admin account (email + password, minimum 10 characters). This also seeds the default settings and generates a random webhook token.
3. Under **Inställningar** (Settings), set the notification email and the optional BabyLoveGrowth API key, and copy the webhook URL and token into BabyLoveGrowth.

### Configuration
The project doesn't use environment variables. Runtime configuration (admin credentials, notification addresses, API key, webhook secret) is stored in the SQLite `settings` table and edited from the admin panel. Static content and prices live in `data/site.php`.

## What's not in this repository

- **Live database** (`data/app.sqlite`): it holds real customer requests, so it was intentionally left out. A fresh, empty database is created on first run (see above). The blog and the admin panel start out empty.
- **Contact-form fallback log** (`_forfragningar.log`): left out for the same reason.
- **Gallery videos** (`media/v/`): left out because of their size. The pages and both media manifests still reference them (`/media/v/vNN.mp4` and `vNN.webp` posters for the hero sections, the home reel, the gallery and the service pages), so videos won't play locally until you add your own MP4s with matching IDs or upload new ones through the admin gallery. The home page hero video (`media/hero.mp4`) and all gallery photos (`media/p/`) are included.
- **`build/` folder:** a compiled Tailwind CSS v4 bundle plus small JS modules (nav, carousel, accordion, RUT calculator, etc.) from an earlier iteration of the site. None of the current PHP templates load it (they use `assets/site.css` and `assets/site.js`), and it contains no references to `media/v`. It's kept for reference only.

## Security notes

- Admin passwords are hashed with `password_hash()` and checked with `password_verify()`. Nothing is hard-coded.
- Sessions use a custom name and `HttpOnly`, `SameSite=Lax` and `Secure` (on HTTPS) cookies. The session ID is regenerated on login, and sessions expire after 8 hours of inactivity.
- All admin forms are CSRF-protected.
- Login is throttled per IP: 5 failed attempts trigger a 15-minute lockout.
- Webhook requests are authenticated with a random 40-character hex token (rotatable in the admin panel) and compared in constant time.
- The SQLite file is created with `0600` permissions. `.htaccess` denies web access to `data/`, `lib/`, `partials/`, `*.sqlite*`, `*.log` and `*.bak`, and `media/.htaccess` stops scripts from running in the upload folder.
- Form input is sanitised, user output is escaped with `htmlspecialchars`, and the contact form has a honeypot field.
- No API keys, tokens or customer data are committed. Secrets are only created at runtime in the database.

## Built with AI-assisted development

Built by **Anas Alnaasan** with AI coding assistants (Claude and ChatGPT). The product thinking, design and UI/UX, SEO, prompting, code review, testing and deployment were his own work.

## Author

**Anas Alnaasan** · [MIZA Group](https://mizagroup.se) · https://mizagroup.se · https://github.com/anasnassan92-cmyk

Logo, design and development by MIZA Group for Svea Hemfix.

---

Source shared for portfolio purposes. All brand names, logos and media belong to their respective owners.

# SOUND Group — Production Deployment Guide

## A. Project Requirements

| Requirement | Details |
|---|---|
| **PHP** | 8.2+ (uses `${}` interpolation fix, typed features) |
| **PHP Extensions** | PDO, PDO_MySQL, cURL, OpenSSL, mbstring, fileinfo, JSON, sessions, curl |
| **MySQL** | 5.7+ or MariaDB 10.4+ |
| **Apache** | mod_rewrite required (.htaccess) |
| **Composer** | Required — installs `vendor/` dependencies |
| **Node/npm** | NOT required at runtime (only for Playwright testing) |

---

## B. Root Folder Deployment Table

| Item | Upload to Production? | Purpose | Notes |
|---|---|---|---|
| `backend/` | YES | PHP handlers, config, helpers, includes | Core application logic |
| `frontend/` | YES | Admin panel + public website | PHP pages, JS, CSS |
| `vendor/` | YES | Composer dependencies | Run `composer install` on server |
| `node_modules/` | NO | npm dependencies (Playwright) | Development/testing only |
| `.env` | YES (configure on server) | Environment configuration | Set production values on server |
| `.env.example` | YES | Template for `.env` | Reference for production setup |
| `.gitignore` | NO | Git ignore rules | Not needed on production |
| `.htaccess` | YES | Apache rewrite rules, security, caching | Required for routing and security |
| `composer.json` | YES | PHP dependency definitions | Needed if running `composer install` on server |
| `composer.lock` | YES | Locked PHP dependency versions | Ensures reproducible builds |
| `index.php` | YES | Root router → redirects to website | Entry point |
| `package.json` | NO | npm dependencies (Playwright) | Not needed on production |
| `package-lock.json` | NO | Locked npm versions | Not needed on production |
| `php.ini` | MAYBE | PHP settings override | Upload if hosting allows `.user.ini` or `php.ini` |

---

## C. Environment Configuration

### Local Development (XAMPP)

In `.env`:
```
APP_ENV=local
APP_URL=http://localhost/Aptech_E_Project_02/sound_management
DB_HOST=127.0.0.1
DB_DATABASE=sound_management
DB_USERNAME=root
DB_PASSWORD=
```

### Production (InfinityFree)

In `.env` on the server, set:
```
APP_ENV=production
APP_URL=https://yourdomain.com
DB_HOST=<from InfinityFree panel>
DB_DATABASE=<from InfinityFree panel>
DB_USERNAME=<from InfinityFree panel>
DB_PASSWORD=<from InfinityFree panel>
CLOUDINARY_CLOUD_NAME=<same as local>
CLOUDINARY_API_KEY=<same as local>
CLOUDINARY_API_SECRET=<same as local>
```

---

## D. InfinityFree Information Checklist

After creating your InfinityFree hosting account and MySQL database, collect:

- [ ] **Database Host** — shown in InfinityFree MySQL panel (e.g., `sql123.infinityfree.com`)
- [ ] **Database Name** — your created database name (e.g., `if0000000_sound`)
- [ ] **Database Username** — usually same as database name
- [ ] **Database Password** — the password you set
- [ ] **Database Port** — usually `3306` (default)
- [ ] **Application URL** — your domain (e.g., `https://yourdomain.com` or subfolder)
- [ ] **Document Root** — InfinityFree typically serves from `htdocs/`
- [ ] **FTP/SFTP Credentials** — for file upload (from InfinityFree panel)

---

## E. Configuration Locations

Every value that needs changing for production:

| Variable | File | Line | What to Enter |
|---|---|---|---|
| `APP_ENV` | `.env` | 5 | `production` |
| `APP_URL` | `.env` | 14 | Your live domain URL |
| `DB_HOST` | `.env` | 19 | InfinityFree MySQL host |
| `DB_DATABASE` | `.env` | 20 | InfinityFree database name |
| `DB_USERNAME` | `.env` | 21 | InfinityFree database username |
| `DB_PASSWORD` | `.env` | 22 | InfinityFree database password |
| `CLOUDINARY_CLOUD_NAME` | `.env` | 31 | Same as local |
| `CLOUDINARY_API_KEY` | `.env` | 32 | Same as local |
| `CLOUDINARY_API_SECRET` | `.env` | 33 | Same as local |
| `MAIL_USERNAME` | `.env` | 25 | Your email |
| `MAIL_PASSWORD` | `.env` | 26 | Your SMTP app password |

---

## F. Local ↔ Production Switching

### LOCAL MODE (XAMPP)

1. Ensure `APP_ENV=local` in `.env`
2. Ensure `APP_URL=http://localhost/Aptech_E_Project_02/sound_management` in `.env`
3. Ensure MySQL is running on XAMPP with credentials matching `.env`
4. Start Apache in XAMPP
5. Visit `http://localhost/Aptech_E_Project_02/sound_management`

### PRODUCTION MODE (InfinityFree)

1. Set `APP_ENV=production` in `.env` on the server
2. Set `APP_URL=https://yourdomain.com` in `.env` on the server
3. Set all `DB_*` values from your InfinityFree MySQL panel
4. Upload all project files (except `node_modules/`)
5. Run `composer install` on the server (or upload `vendor/` from local)
6. Visit your live domain

**You do NOT need to edit any PHP files to switch environments.**

---

## G. Deployment Checklist

1. Create InfinityFree hosting account
2. Create MySQL database in InfinityFree panel
3. Collect database credentials (host, name, username, password)
4. Export your latest local database:
   - In phpMyAdmin, select `sound_management` database
   - Click Export → Quick → Go → save the `.sql` file
5. Import database into InfinityFree MySQL (via phpMyAdmin or SQL import)
6. Create `.env` on the server with production values (use `.env.example` as template)
7. Upload all project files:
   - Upload `backend/`, `frontend/`, `vendor/`, `index.php`, `.htaccess`, `.env`, `composer.json`, `composer.lock`
   - Upload `php.ini` if hosting supports it
   - Do NOT upload `node_modules/`, `.git/`, `.gitignore`
8. Verify PHP version is 8.2+ in InfinityFree panel
9. Verify required PHP extensions are enabled (PDO, PDO_MySQL, cURL, mbstring, fileinfo)
10. Verify `.htaccess` is working (try visiting a clean URL)
11. Verify Cloudinary — upload a test image from admin panel
12. Verify application URL — all redirects and AJAX calls should work
13. Test admin login (`/frontend/admin/authentication/login.php`)
14. Test admin panel — dashboard, music, videos, categories, users, settings
15. Test database reads/writes — add/edit/delete a record
16. Test music — upload, play, edit, delete
17. Test videos — upload, play, edit, delete
18. Test thumbnails/covers — upload and display
19. Test profile images — user and admin
20. Test website logo — upload and display on website
21. Test update/delete operations — verify Cloudinary assets are handled
22. Check error logs for any issues
23. Final production verification — browse entire website and admin panel

---

## H. Things NOT to Upload

| Item | Reason |
|---|---|
| `node_modules/` | Development/testing only (Playwright) |
| `package.json` | Not needed for PHP runtime |
| `package-lock.json` | Not needed for PHP runtime |
| `.git/` | Version control — not needed on production |
| `.gitignore` | Git config — not needed on production |
| `.env.example` | Template only — not needed on production |

---

## I. Things that MUST be Uploaded

| Item | Reason |
|---|---|
| `backend/` | All PHP logic (handlers, config, helpers, includes) |
| `frontend/` | All PHP pages, JS, CSS (admin + website) |
| `vendor/` | Composer dependencies (Cloudinary SDK, PHPMailer) |
| `index.php` | Root router |
| `.htaccess` | Apache rules (routing, security, caching) |
| `.env` | Environment config (configure production values on server) |
| `composer.json` | Dependency definitions |
| `composer.lock` | Locked dependency versions |
| `php.ini` | PHP settings override (if hosting supports it) |
| `backend/database/database.sql` | Reference schema (for manual import if needed) |

---

## J. Security Notes

- `.env` is in `.gitignore` and must NOT be committed to version control
- `.htaccess` blocks direct access to `.env`, `.git`, `.md`, `.lock`, `.log`, `.sql` files
- Cloudinary API secret is only in `.env` — never in source code
- Database credentials are only in `.env` — never hardcoded in PHP
- `APP_DEBUG=false` in production — no error details exposed to users
- Session cookies are `httponly`, `secure`-aware, and use `SameSite=Lax`
- CSRF protection is implemented on all forms

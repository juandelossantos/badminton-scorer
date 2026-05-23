# 🚀 Deployment Guide — Badminton Scorer
# Domain: https://badminton-scorer.deunaybienbonito.com
# Hosting: Shared hosting (cPanel) — NO subdomain available

---

## Overview

This guide deploys everything into a single `public_html/` folder:
- Frontend (React SPA) at the root
- Backend (PHP API) in subfolders (`handlers/`, `config/`, `models/`, `vendor/`)

**URLs after deploy:**
- Frontend: `https://badminton-scorer.deunaybienbonito.com`
- API: `https://badminton-scorer.deunaybienbonito.com/api/matches`

---

## Step 1: Database Setup

1. Log in to your hosting **cPanel**
2. Open **phpMyAdmin**
3. Select your database `huitacad_badmintonscore`
4. Go to the **Import** tab
5. Choose file: `database/prod-setup.sql` (from this repo)
6. Click **Go**

✅ The `matches` table should be created with all columns.

---

## Step 2: Upload Frontend (Build)

On your local machine:

```bash
cd frontend/
npm install
npm run build
```

This creates a `dist/` folder containing:
```
dist/
  ├── index.html
  └── assets/
      ├── index-XXXX.js
      ├── index-XXXX.css
      └── ...
```

**Upload ALL contents of `dist/` to your hosting `public_html/` folder.**

Result should be:
```
public_html/
  ├── index.html          ← from dist/
  └── assets/             ← from dist/
      ├── index-XXXX.js
      └── index-XXXX.css
```

---

## Step 3: Upload Backend

**Create these folders inside `public_html/`:**
```
public_html/
  ├── handlers/           ← upload from backend/handlers/
  │   ├── health.php
  │   └── matches/
  │       ├── create.php
  │       ├── read.php
  │       ├── score.php
  │       └── end.php
  ├── models/           ← upload from backend/models/
  │   └── MatchModel.php
  ├── config/           ← upload from backend/config/
  │   └── database.php
  └── vendor/           ← upload from backend/vendor/
      └── ... (composer dependencies)
```

**Also upload these files to `public_html/`:**
- `composer.json`
- `composer.lock`
- `index.php` (from backend/)

**Note:** Do NOT upload `tests/`, `Dockerfile`, `phpunit.xml`, or `.env`.

---

## Step 4: Upload Production .htaccess

Upload `backend/.htaccess.production` to `public_html/.htaccess`

**This single .htaccess file does three things:**
1. Routes `/api/...` requests to PHP handlers
2. Serves frontend SPA (all non-API routes → `index.html`)
3. Sets environment variables for database connection
4. Protects backend files from direct access

---

## Step 5: Verify API Health

Open in browser:
```
https://badminton-scorer.deunaybienbonito.com/api/health
```

Expected response:
```json
{"status":"ok","database":"connected"}
```

---

## Step 6: Test Full Flow

1. Open `https://badminton-scorer.deunaybienbonito.com`
2. Click "NUEVO PARTIDO"
3. Enter player names and create a match
4. Verify the ShareURL page shows both URLs
5. Test the controller (with `?token=`)
6. Test the TV view at `/watch/:id`
7. Play some points and verify sync

---

## 🗂️ Final Hosting Structure

```
/home/youruser/
└── public_html/                          ← web root
    ├── index.html                        ← React SPA entry
    ├── assets/                           ← JS/CSS bundles
    │
    ├── handlers/                         ← PHP API handlers
    │   ├── health.php
    │   └── matches/
    │       ├── create.php
    │       ├── read.php
    │       ├── score.php
    │       └── end.php
    ├── models/
    │   └── MatchModel.php
    ├── config/
    │   └── database.php
    ├── vendor/                           ← Composer deps
    │
    ├── composer.json
    ├── composer.lock
    └── .htaccess                         ← Router + CORS + Security
```

---

## 🔒 Security Notes

- **`.htaccess`** blocks access to `/config/`, `/models/`, `/vendor/`, `/tests/`
- **`.env`** files are blocked from web access
- **Control token**: Only the creator gets the `?token=` URL; TV viewers see `/watch/:id` (no token)
- **Database credentials**: Stored in Apache environment variables (`SetEnv`), not in code

---

## 🛠️ Troubleshooting

### CORS errors in browser console
- Verify `.htaccess` CORS headers are present
- Check that `Access-Control-Allow-Origin` matches or uses `*`

### "Database connection failed" error
- Check cPanel database credentials match the `SetEnv` values in `.htaccess`
- Try `localhost` or `127.0.0.1` for `DB_HOST`

### 404 on API routes (`/api/matches`)
- Verify `.htaccess` uploaded correctly to `public_html/`
- Check hosting supports `mod_rewrite` (most do, but verify)
- Ensure `handlers/` folder exists with correct permissions (755)

### Blank page / broken frontend
- Verify `index.html` and `assets/` are in `public_html/`
- Check browser console for JS errors
- Confirm `VITE_API_URL` in `.env.production` points to your domain

### "Unauthorized" or 401 errors when scoring
- Check that the URL includes `?token=...` parameter
- Verify the match was created successfully (check DB via phpMyAdmin)

---

## 🔄 Updates After Deploy

### Frontend update:
```bash
cd frontend/
npm run build
# Re-upload dist/ contents to public_html/
```

### Backend update:
```bash
# Re-upload modified files (handlers/, models/, config/) to public_html/
```

---

**Questions?** Check the README.md and spec.md in the repository root.

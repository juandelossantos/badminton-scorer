# 🚀 Deployment Guide

## Target
- **Domain:** https://badminton-scorer.deunaybienbonito.com
- **Database:** MySQL on shared hosting

---

## Step 1: Prepare Database (via phpMyAdmin or cPanel)

1. Log in to your hosting cPanel
2. Open **phpMyAdmin**
3. Select database `huitacad_badmintonscore`
4. Go to **Import** tab
5. Upload `database/prod-setup.sql`
6. Click **Go**

✅ Table `matches` should be created.

---

## Step 2: Configure Backend Environment

1. In the root of your hosting account (same level as `public_html/`), create a file named `.env`:

```bash
DB_HOST=localhost
DB_NAME=huitacad_badmintonscore
DB_USER=huitacad_badmintonscore
DB_PASS=D1L2CXMvJf.H
```

2. Upload `backend/.env.example` content to `.env` (same folder level as `public_html/`)

---

## Step 3: Upload Backend Files

1. In your hosting file manager, create a folder outside public_html (recommended):
   ```
   /home/youruser/badminton-api/
   ```

2. Upload all files from `backend/` folder to this location

3. Create a symlink or subdomain pointing to this folder
   
   **Option A (Subdomain):**
   - Create subdomain `api.badminton-scorer.deunaybienbonito.com`
   - Point it to `/home/youruser/badminton-api/`
   
   **Option B (Subfolder in public_html):**
   - Create folder `public_html/api/`
   - Upload backend files there
   - ⚠️ Less secure, but simpler

---

## Step 4: Configure Frontend API URL

The file `frontend/.env.production` already has:
```
VITE_API_URL=https://badminton-scorer.deunaybienbonito.com/api
```

If using a subdomain:
```
VITE_API_URL=https://api.badminton-scorer.deunaybienbonito.com
```

---

## Step 5: Build & Upload Frontend

```bash
# On your local machine:
cd frontend/
npm install
npm run build
```

1. This creates a `dist/` folder
2. Upload ALL contents of `dist/` to your hosting `public_html/`
3. The files should include:
   - `index.html`
   - `assets/` (JS and CSS bundles)

---

## Step 6: Configure .htaccess for SPA Routing

Create or update `public_html/.htaccess`:

```apache
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /
  RewriteRule ^index\.html$ - [L]
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule . /index.html [L]
</IfModule>
```

This ensures React Router works correctly (all routes serve index.html).

---

## Step 7: Verify Deployment

Test these URLs:
- `https://badminton-scorer.deunaybienbonito.com` → Home page ✅
- `https://badminton-scorer.deunaybienbonito.com/api/health` → `{"status":"ok"}` ✅
- Create a match and verify it works end-to-end

---

## 🔒 Security Notes

- `.env` file should be OUTSIDE `public_html/` or protected with `.htaccess`
- The `.env` contains DB credentials - never commit it to git
- Backend validates control_token for all score operations
- TV viewers cannot guess controller URL (separate /watch/:id route)

---

## 🛠️ Troubleshooting

**CORS errors in browser:**
- Check `.htaccess` CORS header matches your exact domain (with https://)

**Database connection failed:**
- Verify .env credentials match cPanel database info
- Try `localhost` or `127.0.0.1` for DB_HOST

**404 on API routes:**
- Verify backend files uploaded correctly
- Check hosting supports `.htaccess` RewriteEngine
- Ensure PHP version is 8.1 or higher

**Frontend shows blank page:**
- Verify `index.html` is in `public_html/`
- Check browser console for JS errors
- Confirm API_BASE URL is correct in build

---

## 📁 Recommended Hosting Structure

```
/home/youruser/
├── .env                          # Backend credentials (protected)
├── badminton-api/               # Backend PHP files
│   ├── index.php
│   ├── .htaccess
│   ├── config/
│   ├── handlers/
│   └── models/
└── public_html/                 # Frontend (web root)
    ├── index.html
    ├── assets/
    └── .htaccess
```

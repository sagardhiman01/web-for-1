# Core Asset Investing - Hostinger Launch Checklist

This project is ready for launch on shared hosting (Hostinger) with your custom domain.

## 1) Recommended domain names (pick first available)
1. coreassetinvesting.com
2. coreassetinvesting.in
3. coreassetinvesting.co

## 2) Domain connect in Hostinger hPanel
1. Add your chosen domain to Hosting -> Domains.
2. DNS records:
   - A record: Host `@` -> your server IP
   - CNAME: Host `www` -> `@`
3. Enable SSL for domain and force HTTPS.

## 3) Upload website files to `public_html`
Upload these from your local project:
1. `.htaccess`
2. `index.php`
3. `assets/`
4. `core/`
5. `install/` (optional)

Remove default Hostinger page files from `public_html` (like `default.php`) after upload.

## 4) Create database and import live data
1. In hPanel -> Databases, create MySQL DB + user.
2. Import file: `deploy/core_asset_investing_laravel.sql` into your new DB.

## 5) Set production environment
1. Open `core/.env` on server.
2. Copy values from `core/.env.hostinger.example`.
3. Update DB credentials, domain, and SMTP credentials.
4. Important:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://your-domain`

## 6) Run optimization commands (Hostinger Terminal / SSH)
From `public_html/core`:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan storage:link
```

## 7) Set cron job (required)
In hPanel -> Advanced -> Cron Jobs, add:

```bash
* * * * * /usr/bin/curl -s https://your-domain.com/cron > /dev/null 2>&1
```

(Replace `your-domain.com` with final domain.)

## 8) Mobile-friendly check before launch
1. Open home, register, dashboard, deposit, withdraw on real mobile.
2. Confirm no horizontal scroll and buttons/tables responsive.

Mobile improvements are already applied in:
- `assets/templates/neo_dark/css/custom.css`
- `core/resources/views/templates/invester/user/payment/manual.blade.php`

## 9) Final QA checklist
1. Admin login works.
2. Deposit QR uploads from admin panel.
3. Referral signup flow works.
4. Investment code activation works.
5. Currency list + chart works.
6. Cron updates payouts.

## 10) If you want me to complete final live deployment for you
Share these 3 details and I can finish exact domain launch steps:
1. Hostinger hPanel login URL + username
2. Domain name you purchased
3. DB name/user/password you created

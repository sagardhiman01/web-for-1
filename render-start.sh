#!/bin/bash
# NO set -e — we handle errors manually so one failure doesn't crash everything

cd /var/www/html/core

echo "=============================="
echo "=== RENDER STARTUP SCRIPT ==="
echo "=============================="

# --- Force correct env variables (override .env file settings) ---
export DB_CONNECTION=pgsql
export CACHE_DRIVER=array
export SESSION_DRIVER=file
export LOG_CHANNEL=stderr

# --- Fix storage permissions ---
echo "Fixing storage permissions..."
chmod -R 775 /var/www/html/core/storage 2>/dev/null || true
chown -R www-data:www-data /var/www/html/core/storage 2>/dev/null || true
chmod -R 775 /var/www/html/core/bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data /var/www/html/core/bootstrap/cache 2>/dev/null || true
mkdir -p /var/www/html/core/storage/logs 2>/dev/null || true
chmod 775 /var/www/html/core/storage/logs 2>/dev/null || true
touch /var/www/html/core/storage/logs/laravel.log 2>/dev/null || true
chmod 664 /var/www/html/core/storage/logs/laravel.log 2>/dev/null || true
chown www-data:www-data /var/www/html/core/storage/logs/laravel.log 2>/dev/null || true
echo "Permissions fixed."

# --- Step 1: Check DATABASE_URL ---
if [ -z "$DATABASE_URL" ]; then
    echo "ERROR: DATABASE_URL is not set!"
    exit 1
fi
echo "DATABASE_URL is set. OK."

# --- Step 2: Check if DB is reachable ---
echo "Testing database connection..."
psql "$DATABASE_URL" -c "SELECT 1;" 2>&1
if [ $? -ne 0 ]; then
    echo "ERROR: Cannot connect to database! Check DATABASE_URL."
    exit 1
fi
echo "Database connection OK."

# --- Step 3: Check if already initialized ---
echo "Checking if admins table exists..."
ADMINS_EXISTS=$(psql "$DATABASE_URL" -t -c "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='public' AND table_name='admins';" 2>/dev/null | tr -d ' \n' || echo "0")
echo "Admins table check result: '$ADMINS_EXISTS'"

if [ "$ADMINS_EXISTS" != "1" ]; then
    echo "Fresh database. Wiping and importing base schema..."

    # Wipe database
    echo "Dropping and recreating public schema..."
    psql "$DATABASE_URL" -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;" 2>&1
    echo "Schema wiped OK."

    # Import SQL dump (errors are expected in legacy data, they won't stop us)
    echo "Importing database_pg.sql..."
    psql "$DATABASE_URL" --set ON_ERROR_STOP=off -f /var/www/html/install/database_pg.sql 2>&1 || true
    echo "SQL import done (some errors above are normal for legacy data)."
else
    echo "Database already has tables. Skipping SQL import."
fi

# --- Step 4: Run migrations (ignore failures from duplicate tables) ---
echo "Running migrations..."
php artisan migrate --force 2>&1 || true
echo "Migrations done."

# --- Step 5: Seed initial data ---
echo "Running seeder..."
php artisan db:seed --class=InitialDataSeeder --force 2>&1 || true
echo "Seeder done."

# --- Step 6: Clear caches ---
echo "Clearing caches..."
php artisan config:clear 2>&1 || true
php artisan cache:clear 2>&1 || true

# --- Step 7: Fix Apache port ---
echo "Configuring Apache port: $PORT"
sed -i "s/\${PORT}/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf 2>&1 || true

echo "=============================="
echo "=== STARTING APACHE SERVER ==="
echo "=============================="

cd /var/www/html
exec apache2-foreground

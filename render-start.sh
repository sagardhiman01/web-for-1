#!/bin/bash
set -e

# Navigate to the core directory
cd /var/www/html/core

echo "Checking database status..."

# Check if the admins table actually exists in the database
ADMINS_EXISTS=$(php artisan tinker --execute="
try { echo \Illuminate\Support\Facades\Schema::hasTable('admins') ? '1' : '0'; }
catch(\Exception \$e) { echo '0'; }
" --quiet 2>/dev/null | grep -oE '[01]' | tail -1 || echo "0")

if [ "$ADMINS_EXISTS" != "1" ]; then
    echo "Fresh database detected (or missing admins table). Wiping database and importing base schema..."
    if [ ! -z "$DATABASE_URL" ]; then
        # Drop and recreate the public schema to ensure a clean state
        psql "$DATABASE_URL" -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;"
        
        # Import the base SQL file
        psql "$DATABASE_URL" -f /var/www/html/install/database_pg.sql
        echo "Base schema imported successfully."
    else
        echo "ERROR: DATABASE_URL is not set. Cannot import base schema."
        exit 1
    fi
    echo "Running incremental migrations..."
    php artisan migrate --force
else
    echo "Tables exist. Running incremental migrations..."
    php artisan migrate --force
fi

# Seed initial data using Laravel seeder (avoids MySQL->PostgreSQL SQL conversion issues)
# The seeder checks if data exists before inserting, so it's safe to run multiple times
echo "Running initial data seeder..."
php artisan db:seed --class=InitialDataSeeder --force

# Replace the PORT in Apache configuration
sed -i "s/\${PORT}/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Start the actual web server
cd /var/www/html
apache2-foreground

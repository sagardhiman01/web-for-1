#!/bin/bash
set -e

# Navigate to the core directory
cd /var/www/html/core

echo "Checking database status..."

# Check if the admins table actually exists in the database
# If migrations table has records but real tables don't exist,
# we must do migrate:fresh to wipe the stale migrations table and start clean
ADMINS_EXISTS=$(php artisan tinker --execute="
try { echo \Illuminate\Support\Facades\Schema::hasTable('admins') ? '1' : '0'; }
catch(\Exception \$e) { echo '0'; }
" --quiet 2>/dev/null | grep -oE '[01]' | tail -1 || echo "0")

if [ "$ADMINS_EXISTS" != "1" ]; then
    echo "Fresh database detected (or stale migrations). Running migrate:fresh..."
    php artisan migrate:fresh --force
else
    echo "Tables exist. Running incremental migrate..."
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

#!/bin/bash

# Navigate to the core directory
cd /var/www/html/core

# Run migrations if the database is empty or needs updates
# The --force flag is required for production
php artisan migrate --force

# Check if the admins table exists and has data
# We use tinker to get the count and filter only numeric output
ADMIN_COUNT=$(php artisan tinker --execute="echo App\Models\Admin::count();" --quiet 2>/dev/null | grep -oE '^[0-9]+$')

if [ -z "$ADMIN_COUNT" ] || [ "$ADMIN_COUNT" -eq "0" ]; then
    echo "Admins table is empty or doesn't exist. Importing initial data from database_pg.sql..."
    psql $DATABASE_URL -f /var/www/html/install/database_pg.sql
fi

# Replace the PORT in Apache configuration
sed -i "s/\${PORT}/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Start the actual web server
cd /var/www/html
apache2-foreground

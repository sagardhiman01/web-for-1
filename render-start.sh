#!/bin/bash

# Navigate to the core directory
cd /var/www/html/core

# Run migrations if the database is empty or needs updates
# The --force flag is required for production
php artisan migrate --force

# Check if the admins table is empty and seed it if necessary
# We use the converted PostgreSQL compatible SQL file
ADMIN_COUNT=$(php artisan tinker --execute="echo App\Models\Admin::count();")
if [ "$ADMIN_COUNT" -eq "0" ]; then
    echo "Database is empty. Importing initial data from database_pg.sql..."
    psql $DATABASE_URL -f /var/www/html/install/database_pg.sql
fi

# Replace the PORT in Apache configuration
sed -i "s/\${PORT}/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Start the actual web server
cd /var/www/html
apache2-foreground

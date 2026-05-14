#!/bin/bash

# Navigate to the core directory
cd /var/www/html/core

# Run migrations if the database is empty or needs updates
# The --force flag is required for production
php artisan migrate --force

# Seed initial data using Laravel seeder (avoids MySQL->PostgreSQL SQL conversion issues)
# The seeder checks if data exists before inserting, so it's safe to run multiple times
echo "Running initial data seeder..."
php artisan db:seed --class=InitialDataSeeder --force

# Replace the PORT in Apache configuration
sed -i "s/\${PORT}/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Start the actual web server
cd /var/www/html
apache2-foreground

#!/bin/sh

# Create the SQLite database if it doesn't exist
if [ ! -f /var/www/html/database/database.sqlite ]; then
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
fi

# Set proper ownership for the database directory and files
chown -R www-data:www-data /var/www/html/database
chown www-data:www-data /var/www/html/database/database.sqlite

# Run migrations
php artisan migrate --force

# Cache configuration, routes, and views for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start supervisor
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf

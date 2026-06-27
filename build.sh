#!/usr/bin/env bash
# Exit on error
set -o errexit

# Install composer dependencies
composer install --no-dev --optimize-autoloader

# Clear caches
php artisan optimize:clear

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations (Force to run in production)
php artisan migrate --force

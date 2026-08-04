#!/bin/sh

set -e

echo "Preparing Laravel application..."

mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan view:cache

php artisan storage:link 2>/dev/null || true

echo "Starting Apache on port 80..."

exec apache2-foreground
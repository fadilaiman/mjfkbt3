#!/bin/bash
set -e

echo "==> Waiting for MySQL..."
until php -r "new PDO('mysql:host=${DB_HOST};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
    echo "    MySQL not ready yet — retrying in 3s..."
    sleep 3
done
echo "==> MySQL is ready."

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Creating storage link..."
php artisan storage:link --force 2>/dev/null || true

echo "==> Caching config / routes / views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "==> Starting PHP-FPM..."
exec "$@"

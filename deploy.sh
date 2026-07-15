#!/usr/bin/env bash
set -euo pipefail

cd /var/www/tutash-hudud
git pull --ff-only origin main
composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache public/build
chmod -R ug+rwX storage bootstrap/cache
sudo systemctl reload php8.4-fpm
sudo systemctl reload nginx

echo "Deployment completed."

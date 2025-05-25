#!/bin/sh

mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/bootstrap/cache

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

php artisan config:clear
php artisan view:clear
php artisan cache:clear

exec php-fpm

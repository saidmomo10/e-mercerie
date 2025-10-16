#!/bin/bash
set -e

composer install --no-dev --optimize-autoloader

php artisan key:generate --force

php artisan migrate --force
php artisan db:seed --force

exec "$@"

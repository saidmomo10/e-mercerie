#!/bin/bash
set -e

php artisan key:generate --force

php artisan migrate --force
php artisan db:seed --force

exec "$@"

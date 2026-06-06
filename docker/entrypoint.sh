#!/bin/sh
set -eu

cd /var/www/html

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    attempts=0
    until php artisan migrate --force; do
        attempts=$((attempts + 1))
        if [ "$attempts" -ge 30 ]; then
            echo "Database did not become ready after 30 attempts." >&2
            exit 1
        fi
        sleep 2
    done

    php docker/seed-if-empty.php
    php artisan storage:link --force
    php artisan optimize
fi

exec "$@"

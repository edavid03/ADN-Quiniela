#!/bin/sh
set -eu

cd /var/www/html

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache

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

# Use Railway's PORT if available, otherwise default to 8080
APP_PORT="${PORT:-8080}"

# Replace port in CMD if artisan serve is used
if [ "$1" = "php" ] && [ "$2" = "artisan" ] && [ "$3" = "serve" ]; then
    exec php artisan serve --host=0.0.0.0 --port="$APP_PORT"
fi

exec "$@"

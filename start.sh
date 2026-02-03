#!/bin/bash
set -e

echo "=== Starting Application ==="
echo "Waiting for database connection..."

sleep 5

echo "Running database migrations..."
php artisan migrate --force --verbose

echo "Linking storage (public/storage -> storage/app/public)..."
php artisan storage:link 2>/dev/null || true

echo "Ensuring storage directories exist (required when using a volume at /app/storage)..."
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs storage/app/public

echo "Migrations completed!"
echo "Starting web server..."

exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}

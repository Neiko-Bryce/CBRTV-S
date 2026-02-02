#!/bin/bash
set -e

echo "=== Starting Application ==="
echo "Waiting for database connection..."

# Wait a few seconds for database to be fully ready
sleep 5

echo "Running database migrations..."
php artisan migrate --force --verbose

echo "Migrations completed!"
echo "Starting web server..."

php artisan serve --host=0.0.0.0 --port=${PORT:-8080}

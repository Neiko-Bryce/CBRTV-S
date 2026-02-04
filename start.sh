#!/bin/bash
set -e

# Ensure we run from the application root (required on Railway)
cd "$(dirname "$0")"
echo "Working directory: $(pwd)"

echo "=== Starting Application ==="
echo "Waiting for database connection..."

sleep 5

echo "Ensuring storage directories exist (required when using a volume at /app/storage)..."
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs storage/app/public

# Run migrations with retries (DB may not be ready immediately). If migrations fail after retries, still start the server so the app responds.
echo "Running database migrations..."
MIGRATE_OK=0
for attempt in 1 2 3; do
  if php artisan migrate --force --verbose 2>&1; then
    MIGRATE_OK=1
    break
  fi
  echo "Migration attempt $attempt failed, retrying in 5s..."
  sleep 5
done
if [ "$MIGRATE_OK" -eq 0 ]; then
  echo "WARNING: Migrations did not run successfully. The app will start anyway. Check deploy logs or run: php artisan migrate --force"
fi

echo "Linking storage (public/storage -> storage/app/public)..."
php artisan storage:link 2>/dev/null || true

echo "Starting web server..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}

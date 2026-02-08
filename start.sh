#!/bin/bash

# Ensure we run from the application root (required on Railway)
cd "$(dirname "$0")"
echo "Working directory: $(pwd)"

echo "=== Starting Application ==="

# Clear caches first to avoid issues with old config/routes
echo "Clearing application cache..."
php artisan optimize:clear

echo "Waiting for database connection..."
sleep 5

echo "Ensuring storage directories exist (required when using a volume at /app/storage)..."
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs storage/app/public || true

# Run migrations with retries (DB may not be ready immediately).
# We MUST fail if migrations fail to prevent app from running with broken schema.
echo "Running database migrations..."
MIGRATE_OK=0
for attempt in 1 2 3; do
  echo "Migration attempt $attempt..."
  if php artisan migrate --force --verbose; then
    MIGRATE_OK=1
    echo "✓ Migrations completed successfully"
    break
  fi
  echo "Migration attempt $attempt failed, retrying in 5s..."
  sleep 5
done

if [ "$MIGRATE_OK" -eq 0 ]; then
  echo "CRITICAL ERROR: Migrations failed after 3 attempts. Deployment cannot proceed."
  exit 1
fi

echo "Initializing landing page database table..."
php -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); require 'database/init-landing-page-table.php';" || echo "WARNING: Database initialization script failed"

echo "Linking storage (public/storage -> storage/app/public)..."
php artisan storage:link 2>/dev/null || true

echo "Starting web server..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}

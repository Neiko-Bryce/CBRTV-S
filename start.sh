#!/bin/bash

# Run database migrations
php artisan migrate --force

# Start the web server
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}

#!/bin/sh
set -e

echo "Running Docker entrypoint..."

# Wait for services
echo "Waiting for PostgreSQL..."
until psql -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_DATABASE" -c '\q' 2>/dev/null; do
    echo "PostgreSQL is unavailable - sleeping"
    sleep 2
done

echo "PostgreSQL is up - continuing..."

# Run migrations
php artisan migrate --force

# Create storage link
php artisan storage:link

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chown -R www-data:www-data /app/storage /app/bootstrap/cache

echo "Docker entrypoint complete!"

# Start PHP-FPM
exec php-fpm

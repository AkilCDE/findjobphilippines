#!/bin/bash
set -e

# Configure port for Apache based on Render's $PORT (Render provides $PORT, defaults to 10000 or 80)
PORT="${PORT:-10000}"
echo "Configuring Apache to listen on port ${PORT}..."
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf

# Ensure storage and bootstrap directories exist
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

# If SQLite is used and file does not exist, initialize it
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ] && [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
fi

# Set proper ownership and permissions for Apache www-data
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate APP_KEY if missing
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is not set. Generating key..."
    php artisan key:generate --force || true
fi

# Run database migrations and seeders
echo "Running migrations..."
php artisan migrate --force || echo "Warning: Migrations failed or database not reachable yet. Proceeding..."
php artisan db:seed --force || echo "Seeding completed or skipped."

# Optimize Laravel cache for production
echo "Caching configurations, routes, and views..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Starting Apache web server on port ${PORT}..."
exec apache2-foreground

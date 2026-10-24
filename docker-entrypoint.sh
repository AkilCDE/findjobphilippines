#!/bin/bash
set -e

# 1. Configure port for Apache based on Render's $PORT (defaults to 10000 or 80)
PORT="${PORT:-10000}"
echo "Configuring Apache to listen on port ${PORT}..."
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf

# 2. Ensure storage, bootstrap cache, and database directories exist
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache \
         /var/www/html/database

touch /var/www/html/storage/logs/laravel.log

# 3. Initialize .env file if missing
if [ ! -f /var/www/html/.env ]; then
    if [ -f /var/www/html/.env.example ]; then
        echo "Creating .env from .env.example..."
        cp /var/www/html/.env.example /var/www/html/.env
    else
        touch /var/www/html/.env
    fi
fi

# 4. Generate APP_KEY if missing
if [ -z "$APP_KEY" ]; then
    if ! grep -q "^APP_KEY=base64:" /var/www/html/.env 2>/dev/null; then
        echo "APP_KEY is not set. Generating key..."
        php artisan key:generate --force || true
    fi
fi

# 5. Database detection & automatic SQLite fallback
# In Render's single-container deployment, localhost/127.0.0.1 MySQL does not exist.
# If DB_CONNECTION is mysql or mariadb and the host is unreachable, automatically fall back to SQLite.
DB_CONN="${DB_CONNECTION:-sqlite}"
DB_H="${DB_HOST:-127.0.0.1}"
DB_P="${DB_PORT:-3306}"

USE_SQLITE=false

if [ "$DB_CONN" = "sqlite" ]; then
    USE_SQLITE=true
elif [ "$DB_CONN" = "mysql" ] || [ "$DB_CONN" = "mariadb" ]; then
    echo "Testing MySQL connectivity to ${DB_H}:${DB_P}..."
    if ! php -r '
        $host = getenv("DB_HOST") ?: "127.0.0.1";
        $port = (int)(getenv("DB_PORT") ?: 3306);
        $fp = @fsockopen($host, $port, $errno, $errstr, 2);
        if ($fp) { fclose($fp); exit(0); }
        exit(1);
    '; then
        echo "=========================================================================="
        echo "Notice: MySQL server at ${DB_H}:${DB_P} is unreachable in this container."
        echo "Automatically falling back to SQLite (/var/www/html/database/database.sqlite)."
        echo "To use an external database, provide cloud DB credentials in Render's dashboard."
        echo "=========================================================================="
        USE_SQLITE=true
    else
        echo "Successfully connected to database server at ${DB_H}:${DB_P}."
    fi
fi

if [ "$USE_SQLITE" = true ]; then
    export DB_CONNECTION=sqlite
    export DB_DATABASE=/var/www/html/database/database.sqlite
    touch /var/www/html/database/database.sqlite
    sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=sqlite|g" /var/www/html/.env 2>/dev/null || true
    sed -i "s|^DB_HOST=.*|# DB_HOST=127.0.0.1|g" /var/www/html/.env 2>/dev/null || true
    sed -i "s|^DB_DATABASE=.*|DB_DATABASE=/var/www/html/database/database.sqlite|g" /var/www/html/.env 2>/dev/null || true
fi

# 6. Logging configuration: ensure stack streams to stderr for Render live log console
export LOG_CHANNEL="${LOG_CHANNEL:-stack}"
export LOG_STACK="${LOG_STACK:-single,stderr}"

# 7. Ensure HTTPS and production environment for Render
if [ -n "$RENDER_EXTERNAL_URL" ]; then
    export APP_URL="${RENDER_EXTERNAL_URL}"
    export ASSET_URL="${RENDER_EXTERNAL_URL}"
    sed -i "s|^APP_URL=.*|APP_URL=${RENDER_EXTERNAL_URL}|g" /var/www/html/.env 2>/dev/null || true
elif [ -z "$APP_URL" ] || [ "$APP_URL" = "http://localhost:8000" ] || [ "$APP_URL" = "http://localhost" ]; then
    export APP_URL="https://findjobph.onrender.com"
    export ASSET_URL="https://findjobph.onrender.com"
    sed -i "s|^APP_URL=.*|APP_URL=https://findjobph.onrender.com|g" /var/www/html/.env 2>/dev/null || true
fi
export APP_ENV="${APP_ENV:-production}"

# 8. Initial permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# 8. Run database migrations and seeders
echo "Running migrations..."
php artisan migrate --force || echo "Warning: Migrations failed or database already migrated. Proceeding..."
php artisan db:seed --force || echo "Seeding completed or skipped."

# 9. Clear and cache configurations, routes, and views
echo "Optimizing application cache..."
php artisan config:clear || true
php artisan config:cache || true
php artisan route:clear || true
php artisan route:cache || true
php artisan view:clear || true
php artisan view:cache || true

# 10. CRITICAL: Final permission fix AFTER all artisan commands run as root
# This guarantees Apache's www-data has full write access to logs, caches, sessions, and SQLite
echo "Finalizing permissions for Apache www-data..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 777 /var/www/html/storage/logs /var/www/html/storage/framework
chmod 666 /var/www/html/storage/logs/laravel.log 2>/dev/null || true
if [ -f /var/www/html/database/database.sqlite ]; then
    chmod 666 /var/www/html/database/database.sqlite
fi

echo "Starting Apache web server on port ${PORT}..."
exec apache2-foreground

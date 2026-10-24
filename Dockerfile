# ==============================================================================
# Stage 1: Build Frontend Assets (Vite + Tailwind CSS)
# ==============================================================================
FROM node:20-alpine AS frontend

WORKDIR /app

# Copy package descriptors
COPY package*.json ./

# Install npm dependencies
RUN npm ci --ignore-scripts || npm install --ignore-scripts

# Copy configuration and frontend source files
COPY vite.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

# Compile production assets
RUN npm run build

# ==============================================================================
# Stage 2: Production PHP 8.3 with Apache
# ==============================================================================
FROM php:8.3-apache

# Install system dependencies and build libraries
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure Apache DocumentRoot to point to Laravel's public/ folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Enable Apache mod_rewrite & allow .htaccess overrides
RUN a2enmod rewrite headers \
    && printf '<Directory /var/www/html/public>\n    Options -Indexes +FollowSymLinks\n    AllowOverride All\n    Require all granted\n</Directory>\n' >> /etc/apache2/apache2.conf

# Configure production PHP settings
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

RUN { \
        echo 'upload_max_filesize=20M'; \
        echo 'post_max_size=25M'; \
        echo 'memory_limit=256M'; \
    } > /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html

# Copy composer files first for optimal Docker layer caching
COPY composer.json composer.lock ./

# Install PHP dependencies without dev packages and without running scripts yet
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copy application source code
COPY . .

# Copy compiled frontend assets from Stage 1
COPY --from=frontend /app/public/build ./public/build

# Complete composer autoload generation
RUN composer dump-autoload --optimize

# Copy entrypoint script and ensure execution permissions
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set directory permissions for Laravel storage, bootstrap cache & database
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs bootstrap/cache database \
    && touch storage/logs/laravel.log \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Expose Render default port (Render typically expects 10000 or custom $PORT)
EXPOSE 80 10000

ENTRYPOINT ["docker-entrypoint.sh"]

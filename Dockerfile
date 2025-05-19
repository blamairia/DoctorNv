# Dockerfile

# 1. Base image with PHP + Debian (has libssl)
FROM php:8.2-fpm-bullseye

# 2. Install system deps & PHP extensions
RUN apt-get update \
 && apt-get install -y libpq-dev zlib1g-dev libzip-dev \
 && docker-php-ext-install pdo_pgsql zip

# 3. Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Set working directory
WORKDIR /var/www/html

# 5. Copy only composer files first (for caching)
COPY composer.json composer.lock ./

# 6. Install PHP deps
RUN composer install --no-dev --optimize-autoloader

# 7. Now copy the rest of the app
COPY . .

# 8. Generate app key (only if you haven’t committed APP_KEY)
RUN php artisan key:generate --ansi

# 9. Expose port and start the server
EXPOSE 8000
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]

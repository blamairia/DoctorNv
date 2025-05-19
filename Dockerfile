# Dockerfile

# 1. Base image with Debian and PHP 8.2 (includes libssl)
FROM php:8.2-fpm-bullseye

# 2. Install system deps & PHP extensions
RUN apt-get update \
 && apt-get install -y libpq-dev zlib1g-dev libzip-dev \
 && docker-php-ext-install pdo_pgsql zip

# 3. Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Set working directory
WORKDIR /var/www/html

# 5. Copy code & install dependencies
COPY . .
RUN composer install --no-dev --optimize-autoloader \
 && php artisan key:generate --ansi

# 6. Expose the port Laravel’s server will listen on
EXPOSE 8000

# 7. Start Laravel’s built-in server on 0.0.0.0:8000
#    Use sh -c so $PORT can be injected by your host (e.g. Render sets $PORT)
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]

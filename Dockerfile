# Dockerfile

# 1. Base image (Debian Bullseye, PHP 8.2 + correct OpenSSL)
FROM php:8.2-fpm-bullseye

# 2. Install system deps & PHP extensions (including intl)
RUN apt-get update \
 && apt-get install -y \
      libpq-dev \
      zlib1g-dev \
      libzip-dev \
      libicu-dev \
 && docker-php-ext-install \
      pdo_pgsql \
      zip \
      intl

# 3. Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Set working directory
WORKDIR /var/www/html

# 5. Copy composer files first (cache layer)
COPY composer.json composer.lock ./

# 6. Install PHP deps
RUN composer install --no-dev --optimize-autoloader

# 7. Copy the rest of your app
COPY . .

# 8. Generate app key if needed
RUN php artisan key:generate --ansi

# 9. Expose the port and start Laravel's server
EXPOSE 8000
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]

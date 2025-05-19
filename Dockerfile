# Use Debian-based PHP with the correct OpenSSL
FROM php:8.2-fpm-bullseye

# Install system dependencies and PHP extensions
RUN apt-get update \
 && apt-get install -y libpq-dev zlib1g-dev libzip-dev \
 && docker-php-ext-install pdo_pgsql zip

# Install Composer (from the official Composer image)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Install PHP dependencies and optimize
RUN composer install --no-dev --optimize-autoloader \
 && php artisan key:generate --ansi \
 && php artisan migrate --force

# Expose the port PHP-FPM will listen on
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]

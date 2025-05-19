# ── stage 1: build the vendor dir ──────────────────────────────────────
FROM composer:2 AS vendor

WORKDIR /app

# only copy composer files first so we cache this layer
COPY composer.json composer.lock ./

# install & optimize autoloader (no dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# ── stage 2: runtime image ────────────────────────────────────────────
FROM php:8.2-fpm-bullseye

# install Linux deps + compile PHP extensions
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      libpq-dev \
      zlib1g-dev \
      libzip-dev \
      libicu-dev \
      git \
      unzip \
 && docker-php-ext-install \
      pdo_pgsql \
      zip \
      intl \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# copy in the baked vendor/ (with PSR-4 maps)
COPY --from=vendor /app/vendor ./vendor

# copy the rest of your application
COPY . .

# clear & cache everything so Laravel (and Filament) see your new widget
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache \
 && php artisan key:generate --ansi \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache \
 && php artisan optimize

# expose the FPM port Render expects
EXPOSE 9000

# run FPM (Render will auto‐detect and forward HTTP traffic)
CMD ["php-fpm"]

# ── stage 0: build your vendor dir with PHP 8.2 CLI ────────────────────────────
FROM php:8.2-cli AS vendor

# install system deps & intl (just like your fpm stage)
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      libicu-dev \
      libzip-dev \
      unzip \
 && docker-php-ext-install intl zip \
 && rm -rf /var/lib/apt/lists/*

# install composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
      --no-dev \
      --optimize-autoloader \
      --no-interaction \
      --no-progress

# ── stage 1: your normal PHP-FPM runtime ──────────────────────────────────────
FROM php:8.2-fpm-bullseye

# same dependencies as before...
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      libicu-dev \
      libzip-dev \
      unzip \
      libpq-dev \
 && docker-php-ext-install intl zip pdo_pgsql \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY --from=vendor /app/vendor ./vendor
COPY . .

# …rest of your fpm configuration…

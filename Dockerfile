# Use the official PHP-FPM image (here Alpine for slimmer footprint)
FROM php:8.2-fpm-alpine

# Set working dir
WORKDIR /app

# Copy in *everything*, including your local vendor/ and artisan file
COPY . .

# Expose FPM port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]

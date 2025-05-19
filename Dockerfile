# Use the official PHP-FPM image (here Alpine for slimmer footprint)
FROM php:8.2-fpm-alpine

# Set working dir
WORKDIR /app

# Copy in *everything*, including your local vendor/ and artisan file
COPY . .

# Expose FPM port
# 9. Expose the port and start Laravel's server
EXPOSE 8000
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]
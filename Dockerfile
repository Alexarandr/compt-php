# Build stage - composer dependencies
FROM composer:latest AS composer-builder

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --ignore-platform-reqs

# Production stage
FROM php:8.2.8-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy composer dependencies from builder
COPY --from=composer-builder /app/vendor ./vendor

# Create necessary directories with proper permissions
RUN mkdir -p bootstrap/cache storage/framework/{sessions,views,cache} storage/logs \
    && chown -R www-data:www-data . \
    && chmod -R 755 bootstrap/cache storage \
    && chmod -R 644 bootstrap/cache/* storage/**/*

# Configure Apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/api/counter/count || exit 1

EXPOSE 80
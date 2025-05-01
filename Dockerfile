# Build stage
FROM composer:2.6 AS builder

WORKDIR /app
COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-autoloader --no-scripts --ignore-platform-reqs

COPY . .
RUN composer dump-autoload --optimize --no-dev

# Runtime stage
FROM php:8.2-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    icu-dev \
    libzip-dev \
    && docker-php-ext-install -j$(nproc) \
    intl \
    pdo_mysql \
    zip

WORKDIR /var/www/html
COPY --from=builder /app .
COPY --from=builder /app/public public/

# Permissions
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var \
    && chmod -R 777 var/cache var/log

# Entrypoint
COPY docker/railway/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]

CMD ["php", "-S", "0.0.0.0:8000", "public/index.php"]
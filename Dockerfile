# Build stage
FROM composer:2.6 AS builder

WORKDIR /app

# Git saugumo nustatymas
RUN git config --global --add safe.directory /app

# Nukopijuojame visą projektą
COPY . .

# Diegiame priklausomybes pagal aplinką
ARG APP_ENV=prod
RUN if [ "$APP_ENV" = "dev" ]; then \
        composer install --no-interaction --prefer-dist; \
    else \
        composer install --no-dev --no-interaction --prefer-dist; \
    fi

# Autoloader
RUN composer dump-autoload --optimize

# Runtime stage
FROM php:8.2-fpm-alpine

COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache \
    icu-dev \
    libzip-dev \
    git \
    && docker-php-ext-install -j$(nproc) intl pdo_mysql zip

WORKDIR /var/www/html

COPY --from=builder /app .

RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var \
    && chmod -R 777 var/cache var/log

COPY docker/railway/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

ENV PORT=8000
ENTRYPOINT ["entrypoint.sh"]
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public/"]

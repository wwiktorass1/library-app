FROM php:8.2-fpm

RUN apt-get update && \
    apt-get install -y git unzip libicu-dev zlib1g-dev libzip-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

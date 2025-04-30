FROM php:8.2-cli


RUN apt-get update && \
    apt-get install -y git unzip libicu-dev zlib1g-dev libzip-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html


COPY . .


RUN mkdir -p var/cache var/log && \
    chmod -R 777 var/cache var/log && \
    chmod +x bin/console


RUN composer install --no-dev --optimize-autoloader && \
    php bin/console cache:clear --no-warmup


CMD ["sh", "-c", "php -S 0.0.0.0:$PORT public/index.php"]
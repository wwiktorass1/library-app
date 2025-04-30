FROM php:8.2-cli


RUN apt-get update && \
    apt-get install -y git unzip libicu-dev zlib1g-dev libzip-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html


COPY composer.json composer.lock symfony.lock ./
COPY config config/
COPY public public/
COPY src src/
COPY migrations migrations/
COPY templates templates/


RUN mkdir -p var/cache var/log \
    && chmod -R 777 var/cache var/log


RUN composer install --no-dev --optimize-autoloader


COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
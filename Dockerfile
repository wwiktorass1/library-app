FROM php:8.2-cli


RUN apt-get update && \
    apt-get install -y git unzip libicu-dev zlib1g-dev libzip-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html


COPY composer.json composer.lock symfony.lock ./


RUN composer require symfony/flex --no-plugins --no-scripts


RUN composer install --no-dev --no-scripts --no-autoloader


COPY . .


RUN chmod +x bin/console && \
    mkdir -p var/cache var/log && \
    chmod -R 777 var/cache var/log

RUN git config --global --add safe.directory /var/www/html

RUN composer require symfony/framework-bundle doctrine/annotations


RUN composer dump-autoload --optimize --no-dev && \
    php bin/console cache:clear --no-warmup

RUN composer install --no-dev --optimize-autoloader \
    && php bin/console cache:clear \
    && php bin/console doctrine:migrations:migrate --no-interaction


    CMD ["sh", "-c", "php -S 0.0.0.0:$PORT public/index.php"]
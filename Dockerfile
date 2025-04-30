FROM php:8.2-cli

RUN apt-get update && \
    apt-get install -y git unzip libicu-dev zlib1g-dev libzip-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html


COPY . .


RUN echo "APP_ENV=prod" > .env \
    && echo "APP_SECRET=SomeSecretKey123456789" >> .env \
    && echo "DATABASE_URL=mysql://root:SLitaQCpaBtBOCUsWcrQqXsxoFofExbp@mysql.railway.internal:3306/railway?serverVersion=8.0&charset=utf8mb4" >> .env


RUN mkdir -p var/cache var/log \
    && chmod -R 777 var/cache var/log


RUN composer install --no-dev --optimize-autoloader \
    && php bin/console cache:clear \
    && php bin/console doctrine:migrations:migrate -n


CMD ["php", "-S", "0.0.0.0:${PORT}", "-t", "public"]
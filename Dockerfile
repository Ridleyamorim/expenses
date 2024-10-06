FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_sqlite zip


COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY . .

RUN composer install

CMD php artisan serve --host=0.0.0.0 --port=85
FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev libonig-dev libicu-dev libjpeg-dev libpng-dev libwebp-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install pdo pdo_mysql zip intl gd \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html


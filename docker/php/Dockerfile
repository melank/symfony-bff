FROM php:8.5.1-fpm-alpine

RUN apk add --no-cache \
    bash \
    git \
    zip \
    unzip \
    icu-dev \
    linux-headers \
    $PHPIZE_DEPS

RUN docker-php-ext-install intl

RUN pecl install apcu && docker-php-ext-enable apcu

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN addgroup -g 1000 symfony && adduser -u 1000 -G symfony -s /bin/bash -D symfony
RUN chown -R symfony:symfony /var/www/html

USER symfony

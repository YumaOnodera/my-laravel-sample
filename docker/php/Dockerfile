FROM php:8.1.17-fpm-alpine3.17

# PHP
COPY php.ini /usr/local/etc/php/

RUN apk update \
    && apk add autoconf gcc g++ linux-headers make \
    && docker-php-ext-install pdo_mysql opcache \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && adduser -u 1000 sail -D

# Composer install
COPY --from=composer /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin

WORKDIR /var/www/html

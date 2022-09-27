FROM php:8.1-fpm-alpine

WORKDIR /var/www/html

COPY --from=composer /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin

RUN docker-php-ext-install pdo_mysql opcache
RUN apk update \
    && apk add autoconf gcc g++ make
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
RUN adduser -u 1000 sail -D

COPY php.ini /usr/local/etc/php/

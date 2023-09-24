#!/bin/sh

php artisan config:cache

chmod -R 777 ./storage ./bootstrap/cache

php-fpm -D

nginx -g "daemon off;"

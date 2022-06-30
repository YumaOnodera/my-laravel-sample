#!/usr/bin/env bash

mysql --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS my_laravel_sample;
    GRANT ALL PRIVILEGES ON \`my_laravel_sample%\`.* TO '$MYSQL_USER'@'%';
    CREATE DATABASE IF NOT EXISTS my_laravel_sample_testing;
    GRANT ALL PRIVILEGES ON \`my_laravel_sample_testing%\`.* TO '$MYSQL_USER'@'%';
EOSQL

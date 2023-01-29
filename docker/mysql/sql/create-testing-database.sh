#!/usr/bin/env bash

mysql --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS larastep;
    GRANT ALL PRIVILEGES ON \`larastep%\`.* TO '$MYSQL_USER'@'%';
    CREATE DATABASE IF NOT EXISTS larastep_testing;
    GRANT ALL PRIVILEGES ON \`larastep_testing%\`.* TO '$MYSQL_USER'@'%';
EOSQL

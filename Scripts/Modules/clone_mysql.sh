#!/bin/bash
DB_HOST=$1
DB_USER=$2
DB_PASS=$3
SOURCE_DB=$4
TARGET_DB=$5

mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS \`${TARGET_DB}\`;"
mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$SOURCE_DB" > dump.sql
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$TARGET_DB" < dump.sql

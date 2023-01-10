#!/bin/sh
set -e

chmod -R 775 /var/www/html/storage/logs;
chmod -R 775 /var/www/html/storage/framework/views;
chmod -R 775 /var/www/html/storage/framework/cache;
chmod -R 775 /var/www/html/storage/app;

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

exec "$@"
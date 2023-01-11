#!/bin/sh
set -e

chmod -R 775 /var/www/html/var/log;
chmod -R 775 /var/www/html/var/cache;

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

exec "$@"
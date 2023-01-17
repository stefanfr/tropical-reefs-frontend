#!/bin/sh
set -e

php bin/console doctrine:schema:update --force --complete;
php bin/console doctrine:schema:update --force --em=manager --complete;

chown -R www-data:www-data *;
composer dump-autoload --optimize --classmap-authoritative;

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@";
fi

sleep infinity;
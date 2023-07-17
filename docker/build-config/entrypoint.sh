#!/bin/sh
set -e

composer dump-autoload --optimize --classmap-authoritative;

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@";
fi

sleep infinity;
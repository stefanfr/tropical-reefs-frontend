FROM gitlab.hostplus.nl:5050/tropical-reefs/docker/php8.1:latest
COPY ./docker/php/zzz-tropicalreefs-fpm.ini /usr/local/etc/php-fpm.d/
COPY . /var/www/html
COPY ./docker/php/docker-frontend-entrypoint.sh /usr/local/bin/docker-frontend-entrypoint.sh
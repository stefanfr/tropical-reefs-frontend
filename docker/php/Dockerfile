FROM gitlab.hostplus.nl:5050/tropical-reefs/docker/php8.1:latest
COPY ./zzz-tropicalreefs-fpm.ini /usr/local/etc/php-fpm.d/
COPY . /var/www/html
COPY ./docker-frontend-entrypoint.sh /usr/local/bin/docker-frontend-entrypoint.sh
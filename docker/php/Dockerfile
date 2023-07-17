FROM gitlab.hostplus.nl:5050/tropical-reefs/docker/php8.1:latest as build

# Install npm
RUN curl -fsSL https://deb.nodesource.com/setup_19.x | bash -
RUN apt-get update && apt-get install -y nodejs

# entry point
COPY ./docker/build-config/entrypoint.sh /
RUN chmod +x /entrypoint.sh

WORKDIR $CI_PROJECT_DIR/var/www/html

# Copy files
COPY . .

# Run composer install & setup Symfony
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && /bin/cp ./docker/build-config/.env ./.env \
    && make deploy \
    && rm -f /usr/local/bin/composer \
    /bin/rm ./.env

RUN chown -R www-data:www-data /var/www/html

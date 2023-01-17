FROM gitlab.hostplus.nl:5050/tropical-reefs/docker/php8.1:latest as build

# Install npm
RUN curl -fsSL https://deb.nodesource.com/setup_19.x | bash -
RUN apt-get update && apt-get install -y nodejs

# entry point
COPY ./docker/build-config/entrypoint.sh /
RUN chmod +x /entrypoint.sh

# Copy files
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Run composer install & setup Symfony
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && /bin/cp /var/www/html/docker/build-config/.env /var/www/html/.env \
    && make deploy \
    && rm -f /usr/local/bin/composer \
    /bin/rm /var/www/html/.env

RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000

# Stage 2: Deploy
FROM nginx:1.23

# Copy files from build stage
COPY --from=build /var/www/html /var/www/html

# Copy nginx configuration
COPY ./docker/build-config/nginx.conf /etc/nginx/nginx.conf

# Mount php-socket
VOLUME ["/var/run/php"]

# Start nginx
CMD ["nginx", "-g", "daemon off;"]
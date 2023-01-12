FROM gitlab.hostplus.nl:5050/tropical-reefs/docker/php8.1:latest

# copy code
COPY . /var/www/html

WORKDIR /var/www/html

#install symfony and its dependencies
RUN composer install --optimize-autoloader --no-dev

# Install npm dependencies and build assets
RUN npm ci
RUN npm run build

# change ownership
RUN chown -R www-data:www-data /var/www/html
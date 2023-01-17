FROM gitlab.hostplus.nl:5050/tropical-reefs/docker/php8.1:latest

# copy code
COPY . /var/www/html

# entry point
COPY ./docker/config/entrypoint.sh /
RUN chmod +x /entrypoint.sh

# change ownership
RUN chown -R www-data:www-data /var/www/html

RUN make deploy
FROM nginx:1.23
FROM gitlab.hostplus.nl:5050/tropical-reefs/frontend/frontend-php:latest as php

COPY --from=php /var/www/html/ /var/www/html/

# Copy nginx configuration
COPY ./docker/build-config/nginx.conf /etc/nginx/nginx.conf

# Start nginx
CMD ["nginx", "-g", "daemon off;"]
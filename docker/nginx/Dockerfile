FROM gitlab.hostplus.nl:5050/tropical-reefs/frontend/frontend-php:latest AS build
COPY --from=build /var/www/html /var/www/html/

FROM nginx:1.23
# Copy nginx configuration
COPY ./docker/build-config/nginx.conf /etc/nginx/nginx.conf

# Start nginx
CMD ["nginx", "-g", "daemon off;"]
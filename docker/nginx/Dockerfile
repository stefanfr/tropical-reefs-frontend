FROM nginx:1.23

# Copy files from build stage
COPY . /var/www/html

# Copy nginx configuration
COPY ./docker/build-config/nginx.conf /etc/nginx/nginx.conf

# Start nginx
CMD ["nginx", "-g", "daemon off;"]
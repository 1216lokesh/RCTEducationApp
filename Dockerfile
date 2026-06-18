FROM php:8.2-apache

# Install mysqli extension for database connection
RUN docker-php-ext-install mysqli

# Enable Apache mod_rewrite for routing/rewrites
RUN a2enmod rewrite

# Copy all files to the Apache document root
COPY . /var/www/html/

# Adjust permissions for web server
RUN chown -R www-data:www-data /var/www/html

# Expose standard web server port
EXPOSE 80

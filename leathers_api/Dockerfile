FROM php:7.4-apache

# Install MySQLi
RUN docker-php-ext-install mysqli

# Enable Apache modules (optional)
RUN a2enmod rewrite

# Copy app code into the container
COPY / /var/www/html/

# Expose port
EXPOSE 80

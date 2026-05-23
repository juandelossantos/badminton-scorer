FROM php:8.1-apache

# Enable Apache modules
RUN a2enmod rewrite headers

# Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# Install curl for healthcheck
RUN apt-get update && apt-get install -y curl

# Set working directory
WORKDIR /var/www/html

# Copy Apache config
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

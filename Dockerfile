# PHP image with Apache server included
FROM php:8.4-apache

# Enable Apache url rewrite module (required to redirect all traffic to public/index.php)
RUN a2enmod rewrite

# Install necessary PHP extensions for database connection
RUN docker-php-ext-install pdo pdo_mysql



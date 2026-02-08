FROM php:8.2-apache

RUN docker-php-ext-install mysqli

# Enable apache rewrite (optional)
RUN a2enmod rewrite

WORKDIR /var/www/html
COPY app/ /var/www/html/

EXPOSE 80

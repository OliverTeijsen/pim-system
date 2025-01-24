FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
   git \
   unzip \
   libzip-dev \
   libicu-dev \
   openssl \
   && docker-php-ext-install \
   pdo_mysql \
   zip \
   intl \
   && docker-php-ext-configure intl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
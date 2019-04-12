FROM php:7.2-apache

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

RUN apt-get update && apt-get install -y \
    git zlib1g-dev libpng-dev \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install opcache \
    && pecl install xdebug && docker-php-ext-enable xdebug \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

WORKDIR /var/www/app
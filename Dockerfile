FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpq-dev \
    ca-certificates \
    && docker-php-ext-install pdo pdo_pgsql \
    && update-ca-certificates

COPY . /var/www/html/

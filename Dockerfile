FROM php:8.2-apache

# Instala dependências para PostgreSQL + SSL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    openssl \
    ca-certificates \
    && update-ca-certificates \
    && docker-php-ext-install pdo_pgsql pgsql

RUN a2enmod rewrite

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

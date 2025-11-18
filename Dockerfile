FROM php:8.2-apache

# Instala dependências do PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copia os arquivos do projeto
COPY . /var/www/html/

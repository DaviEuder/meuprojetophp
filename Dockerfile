FROM php:8.2-apache

# Instala dependências necessárias para PostgreSQL + SSL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libssl-dev \
    openssl \
    ca-certificates \
    && update-ca-certificates --fresh

# Instala extensões PHP para PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql pgsql

# Ativa o módulo rewrite do Apache
RUN a2enmod rewrite

# Copia os arquivos do projeto
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

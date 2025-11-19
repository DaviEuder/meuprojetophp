FROM php:8.2-apache

# 1. Instala bibliotecas de desenvolvimento do PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Instala as extensões PHP (agora que as libs estão presentes)
RUN docker-php-ext-install pdo pdo_pgsql

# 3. Copia o código da sua aplicação para o diretório web do Apache
COPY . /var/www/html/

# Dockerfile com PDO PgSQL

# Usa uma imagem oficial do PHP com Apache e PHP-FPM
FROM php:8.2-fpm-alpine

# Instala as dependências necessárias para o PostgreSQL (pdo_pgsql)
RUN apk add --no-cache \
    postgresql-dev \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/cache/apk/*

# Configura o diretório de trabalho correto
WORKDIR /app

# Copia todos os arquivos do projeto para o container
COPY . /app

# Define a porta que o PHP Development Server irá escutar
EXPOSE 10000

# Define o comando para iniciar o PHP Development Server
CMD ["php", "-S", "0.0.0.0:10000", "-t", "/app"]

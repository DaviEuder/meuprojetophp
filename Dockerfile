FROM php:8.2-cli

# Dependências para Postgres e SSL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    openssl \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /app
COPY . .

# Servidor embutido na porta usada pela Render
CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]

FROM php:8.2-cli

# Instalar dependências necessárias para SSL e PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    openssl \
    ca-certificates \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /app

COPY . .

# Render expõe a porta 10000
CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]

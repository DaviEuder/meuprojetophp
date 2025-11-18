FROM php:8.2-cli

# Instalar dependências necessárias para SSL e Postgres
RUN apt-get update && apt-get install -y \
    libpq-dev \
    openssl \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /app
COPY . .

CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]

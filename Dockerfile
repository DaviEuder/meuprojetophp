FROM php:8.2.29-cli

ARG CACHE_BUSTER=1

RUN apt-get update && apt-get install -y \
    libpq-dev \
    openssl \
    ca-certificates \
    && update-ca-certificates \
    && docker-php-ext-configure pdo_pgsql \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /app

COPY . /app

CMD ["php", "-S", "0.0.0.0:10000", "-t", "/app"]

EXPOSE 10000

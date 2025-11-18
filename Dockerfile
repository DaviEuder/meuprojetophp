FROM php:8.2.29-cli

ARG CACHE_BUSTER=1

# 1. Instala dependências e compila as extensões (agora com v6 para forçar o cache)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    openssl \
    ca-certificates \
    && update-ca-certificates \
    && echo "forcar_rebuild_v6" \
    && docker-php-ext-configure pdo_pgsql \
    && docker-php-ext-install pdo pdo_pgsql

# 2. PASSO CRÍTICO: Força o PHP a carregar o driver PDO para PostgreSQL.
# Esta linha cria o arquivo .ini que ativa a extensão.
RUN echo "extension=pdo_pgsql.so" > /usr/local/etc/php/conf.d/pdo_pgsql.ini

WORKDIR /app

COPY . /app

CMD ["php", "-S", "0.0.0.0:10000", "-t", "/app"]

EXPOSE 10000

FROM php:8.2.29-cli

ARG CACHE_BUSTER=1

# ⚠️ FORÇANDO A QUEBRA DO CACHE
# Adicionamos um 'echo' com um valor diferente ("v2") para forçar o Render
# a rodar este comando de novo, em vez de usar a versao em CACHE.
RUN apt-get update && apt-get install -y \
    libpq-dev \
    openssl \
    ca-certificates \
    && update-ca-certificates \
    && echo "forcar_rebuild_v2" \
    && docker-php-ext-configure pdo_pgsql \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /app

COPY . /app

CMD ["php", "-S", "0.0.0.0:10000", "-t", "/app"]

EXPOSE 10000

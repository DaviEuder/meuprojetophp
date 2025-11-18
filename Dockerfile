# =============================
# Dockerfile otimizado para Render + PHP + PostgreSQL
# Inclui prevenção de cache, SSL, pdo_pgsql e dev server
# =============================

# Mudança simples para INVALIDAR CACHE
FROM php:8.2-cli AS base

# Instalar dependências essenciais
RUN apt-get update && apt-get install -y \
    libpq-dev \
    openssl \
    ca-certificates \
    && docker-php-ext-configure pdo_pgsql \
    && docker-php-ext-install pdo pdo_pgsql

# Reforçar atualização de certificados SSL
RUN update-ca-certificates

# Diretório da aplicação
WORKDIR /app

# Copia tudo
COPY . .

# Expor a porta do servidor PHP
EXPOSE 10000

# Comando padrão para rodar no Render
CMD ["php", "-d", "variables_order=EGPCS", "-S", "0.0.0.0:10000", "-t", "."]

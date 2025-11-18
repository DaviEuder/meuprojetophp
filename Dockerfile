# Força rebuild e evita cache da camada base
FROM php:8.2.29-cli

# Variável para quebrar cache quando precisar
ARG CACHE_BUSTER=1

# Instala dependências necessárias para PostgreSQL e SSL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    openssl \
    ca-certificates \
    && update-ca-certificates \
    && docker-php-ext-configure pdo_pgsql \
    && docker-php-ext-install pdo pdo_pgsql

# Garante que o container trabalha com diretório correto
WORKDIR /app

# Copia todos os arquivos do projeto
COPY . .

# Render usa porta 10000
EXPOSE 10000

# Executa o PHP embutido como servidor
CMD ["php", "-d", "variables_order=EGPCS", "-S", "0.0.0.0:10000", "-t", "."]

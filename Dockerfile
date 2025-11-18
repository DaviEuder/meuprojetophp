# Usa a imagem base CLI (Command Line Interface)
FROM php:8.2.29-cli

# Variável para quebrar cache
ARG CACHE_BUSTER=1

# Instala dependências necessárias para PostgreSQL e SSL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    openssl \
    ca-certificates \
    && update-ca-certificates \
    \
    # Configuração robusta para extensões PGSQL
    && docker-php-ext-configure pdo_pgsql \
    \
    # Instala extensões PHP necessárias após instalar libpq-dev
    && docker-php-ext-install pdo pdo_pgsql

# Configura diretório de trabalho
WORKDIR /app

# Copia arquivos do projeto
COPY . /app

# Define o comando para iniciar o servidor embutido do PHP
CMD ["php", "-S", "0.0.0.0:10000", "-t", "/app"]

# Porta usada pelo Render
EXPOSE 10000

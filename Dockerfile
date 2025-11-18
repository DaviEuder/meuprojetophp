# Usa a imagem base CLI (Command Line Interface), que é mais simples
# e funciona melhor com o Development Server no plano gratuito.
FROM php:8.2.29-cli

# Variavel para quebrar o cache, se necessario
ARG CACHE_BUSTER=1

# Instala as dependencias necessarias para PostgreSQL (libpq-dev) e SSL
# O comando "docker-php-ext-install" sera usado logo apos a instalacao
RUN apt-get update && apt-get install -y \
    libpq-dev \
    openssl \
    ca-certificates \
    && update-ca-certificates

# ⚠️ AQUI ESTA A CORREÇÃO:
# Garante que as extensoes PDO e pdo_pgsql sejam instaladas e ativadas
# para que o PHP Server possa encontra-las.
RUN docker-php-ext-install pdo pdo_pgsql

# Configura o diretorio de trabalho
WORKDIR /app

# Copia todos os arquivos do projeto para o container
COPY . /app

# Define o comando para iniciar o PHP Development Server
# Este e o "carro-chefe" no plano gratuito, usando a porta 10000 do Render.
CMD ["php", "-S", "0.0.0.0:10000", "-t", "/app"] 

# Render usa a porta 10000
EXPOSE 10000

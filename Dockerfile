FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libssl-dev \
    openssl \
    ca-certificates \
    && update-ca-certificates --fresh

RUN docker-php-ext-install pgsql pdo pdo_pgsql
RUN a2enmod rewrite

# --- CORREÇÃO SSL AQUI ---
# Criar diretório ~/.postgresql e copiar o bundle de certificados
RUN mkdir -p /var/www/.postgresql && \
    cp /etc/ssl/certs/ca-certificates.crt /var/www/.postgresql/root.crt && \
    chmod 600 /var/www/.postgresql/root.crt && \
    chown -R www-data:www-data /var/www/.postgresql

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

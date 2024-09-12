FROM php:8.2-fpm

# Instalar extensões necessárias do PHP
RUN docker-php-ext-install pdo pdo_mysql

# Instalar dependências necessárias (opcional)
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install zip

# Instalar o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

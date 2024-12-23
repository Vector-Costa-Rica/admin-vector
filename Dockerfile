# admin-vector/Dockerfile
FROM php:8.2-fpm-alpine

# Instalar dependencias necesarias
RUN apk add --no-cache \
    php82 \
    php82-fpm \
    php82-pdo \
    php82-pdo_mysql \
    php82-mbstring \
    php82-openssl \
    php82-json \
    php82-tokenizer \
    php82-xml \
    php82-dom \
    php82-xmlwriter \
    php82-curl \
    php82-ctype \
    php82-session \
    php82-fileinfo \
    php82-zip \
    php82-phar \
    php82-iconv \
    nginx \
    curl \
    supervisor

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Crear enlaces simbólicos para PHP
RUN ln -s /usr/bin/php82 /usr/bin/php

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar composer.json y composer.lock primero para aprovechar la caché de Docker
COPY composer.json composer.lock ./

# Instalar dependencias de producción
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Copiar el resto de los archivos de la aplicación
COPY . .

# Copiar scripts
COPY docker/scripts/entrypoint.sh /usr/local/bin/
COPY docker/scripts/handle-migrations.sh /usr/local/bin/
COPY docker/scripts/optimize.sh /usr/local/bin/

# Dar permisos de ejecución a los scripts
RUN chmod +x /usr/local/bin/entrypoint.sh \
    /usr/local/bin/handle-migrations.sh \
    /usr/local/bin/optimize.sh

# Configurar permisos
RUN chown -R nobody:nobody /var/www/html \
    && chmod -R 755 /var/www/html/storage

ENTRYPOINT ["entrypoint.sh"]

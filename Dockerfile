# admin-vector/Dockerfile
FROM php:8.2-fpm-alpine

# Instalar dependencias necesarias
# Instalar dependencias necesarias
RUN apk add --no-cache \
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
    supervisor \
    $PHPIZE_DEPS

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar composer.json y composer.lock primero
COPY composer.json composer.lock ./

# Copiar el resto de la aplicación
COPY . .

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 storage bootstrap/cache

# Scripts de inicio y optimización
COPY docker/scripts/entrypoint.sh /usr/local/bin/
COPY docker/scripts/handle-migrations.sh /usr/local/bin/
COPY docker/scripts/optimize.sh /usr/local/bin/

# Dar permisos de ejecución a los scripts
RUN chmod +x /usr/local/bin/entrypoint.sh \
    /usr/local/bin/handle-migrations.sh \
    /usr/local/bin/optimize.sh

# Instalar dependencias y optimizar
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && php artisan config:clear \
    && php artisan cache:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && composer dump-autoload --optimize --no-dev

ENTRYPOINT ["entrypoint.sh"]

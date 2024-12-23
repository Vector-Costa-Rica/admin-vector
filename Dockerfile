# admin-vector/Dockerfile
FROM php:8.2-fpm-alpine

# Instalar dependencias del sistema
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    $PHPIZE_DEPS

# Instalar y configurar extensiones PHP
RUN docker-php-ext-install pdo pdo_mysql zip bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Crear directorios necesarios
RUN mkdir -p /var/log/nginx \
    /var/log/supervisor \
    /var/run/supervisor \
    storage/framework/views \
    storage/framework/cache \
    storage/framework/sessions \
    bootstrap/cache \
    && chown -R www-data:www-data /var/log/nginx \
    && chown -R www-data:www-data /var/log/supervisor \
    && chown -R www-data:www-data /var/run/supervisor

# Copiar archivos de configuración
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/scripts/entrypoint.sh /usr/local/bin/
COPY docker/scripts/handle-migrations.sh /usr/local/bin/
COPY docker/scripts/optimize.sh /usr/local/bin/

# Dar permisos de ejecución a los scripts
RUN chmod +x /usr/local/bin/entrypoint.sh \
    /usr/local/bin/handle-migrations.sh \
    /usr/local/bin/optimize.sh

# Copiar archivos esenciales de Laravel primero
COPY composer.json composer.lock artisan ./
COPY config ./config
COPY bootstrap ./bootstrap
COPY app ./app

# Instalar dependencias sin ejecutar scripts
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copiar el resto de la aplicación
COPY . .

# Ahora sí ejecutar los scripts de composer
RUN composer run-script post-autoload-dump

# Copiar .env.example a .env
COPY .env.example .env

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 storage bootstrap/cache

# Generar clave y optimizar
RUN php artisan key:generate --force \
    && php artisan storage:link \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && composer dump-autoload --optimize --no-dev

# Exponer puerto
EXPOSE 80

# Configurar comando de entrada
ENTRYPOINT ["entrypoint.sh"]

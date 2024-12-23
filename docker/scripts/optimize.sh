#!/bin/sh
set -e

echo "Iniciando optimizaciones para producción..."

# Asegurarse de que existan los directorios necesarios
mkdir -p storage/framework/views \
    storage/framework/cache \
    storage/framework/sessions \
    bootstrap/cache

# Asegurarse de que los permisos sean correctos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Verificar y generar APP_KEY si es necesario
if [ -z "$APP_KEY" ]; then
    echo "Generando nueva APP_KEY..."
    php artisan key:generate --force
fi

# Crear enlace simbólico del storage si no existe
if [ ! -d "public/storage" ]; then
    php artisan storage:link
fi

# Limpiar cachés existentes
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimizar la aplicación
echo "Optimizando la configuración..."
php artisan config:cache

echo "Optimizando rutas..."
php artisan route:cache

echo "Optimizando vistas..."
php artisan view:cache || echo "Advertencia: No se pudieron cachear las vistas, continuando..."

# Optimizar el cargador de clases
echo "Optimizando el cargador de clases..."
composer dump-autoload --optimize --no-dev

echo "Optimizaciones completadas exitosamente."

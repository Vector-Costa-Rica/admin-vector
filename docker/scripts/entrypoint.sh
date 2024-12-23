#!/bin/sh
set -e

# Crear .env si no existe
if [ ! -f ".env" ]; then
    echo "Creando archivo .env..."
    cp .env.example .env
fi

# Verificar y generar APP_KEY si es necesario
if [ -z "$APP_KEY" ]; then
    echo "Generando nueva APP_KEY..."
    php artisan key:generate --force
fi

# Crear directorios necesarios si no existen
mkdir -p storage/framework/views \
    storage/framework/cache \
    storage/framework/sessions \
    bootstrap/cache

# Establecer permisos
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Crear enlace simbólico del storage si no existe
if [ ! -d "public/storage" ]; then
    php artisan storage:link
fi

# Ejecutar optimizaciones de producción
/usr/local/bin/optimize.sh

# Manejar migraciones de forma segura
/usr/local/bin/handle-migrations.sh

# Iniciar servicios con supervisor
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf

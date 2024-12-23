#!/bin/sh
set -e

# Verificar si estamos en primera ejecución
if [ ! -f ".env" ]; then
    echo "Creando archivo .env..."
    cp .env.example .env
fi

# Generar key si no existe
php artisan key:generate --force

# Establecer permisos
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Ejecutar optimizaciones de producción
/usr/local/bin/optimize.sh

# Manejar migraciones de forma segura
/usr/local/bin/handle-migrations.sh

# Iniciar servicios con supervisor
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf

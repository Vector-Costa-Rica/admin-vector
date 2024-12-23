#!/bin/sh
set -e

# En producción, solo instalar dependencias si es necesario
if [ ! -d "vendor" ]; then
    echo "Instalando dependencias de producción..."
    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
fi

# Generar key de Laravel si no existe
php artisan key:generate --force

# Configurar permisos de storage
chmod -R 775 storage bootstrap/cache
chown -R nobody:nobody storage bootstrap/cache

# Ejecutar optimizaciones de producción
/usr/local/bin/optimize.sh

# Manejar migraciones de forma segura
/usr/local/bin/handle-migrations.sh

# Iniciar servicios con supervisor
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf

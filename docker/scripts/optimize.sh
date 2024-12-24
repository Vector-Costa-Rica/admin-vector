#!/bin/sh
set -e

echo "Iniciando optimizaciones para producción..."

# Verificar si la tabla de caché existe
check_cache_table() {
    php artisan db:show --json 2>/dev/null | grep -q '"cache"'
    return $?
}

# Limpiar cachés de manera segura
if check_cache_table; then
    echo "Limpiando cachés..."
    php artisan cache:clear || true
fi

echo "Limpiando otras cachés..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "Generando cachés..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Optimizando el cargador de clases..."
composer dump-autoload --optimize --no-dev

echo "Optimizaciones completadas exitosamente."

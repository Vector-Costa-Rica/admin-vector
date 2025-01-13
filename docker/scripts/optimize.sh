#!/bin/sh
set -e

echo "Iniciando optimizaciones para producción..."

# Verificar si la tabla de caché existe
check_cache_table() {
    php artisan db:show --json 2>/dev/null | grep -q '"cache"'
    return $?
}

echo "=== Fase 1: Limpieza de cachés ==="
# Limpiar cachés de manera segura
if check_cache_table; then
    echo "Limpiando caché de la base de datos..."
    php artisan cache:clear || true
fi

echo "Limpiando cachés del sistema..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "=== Fase 2: Generación de nuevas cachés ==="
echo "Generando caché de configuración..."
php artisan config:cache

echo "Generando caché de rutas..."
php artisan route:cache

echo "Generando caché de vistas..."
php artisan view:cache

echo "=== Fase 3: Optimización de autoload ==="
echo "Optimizando el cargador de clases..."
composer dump-autoload --optimize --no-dev

echo "=== Optimizaciones completadas exitosamente ==="

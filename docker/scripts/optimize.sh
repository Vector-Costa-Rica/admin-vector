#!/bin/sh
set -e

echo "Iniciando optimizaciones para producción..."

# Limpiar cachés existentes
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar la aplicación
echo "Optimizando la configuración..."
php artisan config:cache

echo "Optimizando rutas..."
php artisan route:cache

echo "Optimizando vistas..."
php artisan view:cache

# Optimizar el cargador de clases
echo "Optimizando el cargador de clases..."
composer dump-autoload --optimize --no-dev

echo "Optimizaciones completadas exitosamente."

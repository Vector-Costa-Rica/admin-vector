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

# Optimizar las clases cargadas por el composer
echo "Optimizando el cargador de clases..."
composer dump-autoload --optimize --no-dev

# Compilar assets si estás usando Vite/Mix (descomentar la línea apropiada)
# if [ -f "vite.config.js" ]; then
#     echo "Compilando assets con Vite..."
#     npm ci --only=production
#     npm run build
# elif [ -f "webpack.mix.js" ]; then
#     echo "Compilando assets con Laravel Mix..."
#     npm ci --only=production
#     npm run production
# fi

# Establecer el modo de producción en .env
sed -i 's/APP_ENV=.*/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env

echo "Optimizaciones completadas exitosamente."

#!/bin/sh
set -e

echo "Verificando estado de la base de datos..."

# Esperar a que MySQL esté disponible
wait_for_mysql() {
    echo "Esperando a que MySQL esté disponible..."
    for i in $(seq 1 30); do
        if php artisan db:show --json > /dev/null 2>&1; then
            echo "MySQL está disponible."
            return 0
        fi
        echo "Intentando conectar a MySQL... intento $i"
        sleep 2
    done
    echo "No se pudo conectar a MySQL después de 30 intentos."
    return 1
}

# Esperar a que MySQL esté disponible
wait_for_mysql

# Verificar si hay tablas en la base de datos
TABLES_COUNT=$(php artisan db:show --json 2>/dev/null | grep -o '"tables":[0-9]*' | cut -d':' -f2)

if [ -z "$TABLES_COUNT" ] || [ "$TABLES_COUNT" -eq "0" ]; then
    echo "Base de datos vacía, ejecutando migraciones iniciales..."
    # Como la base de datos está vacía, es seguro ejecutar todas las migraciones
    php artisan migrate --force
    echo "Migraciones iniciales completadas."

    # Crear las tablas de caché específicamente si no existen
    if ! php artisan schema:table cache > /dev/null 2>&1; then
        echo "Creando tablas de caché..."
        php artisan cache:table
        php artisan migrate --force
    fi

    # Crear las tablas de sesiones si no existen
    if ! php artisan schema:table sessions > /dev/null 2>&1; then
        echo "Creando tablas de sesiones..."
        php artisan session:table
        php artisan migrate --force
    fi
else
    echo "Base de datos existente, verificando migraciones pendientes..."

    # Verificar si hay migraciones pendientes
    PENDING_MIGRATIONS=$(php artisan migrate:status --json | grep -o '"pending":[0-9]*' | cut -d':' -f2)

    if [ "$PENDING_MIGRATIONS" -gt "0" ]; then
        echo "Detectadas $PENDING_MIGRATIONS migraciones pendientes."
        echo "Verificando si son seguras..."

        # Simular las migraciones para verificar que sean seguras
        MIGRATION_PREVIEW=$(php artisan migrate --pretend)

        if echo "$MIGRATION_PREVIEW" | grep -iE "drop|delete|remove|rename|modify|alter" > /dev/null; then
            echo "ADVERTENCIA: Se detectaron operaciones potencialmente destructivas."
            echo "Las migraciones que modifican o eliminan datos están deshabilitadas."
            exit 1
        else
            echo "Las migraciones son seguras, procediendo..."
            php artisan migrate --force
        fi
    else
        echo "No hay migraciones pendientes."
    fi
fi

echo "Proceso de migración completado."

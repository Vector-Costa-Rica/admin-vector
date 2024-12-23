#!/bin/sh
set -e

echo "Verificando estado de la base de datos..."

# Esperar a que la base de datos esté disponible
until php artisan db:show --json > /dev/null 2>&1; do
    echo "Esperando conexión con la base de datos..."
    sleep 2
done

# Verificar si hay tablas en la base de datos
TABLES_COUNT=$(php artisan db:show --json | grep -o '"tables":[0-9]*' | cut -d':' -f2)

if [ "$TABLES_COUNT" -eq "0" ]; then
    echo "Base de datos vacía, ejecutando migración completa..."
    php artisan migrate --force
else
    echo "Base de datos existente, verificando migraciones pendientes..."

    # Verificar migraciones pendientes
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

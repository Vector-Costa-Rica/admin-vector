#!/bin/sh
set -e

echo "Verificando migraciones pendientes..."

# Función para verificar si una migración es segura
check_migration_safety() {
    local migration_output="$1"

    # Lista de operaciones prohibidas que podrían destruir o modificar datos
    local FORBIDDEN_OPERATIONS="drop|delete|remove|modify|change|alter|rename|update"

    # Verificar operaciones prohibidas
    if echo "$migration_output" | grep -iE "$FORBIDDEN_OPERATIONS" > /dev/null; then
        return 1
    fi

    # Verificar que solo contenga operaciones permitidas
    if echo "$migration_output" | grep -iE "create table|add column|add index|add foreign key" > /dev/null; then
        return 0
    fi

    # Si no coincide con ninguna operación conocida, mejor prevenir
    return 1
}

# Obtener la salida de las migraciones pendientes
PENDING_MIGRATIONS=$(php artisan migrate --pretend)

# Verificar si hay migraciones pendientes
if [ -z "$PENDING_MIGRATIONS" ]; then
    echo "No hay migraciones pendientes."
    exit 0
fi

# Flag para rastrear si todas las migraciones son seguras
ALL_MIGRATIONS_SAFE=true

# Analizar cada migración
echo "$PENDING_MIGRATIONS" | while IFS= read -r line; do
    if [ ! -z "$line" ]; then
        if ! check_migration_safety "$line"; then
            echo "ERROR: Se detectó una migración potencialmente peligrosa:"
            echo "$line"
            echo "Esta migración NO será ejecutada para proteger los datos existentes."
            ALL_MIGRATIONS_SAFE=false
            exit 1
        fi
    fi
done

# Si llegamos aquí y ALL_MIGRATIONS_SAFE es true, ejecutar las migraciones
if [ "$ALL_MIGRATIONS_SAFE" = true ]; then
    echo "Todas las migraciones son seguras. Procediendo con la ejecución..."
    php artisan migrate --force
    echo "Migraciones completadas exitosamente."
else
    echo "ERROR: Algunas migraciones fueron rechazadas por seguridad."
    echo "Por favor, revise sus migraciones y asegúrese de que solo incluyan:"
    echo "- Creación de nuevas tablas"
    echo "- Adición de nuevas columnas"
    echo "- Creación de nuevos índices"
    echo "NO se permiten operaciones que modifiquen o eliminen datos existentes."
    exit 1
fi

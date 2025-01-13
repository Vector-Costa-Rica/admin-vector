#!/bin/bash

# Script para generar certificados SAML
echo "Iniciando generación de certificados SAML..."

# Crear estructura de directorios
echo "Creando estructura de directorios..."
mkdir -p storage/saml/{development,production}/{idp,sp}

# Función para generar certificados
generate_certificates() {
    local env=$1
    local path="storage/saml/$env"

    echo "Generando certificados para $env..."

    # Generar certificado SP y llave privada
    openssl req -x509 -newkey rsa:4096 \
        -keyout "$path/sp/key.pem" \
        -out "$path/sp/cert.pem" \
        -days 3650 -nodes \
        -subj "/C=CR/ST=San Jose/L=San Jose/O=Vector/OU=IT/CN=vector-manager"

    # Si es desarrollo, generar también certificado IDP
    if [ "$env" = "development" ]; then
        openssl req -x509 -newkey rsa:4096 \
            -keyout "$path/idp/key.pem" \
            -out "$path/idp/cert.pem" \
            -days 3650 -nodes \
            -subj "/C=CR/ST=San Jose/L=San Jose/O=Vector/OU=IT/CN=mock-idp"
    fi

    # Establecer permisos correctos
    chmod 600 "$path/sp/key.pem"
    chmod 644 "$path/sp/cert.pem"

    if [ "$env" = "development" ]; then
        chmod 600 "$path/idp/key.pem"
        chmod 644 "$path/idp/cert.pem"
    fi
}

# Generar certificados para desarrollo y producción
generate_certificates "development"
generate_certificates "production"

# Si existe un certificado de Azure AD, moverlo
if [ -f "FederationMetadata.xml" ]; then
    echo "Moviendo metadata de Azure AD..."
    mv FederationMetadata.xml storage/saml/production/idp/metadata.xml
fi

echo "Certificados generados exitosamente"
echo "Ubicación de los certificados:"
echo "- Desarrollo: storage/saml/development/"
echo "- Producción: storage/saml/production/"

# Mostrar estructura de directorios creada
echo -e "\nEstructura de directorios creada:"
tree storage/saml

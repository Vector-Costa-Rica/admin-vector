#!/bin/bash

# Crear estructura de directorios
mkdir -p storage/saml/{development,production}/{idp,sp}

# Guardar el certificado de Azure AD
cat > storage/saml/production/idp/azure_idp.pem << 'EOL'
-----BEGIN CERTIFICATE-----
MIIC8DCCAdigAwIBAgIQaeAcGWDZnIpIRF1VI+JVIDANBgkqhkiG9w0BAQsFADA0MTIwMAYDVQQD
EylNaWNyb3NvZnQgQXp1cmUgRmVkZXJhdGVkIFNTTyBDZXJ0aWZpY2F0ZTAeFw0yNDEyMDkwMTIy
MDBaFw0yNzEyMDkwMTIyMDBaMDQxMjAwBgNVBAMTKU1pY3Jvc29mdCBBenVyZSBGZWRlcmF0ZWQg
U1NPIENlcnRpZmljYXRlMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0K5GzWYHP3In
el8qmabvQhT8BAxYHqKvn6+XoMxT2vBeZTG/HlM0wOlUhGdQFgzOYifF3dc/dm2JG79hK90QRK1J
f25m4yUVPce8BkFDRehuaSrY9Ay/XX/vPalssSnPcJ5CMNP/q76CgslzntEUOkeQLi6VkzeEZ8sn
YgSNykB5KPPTm4+wc3K3Ujr75oKYr6fHUH+GzYqiLCZRxkublrguqyWAeqXr+f2JRUF9pBrnbpo1
abGlR8LZ3lGBxiASVVonE0o5sPdZJXLxg268RhaVuxpexjHgQ5it+tHZTZOtI+M3Apz1hrNrkycQ
7kV6dGPQP8sWgAj82b23ruceLQIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQAxzA2+tLlH0tXiW4J0
qrSli7MV4w1psMfVWlzKBdBJum+HmGELl6Xvyb4zX8ZyaCsfO/MViuzH4lvrNwARE3GXqfHPP1gX
fkmYEw57t47p0TgE3LWhoQpenDSYT81yBxzy/vMKtKHJMf1g0PNzn4qbvbQlD/Hf41qCjjCutD7D
uwILhf/RHSGdrveEvq5QHUXGlXPqkaryxEVQBknPz+I0tuNRB3hgSclbXTD6lnhM8xxHCafB5fBN
govjfrNSECN7e/UeUNZosMs2cg5HQxYQG0jtWPHS9wM3sweVqsDbQZ/Y5Su98jESRlIecPksQcZB
IuEf0X2C6FepWsLZbYqu
-----END CERTIFICATE-----
EOL

# Guardar los metadatos de Azure AD
cat > storage/saml/production/idp/metadata.xml << 'EOL'
<?xml version="1.0" encoding="utf-8"?>
<EntityDescriptor ID="_3b61cf45-d936-43e6-ae5c-0d82618f294a" entityID="https://sts.windows.net/f099ade2-d6df-4bf1-ac6c-55e770cd4d90/" xmlns="urn:oasis:names:tc:SAML:2.0:metadata">
<!-- Tu XML completo aquí -->
</EntityDescriptor>
EOL

# Generar certificado SP para producción
openssl req -x509 -newkey rsa:4096 \
    -keyout storage/saml/production/sp/key.pem \
    -out storage/saml/production/sp/cert.pem \
    -days 3650 -nodes \
    -subj "/C=CR/ST=San Jose/L=San Jose/O=Vector/OU=IT/CN=vector-manager"

# Generar certificados para desarrollo (Mock SAML)
openssl req -x509 -newkey rsa:4096 \
    -keyout storage/saml/development/sp/key.pem \
    -out storage/saml/development/sp/cert.pem \
    -days 3650 -nodes \
    -subj "/C=CR/ST=San Jose/L=San Jose/O=Vector/OU=IT/CN=vector-manager-dev"

openssl req -x509 -newkey rsa:4096 \
    -keyout storage/saml/development/idp/key.pem \
    -out storage/saml/development/idp/cert.pem \
    -days 3650 -nodes \
    -subj "/C=CR/ST=San Jose/L=San Jose/O=Vector/OU=IT/CN=mock-idp"

# Establecer permisos correctos
chmod 600 storage/saml/production/sp/key.pem
chmod 644 storage/saml/production/sp/cert.pem
chmod 600 storage/saml/development/sp/key.pem
chmod 644 storage/saml/development/sp/cert.pem
chmod 600 storage/saml/development/idp/key.pem
chmod 644 storage/saml/development/idp/cert.pem
chmod 644 storage/saml/production/idp/azure_idp.pem
chmod 644 storage/saml/production/idp/metadata.xml

echo "Certificados y metadatos configurados exitosamente"

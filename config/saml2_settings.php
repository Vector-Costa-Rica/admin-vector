<?php

return [
    'debug' => env('APP_DEBUG', false),
    'strict' => false,

    // Configuración específica para nuestra aplicación
    'vectoradminapp' => [
        'sp' => [
            'entityId' => 'https://vectoradminapp.vectorcr.com',
            'assertionConsumerService' => [
                'url' => 'https://vectoradminapp.vectorcr.com/saml2/acs',
            ],
            'singleLogoutService' => [
                'url' => 'https://vectoradminapp.vectorcr.com/saml2/logout',
            ],
            'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
            'x509cert' => '',
            'privateKey' => '',
        ],
        'idp' => [
            'entityId' => 'https://sts.windows.net/f099ade2-d6df-4bf1-ac6c-55e770cd4d90/',
            'singleSignOnService' => [
                'url' => 'https://login.microsoftonline.com/f099ade2-d6df-4bf1-ac6c-55e770cd4d90/saml2',
            ],
            'singleLogoutService' => [
                'url' => 'https://login.microsoftonline.com/f099ade2-d6df-4bf1-ac6c-55e770cd4d90/saml2',
            ],
            'x509cert' => '-----BEGIN CERTIFICATE-----
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
-----END CERTIFICATE-----',
        ],
        'security' => [
            'nameIdEncrypted' => false,
            'authnRequestsSigned' => false,
            'logoutRequestSigned' => false,
            'logoutResponseSigned' => false,
            'signMetadata' => false,
            'wantMessagesSigned' => false,
            'wantAssertionsSigned' => false,
            'wantNameIdEncrypted' => false,
            'requestedAuthnContext' => true,
        ],
    ]
];

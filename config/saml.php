<?php

return [
    'sp' => [
        'entityId' => 'https://vectoradminapp.vectorcr.com',
        'assertionConsumerService' => [
            'url' => 'https://vectoradminapp.vectorcr.com/saml/acs',
        ],
    ],
    'idp' => [
        'entityId' => 'https://sts.windows.net/f099ade2-d6df-4bf1-ac6c-55e770cd4d90/',
        'singleSignOnService' => [
            'url' => 'https://login.microsoftonline.com/f099ade2-d6df-4bf1-ac6c-55e770cd4d90/saml2',  // URL de inicio de sesión (Login URL)
        ],
        'singleLogoutService' => [
            'url' => 'https://login.microsoftonline.com/f099ade2-d6df-4bf1-ac6c-55e770cd4d90/saml2',  // URL de cierre de sesión (Logout URL)
        ],
        'x509cert' => file_get_contents(storage_path('app/certs/azure_ad_public_cert.cer')),  // Coloca el certificado descargado desde Azure AD
    ],
];

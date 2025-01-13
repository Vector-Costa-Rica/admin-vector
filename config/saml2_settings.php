<?php

return [
    'debug' => env('APP_DEBUG', false),
    'strict' => false,

    // Configuración específica para nuestra aplicación
    'vectoradminapp' => [
        'sp' => [
            'entityId' => 'https://vectoradminapp.vectorcr.com',
            'assertionConsumerService' => [
                'url' => 'https://vectoradminapp.vectorcr.com/auth/saml2/callback',  // Actualizada esta URL
            ],
            'singleLogoutService' => [
                'url' => 'https://vectoradminapp.vectorcr.com/auth/saml2/logout',    // También actualizada para mantener consistencia
            ],
            'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
            'x509cert' => '-----BEGIN CERTIFICATE-----
MIIFtTCCA52gAwIBAgIUDQOtzxdK8BencsLJXNk2O2oEHB0wDQYJKoZIhvcNAQEL
BQAwajELMAkGA1UEBhMCQ1IxETAPBgNVBAgMCFNhbiBKb3NlMREwDwYDVQQHDAhT
YW4gSm9zZTEPMA0GA1UECgwGVmVjdG9yMQswCQYDVQQLDAJJVDEXMBUGA1UEAwwO
dmVjdG9yLW1hbmFnZXIwHhcNMjQxMjA5MDIxODE3WhcNMzQxMjA3MDIxODE3WjBq
MQswCQYDVQQGEwJDUjERMA8GA1UECAwIU2FuIEpvc2UxETAPBgNVBAcMCFNhbiBK
b3NlMQ8wDQYDVQQKDAZWZWN0b3IxCzAJBgNVBAsMAklUMRcwFQYDVQQDDA52ZWN0
b3ItbWFuYWdlcjCCAiIwDQYJKoZIhvcNAQEBBQADggIPADCCAgoCggIBAMWzlWEj
RzaLhV4HnO2ai8RdNSMT1C3xXLR/8iE2gF+nlk1/e5J7TLDQQTb1avG0QWWdE3Vl
iJCMNjmzDi4PxfjkvR6LFmmFJhsq32Ep4fiLpS3x5pbY7pXsAuC+hUqvDh/+ctMI
c0KESZFF3ACbBY5jpVoX05MaK/vAlKeIRSahcR0ob9PsmPiPIVtjOzuHl0BS+8e1
kP+7Asy9/iuqzdumc5L7F3vx5NWsrVTae0QSiw5T90JruGWMra9ArUIzau5oosqa
FuQ6Jetl9AsncO2AGIE38bnpFkfNkLuGwiwIiSLNDDLPN3EF9HLg6iRFOJat84lC
MGuMkac4mkaL9vLMEVZSt82noerElK5ZxSea2miN+BN9T/3yjoaBFrUzSAJQ8GDx
LFUMnQdOJVoM14xSa3qqyTsflMqrk9V0stokiYQbM1XpGQhZAa8OKu5LReKKtggN
dgC5KLj93fuOc1/7uo5B0RTmmHGc57j8EPofEg56KSuYqan7iAh/IxfEdyBV6KEM
zBP8jP2Hx82tYawLO+KD67txY4FlLw76IIFf0a6sAlTDjapwCGchuAw4xuhpMWUO
2nSI5KKnORx11rrd4ixpetM2lRMC6c7KvDOkg4uu3/eo6SkaVV0nHOicRIxzUR6+
ASqYlfDDPLkSlxlsc/wSWGBlDvGDiVp5XaBXAgMBAAGjUzBRMB0GA1UdDgQWBBT9
FHOnQvivy/0MJxAjxVKonPgQTDAfBgNVHSMEGDAWgBT9FHOnQvivy/0MJxAjxVKo
nPgQTDAPBgNVHRMBAf8EBTADAQH/MA0GCSqGSIb3DQEBCwUAA4ICAQCYnqeJ9WV4
JA5RprWWQTc6D89MlTNNjE1Flv3CiZvTFz5z7/Ar3+LBv5V8BfkMugHCnKB0DwdO
y7WQJkmXLkVUw+HGqjTR515jyaopbRg9cwmvNMHabtx+Jd1Ebs8gPQmKUk/GsT4U
2GRhPadwsJ6lYtXbCFJM922EOvhNcgsCi+0XD8CHDgNp40k8y9XtcEmbwg8XmT0k
1oVE5UQFxmSeRoeLAwCCq2UdO7iwEhY4Cwi8W3D8NwMGq7YEDTHHAk60Lo9C8WH7
fm0+kDRaaOudBc2strxtgS2t09RJKZHcXAgotePjPynSzUFz9w0I7dIBn731f3FX
Kg53di6LAe2BdeezrVPpwqGmZZNZwzp8MUsZ3wXMBL1xzVUmmS3nGt+V4ijU2rIW
Q7aNrU+LxtAy4itxLfkOfZeMpznmyLfIFjlPslr4jtQDxTbZXOYSgrsAUkeRUkDL
QtO5PfY9dILTiy8wbFZvlDB0cpaw6OZEhgerdhbOOX85bpq2Ya85UZe5CexT11XS
TjtlWLnSBzA7w844CLfK2UWaFC/cj1dOf6lit2Qbc3EBwAnL2WWVNBcqWcRkM0E7
J3+mzY1Pb8OsILcxqMbXKRE6MZB3KxYcZdA4WETpRgce9X6CX5RafxINrnA+ywjg
SC8nhBJSP74IQY3+6wr52PO5vnRZ2h7HcQ==
-----END CERTIFICATE-----',
            'privateKey' => '-----BEGIN PRIVATE KEY-----
MIIJQwIBADANBgkqhkiG9w0BAQEFAASCCS0wggkpAgEAAoICAQDFs5VhI0c2i4Ve
B5ztmovEXTUjE9Qt8Vy0f/IhNoBfp5ZNf3uSe0yw0EE29WrxtEFlnRN1ZYiQjDY5
sw4uD8X45L0eixZphSYbKt9hKeH4i6Ut8eaW2O6V7ALgvoVKrw4f/nLTCHNChEmR
RdwAmwWOY6VaF9OTGiv7wJSniEUmoXEdKG/T7Jj4jyFbYzs7h5dAUvvHtZD/uwLM
vf4rqs3bpnOS+xd78eTVrK1U2ntEEosOU/dCa7hljK2vQK1CM2ruaKLKmhbkOiXr
ZfQLJ3DtgBiBN/G56RZHzZC7hsIsCIkizQwyzzdxBfRy4OokRTiWrfOJQjBrjJGn
OJpGi/byzBFWUrfNp6HqxJSuWcUnmtpojfgTfU/98o6GgRa1M0gCUPBg8SxVDJ0H
TiVaDNeMUmt6qsk7H5TKq5PVdLLaJImEGzNV6RkIWQGvDiruS0XiirYIDXYAuSi4
/d37jnNf+7qOQdEU5phxnOe4/BD6HxIOeikrmKmp+4gIfyMXxHcgVeihDMwT/Iz9
h8fNrWGsCzvig+u7cWOBZS8O+iCBX9GurAJUw42qcAhnIbgMOMboaTFlDtp0iOSi
pzkcdda63eIsaXrTNpUTAunOyrwzpIOLrt/3qOkpGlVdJxzonESMc1EevgEqmJXw
wzy5EpcZbHP8ElhgZQ7xg4laeV2gVwIDAQABAoICAFIEMlBeBkkGkIl1txCLMLiB
wm2O6FM28jKfcZ99sLv+FkiIPoeCR0qC2ssl/PvQv+CbzVrCiGkPAd/l/Ff3izW6
DwsAwId+CVTz8D+q1Gf75sast/CUhkYD5x29bCaTgNSdBp2tv8M0hiCSTrahmSh9
Bjfd8pPwI5cJSaJG0gk4qGhkQiA04zEaj8gN3qDPm1vAEVvEYyb0OjEeELek4Enj
P8cWC6QLsddDD5VIHiMbXjzPcKBJd/II7nTp6/auxgmptv899Ykw5lJFtXx7HEoq
/f99afaqT+fxy71AJoWF9P5O+mSJuoJU6hhOXlQ1s0gvWfzzTIsRgctRnA7wOb27
7RK6FDR2fPpOtU6Zqv8oSI/aoNxsF16r358/P9touZjavtGOOvZFMT3vpAHEjgzK
clY0RIMMt7QqNsGXjJQPgIcvyhr9M7LYKWz5Ke+jq8BmPzH21zcfufKY0MGY+VL6
Ul9QsxnKZzejzNg/exwxRL+T8sJU1PG686uwwrC97+T657Ewx3Aa9WGr9XeZVFdG
4FtJ7H9nA8bsgGYu8ktqIGphiuT/tykoRl048LY2PbRzTdhx9QeXAtVGhXQz6ZPV
jcHQ6Ya75jqWVcJdd1bd4ORPc4LADpc5gUbXXCiLwTxrV7jZqp7S/qVgahsowhE2
DaDGceChdgPj6SNCghbBAoIBAQD/OAyXbNgnVP3d/lTBCSwW2vkIZ/mIAV2zWAO0
qZ2PGWc2jSxkIWABByJVKXq62aXP3PaOk0wvq0JGgM5pwQscw0wqj25zfB4PTDyb
U085PC1kvhojr16dBRzO1PQAnKOJ51EPgm13c7xOcoti/8n57HBnZAdnVwIymCJ+
fHIXJ8iToKyMfZNWcJqyB4bqdw4qnfULn11tv+ualnM1dwKT0TgKArEH/zd1x1y6
YEmnby+Ar106G3oeexEkGNzIkrTSNL1VnRYb2nQkrRcmTh4KxBUkHMNlaFKpyM7W
xgBBTKWr/tr1Ya8FmcZt93eJPXtJKI+D4jyf8/n1AtFp1yGfAoIBAQDGTnjuo3HX
XYcPpPOAAwvLCLy9aVEWwSKbp9ll4FsXbGWntGfDf+Xwz62/1Gza5qY2oowIX78X
vAbZ9HJJhWqUoYI/WFzAKWZ3ZGv71df3E5MoKCMJlzI4mk3a3PC2rSfnBZR41BxT
+nfPmhaLcF4EEcLATWMYSImUYhHgbRU7G5DBXu3Ujet6aDJ2UcZ9gL0IWwQ8JsQz
SpFOidGrzLBIcbaa3j7h5YTyzRkGE7WyVjpi7lDmgmaCkdQDdUSFxwUwAghOU43S
tPw7JnOXYeDSgmO+oIhojTxbTVC6S/xuT4sP5d3tk3agALgbpmUGwjDzIjskHKjX
iR7cBxbeIrZJAoIBAEjTqJwpHgETOqH3Kh4vTNp0v61LZiQOJ7u+eMg7wk8MM6yY
Wjt0Xp3MeFqOzIu3AZ6v8dc7ZvkPSIkvxjbdNBuQCxL1/NGNcFJzMbQs8KVna5ic
un7GUxxBUjgfAkWObQgz7qibUjtM1kYYX3fvf6YGdwi5vXcal/DyAp3PSwsaehzO
M46fYsS+uxXkGfab5Hn6uusHHbTsmAFzvwpZnG2rvO72V6fthtjf+7DfOwVBL+/r
mpGzN4StH3YKdi3x1xFIpWJhweZOzueMUNfIYMA3tcSpRISSYUw7lfIpa/c2NOxA
fCCbMENL3xiQDhcmaSC2J0W98AjAqOnmVPV7wwcCggEBAK+hcnOQKFQgjmzpn4tB
p2EzpM1wiBPKC9emCeGzmZmeNE4adkfsb38ev/iEr8ATxSPgRHtqKTlhGwRP/iRl
WCa5AD0QCl1ajViyhPyuwS4flRZcreNhmGYPK2LqiPqRUyTFiWCWI1yxGQmxo8Pt
Sib6f+yG3Xv1Snwzeze0BLirykGozKSS6PopbH/kHxKqQUE0MwT/JId0xEq1vxAU
IhCXPa3dpf21pFDSGxaJwENpIR7biKUO2rFLbGV/p6d/w3VoZ2jPkW0LQ9UzUe7C
DjcCqkp7rTRD2i/WwuesQq6EHlp/MFWoY8iDuosqjJE2zdmX+J8cDuGNnNosITGt
tpkCggEBAOSP8qOoXE4uCK+ceaQ3Vi6wAq9hwloPzIN6RM+dTq/RkNnmeFKphUo5
ylV0qgq7coACAEVXV2rsn/fUIm0vuoV7RNfsBzs6FiMfbnw751G3/HPfv+uUyOBh
ACDajgMdgh35TKXen0nxHOtWDz5wEVpHhD2iB9MvDu+jGwc7AMkKJrIPd+oWKgDn
kclOtezOeT2U81cPw4ivYfYmMFI6ExkleQmf9nKmBtWUF7n8KV7y9EFpU2vbgd8/
7JDOj9osIdRx3ZkgmfP9dxOw9XXxu3WXE4m4ZWVjFWXPWCUshxNSPjTm0TPOcAya
7YXdVmWskvA6nfdsCDlfxJAlDk2Yaa8=
-----END PRIVATE KEY-----',
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
            'wantXMLValidation' => true,
            'relaxDestinationValidation' => true,  // Agregar esto para ser más permisivo con las URLs
            'destinationStrictlyMatches' => false  // Y esto también
        ],
    ]
];

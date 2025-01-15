<?php

namespace App\Http\Controllers;

use Aacotroneo\Saml2\Saml2Auth;
use DOMDocument;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;
use OneLogin\Saml2\Auth as OneLogin_Saml2_Auth;
use OneLogin\Saml2\Settings as OneLogin_Saml2_Settings;
use OneLogin\Saml2\Error;

class Saml2Controller extends Controller
{

    public function login(): Application|string|Redirector|RedirectResponse|null
    {
        try {
            Log::debug('Iniciando proceso de login SAML', [
                'url' => request()->fullUrl(),
                'headers' => request()->headers->all()
            ]);

            $auth = $this->getSaml2Auth();

            // Agregar el token CSRF a la URL de retorno
            $returnTo = route('home') . '?_token=' . csrf_token();
            Log::debug('URL de retorno configurada', ['returnTo' => $returnTo]);

            // Configurar parámetros adicionales para el login
            $parameters = [
                'RelayState' => $returnTo,
                'SigAlg' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256'
            ];

            try {
                $loginUrl = $auth->login($returnTo, $parameters, false, false, true);
                Log::debug('URL de login generada', ['loginUrl' => $loginUrl]);

                return redirect()->away($loginUrl);
            } catch (\Exception $e) {
                Log::error('Error generando URL de login', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (Exception $e) {
            Log::error('SAML2 Login Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/')->with('error', 'Error iniciando sesión. Por favor intente de nuevo.');
        }
    }

    /**
     * @throws Error
     */
    protected function getSaml2Auth(): OneLogin_Saml2_Auth
    {
        try {
            Log::debug('Intentando inicializar SAML2Auth');

            $config = config('saml2_settings.vectoradminapp');
            if (!$config) {
                throw new Exception('Configuración SAML2 no encontrada para vectoradminapp');
            }

            Log::debug('Configuración SAML2 encontrada', ['config' => $config]);

            // Configurar las opciones de SAML para OneLogin v3
            $settingsArray = [
                'strict' => false,
                'debug' => true,
                'baseurl' => url('/'),
                'sp' => [
                    'entityId' => $config['sp']['entityId'],
                    'assertionConsumerService' => [
                        'url' => $config['sp']['assertionConsumerService']['url'],
                        'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                    ],
                    'singleLogoutService' => [
                        'url' => $config['sp']['singleLogoutService']['url'],
                        'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                    ],
                    'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
                    'x509cert' => $config['sp']['x509cert'],
                    'privateKey' => $config['sp']['privateKey'],
                ],
                'idp' => [
                    'entityId' => $config['idp']['entityId'],
                    'singleSignOnService' => [
                        'url' => $config['idp']['singleSignOnService']['url'],
                        'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                    ],
                    'singleLogoutService' => [
                        'url' => $config['idp']['singleLogoutService']['url'],
                        'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                    ],
                    'x509cert' => $config['idp']['x509cert'],
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
                    'relaxDestinationValidation' => true,
                    'destinationStrictlyMatches' => false
                ],
            ];

            Log::debug('Creando instancia de OneLogin_Saml2_Auth');
            return new OneLogin_Saml2_Auth($settingsArray);
        } catch (Exception $e) {
            Log::error('SAML2 Init Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function acs(Request $request): Application|Redirector|RedirectResponse
    {
        try {
            Log::debug('Recibiendo respuesta SAML en ACS', [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'headers' => $request->headers->all(),
                'has_saml_response' => $request->has('SAMLResponse'),
                'all_params' => $request->all()
            ]);

            // Agregar el token CSRF a la sesión
            if ($request->has('RelayState')) {
                $relayState = $request->input('RelayState');
                if (str_contains($relayState, '_token=')) {
                    parse_str(parse_url($relayState, PHP_URL_QUERY), $params);
                    if (isset($params['_token'])) {
                        $request->session()->put('_token', $params['_token']);
                    }
                }
            }

            if (!$request->has('SAMLResponse')) {
                Log::error('No SAMLResponse encontrada');
                throw new Exception('No se recibió respuesta SAML');
            }

            // Decodificar y registrar la respuesta SAML para debugging
            $samlResponse = base64_decode($request->input('SAMLResponse'));
            Log::debug('SAMLResponse decodificada', ['response' => $samlResponse]);

            $auth = $this->getSaml2Auth();

            try {
                // Procesar la respuesta SAML
                $auth->processResponse();

                Log::debug('Respuesta SAML procesada', [
                    'errors' => $auth->getErrors(),
                    'attributes' => $auth->getAttributes(),
                    'nameId' => $auth->getNameId(),
                    'lastErrorReason' => $auth->getLastErrorReason()
                ]);

            } catch (Exception $e) {
                Log::error('Error procesando respuesta SAML', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

            $errors = $auth->getErrors();
            if (!empty($errors)) {
                Log::error('Errores SAML encontrados', [
                    'errors' => $errors,
                    'lastError' => $auth->getLastErrorReason()
                ]);
                throw new Exception('Error SAML: ' . implode(', ', $errors));
            }

            if (!$auth->isAuthenticated()) {
                Log::error('Usuario no autenticado después del SSO');
                throw new Exception('No autenticado después del SSO');
            }

            $email = $auth->getNameId();
            Log::debug('Email del usuario autenticado', ['email' => $email]);

            if (!str_ends_with($email, '@vectorcr.com')) {
                Log::warning('Intento de acceso con correo no autorizado', [
                    'email' => $email
                ]);
                throw new Exception('Solo se permite el acceso con correo de Vector');
            }

            $attributes = $auth->getAttributes();
            Log::debug('Atributos SAML recibidos', [
                'attributes' => $attributes
            ]);

            $name = $attributes['name'][0] ??
                $attributes['givenname'][0] . ' ' . ($attributes['surname'][0] ?? '') ??
                $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'][0] ??
                explode('@', $email)[0];

            try {
                $user = User::updateOrCreate(
                    ['email' => $email],
                    ['name' => $name]
                );

                Log::debug('Usuario creado/actualizado', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name
                ]);

                Auth::login($user);

                // Obtener la URL de retorno del RelayState o usar la ruta home por defecto
                $returnTo = $request->input('RelayState') ?? route('home');

                // Limpiar cualquier parámetro de token de la URL de retorno
                $returnTo = preg_replace('/[\?&]_token=[^&]+/', '', $returnTo);

                Log::info('Login exitoso - redirigiendo', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'returnTo' => $returnTo
                ]);

                return redirect()->intended($returnTo);

            } catch (Exception $e) {
                Log::error('Error creando/actualizando usuario', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (Exception $e) {
            Log::error('Error general en ACS', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/')->with('error', 'Error procesando la autenticación: ' . $e->getMessage());
        }
    }

    /**
     * @throws Error
     * @throws Exception
     */
    public function logout(Request $request): Application|string|Redirector|RedirectResponse|null
    {
        if (Auth::check()) {
            $auth = $this->getSaml2Auth();
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return $auth->logout(route('welcome'));
        }
        return redirect('/');
    }

    public function metadata(): Application|Response|ResponseFactory
    {
        try {
            $auth = $this->getSaml2Auth();
            $settings = $auth->getSettings();
            $metadata = $settings->getSPMetadata();

            // Asegurar que el XML esté limpio
            $dom = new DOMDocument();
            $dom->loadXML($metadata);
            $metadata = $dom->saveXML();

            return response($metadata, 200, [
                'Content-Type' => 'text/xml',
                'Content-Disposition' => 'attachment; filename="metadata.xml"'
            ]);
        } catch (Exception $e) {
            Log::error('SAML2 Metadata Error: ' . $e->getMessage());
            return response('Error generating metadata', 500);
        }
    }
}

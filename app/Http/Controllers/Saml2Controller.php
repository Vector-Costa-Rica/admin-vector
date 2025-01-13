<?php

namespace App\Http\Controllers;

use Aacotroneo\Saml2\Saml2Auth;
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

            Log::debug('Configuración SAML generada:', [
                'settings' => $auth->getSettings()->getSettings()
            ]);

            $loginRedirect = $auth->login(route('home'));

            Log::debug('URL de redirección generada:', [
                'redirect_url' => $loginRedirect
            ]);

            return $loginRedirect;

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

    protected function getSaml2Auth(): OneLogin_Saml2_Auth
    {
        try {
            Log::debug('Intentando inicializar SAML2Auth');

            $config = config('saml2_settings.vectoradminapp');
            if (!$config) {
                throw new Exception('Configuración SAML2 no encontrada para vectoradminapp');
            }

            Log::debug('Configuración SAML2 encontrada', ['config' => $config]);

            // Configurar las opciones de SAML
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
            // Registrar headers de la petición
            Log::debug('Headers de la petición:', [
                'headers' => $request->headers->all(),
                'method' => $request->method(),
                'url' => $request->fullUrl()
            ]);

            // Registrar la respuesta SAML codificada
            Log::debug('SAMLResponse recibida (encoded):', [
                'SAMLResponse' => $request->input('SAMLResponse')
            ]);

            // Decodificar y registrar la respuesta SAML
            if ($request->has('SAMLResponse')) {
                $decodedSAML = base64_decode($request->input('SAMLResponse'));
                Log::debug('SAMLResponse decodificada:', [
                    'decoded' => $decodedSAML
                ]);
            }

            // Registrar todos los parámetros de la petición
            Log::debug('Todos los parámetros de la petición:', [
                'all_parameters' => $request->all()
            ]);

            if (!$request->has('SAMLResponse')) {
                throw new Exception('No se recibió respuesta SAML');
            }

            $auth = $this->getSaml2Auth();

            // Registrar el estado antes de procesar la respuesta
            Log::debug('Estado antes de procesar la respuesta SAML');

            $auth->processResponse();

            // Registrar errores si existen
            $errors = $auth->getErrors();
            if (!empty($errors)) {
                Log::error('Errores SAML:', [
                    'errors' => $errors,
                    'error_reasons' => $auth->getLastErrorReason()
                ]);
                throw new Exception('Error SAML: ' . implode(', ', $errors));
            }

            // Registrar el estado de autenticación
            Log::debug('Estado de autenticación:', [
                'isAuthenticated' => $auth->isAuthenticated(),
                'attributes' => $auth->getAttributes(),
                'nameId' => $auth->getNameId()
            ]);

            if (!$auth->isAuthenticated()) {
                throw new Exception('No autenticado después del SSO');
            }

            $email = $auth->getNameId();

            if (!str_ends_with($email, '@vectorcr.com')) {
                Log::warning('Intento de acceso con correo no autorizado:', [
                    'email' => $email
                ]);
                throw new Exception('Solo se permite el acceso con correo de Vector');
            }

            $attributes = $auth->getAttributes();
            Log::debug('Atributos SAML recibidos:', [
                'attributes' => $attributes
            ]);

            $name = $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'][0] ??
                $attributes['givenname'][0] ??
                explode('@', $email)[0];

            $user = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name]
            );

            Log::debug('Usuario creado/actualizado:', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name
            ]);

            Auth::login($user);

            Log::debug('Usuario autenticado exitosamente', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return redirect()->intended(route('home'));

        } catch (Exception $e) {
            Log::error('SAML ACS Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/')->with('error', 'Error procesando la autenticación.');
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
            $dom = new \DOMDocument();
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

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

            // Registrar la configuración sin usar getSettings()
            Log::debug('Configuración SAML generada para login');

            // Iniciar el proceso de login directamente
            return $auth->login(route('home'));

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
            Log::debug('Inicio de procesamiento ACS SAML', [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'secure' => $request->secure(),
                'headers' => $request->headers->all()
            ]);

            if (!$request->has('SAMLResponse')) {
                Log::error('No SAMLResponse encontrada en la petición', [
                    'inputs' => $request->all()
                ]);
                throw new Exception('No se recibió respuesta SAML');
            }

            // Decodificar y registrar la respuesta SAML (solo para debugging)
            $samlResponse = base64_decode($request->input('SAMLResponse'));
            Log::debug('SAMLResponse decodificada', [
                'response' => $samlResponse
            ]);

            $auth = $this->getSaml2Auth();

            try {
                $auth->processResponse();
            } catch (Exception $e) {
                Log::error('Error procesando respuesta SAML', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

            $errors = $auth->getErrors();
            if (!empty($errors)) {
                Log::error('Errores de autenticación SAML', [
                    'errors' => $errors,
                    'lastError' => $auth->getLastErrorReason(),
                    'lastRequestID' => $auth->getLastRequestID(),
                    'lastResponseXML' => $auth->getLastResponseXML()
                ]);
                throw new Exception('Error SAML: ' . implode(', ', $errors));
            }

            if (!$auth->isAuthenticated()) {
                Log::error('Usuario no autenticado después del SSO');
                throw new Exception('No autenticado después del SSO');
            }

            $email = $auth->getNameId();
            Log::debug('Email obtenido del NameID', ['email' => $email]);

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

            $name = $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'][0] ??
                $attributes['givenname'][0] ??
                explode('@', $email)[0];

            $user = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name]
            );

            Log::debug('Usuario creado/actualizado exitosamente', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name
            ]);

            Auth::login($user);

            Log::info('Login SAML completado exitosamente', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return redirect()->intended(route('home'));

        } catch (Exception $e) {
            Log::error('Error en el proceso ACS SAML', [
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

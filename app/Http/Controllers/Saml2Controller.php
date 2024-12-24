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

    public function login(): Application|string|Redirector|RedirectResponse|null
    {
        try {
            $auth = $this->getSaml2Auth();
            return $auth->login(route('home'));
        } catch (Exception $e) {
            Log::error('SAML Login Error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Error iniciando sesión.');
        }
    }

    public function acs(Request $request): RedirectResponse
    {
        try {
            Log::info('ACS: Recibiendo respuesta SAML', [
                'method' => $request->method(),
                'has_saml_response' => $request->has('SAMLResponse')
            ]);

            $auth = $this->getSaml2Auth();
            $auth->processResponse();

            if (!$auth->isAuthenticated()) {
                Log::error('SAML: Usuario no autenticado después de procesar respuesta');
                return redirect()->route('welcome')->with('error', 'No se pudo autenticar');
            }

            $attributes = $auth->getAttributes();
            $email = $auth->getNameId();

            if (!str_ends_with($email, '@vectorcr.com')) {
                Log::warning("SAML: Intento de acceso con correo no autorizado: {$email}");
                return redirect()->route('welcome')
                    ->with('error', 'Solo se permite el acceso con correo de Vector.');
            }

            // Obtener nombre del usuario
            $name = $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'][0] ??
                $attributes['givenname'][0] ??
                explode('@', $email)[0];

            $user = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name]
            );

            Auth::login($user);

            // Regenerar la sesión después de iniciar sesión
            $request->session()->regenerate();

            Log::info("SAML: Usuario {$email} autenticado exitosamente");

            // Redirigir inmediatamente al home
            return redirect()->intended(route('home'));

        } catch (\Exception $e) {
            Log::error('SAML ACS Error: ' . $e->getMessage());
            return redirect()->route('welcome')
                ->with('error', 'Error procesando la autenticación.');
        }
    }

    /*public function acs(Request $request): Response
    {
        try {
            Log::info('ACS: Iniciando procesamiento de respuesta SAML', [
                'request_method' => $request->method(),
                'saml_response' => $request->get('SAMLResponse') ? 'present' : 'missing'
            ]);

            // Iniciar sesión manualmente
            if (!$request->hasSession()) {
                $request->session()->start();
            }

            $auth = $this->getSaml2Auth();

            // Procesar la respuesta SAML
            $auth->processResponse();

            if (!$auth->isAuthenticated()) {
                throw new Exception('No autenticado después del SSO');
            }

            // Obtener los atributos del usuario
            $attributes = $auth->getAttributes();

            Log::info('SAML Attributes:', $attributes);

            // Obtener el correo electrónico
            $email = $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress'][0] ?? null;
            if (!$email) {
                throw new Exception('No se pudo obtener el correo electrónico');
            }

            // Verificar dominio vectorcr.com
            if (!str_ends_with($email, '@vectorcr.com')) {
                throw new Exception('Solo se permite el acceso con correo de Vector');
            }

            // Obtener o crear el usuario
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'][0] ?? explode('@', $email)[0],
                    'email' => $email,
                ]
            );

            // Autenticar al usuario
            Auth::login($user);

            // Regenerar la sesión
            $request->session()->regenerate();

            Log::info('Usuario autenticado exitosamente', ['email' => $email]);

            // Redireccionar usando JavaScript
            return response()->view('saml.callback', [
                'redirectUrl' => route('home')
            ]);

        } catch (Exception $e) {
            Log::error('SAML ACS Error: ' . $e->getMessage());

            // Redireccionar usando JavaScript en caso de error
            return response()->view('saml.callback', [
                'redirectUrl' => route('welcome'),
                'error' => 'Error en la autenticación: ' . $e->getMessage()
            ]);
        }
    }*/

    public function logout(): Application|string|Redirector|RedirectResponse|null
    {
        try {
            $user = Auth::user();
            $email = $user ? $user->email : 'Unknown';

            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();

            $auth = $this->getSaml2Auth();
            Log::info("Usuario {$email} ha cerrado sesión");

            return $auth->logout(route('welcome'));
        } catch (Exception $e) {
            Log::error('SAML2 Logout Error: ' . $e->getMessage());
            return redirect('/');
        }
    }

    public function metadata(): Application|Response|ResponseFactory
    {
        try {
            $auth = $this->getSaml2Auth();
            $settings = $auth->getSettings();
            $metadata = $settings->getSPMetadata();

            return response($metadata, 200, ['Content-Type' => 'text/xml']);
        } catch (Exception $e) {
            Log::error('SAML2 Metadata Error: ' . $e->getMessage());
            return response('Error generating metadata', 500);
        }
    }
}

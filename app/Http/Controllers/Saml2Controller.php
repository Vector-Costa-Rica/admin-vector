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

    public function login(Request $request): Application|string|Redirector|RedirectResponse|null
    {
        try {
            $auth = $this->getSaml2Auth();
            return $auth->login(route('home'));
        } catch (Exception $e) {
            Log::error('SAML Login Error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Error iniciando sesión.');
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
            Log::debug('Recibiendo respuesta SAML', [
                'method' => $request->method(),
                'has_saml_response' => $request->has('SAMLResponse')
            ]);

            if (!$request->has('SAMLResponse')) {
                throw new Exception('No se recibió respuesta SAML');
            }

            $auth = $this->getSaml2Auth();

            // Verificar y procesar la respuesta SAML
            $requestID = isset($_SESSION['AuthNRequestID']) ? $_SESSION['AuthNRequestID'] : null;
            $auth->processResponse($requestID);
            unset($_SESSION['AuthNRequestID']);

            $errors = $auth->getErrors();
            if (!empty($errors)) {
                throw new Exception('Error SAML: ' . implode(', ', $errors));
            }

            if (!$auth->isAuthenticated()) {
                throw new Exception('No autenticado después del SSO');
            }

            $email = $auth->getNameId();

            if (!str_ends_with($email, '@vectorcr.com')) {
                throw new Exception('Solo se permite el acceso con correo de Vector');
            }

            $attributes = $auth->getAttributes();
            $name = $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'][0] ??
                $attributes['givenname'][0] ??
                explode('@', $email)[0];

            $user = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name]
            );

            Auth::login($user);

            // Ignorar el token CSRF solo para esta respuesta
            $_SERVER['disable_csrf'] = true;

            return redirect()->intended(route('home'));

        } catch (Exception $e) {
            Log::error('SAML ACS Error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Error procesando la autenticación.');
        }
    }


    /*public function acs(Request $request): Application|Redirector|RedirectResponse
    {
        try {
            if (!$request->has('SAMLResponse')) {
                throw new Exception('No se recibió respuesta SAML');
            }

            $auth = $this->getSaml2Auth();
            $auth->processResponse();

            if (!$auth->isAuthenticated()) {
                throw new Exception('No autenticado después del SSO');
            }

            $attributes = $auth->getAttributes();
            $email = $auth->getNameId();

            if (!str_ends_with($email, '@vectorcr.com')) {
                throw new Exception('Solo se permite el acceso con correo de Vector');
            }

            $name = $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname'][0] ??
                explode('@', $email)[0];

            $user = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name]
            );

            Auth::login($user);

            // Regenerar la sesión después del login
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        } catch (Exception $e) {
            Log::error('SAML ACS Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'attributes' => $attributes ?? null
            ]);
            return redirect('/')->with('error', 'Error en la autenticación: ' . $e->getMessage());
        }
    }*/

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

            return response($metadata, 200, ['Content-Type' => 'text/xml']);
        } catch (Exception $e) {
            Log::error('SAML2 Metadata Error: ' . $e->getMessage());
            return response('Error generating metadata', 500);
        }
    }
}

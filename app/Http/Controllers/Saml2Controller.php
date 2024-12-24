<?php

namespace App\Http\Controllers;

use Aacotroneo\Saml2\Saml2Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;
use OneLogin\Saml2\Auth as OneLogin_Saml2_Auth;
use OneLogin\Saml2\Settings as OneLogin_Saml2_Settings;
use OneLogin\Saml2\Error;

class Saml2Controller extends Controller
{
    protected function getSaml2Auth()
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

    public function login()
    {
        try {
            Log::debug('Iniciando proceso de login SAML2');
            $auth = $this->getSaml2Auth();

            // Iniciar el proceso de login con parámetros simples
            $auth->login(route('home'));

            // No deberíamos llegar aquí ya que login() redirige
            return redirect()->route('home');
        } catch (Exception $e) {
            Log::error('SAML2 Login Error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Error iniciando sesión. Por favor intente de nuevo.');
        }
    }

    public function acs(Request $request)
    {
        try {
            Log::debug('Procesando respuesta ACS SAML', ['request' => $request->all()]);

            $auth = $this->getSaml2Auth();
            $auth->processResponse();

            if (!$auth->isAuthenticated()) {
                throw new Exception('No autenticado después del SSO');
            }

            $attributes = $auth->getAttributes();
            Log::debug('Atributos SAML recibidos', ['attributes' => $attributes]);

            $email = $auth->getNameId();
            if (!$email) {
                throw new Exception('No se pudo obtener el correo electrónico');
            }

            // Verificar dominio vectorcr.com
            if (!str_ends_with($email, '@vectorcr.com')) {
                throw new Exception('Solo se permite el acceso con correo de Vector');
            }

            // Obtener nombre del usuario de los atributos
            $name = $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'][0] ??
                $attributes['givenname'][0] ??
                explode('@', $email)[0];

            $user = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name]
            );

            Auth::login($user);
            Log::info("Usuario {$email} ha iniciado sesión exitosamente");

            return redirect()->intended(route('home'));
        } catch (Exception $e) {
            Log::error('SAML2 ACS Error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Error procesando la respuesta de autenticación.');
        }
    }

    public function logout()
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

    public function metadata()
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

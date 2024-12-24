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
    /**
     * @throws Exception
     */
    protected function getSaml2Auth(): Saml2Auth
    {
        return new Saml2Auth(Saml2Auth::loadOneLoginAuthFromIpdConfig('vectoradminapp'));
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

    public function acs(Request $request): Response
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
    }

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

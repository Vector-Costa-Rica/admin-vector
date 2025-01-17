<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OneLogin\Saml2\Auth as SamlAuth;
use OneLogin\Saml2\Error;
use OneLogin\Saml2\ValidationError;

class SamlController extends Controller
{
    /**
     * @throws Error
     */
    public function login(): ?string
    {
        $auth = new SamlAuth();
        return $auth->login();
    }

    /**
     * @throws ValidationError
     * @throws Error
     */
    public function acs(Request $request)
    {
        $auth = new SamlAuth();
        $auth->processResponse();

        // Verifica si la respuesta es válida
        if ($auth->isAuthenticated()) {
            // Obtener los atributos del usuario desde la respuesta SAML
            $userData = $auth->getAttributes();

            // Extraer los datos del usuario según los atributos que recibimos
            $user = [
                'name' => $userData['name'][0] ?? '',  // Aquí obtenemos el nombre completo (userprincipalname)
                'email' => $userData['emailaddress'][0] ?? '',  // Aquí obtenemos el correo (user.mail)
                'given_name' => $userData['givenname'][0] ?? '',  // Primer nombre
                'surname' => $userData['surname'][0] ?? '',  // Apellido
            ];

            // Verifica si el correo electrónico ya existe en la base de datos
            $existingUser = User::where('email', $user['email'])->first();

            // Si el usuario no existe, podemos crear uno nuevo
            if (!$existingUser) {
                $existingUser = User::create([
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'given_name' => $user['given_name'],
                    'surname' => $user['surname'],
                    'password' => bcrypt(str_random(16)),  // Genera una contraseña aleatoria si es necesario
                ]);
            }

            // Autenticar al usuario en Laravel
            Auth::login($existingUser);

            // Redirigir al dashboard (/home)
            return redirect()->route('home');
        } else {
            // Si la autenticación falla, redirige al login con un error
            return redirect()->route('welcome')->withErrors('La autenticación SAML falló');
        }
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();
        return redirect()->route('welcome');
    }
}

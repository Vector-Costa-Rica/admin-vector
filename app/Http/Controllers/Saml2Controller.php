<?php

namespace App\Http\Controllers;

use Aacotroneo\Saml2\Saml2Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Saml2Controller extends Controller
{
    public function login()
    {
        $saml2Auth = new Saml2Auth(Saml2Auth::loadOneLoginAuthFromIpdConfig('vectoradminapp'));
        return $saml2Auth->login(route('home'));
    }

    public function acs()
    {
        $saml2Auth = new Saml2Auth(Saml2Auth::loadOneLoginAuthFromIpdConfig('vectoradminapp'));
        $errors = $saml2Auth->acs();

        if (!empty($errors)) {
            logger()->error('Saml2 error: ' . implode(', ', $errors));
            return redirect('/')->with('error', 'No se pudo iniciar sesión.');
        }

        $messageId = $saml2Auth->getLastMessageId();
        $samlUser = $saml2Auth->getSaml2User();

        $email = $samlUser->getUserAttribute('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress')[0];

        // Verificar dominio vectorcr.com
        if (!str_ends_with($email, '@vectorcr.com')) {
            return redirect('/')->with('error', 'Solo se permite el acceso con correo de Vector.');
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $samlUser->getUserAttribute('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name')[0],
                'email' => $email,
            ]
        );

        Auth::login($user);

        return redirect()->intended(route('home'));
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $saml2Auth = new Saml2Auth(Saml2Auth::loadOneLoginAuthFromIpdConfig('vectoradminapp'));
        return $saml2Auth->logout(route('welcome'));
    }
}

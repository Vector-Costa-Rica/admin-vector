<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Error;
use OneLogin\Saml2\ValidationError;

class SAMLController extends Controller
{
    /**
     * @throws Error
     */
    public function login(Request $request)
    {
        Session::start();
        $auth = new Auth(config('php-saml'));
        $redirectUrl = $auth->login(null, [], false, false, true);
        $request->session()->put('requestId', $auth->getLastRequestID());
        return redirect($redirectUrl);
    }

    /**
     * @throws ValidationError
     * @throws Error
     */
    public function acs(Request $request): string
    {
        $auth = new Auth(config('php-saml'));
        $auth->processResponse($request->get('requestId'));

        if (count($auth->getErrors()) > 0 || !$auth->isAuthenticated()) {
            return 'An error occurred processing SAML response';
        }

        $user = User::query()->where('email', $auth->getNameId())->first();
        if (!$user) {
            return 'User not found.';
        }

        auth()->login($user);

        return redirect('/');
    }
}

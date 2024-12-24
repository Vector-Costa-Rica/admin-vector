<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'saml2/acs',
        'auth/saml2/callback',
        'auth/saml2/acs',
        'saml2/acs',
        'auth/saml2/*'
    ];

    protected function tokensMatch($request): bool
    {
        // Si está marcado para ignorar CSRF (caso SAML)
        if (isset($_SERVER['disable_csrf']) && $_SERVER['disable_csrf'] === true) {
            return true;
        }

        return parent::tokensMatch($request);
    }
}

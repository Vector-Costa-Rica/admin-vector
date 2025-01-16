<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Slides\Saml2\Events\SignedIn;
use Slides\Saml2\Events\SignedOut;
use App\Models\User;

class SAML2ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(SignedIn::class, function (SignedIn $event) {
            try {
                $messageId = $event->getAuth()->getLastMessageId();
                Log::debug('SAML Login Event', ['messageId' => $messageId]);

                $samlUser = $event->getSaml2User();
                $email = $samlUser->getUserId();

                if (!str_ends_with($email, '@vectorcr.com')) {
                    throw new \Exception('Solo se permite el acceso con correo de Vector');
                }

                $attributes = $samlUser->getAttributes();
                Log::debug('SAML User Attributes', ['attributes' => $attributes]);

                $name = $attributes['displayname'][0] ??
                    $attributes['http://schemas.microsoft.com/identity/claims/displayname'][0] ??
                    explode('@', $email)[0];

                $user = User::updateOrCreate(
                    ['email' => $email],
                    ['name' => $name]
                );

                Auth::login($user);

                Log::info('Usuario autenticado correctamente', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);

            } catch (\Exception $e) {
                Log::error('Error en SAML Login Event', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });

        Event::listen(SignedOut::class, function (SignedOut $event) {
            Auth::logout();
            session()->save();
            Log::info('Usuario cerró sesión');
        });
    }
}

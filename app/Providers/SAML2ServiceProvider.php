<?php

namespace App\Providers;

use Exception;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use App\Models\User;

class SAML2ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(Saml2LoginEvent::class, function (Saml2LoginEvent $event) {
            try {
                $messageId = $event->getSaml2Auth()->getLastMessageId();
                Log::debug('SAML Login Event', ['messageId' => $messageId]);

                $user = $event->getSaml2User();
                $email = $user->getUserId();

                if (!str_ends_with($email, '@vectorcr.com')) {
                    throw new Exception('Solo se permite el acceso con correo de Vector');
                }

                $attributes = $user->getAttributes();
                Log::debug('SAML User Attributes', ['attributes' => $attributes]);

                $name = $attributes['displayname'][0] ??
                    $attributes['http://schemas.microsoft.com/identity/claims/displayname'][0] ??
                    explode('@', $email)[0];

                $user = User::updateOrCreate(
                    ['email' => $email],
                    ['name' => $name]
                );

                Auth::login($user);

            } catch (Exception $e) {
                Log::error('Error en SAML Login Event', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }
}

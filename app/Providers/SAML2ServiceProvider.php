<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
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

                $samlUser = $event->getSaml2User();

                Log::debug('SAML User Attributes', [
                    'attributes' => $samlUser->getAttributes(),
                    'userId' => $samlUser->getUserId()
                ]);

                $email = $samlUser->getUserId();
                // Verificar que el email termine en @vectorcr.com
                if (!str_ends_with($email, '@vectorcr.com')) {
                    Log::warning('Intento de acceso con correo no autorizado', ['email' => $email]);
                    throw new \Exception('Solo se permite el acceso con correo de Vector');
                }

                // Obtener el nombre del usuario de los atributos SAML
                $name = $this->getValue($samlUser->getAttribute('displayname')) ??
                    $this->getValue($samlUser->getAttribute('http://schemas.microsoft.com/identity/claims/displayname')) ??
                    $this->getValue($samlUser->getAttribute('name')) ??
                    explode('@', $email)[0];

                // Crear o actualizar el usuario
                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'password' => Hash::make(str_random(32))
                    ]
                );

                Log::debug('Usuario creado/actualizado', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name
                ]);

                Auth::login($user);

                Log::info('Login SAML exitoso', [
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
    }

    protected function getValue($attribute)
    {
        return is_array($attribute) ? ($attribute[0] ?? null) : $attribute;
    }
}

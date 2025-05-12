<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\{ RedirectResponse, Request };
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bundle\SecurityBundle\Security;

class TwoFactorListener
{
    public function __construct(
        private Security $security,
        private RouterInterface $router,
    ) {}

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        // Excluir rutas públicas y la propia ruta de verificación
        $excludedRoutes = ['login', 'enable_2fa', 'verify_2fa', '_wdt', '_profiler'];

        if (\in_array($request->attributes->get('_route'), $excludedRoutes)) {
            return;
        }

        $user = $this->security->getUser();

        if ($user && method_exists($user, 'getGoogleAuthenticatorSecret')) {
            $session = $request->getSession();

            if ($user->getGoogleAuthenticatorSecret() && !$session->get('2fa_completed')) {
                $event->setResponse(new RedirectResponse($this->router->generate('verify_2fa')));
            }
        }
    }
}

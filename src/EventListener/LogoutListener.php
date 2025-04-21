<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener
{
    public function __construct(private EntityManagerInterface $em) {}

    public function __invoke(LogoutEvent $event): void
    {
        $user = $event->getToken()?->getUser();
        if ($user && method_exists($user, 'setIsOnline')) {
            $user->setIsOnline(false);
            $this->em->persist($user);
            $this->em->flush();
        }
    }
}

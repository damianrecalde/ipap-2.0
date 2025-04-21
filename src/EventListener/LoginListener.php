<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginListener
{
    public function __construct(private EntityManagerInterface $em) {}

    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        if ($user instanceof UserInterface) {
            $user->setIsOnline(true);
            $this->em->persist($user);
            $this->em->flush();
        }
    }
}

<?php
// src/Twig/AppExtension.php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Symfony\Bundle\SecurityBundle\Security;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getGlobals(): array
    {
        $user = $this->security->getUser();

        return [
            'imageProfile' => $user && method_exists($user, 'getImageProfile') ? $user->getImageProfile() : null,
        ];
    }
}

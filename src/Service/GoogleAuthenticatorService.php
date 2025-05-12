<?php

namespace App\Service;

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

class GoogleAuthenticatorService
{
    private $googleAuthenticator;

    public function __construct()
    {
        $this->googleAuthenticator = new GoogleAuthenticator();
    }

    // Generar el código secreto
    public function generateSecret(): string
    {
        return $this->googleAuthenticator->generateSecret();
    }

    // Obtener el URL del QR code
    public function getQRCodeUrl(string $username, string $secret): string
    {
        return $this->googleAuthenticator->getQRCodeUrl('NombreDeTuApp', $username, $secret);
    }

    // Verificar el código ingresado por el usuario
    public function checkCode(string $secret, string $code): bool
    {
        return $this->googleAuthenticator->checkCode($secret, $code);
    }
}

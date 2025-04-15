<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class CourseService
{
    private $client;
    private $moodleUrl;
    private $token;

    public function __construct(HttpClientInterface $client, string $moodleToken, string $moodleUrl)
    {
        $this->client = $client;
        $this->moodleUrl = $moodleUrl;
        $this->token = $moodleToken;
    }

    // Método para obtener la lista de cursos
    public function getCourses(): array
    {
        try {
            $response = $this->client->request('GET', $this->moodleUrl, [
                'query' => [
                    'wstoken' => $this->token,
                    'wsfunction' => 'core_course_get_courses',
                    'moodlewsrestformat' => 'json',
                ]
            ]);

            $data = $response->toArray();  // Convertir la respuesta a un array

            // Si la respuesta tiene un formato esperado
            return $data ?: [];
        } catch (TransportExceptionInterface $e) {
            // Manejar errores de la petición HTTP
            throw new \Exception("Error al conectarse a Moodle: " . $e->getMessage());
        }
    }
}

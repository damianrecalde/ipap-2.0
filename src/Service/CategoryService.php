<?php

namespace App\Service;

use App\Service\MoodleApiService;


class CategoryService
{
    private MoodleApiService $moodle;

    public function __construct(MoodleApiService $moodle)
    {
        $this->moodle = $moodle;
    }

    // Método para obtener la lista de cursoscategorías
    public function getCategory(): array
    {
        try {
            return $this->moodle->call('core_course_get_categories');

            $data = $response->toArray();  // Convertir la respuesta a un array

            // Si la respuesta tiene un formato esperado
            return $data ?: [];
        } catch (TransportExceptionInterface $e) {
            // Manejar errores de la petición HTTP
            throw new \Exception("Error al conectarse a Moodle: " . $e->getMessage());
        }
    }
}

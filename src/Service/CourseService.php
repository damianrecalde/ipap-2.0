<?php

namespace App\Service;

use App\Service\MoodleApiService;


class CourseService
{
    private MoodleApiService $moodle;

    public function __construct(MoodleApiService $moodle)
    {
        $this->moodle = $moodle;
    }

    // Método para obtener la lista de cursos
    public function getCourses(): array
    {
        try {
            return $this->moodle->call('core_course_get_courses');

            $data = $response->toArray();  // Convertir la respuesta a un array

            // Si la respuesta tiene un formato esperado
            return $data ?: [];
        } catch (TransportExceptionInterface $e) {
            // Manejar errores de la petición HTTP
            throw new \Exception("Error al conectarse a Moodle: " . $e->getMessage());
        }
    }
}

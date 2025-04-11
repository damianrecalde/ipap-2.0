<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\CourseService;

final class CourseController extends AbstractController
{
    #[Route('/course', name: 'course')]
    public function index(CourseService $courseService): Response
    {
        $page_title = 'Listado de cursos';
        $course = $courseService->listarCursos();

        return $this->render('course/index.html.twig', [
            'name' => 'Cursos',
            'page_title' => $page_title,
            'course' => $course,
        ]);
    }
}

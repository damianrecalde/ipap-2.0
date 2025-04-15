<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\CourseService;

final class CourseController extends AbstractController
{
    private CourseService $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    #[Route('/course', name: 'course')]
    public function index(): Response
    {
        $page_title = 'Listado de cursos';
        $courses = $this->courseService->getCourses();

        return $this->render('course/index.html.twig', [
            'name' => 'Cursos',
            'page_title' => $page_title,
            'course' => $courses,
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Response, Request };
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Course;
use App\Repository\CourseRepository;

final class CourseController extends AbstractController
{
    #[Route('/course', name: 'course')]
    public function index(CourseRepository $courseRepository, Request $request): Response
    {
        $page_title = 'Listado de cursos en campus';
        $limit = 10;
        $page = $request->query->getInt('page', 1);
        $offset = ($page - 1) * $limit;

        $allCourses = $courseRepository->findAllCourses();
        $totalCourses = count($allCourses);

        $totalPages = ceil(count($allCourses) / $limit);

        return $this->render('course/index.html.twig', [
            'page_title' => $page_title,
            'allCourses' => $allCourses,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'totalCourses' => $totalCourses,
        ]);
    }
}

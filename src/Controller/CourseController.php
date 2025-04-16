<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\{ CourseService, CategoryService };

final class CourseController extends AbstractController
{
    private CourseService $courseService;
    private CategoryService $categoryService;

    public function __construct(CourseService $courseService, CategoryService $categoryService)
    {
        $this->courseService = $courseService;
        $this->categoryService = $categoryService;
    }

    #[Route('/course', name: 'course')]
    public function index(): Response
    {
        $page_title = 'Listado de cursos';
        $courses = $this->courseService->getCourses();
        $categories = $this->categoryService->getCategory();
        $categoryMap = [];
        foreach($categories as $category) {
            $categoryMap[$category['id']] = $category['name'];
        }

        return $this->render('course/index.html.twig', [
            'name' => 'Cursos',
            'page_title' => $page_title,
            'course' => $courses,
            'categoryMap' => $categoryMap,
        ]);
    }
}

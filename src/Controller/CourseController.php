<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CourseController extends AbstractController
{
    #[Route('/course', name: 'course')]
    public function index(): Response
    {
        return $this->render('course/index.html.twig', [
            'name' => 'Cursos',
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Workshop;
use App\Repository\WorkshopRepository;

final class WorkshopController extends AbstractController
{
    #[Route('/workshop', name: 'workshop')]
    public function index(WorkshopRepository $workshopRepository): Response
    {
         // Obtener todas las capacitaciones de la base de datos
         $workshops = $workshopRepository->findAll();

         // Renderizar la vista y pasar los talleres a la plantilla
         return $this->render('workshop/index.html.twig', [
             'page_title' => 'Capacitaciones',
             'workshops' => $workshops,
         ]);
    }
}

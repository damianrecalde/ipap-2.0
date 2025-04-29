<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Response, Request };
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CityRepository;
use App\Entity\City;
use App\Form\CityFormType;
use Doctrine\ORM\EntityManagerInterface;


final class CityController extends AbstractController
{
    #[Route('/city', name: 'city', methods:'GET')]
    public function index(Request $request, CityRepository $cityRepository): Response
    {
        $page_title = 'Listado de Localidades';
        $limit = 10;
        $page = $request->query->getInt('page', 1);
        $offset = ($page -1) * $limit;
        $allCity = $cityRepository->findAll();
        $totalCities = count ($allCity);
        $totalPages = ceil(count($allCity) / $limit);
        return $this->render('city/index.html.twig', [
            'allCities' => $allCity,
            'page_title' => $page_title,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'totalCities' => $totalCities,
        ]);
    }

    #[ROUTE('/city_new', name:'city_new', methods:['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em):response
    {
        $page_title = 'Nueva localidad';
        $city = new City();
        $form = $this->createForm(CityFormType::class, $city);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($city);
            $em->flush();

            return $this->redirectToRoute('city');
        }

        return $this->render('city/new.html.twig', [
            'cityForm' => $form->createView(),
            'page_title' => $page_title,
        ]);
    }

    #[ROUTE('city_edit/{id}', name:'city_edit', methods:['GET', 'POST'])]
    public function edit(int $id, Request $request, CityRepository $cityRepository, EntityManagerInterface $em): Response
    {

        $page_title = 'Editar localidad';
        $city = $cityRepository->find($id);

        if (!$city) {
            throw $this->createNotFoundException('Localidad no encontrada');
        }

        $form = $this->createForm(CityFormType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('city');
        }

        return $this->render('city/edit.html.twig', [
            'cityForm' => $form->createView(),
            'city' => $city,
            'page_title' => $page_title,
        ]);
    }

    #[Route('/city_delete/{id}', name: 'city_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, CityRepository $cityRepository, EntityManagerInterface $em): Response
    {
        $city = $cityRepository->find($id);

        if (!$city) {
            throw $this->createNotFoundException('Localidad no encontrada');
        }

        if ($this->isCsrfTokenValid('delete_city_' . $city->getId(), $request->request->get('_token'))) {
            $em->remove($city);
            $em->flush();
        }

        return $this->redirectToRoute('city');
    }
}

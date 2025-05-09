<?php

namespace App\Controller;

use App\Entity\WorkTeam;
use App\Form\WorkTeamFormType;
use App\Repository\WorkTeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response };
use Symfony\Component\Routing\Attribute\Route;

#[Route('/work/team')]
final class WorkTeamController extends AbstractController
{
    #[Route(name: 'work_team', methods: ['GET'])]
    public function index(WorkTeamRepository $workTeamRepository, Request $request): Response
    {
        $page_title = 'Listado de Equipos de Trabajo';
        $limit = 10;
        $page = $request->query->getInt('page', 1);
        $offset = ($page -1) * $limit;
        $allWorkTeam = $workTeamRepository->findAll();
        $totalWorkTeams = count ($allWorkTeam);
        $totalPages = ceil(count($allWorkTeam) / $limit);

        return $this->render('work_team/index.html.twig', [
            'work_teams' => $allWorkTeam,
            'page_title' => $page_title,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'totalWorkTeams' => $totalWorkTeams,
        ]);
    }

    #[Route('/new', name: 'work_team_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $page_title = 'Crear nuevo equipo de trabajo';
        $workTeam = new WorkTeam();
        $form = $this->createForm(WorkTeamFormType::class, $workTeam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($workTeam);
            $entityManager->flush();

            return $this->redirectToRoute('work_team', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('work_team/new.html.twig', [
            'work_team' => $workTeam,
            'workTeamForm' => $form,
            'page_title' => $page_title,
        ]);
    }

    #[Route('/{id}', name: 'work_team_show', methods: ['GET'])]
    public function show(WorkTeam $workTeam): Response
    {
        return $this->render('work_team/show.html.twig', [
            'work_team' => $workTeam,
        ]);
    }

    #[Route('/{id}/edit', name: 'work_team_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, WorkTeam $workTeam, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WorkTeamType::class, $workTeam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_work_team_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('work_team/edit.html.twig', [
            'work_team' => $workTeam,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'work_team_delete', methods: ['POST'])]
    public function delete(Request $request, WorkTeam $workTeam, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$workTeam->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($workTeam);
            $entityManager->flush();
        }

        return $this->redirectToRoute('work_team', [], Response::HTTP_SEE_OTHER);
    }
}

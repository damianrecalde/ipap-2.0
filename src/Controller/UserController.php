<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Response, Request };
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\{User, City};
use App\Repository\UserRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Form\RegistrationFormType;

final class UserController extends AbstractController
{
    //Listando todos los usuarios
    #[Route('/user', name: 'user')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $page_title = 'Listado de usuarios';
        $limit = 10;
        $page = $request->query->getInt('page', 1);
        $offset = ($page - 1) * $limit;

        $allUsers = $userRepository->findAll();
        $totalUSers = count($allUsers);
        $onlineUsers = $userRepository->findOnlineUsers();

        $totalPages = ceil(count($allUsers) / $limit);

        return $this->render('user/index.html.twig', [
            'allUsers' => $allUsers,
            'onlineUsers' => $onlineUsers,
            'page_title' => $page_title,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'totalUsers' => $totalUSers,
        ]);
    }

    //Editando el perfil del usuario
    #[Route('/profile/{id}/edit', name:'edit_profile')]
    public function edit(User $user, Request $request, EntityManager $em, Security $security): Response
    {
        $page_title = 'Editar perfil de usuario';
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $currentUser = $security->getUser();
        if (!$this->isGranted('role_admin') && $currentUser !== $user) {
            throw $this->createAccessDeniedException('No tienes permiso para editar este perfil.');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'page_title' => $page_title,
        ]);
    }

    //Detalle del perfil del usuario
    #[Route('/profile', name: 'user_profile')]
    public function profile(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Debes iniciar sesión para acceder a tu perfil.');
        }

        $page_title = 'Mi perfil';
        $isEditMode = $request->query->getBoolean('editar');

        $form = $this->createForm(RegistrationFormType::class, $user, [
            'disabled' => !$isEditMode,
        ]);
        $form->handleRequest($request);

        if ($isEditMode && $form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageProfile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('profile_images_directory'),
                    $newFilename
                );

                $user->setImageProfile($newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Perfil actualizado correctamente.');
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/profile.html.twig', [
            'registrationForm' => $form->createView(),
            'page_title' => $page_title,
        ]);
    }


    //Suspender un usuario
    #[Route('/user/{id}/suspend', name: 'suspend_user')]
    public function suspendUser(User $user, EntityManagerInterface $em): Response
    {
        $this->denyAccesUnlesGranted('ROLE_ADMIN');
        $user->setIsSupended(true);
        $em->flush();
    }

    //Activar un usuario
    #[Route('/user/{id}/activate', name: 'activate_user')]
    public function activateUser(User $user, EntityManagerInterface $em): Response
    {
        $this->denyAccesUnlesGranted('ROLE_ADMIN');
        $user->setIsSuspended(false);
        $em->flush();
    }

    // Método para eliminar lógicamente a un usuario
    #[Route('/user/{id}/delete', name: 'user_delete')]
    public function delete(User $user, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user->setIsDeleted(true); // O si prefieres usar un campo isDeleted
        $em->flush();

    }

}
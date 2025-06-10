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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\GoogleAuthenticatorService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



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

    #[Route('/user/edit/{id}', name: 'edit_profile')]
    public function edit(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger): Response
    {
        $page = 'Editar perfil';
        $tableTitle = 'Editar perfil de usuario';
        
        // Solo el usuario logueado o un administrador puede editar
        if ($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('No tienes permiso para editar este perfil.');
        }

        // Crear y procesar formulario
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'is_edit' => true,  // Indicamos que estamos editando un usuario existente
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Manejar cambio de contraseña
            $oldPassword = $form->get('oldPassword')->getData();
            $newPassword = $form->get('plainPassword')->getData();

            if ($oldPassword && $newPassword) {
                if ($passwordHasher->isPasswordValid($user, $oldPassword)) {
                    $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                } else {
                    $this->addFlash('danger', 'La contraseña actual es incorrecta.');
                    return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
                }
            }

            // Manejar subida de imagen
            $imageFile = $form->get('imageProfile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'), 
                        $newFilename
                    );
                    $user->setProfileImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error al subir la imagen.');
                }
            }

            // Guardar cambios
            $entityManager->flush();
            $this->addFlash('success', 'Perfil actualizado correctamente.');

            return $this->redirectToRoute('user_profile_view', ['id' => $user->getId()]);
        }

        // Renderizar vista
        return $this->render('user/edit.html.twig', [
            'editForm' => $form->createView(),
            'user' => $user,
            'page_title' => $page,
            'tableTitle' => $tableTitle,
            'template_name' => 'Editar mi perfil',
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
            'user' => $user,
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

    // src/Controller/UserController.php

    #[Route('/perfil/habilitar-2fa', name: 'enable_2fa')]
    public function enable2FA(GoogleAuthenticatorService $googleAuthenticatorService): Response
    {
        $user = $this->getUser();

        if ($user->is2faEnabled()) {
            // Si 2FA ya está habilitado, lo deshabilitamos
            $user->setGoogleAuthenticatorSecret(null); // Eliminar el secreto
            $this->addFlash('success', 'La autenticación en dos pasos ha sido deshabilitada.');
        } else {
            // Si 2FA no está habilitado, lo habilitamos
            $secret = $googleAuthenticatorService->generateSecret();
            $user->setGoogleAuthenticatorSecret($secret);

            $this->addFlash('success', 'La autenticación en dos pasos ha sido habilitada.');
        }

        $this->getDoctrine()->getManager()->flush();

        // Mostrar el código QR si es que se habilitó 2FA
        $qrCodeUrl = $user->is2faEnabled() ? $googleAuthenticatorService->getQRCodeUrl($user->getEmail(), $user->getGoogleAuthenticatorSecret()) : null;

        return $this->render('user/enable_2fa.html.twig', [
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    #[Route('/perfil/verificar-2fa', name: 'verify_2fa')]
    public function verify2FA(Request $request, GoogleAuthenticatorService $googleAuthenticatorService): Response
    {
        $user = $this->getUser();
        
        // Verificar que el usuario tiene un secreto de Google Authenticator asignado
        $secret = $user->getGoogleAuthenticatorSecret();
        
        if (empty($secret)) {
            // Si el usuario no tiene un secreto, significa que 2FA no está habilitado, por lo que no se puede verificar el código
            $this->addFlash('error', 'Autenticación en dos pasos no está habilitada para este usuario.');
            return $this->redirectToRoute('user_profile'); // Cambia esto por la ruta correcta de tu perfil
        }

        $code = $request->request->get('auth_code');

        // Verificar el código solo si el secreto está presente
        $isCodeValid = $googleAuthenticatorService->checkCode($secret, $code);

        if ($isCodeValid) {
            $this->addFlash('success', 'Autenticación en dos pasos habilitada con éxito.');
            // Podés guardar un flag como $user->setIs2FAEnabled(true); si querés
        } else {
            $this->addFlash('error', 'Código inválido, intentá de nuevo.');
        }

        return $this->redirectToRoute('user_profile'); // Cambia esto por la ruta correcta de tu perfil
    }



    #[Route('/perfil/activar-desactivar-2fa/{id}', name: 'toggle_2fa', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')] // Solo los administradores pueden alternar el 2FA
    public function toggle2FA(
        int $id,
        Request $request,
        UserRepository $userRepository,
        GoogleAuthenticatorService $googleAuthenticatorService
        ): Response {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('El usuario no existe');
        }

        // Validar el token CSRF
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('toggle_2fa_' . $user->getId(), $submittedToken)) {
            throw $this->createAccessDeniedException('Token CSRF inválido.');
        }

        // Activar o desactivar 2FA
        if ($user->getGoogleAuthenticatorSecret()) {
            // Desactivar 2FA
            $user->setGoogleAuthenticatorSecret(null);
            $user->setGoogleAuthenticatorEnabled(false);
            $this->addFlash('success', 'Autenticación en dos pasos desactivada correctamente.');
        } else {
            // Activar 2FA
            $secret = $googleAuthenticatorService->generateSecret();
            $user->setGoogleAuthenticatorSecret($secret);
            $user->setGoogleAuthenticatorEnabled(true);
            $this->addFlash('success', 'Autenticación en dos pasos activada correctamente.');
        }

        $this->getDoctrine()->getManager()->flush();

        // Redirigir a la página de edición del usuario
        return $this->redirectToRoute('edit_profile', ['id' => $user->getId()]);
    }

}
<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response };
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier, private SluggerInterface $slugger)
    {
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $page_title = 'Registro de usuario';
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Imagen de perfil
            /** @var UploadedFile $imageProfile */
            $imageProfile = $form->get('imageProfile')->getData();  // Asegúrate de obtener la imagen desde el formulario

            if ($imageProfile) {
                // Obtener el nombre original del archivo
                $originalFileName = pathinfo($imageProfile->getClientOriginalName(), PATHINFO_FILENAME);

                // Crear un nombre seguro para el archivo
                $safeFileName = $this->slugger->slug($originalFileName);

                // Crear un nombre único para evitar colisiones
                $newFileName = $safeFileName.'-'.uniqid().'.'.$imageProfile->guessExtension();

                try {
                    // Mover el archivo a la carpeta de destino
                    $imageProfile->move(
                        $this->getParameter('profile_images_directory'),
                        $newFileName
                    );

                    // Guardar el nombre del archivo en el objeto User
                    $user->setImageProfile($newFileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error al subir la imagen de perfil.');
                }
            }

            // Persistir el usuario
            $entityManager->persist($user);
            $entityManager->flush();

            // Enviar el correo de verificación de email
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('frecalde.ipap@gmail.com', 'IPAP Tierra del Fuego A.eI.A.S.'))
                    ->to((string) $user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // Redirigir al dashboard después del registro
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'page_title' => $page_title,
        ]);
    }


    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\LoginAuthenticator;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        LoginAuthenticator $loginAuthenticator
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setType(0);

            // Gestion de l'upload de l'image de profil
            $ppImgFile = $form->get('pp_img')->getData();
            if ($ppImgFile) {
                $newFilename = uniqid() . '.' . $ppImgFile->guessExtension();

                try {
                    $ppImgFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/pp',
                        $newFilename
                    );
                    $user->setPpImg($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_register');
                }
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a été créé avec succès !');

            // Authentifier l'utilisateur après l'inscription
            return $userAuthenticator->authenticateUser(
                $user,
                $loginAuthenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}

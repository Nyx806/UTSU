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

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setType(0);

             // Gestion de l'upload de l'image de profil
            $ppImgFile = $form->get('pp_img')->getData();
            if ($ppImgFile) {
                $newFilename = uniqid() . '.' . $ppImgFile->guessExtension();

                // Déplacement du fichier dans le répertoire public/uploads/pp
                try {
                    $ppImgFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/pp',
                        $newFilename
                    );
                    $user->setPpImg('uploads/pp/' . $newFilename); // Sauvegarde le chemin relatif dans la base
                } catch (FileException $e) {
                    // Gérer l'erreur si l'upload échoue (par exemple, afficher un message d'erreur)
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_register');
                }
            }

            
            $entityManager->persist($user);
            $entityManager->flush();

            $this ->addFlash('success', 'Votre compte a été créé avec succès !');

            return $this->redirectToRoute('home_index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}

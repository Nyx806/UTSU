<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\AccountFormType;

#[Route('/account', name: 'account_')]
final class AccountController extends AbstractController {

  #[Route('/', name: 'index')]
  public function index(
    UserRepository $userRepository,
    Request $request,
    EntityManagerInterface $em,
  ): Response {
    // Récupérer l'utilisateur connecté.
    $user = $this->getUser();

    // Vérifier si un utilisateur est connecté.
    if (!$user) {
      // Rediriger vers la page de connexion si non connecté.
      return $this->redirectToRoute('app_login');
    }

    // Créer le formulaire de compte.
    $accountForm = $this->createForm(AccountFormType::class, $user);
    $accountForm->handleRequest($request);
    if ($accountForm->isSubmitted() && $accountForm->isValid()) {
      // Récupérer les données du formulaire.
      $data = $accountForm->getData();

      // Vérifier si une nouvelle image de profil a été téléchargée.
      $file = $accountForm->get('pp_img')->getData();
      if ($file) {
        // Générer un nom de fichier unique.
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        // Déplacer le fichier vers le répertoire public/uploads/pp.
        $file->move($this->getParameter('kernel.project_dir') . '/public/uploads/pp', $fileName);
        // Mettre à jour l'image de profil de l'utilisateur.
        $data->setPpImg($fileName);
      }

      // Mettre à jour les informations de l'utilisateur.
      $em->persist($data);
      $em->flush();

      // Rediriger vers la page d'accueil ou afficher un message de succès.
      return $this->redirectToRoute('home_index');
    }
    return $this->render(
          'account/index.html.twig',
          [
            'controller_name' => 'AccountController',
    // Passer l'utilisateur connecté à la vue.
            'user' => $user,
            'accountForm' => $accountForm->createView(),
          ]
      );
  }

}

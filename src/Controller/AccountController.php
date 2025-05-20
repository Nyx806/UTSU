<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/account', name: 'account_')]
final class AccountController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UserRepository $userRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si un utilisateur est connecté
        if (!$user) {
            return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion si non connecté
        }

        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
            'user' => $user, // Passer l'utilisateur connecté à la vue
        ]);
    }
}

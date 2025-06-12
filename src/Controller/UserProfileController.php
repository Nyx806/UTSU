<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserProfileController extends AbstractController
{

    #[Route('/{id}', name: 'user_profile')]
    public function show(User $user, PostsRepository $postsRepository): Response
    {
      // Récupérer les posts de l'utilisateur triés par dangerosité.
        $posts = $postsRepository->findBy(
            ['userID' => $user],
            ['dangerous' => 'DESC', 'date' => 'DESC']
        );

      // Calculer les statistiques.
        $stats = [
        'posts' => count($posts),
        'followers' => $user->getFollowers()->count(),
        'following' => $user->getAbonnements()->count(),
        'dangerous' => $user->getDangerous(),
        ];

        return $this->render('user/profile.html.twig', [
        'user' => $user,
        'posts' => $posts,
        'stats' => $stats,
        ]);
    }
}

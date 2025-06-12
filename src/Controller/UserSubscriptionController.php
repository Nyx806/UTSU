<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/user')]
class UserSubscriptionController extends AbstractController
{

    #[Route('/{id}/toggle-subscription', name: 'api_user_toggle_subscription', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function toggleSubscription(User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $currentUser = $this->getUser();

      // Vérifier si l'utilisateur essaie de s'abonner à lui-même.
        if ($currentUser === $user) {
            return $this->json(
                ['error' => 'Vous ne pouvez pas vous abonner à vous-même'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $abonnementRepository = $entityManager->getRepository(Abonnement::class);

      // Vérifier si l'utilisateur est déjà abonné.
        $existingAbonnement = $abonnementRepository->findOneBy(
            [
            'userID' => $currentUser,
            'followedUser' => $user,
            ]
        );

        if ($existingAbonnement) {
          // Désabonner.
            $entityManager->remove($existingAbonnement);
            $subscribed = false;
        } else {
          // Abonner.
            $abonnement = new Abonnement();
            $abonnement->setUserID($currentUser);
            $abonnement->setFollowedUser($user);
            $entityManager->persist($abonnement);
            $subscribed = true;
        }

        $entityManager->flush();

        return $this->json(
            [
            'subscribed' => $subscribed,
            'followersCount' => $user->getFollowers()->count(),
            ]
        );
    }
}

<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\Categories;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/category')]
class CategorySubscriptionController extends AbstractController
{
    #[Route('/{id}/toggle-subscription', name: 'api_category_toggle_subscription', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function toggleSubscription(Categories $category, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        $abonnementRepository = $entityManager->getRepository(Abonnement::class);
        
        // Vérifier si l'utilisateur est déjà abonné
        $existingAbonnement = $abonnementRepository->findOneBy(
            [
            'userID' => $user,
            'category' => $category
            ]
        );

        if ($existingAbonnement) {
            // Désabonner
            $entityManager->remove($existingAbonnement);
            $subscribed = false;
        } else {
            // Abonner
            $abonnement = new Abonnement();
            $abonnement->setUserID($user);
            $abonnement->setCategory($category);
            $entityManager->persist($abonnement);
            $subscribed = true;
        }

        $entityManager->flush();

        return $this->json(
            [
            'subscribed' => $subscribed
            ]
        );
    }
}

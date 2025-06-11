<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategoriesRepository;
use App\Repository\AbonnementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/categories', name: 'categories_')]
final class CategoriesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categoriesController): Response
    {
        return $this->render(
            'categories/index.html.twig',
            [
            'controller_name' => 'categories',
            'categories' => $categoriesController->findBy([], ['id' => 'ASC']),
            ]
        );
    }

    #[Route('/{id}', name: 'posts')]
    public function showByCategorie(int $id, CategoriesRepository $catRepo, PostsRepository $postRepo): Response
    {
        $categorie = $catRepo->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        $posts = $postRepo->findBy(['cat' => $categorie], ['id' => 'DESC']);

        return $this->render(
            'categories/show.html.twig',
            [
            'categorie' => $categorie,
            'posts' => $posts,
            ]
        );
    }

    #[Route('/{id}/toggle-subscription', name: 'toggle_subscription', methods: ['POST'])]
    public function toggleSubscription(
        int $id,
        CategoriesRepository $catRepo,
        AbonnementRepository $abonnementRepo,
        EntityManagerInterface $em
    ): Response {
        $category = $catRepo->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message' => 'Vous devez être connecté'], Response::HTTP_UNAUTHORIZED);
        }

        // Vérifier si l'utilisateur est déjà abonné
        $existingSubscription = $abonnementRepo->findOneBy(
            [
            'userID' => $user,
            'category' => $category
            ]
        );

        if ($existingSubscription) {
            // Désabonner
            $em->remove($existingSubscription);
            $message = 'Désabonné avec succès';
            $isSubscribed = false;
        } else {
            // Abonner
            $subscription = new \App\Entity\Abonnement();
            $subscription->setUserID($user);
            $subscription->setCategory($category);
            $em->persist($subscription);
            $message = 'Abonné avec succès';
            $isSubscribed = true;
        }

        $em->flush();

        return $this->json(
            [
            'message' => $message,
            'isSubscribed' => $isSubscribed
            ]
        );
    }
}

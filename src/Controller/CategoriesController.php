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
use App\Entity\Categories;

#[Route('/categories', name: 'categories_')]
final class CategoriesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, CategoriesRepository $categoriesController): Response
    {
        $search = $request->query->get('search', '');
        $page = max(1, (int) $request->query->get('page', 1));
        $zone = $request->query->get('zone', ''); // Get the zone parameter
        $limit = 9; // Number of categories per page

        $queryBuilder = $categoriesController->createQueryBuilder('c');

        if ($search) {
            $queryBuilder
                ->andWhere('c.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Add filtering based on the zone
        if ($zone) {
            switch ($zone) {
                case 'safe':
                    $queryBuilder->andWhere('c.dangerous >= :safeThreshold')
                        ->setParameter('safeThreshold', 500);
                    break;
                case 'moderate':
                    $queryBuilder->andWhere('c.dangerous > :minModerate AND c.dangerous < :maxModerate')
                        ->setParameter('minModerate', 0)
                        ->setParameter('maxModerate', 500);
                    break;
                case 'danger':
                    $queryBuilder->andWhere('c.dangerous <= :dangerThreshold')
                        ->setParameter('dangerThreshold', 0);
                    break;
            }
        }

        $queryBuilder->orderBy('c.id', 'ASC');

        $totalCategories = count($queryBuilder->getQuery()->getResult());
        $totalPages = ceil($totalCategories / $limit);

        $queryBuilder
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $categories = $queryBuilder->getQuery()->getResult();
        $mostDangerousCategories = $categoriesController->findBy([], ['dangerous' => 'DESC'], 3);

        return $this->render(
            'categories/index.html.twig',
            [
                'controller_name' => 'categories',
                'categories' => $categories,
                'mostDangerousCategories' => $mostDangerousCategories,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'search' => $search,
                'zone' => $zone // Pass the zone parameter to the template
            ]
        );
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour créer une catégorie');
        }

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');

            if (empty($name)) {
                $this->addFlash('error', 'Le nom de la catégorie ne peut pas être vide');
                return $this->redirectToRoute('categories_new');
            }

            $category = new Categories();
            $category->setName($name);
            $category->setDangerous(0);

            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Catégorie créée avec succès');
            return $this->redirectToRoute('categories_index');
        }

        return $this->render('categories/new.html.twig');
    }

    #[Route('/show/{id}', name: 'posts')]
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

    #[Route('/{id}/join', name: 'join', methods: ['POST'])]
    public function join(int $id, CategoriesRepository $catRepo, AbonnementRepository $abonnementRepo, EntityManagerInterface $em): Response
    {
        // Validate that id is a positive integer
        if ($id <= 0) {
            throw $this->createNotFoundException('Invalid category ID');
        }

        $category = $catRepo->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message' => 'You must be logged in to join a category'], Response::HTTP_UNAUTHORIZED);
        }

        // Check if user is already subscribed
        $existingSubscription = $abonnementRepo->findOneBy([
            'userID' => $user,
            'category' => $category
        ]);

        if ($existingSubscription) {
            // Unsubscribe
            $em->remove($existingSubscription);
            $message = 'Successfully unsubscribed';
            $isSubscribed = false;
        } else {
            // Subscribe
            $subscription = new \App\Entity\Abonnement();
            $subscription->setUserID($user);
            $subscription->setCategory($category);
            $em->persist($subscription);
            $message = 'Successfully subscribed';
            $isSubscribed = true;
        }

        $em->flush();

        return $this->json([
            'message' => $message,
            'isSubscribed' => $isSubscribed
        ]);
    }
}

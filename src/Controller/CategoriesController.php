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
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Abonnement;

#[Route('/categories', name: 'categories_')]
final class CategoriesController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(
        CategoriesRepository $categoriesController,
        Request $request,
        AbonnementRepository $abonnementRepository
    ): Response {
        $search = $request->query->get('search', '');
        $zone = $request->query->get('zone', '');
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 5;

      // Base query builder for total categories, including search filter.
        $baseQueryBuilder = $categoriesController->createQueryBuilder('c');
        if ($search) {
            $baseQueryBuilder->andWhere('LOWER(c.name) LIKE LOWER(:search)')
            ->setParameter('search', '%' . $search . '%');
        }
        $totalFilteredBySearchCount = count($baseQueryBuilder->getQuery()->getResult());

      // Clone the base query builder for further filtering by zone.
        $queryBuilder = clone $baseQueryBuilder;

        if ($zone) {
            switch ($zone) {
                case 'safe':
                    $queryBuilder->andWhere('c.dangerous >= :safeThreshold')
                    ->setParameter('safeThreshold', 500);
                    break;

                case 'moderate':
                    $queryBuilder->andWhere('c.dangerous >= :minModerate AND c.dangerous < :maxModerate')
                    ->setParameter('minModerate', 0)
                    ->setParameter('maxModerate', 500);
                    break;

                case 'danger':
                    $queryBuilder->andWhere('c.dangerous < :dangerThreshold')
                    ->setParameter('dangerThreshold', 0);
                    break;
            }
        }

        $totalCategories = count($queryBuilder->getQuery()->getResult());
        $totalPages = ceil($totalCategories / $limit);

        $categories = $queryBuilder
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();

        $mostDangerousCategories = $categoriesController->findBy([], ['dangerous' => 'DESC'], 3);

        $userSubscriptions = [];
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            $userSubscriptions = $abonnementRepository->findBy(['userID' => $user]);
        }

        return $this->render(
            'categories/index.html.twig',
            [
            'controller_name' => 'categories',
            'categories' => $categories,
            'mostDangerousCategories' => $mostDangerousCategories,
            'search' => $search,
            'zone' => $zone,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalAllCategoriesCount' => $totalFilteredBySearchCount,
            'userSubscriptions' => $userSubscriptions ?? [],
            ]
        );
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException(
                'Vous devez être connecté pour créer une catégorie'
            );
        }

      // Liste des icônes disponibles.
        $availableIcons = [
        'fa-folder' => 'Dossier',
        'fa-comments' => 'Discussion',
        'fa-music' => 'Musique',
        'fa-gamepad' => 'Jeux vidéo',
        'fa-microchip' => 'Technologie',
        'fa-book' => 'Littérature',
        'fa-film' => 'Cinéma',
        'fa-camera' => 'Photographie',
        'fa-code' => 'Programmation',
        'fa-palette' => 'Art',
        'fa-utensils' => 'Cuisine',
        'fa-futbol' => 'Sport',
        ];

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $icon = $request->request->get('icon');

            if (empty($name)) {
                $this->addFlash('error', 'Le nom de la catégorie ne peut pas être vide');
                return $this->redirectToRoute('categories_new');
            }

            $category = new Categories();
            $category->setName($name);
            $category->setDescription($description);
            $category->setIcon($icon);
            $category->setDangerous(0);

            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Catégorie créée avec succès');
            return $this->redirectToRoute('categories_index');
        }

        return $this->render('categories/new.html.twig', [
        'availableIcons' => $availableIcons,
        ]);
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
        CategoriesRepository $categoriesRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $category = $categoriesRepository->find($id);
        if (!$category) {
            throw $this->createNotFoundException(
                'Category not found'
            );
        }

        $user = $this->getUser();
        if (!$user) {
            return $this->json([
                'error' => 'You must be logged in to join a category'
            ], Response::HTTP_UNAUTHORIZED);
        }

      // Utilisation du QueryBuilder pour éviter le warning SQL injection.
        $qb = $entityManager->createQueryBuilder();
        $qb->select('a')
            ->from(Abonnement::class, 'a')
            ->where('a.userID = :userId')
            ->andWhere('a.category = :categoryId')
            ->setParameter('userId', $user)
            ->setParameter('categoryId', $category);
        $existingAbonnement = $qb->getQuery()->getOneOrNullResult();

        if ($existingAbonnement) {
            $entityManager->remove($existingAbonnement);
            $isSubscribed = false;
        } else {
            $abonnement = new Abonnement();
            $abonnement->setUserID($user);
            $abonnement->setCategory($category);
            $entityManager->persist($abonnement);
            $isSubscribed = true;
        }

        $entityManager->flush();

        return $this->json([
        'isSubscribed' => $isSubscribed,
        ]);
    }
}

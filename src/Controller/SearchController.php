<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use App\Repository\CommentairesRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/search')]
class SearchController extends AbstractController {

  #[Route('/', name: 'app_search', methods: ['GET'])]
  public function search(
    Request $request,
    PostsRepository $postsRepository,
    CommentairesRepository $commentairesRepository,
    UserRepository $userRepository,
  ): Response {
    $query = $request->query->get('q', '');
    $type = $request->query->get('type', 'all');

    $results = [
      'posts' => [],
      'comments' => [],
      'users' => [],
    ];

    if (!empty($query)) {
      switch ($type) {
        case 'posts':
          $results['posts'] = $postsRepository->search($query);
          break;

        case 'comments':
          $results['comments'] = $commentairesRepository->search($query);
          break;

        case 'users':
          $results['users'] = $userRepository->search($query);
          break;

        default:
          $results['posts'] = $postsRepository->search($query);
          $results['comments'] = $commentairesRepository->search($query);
          $results['users'] = $userRepository->search($query);
      }
    }

    return $this->render('search/index.html.twig', [
      'query' => $query,
      'type' => $type,
      'results' => $results,
    ]);
  }

  #[Route('/suggestions', name: 'app_search_suggestions', methods: ['GET'])]
  public function suggestions(
    Request $request,
    PostsRepository $postsRepository,
    CommentairesRepository $commentairesRepository,
    UserRepository $userRepository,
  ): JsonResponse {
    $query = $request->query->get('q', '');
    $suggestions = [];

    if (strlen($query) >= 2) {
      // Rechercher dans les posts.
      $posts = $postsRepository->createQueryBuilder('p')
        ->select('DISTINCT p.title')
        ->where('p.title LIKE :query')
        ->setParameter('query', '%' . $query . '%')
        ->setMaxResults(3)
        ->getQuery()
        ->getResult();

      foreach ($posts as $post) {
        $suggestions[] = [
          'text' => $post['title'],
          'type' => 'post',
          'url' => $this->generateUrl('app_search', ['q' => $post['title'], 'type' => 'posts']),
        ];
      }

      // Rechercher dans les utilisateurs.
      $users = $userRepository->createQueryBuilder('u')
        ->select('DISTINCT u.username')
        ->where('u.username LIKE :query')
        ->setParameter('query', '%' . $query . '%')
        ->setMaxResults(3)
        ->getQuery()
        ->getResult();

      foreach ($users as $user) {
        $suggestions[] = [
          'text' => $user['username'],
          'type' => 'user',
          'url' => $this->generateUrl('app_search', ['q' => $user['username'], 'type' => 'users']),
        ];
      }

      // Rechercher dans les catégories.
      $categories = $postsRepository->createQueryBuilder('p')
        ->select('DISTINCT c.name')
        ->leftJoin('p.cat', 'c')
        ->where('c.name LIKE :query')
        ->setParameter('query', '%' . $query . '%')
        ->setMaxResults(3)
        ->getQuery()
        ->getResult();

      foreach ($categories as $category) {
        $suggestions[] = [
          'text' => $category['name'],
          'type' => 'category',
          'url' => $this->generateUrl('app_search', ['q' => $category['name'], 'type' => 'posts']),
        ];
      }
    }

    return $this->json($suggestions);
  }

}

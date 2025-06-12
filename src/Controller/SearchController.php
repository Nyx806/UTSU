<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use App\Repository\CommentairesRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/search')]
class SearchController extends AbstractController
{
    #[Route('/', name: 'app_search', methods: ['GET'])]
    public function search(
        Request $request,
        PostsRepository $postsRepository,
        CommentairesRepository $commentairesRepository,
        UserRepository $userRepository
    ): Response {
        $query = $request->query->get('q', '');
        $type = $request->query->get('type', 'all');

        $results = [
            'posts' => [],
            'comments' => [],
            'users' => []
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
            'results' => $results
        ]);
    }
} 
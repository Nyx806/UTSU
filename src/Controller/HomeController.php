<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\PostsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'home_')]
final class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        CategoriesRepository $categoriesRepository,
        PostsRepository $postsRepository,
        UserRepository $userRepository
    ): Response {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_index');
        }

        return $this->render('home/index.html.twig', [
            'categories' => $categoriesRepository->findBy([], ['id' => 'ASC']),
            'posts' => $postsRepository->findBy([], ['id' => 'ASC']),
            'topUsers' => $userRepository->findTopThreeUsers(),
        ]);
    }
}

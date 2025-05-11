<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/posts', name: 'posts_')]
final class PostsController extends AbstractController
{
    #[Route('/{id}', name: 'detail')]
    public function details(int $id, PostsRepository $postsRepository): Response
    {
        return $this->render('posts/details.html.twig', [
            'controller_name' => 'PostsController',
            'post' => $postsRepository->find($id),
        ]);
    }
}

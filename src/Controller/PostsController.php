<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Posts;
use App\Form\PostsFromType;

#[Route('/posts', name: 'posts_')]
final class PostsController extends AbstractController
{
    #[Route('/ajout', name: 'ajout')]
    public function ajout(): Response
    {
        $posts = new Posts();
        $form = $this->createForm((PostsFromType::class), $posts);

        return $this->render('posts/ajout.html.twig', [
            'controller_name' => 'PostsController',
            'PostsForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'detail')]
    public function details(int $id, PostsRepository $postsRepository): Response
    {
        return $this->render('posts/details.html.twig', [
            'controller_name' => 'PostsController',
            'post' => $postsRepository->find($id),
        ]);
    }

}

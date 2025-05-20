<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Posts;
use App\Form\PostsFromType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/posts', name: 'posts_')]
final class PostsController extends AbstractController
{
    #[Route('/ajout/{id}', name: 'ajout')]
    public function ajout(int $id, Request $request, EntityManagerInterface $em, CategoriesRepository $categories_repository): Response
    {
        dump($id);
        $posts = new Posts();
        $cat = $categories_repository->find($id);
        $form = $this->createForm((PostsFromType::class), $posts);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $posts = $form->getData();
            $file = $form->get('photo')->getData();
            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('kernel.project_dir') . '/public/uploads/posts', $fileName);
                $posts->setPhoto($fileName);
            } else {
                $posts->setPhoto(null);
            }

            $posts->setDate(new \DateTimeImmutable());
            $posts->setCat($cat);
            $em->persist($posts);
            $em->flush();

            return $this->redirectToRoute('home_index');
        }

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

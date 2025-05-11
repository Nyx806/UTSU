<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategoriesRepository;

#[Route('/categories', name: 'categories_')]
final class CategoriesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categoriesController): Response
    {
        return $this->render('categories/index.html.twig', [
            'controller_name' => 'categories',
            'categories' => $categoriesController->findBy([], ['id' => 'ASC']),

        ]);
    }
    #[Route('/{id}', name: 'posts')]
    public function showByCategorie(int $id, CategoriesRepository $catRepo, PostsRepository $postRepo): Response
    {
        dump($id);
        $categorie = $catRepo->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        $posts = $postRepo->findBy(['cat' => $categorie], ['id' => 'DESC']);

        return $this->render('categories/show.html.twig', [
            'categorie' => $categorie,
            'posts' => $posts,
        ]);
    }
}

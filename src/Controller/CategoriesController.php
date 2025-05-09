<?php

namespace App\Controller;

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
}

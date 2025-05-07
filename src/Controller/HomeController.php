<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController{
    #[Route('/', name: 'app_home')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'categories' => $categoriesRepository->findBy([],['id' => 'ASC']),
        ]);
    }
}

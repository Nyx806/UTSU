<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController {

  #[Route(path: '/admin', name: 'admin')]
  public function index(): Response {
    $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
    return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
  }

  public function configureDashboard(): Dashboard {
    return Dashboard::new()
      ->setTitle('Utsu Site');
  }

  public function configureMenuItems(): iterable {
    yield MenuItem::linkToCrud('Users', 'fas fa-user', 'App\Entity\User');
    yield MenuItem::linkToCrud('Comentaires', 'fas fa-commentaires', 'App\Entity\Commentaires');
    yield MenuItem::linkToCrud('Posts', 'fas fa-posts', 'App\Entity\Posts');
    yield MenuItem::linkToCrud('Categories', 'fas fa-categories', 'App\Entity\Categories');
  }

}

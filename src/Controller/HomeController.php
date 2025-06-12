<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\PostsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Commentaires;
use App\Entity\Notification;

#[Route('/', name: 'home_')]
final class HomeController extends AbstractController {

  #[Route('/', name: 'index')]
  public function index(
    CategoriesRepository $categoriesRepository,
    PostsRepository $postsRepository,
    Request $request,
    EntityManagerInterface $em,
    UserRepository $userRepository,
  ): Response {
    if ($this->isGranted('ROLE_ADMIN')) {
      return $this->redirectToRoute('admin_index');
    }

    $user = $this->getUser();
    $filter = $request->query->get('filter', 'hot');
    $page = max(1, (int) $request->query->get('page', 1));
    $limit = 5;

    // Récupérer les posts selon le filtre.
    if ($user) {
      switch ($filter) {
        case 'new':
          $posts = $postsRepository->findNewPosts($user, $page, $limit);
          break;

        case 'friends':
          $posts = $postsRepository->findFriendsPosts($user, $page, $limit);
          break;

        case 'hot':
        default:
          $posts = $postsRepository->findHotPosts($user, $page, $limit);
          break;
      }
      $totalPosts = $postsRepository->countPostsByFilter($user, $filter);
    }
    else {
      // Si l'utilisateur n'est pas connecté, afficher tous les posts.
      $posts = $postsRepository->findBy([], ['date' => 'DESC'], $limit, ($page - 1) * $limit);
      $totalPosts = $postsRepository->count([]);
    }

    // Calculer le nombre total de pages.
    $totalPages = ceil($totalPosts / $limit);

    $mostDangerousCategories = $categoriesRepository->findBy([], ['dangerous' => 'DESC'], 3);
    $mostCommentedPosts = $postsRepository->findMostCommentedPosts(3);

    // Handle comment submission.
    if ($request->isMethod('POST') && $request->request->has('post_id')) {
      $postId = $request->request->get('post_id');
      $post = $postsRepository->find($postId);

      if ($post) {
        $com = new Commentaires();
        $com->setContenu($request->request->get('contenu'));
        $com->setPost($post);
        $com->setUserID($this->getUser());
        $com->setCreationDate(new \DateTimeImmutable());

        // Handle file uploads.
        $file = $request->files->get('img');
        $video = $request->files->get('video');

        if ($file) {
          $fileName = md5(uniqid()) . '.' . $file->guessExtension();
          $file->move(
                $this->getParameter('kernel.project_dir') . '/public/uploads/commentaires/photo',
                $fileName
            );
          $com->setImg($fileName);
        }

        if ($video) {
          $videoName = md5(uniqid()) . '.' . $video->guessExtension();
          $video->move(
                $this->getParameter('kernel.project_dir') . '/public/uploads/commentaires/video',
                $videoName
            );
          $com->setVideo($videoName);
        }

        // Handle parent comment.
        $parentId = $request->request->get('comment_parent');
        if ($parentId) {
          $parent = $em->getRepository(Commentaires::class)->find($parentId);
          if ($parent) {
            $com->setComParent($parent);

            // Créer une notification pour l'auteur du commentaire parent.
            if ($parent->getUserID() !== $this->getUser()) {
              $notification = new Notification();
              $notification->setUser($parent->getUserID());
              $notification->setComment($com);
              $em->persist($notification);
            }
          }
        }

        $em->persist($com);
        $em->flush();

        $this->addFlash('success', 'Comment added successfully!');
        return $this->redirectToRoute('home_index', [
          'filter' => $filter,
          'page' => $page,
        ]);
      }
    }

    return $this->render(
          'home/index.html.twig',
          [
            'categories' => $categoriesRepository->findBy([], ['id' => 'ASC']),
            'posts' => $posts,
            'topUsers' => $userRepository->findTopThreeUsers(),
            'mostDangerousCategories' => $mostDangerousCategories,
            'mostCommentedPosts' => $mostCommentedPosts,
            'currentFilter' => $filter,
            'currentPage' => $page,
            'totalPages' => $totalPages,
          ]
      );
  }

}

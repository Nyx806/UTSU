<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Posts;
use App\Entity\Commentaires;
use App\Form\PostsFromType;
use App\Form\ComFromType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Notification;

#[Route('/posts', name: 'posts_')]
final class PostsController extends AbstractController
{

    #[Route('/ajout/{id}', name: 'ajout')]
    public function ajout(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        CategoriesRepository $categories_repository,
    ): Response {
        $posts = new Posts();
        $cat = $categories_repository->find($id);
        $form = $this->createForm((PostsFromType::class), $posts, [
      // Ne pas afficher le champ catégorie.
        'show_category' => false,
        ]);

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
            $posts->setUserID($this->getUser());
            $posts->setDangerous(false);
            $em->persist($posts);
            $em->flush();

            return $this->redirectToRoute('categories_posts', ['id' => $cat->getId()]);
        }

        return $this->render(
            'posts/ajout.html.twig',
            [
            'controller_name' => 'PostsController',
            'PostsForm' => $form->createView(),
            ]
        );
    }

    #[Route('/new', name: 'new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        CategoriesRepository $categories_repository,
    ): Response {
        $posts = new Posts();
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
            $posts->setUserID($this->getUser());
            $posts->setDangerous(0);
            $em->persist($posts);
            $em->flush();

            return $this->redirectToRoute('categories_posts', ['id' => $posts->getCat()->getId()]);
        }

        return $this->render('posts/new.html.twig', [
        'controller_name' => 'PostsController',
        'PostsForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'detail')]
    public function details(
        int $id,
        PostsRepository $postsRepository,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $post = $postsRepository->findOneByIdWithCommentsAndReplies($id);

        if (!$post) {
            throw $this->createNotFoundException('Le post demandé n\'existe pas.');
        }

      // Handle reply form submission (raw HTML form)
        if ($request->isMethod('POST') && $request->request->has('comment_parent') && $request->request->has('com')) {
            $comData = $request->request->all('com');
            $parentId = $request->request->get('comment_parent');

            if ($parentId && $parentId != '0' && !empty($comData['contenu'])) {
                $replyCom = new Commentaires();
                $replyCom->setContenu($comData['contenu']);

                $parentComment = $em->getRepository(Commentaires::class)->find($parentId);
                if ($parentComment) {
                    $replyCom->setComParent($parentComment);

                    // Create notification for parent comment author.
                    if ($parentComment->getUserID() !== $this->getUser()) {
                        $notification = new Notification();
                        $notification->setUser($parentComment->getUserID());
                        $notification->setComment($replyCom);
                        $em->persist($notification);
                    }
                }

              // Handle file uploads for reply.
                $replyFiles = $request->files->get('com');
                $replyImg = $replyFiles['img'] ?? null;
                $replyVideo = $replyFiles['video'] ?? null;

                if ($replyVideo) {
                    $videoName = md5(uniqid()) . '.' . $replyVideo->guessExtension();
                    $videoDir = $this->getParameter('kernel.project_dir') . '/public/uploads/commentaires/video';
                    $replyVideo->move($videoDir, $videoName);
                    $replyCom->setVideo($videoName);
                } else {
                    $replyCom->setVideo(null);
                }
                if ($replyImg) {
                    $fileName = md5(uniqid()) . '.' . $replyImg->guessExtension();
                    $photoDir = $this->getParameter('kernel.project_dir') . '/public/uploads/commentaires/photo';
                    $replyImg->move($photoDir, $fileName);
                    $replyCom->setImg($fileName);
                } else {
                    $replyCom->setImg(null);
                }

                $replyCom->setCreationDate(new \DateTimeImmutable());
                $replyCom->setUserID($this->getUser());
                $replyCom->setPost($post);
                $em->persist($replyCom);
                $em->flush();

                return $this->redirectToRoute('posts_detail', ['id' => $id]);
            }
        }

        $com = new Commentaires();
        $form = $this->createForm(ComFromType::class, $com);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $com = $form->getData();
            $file = $form->get('img')->getData();
            $video = $form->get('video')->getData();

          // Gestion du commentaire parent.
            $parentId = $request->request->get('comment_parent');
            if ($parentId && $parentId != '0') {
                $parentComment = $em->getRepository(Commentaires::class)->find($parentId);
                if ($parentComment) {
                    $com->setComParent($parentComment);

                    // Créer une notification pour l'auteur du commentaire parent.
                    if ($parentComment->getUserID() !== $this->getUser()) {
                        $notification = new Notification();
                        $notification->setUser($parentComment->getUserID());
                        $notification->setComment($com);
                        $em->persist($notification);
                    }
                }
            }

            if ($video) {
                $videoName = md5(uniqid()) . '.' . $video->guessExtension();
                $videoDir = $this->getParameter('kernel.project_dir') . '/public/uploads/commentaires/video';
                $video->move($videoDir, $videoName);
                $com->setVideo($videoName);
            } else {
                $com->setVideo(null);
            }
            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $photoDir = $this->getParameter('kernel.project_dir') . '/public/uploads/commentaires/photo';
                $file->move($photoDir, $fileName);
                $com->setImg($fileName);
            } else {
                $com->setImg(null);
            }
            $com->setCreationDate(new \DateTimeImmutable());
            $com->setUserID($this->getUser());
            $com->setPost($post);
            $em->persist($com);
            $em->flush();
            return $this->redirectToRoute('posts_detail', ['id' => $id]);
        }
        return $this->render(
            'posts/details.html.twig',
            [
            'controller_name' => 'PostsController',
            'post' => $post,
            'ComForm' => $form->createView(),
            ]
        );
    }
}

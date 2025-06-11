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

#[Route('/posts', name: 'posts_')]
final class PostsController extends AbstractController
{
    #[Route('/ajout/{id}', name: 'ajout')]
    public function ajout(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        CategoriesRepository $categories_repository
    ): Response {
        $posts = new Posts();
        $cat = $categories_repository->find($id);
        $form = $this->createForm((PostsFromType::class), $posts, [
            'show_category' => false, // Ne pas afficher le champ catégorie
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
            $em->persist($posts);
            $em->flush();

            return $this->redirectToRoute('home_index');
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
        CategoriesRepository $categories_repository
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
            $em->persist($posts);
            $em->flush();

            return $this->redirectToRoute('home_index');
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
        EntityManagerInterface $em
    ): Response {

        $com = new Commentaires();
        $post = $postsRepository->find($id);
        $form = $this->createForm(ComFromType::class, $com);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $com = $form->getData();
            $file = $form->get('img')->getData();
            $video = $form->get('video')->getData();

            // Gestion du commentaire parent
            $parentId = $request->request->get('comment_parent');
            if ($parentId && $parentId != '0') {
                $parentComment = $em->getRepository(Commentaires::class)->find($parentId);
                if ($parentComment) {
                    $com->setComParent($parentComment);
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
            'post' => $postsRepository->find($id),
            'ComForm' => $form->createView(),
            ]
        );
    }
}

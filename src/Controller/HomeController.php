<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\PostsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Commentaires;
use App\Form\ComFromType;

#[Route('/', name: 'home_')]
final class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        CategoriesRepository $categoriesRepository,
        PostsRepository $postsRepository,
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): Response {

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_index');
        }
        
         $posts = $postsRepository->findAll();


    $com = new Commentaires();

    // Vérifie si un commentaire est en train d'être soumis
    if ($request->isMethod('POST') && $request->request->has('post_id')) {
        $postId = $request->request->get('post_id');
        $post = $postsRepository->find($postId);
        if ($post) {
            $form = $this->createForm(ComFromType::class, $com);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $com->setPost($post);
                $com->setUserID($this->getUser());
                $com->setCreationDate(new \DateTimeImmutable());

                // Gestion des fichiers
                $file = $form->get('img')->getData();
                $video = $form->get('video')->getData();

                if ($file) {
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move($this->getParameter('kernel.project_dir') . '/public/uploads/commentaires/photo', $fileName);
                    $com->setImg($fileName);
                }

                if ($video) {
                    $videoName = md5(uniqid()) . '.' . $video->guessExtension();
                    $video->move($this->getParameter('kernel.project_dir') . '/public/uploads/commentaires/video', $videoName);
                    $com->setVideo($videoName);
                }

                // Gestion du parent
                $parentId = $request->request->get('comment_parent');
                if ($parentId) {
                    $parent = $em->getRepository(Commentaires::class)->find($parentId);
                    if ($parent) {
                        $com->setComParent($parent);
                    }
                }

                $em->persist($com);
                $em->flush();

                return $this->redirectToRoute('home_index'); // évite re-submission
            }
        }
    }
        return $this->render('home/index.html.twig', [
        'categories' => $categoriesRepository->findBy([], ['id' => 'ASC']),
        'posts' => $postsRepository->findBy([], ['id' => 'ASC']),
        'commentForm' => $this->createForm(ComFromType::class, $com)->createView(),
        'users' => $userRepository->findBy([], ['id' => 'ASC']),
        ]);
    }
}

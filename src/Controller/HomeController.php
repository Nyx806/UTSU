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
        $posts = $postsRepository->findAll();

        $posts = $postsRepository->findAll();

        // Handle comment submission
        if ($request->isMethod('POST') && $request->request->has('post_id')) {
            $postId = $request->request->get('post_id');
            $post = $postsRepository->find($postId);
            
            if ($post) {
                $com = new Commentaires();
                $com->setContenu($request->request->get('contenu'));
                $com->setPost($post);
                $com->setUserID($this->getUser());
                $com->setCreationDate(new \DateTimeImmutable());

                // Handle file uploads
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

                // Handle parent comment
                $parentId = $request->request->get('comment_parent');
                if ($parentId) {
                    $parent = $em->getRepository(Commentaires::class)->find($parentId);
                    if ($parent) {
                        $com->setComParent($parent);
                    }
                }

                $em->persist($com);
                $em->flush();

                $this->addFlash('success', 'Comment added successfully!');
                return $this->redirectToRoute('home_index');
            }
        }

        return $this->render(
            'home/index.html.twig',
            [
            'categories' => $categoriesRepository->findBy([], ['id' => 'ASC']),
            'posts' => $postsRepository->findBy([], ['id' => 'ASC']),
            'topUsers' => $userRepository->findTopThreeUsers(),
            ]
        );
    }
}

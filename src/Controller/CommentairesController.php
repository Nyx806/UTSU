<?php

namespace App\Controller;

use App\Repository\CommentairesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Commentaires;
use App\Form\ComFromType;
use App\Repository\PostsRepository;

#[Route('/commentaires', name: 'commentaires_')]
final class CommentairesController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    #[Route('/{id}', name: 'ajout')]
    public function index(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        PostsRepository $postsRepository,
    ): Response {
        $com= new Commentaires();
        $post = $postsRepository->find($id);
        $form = $this->createForm(ComFromType::class, $com);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $com = $form->getData();
            $file = $form->get('img')->getData();
            $video = $form->get('video')->getData();
            if ($video) {
                $videoName = md5(uniqid()) . '.' . $video->guessExtension();
                $video->move($this->getParameter('kernel.project_dir') . '/public/uploads/commentaires/video', $videoName);
                $com->setVideo($videoName);
            } else {
                $com->setVideo(null);
            }
            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('kernel.project_dir') . '/public/uploads/commentaires/photo', $fileName);
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

        return $this->render('commentaires/index.html.twig', [
            'controller_name' => 'CommentairesController',
            'ComForm' => $form->createView(),
        ]);
    }
}

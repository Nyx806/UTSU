<?php
namespace App\Controller;

use App\Entity\Likes;
use App\Entity\Posts;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikesController extends AbstractController
{
    #[Route('/posts/{id}/toggle-like', name: 'post_toggle_like', methods: ['POST'])]
    public function toggleLike(Posts $post, Request $request, EntityManagerInterface $em): Response
    {
            $type = $request->request->get('type'); // 0 for safe, 1 for dangerous
            $user = $this->getUser();

            // Vérifier si l'utilisateur a déjà liké ce post
            $existingLike = $post->getLikes()->filter(
                fn(Likes $like) => $like->getUserID() === $user 
                && $like->getType() === (int)$type
            )->first();

        if ($existingLike) {
            // Supprimer le like existant
            $post->removeLike($existingLike);
            $em->remove($existingLike);
            $action = 'removed';
        } else {
            // Ajouter un nouveau like
            $like = new Likes();
            $like->setType((int)$type);
            $like->setPost($post);
            $like->setUserID($user);

            $post->addLike($like);
            $em->persist($like);
            $action = 'added';
        }

            $em->flush();

            return $this->json([
                'message' => 'Like toggled successfully',
                'action' => $action
            ]);
    }
}

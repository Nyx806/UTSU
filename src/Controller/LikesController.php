<?php

namespace App\Controller;

use App\Entity\Likes;
use App\Entity\Posts;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikesController extends AbstractController {

  #[Route('/posts/{id}/toggle-like', name: 'post_toggle_like', methods: ['POST'])]
  public function toggleLike(Posts $post, Request $request, EntityManagerInterface $em): Response {
    // Decode JSON body.
    $data = json_decode($request->getContent(), TRUE);
    $type = $data['type'] ?? NULL;
    $user = $this->getUser();

    $existingLike = $post->getLikes()->filter(
              fn(Likes $like) => $like->getUserID() === $user
          )->first();

    // Log the received data for debugging.
    error_log('Received data: ' . $request->getContent());

    // Validate the 'type' parameter ajout.
    if (!in_array($type, ['safe', 'dangerous'], TRUE)) {
      return $this->json(
            [
              'error' => 'Invalid like type provided.',
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

    // Modification de la logique pour utiliser des chaînes de caractères 'safe' et 'dangerous' pour le type de like.
    if ($existingLike) {
      if ($existingLike->getType() === $type) {
        $post->removeLike($existingLike);
        $em->remove($existingLike);
        $action = 'removed';
      }
      else {
        $existingLike->setType($type);
        $em->persist($existingLike);
        $action = 'updated';
      }
    }
    else {
      $like = new Likes();
      $like->setType($type);
      $like->setPost($post);
      $like->setUserID($user);
      $post->addLike($like);
      $em->persist($like);
      $action = 'added';
    }

    $em->flush();
    return $this->json(
          [
            'message' => 'Like toggled successfully',
            'action' => $action,
            'safeLikes' => $post->countSafeLikes(),
            'dangerousLikes' => $post->countDangerousLikes(),
          ]
      );
  }

}

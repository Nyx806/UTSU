<?php

namespace App\Controller;

use App\Entity\Posts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/share', name: 'share_')]
final class ShareController extends AbstractController
{

    #[Route('/{id}', name: 'post')]
    public function sharePost(Posts $post): Response
    {
        $shareUrl = $this->generateUrl('posts_detail', ['id' => $post->getId()], true);
        $shareTitle = $post->getTitle();
        $shareText = substr($post->getContenu(), 0, 100) . '...';

        return $this->render('share/index.html.twig', [
        'post' => $post,
        'shareUrl' => $shareUrl,
        'shareTitle' => $shareTitle,
        'shareText' => $shareText,
        ]);
    }
}
